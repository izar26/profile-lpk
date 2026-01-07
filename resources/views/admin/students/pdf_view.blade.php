<!DOCTYPE html>
<html>
<head>
    <meta http-equiv="Content-Type" content="text/html; charset=utf-8"/>
    <title>Laporan Data Siswa</title>
    <style>
        body {
            font-family: sans-serif;
            font-size: 10pt;

            /* [BARU] Logic Background Watermark */
            @if(isset($profile) && $profile->background_surat)
                background-image: url('{{ public_path("storage/" . $profile->background_surat) }}');
                background-repeat: no-repeat;
                background-position: center;
                background-size: cover;
            @endif
        }

        /* --- HEADER / KOP SURAT --- */
        .header-wrapper {
            width: 100%;
            margin-bottom: 20px;
        }
        .kop-image {
            width: 100%;
            height: auto;
            display: block;
            border-bottom: 2px solid #000;
            margin-bottom: 10px;
        }

        /* Fallback Header Text Style */
        .header-table {
            width: 100%;
            border-bottom: 3px double black;
            padding-bottom: 10px;
            margin-bottom: 20px;
        }
        .header-table td {
            border: none;
            vertical-align: middle;
        }
        .logo-cell {
            width: 15%;
            text-align: center;
        }
        .text-cell {
            width: 85%;
            text-align: center;
        }
        .lpk-name {
            font-size: 16pt;
            font-weight: bold;
            text-transform: uppercase;
            margin: 0;
            color: #000; /* Hitam lebih standar utk laporan, emas opsional */
        }
        .lpk-address {
            font-size: 10pt;
            margin: 2px 0;
        }
        .lpk-contact {
            font-size: 9pt;
            font-style: italic;
        }

        /* --- DATA TABLE STYLE --- */
        .table-data {
            width: 100%;
            border-collapse: collapse;
            margin-top: 10px;
            background-color: rgba(255, 255, 255, 0.85); /* Putih transparan agar teks terbaca di atas watermark */
        }
        .table-data th, .table-data td {
            border: 1px solid #444;
            padding: 6px;
            text-align: left;
            vertical-align: middle;
            font-size: 9pt;
        }
        .table-data th {
            background-color: #e0e0e0; /* Abu-abu muda standar */
            font-weight: bold;
            text-align: center;
            text-transform: uppercase;
        }
        /* Zebra Striping (Opsional) - Baris Genap agak gelap dikit */
        .table-data tr:nth-child(even) {
            background-color: rgba(0, 0, 0, 0.03);
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
    <div class="header-wrapper">
        @if(isset($profile) && $profile->kop_surat)
            {{-- OPSI A: Gambar Kop Full --}}
            <img src="{{ public_path('storage/' . $profile->kop_surat) }}" class="kop-image" alt="Kop Surat">
        @else
            {{-- OPSI B: Teks Manual (Default) --}}
            <table class="header-table">
                <tr>
                    <td class="logo-cell">
                        @if(isset($profile) && $profile->logo)
                            <img src="{{ public_path('storage/' . $profile->logo) }}" width="70" height="auto">
                        @endif
                    </td>
                    <td class="text-cell">
                        <h1 class="lpk-name">{{ $profile->nama_lpk ?? config('app.name') }}</h1>
                        @if(isset($profile))
                            <p class="lpk-address">{{ $profile->alamat }}</p>
                            <p class="lpk-contact">
                                @if($profile->nomor_wa) Telp/WA: {{ $profile->nomor_wa }} @endif
                                @if($profile->email) | Email: {{ $profile->email }} @endif
                            </p>
                        @endif
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

    {{-- FOOTER / TANDA TANGAN (Opsional untuk Laporan) --}}
    <div style="margin-top: 40px; float: right; width: 200px; text-align: center;">
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
