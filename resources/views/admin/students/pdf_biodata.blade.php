<!DOCTYPE html>
<html>
<head>
    <meta http-equiv="Content-Type" content="text/html; charset=utf-8"/>
    <title>Biodata Siswa - {{ $student->nama_lengkap }}</title>
    
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
        @page { size: A4 portrait; margin: 0; }
        body {
            font-family: Arial, Helvetica, sans-serif;
            font-size: 10pt; line-height: 1.5; color: #333;
            padding: 4.5cm 1.5cm 2cm 1.5cm;
            @if($bgImage)
                background-image: url("{{ $bgImage }}"); background-repeat: no-repeat;
                background-position: center center; background-size: 100% 100%;
            @endif
        }
        /* Kop */
        .kop-wrapper { position: absolute; top: 0; left: 0; width: 100%; height: 4.5cm; z-index: -1; }
        .img-kop { width: 100%; height: 100%; object-fit: cover; }
        
        /* Fallback Header */
        .header-manual { position: absolute; top: 0.5cm; left: 1cm; right: 1cm; height: 3.5cm; border-bottom: 3px double #000; }
        .header-table { width: 100%; height: 100%; }
        .header-table td { vertical-align: middle; }
        .text-center { text-align: center; }
        .nama-lpk { font-size: 18px; font-weight: bold; text-transform: uppercase; margin: 0; }
        .sk-lpk { font-size: 11px; font-weight: bold; margin: 2px 0; }
        .alamat-lpk { font-size: 10px; margin: 0; }

        /* Titles */
        .doc-title { text-align: center; font-size: 16pt; font-weight: bold; text-decoration: underline; margin-bottom: 5px; text-transform: uppercase; color: #111; }
        .doc-subtitle { text-align: center; font-size: 11pt; color: #555; margin-bottom: 30px; }
        
        /* Section Title */
        .section-title {
            background-color: #2c3e50;
            color: #ffffff;
            padding: 5px 10px;
            font-weight: bold;
            font-size: 11pt;
            margin-top: 20px;
            margin-bottom: 10px;
            border-left: 5px solid #e74c3c;
            text-transform: uppercase;
        }

        /* Top Layout (Photo & Main Info) */
        .top-container { width: 100%; margin-bottom: 15px; }
        .photo-cell { width: 3.5cm; vertical-align: top; text-align: center; }
        .photo-box {
            width: 3cm; height: 4cm; border: 2px solid #bdc3c7; background-color: #ecf0f1;
            display: inline-block; overflow: hidden;
        }
        .photo-box img { width: 100%; height: 100%; object-fit: cover; }
        .main-info-cell { vertical-align: top; padding-left: 15px; }
        
        /* Data Table */
        .data-table { width: 100%; border-collapse: collapse; }
        .data-table td { padding: 3px 5px; vertical-align: top; }
        .label-col { width: 130px; font-weight: bold; color: #555; }
        .colon-col { width: 10px; font-weight: bold; color: #555; }
        .val-col { color: #111; }

        /* List Table */
        .list-table { width: 100%; border-collapse: collapse; margin-bottom: 15px; font-size: 9.5pt; }
        .list-table th, .list-table td { border: 1px solid #bdc3c7; padding: 6px; }
        .list-table th { background-color: #ecf0f1; color: #2c3e50; font-weight: bold; text-align: center; text-transform: uppercase; }
        .list-table tr:nth-child(even) { background-color: #f9f9f9; }
        
        /* Signature */
        .signature-box { float: right; width: 250px; text-align: center; margin-top: 40px; }
        .signature-name { font-weight: bold; text-decoration: underline; margin-top: 5px; text-transform: uppercase; }
        .clear { clear: both; }
    </style>
</head>
<body>
    {{-- KOP --}}
    @if($kopImage)
        <div class="kop-wrapper"><img src="{{ $kopImage }}" class="img-kop"></div>
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
    <div class="doc-title">Biodata Peserta Pelatihan</div>
    <div class="doc-subtitle">Nomor Pendaftaran: {{ $student->participant_number ?? '-' }}</div>

    {{-- BAGIAN ATAS: FOTO & DATA UTAMA --}}
    <table class="top-container">
        <tr>
            <td class="photo-cell">
                <div class="photo-box">
                    @if($studentFoto)
                        <img src="{{ $studentFoto }}">
                    @else
                        <div style="padding-top: 60px; color: #95a5a6; font-size: 8pt;">FOTO 3x4</div>
                    @endif
                </div>
            </td>
            <td class="main-info-cell">
                <table class="data-table">
                    <tr><td class="label-col" style="width:110px;">Nama Lengkap</td><td class="colon-col">:</td><td class="val-col" style="font-weight: bold; text-transform: uppercase; font-size: 12pt;">{{ $student->nama_lengkap }}</td></tr>
                    <tr><td class="label-col">Program Pilihan</td><td class="colon-col">:</td><td class="val-col" style="font-weight: bold;">{{ $student->program->judul ?? '-' }}</td></tr>
                    <tr><td class="label-col">Email</td><td class="colon-col">:</td><td class="val-col">{{ $student->email }}</td></tr>
                    <tr><td class="label-col">No. Handphone</td><td class="colon-col">:</td><td class="val-col">{{ $student->no_hp_peserta ?? '-' }}</td></tr>
                    <tr><td class="label-col">Status Peserta</td><td class="colon-col">:</td><td class="val-col"><span style="background-color: #e74c3c; color: #fff; padding: 2px 6px; font-size: 8pt; font-weight: bold; border-radius: 3px;">{{ $student->status }}</span></td></tr>
                </table>
            </td>
        </tr>
    </table>

    {{-- DATA PRIBADI --}}
    <div class="section-title">A. Data Pribadi</div>
    <table class="data-table">
        <tr>
            <td class="label-col">Nomor KTP (NIK)</td><td class="colon-col">:</td><td class="val-col">{{ $student->nomor_ktp ?? '-' }}</td>
            <td class="label-col" style="width:90px;">Nomor KK</td><td class="colon-col">:</td><td class="val-col">{{ $student->nomor_kk ?? '-' }}</td>
        </tr>
        <tr>
            <td class="label-col">Tempat, Tgl Lahir</td><td class="colon-col">:</td><td class="val-col">{{ $student->tempat_lahir ?? '-' }}, {{ $student->tanggal_lahir ? \Carbon\Carbon::parse($student->tanggal_lahir)->isoFormat('D MMMM Y') : '-' }}</td>
            <td class="label-col">Jenis Kelamin</td><td class="colon-col">:</td><td class="val-col">{{ $student->jenis_kelamin == 'L' ? 'Laki-laki' : 'Perempuan' }}</td>
        </tr>
        <tr>
            <td class="label-col">Paspor / NPWP</td><td class="colon-col">:</td><td class="val-col">{{ $student->nomor_paspor ? 'Paspor: '.$student->nomor_paspor : '-' }} / {{ $student->nomor_npwp ? 'NPWP: '.$student->nomor_npwp : '-' }}</td>
            <td class="label-col">Agama</td><td class="colon-col">:</td><td class="val-col">{{ $student->agama ?? '-' }}</td>
        </tr>
        <tr>
            <td class="label-col">Tinggi/Berat Badan</td><td class="colon-col">:</td><td class="val-col">{{ $student->tinggi_badan ?? '-' }} cm / {{ $student->berat_badan ?? '-' }} kg</td>
            <td class="label-col">Gol. Darah</td><td class="colon-col">:</td><td class="val-col">{{ $student->golongan_darah ?? '-' }}</td>
        </tr>
        <tr>
            <td class="label-col">Status Pernikahan</td><td class="colon-col">:</td><td class="val-col" colspan="4">{{ $student->status_pernikahan ?? '-' }}</td>
        </tr>
    </table>

    {{-- ALAMAT --}}
    <div class="section-title">B. Alamat & Kontak Darurat</div>
    <table class="data-table">
        <tr>
            <td class="label-col">Alamat Sesuai KTP</td><td class="colon-col">:</td>
            <td class="val-col">{{ $student->alamat_ktp ?? '-' }}<br>{{ $student->kota_ktp ?? '' }} {{ $student->provinsi_ktp ? ', '.$student->provinsi_ktp : '' }}</td>
        </tr>
        <tr>
            <td class="label-col">Alamat Domisili</td><td class="colon-col">:</td>
            <td class="val-col">{{ $student->alamat_domisili ?? '(Sama dengan alamat KTP)' }}</td>
        </tr>
        <tr>
            <td class="label-col">No. HP Orang Tua</td><td class="colon-col">:</td>
            <td class="val-col">{{ $student->no_hp_ortu ?? '-' }}</td>
        </tr>
    </table>

    {{-- PENDIDIKAN --}}
    <div class="section-title">C. Riwayat Pendidikan</div>
    <table class="list-table">
        <thead>
            <tr>
                <th style="width: 15%;">Tingkat</th>
                <th style="width: 35%;">Nama Institusi</th>
                <th style="width: 20%;">Jurusan</th>
                <th style="width: 15%;">Tahun Lulus</th>
                <th style="width: 15%;">Nilai</th>
            </tr>
        </thead>
        <tbody>
            @forelse($student->educations as $edu)
                <tr>
                    <td style="text-align: center;">{{ $edu->tingkat }}</td>
                    <td>{{ $edu->nama_institusi }}</td>
                    <td>{{ $edu->jurusan ?? '-' }}</td>
                    <td style="text-align: center;">{{ $edu->tahun_lulus ?? '-' }}</td>
                    <td style="text-align: center;">{{ $edu->nilai_rata_rata ?? '-' }}</td>
                </tr>
            @empty
                <tr><td colspan="5" style="text-align: center; color: #7f8c8d;">Belum ada data riwayat pendidikan.</td></tr>
            @endforelse
        </tbody>
    </table>

    {{-- KELUARGA --}}
    <div class="section-title">D. Susunan Keluarga</div>
    <table class="list-table">
        <thead>
            <tr>
                <th style="width: 15%;">Hubungan</th>
                <th style="width: 35%;">Nama Lengkap</th>
                <th style="width: 10%;">L/P</th>
                <th style="width: 25%;">Pekerjaan</th>
                <th style="width: 15%;">Usia</th>
            </tr>
        </thead>
        <tbody>
            @forelse($student->families as $fam)
                <tr>
                    <td style="text-align: center;">{{ $fam->hubungan }}</td>
                    <td>{{ $fam->nama }}</td>
                    <td style="text-align: center;">{{ $fam->jenis_kelamin }}</td>
                    <td>{{ $fam->pekerjaan ?? '-' }}</td>
                    <td style="text-align: center;">{{ $fam->usia ? $fam->usia.' Thn' : '-' }}</td>
                </tr>
            @empty
                <tr><td colspan="5" style="text-align: center; color: #7f8c8d;">Belum ada data susunan keluarga.</td></tr>
            @endforelse
        </tbody>
    </table>

    {{-- PENGALAMAN --}}
    @if($student->pernah_bekerja)
    <div class="section-title">E. Pengalaman Kerja / Organisasi</div>
    <table class="list-table">
        <thead>
            <tr>
                <th style="width: 35%;">Nama Instansi</th>
                <th style="width: 25%;">Posisi / Jabatan</th>
                <th style="width: 20%;">Mulai</th>
                <th style="width: 20%;">Selesai</th>
            </tr>
        </thead>
        <tbody>
            @forelse($student->experiences as $exp)
                <tr>
                    <td>{{ $exp->nama_instansi }}</td>
                    <td>{{ $exp->posisi ?? '-' }}</td>
                    <td style="text-align: center;">{{ $exp->tanggal_mulai ? \Carbon\Carbon::parse($exp->tanggal_mulai)->format('M Y') : '-' }}</td>
                    <td style="text-align: center;">{{ $exp->tanggal_selesai ? \Carbon\Carbon::parse($exp->tanggal_selesai)->format('M Y') : '-' }}</td>
                </tr>
            @empty
                <tr><td colspan="4" style="text-align: center; color: #7f8c8d;">Belum ada data pengalaman.</td></tr>
            @endforelse
        </tbody>
    </table>
    @endif

    {{-- TANDA TANGAN --}}
    <div class="signature-box">
        <p style="margin-bottom: 5px;">Dicetak Tanggal: {{ date('d F Y') }}</p>
        <p style="margin-bottom: 10px;">Calon Peserta Pelatihan,</p>
        @if($student->signature && $sigImage = imageToBase64($student->signature))
            <img src="{{ $sigImage }}" style="height: 60px; object-fit: contain;">
        @else
            <div style="height: 60px;"></div>
        @endif
        <div class="signature-name">{{ $student->nama_lengkap }}</div>
    </div>
    <div class="clear"></div>

</body>
</html>
