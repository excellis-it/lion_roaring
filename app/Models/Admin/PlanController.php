<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\Plan;
use Illuminate\Http\Request;

class PlanController extends Controller
{
    /**
     * Display a listing of the resource.
     *
     * @return \Illuminate\Http\Response
     */
    public function index()
    {
        $plans = Plan::orderBy('id', 'desc')->paginate(10);
        return view('admin.plans.list', compact('plans'));
    }

    /**
     * Show the form for creating a new resource.
     *
     * @return \Illuminate\Http\Response
     */
    public function create()
    {
        return view('admin.plans.create');
    }

    /**
     * Store a newly created resource in storage.
     *
     * @param  \Illuminate\Http\Request  $request
     * @return \Illuminate\Http\Response
     */
    public function store(Request $request)
    {
        $request->validate([
            'plan_name' => 'required|unique:plans',
            'plan_price' => 'required|numeric',
            'plan_validity' => 'required|numeric',
            'plan_status' => 'required',
            'plan_description' => 'required|max:152',
        ]);

        $plan = new Plan();
        $plan->plan_name = $request->plan_name;
        $plan->plan_price = $request->plan_price;
        $plan->plan_validity = $request->plan_validity;
        $plan->plan_status = $request->plan_status;
        $plan->plan_description = $request->plan_description;
        $plan->save();

        return redirect()->route('plans.index')->with('message', 'Plan created successfully.');
    }

    /**
     * Display the specified resource.
     *
     * @param  int  $id
     * @return \Illuminate\Http\Response
     */
    public function show($id)
    {
        //
    }

    /**
     * Show the form for editing the specified resource.
     *
     * @param  int  $id
     * @return \Illuminate\Http\Response
     */
    public function edit($id)
    {
        $plan = Plan::find($id);
        return view('admin.plans.edit', compact('plan'));
    }

    /**
     * Update the specified resource in storage.
     *
     * @param  \Illuminate\Http\Request  $request
     * @param  int  $id
     * @return \Illuminate\Http\Response
     */
    public function update(Request $request, $id)
    {
        $request->validate([
            'plan_name' => 'required|unique:plans,plan_name,' . $id,
            'plan_price' => 'required|numeric',
            'plan_validity' => 'required|numeric',
            'plan_status' => 'required',
            'plan_description' => 'required|max:152',
        ]);

        $plan = Plan::find($id);
        $plan->plan_name = $request->plan_name;
        $plan->plan_price = $request->plan_price;
        $plan->plan_validity = $request->plan_validity;
        $plan->plan_status = $request->plan_status;
        $plan->plan_description = $request->plan_description;
        $plan->save();

        return redirect()->route('plans.index')->with('message', 'Plan updated successfully.');
    }

    /**
     * Remove the specified resource from storage.
     *
     * @param  int  $id
     * @return \Illuminate\Http\Response
     */
    public function destroy($id)
    {
        //
    }

    public function delete($id)
    {
        $plan = Plan::find($id);
        $plan->delete();
        return redirect()->route('plans.index')->with('message', 'Plan deleted successfully.');
    }

    public function changePlansStatus(Request $request)
    {
        $plan = Plan::find($request->user_id);
        $plan->plan_status = $request->status;
        $plan->save();
        return response()->json(['success' => 'Status change successfully.']);
    }
    public function fetchData(Request $request)
    {
        if ($request->ajax()) {
            $sort_by = $request->get('sortby');
            $sort_type = $request->get('sorttype');
            $query = $request->get('query');
            $query = str_replace(" ", "%", $query);
            $plans = Plan::where('id', 'like', '%' . $query . '%')
                ->orWhere('plan_name', 'like', '%' . $query . '%')
                ->orWhere('plan_validity', 'like', '%' . $query . '%')
                ->orWhere('plan_price', 'like', '%' . $query . '%');

            $plans =  $plans->orderBy($sort_by, $sort_type)->paginate(15);

            return response()->json(['data' => view('admin.plans.table', compact('plans'))->render()]);
        }
    }
}
