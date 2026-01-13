<!DOCTYPE html>
<html lang="id">
<head>
    <meta charset="UTF-8">
    <title>Bukti Kelulusan Administrasi - {{ $student->nama_lengkap }}</title>

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
        $studentFoto = ($student->foto) ? imageToBase64($student->foto) : null;
    @endphp

    <style>
        @page {
            size: A4 portrait;
            margin: 0;
        }

        body {
            font-family: Arial, sans-serif;
            font-size: 12px;
            line-height: 1.5;
            color: #333;

            /* PADDING KHUSUS KOP */
            padding-top: 4.5cm;
            padding-left: 2cm;
            padding-right: 2cm;
            padding-bottom: 2cm;

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
        .header-manual {
            position: absolute;
            top: 0.5cm;
            left: 1cm;
            right: 1cm;
            height: 3.5cm;
            border-bottom: 3px double #000;
        }
        .header-table { width: 100%; height: 100%; }
        .header-table td { vertical-align: middle; }
        .text-center { text-align: center; }
        .nama-lpk { font-size: 18px; font-weight: bold; text-transform: uppercase; margin: 0; }
        .sk-lpk { font-size: 11px; font-weight: bold; margin: 2px 0; }
        .alamat-lpk { font-size: 10px; margin: 0; }

        /* --- JUDUL DOKUMEN --- */
        .document-title {
            text-align: center;
            font-size: 16px;
            font-weight: bold;
            text-decoration: underline;
            margin-bottom: 5px;
            text-transform: uppercase;
        }
        .document-subtitle {
            text-align: center;
            font-size: 12px;
            margin-bottom: 25px;
        }

        /* --- FOTO & DATA --- */
        .content-table {
            width: 100%;
            margin-bottom: 10px;
            background-color: rgba(255, 255, 255, 0.85);
        }
        .photo-container {
            width: 130px;
            text-align: center;
            vertical-align: top;
            padding-right: 15px;
        }
        .photo-img {
            width: 3cm;
            height: 4cm;
            object-fit: cover;
            border: 1px solid #000;
            background-color: #eee;
        }
        .data-label {
            width: 140px;
            font-weight: bold;
            vertical-align: top;
        }
        .data-separator {
            width: 10px;
            vertical-align: top;
        }
        .data-value {
            vertical-align: top;
        }

        /* --- TANDA TANGAN --- */
        .signature-section {
            margin-top: 50px;
            width: 100%;
        }
        .signature-box {
            float: right;
            width: 250px;
            text-align: center;
            background-color: rgba(255, 255, 255, 0.6);
        }
        .nama-pimpinan {
            font-weight: bold;
            text-decoration: underline;
        }
        .status-box {
            font-weight: bold;
            padding: 5px 10px;
            border: 1px solid #000;
            display: inline-block;
            text-transform: uppercase;
            background: #fff;
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
            <table class="header-table">
                <tr>
                    <td width="15%" class="text-center">
                        @if($logoImage) <img src="{{ $logoImage }}" width="80"> @else <h3>LOGO</h3> @endif
                    </td>
                    <td width="85%" class="text-center">
                        <h1 class="nama-lpk">{{ $profile->nama_lpk ?? 'LPK HACHIMITSU' }}</h1>
                        @if(isset($profile->nomor_sk))
                            <p class="sk-lpk">Izin Dinas Tenaga Kerja No: {{ $profile->nomor_sk }}</p>
                        @endif
                        <p class="alamat-lpk">
                            {{ $profile->alamat ?? 'Alamat LPK Belum Diisi' }} <br>
                            Telp: {{ $profile->telepon_lpk ?? '-' }} | Email: {{ $profile->email_lpk ?? '-' }}
                        </p>
                    </td>
                </tr>
            </table>
        </div>
    @endif

    {{-- JUDUL DOKUMEN --}}
    <div class="document-title">BUKTI KELULUSAN ADMINISTRASI</div>
    <div class="document-subtitle">Nomor Peserta: {{ $student->participant_number ?? '-' }}</div>

    {{-- ISI DATA PESERTA --}}
    <table class="content-table">
        <tr>
            {{-- KOLOM FOTO --}}
            <td class="photo-container">
                @if($studentFoto)
                    <img src="{{ $studentFoto }}" class="photo-img">
                @else
                    <div class="photo-img" style="display:flex; align-items:center; justify-content:center; border: 1px dashed #999;">No Photo</div>
                @endif
                <br>
                <div style="margin-top: 5px; font-size: 10px; font-weight: bold;">FOTO PESERTA</div>
            </td>

            {{-- KOLOM DATA --}}
            <td>
                <table style="width: 100%;">
                    <tr>
                        <td class="data-label">Nama Lengkap</td>
                        <td class="data-separator">:</td>
                        <td class="data-value" style="text-transform: uppercase; font-weight: bold;">{{ $student->nama_lengkap }}</td>
                    </tr>
                    <tr>
                        <td class="data-label">Nomor KTP</td>
                        <td class="data-separator">:</td>
                        <td class="data-value">{{ $student->nomor_ktp ?? '-' }}</td>
                    </tr>
                    <tr>
                        <td class="data-label">Tempat, Tgl Lahir</td>
                        <td class="data-separator">:</td>
                        <td class="data-value">
                            {{ $student->tempat_lahir ?? '-' }},
                            {{ $student->tanggal_lahir ? \Carbon\Carbon::parse($student->tanggal_lahir)->isoFormat('D MMMM Y') : '-' }}
                        </td>
                    </tr>
                    <tr>
                        <td class="data-label">Jenis Kelamin</td>
                        <td class="data-separator">:</td>
                        <td class="data-value">{{ $student->jenis_kelamin }}</td>
                    </tr>
                    <tr>
                        <td class="data-label">Alamat Domisili</td>
                        <td class="data-separator">:</td>
                        <td class="data-value">{{ $student->alamat_domisili ?? '-' }}</td>
                    </tr>
                    <tr>
                        <td class="data-label">No. Handphone</td>
                        <td class="data-separator">:</td>
                        <td class="data-value">{{ $student->no_hp_peserta ?? '-' }}</td>
                    </tr>
                    <tr><td colspan="3" style="height: 10px;"></td></tr>
                    <tr>
                        <td class="data-label">Program Pilihan</td>
                        <td class="data-separator">:</td>
                        <td class="data-value" style="font-weight: bold; color: #000;">
                            {{ $student->program->judul ?? '-' }}
                        </td>
                    </tr>
                    <tr>
                        <td class="data-label">Status Seleksi</td>
                        <td class="data-separator">:</td>
                        <td class="data-value">
                            <span class="status-box">LOLOS VERIFIKASI ADMIN</span>
                        </td>
                    </tr>
                </table>
            </td>
        </tr>
    </table>

    <div style="margin-top: 20px; border: 1px dashed #000; padding: 10px; font-size: 11px; background-color: rgba(255,255,255,0.8);">
        <strong>Catatan untuk Peserta:</strong>
        <ol style="margin-top: 5px; margin-bottom: 0; padding-left: 20px;">
            <li>Simpan kartu bukti ini sebagai syarat mengikuti tahapan selanjutnya (Wawancara).</li>
            <li>Tunjukkan kartu ini kepada petugas saat jadwal wawancara berlangsung.</li>
            <li>Pastikan membawa dokumen asli (KTP, Ijazah, KK) saat wawancara untuk validasi fisik.</li>
        </ol>
    </div>

    {{-- TANDA TANGAN --}}
    <div class="signature-section">
        <div class="signature-box">
            @php
                $kota = 'Indonesia';
                if(isset($profile->alamat)) {
                    $parts = explode(',', $profile->alamat);
                    $kota = trim(end($parts));
                }
            @endphp

            <div style="margin-bottom: 10px;">
                {{ $kota }}, {{ \Carbon\Carbon::now()->isoFormat('D MMMM Y') }}
            </div>
            <div style="font-weight: bold; margin-bottom: 60px;">Pimpinan LPK,</div>

            <div class="nama-pimpinan">
                {{ $profile->nama_pimpinan ?? '(Nama Pimpinan)' }}
            </div>

            <div>Pimpinan / Direktur</div>
        </div>
        <div style="clear: both;"></div>
    </div>

</body>
</html>
