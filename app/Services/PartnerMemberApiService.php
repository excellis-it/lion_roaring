<?php

namespace App\Services;

use App\Http\Controllers\User\PartnerController as WebPartnerController;
use App\Mail\RegistrationMail;
use App\Models\Country;
use App\Models\Ecclesia;
use App\Models\MembershipTier;
use App\Models\User;
use App\Models\UserSubscription;
use App\Models\UserType;
use App\Models\UserTypePermission;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Mail;
use Illuminate\Support\Str;
use Spatie\Permission\Models\Permission;
use Spatie\Permission\Models\Role;

class PartnerMemberApiService
{
    /**
     * @return array{prefix: string, suffix: string}
     */
    public function resolveLionRoaringIdParts(?User $user = null): array
    {
        $todayCount = User::withTrashed()->whereDate('created_at', now()->toDateString())->count();
        $sequence = str_pad($todayCount + 1, 4, '0', STR_PAD_LEFT);
        $datePart = now()->format('mdY');
        $generatedPrefix = 'LR' . $sequence . $datePart;

        $currentPrefix = $generatedPrefix;
        $currentSuffix = '';

        if ($user) {
            $existingId = (string) ($user->lion_roaring_id ?? '');
            if (strlen($existingId) >= 14 && substr($existingId, 0, 2) === 'LR') {
                $currentPrefix = substr($existingId, 0, 14);
                $currentSuffix = substr($existingId, 14);
            } elseif ($existingId !== '') {
                $currentSuffix = $existingId;
            }
        }

        return [
            'prefix' => $currentPrefix,
            'suffix' => $currentSuffix,
        ];
    }

    /**
     * Form metadata for create/edit member screens (slim payload).
     */
    public function buildFormData(): array
    {
        $authUser = Auth::user();
        $isSuperAdmin = $authUser->hasNewRole('SUPER ADMIN');
        $authUserType = $authUser->user_type;
        $authUserCountry = $authUser->country;

        if ($isSuperAdmin || $authUserType === 'Global') {
            $roleTemplates = UserType::whereIn('type', [2, 3])->orderBy('name')->get();
            $ecclesias = Ecclesia::orderBy('id', 'asc')->get(['id', 'name', 'country']);
            $allowedUserTypes = $isSuperAdmin ? ['Global', 'Regional', 'G_R'] : ['Global', 'G_R'];
        } else {
            $roleTemplates = UserType::whereIn('type', [2, 3])->orderBy('name')->get();
            $allowedUserTypes = ['Regional', 'G_R'];
            if ($authUser->isEcclesiaUser()) {
                $ecclesias = collect($authUser->ecclesia_access)->map(fn ($e) => [
                    'id' => $e->id,
                    'name' => $e->name,
                    'country' => $e->country ?? null,
                ]);
            } else {
                $ecclesias = Ecclesia::where('country', $authUserCountry)
                    ->orderBy('name', 'asc')
                    ->get(['id', 'name', 'country']);
            }
        }

        if (!$isSuperAdmin && $authUserType === 'Regional') {
            $countries = Country::where('id', $authUserCountry)
                ->where('code', '!=', 'GL')
                ->orderBy('name', 'asc')
                ->get(['id', 'name', 'code']);
        } else {
            $countries = Country::orderBy('name', 'asc')
                ->where('code', '!=', 'GL')
                ->get(['id', 'name', 'code']);
        }

        $roles = $roleTemplates->map(function (UserType $role) {
            $permissions = UserTypePermission::where('user_type_id', $role->id)
                ->join('permissions', 'user_type_permissions.permission_id', '=', 'permissions.id')
                ->select('permissions.id', 'permissions.name')
                ->get();

            return [
                'id' => $role->id,
                'name' => $role->name,
                'type' => $role->type,
                'is_ecclesia' => (int) ($role->is_ecclesia ?? 0),
                'is_admin' => (int) ($role->is_admin ?? 0),
                'permissions' => $permissions->map(fn ($p) => [
                    'id' => $p->id,
                    'name' => $p->name,
                ])->values()->all(),
            ];
        })->values()->all();

        $idParts = $this->resolveLionRoaringIdParts();

        $allPermissions = Permission::all();
        $permData = app(WebPartnerController::class)->permissionsArray($allPermissions);

        return [
            'status' => true,
            'generated_id_part' => $idParts['prefix'],
            'allowed_user_types' => $allowedUserTypes,
            'auth_user_type' => $authUserType,
            'auth_user_country_id' => $authUserCountry,
            'country_locked' => !$isSuperAdmin && $authUserType === 'Regional',
            'is_super_admin' => $isSuperAdmin,
            'roles' => $roles,
            'ecclesias' => $this->formatEcclesiasList($ecclesias),
            'countries' => $countries,
            'membership_tiers' => MembershipTier::orderBy('name')
                ->get(['id', 'name', 'cost', 'duration_months', 'pricing_type', 'life_force_energy_tokens']),
            'categorized_permissions' => $this->formatCategorizedPermissions(
                $permData['categorizedPermissions'],
                $permData['allPermsArray'],
                $allPermissions
            ),
        ];
    }

