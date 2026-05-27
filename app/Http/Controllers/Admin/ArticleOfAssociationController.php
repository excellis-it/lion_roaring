<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\Article;
use App\Traits\ImageTrait;
use Illuminate\Http\Request;

class ArticleOfAssociationController extends Controller
{
    use ImageTrait;

    public function index(Request $request)
    {
        if (auth()->user()->can('Manage Article of Association Page')) {
            $article = Article::where('country_code', $request->get('content_country_code', 'US'))->orderBy('id', 'desc')->first();
            return view('admin.article_of_association.update', compact('article'));
        } else {
            return redirect()->route('admin.dashboard')->with('error', 'Unauthorized Access');
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
        $request->validate([
            'pdf' => 'nullable|mimes:pdf',
            'checkbox_text' => 'nullable|string|max:255'
        ]);
        $country = $request->content_country_code ?? 'US';
        $data = ['country_code' => $country];
        if ($request->hasFile('pdf')) {
            $data['pdf'] = $this->imageUpload($request->file('pdf'), 'article_of_association');
        }
        if ($request->has('checkbox_text')) {
            $data['checkbox_text'] = $request->checkbox_text;
        }
        $article = Article::updateOrCreate(['country_code' => $country], $data);

        return redirect()->back()->with('message', 'Article of association updated successfully');
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
