<!DOCTYPE html>
<html>
<head>
    <style>
        body { font-family: sans-serif; font-size: 10pt; }
        table { width: 100%; border-collapse: collapse; }
        th, td { border: 1px solid black; padding: 5px; text-align: left; vertical-align: top; }
        th { background-color: #f2f2f2; font-weight: bold; }
        .header { text-align: center; margin-bottom: 20px; }
        .text-center { text-align: center; }
        .small { font-size: 8pt; color: #555; }
    </style>
</head>
<body>
    <div class="header">
        <h2>Laporan Data Siswa</h2>
        <p style="margin: 0; font-size: 12pt; font-weight: bold;">{{ config('app.name') }}</p>
        <p style="margin: 5px 0 0 0; font-size: 9pt;">Dicetak pada: {{ now()->format('d/m/Y H:i') }}</p>
    </div>
    
    <table>
        <thead>
            <tr>
                <th style="width: 5%" class="text-center">No</th>
                <th style="width: 20%">Nama Lengkap</th>
                <th style="width: 15%">No. KTP</th>
                <th style="width: 15%">Program</th>
                <th style="width: 15%">Status</th>
                <th style="width: 15%">Email</th>
                <th style="width: 15%">No. HP</th>
            </tr>
        </thead>
        <tbody>
            @foreach($students as $index => $student)
            <tr>
                <td class="text-center">{{ $index + 1 }}</td>
                <td>{{ $student->nama_lengkap }}</td>
                <td>{{ $student->nomor_ktp ?? '-' }}</td>
                <td>{{ $student->program->judul ?? '-' }}</td>
                <td>{{ $student->status }}</td>
                <td>{{ $student->email }}</td>
                <td>{{ $student->no_hp_peserta ?? '-' }}</td>
            </tr>
            @endforeach
        </tbody>
    </table>
</body>
</html>