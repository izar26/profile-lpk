<!DOCTYPE html>
<html>
<head>
    <meta http-equiv="Content-Type" content="text/html; charset=utf-8"/>
    <title>Laporan Data Siswa</title>
    <style>
        /* 1. ATURAN HALAMAN FISIK (GLOBAL) */
        @page {
            /* Paksa Ukuran Kertas A4 Portrait */
            size: A4 portrait;

            /* Margin halaman standar */
            margin: 1cm 1.5cm;

            /* LOGIKA BACKGROUND (WATERMARK) DI SEMUA HALAMAN */
            @if(isset($profile) && $profile->background_surat)
                background-image: url('{{ public_path("storage/" . $profile->background_surat) }}');
                background-repeat: no-repeat;

                /* POSISI: Tengah-tengah kertas */
                background-position: center center;

                /* UKURAN:
                   - Jangan pakai 'cover' agar tidak penyok/terpotong.
                   - Gunakan angka persentase (misal 60% - 80%) atau 'contain'.
                   - Ini menjaga aspek rasio gambar asli Anda.
                */
                background-size: 80%;
            @endif
        }

        body {
            font-family: sans-serif;
            font-size: 10pt;
            /* Pastikan body tidak menimpa background @page */
            background-color: transparent;
        }

        /* --- KOP SURAT (Hanya Halaman 1) --- */
        .kop-image {
            width: 100%;
            height: auto;
            display: block;
            border-bottom: 2px solid #000;
            margin-bottom: 20px;
        }

        /* --- DATA TABLE STYLE --- */
        .table-data {
            width: 100%;
            border-collapse: collapse;
            margin-top: 10px;
            /* Beri latar belakang putih semi-transparan pada tabel
               agar teks tetap terbaca jelas di atas watermark */
            background-color: rgba(255, 255, 255, 0.85);
        }
        .table-data th, .table-data td {
            border: 1px solid #444;
            padding: 6px;
            text-align: left;
            vertical-align: middle;
            font-size: 9pt;
        }
        .table-data th {
            background-color: #e0e0e0;
            font-weight: bold;
            text-align: center;
            text-transform: uppercase;
        }
        .text-center { text-align: center; }
        .badge {
            padding: 2px 5px;
            border-radius: 3px;
            font-size: 8pt;
            font-weight: bold;
            border: 1px solid #ccc;
        }
    </style>
</head>
<body>

    {{-- 1. HEADER / KOP SURAT --}}
    {{-- Karena diletakkan di dalam body (bukan @page), ini hanya muncul di Halaman 1 --}}
    <div class="header-wrapper">
        @if(isset($profile) && $profile->kop_surat)
            {{-- Tampilkan Gambar Kop Full --}}
            <img src="{{ public_path('storage/' . $profile->kop_surat) }}" class="kop-image" alt="Kop Surat">
        @else
            {{-- Fallback jika tidak ada gambar kop (Text Only) --}}
            <div style="text-align: center; border-bottom: 3px double black; padding-bottom: 10px; margin-bottom: 20px;">
                <h1 style="margin:0; font-size:16pt; text-transform:uppercase;">{{ $profile->nama_lpk ?? config('app.name') }}</h1>
                <p style="margin:2px; font-size:10pt;">{{ $profile->alamat }}</p>
            </div>
        @endif
    </div>

    {{-- JUDUL LAPORAN --}}
    <div style="text-align: center; margin-bottom: 20px;">
        <h3 style="margin: 0; text-decoration: underline; text-transform: uppercase;">LAPORAN DATA SISWA</h3>
        <p style="margin: 5px 0 0 0; font-size: 9pt; color: #555;">Dicetak pada: {{ now()->format('d F Y, H:i') }} WIB</p>
    </div>

    {{-- TABEL DATA --}}
    <table class="table-data">
        <thead>
            <tr>
                <th style="width: 5%">No</th>
                <th style="width: 20%">Nama Lengkap</th>
                <th style="width: 15%">No. KTP</th>
                <th style="width: 15%">Program</th>
                <th style="width: 12%">Status</th>
                <th style="width: 18%">Email</th>
                <th style="width: 15%">No. HP</th>
            </tr>
        </thead>
        <tbody>
            @forelse($students as $index => $student)
            <tr>
                <td class="text-center">{{ $index + 1 }}</td>
                <td><strong style="text-transform: uppercase;">{{ $student->nama_lengkap }}</strong></td>
                <td>{{ $student->nomor_ktp ?? '-' }}</td>
                <td>{{ $student->program->judul ?? '-' }}</td>
                <td class="text-center">
                    <span class="badge">{{ $student->status }}</span>
                </td>
                <td style="font-size: 8pt;">{{ $student->email }}</td>
                <td>{{ $student->no_hp_peserta ?? '-' }}</td>
            </tr>
            @empty
            <tr>
                <td colspan="7" class="text-center" style="padding: 20px; font-style: italic; color: #777;">
                    Tidak ada data siswa ditemukan.
                </td>
            </tr>
            @endforelse
        </tbody>
    </table>

    {{-- FOOTER / TANDA TANGAN --}}
    {{-- Menggunakan page-break-inside: avoid agar TTD tidak terpotong ke halaman baru sendirian --}}
    <div style="margin-top: 40px; float: right; width: 200px; text-align: center; page-break-inside: avoid;">
        <p style="font-size: 10pt; margin-bottom: 60px;">
            Mengetahui,<br>
            Pimpinan
        </p>
        <p style="font-weight: bold; text-decoration: underline;">
            {{ $profile->nama_pimpinan ?? '(....................)' }}
        </p>
    </div>

</body>
</html>
