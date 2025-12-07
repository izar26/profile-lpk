<!DOCTYPE html>
<html>
<head>
    <title>Cetak Kartu Siswa</title>
    <style>
        /* ... CSS LAINNYA BIARKAN SAMA ... */

        /* PERBAIKAN DI SINI */
        .header-section {
            background-color: rgba(255, 255, 255, 0.95);
            
            /* LOGIKA BARU: PADDING MANUAL */
            height: 10mm;      /* Tinggi dikurangi (asalnya 12mm) */
            padding-top: 2mm;  /* Memberi jarak dari atas */
            
            width: 100%;
            position: absolute;
            top: 0;
            left: 0;
            
            text-align: center; /* Agar logo di tengah horizontal */
            border-bottom: 2px solid #b48e24;
            z-index: 10;
        }

        .logo-img {
            height: 8mm; /* Tinggi logo tetap */
            width: auto;
            max-width: 90%;
            object-fit: contain;
            /* Hapus vertical-align atau margin aneh-aneh, biarkan padding container yang mengatur */
        }

        /* ... CSS BAWAHNYA TETAP SAMA ... */
        @page { margin: 10mm; }
        body { font-family: Arial, Helvetica, sans-serif; background-color: #fff; margin: 0; padding: 0; }
        .container { width: 100%; display: flex; flex-wrap: wrap; gap: 15px; }
        .card-wrapper { width: 54mm; height: 86mm; display: inline-block; margin-right: 5mm; margin-bottom: 5mm; position: relative; }
        
        .card {
            width: 100%; height: 100%; border-radius: 8px; overflow: hidden; position: relative; border: 1px solid #ddd;
            @if($profile && $profile->background_kartu)
                background-image: url('{{ public_path("storage/" . $profile->background_kartu) }}');
                background-size: cover; background-position: center; background-repeat: no-repeat;
            @else
                background-color: #8B0000;
            @endif
        }

        /* DEKORASI AWAN */
        @if(!$profile || !$profile->background_kartu)
            .cloud-1 { position: absolute; top: 18mm; left: -10mm; width: 30mm; height: 30mm; background: rgba(255,255,255,0.1); border-radius: 50%; z-index: 1; }
            .cloud-2 { position: absolute; top: 12mm; right: -5mm; width: 25mm; height: 25mm; background: rgba(255,255,255,0.1); border-radius: 50%; z-index: 1; }
            .cloud-3 { position: absolute; bottom: 15mm; left: -5mm; width: 40mm; height: 20mm; background: rgba(255,255,255,0.1); border-radius: 50px; transform: rotate(-10deg); z-index: 1; }
        @endif

        /* FOTO UTAMA */
        .photo-wrapper { position: absolute; top: 14mm; left: 0; width: 100%; text-align: center; z-index: 5; }
        .photo-circle { width: 28mm; height: 28mm; background: #fff; border-radius: 50%; border: 3px solid #fff; margin: 0 auto; overflow: hidden; box-shadow: 0 4px 6px rgba(0,0,0,0.3); }
        .photo-circle img { width: 100%; height: 100%; object-fit: cover; }

        /* INFO SISWA */
        .info-section { position: absolute; top: 45mm; width: 100%; text-align: center; color: white; z-index: 5; padding: 0 2mm; text-shadow: 1px 1px 3px rgba(0,0,0,0.9); }
        .nama-siswa { font-weight: bold; font-size: 10pt; text-transform: uppercase; margin-bottom: 3px; line-height: 1.1; display: block; max-height: 22px; overflow: hidden; }
        .nomor-induk { font-size: 8pt; font-weight: bold; letter-spacing: 1px; background-color: rgba(0,0,0,0.2); padding: 2px 6px; border-radius: 4px; display: inline-block; }

        /* QR CODE */
        .qr-section { position: absolute; bottom: 3mm; width: 100%; text-align: center; z-index: 5; }
        .qr-box { background: white; padding: 2mm; display: inline-block; border-radius: 4px; box-shadow: 0 2px 4px rgba(0,0,0,0.2); }
        .qr-img { width: 16mm; height: 16mm; display: block; }
    </style>
</head>
<body>

    <div class="container">
        @foreach($students as $student)
        <div class="card-wrapper">
            <div class="card">
                
                <div class="header-section">
                    @if($profile && $profile->logo)
                        <img src="{{ public_path('storage/' . $profile->logo) }}" class="logo-img">
                    @else
                        <div style="display:inline-block; font-weight:bold; color:#b48e24; font-size:14pt;">LPK</div>
                    @endif
                </div>

                @if(!$profile || !$profile->background_kartu)
                    <div class="cloud-1"></div> <div class="cloud-2"></div> <div class="cloud-3"></div>
                @endif

                <div class="photo-wrapper">
                    <div class="photo-circle">
                        @if($student->foto)
                            <img src="{{ public_path('storage/' . $student->foto) }}">
                        @else
                            <div style="width:100%; height:100%; background:#ddd; display:flex; align-items:center; justify-content:center; color:#555; font-size:8px;">No Photo</div>
                        @endif
                    </div>
                </div>

                <div class="info-section">
                    <div class="nama-siswa">{{ Str::limit($student->nama_lengkap, 25) }}</div>
                    <div class="nomor-induk">ID: {{ $student->nomor_ktp }}</div>
                </div>

                <div class="qr-section">
                    <div class="qr-box">
                        <img class="qr-img" src="data:image/svg+xml;base64,{{ base64_encode(QrCode::format('svg')->size(100)->margin(0)->generate(route('student.verify', $student->id))) }}">
                    </div>
                </div>

            </div>
        </div>
        @endforeach
    </div>

</body>
</html>