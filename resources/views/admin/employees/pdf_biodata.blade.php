<!DOCTYPE html>
<html>
<head>
    <meta http-equiv="Content-Type" content="text/html; charset=utf-8"/>
    <title>Biodata Pegawai - {{ $employee->nama }}</title>
    
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
        $empFoto = ($employee->foto) ? imageToBase64($employee->foto) : null;
    @endphp

    <style>
        @page {
            size: A4 portrait;
            margin: 0;
        }

        body {
            font-family: Arial, Helvetica, sans-serif;
            font-size: 10pt;
            line-height: 1.4;
            
            /* PADDING KHUSUS KOP & MARGIN HALAMAN */
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

        /* JUDUL DOKUMEN */
        .document-title {
            text-align: center;
            font-size: 14pt;
            font-weight: bold;
            text-decoration: underline;
            margin-bottom: 5px;
            text-transform: uppercase;
        }
        .document-subtitle {
            text-align: center;
            font-size: 10pt;
            color: #555;
            margin-bottom: 20px;
        }

        /* Layout Foto */
        .photo-container {
            float: right; width: 3cm; height: 4cm;
            border: 1px solid #ccc; margin-left: 15px; margin-bottom: 10px;
            overflow: hidden;
            background-color: #eee;
        }
        .photo-container img { width: 100%; height: 100%; object-fit: cover; }

        /* Data Baris */
        .row { margin-bottom: 4px; clear: both; }
        .label { float: left; width: 140px; font-weight: bold; }
        .colon { float: left; width: 15px; }
        .value { float: left; width: 360px; }
        .clear { clear: both; }

        /* Section Header */
        .section-title {
            font-weight: bold; font-size: 11pt; background-color: rgba(238, 238, 238, 0.8);
            padding: 5px; margin-top: 15px; margin-bottom: 8px;
            border-bottom: 1px solid #999;
        }

        /* Tabel Data (Pendidikan/Keluarga) */
        table { width: 100%; border-collapse: collapse; margin-bottom: 10px; font-size: 9pt; background-color: rgba(255, 255, 255, 0.8); }
        table th, table td { border: 1px solid #999; padding: 4px 6px; text-align: left; }
        table th { background-color: rgba(245, 245, 245, 0.9); text-align: center; font-weight: bold; }
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
                        <h1 class="nama-lpk">{{ $profile->nama_lpk ?? 'NAMA LPK' }}</h1>
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

    {{-- JUDUL --}}
    <div class="document-title">Biodata Pegawai</div>
    <div class="document-subtitle">{{ config('app.name', 'LPK Profile') }}</div>

    {{-- FOTO --}}
    <div class="photo-container">
        @if($empFoto)
            <img src="{{ $empFoto }}">
        @else
            <div style="text-align: center; padding-top: 50px; color: #aaa; font-size: 8pt;">No Photo</div>
        @endif
    </div>

    {{-- DATA UTAMA --}}
    <div class="row"><div class="label">Nama Lengkap</div><div class="colon">:</div><div class="value">{{ $employee->nama }}</div></div>
    <div class="row"><div class="label">NIP</div><div class="colon">:</div><div class="value">{{ $employee->nip ?? '-' }}</div></div>
    <div class="row"><div class="label">Jabatan</div><div class="colon">:</div><div class="value">{{ $employee->jabatan }}</div></div>
    <div class="row"><div class="label">Status Pegawai</div><div class="colon">:</div><div class="value">{{ $employee->status_kepegawaian }}</div></div>
    <div class="row"><div class="label">Email</div><div class="colon">:</div><div class="value">{{ $employee->email }}</div></div>
    <div class="row"><div class="label">No. Telepon</div><div class="colon">:</div><div class="value">{{ $employee->telepon ?? '-' }}</div></div>

    <div class="clear"></div>

    {{-- DATA PRIBADI --}}
    <div class="section-title">DATA PRIBADI</div>
    <div class="row"><div class="label">NIK (KTP)</div><div class="colon">:</div><div class="value">{{ $employee->nomor_ktp ?? '-' }}</div></div>
    <div class="row"><div class="label">Nomor KK</div><div class="colon">:</div><div class="value">{{ $employee->nomor_kk ?? '-' }}</div></div>
    <div class="row"><div class="label">NPWP</div><div class="colon">:</div><div class="value">{{ $employee->nomor_npwp ?? '-' }}</div></div>
    <div class="row">
        <div class="label">TTL</div><div class="colon">:</div>
        <div class="value">{{ $employee->tempat_lahir }}, {{ $employee->tanggal_lahir ? $employee->tanggal_lahir->format('d F Y') : '-' }}</div>
    </div>
    <div class="row">
        <div class="label">Jenis Kelamin</div><div class="colon">:</div>
        <div class="value">{{ $employee->jenis_kelamin == 'L' ? 'Laki-laki' : 'Perempuan' }}</div>
    </div>
    <div class="row">
        <div class="label">Gol. Darah / Agama</div><div class="colon">:</div>
        <div class="value">{{ $employee->golongan_darah ?? '-' }} / {{ $employee->agama ?? '-' }}</div>
    </div>
    <div class="row">
        <div class="label">Tinggi / Berat Badan</div><div class="colon">:</div>
        <div class="value">{{ $employee->tinggi_badan ?? '-' }} cm / {{ $employee->berat_badan ?? '-' }} kg</div>
    </div>
    <div class="row">
        <div class="label">Status Pernikahan</div><div class="colon">:</div>
        <div class="value">{{ $employee->status_pernikahan ?? '-' }}</div>
    </div>

    {{-- ALAMAT --}}
    <div class="section-title">ALAMAT</div>
    <div class="row">
        <div class="label">Alamat KTP</div><div class="colon">:</div>
        <div class="value">
            {{ $employee->alamat_ktp ?? '-' }}<br>
            {{ $employee->kota_ktp }} - {{ $employee->provinsi_ktp }}
        </div>
    </div>
    <br>
    <div class="row">
        <div class="label">Alamat Domisili</div><div class="colon">:</div>
        <div class="value">{{ $employee->alamat_domisili ?? '(Sama dengan KTP)' }}</div>
    </div>

    {{-- RIWAYAT PENDIDIKAN --}}
    <div class="section-title">RIWAYAT PENDIDIKAN</div>
    <table>
        <thead>
            <tr>
                <th style="width: 50px;">Jenjang</th>
                <th>Nama Sekolah / Universitas</th>
                <th>Jurusan</th>
                <th style="width: 60px;">Lulus</th>
                <th style="width: 40px;">Nilai</th>
            </tr>
        </thead>
        <tbody>
            @forelse($employee->educations as $edu)
                <tr>
                    <td style="text-align: center;">{{ $edu->jenjang }}</td>
                    <td>{{ $edu->nama_sekolah }}</td>
                    <td>{{ $edu->jurusan ?? '-' }}</td>
                    <td style="text-align: center;">{{ $edu->tahun_lulus }}</td>
                    <td style="text-align: center;">{{ $edu->nilai_akhir ?? '-' }}</td>
                </tr>
            @empty
                <tr><td colspan="5" style="text-align: center; color: #777;">Belum ada data pendidikan</td></tr>
            @endforelse
        </tbody>
    </table>

    {{-- DATA KELUARGA --}}
    <div class="section-title">DATA KELUARGA</div>
    <table>
        <thead>
            <tr>
                <th style="width: 80px;">Hubungan</th>
                <th>Nama Lengkap</th>
                <th>Pekerjaan</th>
                <th style="width: 90px;">No. HP</th>
            </tr>
        </thead>
        <tbody>
            @forelse($employee->families as $fam)
                <tr>
                    <td style="text-align: center;">{{ $fam->hubungan }}</td>
                    <td>{{ $fam->nama_lengkap }}</td>
                    <td>{{ $fam->pekerjaan ?? '-' }}</td>
                    <td style="text-align: center;">{{ $fam->no_hp ?? '-' }}</td>
                </tr>
            @empty
                <tr><td colspan="4" style="text-align: center; color: #777;">Belum ada data keluarga</td></tr>
            @endforelse
        </tbody>
    </table>

    {{-- TANDA TANGAN --}}
    <div style="margin-top: 40px; float: right; width: 220px; text-align: center; page-break-inside: avoid;">
        <p>Dicetak Tanggal: {{ date('d F Y') }}</p>
        <br><br><br>
        <p style="border-top: 1px solid #000; font-weight: bold; display: inline-block; min-width: 150px;">
            {{ $employee->nama }}
        </p>
    </div>

</body>
</html>
