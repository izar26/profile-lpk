<?php

namespace App\Exports;

use App\Models\Employee;
use Maatwebsite\Excel\Concerns\FromCollection;
use Maatwebsite\Excel\Concerns\WithHeadings;
use Maatwebsite\Excel\Concerns\WithMapping;
use Maatwebsite\Excel\Concerns\ShouldAutoSize;
use Maatwebsite\Excel\Concerns\WithStyles;
use PhpOffice\PhpSpreadsheet\Worksheet\Worksheet;

class EmployeesExport implements FromCollection, WithHeadings, WithMapping, ShouldAutoSize, WithStyles
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
        $query = Employee::latest();

        if ($this->ids) {
            $query->whereIn('id', $this->ids);
        }

        return $query->get();
    }

    /**
    * Mapping data per baris
    */
    public function map($employee): array
    {
        return [
            $employee->nama,
            "'" . $employee->nip, // Tanda kutip agar NIP dianggap teks (tidak error scientific notation)
            $employee->jabatan,
            $employee->status_kepegawaian,
            $employee->email,
            "'" . $employee->telepon, // Tanda kutip agar 0 di depan nomor HP tidak hilang
            $employee->alamat,
            $employee->kota,
            $employee->provinsi,
            $employee->created_at ? $employee->created_at->format('d-m-Y') : '-', // Tanggal Input Data
        ];
    }

    /**
    * Header Judul Kolom
    */
    public function headings(): array
    {
        return [
            'Nama Lengkap',
            'NIP',
            'Jabatan',
            'Status Kepegawaian',
            'Email',
            'Telepon',
            'Alamat',
            'Kota',
            'Provinsi',
            'Tanggal Input',
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