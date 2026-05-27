<?php

namespace App\Http\Controllers\Api;

use App\Http\Controllers\Controller;
use App\Models\Organization;
use App\Models\OrganizationCenter;
use App\Models\OrganizationProject;
use App\Models\OurGovernance;
use App\Models\OurOrganization;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\Request;

/**
 * @group Organizations & Governance
 */
class OrganizationApiController extends Controller
{
    /**
     * GET /organizations/latest
     * Returns the most recently created Organization (the banner/hero record) with images
     * ordered by `sort_order` (April 8 change) plus two project sections.
     * @queryParam country_code string optional
     */
    public function latestOrganization(Request $request): JsonResponse
    {
        $org = Organization::query()
            ->when($request->filled('country_code'), fn ($q) => $q->where('country_code', $request->country_code))
            ->with(['images', 'projects', 'projectsTwo'])
            ->orderByDesc('id')
            ->first();

        if (!$org) {
            return response()->json(['status' => false, 'message' => 'No organization found.'], 404);
        }

        return response()->json([
            'status' => true,
            'message' => 'Organization.',
            'data' => $org,
        ]);
    }

    /**
     * GET /organizations/{id}
     * Returns a specific Organization row with images (sort_order honored) + projects.
     */
    public function organizationDetail(int $id): JsonResponse
    {
        $org = Organization::with(['images', 'projects', 'projectsTwo'])->find($id);
        if (!$org) {
            return response()->json(['status' => false, 'message' => 'Organization not found.'], 404);
        }

        return response()->json([
            'status' => true,
            'message' => 'Organization.',
            'data' => $org,
        ]);
    }

    /**
     * GET /our-organizations
     * List OurOrganization records (the CMS entity-per-organization collection).
     * @queryParam country_code string optional
     */
    public function ourOrganizations(Request $request): JsonResponse
    {
        $orgs = OurOrganization::query()
            ->when($request->filled('country_code'), fn ($q) => $q->where('country_code', $request->country_code))
            ->orderBy('name')
            ->get();

        return response()->json([
            'status' => true,
            'message' => 'Our organizations.',
            'data' => $orgs,
        ]);
    }

    /**
     * GET /our-organizations/{id}
     */
    public function ourOrganizationDetail(int $id): JsonResponse
    {
        $org = OurOrganization::find($id);
        if (!$org) {
            return response()->json(['status' => false, 'message' => 'Organization not found.'], 404);
        }

        return response()->json([
            'status' => true,
            'message' => 'Organization.',
            'data' => $org,
        ]);
    }

    /**
     * GET /our-organizations/{id}/centers
     */
    public function ourOrganizationCenters(int $id): JsonResponse
    {
        $org = OurOrganization::find($id);
        if (!$org) {
            return response()->json(['status' => false, 'message' => 'Organization not found.'], 404);
        }

        $centers = OrganizationCenter::where('our_organization_id', $org->id)
            ->orderByDesc('id')
            ->get();

        return response()->json([
            'status' => true,
            'message' => 'Centers.',
            'data' => $centers,
        ]);
    }

    /**
     * GET /organization-centers/{id}
     */
    public function centerDetail(int $id): JsonResponse
    {
        $center = OrganizationCenter::with('ourOrganization')->find($id);
        if (!$center) {
            return response()->json(['status' => false, 'message' => 'Center not found.'], 404);
        }

        return response()->json([
            'status' => true,
            'message' => 'Center.',
            'data' => $center,
        ]);
    }

    /**
     * GET /organizations/{id}/projects
     * @queryParam section int optional  1 or 2 (defaults to both).
     */
    public function organizationProjects(int $id, Request $request): JsonResponse
    {
        $section = $request->input('section');

        $query = OrganizationProject::where('organization_id', $id);
        if (in_array((int) $section, [1, 2], true)) {
            $query->where('section', (int) $section);
        }

        $projects = $query->orderBy('id')->get();

        return response()->json([
            'status' => true,
            'message' => 'Projects.',
            'data' => $projects,
        ]);
    }

    /**
     * GET /our-governance
     * Paginated governance list, ordered by order_no then id.
     * @queryParam country_code string optional
     */
    public function governanceList(Request $request): JsonResponse
    {
        $items = OurGovernance::query()
            ->when($request->filled('country_code'), fn ($q) => $q->where('country_code', $request->country_code))
            ->orderBy('order_no')
            ->orderBy('id')
            ->get();

        return response()->json([
            'status' => true,
            'message' => 'Governance.',
            'data' => $items,
        ]);
    }

    /**
     * GET /our-governance/{id}
     */
    public function governanceDetail(int $id): JsonResponse
    {
        $item = OurGovernance::find($id);
        if (!$item) {
            return response()->json(['status' => false, 'message' => 'Governance entry not found.'], 404);
        }

        return response()->json([
            'status' => true,
            'message' => 'Governance.',
            'data' => $item,
        ]);
    }
}
