# Support Reports & Change Logs Implementation Plan

> **For agentic workers:** REQUIRED SUB-SKILL: Use superpowers:subagent-driven-development (recommended) or superpowers:executing-plans to implement this plan task-by-task. Steps use checkbox (`- [ ]`) syntax for tracking.

**Goal:** Add Support Reports (ticket submission + management) and Change Logs (release notes) to the User PMA sidebar, available to all users with management views gated by role permissions.

**Architecture:** Two self-contained modules following the existing PMA pattern — each has its own migration, model, controller(s), Blade views, and sidebar entries. Spatie permissions gate management actions. Email notifications sent via Laravel Mailables using existing mail infrastructure.

**Tech Stack:** Laravel (PHP), Blade templates, Spatie Laravel Permission, Bootstrap 5 (existing), `Storage::disk('public')` for attachments, Laravel Mailables.

## Global Constraints

- All user panel routes live under prefix `user` with middleware `['user', 'preventBackHistory', 'userActivity', 'member.access', 'agreement.signed']` — match exactly.
- Blade views extend `user.layouts.master`.
- Use `Helper::getMenuName('key', 'Default')` for sidebar labels.
- Use `Gate::check('Permission Name')` for permission gates in sidebar.
- Use `auth()->user()->can('Permission Name')` in controllers — abort(403) on failure.
- File uploads use `Storage::disk('public')` via the `ImageTrait::imageUpload()` trait at path `support-reports/attachments`.
- For non-image attachments (pdf/doc), store with `$file->storeAs('support-reports/attachments', $filename, 'public')` directly.
- Permissions added to `database/seeders/RolePermissionSeeder.php` array AND via a standalone migration-seeder.
- Sidebar menu name keys added to `sidebar_menus` table via a seeder.
- All pagination uses `->paginate(15)`.
- No delete on support reports — only status change.
- Permission names: `Manage Support Reports`, `Manage Change Logs`.

---

## File Map

**Create:**
- `database/migrations/2026_07_27_000001_create_support_reports_table.php`
- `database/migrations/2026_07_27_000002_create_change_logs_table.php`
- `database/seeders/AddSupportReportsChangeLogsPermissionsSeeder.php`
- `app/Models/SupportReport.php`
- `app/Models/ChangeLog.php`
- `app/Mail/SupportReportSubmittedMail.php`
- `app/Mail/SupportReportStatusUpdatedMail.php`
- `resources/views/emails/support_report_submitted.blade.php`
- `resources/views/emails/support_report_status_updated.blade.php`
- `app/Http/Controllers/User/SupportReportController.php`
- `app/Http/Controllers/User/SupportReportManageController.php`
- `app/Http/Controllers/User/ChangeLogController.php`
- `resources/views/user/support-reports/index.blade.php`
- `resources/views/user/support-reports/create.blade.php`
- `resources/views/user/support-reports/show.blade.php`
- `resources/views/user/support-reports/manage/index.blade.php`
- `resources/views/user/support-reports/manage/show.blade.php`
- `resources/views/user/change-logs/index.blade.php`
- `resources/views/user/change-logs/show.blade.php`
- `resources/views/user/change-logs/create.blade.php`
- `resources/views/user/change-logs/edit.blade.php`
- `docs/pma/support-reports.md`
- `docs/pma/change-logs.md`

**Modify:**
- `database/seeders/RolePermissionSeeder.php` — add two permissions to array
- `routes/web.php` — add route groups
- `resources/views/user/includes/sidebar.blade.php` — add two sidebar entries
- `docs/pma/user-pma.md` — update Major Menu Groups section

---

## Task 1: Migrations — Create Tables

**Files:**
- Create: `database/migrations/2026_07_27_000001_create_support_reports_table.php`
- Create: `database/migrations/2026_07_27_000002_create_change_logs_table.php`

**Interfaces:**
- Produces: `support_reports` table, `change_logs` table used by all subsequent tasks.

- [ ] **Step 1: Create support_reports migration**

```php
<?php
// database/migrations/2026_07_27_000001_create_support_reports_table.php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration {
    public function up(): void
    {
        Schema::create('support_reports', function (Blueprint $table) {
            $table->id();
            $table->foreignId('user_id')->constrained()->cascadeOnDelete();
            $table->string('subject');
            $table->text('message');
            $table->string('attachment')->nullable();
            $table->enum('status', ['open', 'in_progress', 'resolved', 'closed'])->default('open');
            $table->text('admin_notes')->nullable();
            $table->foreignId('resolved_by')->nullable()->constrained('users')->nullOnDelete();
            $table->timestamp('resolved_at')->nullable();
            $table->timestamps();
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('support_reports');
    }
};
```

- [ ] **Step 2: Create change_logs migration**

```php
<?php
// database/migrations/2026_07_27_000002_create_change_logs_table.php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration {
    public function up(): void
    {
        Schema::create('change_logs', function (Blueprint $table) {
            $table->id();
            $table->foreignId('created_by')->constrained('users')->cascadeOnDelete();
            $table->string('version');
            $table->string('title');
            $table->longText('description');
            $table->enum('type', ['feature', 'improvement', 'bugfix', 'security'])->default('feature');
            $table->timestamp('published_at')->nullable();
            $table->timestamps();
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('change_logs');
    }
};
```

- [ ] **Step 3: Run migrations**

```bash
cd /Volumes/CrucialMacExt/MacOffload/MAMP_htdocs/lion_roaring
php artisan migrate
```

Expected: `support_reports` and `change_logs` tables created. No errors.

- [ ] **Step 4: Commit**

```bash
git add database/migrations/2026_07_27_000001_create_support_reports_table.php database/migrations/2026_07_27_000002_create_change_logs_table.php
git commit -m "feat: add support_reports and change_logs migrations"
```

---

## Task 2: Permissions Seeder

**Files:**
- Create: `database/seeders/AddSupportReportsChangeLogsPermissionsSeeder.php`
- Modify: `database/seeders/RolePermissionSeeder.php`

**Interfaces:**
- Produces: Spatie permissions `Manage Support Reports` and `Manage Change Logs` in the database. Sidebar menu keys `support_reports` and `change_logs` in `sidebar_menus` table.

- [ ] **Step 1: Create the permissions + sidebar menu seeder**

```php
<?php
// database/seeders/AddSupportReportsChangeLogsPermissionsSeeder.php

namespace Database\Seeders;

use Illuminate\Database\Seeder;
use Spatie\Permission\Models\Permission;
use Illuminate\Support\Facades\DB;

class AddSupportReportsChangeLogsPermissionsSeeder extends Seeder
{
    public function run(): void
    {
        $permissions = [
            'Manage Support Reports',
            'Manage Change Logs',
        ];

        foreach ($permissions as $name) {
            Permission::firstOrCreate(['name' => $name, 'guard_name' => 'web']);
        }

        $sidebarMenus = [
            ['key' => 'support_reports',   'default_name' => 'Support Reports'],
            ['key' => 'change_logs',        'default_name' => 'Change Logs'],
        ];

        foreach ($sidebarMenus as $menu) {
            $exists = DB::table('sidebar_menus')->where('key', $menu['key'])->exists();
            if (!$exists) {
                DB::table('sidebar_menus')->insert([
                    'key'          => $menu['key'],
                    'default_name' => $menu['default_name'],
                    'created_at'   => now(),
                    'updated_at'   => now(),
                ]);
            }
        }
    }
}
```

- [ ] **Step 2: Add permissions to RolePermissionSeeder array**

Open `database/seeders/RolePermissionSeeder.php`. Find the `$permissions` array (around line 20). Add after the last `// Help` section entry:

