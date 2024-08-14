<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\Donation;
use Illuminate\Http\Request;

class DonationController extends Controller
{
    /**
     * Display a listing of the resource.
     *
     * @return \Illuminate\Http\Response
     */
    public function index()
    {
        $donations = Donation::orderBy('id', 'desc')->paginate(15);
        return view('admin.donations.list')->with(compact('donations'));
    }

    public function fetchData(Request $request)
    {
        if ($request->ajax()) {

            $sort_by = $request->get('sortby');
            $sort_type = $request->get('sorttype');
            $query = $request->get('query');
            $query = str_replace(" ", "%", $query);
            $donations = Donation::where('id', 'like', '%' . $query . '%')
                ->orWhereRaw("CONCAT(first_name, ' ', last_name) like '%" . $query . "%'")
                ->orWhere('email', 'like', '%' . $query . '%')
                ->orWhere('address', 'like', '%' . $query . '%')
                ->orWhere('city', 'like', '%' . $query . '%')
                ->orWhere('postcode', 'like', '%' . $query . '%')
                ->orWhere('phone', 'like', '%' . $query . '%')
                ->orWhere('transaction_id', 'like', '%' . $query . '%')
                ->orWhere('donation_amount', 'like', '%' . $query . '%')
                ->orWhere('payment_status', 'like', '%' . $query . '%')
                ->orWhereHas('country', function ($q) use ($query) {
                    $q->where('name', 'like', '%' . $query . '%');
                })
                ->orWhereHas('state', function ($q) use ($query) {
                    $q->where('name', 'like', '%' . $query . '%');
                })
                ->orderBy($sort_by, $sort_type)
                ->paginate(15);

            return response()->json(['data' => view('admin.donations.table', compact('donations'))->render()]);
        }
    }


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
}
