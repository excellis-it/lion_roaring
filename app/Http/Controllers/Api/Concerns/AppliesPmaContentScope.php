<?php

namespace App\Http\Controllers\Api\Concerns;

use App\Models\Country;
use App\Models\EventRsvp;
use Illuminate\Database\Eloquent\Builder;
use Illuminate\Support\Collection;

trait AppliesPmaContentScope
{
    /**
     * @return array{user: \App\Models\User, user_type: string, user_country: int|null, is_super_admin: bool, is_global_scope: bool, is_on_global_server: bool}
     */
    protected function pmaScopeContext(): array
    {
        $user = auth()->user();
        $currentCountry = Country::findByCurrentRequest();
        $isOnGlobalServer = $currentCountry && $currentCountry->is_global;
        $userType = $user->user_type ?? 'Global';
        $isSuperAdmin = $user->hasNewRole('SUPER ADMIN');
        $isGlobalScope = ! $isSuperAdmin
            && ($userType === 'Global' || ($userType === 'G_R' && $isOnGlobalServer));

        return [
            'user' => $user,
            'user_type' => $userType,
            'user_country' => $user->country ?? null,
            'is_super_admin' => $isSuperAdmin,
            'is_global_scope' => $isGlobalScope,
            'is_on_global_server' => $isOnGlobalServer,
        ];
    }

    /**
     * @return list<string>
     */
    protected function manageEcclesiaIds($user): array
    {
        if ((int) ($user->is_ecclesia_admin ?? 0) !== 1) {
            return [];
        }

        $ids = is_array($user->manage_ecclesia)
            ? $user->manage_ecclesia
            : explode(',', (string) ($user->manage_ecclesia ?? ''));

        return array_values(array_filter(array_map('trim', $ids), fn ($id) => $id !== ''));
    }

    /**
     * Scope bulletins / meetings (owner column: user_id).
     */
    protected function applyPmaCreatorContentScope(
        Builder $query,
        array $ctx,
        string $ownerColumn = 'user_id'
    ): Builder {
        if ($ctx['is_super_admin']) {
            return $query;
        }

        if ($ctx['is_global_scope']) {
            return $query
                ->whereHas('country', fn ($q) => $q->where('code', 'GL'))
                ->whereHas('user', fn ($q) => $q->whereIn('user_type', ['Global', 'G_R'])->where('status', 1));
        }

        $query->where('country_id', $ctx['user_country'])
            ->whereHas('user', fn ($q) => $q->whereIn('user_type', ['Regional', 'G_R'])->where('status', 1));

        $ecclesiaIds = $this->manageEcclesiaIds($ctx['user']);
        if ($ecclesiaIds !== []) {
            $user = $ctx['user'];
            $query->where(function ($q) use ($ecclesiaIds, $user, $ownerColumn) {
                $q->whereHas('user', function ($uq) use ($ecclesiaIds) {
                    $uq->where(function ($sub) use ($ecclesiaIds) {
                        $sub->whereIn('ecclesia_id', $ecclesiaIds)->whereNotNull('ecclesia_id');
                        foreach ($ecclesiaIds as $id) {
                            $sub->orWhereRaw('FIND_IN_SET(?, manage_ecclesia)', [$id]);
                        }
                    });
                })->orWhere($ownerColumn, $user->id);
            });
        }

        return $query;
    }

    /**
     * Scope job postings (owner column: created_by; simpler ecclesia filter on web).
     */
    protected function applyPmaJobScope(Builder $query, array $ctx): Builder
    {
        if ($ctx['is_super_admin']) {
            return $query;
        }

        if ($ctx['is_global_scope']) {
            return $query
                ->whereHas('country', fn ($q) => $q->where('code', 'GL'))
                ->whereHas('user', fn ($q) => $q->whereIn('user_type', ['Global', 'G_R']));
        }

        $query->where('country_id', $ctx['user_country'])
            ->whereHas('user', fn ($q) => $q->whereIn('user_type', ['Regional', 'G_R']));

        $ecclesiaIds = $this->manageEcclesiaIds($ctx['user']);
        if ($ecclesiaIds !== []) {
            $user = $ctx['user'];
            $query->where(function ($q) use ($ecclesiaIds, $user) {
                $q->whereHas('user', fn ($uq) => $uq->whereIn('ecclesia_id', $ecclesiaIds))
                    ->orWhere('created_by', $user->id);
            });
        }

        return $query;
    }

    /**
     * Scope live events; includes RSVP'd events outside country (mirrors web LiveEventController).
     */
    protected function applyPmaEventScope(Builder $query, array $ctx): Builder
    {
        if ($ctx['is_super_admin']) {
            return $query;
        }

        $rsvpEventIds = EventRsvp::where('user_id', $ctx['user']->id)
            ->whereIn('status', ['confirmed', 'pending'])
            ->pluck('event_id');

        if ($ctx['is_global_scope']) {
            return $query
                ->where(function ($q) use ($rsvpEventIds) {
                    $q->whereHas('country', fn ($cq) => $cq->where('code', 'GL'))
                        ->orWhereIn('id', $rsvpEventIds);
                })
                ->whereHas('user', fn ($q) => $q->whereIn('user_type', ['Global', 'G_R']));
        }

        $query->where(function ($q) use ($ctx, $rsvpEventIds) {
            $q->where('country_id', $ctx['user_country'])
                ->orWhereIn('id', $rsvpEventIds);
        })->whereHas('user', fn ($q) => $q->whereIn('user_type', ['Regional', 'G_R']));

        $ecclesiaIds = $this->manageEcclesiaIds($ctx['user']);
        if ($ecclesiaIds !== []) {
            $user = $ctx['user'];
            $query->where(function ($q) use ($ecclesiaIds, $user) {
                $q->whereHas('user', fn ($uq) => $uq->whereIn('ecclesia_id', $ecclesiaIds))
                    ->orWhere('user_id', $user->id);
            });
        }

        return $query;
    }

    /**
     * Private collaboration list: creator or invitee, with country scope.
     */
    protected function applyPmaPrivateCollaborationScope(Builder $query, array $ctx): Builder
    {
        $query->where(function ($q) {
            $q->where('user_id', auth()->id())
                ->orWhereHas('invitations', fn ($iq) => $iq->where('user_id', auth()->id()));
        });

        if ($ctx['is_super_admin']) {
            return $query;
        }

        if ($ctx['is_global_scope']) {
            return $query->whereHas('country', fn ($q) => $q->where('code', 'GL'));
        }

        if ($ctx['user_country']) {
            $query->where('country_id', $ctx['user_country']);
        }

        return $query;
    }

    protected function userRsvpEventIds(): Collection
    {
        return EventRsvp::where('user_id', auth()->id())
            ->whereIn('status', ['confirmed', 'pending'])
            ->pluck('event_id');
    }

    protected function resolvePmaCountryIdFromContext(array $ctx, ?int $requestCountryId = null): int
    {
        if ($ctx['is_super_admin'] || $ctx['user_type'] === 'Global') {
            return (int) $requestCountryId;
        }

        if ($ctx['is_global_scope']) {
            return (int) Country::where('code', 'GL')->value('id');
        }

        return (int) $ctx['user_country'];
    }
}
