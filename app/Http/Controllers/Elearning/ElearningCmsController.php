<?php

namespace App\Http\Controllers\Elearning;

use App\Http\Controllers\Controller;
use App\Models\ElearningEcomCmsPage;
use Illuminate\Http\Request;

class ElearningCmsController extends Controller
{
    public function cmsPage($page_id = null)
    {
        $page_id = $page_id ?? 1;
        $cms = ElearningEcomCmsPage::findOrfail($page_id);
        return view('elearning.cms')->with(compact('cms'));
    }
}
