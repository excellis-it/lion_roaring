<?php

use App\Http\Controllers\Api\CmsController;
use App\Http\Controllers\Api\ContactUsController;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Route;

/*
|--------------------------------------------------------------------------
| API Routes
|--------------------------------------------------------------------------
|
| Here is where you can register API routes for your application. These
| routes are loaded by the RouteServiceProvider within a group which
| is assigned the "api" middleware group. Enjoy building your API!
|
*/

// Route::middleware('auth:sanctum')->get('/user', function (Request $request) {
//     return $request->user();
// });

Route::prefix('v1')->group(function () {
    Route::post('contact-us', [ContactUsController::class, 'store']);

    Route::prefix('cms')->group(function () {
        Route::post('home', [CmsController::class, 'home']);
        Route::post('gallery', [CmsController::class, 'gallery']);
        Route::post('contact-us', [CmsController::class, 'contactUs']);
        Route::post('faq', [CmsController::class, 'faq']);
        Route::post('principle-and-business', [CmsController::class, 'principleAndBusiness']);
        Route::post('ecclesia-associations', [CmsController::class, 'ecclesiaAssociations']);
        Route::post('our-organization', [CmsController::class, 'ourOrganization']);
        Route::post('common', [CmsController::class, 'common']);
        Route::post('our-governance', [CmsController::class, 'ourGovernance']);
        Route::post('organization-center',[CmsController::class, 'organizationCenter']);
        Route::post('organization-center-details',[CmsController::class, 'organizationCenterDetails']);
    });
});