    /**
     * Ecclesias for partner form (matches web get.ecclesias).
     */
    public function listEcclesiasForPartner(?string $countryId, ?string $userType): array
    {
        $query = Ecclesia::with('countryName')->orderBy('name', 'asc');

        if (in_array($userType, ['Regional', 'G_R'], true) && $countryId) {
            $query->where('country', $countryId);
        }

        return $query->get()->map(fn (Ecclesia $e) => [
            'id' => $e->id,
            'name' => $e->name,
            'country' => $e->country,
            'country_name' => $e->countryName?->name,
        ])->values()->all();
    }

    /**
     * @param  \Illuminate\Support\Collection|array  $ecclesias
     */
    private function formatEcclesiasList($ecclesias): array
    {
        $collection = $ecclesias instanceof \Illuminate\Support\Collection
            ? $ecclesias
            : collect($ecclesias);

        return $collection->map(function ($e) {
            if (is_array($e)) {
                return $e;
            }

            return [
                'id' => $e->id,
                'name' => $e->name,
                'country' => $e->country ?? null,
                'country_name' => $e->countryName?->name ?? null,
            ];
        })->values()->all();
    }

    /**
     * Flatten categorized permissions for mobile UI.
     */
    private function formatCategorizedPermissions(
        array $categorizedPermissions,
        array $allPermsArray,
        $allPermissions
    ): array {
        $result = [];

        foreach ($categorizedPermissions as $mainCategory => $subCategories) {
            $subList = [];
            foreach ($subCategories as $subCategory => $permissionNames) {
                $available = array_values(array_intersect($permissionNames, $allPermsArray));
                if (empty($available)) {
                    continue;
                }
                $subList[] = [
                    'name' => $subCategory,
                    'permissions' => collect($available)->map(function ($permName) use ($allPermissions) {
                        $perm = $allPermissions->firstWhere('name', $permName);

                        return [
                            'id' => $perm?->id ?? 0,
                            'name' => $permName,
                        ];
                    })->values()->all(),
                ];
            }
            if (!empty($subList)) {
                $result[] = [
                    'category' => $mainCategory,
                    'subcategories' => $subList,
                ];
            }
        }

        return $result;
    }

    /**
     * Partner record for edit screen (no duplicate form metadata).
     */
    public function buildPartnerEditPayload(User $partner): array
    {
        $partner->load([
            'ecclesia:id,name',
            'countries:id,name',
            'states:id,name',
            'userLastSubscription:id,user_id,plan_id,subscription_name,subscription_expire_date',
            'userRegisterAgreement:id,user_id,pdf_path,signer_name,country_code,agreement_title_snapshot',
        ]);

        $idParts = $this->resolveLionRoaringIdParts($partner);
        $roleTemplate = $partner->user_type_id
            ? UserType::find($partner->user_type_id)
            : null;

        $manageEcclesia = [];
        if (!empty($partner->manage_ecclesia)) {
            $manageEcclesia = array_values(array_filter(array_map(
                'intval',
                explode(',', (string) $partner->manage_ecclesia)
            )));
        }

        $baseRoleNames = UserType::pluck('name')->toArray();
        $customRole = $partner->roles->first(fn ($role) => !in_array($role->name, $baseRoleNames, true));
        $currentPermissions = $customRole
            ? $customRole->permissions->pluck('name')->values()->all()
            : $partner->getAllPermissions()->pluck('name')->values()->all();

        return [
            'status' => true,
            'partner' => $this->formatPartner($partner),
            'role_template' => $roleTemplate?->name,
            'role_template_id' => $roleTemplate?->id,
            'generated_id_part' => $idParts['prefix'],
            'lion_roaring_id_suffix' => $idParts['suffix'],
            'manage_ecclesia' => $manageEcclesia,
            'current_permissions' => $currentPermissions,
            'membership_tier_id' => $partner->userLastSubscription?->plan_id,
        ];
    }

