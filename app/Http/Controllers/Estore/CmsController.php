<?php

namespace App\Http\Controllers\Estore;

use App\Http\Controllers\Controller;
use App\Models\EcomCmsPage;
use Illuminate\Http\Request;

class CmsController extends Controller
{
    public function cmsPage($page_id = null)
    {
        $page_id = $page_id ?? 1;
        $cms = EcomCmsPage::findOrfail($page_id);
        return view('ecom.cms')->with(compact('cms'));
    }
}
