<?php

use App\Http\Controllers\Api\AuthController;
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



Route::prefix('v3')->group(function () {
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
        Route::post('organization-center', [CmsController::class, 'organizationCenter']);
        Route::post('organization-center-details', [CmsController::class, 'organizationCenterDetails']);
        Route::post('members-privacy-policy', [CmsController::class, 'membersPrivacyPolicy']);
    });
    // donation
    Route::post('donation', [DonationController::class, 'donation']);
    Route::post('country-list', [DonationController::class, 'countryList']);

    Route::post('register', [AuthController::class, 'register']);
    Route::post('login', [AuthController::class, 'login']);
    Route::post('register-agreement', [AuthController::class, 'registerAgreement']);
    Route::post('forget-password', [ForgetPasswordController::class, 'forgetPassword']);

    Route::group(['middleware' => ['auth:api', 'user'], 'prefix' => 'user'], function () {
        Route::post('profile', [ProfileController::class, 'profile']);
        Route::post('update-profile', [ProfileController::class, 'updateProfile']);
        Route::post('profile-picture-update', [ProfileController::class, 'profilePictureUpdate']);
        Route::post('change-password', [ProfileController::class, 'changePassword']);
        Route::post('check-role-permission', [ProfileController::class, 'checkUserHasPermission']);


        Route::prefix('chats')->name('chats.')->group(function () {
            Route::post('/list', [ChatController::class, 'chats']);
            Route::post('/load', [ChatController::class, 'load']);
            Route::post('/send', [ChatController::class, 'send']);
            Route::post('/clear', [ChatController::class, 'clear']);
            Route::post('/seen', [ChatController::class, 'seen']);
            Route::post('/remove', [ChatController::class, 'remove']);
            Route::post('/notification', [ChatController::class, 'notification']);
        });

        // Team Chat
        Route::prefix('team-chats')->name('team-chats.')->group(function () {
            Route::post('/list', [TeamChatController::class, 'list']);
            Route::post('/create', [TeamChatController::class, 'create']);
            Route::post('/load', [TeamChatController::class, 'load']);
            Route::post('/send', [TeamChatController::class, 'send']);
            Route::post('/group-info', [TeamChatController::class, 'groupInfo']);
            Route::post('/update-group-image', [TeamChatController::class, 'updateGroupImage']);
            Route::post('/name-des-update', [TeamChatController::class, 'nameDescriptionUpdate']);
            // Route::post('/remove-member', [TeamChatController::class, 'removeMember'])->name('remove-member');
            // Route::post('/group-list', [TeamChatController::class, 'groupList'])->name('group-list');
            // Route::post('/exit-from-group', [TeamChatController::class, 'exitFromGroup'])->name('exit-from-group');
            // Route::post('/add-member-team', [TeamChatController::class, 'addMemberTeam'])->name('add-member-team');
            // Route::post('/delete-group', [TeamChatController::class, 'deleteGroup'])->name('delete-group');
            // Route::post('/make-admin', [TeamChatController::class, 'makeAdmin'])->name('make-admin');
            // Route::post('/seen', [TeamChatController::class, 'seen'])->name('seen');
            // Route::post('/notification', [TeamChatController::class, 'notification'])->name('notification');
            // Route::post('/remove-chat', [TeamChatController::class, 'removeChat'])->name('remove-chat');
            // // clear-all-conversation
            // Route::post('/clear-all-conversation', [TeamChatController::class, 'clearAllConversation'])->name('clear-all-conversation');
        });

        //
    });
});