    public function formatPartner(User $partner): array
    {
        $agreement = $partner->relationLoaded('userRegisterAgreement')
            ? $partner->userRegisterAgreement
            : null;

        return [
            'id' => $partner->id,
            'user_name' => $partner->user_name,
            'first_name' => $partner->first_name,
            'middle_name' => $partner->middle_name,
            'last_name' => $partner->last_name,
            'full_name' => $partner->full_name,
            'email' => $partner->email,
            'phone' => $partner->phone,
            'phone_country_code_name' => $partner->phone_country_code_name,
            'profile_picture' => $partner->profile_picture,
            'address' => $partner->address,
            'address2' => $partner->address2,
            'city' => $partner->city,
            'state' => $partner->state,
            'country' => $partner->country,
            'zip' => $partner->zip,
            'status' => $partner->status,
            'signature' => $partner->signature,
            'created_at' => $partner->created_at,
            'updated_at' => $partner->updated_at,
            'ecclesia_id' => $partner->ecclesia_id,
            'user_type' => $partner->user_type,
            'user_type_id' => $partner->user_type_id,
            'lion_roaring_id' => $partner->lion_roaring_id,
            'roar_id' => $partner->roar_id,
            'is_ecclesia_admin' => (int) ($partner->is_ecclesia_admin ?? 0),
            'manage_ecclesia' => $partner->manage_ecclesia,
            'ecclesia' => $partner->ecclesia ? [
                'id' => $partner->ecclesia->id,
                'name' => $partner->ecclesia->name,
            ] : null,
            'countries' => $partner->countries ? [
                'id' => $partner->countries->id,
                'name' => $partner->countries->name,
            ] : null,
            'states' => $partner->states ? [
                'id' => $partner->states->id,
                'name' => $partner->states->name,
            ] : null,
            'userRegisterAgreement' => $agreement ? [
                'pdf_path' => $agreement->pdf_path,
                'signer_name' => $agreement->signer_name,
                'country_code' => $agreement->country_code,
                'agreement_title_snapshot' => $agreement->agreement_title_snapshot,
            ] : null,
        ];
    }

