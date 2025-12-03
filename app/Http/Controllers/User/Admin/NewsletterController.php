<?php

namespace App\Http\Controllers\User\Admin;

use App\Http\Controllers\Controller;
use App\Models\Newsletter;
use Illuminate\Http\Request;

class NewsletterController extends Controller
{
    /**
     * Display a listing of the resource.
     *
     * @return \Illuminate\Http\Response
     */
    public function index()
    {
        if (auth()->user()->can('Manage Newsletters')) {
            $newsletters = Newsletter::orderBy('id', 'desc')->paginate(10);
            return view('user.admin.newsletter.list')->with('newsletters', $newsletters);
        } else {
            abort(403, 'You do not have permission to access this page.');
        }
    }

    public function fetchData(Request $request)
    {
        if ($request->ajax()) {

            $sort_by = $request->get('sortby');
            $sort_type = $request->get('sorttype');
            $query = $request->get('query');
            $query = str_replace(" ", "%", $query);
            $newsletters = Newsletter::where('id', 'like', '%' . $query . '%')
                ->orWhere('full_name', 'like', '%' . $query . '%')
                ->orWhere('email', 'like', '%' . $query . '%')
                ->orWhere('message', 'like', '%' . $query . '%')
                ->orderBy($sort_by, $sort_type)
                ->paginate(10);

            return response()->json(['data' => view('user.admin.newsletter.table', compact('newsletters'))->render()]);
        }
    }

    /**
     * Show the form for creating a new resource.
     *
     * @return \Illuminate\Http\Response
     */
    public function create()
    {
        //
    }

    /**
     * Store a newly created resource in storage.
     *
     * @param  \Illuminate\Http\Request  $request
     * @return \Illuminate\Http\Response
     */
    public function store(Request $request)
    {
        //
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
        //
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
        //
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
        if (auth()->user()->can('Delete Newsletters')) {
            $newsletter = Newsletter::find($id);
            if ($newsletter) {
                $newsletter->delete();
                return redirect()->route('newsletters.index')->with('message', 'Newsletter deleted successfully.');
            } else {
                return redirect()->route('newsletters.index')->with('error', 'Newsletter not found.');
            }
        } else {
            abort(403, 'You do not have permission to access this page.');
        }
    }
}
