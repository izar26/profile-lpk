<!DOCTYPE html>
<html lang="id">
<head>
    <meta charset="UTF-8">
    <title>Bukti Kelulusan Administrasi - {{ $student->nama_lengkap }}</title>
    <style>
        body {
            font-family: Arial, sans-serif;
            font-size: 12px;
            line-height: 1.5;
            color: #333;
        }
        
        /* --- KOP SURAT (HEADER) --- */
        .header-table {
            width: 100%;
            border-bottom: 3px double #000; /* Garis ganda di bawah kop */
            padding-bottom: 10px;
            margin-bottom: 20px;
        }
        .logo-lpk {
            width: 80px;
            height: auto;
        }
        .header-text {
            text-align: center;
        }
        .nama-lpk {
            font-size: 18px;
            font-weight: bold;
            text-transform: uppercase;
            margin: 0;
            color: #000;
        }
        .sk-lpk {
            font-size: 11px;
            font-weight: bold;
            margin: 2px 0;
        }
        .alamat-lpk {
            font-size: 10px;
            margin: 0;
        }

        /* --- JUDUL DOKUMEN --- */
        .document-title {
            text-align: center;
            font-size: 16px;
            font-weight: bold;
            text-decoration: underline;
            margin-bottom: 5px;
            text-transform: uppercase;
        }
        .document-subtitle {
            text-align: center;
            font-size: 12px;
            margin-bottom: 25px;
        }

        /* --- FOTO & DATA --- */
        .content-table {
            width: 100%;
            margin-bottom: 10px;
        }
        .photo-container {
            width: 130px;
            text-align: center;
            vertical-align: top;
            padding-right: 15px;
        }
        .photo-img {
            width: 3cm; /* Ukuran pas foto standar 3x4 */
            height: 4cm;
            object-fit: cover;
            border: 1px solid #000;
            background-color: #eee;
        }
        .data-label {
            width: 140px;
            font-weight: bold;
            vertical-align: top;
        }
        .data-separator {
            width: 10px;
            vertical-align: top;
        }
        .data-value {
            vertical-align: top;
        }

        /* --- BAGIAN TANDA TANGAN --- */
        .signature-section {
            margin-top: 50px;
            width: 100%;
        }
        .signature-box {
            float: right;
            width: 250px;
            text-align: center;
        }
        .tgl-surat {
            margin-bottom: 10px;
        }
        .jabatan {
            font-weight: bold;
            margin-bottom: 60px; /* Ruang untuk tanda tangan */
        }
        .nama-pimpinan {
            font-weight: bold;
            text-decoration: underline;
        }
        
        .status-box {
            font-weight: bold;
            padding: 5px 10px;
            border: 1px solid #000;
            display: inline-block;
            text-transform: uppercase;
        }
    </style>
