<?php

namespace App\Http\Controllers\User;

use App\Exports\RolePermissionAuditLogsExport;
use App\Http\Controllers\Controller;
use App\Models\RolePermissionAuditLog;
use App\Models\User;
use App\Support\PartnerVisibility;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Crypt;
use Maatwebsite\Excel\Facades\Excel;

class RolePermissionAuditLogController extends Controller
{
    public function index(Request $request)
    {
        $viewer = Auth::user();

        if (! PartnerVisibility::canAccessAudit($viewer)) {
            abort(403, 'You do not have permission to access this page.');
        }

        $logs = $this->baseQuery($request, $viewer)->paginate(15)->withQueryString();

        return view('user.partner.audit-logs', [
            'logs' => $logs,
            'targetUser' => null,
            'actions' => $this->actionOptions(),
            'sources' => $this->sourceOptions(),
        ]);
    }

    public function member(Request $request, $id)
    {
        $viewer = Auth::user();

        if (! PartnerVisibility::canAccessAudit($viewer)) {
            abort(403, 'You do not have permission to access this page.');
        }

        $partnerId = Crypt::decrypt($id);
        $targetUser = User::findOrFail($partnerId);

        if (! PartnerVisibility::viewerCanSeePartner($viewer, $targetUser)) {
            abort(403, 'You do not have permission to access this page.');
        }

        $logs = $this->baseQuery($request, $viewer, $partnerId)->paginate(15)->withQueryString();

        return view('user.partner.audit-logs', [
            'logs' => $logs,
            'targetUser' => $targetUser,
            'actions' => $this->actionOptions(),
            'sources' => $this->sourceOptions(),
        ]);
    }

    public function export(Request $request)
    {
        $viewer = Auth::user();

        if (! PartnerVisibility::canAccessAudit($viewer)) {
            abort(403, 'You do not have permission to access this page.');
        }

        return $this->downloadExport($request, $viewer);
    }

    public function memberExport(Request $request, $id)
    {
        $viewer = Auth::user();

        if (! PartnerVisibility::canAccessAudit($viewer)) {
            abort(403, 'You do not have permission to access this page.');
        }

        $partnerId = Crypt::decrypt($id);
        $targetUser = User::findOrFail($partnerId);

        if (! PartnerVisibility::viewerCanSeePartner($viewer, $targetUser)) {
            abort(403, 'You do not have permission to access this page.');
        }

        return $this->downloadExport($request, $viewer, $partnerId);
    }

    private function baseQuery(Request $request, User $viewer, ?int $targetUserId = null)
    {
        $query = RolePermissionAuditLog::query();

        PartnerVisibility::constrainAuditQuery($query, $viewer);

        if ($targetUserId !== null) {
            $query->where('target_user_id', $targetUserId);
        }

        if ($request->filled('date_from')) {
            $query->whereDate('created_at', '>=', $request->date_from);
        }

        if ($request->filled('date_to')) {
            $query->whereDate('created_at', '<=', $request->date_to);
        }

        if ($request->filled('action')) {
            $query->where('action', $request->action);
        }

        if ($request->filled('source')) {
            $query->where('source', $request->source);
        }

        if ($request->filled('actor')) {
            $actor = $request->actor;
            $query->where(function ($q) use ($actor) {
                $q->where('actor_name', 'like', '%'.$actor.'%')
                    ->orWhere('actor_email', 'like', '%'.$actor.'%');
            });
        }

        if ($targetUserId === null && $request->filled('target')) {
            $target = $request->target;
            $query->where(function ($q) use ($target) {
                $q->where('target_user_name', 'like', '%'.$target.'%')
                    ->orWhere('target_user_email', 'like', '%'.$target.'%');
            });
        }

        if ($request->filled('role')) {
            $role = $request->role;
            $query->where(function ($q) use ($role) {
                $q->where('old_role_name', 'like', '%'.$role.'%')
                    ->orWhere('new_role_name', 'like', '%'.$role.'%');
            });
        }

        return $query->orderByDesc('created_at')->orderByDesc('id');
    }

    private function downloadExport(Request $request, User $viewer, ?int $targetUserId = null)
    {
        $rows = $this->baseQuery($request, $viewer, $targetUserId)
            ->limit(5000)
            ->get();

        $filename = 'role_permission_audit_logs_'.date('Ymd_His').'.xlsx';

        return Excel::download(new RolePermissionAuditLogsExport($rows), $filename);
    }

    private function actionOptions(): array
    {
        return [
            'member_role_created',
            'member_role_updated',
            'member_permissions_updated',
            'role_template_created',
            'role_template_updated',
            'role_template_deleted',
            'membership_privilege_synced',
        ];
    }

    private function sourceOptions(): array
    {
        return [
            'pma',
            'api',
            'membership_sync',
            'registration',
            'system',
        ];
    }
}
