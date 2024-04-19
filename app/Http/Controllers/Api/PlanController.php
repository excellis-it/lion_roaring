<?php

namespace App\Http\Controllers\Api;

use App\Http\Controllers\Controller;
use App\Models\Plan;
use Illuminate\Http\Request;

/**
 * @group Plan Management
 */
class PlanController extends Controller
{
    protected $successStatus = 200;

    /**
     * Plan List
     *
     * @response 200 {
     * "plan_status": true,
     * "message": "Plan list",
     * "data": [
     * {
     * "id": 1,
     * "plan_name": "Basic",
     * "plan_price": 100,
     * "plan_validity": 30,
     * "plan_status": 1,
     * "created_at": "2024-03-05T10:58:13.000000Z",
     * "updated_at": "2024-04-18T12:27:38.000000Z"
     * },
     * {
     * "id": 2,
     * "plan_name": "Standard",
     * "plan_price": 200,
     * "plan_validity": 60,
     * "plan_status": 1,
     * "created_at": "2024-03-05T10:58:13.000000Z",
     * "updated_at": "2024-04-18T12:27:38.000000Z"
     * }
     * ]
     * }
     */

    public function planDetails(Request $request)
    {
        try {
            $plans = Plan::where('plan_status', 1)->get();
            if ($plans) {
                return response()->json(['status' => true, 'message' => 'Plan list', 'data' => $plans], $this->successStatus);
            } else {
                return response()->json(['status' => false, 'message' => 'No plan found'], 201);
            }
        } catch (\Throwable $th) {
            return response()->json(['status' => false, 'message' => $th->getMessage()], 401);
        }
    }
}
