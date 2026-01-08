<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Relations\HasMany;

class Employee extends Model
{
    use HasFactory;

    // UPDATE FILLABLE SESUAI MIGRATION TERBARU
    protected $fillable = [
        'user_id',
        'nip',
        'nama',
        'email',
        'jabatan',
        'status_kepegawaian',

        // Biodata Fisik & Identitas (Baru)
        'nomor_ktp',
        'nomor_kk',
        'nomor_npwp',
        'tempat_lahir',
        'tanggal_lahir',
        'jenis_kelamin',
        'golongan_darah',
        'tinggi_badan',
        'berat_badan',
        'agama',
        'status_pernikahan',

        // Kontak & Alamat (Update)
        'telepon',
        'no_hp_keluarga_darurat',
        'alamat_ktp',      // Dulu 'alamat'
        'kota_ktp',        // Dulu 'kota'
        'provinsi_ktp',    // Dulu 'provinsi'
        // 'kode_pos',     // (Opsional jika masih ada di db)
        'alamat_domisili',

        // Medsos & File
        'foto',
        'instagram',
        // 'linkedin', // SUDAH DIHAPUS
        // 'pendidikan_terakhir' // SUDAH DIHAPUS (Pindah ke tabel relasi)
    ];

    protected $casts = [
        'tanggal_lahir' => 'date',
    ];

    // --- RELASI UTAMA ---

    public function user(): BelongsTo
    {
        return $this->belongsTo(User::class);
    }

    // Relasi ke Riwayat Pendidikan (Pengganti kolom pendidikan_terakhir)
    public function educations(): HasMany
    {
        return $this->hasMany(EmployeeEducation::class, 'employee_id');
    }

    // Relasi ke Data Keluarga
    public function families(): HasMany
    {
        return $this->hasMany(EmployeeFamily::class, 'employee_id');
    }

    public function documents()
    {
        return $this->hasMany(EmployeeDocument::class, 'employee_id');
    }

    // --- ACCESSOR KELENGKAPAN DATA (Mirip Student) ---
    // Gunanya untuk menampilkan progres kelengkapan profil pegawai (misal: 80% Lengkap)
    public function getDataCompletionAttribute()
    {
        // Field wajib bagi pegawai
        $fieldsToCheck = [
            'nama', 'nip', 'email', 'telepon',
            'nomor_ktp', 'nomor_kk', 'nomor_npwp',
            'tempat_lahir', 'tanggal_lahir', 'jenis_kelamin',
            'alamat_ktp', 'alamat_domisili',
            'foto', 'jabatan'
        ];

        $total = count($fieldsToCheck);
        $filled = 0;

        foreach ($fieldsToCheck as $field) {
            if (!empty($this->$field)) {
                $filled++;
            }
        }

        // Opsional: Wajib isi minimal 1 pendidikan dan 1 keluarga
        /*
        if ($this->educations()->count() > 0) { $filled++; } $total++;
        if ($this->families()->count() > 0) { $filled++; } $total++;
        */

        $percentage = ($total > 0) ? ($filled / $total) * 100 : 0;

        return [
            'filled' => $filled,
            'total' => $total,
            'percentage' => round($percentage),
            'is_complete' => $filled === $total,
            'text' => "$filled / $total Data Utama Terisi"
        ];
    }
}
