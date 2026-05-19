<?php

namespace App\Http\Controllers\Api\Concerns;

use App\Models\Country;
use Illuminate\Database\Eloquent\Builder;
use Illuminate\Http\Request;

trait AppliesEducationScope
{
    /**
     * @return array{user: \App\Models\User, user_type: string, user_country: int|null, is_super_admin: bool, is_global_scope: bool, is_on_global_server: bool}
     */
    protected function educationScopeContext(): array
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

    protected function applyEducationFileScope(Builder $query, array $ctx): Builder
    {
        if ($ctx['is_super_admin']) {
            return $query;
        }

        if ($ctx['is_global_scope']) {
            return $query->whereHas('country', function ($q) {
                $q->where('code', 'GL');
            });
        }

        if ($ctx['user_country']) {
            return $query->where('country_id', $ctx['user_country']);
        }

        return $query;
    }

    protected function applyEducationTopicScope(Builder $query, array $ctx, ?string $educationType = null): Builder
    {
        if ($educationType) {
            $query->where('education_type', $educationType);
        }

        if ($ctx['is_super_admin']) {
            return $query;
        }

        if ($ctx['is_global_scope']) {
            return $query->whereHas('country', function ($q) {
                $q->where('code', 'GL');
            });
        }

        if ($ctx['user_country']) {
            return $query->where('country_id', $ctx['user_country']);
        }

        return $query;
    }

    protected function resolveEducationCountryId(Request $request, array $ctx): int
    {
        if ($this->requiresCountryIdFromRequest($ctx)) {
            return (int) $request->input('country_id');
        }

        if ($ctx['is_global_scope']) {
            return (int) Country::where('code', 'GL')->value('id');
        }

        return (int) $ctx['user_country'];
    }

    protected function requiresCountryIdFromRequest(array $ctx): bool
    {
        return $ctx['is_super_admin'] || $ctx['user_type'] === 'Global';
    }

    protected function canAccessFileInScope($file, array $ctx): bool
    {
        if ($ctx['is_super_admin']) {
            return true;
        }

        if ($ctx['is_global_scope']) {
            return $file->country && $file->country->code === 'GL';
        }

        return (int) $file->country_id === (int) $ctx['user_country'];
    }

    /**
     * @param  \Illuminate\Contracts\Pagination\LengthAwarePaginator  $paginator
     * @return \Illuminate\Contracts\Pagination\LengthAwarePaginator
     */
    protected function enrichEducationFilePaginator($paginator)
    {
        $paginator->getCollection()->transform(function ($file) {
            $file->loadMissing(['topic', 'country', 'user']);
            $file->file_topic_name = $file->topic?->topic_name ?? '--';
            $file->country_name = $file->country?->name ?? '--';
            $file->created_by_full_name = $file->user?->full_name ?? '--';
            $file->formatted_date = $file->created_at?->format('d M Y') ?? '--';

            return $file;
        });

        return $paginator;
    }

    /**
     * @param  \Illuminate\Contracts\Pagination\LengthAwarePaginator  $paginator
     * @return \Illuminate\Contracts\Pagination\LengthAwarePaginator
     */
    protected function enrichEducationTopicPaginator($paginator)
    {
        $paginator->getCollection()->transform(function ($topic) {
            $topic->loadMissing('country');
            $topic->country_name = $topic->country?->name ?? '--';

            return $topic;
        });

        return $paginator;
    }

    protected function applyEducationFileSearch(Builder $query, ?string $search): Builder
    {
        if ($search === null || trim($search) === '') {
            return $query;
        }

        $search = str_replace(' ', '%', trim($search));

        return $query->where(function ($q) use ($search) {
            $q->where('id', 'like', '%'.$search.'%')
                ->orWhere('file_name', 'like', '%'.$search.'%')
                ->orWhere('file_extension', 'like', '%'.$search.'%')
                ->orWhereHas('country', function ($cq) use ($search) {
                    $cq->where('name', 'like', '%'.$search.'%');
                });
        });
    }
}
