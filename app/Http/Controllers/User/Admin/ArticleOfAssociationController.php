<?php

namespace App\Http\Controllers\User\Admin;

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
            return view('user.admin.article_of_association.update', compact('article'));
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
            'pdf' => 'required|mimes:pdf'
        ]);
        if ($request->id != '') {
            $article = Article::find($request->id);
        } else {
            $article = new Article();
        }
        $article->pdf = $this->imageUpload($request->file('pdf'), 'article_of_association');
        //  $article->save();
        $country = $request->content_country_code ?? 'US';
        $article = Article::updateOrCreate(['country_code' => $country], array_merge($article->getAttributes(), ['country_code' => $country]));

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
