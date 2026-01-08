<!DOCTYPE html>
<html>
<head>
    <meta http-equiv="Content-Type" content="text/html; charset=utf-8"/>
    <title>Laporan Data Siswa</title>

    @php
        function imageToBase64($path) {
            $fullPath = public_path('storage/' . $path);
            if(file_exists($fullPath)) {
                $type = pathinfo($fullPath, PATHINFO_EXTENSION);
                $data = file_get_contents($fullPath);
                return 'data:image/' . $type . ';base64,' . base64_encode($data);
            }
            return null;
        }

        $bgImage = (isset($profile) && $profile->background_surat) ? imageToBase64($profile->background_surat) : null;
        $kopImage = (isset($profile) && $profile->kop_surat) ? imageToBase64($profile->kop_surat) : null;
        $logoImage = (isset($profile) && $profile->logo) ? imageToBase64($profile->logo) : null;
    @endphp

    <style>
        @page {
            size: A4 portrait;
            margin: 0;
        }

        body {
            font-family: Arial, Helvetica, sans-serif; /* Font lebih tegas */
            font-size: 9pt; /* UKURAN FONT DIPERKECIL BIAR MUAT */

            /* PADDING ATAS TETAP 4.5CM UNTUK KOP */
            padding-top: 4.5cm;

            /* PADDING SAMPING DIKURANGI JADI 1CM BIAR TABEL LEBIH LEBAR */
            padding-left: 1cm;
            padding-right: 1cm;
            padding-bottom: 1.5cm;

            @if($bgImage)
                background-image: url("{{ $bgImage }}");
                background-repeat: no-repeat;
                background-position: center center;
                background-size: 100% 100%;
            @endif
        }

        /* KOP SURAT */
        .kop-wrapper {
            position: absolute;
            top: 0;
            left: 0;
            width: 100%;
            height: 4.5cm;
            z-index: -1;
        }
        .img-kop {
            width: 100%;
            height: 100%;
            object-fit: cover;
        }

        /* HEADER MANUAL (FALLBACK) */
        /* Disesuaikan posisinya karena margin body berubah */
        .header-manual {
            position: absolute;
            top: 0.5cm;
            left: 1cm; /* Ikuti padding body */
            right: 1cm;
            height: 3.5cm;
            border-bottom: 2px solid #000;
        }
        .tbl-header td { vertical-align: middle; }
        .text-lpk { font-size: 14pt; font-weight: bold; text-transform: uppercase; margin: 0; }

        /* TABEL DATA SISWA - VERSI RAPI */
        .table-data {
            width: 100%;
            border-collapse: collapse;
            background-color: rgba(255, 255, 255, 0.9);
        }

        .table-data th, .table-data td {
            border: 1px solid #000;
            /* Padding diperkecil biar hemat tempat vertikal */
            padding: 4px 5px;
            vertical-align: middle; /* Tengah secara vertikal biar rapi */
            line-height: 1.2; /* Spasi antar baris diperpadat */
        }

        .table-data th {
            background-color: #f0f0f0;
            text-transform: uppercase;
            font-size: 8pt; /* Header sedikit lebih kecil */
            font-weight: bold;
            text-align: center;
        }

        /* UTILITY CLASS KHUSUS KOLOM */
        /* Total 100% */
        .col-no { width: 4%; text-align: center; }
        .col-nama { width: 22%; }
        .col-ktp { width: 14%; white-space: nowrap; } /* KTP Dilarang Turun Baris */
        .col-prog { width: 20%; }
        .col-sts { width: 8%; text-align: center; white-space: nowrap;}
        .col-kontak { width: 32%; font-size: 8pt; } /* Kontak huruf lebih kecil dikit */

        .badge {
            background: #fff; border: 1px solid #333;
            padding: 2px 4px; border-radius: 3px; font-size: 7pt;
            display: inline-block;
        }

        .text-center { text-align: center; }
        .text-bold { font-weight: bold; }
        .text-upper { text-transform: uppercase; }

        /* Helper break word untuk email panjang */
        .break-word {
            word-wrap: break-word;
            word-break: break-all;
        }
    </style>
</head>
<body>

    {{-- KOP SURAT --}}
    @if($kopImage)
        <div class="kop-wrapper">
            <img src="{{ $kopImage }}" class="img-kop">
        </div>
    @else
        <div class="header-manual">
            <table class="tbl-header" style="width: 100%;">
                <tr>
                    <td style="width: 80px;">
                        @if($logoImage) <img src="{{ $logoImage }}" width="70"> @endif
                    </td>
                    <td class="text-center">
                        <h1 class="text-lpk">{{ $profile->nama_lpk ?? 'NAMA LPK' }}</h1>
                        <p style="margin: 0; font-size: 9pt;">{{ $profile->alamat }}</p>
                        <p style="margin: 0; font-size: 8pt;">{{ $profile->email_lpk }} | {{ $profile->nomor_wa }}</p>
                    </td>
                </tr>
            </table>
        </div>
    @endif

    {{-- JUDUL --}}
    <div class="text-center" style="margin-bottom: 15px;">
        <h3 style="text-decoration: underline; margin:0; font-size: 12pt;">LAPORAN DATA SISWA</h3>
        <p style="font-size: 8pt; margin-top: 3px; color: #555;">Dicetak: {{ now()->translatedFormat('d F Y, H:i') }} WIB</p>
    </div>

    {{-- TABEL --}}
    <table class="table-data">
        <thead>
            <tr>
                <th class="col-no">No</th>
                <th class="col-nama">Nama Lengkap</th>
                <th class="col-ktp">No. KTP</th>
                <th class="col-prog">Program</th>
                <th class="col-sts">Status</th>
                <th class="col-kontak">Email / HP</th>
            </tr>
        </thead>
        <tbody>
            @forelse($students as $index => $student)
            <tr>
                <td class="text-center">{{ $index + 1 }}</td>
                <td class="text-bold text-upper">
                    {{ $student->nama_lengkap }}
                </td>
                <td class="text-center font-mono">
                    {{-- Font Mono biar angka KTP rapi --}}
                    <span style="font-family: 'Courier New', monospace; letter-spacing: -0.5px;">
                        {{ $student->nomor_ktp ?? '-' }}
                    </span>
                </td>
                <td>{{ $student->program->judul ?? '-' }}</td>
                <td class="text-center">
                    <span class="badge">{{ $student->status }}</span>
                </td>
                <td>
                    <div class="break-word">{{ $student->email }}</div>
                    <div style="margin-top:2px; color:#444;">{{ $student->no_hp_peserta ?? '-' }}</div>
                </td>
            </tr>
            @empty
            <tr>
                <td colspan="6" class="text-center" style="padding: 15px;">Data siswa tidak ditemukan.</td>
            </tr>
            @endforelse
        </tbody>
    </table>

    {{-- TANDA TANGAN --}}
    <div style="margin-top: 25px; width: 40%; float: right; text-align: center; page-break-inside: avoid;">
        <p style="margin-bottom: 60px;">
            {{ $profile->kabupaten ?? 'Tempat' }}, {{ now()->translatedFormat('d F Y') }}<br>
            Pimpinan LPK,
        </p>
        <p style="font-weight: bold; text-decoration: underline;">
            {{ $profile->nama_pimpinan ?? '....................' }}
        </p>
    </div>

</body>
</html>