```php
// Support Reports
["name" => "Manage Support Reports"],

// Change Logs
["name" => "Manage Change Logs"],
```

- [ ] **Step 3: Run the seeder**

```bash
php artisan db:seed --class=AddSupportReportsChangeLogsPermissionsSeeder
```

Expected: `Manage Support Reports` and `Manage Change Logs` appear in `permissions` table. `support_reports` and `change_logs` appear in `sidebar_menus` table.

- [ ] **Step 4: Commit**

```bash
git add database/seeders/AddSupportReportsChangeLogsPermissionsSeeder.php database/seeders/RolePermissionSeeder.php
git commit -m "feat: add Manage Support Reports and Manage Change Logs permissions"
```

---

## Task 3: Models

**Files:**
- Create: `app/Models/SupportReport.php`
- Create: `app/Models/ChangeLog.php`

**Interfaces:**
- Produces:
  - `SupportReport` — fillable: `user_id`, `subject`, `message`, `attachment`, `status`, `admin_notes`, `resolved_by`, `resolved_at`. Relations: `user()`, `resolver()`. Scope: `forUser($userId)`.
  - `ChangeLog` — fillable: `created_by`, `version`, `title`, `description`, `type`, `published_at`. Scope: `published()`. Relation: `author()`.

- [ ] **Step 1: Create SupportReport model**

```php
<?php
// app/Models/SupportReport.php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

class SupportReport extends Model
{
    protected $fillable = [
        'user_id',
        'subject',
        'message',
        'attachment',
        'status',
        'admin_notes',
        'resolved_by',
        'resolved_at',
    ];

    protected $casts = [
        'resolved_at' => 'datetime',
    ];

    public function user(): BelongsTo
    {
        return $this->belongsTo(User::class, 'user_id');
    }

    public function resolver(): BelongsTo
    {
        return $this->belongsTo(User::class, 'resolved_by');
    }

    public function scopeForUser($query, int $userId)
    {
        return $query->where('user_id', $userId);
    }

    public function statusBadgeClass(): string
    {
        return match($this->status) {
            'open'        => 'badge bg-danger',
            'in_progress' => 'badge bg-warning text-dark',
            'resolved'    => 'badge bg-success',
            'closed'      => 'badge bg-secondary',
            default       => 'badge bg-secondary',
        };
    }
}
```

- [ ] **Step 2: Create ChangeLog model**

```php
<?php
// app/Models/ChangeLog.php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

class ChangeLog extends Model
{
    protected $fillable = [
        'created_by',
        'version',
        'title',
        'description',
        'type',
        'published_at',
    ];

    protected $casts = [
        'published_at' => 'datetime',
    ];

    public function author(): BelongsTo
    {
        return $this->belongsTo(User::class, 'created_by');
    }

    public function scopePublished($query)
    {
        return $query->whereNotNull('published_at')->where('published_at', '<=', now());
    }

    public function typeBadgeClass(): string
    {
        return match($this->type) {
            'feature'     => 'badge bg-primary',
            'improvement' => 'badge bg-info text-dark',
            'bugfix'      => 'badge bg-warning text-dark',
            'security'    => 'badge bg-danger',
            default       => 'badge bg-secondary',
        };
    }
}
```

- [ ] **Step 3: Commit**

```bash
git add app/Models/SupportReport.php app/Models/ChangeLog.php
git commit -m "feat: add SupportReport and ChangeLog models"
```

---

## Task 4: Email Mailables and Views

**Files:**
- Create: `app/Mail/SupportReportSubmittedMail.php`
- Create: `app/Mail/SupportReportStatusUpdatedMail.php`
- Create: `resources/views/emails/support_report_submitted.blade.php`
- Create: `resources/views/emails/support_report_status_updated.blade.php`

**Interfaces:**
- Consumes: `SupportReport` model (Task 3).
- Produces:
  - `SupportReportSubmittedMail($report)` — email to managers on new submission.
  - `SupportReportStatusUpdatedMail($report)` — email to submitter on status update.

- [ ] **Step 1: Create SupportReportSubmittedMail**

```php
<?php
// app/Mail/SupportReportSubmittedMail.php

namespace App\Mail;

use App\Models\SupportReport;
use Illuminate\Bus\Queueable;
use Illuminate\Mail\Mailable;
use Illuminate\Queue\SerializesModels;

class SupportReportSubmittedMail extends Mailable
{
    use Queueable, SerializesModels;

    public SupportReport $report;

    public function __construct(SupportReport $report)
    {
        $this->report = $report;
    }

    public function build(): self
    {
        return $this->subject('New Support Report Submitted: ' . $this->report->subject)
            ->view('emails.support_report_submitted')
            ->with(['report' => $this->report]);
    }
}
```

- [ ] **Step 2: Create SupportReportStatusUpdatedMail**

```php
<?php
// app/Mail/SupportReportStatusUpdatedMail.php

namespace App\Mail;

use App\Models\SupportReport;
use Illuminate\Bus\Queueable;
use Illuminate\Mail\Mailable;
use Illuminate\Queue\SerializesModels;

class SupportReportStatusUpdatedMail extends Mailable
{
    use Queueable, SerializesModels;

    public SupportReport $report;

    public function __construct(SupportReport $report)
    {
        $this->report = $report;
    }

    public function build(): self
    {
        return $this->subject('Your Support Report Has Been Updated: ' . $this->report->subject)
            ->view('emails.support_report_status_updated')
            ->with(['report' => $this->report]);
    }
}
```

- [ ] **Step 3: Create email view for new submission**

```blade
{{-- resources/views/emails/support_report_submitted.blade.php --}}
<!DOCTYPE html>
<html>
<body style="font-family: Arial, sans-serif; color: #333; padding: 20px;">
    <h2>New Support Report Submitted</h2>
    <p>A new support report has been submitted by <strong>{{ $report->user->name ?? 'A user' }}</strong>.</p>
    <table style="border-collapse: collapse; width: 100%; max-width: 600px;">
        <tr>
            <td style="padding: 8px; font-weight: bold; width: 120px;">Subject:</td>
            <td style="padding: 8px;">{{ $report->subject }}</td>
        </tr>
        <tr>
            <td style="padding: 8px; font-weight: bold;">Message:</td>
            <td style="padding: 8px;">{{ $report->message }}</td>
        </tr>
        <tr>
            <td style="padding: 8px; font-weight: bold;">Submitted:</td>
            <td style="padding: 8px;">{{ $report->created_at->format('d M Y, H:i') }}</td>
        </tr>
    </table>
    <p style="margin-top: 20px;">
        <a href="{{ url('/user/support-reports/manage/' . $report->id) }}"
           style="background: #0d6efd; color: #fff; padding: 10px 20px; text-decoration: none; border-radius: 4px;">
            View Report
        </a>
    </p>
</body>
</html>
```

- [ ] **Step 4: Create email view for status update**

```blade
{{-- resources/views/emails/support_report_status_updated.blade.php --}}
<!DOCTYPE html>
<html>
<body style="font-family: Arial, sans-serif; color: #333; padding: 20px;">
    <h2>Your Support Report Has Been Updated</h2>
    <p>Your support report has been reviewed and its status has been updated.</p>
    <table style="border-collapse: collapse; width: 100%; max-width: 600px;">
        <tr>
            <td style="padding: 8px; font-weight: bold; width: 120px;">Subject:</td>
            <td style="padding: 8px;">{{ $report->subject }}</td>
        </tr>
        <tr>
            <td style="padding: 8px; font-weight: bold;">New Status:</td>
            <td style="padding: 8px; text-transform: capitalize;">{{ str_replace('_', ' ', $report->status) }}</td>
        </tr>
        @if($report->admin_notes)
        <tr>
            <td style="padding: 8px; font-weight: bold; vertical-align: top;">Notes:</td>
            <td style="padding: 8px;">{{ $report->admin_notes }}</td>
        </tr>
        @endif
    </table>
    <p style="margin-top: 20px;">
        <a href="{{ url('/user/support-reports/' . $report->id) }}"
           style="background: #0d6efd; color: #fff; padding: 10px 20px; text-decoration: none; border-radius: 4px;">
            View Your Report
        </a>
    </p>
</body>
</html>
```

