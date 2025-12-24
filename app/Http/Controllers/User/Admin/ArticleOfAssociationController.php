<?php

namespace App\Http\Controllers\User\Admin;

use App\Http\Controllers\Controller;
use App\Models\Article;
use App\Models\Country;
use App\Traits\ImageTrait;
use Illuminate\Http\Request;

class ArticleOfAssociationController extends Controller
{
    use ImageTrait;
     public $user_type;
    public $user_country;
    public $country;

    // use consructor
    public function __construct()
    {
        $this->middleware(function ($request, $next) {
            $this->user_type = auth()->user()->user_type;
            $this->user_country = auth()->user()->country;
            $this->country = Country::where('id', $this->user_country)->first();

            return $next($request);
        });
    }
    public function index(Request $request)
    {
        if (auth()->user()->can('Manage Article of Association Page')) {
            if ($this->user_type == 'Global') {
                $article = Article::where('country_code', $request->get('content_country_code', 'US'))->orderBy('id', 'desc')->first();
            } else {
                $article = Article::where('country_code', $this->country->code)->orderBy('id', 'desc')->first();
            }
            return view('user.admin.article_of_association.update', compact('article'));
        } else {
            return redirect()->route('user.profile')->with('error', 'Unauthorized Access');
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
        if ($this->user_type == 'Global') {
            $country = $request->content_country_code ?? 'US';
        } else {
            $country = $this->country->code;
        }
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
