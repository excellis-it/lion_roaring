<?php

use App\Http\Controllers\Api\AuthController;
use App\Http\Controllers\Api\BecomingChristLikeController;
use App\Http\Controllers\Api\BecomingSovereignController;
use App\Http\Controllers\Api\CmsController;
use App\Http\Controllers\Api\ContactUsController;
use App\Http\Controllers\Api\DonationController;
use App\Http\Controllers\Api\ForgetPasswordController;
use App\Http\Controllers\Api\PlanController;
use App\Http\Controllers\Api\ProfileController;
use App\Http\Controllers\Api\SubscriptionController;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Route;
use App\Http\Controllers\Api\ChatController;
use App\Http\Controllers\Api\TeamChatController;
use App\Http\Controllers\Api\EmailController;
use App\Http\Controllers\Api\LeadershipDevelopmentController;
use App\Http\Controllers\Api\TopicController;


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

// Route::prefix('v1')->group(function () {
//     Route::post('contact-us', [ContactUsController::class, 'store']);

//     Route::prefix('cms')->group(function () {
//         Route::post('home', [CmsController::class, 'home']);
//         Route::post('gallery', [CmsController::class, 'gallery']);
//         Route::post('contact-us', [CmsController::class, 'contactUs']);
//         Route::post('faq', [CmsController::class, 'faq']);
//         Route::post('principle-and-business', [CmsController::class, 'principleAndBusiness']);
//         Route::post('ecclesia-associations', [CmsController::class, 'ecclesiaAssociations']);
//         Route::post('our-organization', [CmsController::class, 'ourOrganization']);
//         Route::post('common', [CmsController::class, 'common']);
//         Route::post('our-governance', [CmsController::class, 'ourGovernance']);
//         Route::post('organization-center', [CmsController::class, 'organizationCenter']);
//         Route::post('organization-center-details', [CmsController::class, 'organizationCenterDetails']);
//         Route::post('members-privacy-policy', [CmsController::class, 'membersPrivacyPolicy']);
//     });
//     // donation
//     Route::post('donation', [DonationController::class, 'donation']);
//     Route::post('country-list', [DonationController::class, 'countryList']);
//     Route::post('login', [AuthController::class, 'login']);
//     Route::post('register', [AuthController::class, 'register']);
//     Route::post('register-agreement', [AuthController::class, 'registerAgreement']);
//     Route::post('forget-password', [ForgetPasswordController::class, 'forgetPassword']);

//     Route::group(['middleware' => ['auth:api'], 'prefix' => 'user'], function () {
//         Route::prefix('subscription')->group(function () {
//         Route::post('details', [SubscriptionController::class, 'details']);
//         Route::post('subscribe', [SubscriptionController::class, 'subscribe']);
//         // api.stripe.checkout.success
//         Route::get('stripe-checkout-success', [SubscriptionController::class, 'stripeCheckoutSuccess'])->name('api.stripe.checkout.success');
//         });
//         Route::middleware(['api.member.access'])->group(function () {
//             Route::post('profile', [ProfileController::class, 'profile']);
//             Route::post('update-profile', [ProfileController::class, 'updateProfile']);
//             Route::post('profile-picture-update', [ProfileController::class, 'profilePictureUpdate']);
//             Route::post('change-password', [ProfileController::class, 'changePassword']);
//         });
//     });
// });


