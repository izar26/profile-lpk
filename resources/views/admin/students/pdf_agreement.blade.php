<!DOCTYPE html>
<html lang="id">
<head>
    <meta charset="UTF-8">
    <title>Surat Perjanjian Pelatihan</title>
    <style>
        /* Mengatur Margin Halaman PDF secara global */
        @page {
            /* Margin standar kiri/kanan/bawah */
            margin: 2cm 2cm 2cm 2cm; 
        }

        body {
            font-family: "Times New Roman", Times, serif;
            font-size: 11pt; /* Sedikit dikecilkan agar muat */
            line-height: 1.3;
            color: #000;
        }

        /* KUNCI PERUBAHAN:
           Jarak untuk Kop Surat Fisik. 
           Jika kop suratmu tingginya 4cm, ubah height: 4cm.
           Ini hanya akan mendorong konten di halaman pertama.
        */
        .header-space {
            width: 100%;
            height: 4.5cm; /* Sesuaikan angka ini dengan tinggi Kop Surat kertasmu */
            display: block;
        }

        .text-center { text-align: center; }
        .text-right { text-align: right; }
        .text-justify { text-align: justify; }
        .text-bold { font-weight: bold; }
        .uppercase { text-transform: uppercase; }
        .mb-1 { margin-bottom: 5px; }
        .mt-2 { margin-top: 10px; }
        
        /* Layout Tabel Informasi */
        .info-table {
            width: 100%;
            border-collapse: collapse;
            margin-bottom: 5px;
        }
        .info-table td {
            vertical-align: top;
            padding: 1px 0;
        }
        .label-col { width: 140px; }
        .sep-col { width: 10px; }

        /* Judul Pasal */
        .pasal-title {
            text-align: center;
            font-weight: bold;
            margin-top: 10px;
            text-decoration: underline;
        }
        .pasal-sub {
            text-align: center;
            font-weight: bold;
            margin-bottom: 5px;
        }

        /* List numbering */
        ol { margin: 0; padding-left: 20px; }
        li { margin-bottom: 2px; text-align: justify; }
        ul { margin: 0; padding-left: 15px; }

        /* Tabel Biaya */
        .biaya-table {
            width: 100%;
            border-collapse: collapse;
            margin: 5px 0;
            font-size: 10pt;
        }
        .biaya-table th, .biaya-table td {
            border: 1px solid black;
            padding: 4px;
        }
        .biaya-table th { background-color: #f0f0f0; }
        
        /* Tanda Tangan */
        .ttd-table {
            width: 100%;
            margin-top: 20px;
            page-break-inside: avoid; /* Mencegah TTD terpotong halaman */
        }
        .ttd-table td {
            text-align: center;
            vertical-align: top;
        }
        .ttd-space { height: 70px; }
    </style>
</head>
<body>

    <div class="header-space"></div>

    <div class="text-center text-bold mb-1">
        SURAT PERJANJIAN PELATIHAN BAHASA JEPANG ANTARA<br>
        {{ strtoupper($profile->nama_lpk ?? 'LPK ANDA') }}<br>
        DENGAN<br>
        PESERTA PELATIHAN
    </div>

    <p style="margin: 5px 0;">Bertanda Tangan di Bawah ini Masing-masing :</p>

    <table class="info-table">
        <tr>
            <td class="label-col">Nama Lembaga</td>
            <td class="sep-col">:</td>
            <td><b>{{ strtoupper($profile->nama_lpk ?? 'NAMA LEMBAGA') }}</b></td>
        </tr>
        <tr>
            <td class="label-col">Pimpinan</td>
            <td class="sep-col">:</td>
            <td>{{ $profile->nama_pimpinan ?? '-' }}</td>
        </tr>
        <tr>
            <td class="label-col">Alamat</td>
            <td class="sep-col">:</td>
            <td>{{ $profile->alamat ?? '-' }}</td>
        </tr>
        <tr>
            <td class="label-col">No. Tlp</td>
            <td class="sep-col">:</td>
            <td>{{ $profile->telepon_lpk ?? $profile->nomor_wa ?? '-' }}</td>
        </tr>
    </table>
    <div class="text-justify" style="margin-bottom: 10px;">
        Dalam hal ini disebutkan dan bertindak atas nama atau sebagai <b>{{ strtoupper($profile->nama_lpk) }}</b> selanjutnya disebut <b>PIHAK PERTAMA</b>.
    </div>

    <table class="info-table">
        <tr>
            <td class="label-col">Nama</td>
            <td class="sep-col">:</td>
            <td><b>{{ strtoupper($student->nama_lengkap) }}</b></td>
        </tr>
        <tr>
            <td class="label-col">TTL</td>
            <td class="sep-col">:</td>
            <td>{{ $student->tempat_lahir ?? '...' }}, {{ $student->tanggal_lahir ? \Carbon\Carbon::parse($student->tanggal_lahir)->translatedFormat('d F Y') : '...' }}</td>
        </tr>
        <tr>
            <td class="label-col">No. KTP</td>
            <td class="sep-col">:</td>
            <td>{{ $student->nomor_ktp ?? '-' }}</td>
        </tr>
        <tr>
            <td class="label-col">Alamat</td>
            <td class="sep-col">:</td>
            <td>{{ $student->alamat_ktp ?? $student->alamat_domisili ?? '-' }}</td>
        </tr>
    </table>
    <div class="text-justify">
        Dalam hal ini disebutkan dan bertindak atas nama atau sebagai <b>PESERTA PELATIHAN</b> selanjutnya disebut sebagai <b>PIHAK KEDUA</b>.
    </div>

    <div class="text-justify mt-2">
        Kedua pihak dengan penuh itikad baik telah sepakat untuk mengadakan perjanjian bersama dalam rangka program Pelatihan Bahasa dan Budaya Jepang.
        Untuk mencapai maksud dan tujuan tersebut maka kedua belah pihak sepakat untuk menjabarkan hak dan kewajiban masing – masing sebagai berikut :
    </div>

    <div class="pasal-title">Pasal 1</div>
    <div class="pasal-sub">TUJUAN</div>
    <p class="text-justify" style="margin:0;">
        Dalam rangka memenuhi kebutuhan pekerja di Negara Jepang PIHAK PERTAMA akan memberikan pelatihan bahasa dan budaya Jepang kepada PIHAK KEDUA agar dapat memenuhi standar minimal bahasa Jepang serta budaya yang dapat di terima oleh perusahaan penerima di Negara Jepang.
    </p>

    <div class="pasal-title">Pasal 2</div>
    <p class="text-justify" style="margin:0;">
        <b>{{ strtoupper($profile->nama_lpk) }}</b> adalah Lembaga Penyelenggara Program pelatihan Bahasa dan budaya yang telah mendapatkan izin atau telah terdaftar pada OSS Online Single Submission Risk Based Approach (OSS RBA) di bidang ketenagakerjaan dengan kode KBLI 78425 mengacu pada kegiatan pelatihan bahasa asing untuk melaksanakan program pelatihan kerja.
    </p>

    <div class="pasal-title">Pasal 3</div>
    <div class="pasal-sub">SYARAT PESERTA PELATIHAN</div>
    <ol>
        <li>PIHAK KEDUA wajib mengikuti tahapan proses, mulai dari tahap penyeleksian, pembelajaran, serta konsultasi dalam penentuan program yang akan di tempuh.</li>
        <li>Proses Penyeleksian meliputi: Sehat Jasmani dan Rohani, Test tertulis, Kepribadian dan evaluasi karakter, Tidak mempunyai catatan kriminal, Lengkap berkas dan administrasi .</li>
        <li>Peserta yang dinyatakan lulus proses penyeleksian, wajib mengikuti tahapan pelatihan sampai peserta dapat lulus sertifikasi Bahasa Level 4 (N4) atau JFT Basic A2.</li>
        <li>Kegiatan ini meliputi pelatihan bahasa Jepang, orientasi lintas budaya, system kerja di Jepang, orientasi kepribadian dan evaluasi karakter serta kedisiplinan.</li>
        <li>Para peserta akan dilatih oleh instruktur professional yang berpengalaman, selama proses pelatihan peserta pelatihan wajib mengikuti peraturan dan tata tertib yang berlaku.</li>
    </ol>

    <div class="pasal-title">Pasal 4</div>
    <div class="pasal-sub">JAM BELAJAR</div>
    <p class="text-justify" style="margin:0;">
        PIHAK KEDUA wajib mengikuti Pendidikan dan pelatihan yang diselangarakan oleh PIHAK PERTAMA dari hari Senin-Sabtu dengan jadwal pembelajaran sebagai berikut:
        Senin-Jum’at : 08.00 - 16.00 WIB atau di jam yang telah disesuaikan oleh pihak Lembaga.
    </p>

    <div class="pasal-title">Pasal 5</div>
    <div class="pasal-sub">PERATURAN</div>
    <p style="margin:0;">PIHAK KEDUA wajib mematuhi peraturan yang berlaku di LPK, adapun peraturan tersebut sebagai berikut:</p>
    <ol type="a">
        <li>Dilarang merokok diluar area yang telah di tentukan</li>
        <li>Wajib mengikuti senam pagi</li>
        <li>Semua peserta wajib hadir di Tempat pelatihan 15 menit sebelum pelatihan di mulai</li>
        <li>Menjaga Norma – Norma yang berlaku dilingkungan pembelajaran</li>
        <li>Mengikuti semua aturan yang berlaku di lingkungan tempat pelatihan</li>
        <li>Mengikuti arahan dan bimbingan dari PIHAK PERTAMA.</li>
    </ol>

    <div class="pasal-title">Pasal 6</div>
    <div class="pasal-sub">HAK DAN KEWAJIBAN PIHAK KEDUA</div>
    <p style="margin:0;"><b>Ayat 1. PIHAK KEDUA berhak untuk :</b></p>
    <ol>
        <li>Mendapatkan pembelajaran budaya dan bahasa Jepang</li>
        <li>Mendapatkan Seragam, buku/Modul Pelajaran</li>
        <li>Mendapatkan kesempatan informasi serta wawancara dengan pihak penerima di Jepang</li>
        <li>Mendapatkan sertifikat pelatihan apabila telah menyelesaikan program pelatihan.</li>
    </ol>
    <p style="margin:0;"><b>Ayat 2. Peserta Pelatihan memiliki kewajiban sebagai berikut :</b></p>
    <ol>
        <li>Melakukan pendaftaran dan menyelesaikan pembiayaan Pendidikan;</li>
        <li>Mentaati peraturan yang berlaku di LPK[cite: 58];</li>
        <li>Menyelesaikan pelatihan Bahasa Jepang[cite: 59].</li>
    </ol>

    <div class="pasal-title">Pasal 7</div>
    <div class="pasal-sub">HAK DAN KEWAJIBAN PIHAK PERTAMA</div>
    <p style="margin:0;"><b>Ayat 1. Pihak Pertama berhak untuk :</b></p>
    <ol>
        <li>Menarik biaya Pendidikan / jasa peserta pelatihan[cite: 63];</li>
        <li>Mengevaluasi peserta pelatihan[cite: 64];</li>
        <li>Memberhentikan peserta pelatihan yang melanggar peraturan/perjanjian[cite: 65].</li>
    </ol>
    <p style="margin:0;"><b>Ayat 2. Lembaga Pelatihan kerja (LPK) berkewajiban untuk :</b></p>
    <ol>
        <li>Memberikan fasilitas pelatihan bahasa dan budaya jepang[cite: 67];</li>
        <li>Menyediakan instruktur dan tenaga kepelatihan[cite: 68];</li>
        <li>Mengatur dan menjadwalkan Medical Check up[cite: 69];</li>
        <li>Memfasilitasi interview dengan perusahaan penerima kerja di Jepang[cite: 70];</li>
        <li>Memberikan sertifikat kepada peserta yang telah menyelesaikan program pelatihan[cite: 71].</li>
    </ol>

    <div style="page-break-inside: avoid;">
        <div class="pasal-title">Pasal 8</div>
        <div class="pasal-sub">PEMBIAYAAN</div>
        <p style="margin:0;">Biaya pelatihan yang menjadi tanggungan peserta pelatihan sekaligus Fasilitas yang diberikan PIHAK PERTAMA kepada PIHAK KEDUA sebagai berikut :</p>
        
        <div class="text-center text-bold" style="margin-top:5px;">RINCIAN BIAYA</div>
        <table class="biaya-table">
            <thead>
                <tr>
                    <th width="5%">No</th>
                    <th>ITEM PEMBIAYAAN</th>
                    <th width="25%">BIAYA</th>
                </tr>
            </thead>
            <tbody>
                <tr><td class="text-center">1</td><td>Biaya Pendaftaran</td><td class="text-right">Rp. 100.000,-</td></tr>
                <tr><td class="text-center">2</td><td>Modul dan Bahan ajar Pelatihan</td><td class="text-right">Rp. 2.000.000,-</td></tr>
                <tr><td class="text-center">3</td><td>Seragam</td><td class="text-right">Rp. 400.000,-</td></tr>
                <tr><td class="text-center">4</td><td>Fasilitas pembelajaran dan Pendampingan selama 6 Bulan / Sampai Lulus N4</td><td class="text-right">Rp. 5.500.000,-</td></tr>
                <tr><td class="text-center">5</td><td>Modul Pembelajaran Keahlian khusus</td><td class="text-right">Rp. 1.000.000,-</td></tr>
                <tr><td colspan="2" class="text-right text-bold">TOTAL</td><td class="text-right text-bold">Rp. 9.000.000,-</td></tr>
            </tbody>
        </table>
        
        <ol>
            <li>Biaya kolom di atas hanya meliputi biaya pelatihan beserta kelengkapannya, tidak termasuk dengan biaya proses pemberangkatan[cite: 77].</li>
            <li>Biaya proses pemberangkatan tergantung program yang akan di ikuti oleh pihak kedua[cite: 78].</li>
            <li>Pihak pertama hanya memfasilitasi pembelajaran Bahasa dan budaya Jepang serta konsultasi[cite: 79].</li>
            <li>Adapun Konsultasi Fee dibicarakan setelah PIHAK KEDUA memilih program[cite: 80].</li>
        </ol>
    </div>

    <div class="pasal-title">Pasal 9</div>
    <div class="pasal-sub">PELANGGARAN</div>
    <ol>
        <li>Jika peserta pelatihan melakukan tindakan pelanggaran, akan dikenakan sanksi berdasarkan pasal 12[cite: 83].</li>
        <li>Jika PIHAK KEDUA tidak memahami tentang program, bergaul dalam kelompok pelanggaran, atau kegiatan pelanggaran fatal (narkoba dll)[cite: 84].</li>
        <li>Melakukan perusakan fasilitas PIHAK PERTAMA, kelakuan tidak baik (selama pelatihan kerja)[cite: 85].</li>
        <li>Tindakan yang melanggar hukum di Indonesia: Kekerasan, Merokok sembarangan, Pencurian, Menghina/Membully orang lain, Jual beli barang terlarang [cite: 86-91].</li>
    </ol>

    <div class="pasal-title">Pasal 10 (SANKSI PELANGGARAN)</div>
    <p class="text-justify" style="margin:0;">Jika melanggar Pasal 9, akan mendapatkan peringatan / Dikeluarkan / Dikembalikan ke keluarga tergantung beratnya pelanggaran[cite: 95].</p>

    <div class="pasal-title">Pasal 11 (JANGKA WAKTU)</div>
    <p class="text-justify" style="margin:0;">Pelatihan selesai ditentukan PIHAK PERTAMA (sampai N4). Jika peserta tidak hadir/mengikuti, perjanjian dapat dibatalkan. Biaya yang sudah dibayar tidak dapat diambil kembali[cite: 98, 99].</p>

    <div class="pasal-title">Pasal 12 (PENYELESAIAN PERSELISIHAN)</div>
    <ol>
        <li>Perselisihan diselesaikan dengan musyawarah[cite: 102].</li>
        <li>Jika buntu, diselesaikan melalui Putusan Pengadilan[cite: 103].</li>
        <li>Pelanggaran pribadi diselesaikan sesuai hukum yang berlaku di Indonesia[cite: 104].</li>
    </ol>

    <div class="pasal-title">Pasal 13</div>
    <p class="text-justify" style="margin:0;">Peserta wajib menjunjung tinggi nama baik PIHAK PERTAMA dan tidak merusak citra nama baik PIHAK PERTAMA[cite: 106].</p>

    <div class="pasal-title">Pasal 14 (MASA BERLAKU)</div>
    <ol>
        <li>Perjanjian berlaku sejak tanggal tanda tangan[cite: 109].</li>
        <li>Dibuat dua salinan untuk disimpan masing-masing pihak[cite: 110].</li>
    </ol>
    <p style="margin-top:5px;">Demikian surat perjanjian bersama ini dibuat, tanpa ada tekanan atau paksaan dari pihak manapun juga dan dibuat dalam keadaan sadar dan mengetahui orang tua sebagai saksi[cite: 111].</p>

    <br>
    
    <table class="ttd-table">
        <tr>
            <td colspan="2">
                {{ $profile->kota_ktp ?? 'Cianjur' }}, {{ $tanggalSurat }} [cite: 112]
            </td>
        </tr>
        <tr>
            <td width="50%"><b>PIHAK PERTAMA</b></td>
            <td width="50%"><b>PIHAK KEDUA</b></td>
        </tr>
        <tr>
            <td class="ttd-space" style="vertical-align: bottom;">
                <div style="font-size: 8pt; color: #666;">(Materai 10.000)</div>
            </td>
            <td class="ttd-space"></td>
        </tr>
        <tr>
            <td>
                <b><u>{{ strtoupper($profile->nama_pimpinan) }}</u></b><br>
                Direktur {{ $profile->nama_lpk }}
            </td>
            <td>
                <b><u>{{ strtoupper($student->nama_lengkap) }}</u></b><br>
                Peserta Pelatihan
            </td>
        </tr>
    </table>

    <table class="ttd-table" style="margin-top: 20px;">
        <tr>
            <td width="33%">
                Saksi 1 (Wali Peserta)<br><br><br><br>
                ( ..................................... )
            </td>
            <td width="33%">
                Saksi 2<br><br><br><br>
                ( ..................................... )
            </td>
            <td width="33%">
                Saksi 3<br><br><br><br>
                ( ..................................... )
            </td>
        </tr>
    </table>

</body>
</html>