<?php

namespace App\Exports;

use Illuminate\Support\Collection;
use Maatwebsite\Excel\Concerns\FromCollection;
use Maatwebsite\Excel\Concerns\ShouldAutoSize;
use Maatwebsite\Excel\Concerns\WithHeadings;
use Maatwebsite\Excel\Concerns\WithMapping;

class RolePermissionAuditLogsExport implements FromCollection, WithHeadings, WithMapping, ShouldAutoSize
{
    public function __construct(protected Collection $rows) {}

    public function collection()
    {
        return $this->rows;
    }

    public function headings(): array
    {
        return [
            'Date', 'Action', 'Source', 'Actor', 'Actor Email',
            'Target Member', 'Target Email', 'Old Role', 'New Role',
            'Old User Type', 'New User Type', 'Permissions Added',
            'Permissions Removed', 'IP',
        ];
    }

    public function map($row): array
    {
        return [
            optional($row->created_at)?->format('Y-m-d H:i:s'),
            $row->action,
            $row->source,
            $row->actor_name,
            $row->actor_email,
            $row->target_user_name,
            $row->target_user_email,
            $row->old_role_name,
            $row->new_role_name,
            $row->old_user_type,
            $row->new_user_type,
            implode(', ', $row->permissions_added ?? []),
            implode(', ', $row->permissions_removed ?? []),
            $row->ip,
        ];
    }
}
