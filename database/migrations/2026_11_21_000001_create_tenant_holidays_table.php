<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    /**
     * Tabel master hari libur nasional & cuti bersama per tenant (satker).
     * Diisi oleh Admin setiap tahun berdasarkan SKB 3 Menteri.
     * Digunakan oleh WorkingDayHelper untuk perhitungan hari kerja yang akurat.
     */
    public function up(): void
    {
        Schema::create('tenant_holidays', function (Blueprint $table) {
            $table->id();

            $table->foreignId('tenant_id')->constrained()->cascadeOnDelete();

            $table->date('holiday_date');
            $table->string('description'); // Misal: "Idul Fitri 1448 H", "Cuti Bersama", dll.
            $table->year('year');          // Tahun libur untuk memudahkan filter

            $table->foreignId('created_by')->nullable()->constrained('users')->nullOnDelete();
            $table->timestamps();

            // Satu tanggal tidak boleh double per tenant
            $table->unique(['tenant_id', 'holiday_date']);
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('tenant_holidays');
    }
};
