<!DOCTYPE html>
<html>
<head>
    <title>Laporan Data Pegawai</title>
    <style>
        body {
            font-family: sans-serif;
            font-size: 10pt;
        }
        .header {
            text-align: center;
            margin-bottom: 20px;
            border-bottom: 2px solid #444;
            padding-bottom: 10px;
        }
        .header h2 {
            margin: 0;
            text-transform: uppercase;
        }
        .header p {
            margin: 5px 0 0;
            color: #555;
        }
        table {
            width: 100%;
            border-collapse: collapse;
            margin-top: 10px;
        }
        th, td {
            border: 1px solid #000;
            padding: 6px;
            text-align: left;
            vertical-align: top;
        }
        th {
            background-color: #f2f2f2;
            font-weight: bold;
        }
        .text-center {
            text-align: center;
        }
    </style>
</head>
<body>
    <div class="header">
        <h2>Laporan Data Pegawai</h2>
        <p>{{ config('app.name', 'LPK Profile') }}</p>
    </div>

    <table>
        <thead>
            <tr>
                <th style="width: 5%" class="text-center">No</th>
                <th style="width: 20%">Nama Lengkap</th>
                <th style="width: 15%">NIP</th>
                <th style="width: 15%">Jabatan</th>
                <th style="width: 10%">Status</th>
                <th style="width: 20%">Email</th>
                <th style="width: 15%">Telepon</th>
            </tr>
        </thead>
        <tbody>
            @foreach($employees as $index => $emp)
            <tr>
                <td class="text-center">{{ $index + 1 }}</td>
                <td>{{ $emp->nama }}</td>
                <td>{{ $emp->nip ?? '-' }}</td>
                <td>{{ $emp->jabatan }}</td>
                <td>{{ $emp->status_kepegawaian }}</td>
                <td>{{ $emp->email ?? '-' }}</td>
                <td>{{ $emp->telepon ?? '-' }}</td>
            </tr>
            @endforeach
        </tbody>
    </table>

    <div style="margin-top: 30px; text-align: right; font-size: 9pt; color: #555;">
        Dicetak pada: {{ now()->format('d F Y, H:i') }}
    </div>
</body>
</html>