    /**
     * @return array{0: bool, 1: string, 2: int}
     */
    public function store(Request $request): array
    {
        $rules = [
            'user_name' => 'required|unique:users',
            'lion_roaring_id_suffix' => 'required|digits:4',
            'generated_id_part' => 'required|string',
            'roar_id' => 'nullable|string|max:255',
            'ecclesia_id' => 'nullable|exists:ecclesias,id',
            'role' => 'required',
            'first_name' => 'required',
            'last_name' => 'required',
            'middle_name' => 'nullable',
            'email' => 'required|unique:users|regex:/^([a-z0-9\+_\-]+)(\.[a-z0-9\+_\-]+)*@([a-z0-9\-]+\.)+[a-z]{2,6}$/ix',
            'password' => ['required', 'string', 'regex:/^(?=.*[@$%&])[^\s]{8,}$/'],
            'confirm_password' => 'required|min:8|same:password',
            'address' => 'required',
            'country' => 'required',
            'state' => 'required',
            'city' => 'required',
            'zip' => 'required',
            'address2' => 'nullable',
            'phone' => 'required',
            'user_type' => 'required',
        ];

        if ($request->role === 'MEMBER_SOVEREIGN') {
            $rules['membership_tier_id'] = 'required|exists:membership_tiers,id';
        } else {
            $rules['permissions'] = 'nullable|array';
        }

        $validator = validator($request->all(), $rules, [
            'password.regex' => 'The password must be at least 8 characters long and include at least one special character from @$%&.',
        ]);

        if ($validator->fails()) {
            return [false, $validator->errors()->first(), 201];
        }

        $fullLionRoaringId = $request->generated_id_part . $request->lion_roaring_id_suffix;
        if (User::where('lion_roaring_id', $fullLionRoaringId)->exists()) {
            return [false, 'This Lion Roaring ID already exists.', 201];
        }

        $authUser = Auth::user();
        if (!$authUser->hasNewRole('SUPER ADMIN')) {
            if ($request->user_type !== $authUser->user_type) {
                return [false, 'You are not authorized to create partners of this type.', 201];
            }
            if ($authUser->user_type === 'Regional' && (string) $request->country !== (string) $authUser->country) {
                return [false, 'You are not authorized to create partners in this country.', 201];
            }
        }

        $theRole = UserType::where('name', $request->role)->first();
        if (!$theRole) {
            return [false, 'Invalid role selected.', 201];
        }

        $isEcclesiaAdmin = 0;
        if ((int) ($theRole->is_ecclesia ?? 0) === 1) {
            $isEcclesiaAdmin = 1;
            if (empty($request->manage_ecclesia)) {
                return [false, 'Required - House Of ECCLESIA if Role is an ECCLESIA.', 201];
            }
        }

        $permissions = $this->resolvePermissionsForRequest($request, $theRole);
        if ($theRole->name !== 'MEMBER_SOVEREIGN' && empty($permissions)) {
            return [false, 'At least one permission is required.', 201];
        }

        $uniqueNumber = rand(1000, 9999);
        $lrEmail = strtolower(trim($request->first_name))
            . strtolower(trim((string) $request->middle_name))
            . strtolower(trim($request->last_name))
            . $uniqueNumber . '@lionroaring.us';

        $slug = $this->uniqueRoleSlug($request->user_name);
        $newRole = Role::create([
            'name' => $slug,
            'type' => $theRole->type ?? 2,
            'is_ecclesia' => $theRole->is_ecclesia ?? 0,
            'guard_name' => 'web',
        ]);
        $newRole->syncPermissions($permissions);
        app()->make(\Spatie\Permission\PermissionRegistrar::class)->forgetCachedPermissions();

        $userType = $request->user_type;
        if ((int) ($theRole->is_admin ?? 0) === 1) {
            $userType = 'G_R';
        }

        $data = new User();
        $data->created_id = Auth::id();
        $data->user_name = $request->user_name;
        $data->lion_roaring_id = $fullLionRoaringId;
        $data->roar_id = $request->roar_id;
        $data->first_name = $request->first_name;
        $data->last_name = $request->last_name;
        $data->middle_name = $request->middle_name;
        $data->personal_email = str_replace(' ', '', $lrEmail);
        $data->email = $request->email;
        $data->user_type = $userType;
        $data->user_type_id = $theRole->id;
        $data->password = bcrypt($request->password);
        $data->address = $request->address;
        $data->country = $request->country;
        $data->state = $request->state;
        $data->city = $request->city;
        $data->zip = $request->zip;
        $data->address2 = $request->address2;
        $data->ecclesia_id = $request->ecclesia_id;
        $data->is_ecclesia_admin = $isEcclesiaAdmin;
        $data->phone = $this->formatPhone($request);
        $data->phone_country_code_name = $request->phone_country_code_name;
        $data->status = 1;
        $data->is_accept = 1;
        $data->manage_ecclesia = $request->has('manage_ecclesia')
            ? implode(',', (array) $request->manage_ecclesia)
            : null;
        $data->save();
        $data->assignRole($newRole->name);

        if ($theRole->name === 'MEMBER_SOVEREIGN' && $request->filled('membership_tier_id')) {
            $this->syncMembershipTier($data, (int) $request->membership_tier_id);
        }

        Mail::to($request->email)->send(new RegistrationMail([
            'name' => $request->first_name . ' ' . $request->last_name,
            'email' => $request->email,
            'password' => $request->password,
            'type' => ucfirst(strtolower($request->role)),
        ]));

        return [true, 'Customer created successfully.', 200];
    }

