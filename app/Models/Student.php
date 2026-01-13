<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Relations\HasMany;

class Student extends Model
{
    use HasFactory;

    protected $fillable = [
        'user_id',
        'participant_number',
        'program_pelatihan_id',
        'foto',
        'nama_lengkap',
        'tempat_lahir',
        'tanggal_lahir',
        'jenis_kelamin',
        'tinggi_badan',
        'berat_badan',
        'alamat_ktp',
        'kota_ktp',
        'provinsi_ktp',
        'alamat_domisili',
        'status_pernikahan',
        'agama',

        // Identitas & Kontak
        'nomor_ktp',
        'nomor_kk',       // [Cite: Lampiran/Data Keluarga]
        'nomor_paspor',
        'nomor_npwp',
        'email',
        'golongan_darah',
        'no_hp_peserta',
        'no_hp_ortu',

        // Pertanyaan Khusus
        'pernah_bekerja', // [NEW] Boolean field

        // Lampiran
        'file_ktp',
        'file_kk',
        'file_ijazah',
        'file_sertifikat_jlpt',
        'file_rekomendasi_sekolah',
        'file_izin_ortu',

        // Tanda Tangan
        'kota_pembuatan', // [NEW] Untuk isian kota di atas ttd

        // System
        'status',
        'admin_note',
        'verified_at'
    ];

    protected $casts = [
        'tanggal_lahir' => 'date',
        'verified_at' => 'datetime',
        'pernah_bekerja' => 'boolean', // Casting otomatis ke true/false
    ];

    protected static function booted()
    {
        static::creating(function ($student) {
            // Jika nomor peserta sudah diisi (custom), jangan generate otomatis
            if (!empty($student->participant_number)) {
                return;
            }

            $now = now();
            $month = $now->format('m');
            $year = $now->format('Y');
            $prefix = 'HGS';

            // Cari nomor urut terakhir di bulan & tahun ini
            $latestStudent = self::whereYear('created_at', $year)
                                 ->whereMonth('created_at', $month)
                                 ->whereNotNull('participant_number')
                                 ->latest('id')
                                 ->first();
            
            $sequence = 1;
            if ($latestStudent) {
                // Format: HGS/002/10/2025
                $parts = explode('/', $latestStudent->participant_number);
                if (count($parts) === 4) {
                     $sequence = intval($parts[1]) + 1;
                }
            }
            
            $student->participant_number = sprintf('%s/%03d/%s/%s', $prefix, $sequence, $month, $year);
        });
    }

    // --- RELASI ---

    public function program(): BelongsTo
    {
        return $this->belongsTo(ProgramPelatihan::class, 'program_pelatihan_id');
    }

    public function user(): BelongsTo
    {
        return $this->belongsTo(User::class);
    }

    public function educations(): HasMany
    {
        return $this->hasMany(StudentEducation::class, 'student_id');
    }

    public function families(): HasMany
    {
        return $this->hasMany(StudentFamily::class, 'student_id');
    }

    public function experiences(): HasMany
    {
        return $this->hasMany(StudentExperience::class, 'student_id');
    }

    public function alumni()
    {
        return $this->hasOne(Alumni::class);
    }

    // --- ACCESSOR KELENGKAPAN DATA (LOGIKA BARU) ---
    public function getDataCompletionAttribute()
    {
        // Daftar field wajib sesuai Formulir Hachimitsu
        $fieldsToCheck = [
            'nama_lengkap',
            'tempat_lahir', 'tanggal_lahir', 'jenis_kelamin',
            'alamat_ktp', 'alamat_domisili',
            'nomor_ktp', 'email', 'no_hp_peserta',
            'file_ktp', 'file_kk', 'file_ijazah', 'foto'
        ];

        $total = count($fieldsToCheck);
        $filled = 0;

        foreach ($fieldsToCheck as $field) {
            if (!empty($this->$field)) {
                $filled++;
            }
        }

        // Cek relasi (Opsional: Tambahkan logika jika wajib mengisi minimal 1 keluarga/pendidikan)
        // Contoh sederhana:
        // if ($this->families()->count() > 0) $filled++;
        // $total++; // Jangan lupa tambah total jika nambah kriteria

        $percentage = ($total > 0) ? ($filled / $total) * 100 : 0;

        return [
            'filled' => $filled,
            'total' => $total,
            'percentage' => round($percentage),
            'is_complete' => $filled === $total,
            'text' => "$filled / $total Field Utama Terisi"
        ];
    }


// Tambahkan fungsi ini
public function documents()
{
    return $this->hasMany(StudentDocument::class, 'student_id');
}

// Helper untuk mengecek apakah siswa sudah upload dokumen tertentu (berdasarkan ID tipe)
public function hasDocument($typeId)
{
    return $this->documents()->where('document_type_id', $typeId)->exists();
}

// Helper untuk mengambil path file dokumen tertentu
public function getDocumentPath($typeId)
{
    $doc = $this->documents()->where('document_type_id', $typeId)->first();
    return $doc ? $doc->file_path : null;
}
}
