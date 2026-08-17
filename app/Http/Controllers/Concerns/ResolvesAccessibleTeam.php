<?php

namespace App\Http\Controllers\Concerns;

use App\Models\Team;
use Illuminate\Http\JsonResponse;

trait ResolvesAccessibleTeam
{
    protected function accessibleTeam(int $teamId): ?Team
    {
        return Team::accessibleToAuth($teamId);
    }

    protected function accessibleTeamOrJsonError(int $teamId): Team|JsonResponse
    {
        $team = $this->accessibleTeam($teamId);

        if (!$team) {
            return response()->json([
                'message' => 'Group not found.',
                'status' => false,
            ], 403);
        }

        return $team;
    }
}
