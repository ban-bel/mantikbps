<!DOCTYPE html>
<html lang="id">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Draf Surat Keterangan Perubahan Jadwal Rilis - {{ $publication->nama_publikasi }}</title>
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.3/dist/css/bootstrap.min.css" rel="stylesheet">
    <link href="https://cdn.jsdelivr.net/npm/bootstrap-icons@1.11.3/font/bootstrap-icons.css" rel="stylesheet">
    <style>
        body {
            background-color: #f8f9fa;
            font-family: 'Times New Roman', Times, serif;
            color: #000;
        }
        .page {
            width: 210mm;
            min-height: 297mm;
            padding: 20mm 25mm;
            margin: 20px auto;
            background: white;
            box-shadow: 0 0 10px rgba(0,0,0,0.1);
        }
        .kop-surat {
            border-bottom: 3px double #000;
            padding-bottom: 12px;
            margin-bottom: 20px;
        }
        .table-data td {
            padding: 4px 8px;
            vertical-align: top;
        }
        @media print {
            body {
                background: none;
                margin: 0;
            }
            .page {
                box-shadow: none;
                margin: 0;
                width: 100%;
                padding: 15mm 20mm;
            }
            .no-print {
                display: none !important;
            }
        }
    </style>
</head>
<body>

<div class="no-print bg-dark text-white py-2 px-4 d-flex justify-content-between align-items-center">
    <div>
        <strong>Draf Surat Keterangan / Berita Acara Perubahan Jadwal Rilis</strong>
        <span class="badge bg-warning text-dark ms-2">Format Standar BPS Daerah</span>
    </div>
    <div class="d-flex gap-2">
        <button onclick="window.print()" class="btn btn-primary btn-sm">
            <i class="bi bi-printer me-1"></i>Cetak / Simpan PDF
        </button>
        <button onclick="window.close()" class="btn btn-outline-light btn-sm">Tutup</button>
    </div>
</div>

<div class="page">
    <!-- KOP SURAT -->
    <div class="kop-surat text-center position-relative">
        <div style="font-size: 15pt; font-weight: bold; letter-spacing: 1px;">BADAN PUSAT STATISTIK</div>
        <div style="font-size: 14pt; font-weight: bold;">{{ strtoupper($tenant->name ?? 'PROVINSI KEPULAUAN BANGKA BELITUNG') }}</div>
        <div style="font-size: 10pt;" class="text-secondary mt-1">
            {{ $tenant->address ?? 'Komplek Perkantoran Terpadu Pemerintah Provinsi Kepulauan Bangka Belitung' }}
        </div>
    </div>

    <!-- JUDUL SURAT -->
    <div class="text-center my-4">
        <div style="font-size: 13pt; font-weight: bold; text-decoration: underline;">SURAT KETERANGAN PERUBAHAN JADWAL RILIS PUBLIKASI</div>
        <div style="font-size: 11pt;" class="mt-1">Nomor: &nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;/BPS/{{ $tenant->kode ?? '1900' }}/{{ date('m/Y') }}</div>
    </div>

    <!-- ISI SURAT -->
    <div style="font-size: 11pt; line-height: 1.6;" class="text-justify mb-3">
        Menindaklanjuti penetapan Perencanaan Jadwal Rilis Definitif Tahun {{ $publication->tahun }} serta berdasarkan Peraturan/Kebijakan Rilis Statistik Daerah, dengan ini menerangkan bahwa telah dilakukan penyesuaian jadwal rilis untuk publikasi berikut:
    </div>

    <table class="table-data mb-4 w-100" style="font-size: 11pt;">
        <tr>
            <td style="width: 220px;">1. Judul Publikasi</td>
            <td style="width: 15px;">:</td>
            <td class="fw-bold">{{ $publication->nama_publikasi }}</td>
        </tr>
        <tr>
            <td>2. Kategori Publikasi</td>
            <td>:</td>
            <td>{{ $publication->kategori }}</td>
        </tr>
        <tr>
            <td>3. Periode / Akurasi</td>
            <td>:</td>
            <td>{{ $publication->periode ?? '-' }} / {{ $publication->akurasi_publikasi ?? '-' }}</td>
        </tr>
        <tr>
            <td>4. Jadwal Rilis Semula</td>
            <td>:</td>
            <td class="text-danger fw-bold">
                {{ $publication->jadwal_rilis ? $publication->jadwal_rilis->translatedFormat('d F Y') : '-' }}
            </td>
        </tr>
        <tr>
            <td>5. Jadwal Rilis Baru (Diusulkan)</td>
            <td>:</td>
            <td class="text-success fw-bold">
                {{ $jadwalRilisBaru ? \Carbon\Carbon::parse($jadwalRilisBaru)->translatedFormat('d F Y') : '...................................' }}
            </td>
        </tr>
        <tr>
            <td>6. Jenis Tindakan</td>
            <td>:</td>
            <td class="text-capitalize">{{ $actionType }}</td>
        </tr>
        <tr>
            <td>7. Alasan Penyesuaian Jadwal</td>
            <td>:</td>
            <td>{{ $reason }}</td>
        </tr>
    </table>

    <div style="font-size: 11pt; line-height: 1.6;" class="text-justify mb-5">
        Demikian Surat Keterangan ini dibuat dengan sebenar-benarnya sebagai dokumen pertanggungjawaban penyesuaian jadwal rilis publikasi resmi Badan Pusat Statistik, untuk dapat dipergunakan sebagaimana mestinya.
    </div>

    <!-- TANDA TANGAN -->
    <div class="row" style="font-size: 11pt; margin-top: 40px;">
        <div class="col-6"></div>
        <div class="col-6 text-center">
            <div>{{ $tenant->wilayah ?? 'Pangkalpinang' }}, {{ \Carbon\Carbon::now()->translatedFormat('d F Y') }}</div>
            <div class="fw-bold mt-1">Kepala / Ketua Tim Publikasi,</div>
            <div style="height: 70px;"></div>
            <div class="fw-bold" style="text-decoration: underline;">( .................................................... )</div>
            <div>NIP. ....................................................</div>
        </div>
    </div>
</div>

</body>
</html>
