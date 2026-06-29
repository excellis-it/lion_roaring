<?php

namespace App\Http\Controllers\Concerns;

use Illuminate\Http\Request;
use Illuminate\Support\Facades\Validator;
use Illuminate\Validation\Validator as ValidationValidator;

trait PreparesPrivateCollaborationInput
{
    protected function normalizePrivateCollaborationInput(Request $request): void
    {
        $createZoom = filter_var(
            $request->input('create_zoom', false),
            FILTER_VALIDATE_BOOLEAN
        );

        $invitees = collect((array) $request->input('invitees', []))
            ->map(static fn ($id) => (int) $id)
            ->filter(static fn ($id) => $id > 0)
            ->unique()
            ->reject(static fn ($id) => auth()->check() && $id === (int) auth()->id())
            ->values()
            ->all();

        $request->merge([
            'invitees' => $invitees,
            'create_zoom' => $createZoom ? 1 : 0,
            'meeting_link' => $createZoom || blank($request->input('meeting_link'))
                ? null
                : $request->input('meeting_link'),
        ]);
    }

    /**
     * @return array<string, mixed>
     */
    protected function privateCollaborationStoreRules(Request $request): array
    {
        $createZoom = (bool) $request->input('create_zoom');

        return [
            'title' => 'required|string|max:255',
            'description' => 'nullable|string',
            'meeting_link' => $createZoom ? 'nullable' : 'required|url',
            'start_time' => 'required|date',
            'end_time' => 'required|date|after:start_time',
            'create_zoom' => 'nullable|boolean',
            'country_id' => 'required|exists:countries,id',
            'invitees' => 'required|array|min:1',
            'invitees.*' => 'integer|exists:users,id',
        ];
    }

    protected function validatePrivateCollaborationStore(Request $request): ValidationValidator
    {
        $this->normalizePrivateCollaborationInput($request);

        return Validator::make(
            $request->all(),
            $this->privateCollaborationStoreRules($request)
        );
    }

    protected function assertEligibleInvitees(array $invitees): ?array
    {
        $eligibleCount = \App\Models\User::whereIn('id', $invitees)
            ->whereHas('roles.permissions', function ($q) {
                $q->where('name', 'Manage Private Collaboration');
            })
            ->where('id', '!=', auth()->id())
            ->count();

        if (count($invitees) !== $eligibleCount) {
            return [
                'status' => false,
                'message' => 'One or more selected invitees are not eligible for invitation.',
            ];
        }

        return null;
    }
}