    /**
     * @return array{0: bool, 1: string, 2: int}
     */
    public function update(Request $request, int $id): array
    {
        $rules = [
            'role' => 'required',
            'first_name' => 'required',
            'last_name' => 'required',
            'middle_name' => 'nullable',
            'lion_roaring_id_suffix' => 'required|digits:4',
            'generated_id_part' => 'required|string',
            'roar_id' => 'nullable|string|max:255',
            'email' => 'required|regex:/^([a-z0-9\+_\-]+)(\.[a-z0-9\+_\-]+)*@([a-z0-9\-]+\.)+[a-z]{2,6}$/ix|unique:users,email,' . $id,
            'user_type' => 'required',
            'address' => 'required',
            'phone' => 'required',
            'ecclesia_id' => 'nullable|exists:ecclesias,id',
            'country' => 'required',
            'state' => 'required',
            'city' => 'required',
            'zip' => 'required',
            'address2' => 'nullable',
            'password' => ['nullable', 'string', 'regex:/^(?=.*[@$%&])[^\s]{8,}$/'],
            'confirm_password' => 'nullable|min:8|same:password',
        ];

        if ($request->role === 'MEMBER_SOVEREIGN') {
            $rules['membership_tier_id'] = 'required|exists:membership_tiers,id';
        } else {
            $rules['permissions'] = 'nullable|array';
        }

        $validator = validator($request->all(), $rules, [
            'password.regex' => 'The password must be at least 8 characters long and include at least one special character from @$%&.',
        ]);

        if ($validator->fails()) {
            return [false, $validator->errors()->first(), 201];
        }

        $fullLionRoaringId = $request->generated_id_part . $request->lion_roaring_id_suffix;
        if (User::where('lion_roaring_id', $fullLionRoaringId)->where('id', '!=', $id)->exists()) {
            return [false, 'This Lion Roaring ID already exists.', 201];
        }

        $authUser = Auth::user();
        if (!$authUser->hasNewRole('SUPER ADMIN')) {
            if ($request->user_type !== $authUser->user_type) {
                return [false, 'You are not authorized to edit partners to this type.', 201];
            }
            if ($authUser->user_type === 'Regional' && (string) $request->country !== (string) $authUser->country) {
                return [false, 'You are not authorized to edit partners in this country.', 201];
            }
        }

        $theRole = UserType::where('name', $request->role)->first();
        if (!$theRole) {
            return [false, 'Invalid role selected.', 201];
        }

        $isEcclesiaAdmin = 0;
        if ((int) ($theRole->is_ecclesia ?? 0) === 1) {
            $isEcclesiaAdmin = 1;
            if (empty($request->manage_ecclesia)) {
                return [false, 'Required - House Of ECCLESIA if Role is an ECCLESIA.', 201];
            }
        }

        $permissions = $this->resolvePermissionsForRequest($request, $theRole);
        if ($theRole->name !== 'MEMBER_SOVEREIGN' && empty($permissions)) {
            return [false, 'At least one permission is required.', 201];
        }

        $data = User::findOrFail($id);
        $data->first_name = $request->first_name;
        $data->last_name = $request->last_name;
        $data->middle_name = $request->middle_name;
        $data->lion_roaring_id = $fullLionRoaringId;
        $data->roar_id = $request->roar_id;
        $data->email = $request->email;

        $userType = $request->user_type;
        if ((int) ($theRole->is_admin ?? 0) === 1) {
            $userType = 'G_R';
        }

        $data->user_type = $userType;
        $data->user_type_id = $theRole->id;
        $data->address = $request->address;
        $data->country = $request->country;
        $data->state = $request->state;
        $data->city = $request->city;
        $data->zip = $request->zip;
        $data->address2 = $request->address2;
        if ($isEcclesiaAdmin === 1) {
            $data->ecclesia_id = null;
        } else {
            $data->ecclesia_id = $request->ecclesia_id;
        }
        $data->is_ecclesia_admin = $isEcclesiaAdmin;
        $data->phone = $this->formatPhone($request);
        $data->phone_country_code_name = $request->phone_country_code_name;
        if ($request->filled('password')) {
            $data->password = bcrypt($request->password);
        }
        $data->manage_ecclesia = $request->has('manage_ecclesia')
            ? implode(',', (array) $request->manage_ecclesia)
            : null;
        $data->save();

        $userRole = $this->resolveOrCreateCustomRole($data, $theRole);
        if ($theRole->name === 'MEMBER_SOVEREIGN' && $request->filled('membership_tier_id')) {
            $tier = MembershipTier::find($request->membership_tier_id);
            if ($tier && !empty($tier->permissions)) {
                $permissions = array_filter(array_map('trim', explode(',', $tier->permissions)));
            }
        }
        $userRole->syncPermissions($permissions);
        $data->syncRoles([$userRole->name]);

        $directPerms = $data->getDirectPermissions()->pluck('name')->toArray();
        if (!empty($directPerms)) {
            $data->revokePermissionTo($directPerms);
        }

        app()->make(\Spatie\Permission\PermissionRegistrar::class)->forgetCachedPermissions();
        $data->forgetCachedPermissions();

        if ($theRole->name === 'MEMBER_SOVEREIGN' && $request->filled('membership_tier_id')) {
            $this->syncMembershipTier($data, (int) $request->membership_tier_id, true);
        }

        return [true, 'Member updated successfully.', 200];
    }