- [ ] **Step 5: Commit**

```bash
git add app/Mail/ resources/views/emails/
git commit -m "feat: add support report email mailables and views"
```

---

## Task 5: SupportReportController (User — Own Reports)

**Files:**
- Create: `app/Http/Controllers/User/SupportReportController.php`

**Interfaces:**
- Consumes: `SupportReport` model (Task 3), `SupportReportSubmittedMail` (Task 4), `ImageTrait`.
- Produces routes: `support-reports.index`, `support-reports.create`, `support-reports.store`, `support-reports.show`.

- [ ] **Step 1: Create controller**

```php
<?php
// app/Http/Controllers/User/SupportReportController.php

namespace App\Http\Controllers\User;

use App\Http\Controllers\Controller;
use App\Mail\SupportReportSubmittedMail;
use App\Models\SupportReport;
use App\Traits\ImageTrait;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Mail;
use Illuminate\Support\Facades\Storage;
use Spatie\Permission\Models\Permission;

class SupportReportController extends Controller
{
    use ImageTrait;

    public function index()
    {
        $reports = SupportReport::forUser(auth()->id())
            ->orderBy('id', 'desc')
            ->paginate(15);

        return view('user.support-reports.index', compact('reports'));
    }

    public function create()
    {
        return view('user.support-reports.create');
    }

    public function store(Request $request)
    {
        $validated = $request->validate([
            'subject'    => 'required|string|max:255',
            'message'    => 'required|string',
            'attachment' => 'nullable|file|mimes:jpg,jpeg,png,gif,pdf,doc,docx|max:5120',
        ]);

        $attachmentPath = null;
        if ($request->hasFile('attachment')) {
            $file = $request->file('attachment');
            $ext = strtolower($file->getClientOriginalExtension());
            $allowedImages = ['jpg', 'jpeg', 'png', 'gif'];

            if (in_array($ext, $allowedImages)) {
                $attachmentPath = $this->imageUpload($file, 'support-reports/attachments');
            } else {
                $filename = date('YmdHis') . '_' . uniqid() . '.' . $ext;
                $attachmentPath = $file->storeAs('support-reports/attachments', $filename, 'public');
            }
        }

        $report = SupportReport::create([
            'user_id'    => auth()->id(),
            'subject'    => $validated['subject'],
            'message'    => $validated['message'],
            'attachment' => $attachmentPath,
            'status'     => 'open',
        ]);

        // Notify all users with Manage Support Reports permission
        $managers = \App\Models\User::permission('Manage Support Reports')->where('status', 1)->get();
        foreach ($managers as $manager) {
            try {
                Mail::to($manager->email)->send(new SupportReportSubmittedMail($report));
            } catch (\Exception $e) {
                \Illuminate\Support\Facades\Log::error('SupportReport notify failed: ' . $e->getMessage());
            }
        }

        return redirect()->route('support-reports.index')
            ->with('message', 'Your support report has been submitted successfully.');
    }

    public function show(SupportReport $supportReport)
    {
        if ($supportReport->user_id !== auth()->id()) {
            abort(403, 'You do not have permission to view this report.');
        }

        return view('user.support-reports.show', ['report' => $supportReport]);
    }
}
```

- [ ] **Step 2: Commit**

```bash
git add app/Http/Controllers/User/SupportReportController.php
git commit -m "feat: add SupportReportController (user own reports)"
```

---

## Task 6: SupportReportManageController (Management)

**Files:**
- Create: `app/Http/Controllers/User/SupportReportManageController.php`

**Interfaces:**
- Consumes: `SupportReport` model (Task 3), `SupportReportStatusUpdatedMail` (Task 4).
- Produces routes: `support-reports.manage.index`, `support-reports.manage.show`, `support-reports.manage.update`.

- [ ] **Step 1: Create controller**

```php
<?php
// app/Http/Controllers/User/SupportReportManageController.php

namespace App\Http\Controllers\User;

use App\Http\Controllers\Controller;
use App\Mail\SupportReportStatusUpdatedMail;
use App\Models\SupportReport;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Mail;
use Illuminate\Support\Facades\Log;

class SupportReportManageController extends Controller
{
    public function index(Request $request)
    {
        if (!auth()->user()->can('Manage Support Reports')) {
            abort(403, 'You do not have permission to access this page.');
        }

        $query = SupportReport::with('user')->orderBy('id', 'desc');

        if ($request->filled('status')) {
            $query->where('status', $request->status);
        }

        $reports = $query->paginate(15)->withQueryString();
        $statusFilter = $request->status;

        return view('user.support-reports.manage.index', compact('reports', 'statusFilter'));
    }

    public function show(SupportReport $supportReport)
    {
        if (!auth()->user()->can('Manage Support Reports')) {
            abort(403, 'You do not have permission to access this page.');
        }

        $supportReport->load('user', 'resolver');

        return view('user.support-reports.manage.show', ['report' => $supportReport]);
    }

    public function update(Request $request, SupportReport $supportReport)
    {
        if (!auth()->user()->can('Manage Support Reports')) {
            abort(403, 'You do not have permission to access this page.');
        }

        $validated = $request->validate([
            'status'      => 'required|in:open,in_progress,resolved,closed',
            'admin_notes' => 'nullable|string',
        ]);

        $updateData = [
            'status'      => $validated['status'],
            'admin_notes' => $validated['admin_notes'] ?? null,
        ];

        if (in_array($validated['status'], ['resolved', 'closed']) && !$supportReport->resolved_at) {
            $updateData['resolved_by'] = auth()->id();
            $updateData['resolved_at'] = now();
        }

        $supportReport->update($updateData);

        // Notify submitter
        try {
            Mail::to($supportReport->user->email)->send(new SupportReportStatusUpdatedMail($supportReport));
        } catch (\Exception $e) {
            Log::error('SupportReport status notify failed: ' . $e->getMessage());
        }

        return redirect()->route('support-reports.manage.show', $supportReport)
            ->with('message', 'Report updated successfully.');
    }
}
```

- [ ] **Step 2: Commit**

```bash
git add app/Http/Controllers/User/SupportReportManageController.php
git commit -m "feat: add SupportReportManageController"
```

---

## Task 7: ChangeLogController

**Files:**
- Create: `app/Http/Controllers/User/ChangeLogController.php`

**Interfaces:**
- Consumes: `ChangeLog` model (Task 3).
- Produces routes: `change-logs.index`, `change-logs.show`, `change-logs.create`, `change-logs.store`, `change-logs.edit`, `change-logs.update`, `change-logs.destroy`.

- [ ] **Step 1: Create controller**

