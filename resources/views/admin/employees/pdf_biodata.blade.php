<!DOCTYPE html>
<html>
<head>
    <title>Biodata Pegawai - {{ $employee->nama }}</title>
    <style>
        body { font-family: sans-serif; font-size: 10pt; line-height: 1.4; }
        .header { text-align: center; border-bottom: 2px solid #000; padding-bottom: 10px; margin-bottom: 20px; }
        .title { font-size: 14pt; font-weight: bold; text-transform: uppercase; }
        .subtitle { font-size: 9pt; color: #555; }

        /* Layout Foto */
        .photo-container {
            float: right; width: 3cm; height: 4cm;
            border: 1px solid #ccc; margin-left: 15px; margin-bottom: 10px;
            overflow: hidden;
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
            font-weight: bold; font-size: 11pt; background-color: #eee;
            padding: 5px; margin-top: 15px; margin-bottom: 8px;
            border-bottom: 1px solid #999;
        }

        /* Tabel Data (Pendidikan/Keluarga) */
        table { width: 100%; border-collapse: collapse; margin-bottom: 10px; font-size: 9pt; }
        table th, table td { border: 1px solid #999; padding: 4px 6px; text-align: left; }
        table th { background-color: #f5f5f5; text-align: center; }
    </style>
</head>
<body>

    <div class="header">
        <div class="title">Biodata Pegawai</div>
        <div class="subtitle">{{ config('app.name', 'LPK Profile') }}</div>
    </div>

    {{-- FOTO --}}
    <div class="photo-container">
        @if($employee->foto)
            {{-- Menggunakan public_path agar terbaca oleh DOMPDF --}}
            <img src="{{ public_path('storage/' . $employee->foto) }}">
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
    <div style="margin-top: 40px; float: right; width: 220px; text-align: center;">
        <p>Dicetak Tanggal: {{ date('d F Y') }}</p>
        <br><br><br>
        <p style="border-top: 1px solid #000; font-weight: bold; display: inline-block; min-width: 150px;">
            {{ $employee->nama }}
        </p>
    </div>

</body>
</html>
