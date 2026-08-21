<?php

namespace App\Support;

use App\Helpers\Helper;
use App\Models\User;
use Illuminate\Database\Eloquent\Builder;

class PartnerVisibility
{
    public static function canAccessAudit(?User $user): bool
    {
        if ($user === null) {
            return false;
        }

        return $user->hasNewRole('SUPER ADMIN')
            || $user->can('View Member Audit Logs');
    }

    /**
     * User query matching Members List visibility (PartnerController@index).
     */
    public static function visiblePartnerIdsQuery(User $viewer): Builder
    {
        $is_user_ecclesia_admin = $viewer->is_ecclesia_admin;
        $currentCode = strtoupper(Helper::resolveVisitorCountryCode());

        $partners = User::query()
            ->leftJoin('user_types as ut', 'users.user_type_id', '=', 'ut.id')
            ->where(function ($q) {
                $q->whereNull('ut.id')
                    ->orWhere(function ($subQ) {
                        $subQ->where('ut.name', '!=', 'SUPER ADMIN');
                    });
            })
            ->select('users.*');

        if ($viewer->hasNewRole('SUPER ADMIN')) {
            $partners->where(function ($q) {
                $q->whereNull('ut.id')
                    ->orWhereHas('userRole', function ($subQ) {
                        $subQ->whereIn('type', [2, 3]);
                    });
            })
                ->where('users.id', '!=', $viewer->id);
        } else {
            $partners->where('users.status', 1);

            if ($viewer->user_type == 'Global') {
                $partners->whereIn('users.user_type', ['Global', 'G_R']);
            } elseif ($viewer->user_type == 'G_R') {
                if ($currentCode == 'GL') {
                    $partners->whereIn('users.user_type', ['Global', 'G_R']);
                } else {
                    $manage_ecclesia_ids = is_array($viewer->manage_ecclesia)
                        ? $viewer->manage_ecclesia
                        : explode(',', (string) $viewer->manage_ecclesia);

                    $partners->where('users.country', $viewer->country)
                        ->whereIn('users.user_type', ['Regional', 'G_R'])
                        ->where(function ($q) {
                            $q->whereNull('ut.id')
                                ->orWhereHas('userRole', function ($subQ) {
                                    $subQ->whereIn('type', [2, 3]);
                                });
                        });

                    if ($is_user_ecclesia_admin == 1) {
                        $partners->where(function ($q) use ($manage_ecclesia_ids, $viewer) {
                            $q->where(function ($sub) use ($manage_ecclesia_ids) {
                                $sub->whereIn('users.ecclesia_id', $manage_ecclesia_ids)->whereNotNull('users.ecclesia_id');
                            });
                            foreach ($manage_ecclesia_ids as $id) {
                                $id = trim($id);
                                if ($id !== '') {
                                    $q->orWhere(function ($sub) use ($id) {
                                        $sub->where('users.is_ecclesia_admin', 1)
                                            ->whereRaw('FIND_IN_SET(?, users.manage_ecclesia)', [$id]);
                                    });
                                }
                            }
                            $q->orWhere('users.created_id', $viewer->id);
                            $q->orWhere('users.id', $viewer->id);
                        });
                    }
                }
            } elseif ($viewer->user_type == 'Regional') {
                $partners->where('users.country', $viewer->country)
                    ->whereIn('users.user_type', ['Regional', 'G_R']);

                if ($is_user_ecclesia_admin == 1) {
                    $manage_ecclesia_ids = is_array($viewer->manage_ecclesia)
                        ? $viewer->manage_ecclesia
                        : explode(',', (string) $viewer->manage_ecclesia);
                    $partners->where(function ($q) use ($manage_ecclesia_ids, $viewer) {
                        $q->where(function ($sub) use ($manage_ecclesia_ids) {
                            $sub->whereIn('users.ecclesia_id', $manage_ecclesia_ids)->whereNotNull('users.ecclesia_id');
                        });
                        foreach ($manage_ecclesia_ids as $id) {
                            $id = trim($id);
                            if ($id !== '') {
                                $q->orWhere(function ($sub) use ($id) {
                                    $sub->where('users.is_ecclesia_admin', 1)
                                        ->whereRaw('FIND_IN_SET(?, users.manage_ecclesia)', [$id]);
                                });
                            }
                        }
                        $q->orWhere('users.created_id', $viewer->id);
                        $q->orWhere('users.id', $viewer->id);
                    });
                }
            }
        }

        return $partners;
    }

    /**
     * user_type values this viewer may assign when creating or editing a member.
     *
     * Mirrors the Members List scope: you may only create members you would then be able
     * to see. create()/edit() offered a wider list than store()/update() accepted, which is
     * why an ECCLESIA member could pick Regional and then be refused (client test #19), and
     * why a G_R viewer on .org was offered Regional instead of Global (client test ORG #6).
     *
     * @return list<string>
     */
    public static function creatableUserTypes(User $viewer): array
    {
        if ($viewer->hasNewRole('SUPER ADMIN')) {
            return ['Global', 'Regional', 'G_R'];
        }

        $currentCode = strtoupper(Helper::resolveVisitorCountryCode());

        return match ($viewer->user_type) {
            'Global' => ['Global', 'G_R'],
            'G_R' => $currentCode === 'GL' ? ['Global', 'G_R'] : ['Regional', 'G_R'],
            'Regional' => ['Regional', 'G_R'],
            default => [(string) $viewer->user_type],
        };
    }

    public static function canAssignUserType(User $viewer, ?string $userType): bool
    {
        return in_array((string) $userType, static::creatableUserTypes($viewer), true);
    }

    /**
     * Regional members are country-scoped by definition, so a Regional member must be
     * created in the viewer's own country; a Regional viewer is confined to it outright.
     */
    public static function mustMatchViewerCountry(User $viewer, ?string $userType): bool
    {
        if ($viewer->hasNewRole('SUPER ADMIN')) {
            return false;
        }

        return $viewer->user_type === 'Regional' || (string) $userType === 'Regional';
    }

    public static function viewerCanSeePartner(User $viewer, User $partner): bool
    {
        return static::visiblePartnerIdsQuery($viewer)
            ->where('users.id', $partner->id)
            ->exists();
    }

    public static function constrainAuditQuery($query, User $viewer): void
    {
        if ($viewer->hasNewRole('SUPER ADMIN')) {
            return;
        }

        $visiblePartnerIds = static::visiblePartnerIdsQuery($viewer)->select('users.id');

        $query->where(function ($q) use ($visiblePartnerIds, $viewer) {
            $q->whereIn('target_user_id', $visiblePartnerIds);

            if ($viewer->user_type === 'Global') {
                $q->orWhereNull('target_user_id');
            }
        });
    }
}