```php
<?php
// app/Http/Controllers/User/ChangeLogController.php

namespace App\Http\Controllers\User;

use App\Http\Controllers\Controller;
use App\Models\ChangeLog;
use Illuminate\Http\Request;

class ChangeLogController extends Controller
{
    public function index()
    {
        $changeLogs = ChangeLog::published()
            ->orderBy('published_at', 'desc')
            ->paginate(15);

        return view('user.change-logs.index', compact('changeLogs'));
    }

    public function show(ChangeLog $changeLog)
    {
        if (!$changeLog->published_at || $changeLog->published_at->isFuture()) {
            if (!auth()->user()->can('Manage Change Logs')) {
                abort(404);
            }
        }

        return view('user.change-logs.show', compact('changeLog'));
    }

    public function create()
    {
        if (!auth()->user()->can('Manage Change Logs')) {
            abort(403, 'You do not have permission to access this page.');
        }

        return view('user.change-logs.create');
    }

    public function store(Request $request)
    {
        if (!auth()->user()->can('Manage Change Logs')) {
            abort(403, 'You do not have permission to access this page.');
        }

        $validated = $request->validate([
            'version'      => 'required|string|max:50',
            'title'        => 'required|string|max:255',
            'type'         => 'required|in:feature,improvement,bugfix,security',
            'description'  => 'required|string',
            'published_at' => 'nullable|date',
        ]);

        ChangeLog::create([
            'created_by'   => auth()->id(),
            'version'      => $validated['version'],
            'title'        => $validated['title'],
            'type'         => $validated['type'],
            'description'  => $validated['description'],
            'published_at' => $validated['published_at'] ?? now(),
        ]);

        return redirect()->route('change-logs.index')
            ->with('message', 'Change log entry created successfully.');
    }

    public function edit(ChangeLog $changeLog)
    {
        if (!auth()->user()->can('Manage Change Logs')) {
            abort(403, 'You do not have permission to access this page.');
        }

        return view('user.change-logs.edit', compact('changeLog'));
    }

    public function update(Request $request, ChangeLog $changeLog)
    {
        if (!auth()->user()->can('Manage Change Logs')) {
            abort(403, 'You do not have permission to access this page.');
        }

        $validated = $request->validate([
            'version'      => 'required|string|max:50',
            'title'        => 'required|string|max:255',
            'type'         => 'required|in:feature,improvement,bugfix,security',
            'description'  => 'required|string',
            'published_at' => 'nullable|date',
        ]);

        $changeLog->update($validated);

        return redirect()->route('change-logs.index')
            ->with('message', 'Change log entry updated successfully.');
    }

    public function destroy(ChangeLog $changeLog)
    {
        if (!auth()->user()->can('Manage Change Logs')) {
            abort(403, 'You do not have permission to access this page.');
        }

        $changeLog->delete();

        return redirect()->route('change-logs.index')
            ->with('message', 'Change log entry deleted.');
    }
}
```

- [ ] **Step 2: Commit**

```bash
git add app/Http/Controllers/User/ChangeLogController.php
git commit -m "feat: add ChangeLogController"
```

---

## Task 8: Routes

**Files:**
- Modify: `routes/web.php`

**Interfaces:**
- Consumes: All three controllers (Tasks 5, 6, 7).
- Produces named routes:
  - `support-reports.index`, `support-reports.create`, `support-reports.store`, `support-reports.show`
  - `support-reports.manage.index`, `support-reports.manage.show`, `support-reports.manage.update`
  - `change-logs.index`, `change-logs.show`, `change-logs.create`, `change-logs.store`, `change-logs.edit`, `change-logs.update`, `change-logs.destroy`

- [ ] **Step 1: Add use statements at top of web.php**

Open `routes/web.php`. At the top, find the last `use App\Http\Controllers\User\...` line (around line 550). Add after it:

```php
use App\Http\Controllers\User\SupportReportController;
use App\Http\Controllers\User\SupportReportManageController;
use App\Http\Controllers\User\ChangeLogController;
```

- [ ] **Step 2: Add route groups inside the user middleware group**

Inside `Route::prefix('user')->middleware(['user', 'preventBackHistory', 'userActivity', 'member.access', 'agreement.signed'])` group, find the end of the group (before the closing `}`). Add:

```php
    // Support Reports — user
    Route::prefix('support-reports')->name('support-reports.')->group(function () {
        Route::get('/', [SupportReportController::class, 'index'])->name('index');
        Route::get('/create', [SupportReportController::class, 'create'])->name('create');
        Route::post('/', [SupportReportController::class, 'store'])->name('store');
        Route::get('/{supportReport}', [SupportReportController::class, 'show'])->name('show');

        // Management
        Route::prefix('manage')->name('manage.')->group(function () {
            Route::get('/', [SupportReportManageController::class, 'index'])->name('index');
            Route::get('/{supportReport}', [SupportReportManageController::class, 'show'])->name('show');
            Route::put('/{supportReport}', [SupportReportManageController::class, 'update'])->name('update');
        });
    });

    // Change Logs
    Route::prefix('change-logs')->name('change-logs.')->group(function () {
        Route::get('/', [ChangeLogController::class, 'index'])->name('index');
        Route::get('/create', [ChangeLogController::class, 'create'])->name('create');
        Route::post('/', [ChangeLogController::class, 'store'])->name('store');
        Route::get('/{changeLog}/edit', [ChangeLogController::class, 'edit'])->name('edit');
        Route::put('/{changeLog}', [ChangeLogController::class, 'update'])->name('update');
        Route::delete('/{changeLog}', [ChangeLogController::class, 'destroy'])->name('destroy');
        Route::get('/{changeLog}', [ChangeLogController::class, 'show'])->name('show');
    });
```

- [ ] **Step 3: Verify routes registered**

```bash
php artisan route:list --name="support-reports\|change-logs"
```

Expected: 10 routes listed covering all named routes above.

- [ ] **Step 4: Commit**

```bash
git add routes/web.php
git commit -m "feat: add support reports and change logs routes"
```

---

## Task 9: Support Reports Blade Views (User)

**Files:**
- Create: `resources/views/user/support-reports/index.blade.php`
- Create: `resources/views/user/support-reports/create.blade.php`
- Create: `resources/views/user/support-reports/show.blade.php`

**Interfaces:**
- Consumes routes from Task 8. Uses `$reports` (paginated collection of `SupportReport`), `$report` (single `SupportReport`).

- [ ] **Step 1: Create index view (My Reports)**

```blade
{{-- resources/views/user/support-reports/index.blade.php --}}
@extends('user.layouts.master')
@section('title')
    My Support Reports - {{ env('APP_NAME') }}
@endsection
@section('content')
<div class="container-fluid">
    <div class="bg_white_border">
        <div class="row mb-3">
            <div class="col-md-9">
                <h3 class="mb-3">My Support Reports</h3>
            </div>
            <div class="col-md-3 text-end">
                <a href="{{ route('support-reports.create') }}" class="btn btn-primary">
                    <i class="fa-solid fa-plus"></i> Submit New Report
                </a>
            </div>
        </div>

        @if(session('message'))
            <div class="alert alert-success">{{ session('message') }}</div>
        @endif

        <div class="table-responsive">
            <table class="table align-middle bg-white color_body_text">
                <thead class="color_head">
                    <tr>
                        <th>#</th>
                        <th>Subject</th>
                        <th>Status</th>
                        <th>Submitted</th>
                        <th>Action</th>
                    </tr>
                </thead>
                <tbody>
                    @forelse($reports as $report)
                    <tr>
                        <td>{{ $report->id }}</td>
                        <td>{{ $report->subject }}</td>
                        <td><span class="{{ $report->statusBadgeClass() }}">{{ ucfirst(str_replace('_', ' ', $report->status)) }}</span></td>
                        <td>{{ $report->created_at->format('d M Y') }}</td>
                        <td>
                            <a href="{{ route('support-reports.show', $report) }}" class="btn btn-sm btn-outline-primary">View</a>
                        </td>
                    </tr>
                    @empty
                    <tr>
                        <td colspan="5" class="text-center text-muted py-4">You have not submitted any support reports yet.</td>
                    </tr>
                    @endforelse
                </tbody>
            </table>
        </div>
        {{ $reports->links() }}
    </div>
</div>
@endsection
```

