<!DOCTYPE html>
<html>
<head>
    <title>Biodata Pegawai - {{ $employee->nama }}</title>
    <style>
        body {
            font-family: sans-serif;
            font-size: 11pt;
            line-height: 1.5;
        }
        .header {
            text-align: center;
            border-bottom: 3px double #000;
            padding-bottom: 10px;
            margin-bottom: 30px;
        }
        .title {
            font-size: 16pt;
            font-weight: bold;
            text-transform: uppercase;
        }
        .subtitle {
            font-size: 10pt;
            color: #555;
        }
        
        /* Layout Foto */
        .photo-container {
            float: right;
            width: 113px; /* setara 3cm */
            height: 151px; /* setara 4cm */
            border: 1px solid #ccc;
            margin-left: 20px;
            margin-bottom: 20px;
            overflow: hidden;
        }
        .photo-container img {
            width: 100%;
            height: 100%;
            object-fit: cover;
        }

        /* Layout Baris Data */
        .row {
            margin-bottom: 8px;
            clear: both;
        }
        .label {
            float: left;
            width: 160px;
            font-weight: bold;
        }
        .colon {
            float: left;
            width: 20px;
        }
        .value {
            float: left;
            width: 350px;
        }
        .clear {
            clear: both;
        }
        
        /* Judul Section */
        .section-title {
            font-weight: bold;
            font-size: 12pt;
            background-color: #e0e0e0;
            padding: 5px 10px;
            margin-top: 20px;
            margin-bottom: 10px;
            border-left: 5px solid #555;
        }
    </style>
</head>
<body>

    <div class="header">
        <div class="title">Biodata Pegawai</div>
        <div class="subtitle">{{ config('app.name', 'LPK Profile') }}</div>
    </div>

    <div class="photo-container">
        @if($employee->foto)
            <img src="{{ public_path('storage/' . $employee->foto) }}" alt="Foto Pegawai">
        @else
            <div style="text-align: center; padding-top: 60px; color: #aaa; font-size: 10pt;">
                Tidak Ada Foto
            </div>
        @endif
    </div>

    <div class="row">
        <div class="label">Nama Lengkap</div>
        <div class="colon">:</div>
        <div class="value">{{ $employee->nama }}</div>
    </div>
    <div class="row">
        <div class="label">NIP</div>
        <div class="colon">:</div>
        <div class="value">{{ $employee->nip ?? '-' }}</div>
    </div>
    <div class="row">
        <div class="label">Jabatan</div>
        <div class="colon">:</div>
        <div class="value">{{ $employee->jabatan }}</div>
    </div>
    <div class="row">
        <div class="label">Status</div>
        <div class="colon">:</div>
        <div class="value">{{ $employee->status_kepegawaian }}</div>
    </div>

    <div class="clear"></div>

    <div class="section-title">DATA PRIBADI</div>
    
    <div class="row">
        <div class="label">Tempat, Tgl Lahir</div>
        <div class="colon">:</div>
        <div class="value">
            {{ $employee->tempat_lahir ? $employee->tempat_lahir . ', ' : '' }}
            {{ $employee->tanggal_lahir ? $employee->tanggal_lahir->format('d F Y') : '-' }}
        </div>
    </div>
    <div class="row">
        <div class="label">Jenis Kelamin</div>
        <div class="colon">:</div>
        <div class="value">
            {{ $employee->jenis_kelamin == 'L' ? 'Laki-laki' : ($employee->jenis_kelamin == 'P' ? 'Perempuan' : '-') }}
        </div>
    </div>
    <div class="row">
        <div class="label">Agama</div>
        <div class="colon">:</div>
        <div class="value">{{ $employee->agama ?? '-' }}</div>
    </div>
    <div class="row">
        <div class="label">Pendidikan Terakhir</div>
        <div class="colon">:</div>
        <div class="value">{{ $employee->pendidikan_terakhir ?? '-' }}</div>
    </div>

    <div class="section-title">KONTAK & ALAMAT</div>

    <div class="row">
        <div class="label">Alamat Lengkap</div>
        <div class="colon">:</div>
        <div class="value">{{ $employee->alamat ?? '-' }}</div>
    </div>
    <div class="row">
        <div class="label">Kota / Provinsi</div>
        <div class="colon">:</div>
        <div class="value">
            {{ $employee->kota ?? '-' }} / {{ $employee->provinsi ?? '-' }}
            @if($employee->kode_pos) ({{ $employee->kode_pos }}) @endif
        </div>
    </div>
    <div class="row">
        <div class="label">No. Telepon / WA</div>
        <div class="colon">:</div>
        <div class="value">{{ $employee->telepon ?? '-' }}</div>
    </div>
    <div class="row">
        <div class="label">Email</div>
        <div class="colon">:</div>
        <div class="value">{{ $employee->email ?? '-' }}</div>
    </div>

    <div style="margin-top: 50px; float: right; width: 200px; text-align: center;">
        <p>Dicetak Tanggal: {{ date('d/m/Y') }}</p>
        <br><br><br><br>
        <p style="border-top: 1px solid #000; display: inline-block; padding-top: 5px; min-width: 150px;">
            ( Admin / HRD )
        </p>
    </div>

</body>
</html>