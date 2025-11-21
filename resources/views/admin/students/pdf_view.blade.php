<!DOCTYPE html>
<html>
<head>
    <style>
        body { font-family: sans-serif; font-size: 10pt; }
        table { width: 100%; border-collapse: collapse; }
        th, td { border: 1px solid black; padding: 5px; text-align: left; }
        th { background-color: #f2f2f2; }
        .header { text-align: center; margin-bottom: 20px; }
    </style>
</head>
<body>
    <div class="header">
        <h2>Laporan Data Siswa</h2>
        <p>{{ config('app.name') }}</p>
    </div>
    <table>
        <thead>
            <tr>
                <th>Nama</th>
                <th>NIK</th>
                <th>Program</th>
                <th>Status</th>
                <th>Email</th>
                <th>Telepon</th>
            </tr>
        </thead>
        <tbody>
            @foreach($students as $student)
            <tr>
                <td>{{ $student->nama }}</td>
                <td>{{ $student->NIK }}</td>
                <td>{{ $student->program->judul ?? '-' }}</td>
                <td>{{ $student->status }}</td>
                <td>{{ $student->email }}</td>
                <td>{{ $student->telepon }}</td>
            </tr>
            @endforeach
        </tbody>
    </table>
</body>
</html>