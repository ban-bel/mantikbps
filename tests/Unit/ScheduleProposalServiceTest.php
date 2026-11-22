<?php

namespace Tests\Unit;

use App\Models\Publication;
use App\Models\PublicationScheduleProposal;
use App\Models\PublicationTeamAssignment;
use App\Models\User;
use App\Support\ScheduleProposalService;
use Tests\TestCase;

class ScheduleProposalServiceTest extends TestCase
{
    public function test_proposer_and_finalize_roles_permissions()
    {
        $publication = new Publication(['id' => 10, 'tenant_id' => 1]);
        $publication->setRelation('teamAssignments', collect());

        $penyusun = new User(['name' => 'Penyusun User']);
        $penyusun->id = 101;
        $anggota = new User(['name' => 'Anggota Pemeriksa']);
        $anggota->id = 102;
        $ketua = new User(['name' => 'Ketua Pemeriksa']);
        $ketua->id = 103;
        $operator = new User(['name' => 'Operator Web']);
        $operator->id = 104;

        $assignments = collect([
            new PublicationTeamAssignment(['publication_id' => 10, 'user_id' => 101, 'assignment_role' => 'penyusun_naskah']),
            new PublicationTeamAssignment(['publication_id' => 10, 'user_id' => 102, 'assignment_role' => 'anggota_pemeriksa_konten']),
            new PublicationTeamAssignment(['publication_id' => 10, 'user_id' => 103, 'assignment_role' => 'ketua_pemeriksa_konten']),
            new PublicationTeamAssignment(['publication_id' => 10, 'user_id' => 104, 'assignment_role' => 'operator_website']),
        ]);

        $assignments->each(function ($a) use ($penyusun, $anggota, $ketua, $operator) {
            $user = match($a->user_id) {
                101 => $penyusun,
                102 => $anggota,
                103 => $ketua,
                104 => $operator,
            };
            $a->setRelation('user', $user);
        });

        $publication->setRelation('teamAssignments', $assignments);
        $publication->setRelation('scheduleProposals', collect());

        // Can Propose
        $this->assertTrue(ScheduleProposalService::canPropose($publication, $penyusun));
        $this->assertTrue(ScheduleProposalService::canPropose($publication, $anggota));
        $this->assertTrue(ScheduleProposalService::canPropose($publication, $ketua));
        $this->assertFalse(ScheduleProposalService::canPropose($publication, $operator));

        // Can Finalize (Only ketua_pemeriksa_konten)
        $this->assertFalse(ScheduleProposalService::canFinalize($publication, $penyusun));
        $this->assertFalse(ScheduleProposalService::canFinalize($publication, $anggota));
        $this->assertTrue(ScheduleProposalService::canFinalize($publication, $ketua));
        $this->assertFalse(ScheduleProposalService::canFinalize($publication, $operator));
    }

    public function test_summary_detects_complete_agreement_and_conflict()
    {
        $publication = new Publication(['id' => 20, 'tenant_id' => 1]);

        $user1 = new User(['id' => 201, 'name' => 'Penyusun']);
        $user2 = new User(['id' => 202, 'name' => 'Ketua Konten']);

        $assignments = collect([
            new PublicationTeamAssignment(['publication_id' => 20, 'user_id' => 201, 'assignment_role' => 'penyusun_naskah']),
            new PublicationTeamAssignment(['publication_id' => 20, 'user_id' => 202, 'assignment_role' => 'ketua_pemeriksa_konten']),
        ]);
        $assignments[0]->setRelation('user', $user1);
        $assignments[1]->setRelation('user', $user2);

        $publication->setRelation('teamAssignments', $assignments);

        // Case 1: Both propose identical dates (Agreed)
        $prop1 = new PublicationScheduleProposal([
            'publication_id' => 20,
            'user_id' => 201,
            'role' => 'penyusun_naskah',
            'proposed_kategori' => 'ARC',
            'proposed_jadwal_rilis' => '2027-06-15',
            'proposed_jadwal_upload' => '2027-06-10',
            'proposed_jadwal_mulai_pemeriksaan' => '2027-06-03',
            'proposed_jadwal_mulai_penyusunan' => '2027-06-01',
        ]);
        $prop2 = new PublicationScheduleProposal([
            'publication_id' => 20,
            'user_id' => 202,
            'role' => 'ketua_pemeriksa_konten',
            'proposed_kategori' => 'ARC',
            'proposed_jadwal_rilis' => '2027-06-15',
            'proposed_jadwal_upload' => '2027-06-10',
            'proposed_jadwal_mulai_pemeriksaan' => '2027-06-03',
            'proposed_jadwal_mulai_penyusunan' => '2027-06-01',
        ]);

        $publication->setRelation('scheduleProposals', collect([$prop1, $prop2]));

        $summary = ScheduleProposalService::getSummary($publication);
        $this->assertTrue($summary['is_complete']);
        $this->assertFalse($summary['has_conflict']);
        $this->assertEquals('complete_agreed', $summary['status_code']);

        // Case 2: Different dates (Conflict / Need Reconciliation)
        $prop2->proposed_jadwal_rilis = '2027-06-20';
        $summaryConflict = ScheduleProposalService::getSummary($publication);
        $this->assertTrue($summaryConflict['is_complete']);
        $this->assertTrue($summaryConflict['has_conflict']);
        $this->assertEquals('complete_conflict', $summaryConflict['status_code']);

        // Case 3: Complete Drop (All propose not releasing in T+1)
        $prop1->will_release = false;
        $prop1->drop_reason = 'Survei ad-hoc selesai';
        $prop2->will_release = false;
        $prop2->drop_reason = 'Tidak ada alokasi DIPA';
        $summaryDrop = ScheduleProposalService::getSummary($publication);
        $this->assertTrue($summaryDrop['is_complete']);
        $this->assertFalse($summaryDrop['has_conflict']);
        $this->assertEquals('complete_drop', $summaryDrop['status_code']);

        // Case 4: Conflict between keep release vs drop release
        $prop1->will_release = true;
        $prop2->will_release = false;
        $summaryWillReleaseConflict = ScheduleProposalService::getSummary($publication);
        $this->assertTrue($summaryWillReleaseConflict['has_conflict']);
        $this->assertEquals('complete_conflict', $summaryWillReleaseConflict['status_code']);

        // Case 5: Publication locked as cancelled_t1
        $publication->planning_status = 'cancelled_t1';
        $summaryCancelled = ScheduleProposalService::getSummary($publication);
        $this->assertEquals('cancelled_t1', $summaryCancelled['status_code']);
    }

    public function test_is_ketua_tim_authorization()
    {
        $admin = new User(['role' => 'admin']);
        $admin->id = 501;
        $this->assertTrue(ScheduleProposalService::isKetuaTim($admin, 1));

        $pegawaiBiasa = new User(['role' => 'pegawai']);
        $pegawaiBiasa->id = 502;
        $this->assertFalse(ScheduleProposalService::isKetuaTim($pegawaiBiasa, 1));
    }
}