</head>
<body>

    {{-- 1. KOP SURAT (Menggunakan Data LpkProfile jika ada, fallback ke teks statis) --}}
    <table class="header-table">
        <tr>
            <td width="15%" style="vertical-align: middle; text-align: center;">
                {{-- Gunakan public_path agar terbaca oleh DomPDF --}}
                @if(isset($profile) && $profile->logo)
                    <img src="{{ public_path('storage/' . $profile->logo) }}" class="logo-lpk">
                @else
                    {{-- Placeholder --}}
                    <h3>LOGO</h3>
                @endif
            </td>
            <td width="85%" class="header-text">
                <h1 class="nama-lpk">{{ $profile->nama_lpk ?? 'LPK HACHIMITSU' }}</h1>
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

    {{-- 2. JUDUL DOKUMEN --}}
    <div class="document-title">BUKTI KELULUSAN ADMINISTRASI</div>
    <div class="document-subtitle">Nomor Peserta: {{ sprintf('%04d', $student->id) }}/{{ date('Y') }}/LPK-HCM</div>

    {{-- 3. ISI DATA PESERTA --}}
    <table class="content-table">
        <tr>
            {{-- KOLOM FOTO (KIRI) --}}
            <td class="photo-container">
                @if($student->foto)
                    <img src="{{ public_path('storage/' . $student->foto) }}" class="photo-img">
                @else
                    <div class="photo-img" style="display:flex; align-items:center; justify-content:center; border: 1px dashed #999;">
                        No Photo
                    </div>
                @endif
                <br>
                <div style="margin-top: 5px; font-size: 10px; font-weight: bold;">FOTO PESERTA</div>
            </td>

            {{-- KOLOM DATA (KANAN) --}}
            <td>
                <table style="width: 100%;">
                    <tr>
                        <td class="data-label">Nama Lengkap</td>
                        <td class="data-separator">:</td>
                        <td class="data-value" style="text-transform: uppercase; font-weight: bold;">{{ $student->nama_lengkap }}</td>
                    </tr>
                    <tr>
                        <td class="data-label">Nomor KTP</td>
                        <td class="data-separator">:</td>
                        <td class="data-value">{{ $student->nomor_ktp ?? '-' }}</td>
                    </tr>
                    <tr>
                        <td class="data-label">Tempat, Tgl Lahir</td>
                        <td class="data-separator">:</td>
                        <td class="data-value">
                            {{ $student->tempat_lahir ?? '-' }}, 
                            {{ $student->tanggal_lahir ? \Carbon\Carbon::parse($student->tanggal_lahir)->isoFormat('D MMMM Y') : '-' }}
                        </td>
                    </tr>
                    <tr>
                        <td class="data-label">Jenis Kelamin</td>
                        <td class="data-separator">:</td>
                        <td class="data-value">{{ $student->jenis_kelamin }}</td>
                    </tr>
                    <tr>
                        <td class="data-label">Alamat Domisili</td>
                        <td class="data-separator">:</td>
                        <td class="data-value">{{ $student->alamat_domisili ?? '-' }}</td>
                    </tr>
                    <tr>
                        <td class="data-label">No. Handphone</td>
                        <td class="data-separator">:</td>
                        <td class="data-value">{{ $student->no_hp_peserta ?? '-' }}</td>
                    </tr>
                    <tr><td colspan="3" style="height: 10px;"></td></tr>
                    <tr>
                        <td class="data-label">Program Pilihan</td>
                        <td class="data-separator">:</td>
                        <td class="data-value" style="font-weight: bold; color: #000;">
                            {{ $student->program->judul ?? '-' }}
                        </td>
                    </tr>
                    <tr>
                        <td class="data-label">Status Seleksi</td>
                        <td class="data-separator">:</td>
                        <td class="data-value">
                            <span class="status-box">LOLOS VERIFIKASI ADMIN</span>
                        </td>
                    </tr>
                </table>
            </td>
        </tr>
    </table>

    <div style="margin-top: 20px; border: 1px dashed #000; padding: 10px; font-size: 11px;">
        <strong>Catatan untuk Peserta:</strong>
        <ol style="margin-top: 5px; margin-bottom: 0; padding-left: 20px;">
            <li>Simpan kartu bukti ini sebagai syarat mengikuti tahapan selanjutnya (Wawancara).</li>
            <li>Tunjukkan kartu ini kepada petugas saat jadwal wawancara berlangsung.</li>
            <li>Pastikan membawa dokumen asli (KTP, Ijazah, KK) saat wawancara untuk validasi fisik.</li>
        </ol>
    </div>

    {{-- 4. TANDA TANGAN PIMPINAN --}}
    <div class="signature-section">
        <div class="signature-box">
            {{-- Mengambil Kota dari Profile LPK, atau Default --}}
            @php
                $kota = 'Indonesia';
                if(isset($profile->alamat)) {
                    $parts = explode(',', $profile->alamat);
                    // Ambil bagian terakhir alamat sebagai Kota (Simple Logic)
                    $kota = trim(end($parts)); 
                }
            @endphp

            <div class="tgl-surat">
                {{ $kota }}, {{ \Carbon\Carbon::now()->isoFormat('D MMMM Y') }}
            </div>
            <div class="jabatan">Pimpinan LPK,</div>
            
            {{-- Nama Pimpinan --}}
            <div class="nama-pimpinan">
                {{ $profile->nama_pimpinan ?? '(Nama Pimpinan)' }}
            </div>
            
            <div>Pimpinan / Direktur</div>
        </div>
        <div style="clear: both;"></div>
    </div>

</body>
</html>