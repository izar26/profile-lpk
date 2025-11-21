<!DOCTYPE html>
<html>
<head>
    <style>
        body { font-family: sans-serif; }
        .header { text-align: center; border-bottom: 2px solid #333; padding-bottom: 10px; margin-bottom: 20px; }
        .photo { float: right; width: 120px; height: 150px; border: 1px solid #ddd; object-fit: cover; }
        .label { font-weight: bold; width: 150px; display: inline-block; }
        .row { margin-bottom: 10px; }
    </style>
</head>
<body>
    <div class="header">
        <h2>BIODATA SISWA</h2>
        <p>{{ config('app.name') }}</p>
    </div>

    @if($student->foto)
        <img src="{{ public_path('storage/'.$student->foto) }}" class="photo">
    @endif

    <div class="content">
        <div class="row"><span class="label">Nama Lengkap:</span> {{ $student->nama }}</div>
        <div class="row"><span class="label">NIK:</span> {{ $student->NIK ?? '-' }}</div>
        <div class="row"><span class="label">Program:</span> {{ $student->program->judul ?? '-' }}</div>
        <div class="row"><span class="label">Status:</span> {{ $student->status }}</div>
        <br>
        <div class="row"><span class="label">Tempat, Tgl Lahir:</span> {{ $student->tempat_lahir }}, {{ $student->tanggal_lahir ? $student->tanggal_lahir->format('d-m-Y') : '-' }}</div>
        <div class="row"><span class="label">Jenis Kelamin:</span> {{ $student->jenis_kelamin == 'L' ? 'Laki-laki' : 'Perempuan' }}</div>
        <div class="row"><span class="label">Alamat:</span> {{ $student->alamat }}</div>
        <br>
        <div class="row"><span class="label">Email:</span> {{ $student->email }}</div>
        <div class="row"><span class="label">Telepon:</span> {{ $student->telepon }}</div>
    </div>
</body>
</html>