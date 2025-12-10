<?php

namespace App\Exports;

use App\Models\Student;
use Maatwebsite\Excel\Concerns\FromCollection;
use Maatwebsite\Excel\Concerns\WithHeadings;
use Maatwebsite\Excel\Concerns\WithMapping;
use Maatwebsite\Excel\Concerns\ShouldAutoSize;
use Maatwebsite\Excel\Concerns\WithStyles;
use PhpOffice\PhpSpreadsheet\Worksheet\Worksheet;

class StudentsExport implements FromCollection, WithHeadings, WithMapping, ShouldAutoSize, WithStyles
{
    protected $ids;

    public function __construct($ids = null)
    {
        $this->ids = $ids;
    }

    /**
    * Ambil data dari database
    */
    public function collection()
    {
        $query = Student::with('program');

        if ($this->ids) {
            $query->whereIn('id', $this->ids);
        }

        return $query->get();
    }

    /**
    * Mapping data per baris (Agar KTP/HP tidak jadi format scientific 1.23E+15)
    */
    public function map($student): array
    {
        return [
            $student->nama_lengkap,
            "'" . $student->nomor_ktp, // Tanda kutip agar Excel membacanya sebagai Teks, bukan Angka
            $student->program->judul ?? '-',
            $student->status,
            $student->email,
            "'" . $student->no_hp_peserta, // Tanda kutip agar 0 di depan tidak hilang
            $student->alamat_domisili,
            \Carbon\Carbon::parse($student->created_at)->format('d-m-Y'), // Tanggal Daftar
        ];
    }

    /**
    * Header Judul Kolom
    */
    public function headings(): array
    {
        return [
            'Nama Lengkap',
            'Nomor KTP',
            'Program Pelatihan',
            'Status',
            'Email',
            'No HP / WA',
            'Alamat Domisili',
            'Tanggal Mendaftar',
        ];
    }

    /**
    * Styling Header (Bold)
    */
    public function styles(Worksheet $sheet)
    {
        return [
            // Baris 1 di-bold
            1 => ['font' => ['bold' => true]],
        ];
    }
}