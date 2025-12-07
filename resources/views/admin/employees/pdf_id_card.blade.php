<!DOCTYPE html>
<html>
<head>
    <title>Cetak Kartu Pegawai</title>
    <style>
        @page { margin: 10mm; }
        body { font-family: Arial, Helvetica, sans-serif; background-color: #fff; margin: 0; padding: 0; }
        
        .container { width: 100%; display: block; }
        
        .card-wrapper { 
            width: 54mm; 
            height: 86mm; 
            float: left; 
            margin-right: 5mm; 
            margin-bottom: 5mm; 
            position: relative; 
            page-break-inside: avoid; 
        }
        
        .card {
            width: 100%; 
            height: 100%; 
            border-radius: 8px; 
            overflow: hidden; 
            position: relative; 
            border: 1px solid #ddd;
            
            @if($profile && $profile->background_kartu)
                background-image: url('{{ public_path("storage/" . $profile->background_kartu) }}');
                background-size: cover; background-position: center; background-repeat: no-repeat;
            @else
                background-color: #1e3a8a; /* Warna Biru Tua */
            @endif
        }

        /* HEADER */
        .header-section {
            background-color: rgba(255, 255, 255, 0.95);
            height: 10mm;
            padding-top: 2mm;
            width: 100%;
            position: absolute;
            top: 0; left: 0;
            text-align: center;
            border-bottom: 2px solid #b48e24;
            z-index: 10;
        }

        .logo-img {
            height: 8mm;
            width: auto;
            max-width: 90%;
            object-fit: contain;
        }

        /* DEKORASI */
        @if(!$profile || !$profile->background_kartu)
            .cloud-1 { position: absolute; top: 18mm; left: -10mm; width: 30mm; height: 30mm; background: rgba(255,255,255,0.1); border-radius: 50%; z-index: 1; }
            .cloud-2 { position: absolute; top: 12mm; right: -5mm; width: 25mm; height: 25mm; background: rgba(255,255,255,0.1); border-radius: 50%; z-index: 1; }
            .cloud-3 { position: absolute; bottom: 15mm; left: -5mm; width: 40mm; height: 20mm; background: rgba(255,255,255,0.1); border-radius: 50px; transform: rotate(-10deg); z-index: 1; }
        @endif

        /* FOTO */
        .photo-wrapper { 
            position: absolute; 
            top: 14mm; 
            left: 0; 
            width: 100%; /* Pastikan wrapper foto full width */
            text-align: center; /* Center foto */
            z-index: 5; 
        }
        .photo-circle { 
            width: 28mm; 
            height: 28mm; 
            background: #fff; 
            border-radius: 50%; 
            border: 3px solid #fff; 
            margin: 0 auto; /* Center horizontal */
            overflow: hidden; 
            box-shadow: 0 4px 6px rgba(0,0,0,0.3); 
        }
        .photo-circle img { 
            width: 100%; height: 100%; object-fit: cover; 
        }

        /* INFO PEGAWAI (PERBAIKAN CSS DI SINI) */
        .info-section { 
            position: absolute; 
            top: 45mm; 
            left: 0;
            right: 0;
            width: 100%; 
            text-align: center; /* Kunci agar teks di tengah */
            color: white; 
            z-index: 5; 
            padding: 0; /* HAPUS PADDING SAMPING agar width 100% akurat */
            text-shadow: 1px 1px 3px rgba(0,0,0,0.9); 
        }
        
        .nama-pegawai { 
            font-weight: bold; 
            font-size: 10pt; 
            text-transform: uppercase; 
            margin-bottom: 4px; 
            line-height: 1.2; 
            display: block; 
            width: 90%; /* Batasi lebar teks nama */
            margin-left: auto; /* Center block element */
            margin-right: auto; /* Center block element */
            max-height: 35px; 
            overflow: hidden; 
        }
        
        .jabatan-wrapper {
            display: block;
            text-align: center;
            margin-bottom: 4px;
        }

        .jabatan-badge { 
            font-size: 7pt; 
            font-weight: bold; 
            letter-spacing: 0.5px; 
            background-color: #b48e24; 
            color: #fff;
            padding: 3px 8px; /* Padding badge sedikit diperbesar */
            border-radius: 4px; 
            display: inline-block; /* Agar mengikuti text-align center parent */
            text-transform: uppercase;
            max-width: 90%; /* Mencegah badge keluar kartu */
            white-space: nowrap;
            overflow: hidden;
            text-overflow: ellipsis;
        }
        
        .nomor-induk { 
            font-size: 8pt; 
            font-weight: normal;
            letter-spacing: 1px; 
            background-color: rgba(0,0,0,0.3); 
            padding: 2px 8px; 
            border-radius: 4px; 
            display: inline-block; /* Agar mengikuti text-align center parent */
        }

        /* QR CODE */
        .qr-section { 
            position: absolute; 
            bottom: 3mm; 
            width: 100%; 
            text-align: center; 
            z-index: 5; 
        }
        .qr-box { 
            background: white; 
            padding: 2mm; 
            display: inline-block; 
            border-radius: 4px; 
            box-shadow: 0 2px 4px rgba(0,0,0,0.2); 
        }
        .qr-img { width: 16mm; height: 16mm; display: block; }
    </style>
</head>
<body>

    <div class="container">
        @foreach($employees as $emp)
        <div class="card-wrapper">
            <div class="card">
                
                {{-- HEADER --}}
                <div class="header-section">
                    @if($profile && $profile->logo)
                        <img src="{{ public_path('storage/' . $profile->logo) }}" class="logo-img">
                    @else
                        <div style="display:inline-block; font-weight:bold; color:#b48e24; font-size:14pt; padding-top:2mm;">LPK</div>
                    @endif
                </div>

                {{-- BACKGROUND --}}
                @if(!$profile || !$profile->background_kartu)
                    <div class="cloud-1"></div> <div class="cloud-2"></div> <div class="cloud-3"></div>
                @endif

                {{-- FOTO --}}
                <div class="photo-wrapper">
                    <div class="photo-circle">
                        @if($emp->foto)
                            <img src="{{ public_path('storage/' . $emp->foto) }}">
                        @else
                            <div style="width:100%; height:100%; background:#ddd; display:flex; align-items:center; justify-content:center; color:#555; font-size:20px; font-weight:bold;">
                                {{ substr($emp->nama, 0, 1) }}
                            </div>
                        @endif
                    </div>
                </div>

                {{-- INFO PEGAWAI --}}
                <div class="info-section">
                    {{-- Nama --}}
                    <div class="nama-pegawai">{{ Str::limit($emp->nama, 25) }}</div>
                    
                    {{-- Jabatan (Dibungkus wrapper agar inline-block ter-center sempurna) --}}
                    <div class="jabatan-wrapper">
                        <div class="jabatan-badge">{{ $emp->jabatan }}</div>
                    </div>
                    
                    {{-- NIP --}}
                    <div class="nomor-induk">NIP: {{ $emp->nip ?? '-' }}</div>
                </div>

                {{-- QR CODE --}}
                <div class="qr-section">
                    <div class="qr-box">
                        <img class="qr-img" src="data:image/svg+xml;base64,{{ base64_encode(QrCode::format('svg')->size(100)->margin(0)->generate(route('pegawai.verification.public', $emp->id))) }}">
                    </div>
                </div>

            </div>
        </div>
        @endforeach
    </div>

</body>
</html>