- [ ] **Step 2: Create create view (Submit Report)**

```blade
{{-- resources/views/user/support-reports/create.blade.php --}}
@extends('user.layouts.master')
@section('title')
    Submit Support Report - {{ env('APP_NAME') }}
@endsection
@section('content')
<div class="container-fluid">
    <div class="bg_white_border">
        <div class="row mb-3">
            <div class="col-md-12">
                <h3 class="mb-3">Submit Support Report</h3>
            </div>
        </div>

        @if($errors->any())
            <div class="alert alert-danger">
                <ul class="mb-0">@foreach($errors->all() as $e)<li>{{ $e }}</li>@endforeach</ul>
            </div>
        @endif

        <form action="{{ route('support-reports.store') }}" method="POST" enctype="multipart/form-data">
            @csrf
            <div class="mb-3">
                <label for="subject" class="form-label fw-semibold">Subject <span class="text-danger">*</span></label>
                <input type="text" name="subject" id="subject" class="form-control @error('subject') is-invalid @enderror"
                    value="{{ old('subject') }}" placeholder="Briefly describe your issue" required>
                @error('subject')<div class="invalid-feedback">{{ $message }}</div>@enderror
            </div>
            <div class="mb-3">
                <label for="message" class="form-label fw-semibold">Message <span class="text-danger">*</span></label>
                <textarea name="message" id="message" rows="6"
                    class="form-control @error('message') is-invalid @enderror"
                    placeholder="Describe your issue in detail" required>{{ old('message') }}</textarea>
                @error('message')<div class="invalid-feedback">{{ $message }}</div>@enderror
            </div>
            <div class="mb-3">
                <label for="attachment" class="form-label fw-semibold">Attachment <span class="text-muted">(optional, max 5MB — jpg, png, pdf, doc, docx)</span></label>
                <input type="file" name="attachment" id="attachment"
                    class="form-control @error('attachment') is-invalid @enderror"
                    accept=".jpg,.jpeg,.png,.gif,.pdf,.doc,.docx">
                @error('attachment')<div class="invalid-feedback">{{ $message }}</div>@enderror
            </div>
            <div class="d-flex gap-2">
                <button type="submit" class="btn btn-primary">Submit Report</button>
                <a href="{{ route('support-reports.index') }}" class="btn btn-outline-secondary">Cancel</a>
            </div>
        </form>
    </div>
</div>
@endsection
```

- [ ] **Step 3: Create show view (View own report)**

```blade
{{-- resources/views/user/support-reports/show.blade.php --}}
@extends('user.layouts.master')
@section('title')
    Support Report #{{ $report->id }} - {{ env('APP_NAME') }}
@endsection
@section('content')
<div class="container-fluid">
    <div class="bg_white_border">
        <div class="row mb-3">
            <div class="col-md-9">
                <h3 class="mb-0">Support Report #{{ $report->id }}</h3>
                <small class="text-muted">Submitted {{ $report->created_at->format('d M Y, H:i') }}</small>
            </div>
            <div class="col-md-3 text-end">
                <a href="{{ route('support-reports.index') }}" class="btn btn-outline-secondary btn-sm">
                    &larr; Back to My Reports
                </a>
            </div>
        </div>

        <div class="mb-3">
            <span class="{{ $report->statusBadgeClass() }} fs-6">{{ ucfirst(str_replace('_', ' ', $report->status)) }}</span>
        </div>

        <div class="card mb-3">
            <div class="card-body">
                <h5 class="card-title">{{ $report->subject }}</h5>
                <p class="card-text" style="white-space: pre-wrap;">{{ $report->message }}</p>
                @if($report->attachment)
                    <a href="{{ Storage::url($report->attachment) }}" target="_blank" class="btn btn-sm btn-outline-primary mt-2">
                        <i class="fa-solid fa-paperclip"></i> View Attachment
                    </a>
                @endif
            </div>
        </div>

        @if($report->admin_notes)
        <div class="card border-success">
            <div class="card-header bg-success text-white">Response from Support Team</div>
            <div class="card-body">
                <p class="mb-0" style="white-space: pre-wrap;">{{ $report->admin_notes }}</p>
                @if($report->resolved_at)
                    <small class="text-muted">Updated {{ $report->resolved_at->format('d M Y, H:i') }}</small>
                @endif
            </div>
        </div>
        @endif
    </div>
</div>
@endsection
```

- [ ] **Step 4: Commit**

```bash
git add resources/views/user/support-reports/index.blade.php resources/views/user/support-reports/create.blade.php resources/views/user/support-reports/show.blade.php
git commit -m "feat: add support reports user views (index, create, show)"
```

---

## Task 10: Support Reports Blade Views (Management)

**Files:**
- Create: `resources/views/user/support-reports/manage/index.blade.php`
- Create: `resources/views/user/support-reports/manage/show.blade.php`

**Interfaces:**
- Consumes: `$reports` (paginated all SupportReports with user), `$statusFilter`, `$report` (single with user and resolver).

- [ ] **Step 1: Create manage index view (All Reports)**

```blade
{{-- resources/views/user/support-reports/manage/index.blade.php --}}
@extends('user.layouts.master')
@section('title')
    Manage Support Reports - {{ env('APP_NAME') }}
@endsection
@section('content')
<div class="container-fluid">
    <div class="bg_white_border">
        <div class="row mb-3">
            <div class="col-md-9">
                <h3 class="mb-3">Manage Support Reports</h3>
            </div>
            <div class="col-md-3 text-end">
                <a href="{{ route('support-reports.index') }}" class="btn btn-outline-secondary btn-sm">
                    My Reports
                </a>
            </div>
        </div>

        @if(session('message'))
            <div class="alert alert-success">{{ session('message') }}</div>
        @endif

        <form method="GET" class="mb-3 d-flex gap-2 align-items-center">
            <select name="status" class="form-select" style="max-width: 200px;">
                <option value="">All Statuses</option>
                @foreach(['open','in_progress','resolved','closed'] as $s)
                    <option value="{{ $s }}" @selected($statusFilter === $s)>{{ ucfirst(str_replace('_',' ',$s)) }}</option>
                @endforeach
            </select>
            <button type="submit" class="btn btn-primary btn-sm">Filter</button>
            @if($statusFilter)
                <a href="{{ route('support-reports.manage.index') }}" class="btn btn-outline-secondary btn-sm">Clear</a>
            @endif
        </form>

        <div class="table-responsive">
            <table class="table align-middle bg-white color_body_text">
                <thead class="color_head">
                    <tr>
                        <th>#</th>
                        <th>User</th>
                        <th>Subject</th>
                        <th>Status</th>
                        <th>Submitted</th>
                        <th>Action</th>
                    </tr>
                </thead>
                <tbody>
                    @forelse($reports as $report)
                    <tr>
                        <td>{{ $report->id }}</td>
                        <td>{{ $report->user->name ?? '-' }}</td>
                        <td>{{ $report->subject }}</td>
                        <td><span class="{{ $report->statusBadgeClass() }}">{{ ucfirst(str_replace('_', ' ', $report->status)) }}</span></td>
                        <td>{{ $report->created_at->format('d M Y') }}</td>
                        <td>
                            <a href="{{ route('support-reports.manage.show', $report) }}" class="btn btn-sm btn-outline-primary">View & Respond</a>
                        </td>
                    </tr>
                    @empty
                    <tr>
                        <td colspan="6" class="text-center text-muted py-4">No support reports found.</td>
                    </tr>
                    @endforelse
                </tbody>
            </table>
        </div>
        {{ $reports->links() }}
    </div>
</div>
@endsection
```

