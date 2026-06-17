<?php

namespace App\Exports;

use Illuminate\Support\Collection;
use Maatwebsite\Excel\Concerns\FromCollection;
use Maatwebsite\Excel\Concerns\WithHeadings;
use Maatwebsite\Excel\Concerns\WithMapping;
use Maatwebsite\Excel\Concerns\ShouldAutoSize;
use Maatwebsite\Excel\Concerns\WithStyles;
use PhpOffice\PhpSpreadsheet\Worksheet\Worksheet;

class PartnersExport implements FromCollection, WithHeadings, WithMapping, ShouldAutoSize, WithStyles
{
    protected $partners;

    public function __construct(Collection $partners)
    {
        $this->partners = $partners;
    }

    public function collection()
    {
        return $this->partners;
    }

    public function headings(): array
    {
        return [
            'ID',
            'Email',
            'First Name',
            'Middle Name',
            'Last Name',
            'User Type',
            'Country',
            'Role',
            'House Of Ecclesia',
            'Registration Agreement',
            'Phone',
            'Status',
        ];
    }

    public function map($partner): array
    {
        return [
            $partner->lion_roaring_id ?? $partner->id,
            $partner->email,
            $partner->first_name,
            $partner->middle_name,
            $partner->last_name,
            $partner->user_type,
            $partner->countries->name ?? '-',
            $partner->userRole->name ?? '',
            $partner->ecclesia->name ?? 'NO NAME',
            $partner->userRegisterAgreement ? 'Yes' : 'No',
            $partner->phone,
            $partner->status == 1 ? 'Active' : 'Inactive',
        ];
    }

    public function styles(Worksheet $sheet)
    {
        return [
            1 => ['font' => ['bold' => true]],
        ];
    }
}
