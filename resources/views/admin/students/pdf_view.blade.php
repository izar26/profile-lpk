<!DOCTYPE html>
<html>
<head>
    <meta http-equiv="Content-Type" content="text/html; charset=utf-8"/>
    <title>Laporan Data Siswa</title>
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
            border-bottom: 3px double black; /* Garis ganda di bawah kop */
            padding-bottom: 10px;
            margin-bottom: 20px;
        }
        .header-table td {
            border: none; /* Hilangkan border kotak untuk kop */
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
            color: #D4AF37; /* Warna Gold (Opsional, ganti black jika mau hitam) */
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
        .badge {
            padding: 2px 5px;
            border-radius: 3px;
            font-size: 8pt;
            font-weight: bold;
        }
    </style>
</head>
<body>

    {{-- KOP SURAT --}}
    <table class="header-table">
        <tr>
            {{-- LOGO --}}
            <td class="logo-cell">
                @if(isset($profile) && $profile->logo)
                    {{-- Gunakan public_path agar DomPDF bisa baca gambar local --}}
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
        <h3 style="margin: 0; text-decoration: underline;">LAPORAN DATA SISWA</h3>
        <p style="margin: 5px 0 0 0; font-size: 9pt;">Dicetak pada: {{ now()->format('d F Y, H:i') }}</p>
    </div>
    
    {{-- TABEL DATA --}}
    <table class="table-data">
        <thead>
            <tr>
                <th style="width: 5%">No</th>
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
                <td class="text-center">
                    {{ $student->status }}
                </td>
                <td style="font-size: 9pt;">{{ $student->email }}</td>
                <td>{{ $student->no_hp_peserta ?? '-' }}</td>
            </tr>
            @endforeach
        </tbody>
    </table>

</body>
</html>