- [ ] **Step 2: Create manage show view (View & Respond)**

```blade
{{-- resources/views/user/support-reports/manage/show.blade.php --}}
@extends('user.layouts.master')
@section('title')
    Report #{{ $report->id }} — Management - {{ env('APP_NAME') }}
@endsection
@section('content')
<div class="container-fluid">
    <div class="bg_white_border">
        <div class="row mb-3">
            <div class="col-md-9">
                <h3 class="mb-0">Support Report #{{ $report->id }}</h3>
                <small class="text-muted">Submitted by <strong>{{ $report->user->name ?? '-' }}</strong> on {{ $report->created_at->format('d M Y, H:i') }}</small>
            </div>
            <div class="col-md-3 text-end">
                <a href="{{ route('support-reports.manage.index') }}" class="btn btn-outline-secondary btn-sm">&larr; All Reports</a>
            </div>
        </div>

        @if(session('message'))
            <div class="alert alert-success">{{ session('message') }}</div>
        @endif

        <div class="card mb-3">
            <div class="card-body">
                <h5 class="card-title">{{ $report->subject }}</h5>
                <p class="card-text" style="white-space: pre-wrap;">{{ $report->message }}</p>
                @if($report->attachment)
                    <a href="{{ Storage::url($report->attachment) }}" target="_blank" class="btn btn-sm btn-outline-primary">
                        <i class="fa-solid fa-paperclip"></i> View Attachment
                    </a>
                @endif
            </div>
        </div>

        <div class="card">
            <div class="card-header fw-semibold">Update Status & Add Notes</div>
            <div class="card-body">
                <form action="{{ route('support-reports.manage.update', $report) }}" method="POST">
                    @csrf
                    @method('PUT')
                    @if($errors->any())
                        <div class="alert alert-danger">
                            <ul class="mb-0">@foreach($errors->all() as $e)<li>{{ $e }}</li>@endforeach</ul>
                        </div>
                    @endif
                    <div class="mb-3">
                        <label class="form-label fw-semibold">Status</label>
                        <select name="status" class="form-select @error('status') is-invalid @enderror">
                            @foreach(['open','in_progress','resolved','closed'] as $s)
                                <option value="{{ $s }}" @selected($report->status === $s)>{{ ucfirst(str_replace('_',' ',$s)) }}</option>
                            @endforeach
                        </select>
                        @error('status')<div class="invalid-feedback">{{ $message }}</div>@enderror
                    </div>
                    <div class="mb-3">
                        <label class="form-label fw-semibold">Admin Notes <span class="text-muted">(visible to submitter)</span></label>
                        <textarea name="admin_notes" rows="5" class="form-control @error('admin_notes') is-invalid @enderror"
                            placeholder="Add a reply or internal notes...">{{ old('admin_notes', $report->admin_notes) }}</textarea>
                        @error('admin_notes')<div class="invalid-feedback">{{ $message }}</div>@enderror
                    </div>
                    <button type="submit" class="btn btn-primary">Save & Notify User</button>
                </form>
            </div>
        </div>

        @if($report->resolved_at)
        <p class="text-muted mt-2 small">Last updated {{ $report->resolved_at->format('d M Y, H:i') }} by {{ $report->resolver->name ?? '-' }}</p>
        @endif
    </div>
</div>
@endsection
```

- [ ] **Step 3: Commit**

```bash
git add resources/views/user/support-reports/manage/
git commit -m "feat: add support reports management views"
```

---

## Task 11: Change Logs Blade Views

**Files:**
- Create: `resources/views/user/change-logs/index.blade.php`
- Create: `resources/views/user/change-logs/show.blade.php`
- Create: `resources/views/user/change-logs/create.blade.php`
- Create: `resources/views/user/change-logs/edit.blade.php`

**Interfaces:**
- Consumes: `$changeLogs` (paginated), `$changeLog` (single). Routes from Task 8.

- [ ] **Step 1: Create index view**

```blade
{{-- resources/views/user/change-logs/index.blade.php --}}
@extends('user.layouts.master')
@section('title')
    Change Logs - {{ env('APP_NAME') }}
@endsection
@section('content')
<div class="container-fluid">
    <div class="bg_white_border">
        <div class="row mb-3">
            <div class="col-md-9">
                <h3 class="mb-3">Change Logs</h3>
            </div>
            @can('Manage Change Logs')
            <div class="col-md-3 text-end">
                <a href="{{ route('change-logs.create') }}" class="btn btn-primary btn-sm">
                    <i class="fa-solid fa-plus"></i> Add Entry
                </a>
            </div>
            @endcan
        </div>

        @if(session('message'))
            <div class="alert alert-success">{{ session('message') }}</div>
        @endif

        @forelse($changeLogs as $log)
        <div class="card mb-3">
            <div class="card-body">
                <div class="d-flex justify-content-between align-items-start">
                    <div>
                        <span class="badge bg-secondary me-1">{{ $log->version }}</span>
                        <span class="{{ $log->typeBadgeClass() }} me-2">{{ ucfirst($log->type) }}</span>
                        <strong>{{ $log->title }}</strong>
                    </div>
                    <div class="text-muted small text-nowrap ms-3">
                        {{ $log->published_at?->format('d M Y') }}
                    </div>
                </div>
                <p class="mt-2 mb-1 text-muted" style="overflow:hidden;max-height:60px;">{{ Str::limit(strip_tags($log->description), 180) }}</p>
                <div class="d-flex gap-2 mt-2">
                    <a href="{{ route('change-logs.show', $log) }}" class="btn btn-sm btn-outline-primary">Read More</a>
                    @can('Manage Change Logs')
                        <a href="{{ route('change-logs.edit', $log) }}" class="btn btn-sm btn-outline-secondary">Edit</a>
                        <form action="{{ route('change-logs.destroy', $log) }}" method="POST" onsubmit="return confirm('Delete this entry?')">
                            @csrf @method('DELETE')
                            <button class="btn btn-sm btn-outline-danger">Delete</button>
                        </form>
                    @endcan
                </div>
            </div>
        </div>
        @empty
        <div class="text-center text-muted py-5">No change log entries yet.</div>
        @endforelse
        {{ $changeLogs->links() }}
    </div>
</div>
@endsection
```

- [ ] **Step 2: Create show view**

```blade
{{-- resources/views/user/change-logs/show.blade.php --}}
@extends('user.layouts.master')
@section('title')
    {{ $changeLog->title }} - Change Logs - {{ env('APP_NAME') }}
@endsection
@section('content')
<div class="container-fluid">
    <div class="bg_white_border">
        <div class="mb-3">
            <a href="{{ route('change-logs.index') }}" class="btn btn-outline-secondary btn-sm">&larr; Back to Change Logs</a>
        </div>
        <div class="mb-2">
            <span class="badge bg-secondary me-1">{{ $changeLog->version }}</span>
            <span class="{{ $changeLog->typeBadgeClass() }}">{{ ucfirst($changeLog->type) }}</span>
            <span class="text-muted ms-2 small">{{ $changeLog->published_at?->format('d M Y') }}</span>
        </div>
        <h2>{{ $changeLog->title }}</h2>
        <hr>
        <div style="white-space: pre-wrap; line-height: 1.8;">{{ $changeLog->description }}</div>
        <p class="text-muted mt-3 small">Published by {{ $changeLog->author->name ?? '-' }}</p>
    </div>
</div>
@endsection
```

- [ ] **Step 3: Create create view**

