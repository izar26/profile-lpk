<!DOCTYPE html>
<html>
<head>
    <meta http-equiv="Content-Type" content="text/html; charset=utf-8"/>
    <title>Laporan Data Siswa</title>

    {{-- 1. PROSES KONVERSI GAMBAR KE BASE64 (Supaya pasti muncul) --}}
    @php
        $bgImage = null;
        if(isset($profile) && $profile->background_surat) {
            $path = public_path('storage/' . $profile->background_surat);

            // Cek apakah file ada
            if(file_exists($path)) {
                $type = pathinfo($path, PATHINFO_EXTENSION);
                $data = file_get_contents($path);
                $bgImage = 'data:image/' . $type . ';base64,' . base64_encode($data);
            }
        }
    @endphp

    <style>
        /* 2. ATURAN HALAMAN KERTAS */
        @page {
            /* Paksa ukuran A4 */
            size: A4 portrait;
            margin: 0; /* Margin 0 di @page agar background bisa full tepi (bleed) */
        }

        body {
            font-family: sans-serif;
            font-size: 10pt;

            /* Margin konten sesungguhnya diatur di sini */
            margin: 1cm 2cm;

            /* Background ditaruh di body agar lebih fleksibel */
            @if($bgImage)
                background-image: url("{{ $bgImage }}");
                background-repeat: no-repeat;
                background-position: center center;

                /* SOLUSI AGAR TIDAK PENYOK / KEGEDEAN:
                   - '100% 100%' = Memaksa gambar memenuhi kertas A4 (Bisa penyok dikit kalau rasio gambar beda).
                   - 'cover' = Memenuhi kertas tapi ada bagian terpotong.
                   - 'contain' = Gambar utuh tapi ada sisa putih.

                   Saran: Gunakan 100% 100% jika gambar Anda memang didesain seukuran A4.
                */
                background-size: 100% 100%;

                /* Pastikan background ada di belakang (z-index tidak berlaku di PDF tapi urutan penting) */
                background-attachment: fixed;
            @endif
        }

        /* --- STYLE LAINNYA --- */
        .header-wrapper {
            margin-top: 1cm; /* Jarak tambahan dari atas karena margin body 0 */
            width: 100%;
            margin-bottom: 20px;
        }

        .kop-image {
            width: 100%;
            height: auto;
            border-bottom: 2px solid #000;
        }

        /* Header Manual Text */
        .header-table {
            width: 100%;
            border-bottom: 3px double black;
            padding-bottom: 10px;
            margin-bottom: 20px;
        }
        .header-table td { border: none; vertical-align: middle; }
        .logo-cell { width: 15%; text-align: center; }
        .text-cell { width: 85%; text-align: center; }
        .lpk-name { font-size: 16pt; font-weight: bold; text-transform: uppercase; margin: 0; color: #D4AF37; }
        .lpk-address { font-size: 10pt; margin: 2px 0; }

        /* Tabel Data */
        .table-data {
            width: 100%;
            border-collapse: collapse;
            margin-top: 10px;
            /* Latar putih transparan supaya tulisan terbaca jelas di atas background */
            background-color: rgba(255, 255, 255, 0.7);
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
        .badge { padding: 2px 5px; border-radius: 3px; font-size: 8pt; font-weight: bold; border: 1px solid #ccc; background: #fff; }
    </style>
</head>
<body>

    {{-- KOP SURAT (Hanya Halaman 1) --}}
    <div class="header-wrapper">
        @if(isset($profile) && $profile->kop_surat)
            {{-- Gunakan public_path biasa untuk img tag, atau base64 juga boleh --}}
            <img src="{{ public_path('storage/' . $profile->kop_surat) }}" class="kop-image" alt="Kop Surat">
        @else
            {{-- Fallback Text Header --}}
            <table class="header-table">
                <tr>
                    <td class="logo-cell">
                        @if(isset($profile) && $profile->logo)
                            <img src="{{ public_path('storage/' . $profile->logo) }}" width="70">
                        @endif
                    </td>
                    <td class="text-cell">
                        <h1 class="lpk-name">{{ $profile->nama_lpk ?? 'LPK HACHIMITSU' }}</h1>
                        <p class="lpk-address">{{ $profile->alamat }}</p>
                        <p style="font-size:9pt; font-style:italic;">
                            {{ $profile->nomor_wa ? 'Telp: '.$profile->nomor_wa : '' }}
                            {{ $profile->email ? '| Email: '.$profile->email : '' }}
                        </p>
                    </td>
                </tr>
            </table>
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
                <td class="text-center"><span class="badge">{{ $student->status }}</span></td>
                <td style="font-size: 8pt;">{{ $student->email }}</td>
                <td>{{ $student->no_hp_peserta ?? '-' }}</td>
            </tr>
            @empty
            <tr>
                <td colspan="7" class="text-center" style="padding: 20px;">Tidak ada data siswa.</td>
            </tr>
            @endforelse
        </tbody>
    </table>

    {{-- TANDA TANGAN --}}
    <div style="margin-top: 40px; float: right; width: 200px; text-align: center; page-break-inside: avoid;">
        <p style="font-size: 10pt; margin-bottom: 60px;">Mengetahui,<br>Pimpinan</p>
        <p style="font-weight: bold; text-decoration: underline;">{{ $profile->nama_pimpinan ?? '....................' }}</p>
    </div>

</body>
</html>
