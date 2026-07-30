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
            'Permissions Removed', 'Field Changes', 'IP',
        ];
    }

    public function map($row): array
    {
        $fieldChanges = collect($row->field_changes ?? [])
            ->map(function ($change) {
                if (($change['field'] ?? '') === 'permissions') {
                    $added = $change['added'] ?? [];
                    $removed = $change['removed'] ?? [];
                    if ($added === [] && $removed === [] && is_array($change['old'] ?? null) && is_array($change['new'] ?? null)) {
                        $added = array_values(array_diff($change['new'], $change['old']));
                        $removed = array_values(array_diff($change['old'], $change['new']));
                    }

                    return 'Permissions: +[' . implode(', ', $added) . '] -[' . implode(', ', $removed) . ']';
                }

                $old = is_array($change['old'] ?? null)
                    ? implode(', ', $change['old'])
                    : ($change['old'] ?? '—');
                $new = is_array($change['new'] ?? null)
                    ? implode(', ', $change['new'])
                    : ($change['new'] ?? '—');

                return ($change['label'] ?? $change['field'] ?? 'field') . ': ' . $old . ' → ' . $new;
            })
            ->implode('; ');

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
            $fieldChanges,
            $row->ip,
        ];
    }
}