```blade
{{-- resources/views/user/change-logs/create.blade.php --}}
@extends('user.layouts.master')
@section('title')
    Add Change Log - {{ env('APP_NAME') }}
@endsection
@section('content')
<div class="container-fluid">
    <div class="bg_white_border">
        <h3 class="mb-3">Add Change Log Entry</h3>

        @if($errors->any())
            <div class="alert alert-danger">
                <ul class="mb-0">@foreach($errors->all() as $e)<li>{{ $e }}</li>@endforeach</ul>
            </div>
        @endif

        <form action="{{ route('change-logs.store') }}" method="POST">
            @csrf
            <div class="row g-3">
                <div class="col-md-3">
                    <label class="form-label fw-semibold">Version <span class="text-danger">*</span></label>
                    <input type="text" name="version" class="form-control @error('version') is-invalid @enderror"
                        value="{{ old('version') }}" placeholder="e.g. v2.4.1" required>
                    @error('version')<div class="invalid-feedback">{{ $message }}</div>@enderror
                </div>
                <div class="col-md-6">
                    <label class="form-label fw-semibold">Title <span class="text-danger">*</span></label>
                    <input type="text" name="title" class="form-control @error('title') is-invalid @enderror"
                        value="{{ old('title') }}" placeholder="Short description of the release" required>
                    @error('title')<div class="invalid-feedback">{{ $message }}</div>@enderror
                </div>
                <div class="col-md-3">
                    <label class="form-label fw-semibold">Type <span class="text-danger">*</span></label>
                    <select name="type" class="form-select @error('type') is-invalid @enderror">
                        @foreach(['feature','improvement','bugfix','security'] as $t)
                            <option value="{{ $t }}" @selected(old('type') === $t)>{{ ucfirst($t) }}</option>
                        @endforeach
                    </select>
                    @error('type')<div class="invalid-feedback">{{ $message }}</div>@enderror
                </div>
                <div class="col-md-12">
                    <label class="form-label fw-semibold">Description <span class="text-danger">*</span></label>
                    <textarea name="description" rows="10" class="form-control @error('description') is-invalid @enderror"
                        required>{{ old('description') }}</textarea>
                    @error('description')<div class="invalid-feedback">{{ $message }}</div>@enderror
                </div>
                <div class="col-md-4">
                    <label class="form-label fw-semibold">Publish Date</label>
                    <input type="datetime-local" name="published_at" class="form-control @error('published_at') is-invalid @enderror"
                        value="{{ old('published_at', now()->format('Y-m-d\TH:i')) }}">
                    <small class="text-muted">Leave blank to publish now.</small>
                    @error('published_at')<div class="invalid-feedback">{{ $message }}</div>@enderror
                </div>
            </div>
            <div class="mt-3 d-flex gap-2">
                <button type="submit" class="btn btn-primary">Save Entry</button>
                <a href="{{ route('change-logs.index') }}" class="btn btn-outline-secondary">Cancel</a>
            </div>
        </form>
    </div>
</div>
@endsection
```

- [ ] **Step 4: Create edit view**

```blade
{{-- resources/views/user/change-logs/edit.blade.php --}}
@extends('user.layouts.master')
@section('title')
    Edit Change Log - {{ env('APP_NAME') }}
@endsection
@section('content')
<div class="container-fluid">
    <div class="bg_white_border">
        <h3 class="mb-3">Edit Change Log Entry</h3>

        @if($errors->any())
            <div class="alert alert-danger">
                <ul class="mb-0">@foreach($errors->all() as $e)<li>{{ $e }}</li>@endforeach</ul>
            </div>
        @endif

        <form action="{{ route('change-logs.update', $changeLog) }}" method="POST">
            @csrf @method('PUT')
            <div class="row g-3">
                <div class="col-md-3">
                    <label class="form-label fw-semibold">Version <span class="text-danger">*</span></label>
                    <input type="text" name="version" class="form-control @error('version') is-invalid @enderror"
                        value="{{ old('version', $changeLog->version) }}" required>
                    @error('version')<div class="invalid-feedback">{{ $message }}</div>@enderror
                </div>
                <div class="col-md-6">
                    <label class="form-label fw-semibold">Title <span class="text-danger">*</span></label>
                    <input type="text" name="title" class="form-control @error('title') is-invalid @enderror"
                        value="{{ old('title', $changeLog->title) }}" required>
                    @error('title')<div class="invalid-feedback">{{ $message }}</div>@enderror
                </div>
                <div class="col-md-3">
                    <label class="form-label fw-semibold">Type <span class="text-danger">*</span></label>
                    <select name="type" class="form-select @error('type') is-invalid @enderror">
                        @foreach(['feature','improvement','bugfix','security'] as $t)
                            <option value="{{ $t }}" @selected(old('type', $changeLog->type) === $t)>{{ ucfirst($t) }}</option>
                        @endforeach
                    </select>
                    @error('type')<div class="invalid-feedback">{{ $message }}</div>@enderror
                </div>
                <div class="col-md-12">
                    <label class="form-label fw-semibold">Description <span class="text-danger">*</span></label>
                    <textarea name="description" rows="10" class="form-control @error('description') is-invalid @enderror"
                        required>{{ old('description', $changeLog->description) }}</textarea>
                    @error('description')<div class="invalid-feedback">{{ $message }}</div>@enderror
                </div>
                <div class="col-md-4">
                    <label class="form-label fw-semibold">Publish Date</label>
                    <input type="datetime-local" name="published_at" class="form-control @error('published_at') is-invalid @enderror"
                        value="{{ old('published_at', $changeLog->published_at?->format('Y-m-d\TH:i')) }}">
                    @error('published_at')<div class="invalid-feedback">{{ $message }}</div>@enderror
                </div>
            </div>
            <div class="mt-3 d-flex gap-2">
                <button type="submit" class="btn btn-primary">Update Entry</button>
                <a href="{{ route('change-logs.index') }}" class="btn btn-outline-secondary">Cancel</a>
            </div>
        </form>
    </div>
</div>
@endsection
```

- [ ] **Step 5: Commit**

```bash
git add resources/views/user/change-logs/
git commit -m "feat: add change logs blade views (index, show, create, edit)"
```

---

## Task 12: Sidebar Entries

**Files:**
- Modify: `resources/views/user/includes/sidebar.blade.php`

**Interfaces:**
- Consumes routes: `support-reports.index`, `support-reports.manage.index`, `change-logs.index`.

- [ ] **Step 1: Find a good insertion point in the sidebar**

Open `resources/views/user/includes/sidebar.blade.php`. Find the block for **Strategy** (around the line with `route('strategy.index')`). Insert the two new sidebar entries just before or after Strategy — between Strategy and Policy & Guidance is a good location.

- [ ] **Step 2: Add Support Reports sidebar entry**

Add this block at the chosen location:

```blade
{{-- Support Reports --}}
<li class="sidebar-item">
    <a class="sidebar-link {{ Request::is('user/support-reports*') ? 'active' : '' }}"
       href="{{ route('support-reports.index') }}" aria-expanded="false">
        <span>
            <img src="{{ asset('user_assets/images/ICON/support_reports.png') }}"
                 onerror="this.src='{{ asset('user_assets/images/ICON/policy_and_guidance.png') }}'" alt="">
        </span>
        <span class="hide-menu">{{ Helper::getMenuName('support_reports', 'Support Reports') }}</span>
    </a>
    @can('Manage Support Reports')
    <ul class="collapse first-level {{ Request::is('user/support-reports/manage*') ? 'show' : '' }}">
        <li class="sidebar-item">
            <a class="sidebar-link {{ Request::is('user/support-reports/manage*') ? 'active' : '' }}"
               href="{{ route('support-reports.manage.index') }}">
                <span class="hide-menu">Manage Reports</span>
            </a>
        </li>
    </ul>
    @endcan
</li>
```

- [ ] **Step 3: Add Change Logs sidebar entry**

