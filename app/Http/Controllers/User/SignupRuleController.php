<?php

namespace App\Http\Controllers\User;

use App\Http\Controllers\Controller;
use App\Models\SignupRule;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Validator;

class SignupRuleController extends Controller
{
    /**
     * Display a listing of the resource.
     *
     * @return \Illuminate\Http\Response
     */
    public function index()
    {
        $rules = SignupRule::orderBy('priority', 'desc')->orderBy('created_at', 'desc')->get();
        return view('user.signup-rules.index', compact('rules'));
    }

    /**
     * Show the form for creating a new resource.
     *
     * @return \Illuminate\Http\Response
     */
    public function create()
    {
        return view('user.signup-rules.create');
    }

    /**
     * Store a newly created resource in storage.
     *
     * @param  \Illuminate\Http\Request  $request
     * @return \Illuminate\Http\Response
     */
    public function store(Request $request)
    {
        $validator = Validator::make($request->all(), [
            'field_name' => 'required|string|max:255',
            'rule_type' => 'required|string|max:255',
            'rule_value' => 'nullable|string',
            'error_message' => 'nullable|string',
            'description' => 'nullable|string',
            'is_active' => 'boolean',
            'is_critical' => 'boolean',
            'priority' => 'required|integer|min:0',
        ]);

        if ($validator->fails()) {
            return redirect()->back()
                ->withErrors($validator)
                ->withInput();
        }

        SignupRule::create([
            'field_name' => $request->field_name,
            'rule_type' => $request->rule_type,
            'rule_value' => $request->rule_value,
            'error_message' => $request->error_message,
            'description' => $request->description,
            'is_active' => $request->has('is_active') ? 1 : 0,
            'is_critical' => $request->has('is_critical') ? 1 : 0,
            'priority' => $request->priority,
        ]);

        return redirect()->route('user.signup-rules.index')
            ->with('success', 'Signup rule created successfully');
    }

    /**
     * Display the specified resource.
     *
     * @param  int  $id
     * @return \Illuminate\Http\Response
     */
    public function show($id)
    {
        $rule = SignupRule::findOrFail($id);
        return view('user.signup-rules.show', compact('rule'));
    }

    /**
     * Show the form for editing the specified resource.
     *
     * @param  int  $id
     * @return \Illuminate\Http\Response
     */
    public function edit($id)
    {
        $rule = SignupRule::findOrFail($id);
        return view('user.signup-rules.edit', compact('rule'));
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
        $rule = SignupRule::findOrFail($id);

        $validator = Validator::make($request->all(), [
            'field_name' => 'required|string|max:255',
            'rule_type' => 'required|string|max:255',
            'rule_value' => 'nullable|string',
            'error_message' => 'nullable|string',
            'description' => 'nullable|string',
            'is_active' => 'boolean',
            'is_critical' => 'boolean',
            'priority' => 'required|integer|min:0',
        ]);

        if ($validator->fails()) {
            return redirect()->back()
                ->withErrors($validator)
                ->withInput();
        }

        $rule->update([
            'field_name' => $request->field_name,
            'rule_type' => $request->rule_type,
            'rule_value' => $request->rule_value,
            'error_message' => $request->error_message,
            'description' => $request->description,
            'is_active' => $request->has('is_active') ? 1 : 0,
            'is_critical' => $request->has('is_critical') ? 1 : 0,
            'priority' => $request->priority,
        ]);

        return redirect()->route('user.signup-rules.index')
            ->with('success', 'Signup rule updated successfully');
    }

    /**
     * Remove the specified resource from storage.
     *
     * @param  int  $id
     * @return \Illuminate\Http\Response
     */
    public function destroy($id)
    {
        $rule = SignupRule::findOrFail($id);
        $rule->delete();

        return redirect()->route('user.signup-rules.index')
            ->with('success', 'Signup rule deleted successfully');
    }

    /**
     * Toggle the active status of a signup rule
     *
     * @param  int  $id
     * @return \Illuminate\Http\Response
     */
    public function toggleStatus($id)
    {
        $rule = SignupRule::findOrFail($id);
        $rule->is_active = !$rule->is_active;
        $rule->save();

        return redirect()->route('user.signup-rules.index')
            ->with('success', 'Signup rule status updated successfully');
    }
}
