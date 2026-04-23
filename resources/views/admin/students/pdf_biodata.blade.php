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
    <div class="document-title">Biodata Peserta Pelatihan</div>
    <div class="document-subtitle">Nomor Peserta: {{ $student->participant_number ?? '-' }}</div>

    {{-- FOTO --}}
    <div class="photo-container">
        @if($studentFoto)
            <img src="{{ $studentFoto }}">
        @else
            <div style="text-align: center; padding-top: 50px; color: #aaa; font-size: 8pt;">No Photo</div>
        @endif
    </div>

    {{-- DATA UTAMA --}}
    <div class="row"><div class="label">Nama Lengkap</div><div class="colon">:</div><div class="value" style="font-weight: bold; text-transform: uppercase;">{{ $student->nama_lengkap }}</div></div>
    <div class="row"><div class="label">Program Pilihan</div><div class="colon">:</div><div class="value">{{ $student->program->judul ?? '-' }}</div></div>
    <div class="row"><div class="label">Email</div><div class="colon">:</div><div class="value">{{ $student->email }}</div></div>
    <div class="row"><div class="label">No. HP (WA)</div><div class="colon">:</div><div class="value">{{ $student->no_hp_peserta ?? '-' }}</div></div>
    <div class="row"><div class="label">Status Peserta</div><div class="colon">:</div><div class="value">{{ $student->status }}</div></div>

    <div class="clear"></div>

    {{-- DATA PRIBADI --}}
    <div class="section-title">DATA PRIBADI</div>
    <div class="row"><div class="label">No. KTP (NIK)</div><div class="colon">:</div><div class="value">{{ $student->nomor_ktp ?? '-' }}</div></div>
    <div class="row"><div class="label">Nomor KK</div><div class="colon">:</div><div class="value">{{ $student->nomor_kk ?? '-' }}</div></div>
    <div class="row"><div class="label">Paspor / NPWP</div><div class="colon">:</div><div class="value">{{ $student->nomor_paspor ? 'Paspor: '.$student->nomor_paspor : '-' }} / {{ $student->nomor_npwp ? 'NPWP: '.$student->nomor_npwp : '-' }}</div></div>
    <div class="row">
        <div class="label">TTL</div><div class="colon">:</div>
        <div class="value">{{ $student->tempat_lahir ?? '-' }}, {{ $student->tanggal_lahir ? \Carbon\Carbon::parse($student->tanggal_lahir)->isoFormat('D MMMM Y') : '-' }}</div>
    </div>
    <div class="row">
        <div class="label">Jenis Kelamin</div><div class="colon">:</div>
        <div class="value">{{ $student->jenis_kelamin }}</div>
    </div>
    <div class="row">
        <div class="label">Gol. Darah / Agama</div><div class="colon">:</div>
        <div class="value">{{ $student->golongan_darah ?? '-' }} / {{ $student->agama ?? '-' }}</div>
    </div>
    <div class="row">
        <div class="label">Tinggi / Berat Badan</div><div class="colon">:</div>
        <div class="value">{{ $student->tinggi_badan ?? '-' }} cm / {{ $student->berat_badan ?? '-' }} kg</div>
    </div>
    <div class="row">
        <div class="label">Status Pernikahan</div><div class="colon">:</div>
        <div class="value">{{ $student->status_pernikahan ?? '-' }}</div>
    </div>

    {{-- ALAMAT --}}
    <div class="section-title">ALAMAT</div>
    <div class="row">
        <div class="label">Alamat KTP</div><div class="colon">:</div>
        <div class="value">
            {{ $student->alamat_ktp ?? '-' }}<br>
            {{ $student->kota_ktp ?? '' }} {{ $student->provinsi_ktp ? ', '.$student->provinsi_ktp : '' }}
        </div>
    </div>
    <br>
    <div class="row">
        <div class="label">Alamat Domisili</div><div class="colon">:</div>
        <div class="value">{{ $student->alamat_domisili ?? '(Sama dengan KTP)' }}</div>
    </div>
    <div class="row">
        <div class="label">No HP Orang Tua</div><div class="colon">:</div>
        <div class="value">{{ $student->no_hp_ortu ?? '-' }}</div>
    </div>

    {{-- RIWAYAT PENDIDIKAN --}}
    <div class="section-title">RIWAYAT PENDIDIKAN</div>
    <table>
        <thead>
            <tr>
                <th style="width: 50px;">Tingkat</th>
                <th>Nama Institusi</th>
                <th>Jurusan</th>
                <th style="width: 60px;">Masuk</th>
                <th style="width: 60px;">Lulus</th>
                <th style="width: 40px;">Nilai</th>
            </tr>
        </thead>
        <tbody>
            @forelse($student->educations as $edu)
                <tr>
                    <td style="text-align: center;">{{ $edu->tingkat }}</td>
                    <td>{{ $edu->nama_institusi }}</td>
                    <td>{{ $edu->jurusan ?? '-' }}</td>
                    <td style="text-align: center;">{{ $edu->tahun_masuk ?? '-' }}</td>
                    <td style="text-align: center;">{{ $edu->tahun_lulus ?? '-' }}</td>
                    <td style="text-align: center;">{{ $edu->nilai_rata_rata ?? '-' }}</td>
                </tr>
            @empty
                <tr><td colspan="6" style="text-align: center; color: #777;">Belum ada data pendidikan</td></tr>
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
                <th>L/P</th>
                <th>Pekerjaan</th>
                <th style="width: 40px;">Usia</th>
            </tr>
        </thead>
        <tbody>
            @forelse($student->families as $fam)
                <tr>
                    <td style="text-align: center;">{{ $fam->hubungan }}</td>
                    <td>{{ $fam->nama }}</td>
                    <td style="text-align: center;">{{ $fam->jenis_kelamin }}</td>
                    <td>{{ $fam->pekerjaan ?? '-' }}</td>
                    <td style="text-align: center;">{{ $fam->usia ?? '-' }}</td>
                </tr>
            @empty
                <tr><td colspan="5" style="text-align: center; color: #777;">Belum ada data keluarga</td></tr>
            @endforelse
        </tbody>
    </table>

    {{-- DATA PENGALAMAN --}}
    @if($student->pernah_bekerja)
    <div class="section-title">PENGALAMAN KERJA / ORGANISASI</div>
    <table>
        <thead>
            <tr>
                <th>Nama Instansi</th>
                <th>Posisi</th>
                <th style="width: 80px;">Mulai</th>
                <th style="width: 80px;">Selesai</th>
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
                <tr><td colspan="4" style="text-align: center; color: #777;">Belum ada data pengalaman</td></tr>
            @endforelse
        </tbody>
    </table>
    @endif

    {{-- TANDA TANGAN --}}
    <div style="margin-top: 40px; float: right; width: 220px; text-align: center; page-break-inside: avoid;">
        <p>Dicetak Tanggal: {{ date('d F Y') }}</p>
        
        @if($student->signature)
            <br>
            <img src="{{ asset('storage/'.$student->signature) }}" style="height: 60px; object-fit: contain;">
            <br>
        @else
            <br><br><br>
        @endif
        
        <p style="border-top: 1px solid #000; font-weight: bold; display: inline-block; min-width: 150px; text-transform: uppercase;">
            {{ $student->nama_lengkap }}
        </p>
    </div>

</body>
</html>