Add immediately after the Support Reports block:

```blade
{{-- Change Logs --}}
<li class="sidebar-item">
    <a class="sidebar-link {{ Request::is('user/change-logs*') ? 'active' : '' }}"
       href="{{ route('change-logs.index') }}" aria-expanded="false">
        <span>
            <img src="{{ asset('user_assets/images/ICON/change_logs.png') }}"
                 onerror="this.src='{{ asset('user_assets/images/ICON/policy_and_guidance.png') }}'" alt="">
        </span>
        <span class="hide-menu">{{ Helper::getMenuName('change_logs', 'Change Logs') }}</span>
    </a>
</li>
```

- [ ] **Step 4: Commit**

```bash
git add resources/views/user/includes/sidebar.blade.php
git commit -m "feat: add Support Reports and Change Logs sidebar entries"
```

---

## Task 13: PMA Documentation Update

**Files:**
- Modify: `docs/pma/user-pma.md`
- Create: `docs/pma/support-reports.md`
- Create: `docs/pma/change-logs.md`

- [ ] **Step 1: Update user-pma.md — Major Menu Groups section**

Open `docs/pma/user-pma.md`. Find the `### Major menu groups` numbered list. Add two new items at the end of the list:

```markdown
10. **Support Reports** — All users: submit/view own reports. `Manage Support Reports` permission: view all reports, update status, add admin notes. Email notifications sent on submit and status update.
11. **Change Logs** — All users: read published release notes. `Manage Change Logs` permission: create, edit, delete entries.
```

Also bump the frontmatter `updated: 2026-07-27`.

- [ ] **Step 2: Create support-reports.md**

```markdown
---
title: Support Reports
updated: 2026-07-27
status: ready
sidebar_key: support_reports
---

# Support Reports

## Overview

Allows all authenticated PMA users to submit support reports and track their status. Users with the `Manage Support Reports` permission can view all reports, update status, and add admin notes.

## User Features (All Users)

- **My Reports** `/user/support-reports` — paginated list of own submitted reports with status badges.
- **Submit Report** `/user/support-reports/create` — form with Subject, Message, and optional attachment (jpg/png/gif/pdf/doc/docx, max 5MB).
- **View Report** `/user/support-reports/{id}` — read-only detail including admin notes and attachment download link. Authorization: own reports only.

## Management Features

Gated by `Manage Support Reports` permission.

- **All Reports** `/user/support-reports/manage` — all users' reports, filterable by status.
- **View & Respond** `/user/support-reports/manage/{id}` — update status (open/in_progress/resolved/closed) and add admin notes. On save, email sent to submitter.

## Status Flow

`open` → `in_progress` → `resolved` → `closed`

Reports cannot be deleted — only moved to `closed` status.

## Email Notifications

- **On new submission:** email sent to all users with `Manage Support Reports` permission.
- **On status update:** email sent to the original submitter.

## Permissions

| Permission | Description |
|---|---|
| `Manage Support Reports` | Access management view, update status, add notes |
```

- [ ] **Step 3: Create change-logs.md**

```markdown
---
title: Change Logs
updated: 2026-07-27
status: ready
sidebar_key: change_logs
---

# Change Logs

## Overview

Platform release notes / update history. All authenticated PMA users can read published entries. Users with the `Manage Change Logs` permission can create, edit, and delete entries.

## User Features (All Users)

- **Change Logs List** `/user/change-logs` — reverse-chronological list of all published entries (where `published_at <= now()`). Shows version, type badge, title, date.
- **Change Log Detail** `/user/change-logs/{id}` — full description.

## Management Features

Gated by `Manage Change Logs` permission.

- **Create** `/user/change-logs/create` — form with Version, Title, Type, Description, Publish Date.
- **Edit** `/user/change-logs/{id}/edit` — update any field.
- **Delete** — delete button on list and edit pages.

Unpublished (future-dated) entries are only visible to users with `Manage Change Logs`.

## Entry Types

| Type | Badge Color | Description |
|---|---|---|
| `feature` | Blue | New feature added |
| `improvement` | Cyan | Enhancement to existing feature |
| `bugfix` | Yellow | Bug fix |
| `security` | Red | Security fix |

## Permissions

| Permission | Description |
|---|---|
| `Manage Change Logs` | Create, edit, delete change log entries |
```

- [ ] **Step 4: Commit**

```bash
git add docs/pma/user-pma.md docs/pma/support-reports.md docs/pma/change-logs.md
git commit -m "docs: update PMA documentation for Support Reports and Change Logs"
```

---

## Task 14: Smoke Test

- [ ] **Step 1: Clear caches**

```bash
php artisan config:clear && php artisan route:clear && php artisan view:clear
```

- [ ] **Step 2: Verify all routes**

```bash
php artisan route:list --name="support-reports\|change-logs"
```

Expected: 10 routes listed.

- [ ] **Step 3: Check for syntax errors in PHP files**

```bash
php -l app/Http/Controllers/User/SupportReportController.php
php -l app/Http/Controllers/User/SupportReportManageController.php
php -l app/Http/Controllers/User/ChangeLogController.php
php -l app/Models/SupportReport.php
php -l app/Models/ChangeLog.php
php -l app/Mail/SupportReportSubmittedMail.php
php -l app/Mail/SupportReportStatusUpdatedMail.php
```

Expected: `No syntax errors detected` for each file.

- [ ] **Step 4: Manual browser test (logged-in user)**

1. Navigate to `/user/support-reports` — My Reports page loads.
2. Navigate to `/user/support-reports/create` — form loads with Subject, Message, Attachment fields.
3. Submit a report — redirected to My Reports with success message.
4. Click "View" on a report — detail page shows correctly.
5. Navigate to `/user/change-logs` — list page loads (empty or with entries).

- [ ] **Step 5: Manual browser test (user with Manage Support Reports permission)**

1. Assign `Manage Support Reports` to a role in Role Permissions.
2. Log in as that user, navigate to `/user/support-reports/manage` — All Reports page loads.
3. Click "View & Respond" on a report — form with status dropdown and admin notes loads.
4. Submit an update — success message, submitter email sent.

- [ ] **Step 6: Manual browser test (user with Manage Change Logs permission)**

1. Assign `Manage Change Logs` to a role.
2. Log in, navigate to `/user/change-logs` — "Add Entry" button visible.
3. Create a change log entry — appears in list.
4. Edit it — changes saved.
5. Delete it — removed from list.

- [ ] **Step 7: Final commit**

```bash
git add .
git status
git commit -m "feat: Support Reports and Change Logs — complete implementation"
```

---

## Spec Coverage Check

| Spec requirement | Task |
|---|---|
| `support_reports` DB table | Task 1 |
| `change_logs` DB table | Task 1 |
| `Manage Support Reports` permission | Task 2 |
| `Manage Change Logs` permission | Task 2 |
| Sidebar menu keys seeded | Task 2 |
| SupportReport model + scopes | Task 3 |
| ChangeLog model + scopes | Task 3 |
| Email on new report submitted | Task 4 + 5 |
| Email on status update | Task 4 + 6 |
| User: submit report (Subject + Message + optional attachment) | Task 5 |
| User: view own reports only | Task 5 |
| Management: all reports, filter by status | Task 6 |
| Management: update status + admin notes | Task 6 |
| Change Logs: user list + detail view | Task 7 |
| Change Logs: CRUD for managers | Task 7 |
| Routes (all 10 named routes) | Task 8 |
| User views (index, create, show) | Task 9 |
| Management views (manage index + show) | Task 10 |
| Change Log views (all 4) | Task 11 |
| Sidebar entries (Support Reports + Change Logs) | Task 12 |
| PMA docs updated | Task 13 |