    /**
     * @return list<string>
     */
    private function resolvePermissionsForRequest(Request $request, UserType $theRole): array
    {
        if ($theRole->name === 'MEMBER_SOVEREIGN' && $request->filled('membership_tier_id')) {
            $tier = MembershipTier::find($request->membership_tier_id);
            if ($tier && !empty($tier->permissions)) {
                return array_values(array_filter(array_map('trim', explode(',', $tier->permissions))));
            }

            return [];
        }

        if ($request->has('permissions') && is_array($request->permissions) && count($request->permissions) > 0) {
            return array_values(array_filter($request->permissions));
        }

        return UserTypePermission::where('user_type_id', $theRole->id)
            ->join('permissions', 'user_type_permissions.permission_id', '=', 'permissions.id')
            ->pluck('permissions.name')
            ->values()
            ->all();
    }

    private function uniqueRoleSlug(string $userName): string
    {
        $slug = Str::slug($userName);
        $originalSlug = $slug;
        $counter = 1;
        while (Role::where('name', $slug)->exists()) {
            $slug = $originalSlug . '-' . $counter;
            $counter++;
        }

        return $slug;
    }

    private function resolveOrCreateCustomRole(User $data, UserType $theRole): Role
    {
        $baseRoleNames = UserType::pluck('name')->toArray();
        $userRole = null;
        foreach ($data->roles as $role) {
            if (!in_array($role->name, $baseRoleNames, true)) {
                $userRole = $role;
                break;
            }
        }

        if (!$userRole) {
            $slug = $this->uniqueRoleSlug($data->user_name);
            $userRole = Role::create([
                'name' => $slug,
                'type' => $theRole->type ?? 2,
                'is_ecclesia' => $theRole->is_ecclesia ?? 0,
                'guard_name' => 'web',
            ]);
        } else {
            $userRole->type = $theRole->type ?? 2;
            $userRole->is_ecclesia = $theRole->is_ecclesia ?? 0;
            $userRole->save();
        }

        return $userRole;
    }

    private function syncMembershipTier(User $user, int $tierId, bool $update = false): void
    {
        $tier = MembershipTier::find($tierId);
        if (!$tier) {
            return;
        }

        if ($update) {
            $sub = UserSubscription::where('user_id', $user->id)->orderBy('id', 'desc')->first();
            if ($sub) {
                $sub->update([
                    'plan_id' => $tier->id,
                    'subscription_name' => $tier->name,
                    'subscription_method' => $tier->pricing_type ?? 'amount',
                    'subscription_price' => $tier->cost ?? 0,
                ]);

                return;
            }
        }

        $durationMonths = $tier->duration_months ?? 12;
        UserSubscription::create([
            'user_id' => $user->id,
            'plan_id' => $tier->id,
            'subscription_name' => $tier->name,
            'subscription_method' => $tier->pricing_type ?? 'amount',
            'subscription_price' => $tier->cost ?? 0,
            'subscription_start_date' => now(),
            'subscription_expire_date' => now()->addMonths($durationMonths),
            'subscription_validity' => $durationMonths,
        ]);
    }

    private function formatPhone(Request $request): string
    {
        if ($request->filled('country_code')) {
            return '+' . $request->country_code . ' ' . $request->phone;
        }

        return (string) $request->phone;
    }
}
