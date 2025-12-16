<!DOCTYPE html>
<html>
<head>
    <meta http-equiv="Content-Type" content="text/html; charset=utf-8"/>
    <title>Laporan Data Pegawai</title>
    <style>
        body { 
            font-family: sans-serif; 
            font-size: 10pt; 
        }
        
        /* Style untuk Tabel Data Utama */
        .table-data { 
            width: 100%; 
            border-collapse: collapse; 
            margin-top: 20px;
        }
        .table-data th, .table-data td { 
            border: 1px solid black; 
            padding: 6px; 
            text-align: left; 
            vertical-align: top; 
        }
        .table-data th { 
            background-color: #f2f2f2; 
            font-weight: bold; 
            text-align: center;
        }
        
        /* Style untuk Kop Surat (Header) */
        .header-table {
            width: 100%;
            border-bottom: 3px double black;
            padding-bottom: 10px;
            margin-bottom: 20px;
        }
        .header-table td {
            border: none;
            vertical-align: middle;
        }
        .logo-cell {
            width: 15%;
            text-align: center;
        }
        .text-cell {
            width: 85%;
            text-align: center;
        }
        .lpk-name {
            font-size: 16pt;
            font-weight: bold;
            text-transform: uppercase;
            margin: 0;
            color: #D4AF37; /* Gold */
        }
        .lpk-address {
            font-size: 10pt;
            margin: 2px 0;
        }
        .lpk-contact {
            font-size: 9pt;
            font-style: italic;
        }

        .text-center { text-align: center; }
    </style>
</head>
<body>

    {{-- KOP SURAT --}}
    <table class="header-table">
        <tr>
            {{-- LOGO --}}
            <td class="logo-cell">
                @if(isset($profile) && $profile->logo)
                    <img src="{{ public_path('storage/' . $profile->logo) }}" width="70" height="auto">
                @endif
            </td>
            
            {{-- INFORMASI LPK --}}
            <td class="text-cell">
                <h1 class="lpk-name">{{ $profile->nama_lpk ?? config('app.name') }}</h1>
                @if(isset($profile))
                    <p class="lpk-address">{{ $profile->alamat }}</p>
                    <p class="lpk-contact">
                        @if($profile->nomor_wa) Telp/WA: {{ $profile->nomor_wa }} @endif
                        @if($profile->email) | Email: {{ $profile->email }} @endif
                    </p>
                @endif
            </td>
        </tr>
    </table>

    {{-- JUDUL LAPORAN --}}
    <div style="text-align: center; margin-bottom: 15px;">
        <h3 style="margin: 0; text-decoration: underline;">LAPORAN DATA PEGAWAI</h3>
        <p style="margin: 5px 0 0 0; font-size: 9pt;">Dicetak pada: {{ now()->format('d F Y, H:i') }}</p>
    </div>

    {{-- TABEL DATA --}}
    <table class="table-data">
        <thead>
            <tr>
                <th style="width: 5%">No</th>
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
                <td class="text-center">{{ $emp->status_kepegawaian }}</td>
                <td style="font-size: 9pt;">{{ $emp->email ?? '-' }}</td>
                <td>{{ $emp->telepon ?? '-' }}</td>
            </tr>
            @endforeach
        </tbody>
    </table>

</body>
</html>