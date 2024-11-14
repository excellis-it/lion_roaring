<?php

use App\Http\Controllers\Api\AuthController;
use App\Http\Controllers\Api\BecomingChristLikeController;
use App\Http\Controllers\Api\BecomingSovereignController;
use App\Http\Controllers\Api\BulletinController;
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
use App\Http\Controllers\Api\EstoreProductController;
use App\Http\Controllers\Api\EventController;
use App\Http\Controllers\Api\JobController;
use App\Http\Controllers\Api\LeadershipDevelopmentController;
use App\Http\Controllers\Api\MeetingController;
use App\Http\Controllers\Api\PartnerController;
use App\Http\Controllers\Api\RolePermissionsController;
use App\Http\Controllers\Api\StrategyController;
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

    Route::post('register-ecclesi-list', [AuthController::class, 'ecclesiList']);
    Route::post('register-country-list', [AuthController::class, 'countryList']);
    Route::post('register-states-list', [AuthController::class, 'getStates']);

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
            Route::post('/remove-member', [TeamChatController::class, 'removeMember']);
            Route::post('/add-member-team', [TeamChatController::class, 'addMemberTeam']);
            Route::post('/exit-from-group', [TeamChatController::class, 'exitFromGroup']);
            Route::post('/delete-group', [TeamChatController::class, 'deleteGroup']);
            Route::post('/make-admin', [TeamChatController::class, 'makeAdmin']);
            Route::post('/seen', [TeamChatController::class, 'seen']);
            Route::post('/remove-chat', [TeamChatController::class, 'removeChat']);
            Route::post('/clear-all-conversation', [TeamChatController::class, 'clearAllConversation']);
            Route::post('/notification', [TeamChatController::class, 'notification']);
        });


        Route::prefix('mail')->group(function () {

            Route::post('/inbox-email-list', [EmailController::class, 'inboxEmailList']);
            Route::post('/sent-email-list', [EmailController::class, 'sentEmailList']);
            Route::post('/star-email-list', [EmailController::class, 'starEmailList']);
            Route::post('/trash-email-list', [EmailController::class, 'trashEmailList']);

            Route::post('/view', [EmailController::class, 'view']);

            Route::post('/compose-mail-users', [EmailController::class, 'composeMailUsers']);
            Route::post('/send', [EmailController::class, 'sendMail']);
            Route::post('/sendReply', [EmailController::class, 'sendMailReply']);
            Route::post('/sendForward', [EmailController::class, 'sendMailForward']);

            Route::post('/mail-delete', [EmailController::class, 'delete']);
            Route::post('/mail-delete-sent', [EmailController::class, 'deleteSentsMail']);
            Route::post('/mail-restore', [EmailController::class, 'restore']);
            Route::post('/mail-star', [EmailController::class, 'star']);

            Route::post('/mail-delete-single', [EmailController::class, 'deleteSingleMail']);
            Route::post('/mail-restore-single', [EmailController::class, 'restoreSingleMail']);

            Route::get('/print/{id}', [EmailController::class, 'printMail']);
        });


        Route::resources([
            // 'roles' => RolePermissionsController::class,
            // 'partners' => PartnerController::class,
            'topics' => TopicController::class,
            // 'categories' => CategoryController::class,
            // 'products' => ProductController::class,
            // 'ecclesias' => EcclesiaContorller::class,
        ]);

        Route::prefix('becoming-sovereign')->group(function () {
            Route::get('/', [BecomingSovereignController::class, 'index']);
            Route::get('/list-by-topics', [BecomingSovereignController::class, 'listByTopic']);
            Route::get('/topics', [BecomingSovereignController::class, 'topics']);
            Route::post('/store', [BecomingSovereignController::class, 'store']);
            Route::get('/view/{id}', [BecomingSovereignController::class, 'view']);
            Route::post('/update/{id}', [BecomingSovereignController::class, 'update']);
            Route::get('/delete/{id}', [BecomingSovereignController::class, 'delete']);
            Route::get('/download/{file}', [BecomingSovereignController::class, 'download']);
        });

        Route::prefix('becoming-christ-link')->group(function () {
            Route::get('/', [BecomingChristLikeController::class, 'index']);
            Route::get('/list-by-topics', [BecomingChristLikeController::class, 'listByTopic']);
            Route::get('/topics', [BecomingChristLikeController::class, 'topics']);
            Route::post('/store', [BecomingChristLikeController::class, 'store']);
            Route::get('/view/{id}', [BecomingChristLikeController::class, 'view']);
            Route::post('/update/{id}', [BecomingChristLikeController::class, 'update']);
            Route::get('/delete/{id}', [BecomingChristLikeController::class, 'delete']);
            Route::get('/download/{file}', [BecomingChristLikeController::class, 'download']);
        });

        Route::prefix('leadership-development')->group(function () {
            Route::get('/', [LeadershipDevelopmentController::class, 'index']);
            Route::get('/list-by-topics', [LeadershipDevelopmentController::class, 'listByTopic']);
            Route::get('/topics', [LeadershipDevelopmentController::class, 'topics']);
            Route::post('/store', [LeadershipDevelopmentController::class, 'store']);
            Route::get('/view/{id}', [LeadershipDevelopmentController::class, 'view']);
            Route::post('/update/{id}', [LeadershipDevelopmentController::class, 'update']);
            Route::get('/delete/{id}', [LeadershipDevelopmentController::class, 'delete']);
            Route::get('/download/{file}', [LeadershipDevelopmentController::class, 'download']);
        });


        Route::prefix('bulletins')->group(function () {
            Route::get('/load', [BulletinController::class, 'index']);
            Route::get('/view/{id}', [BulletinController::class, 'show']);
            Route::get('/board', [BulletinController::class, 'allBulletins']);
            Route::post('/store', [BulletinController::class, 'store']);
            Route::put('/edit/{id}', [BulletinController::class, 'update']);
            Route::delete('/delete/{id}', [BulletinController::class, 'destroy']);
        });

        Route::prefix('jobs')->group(function () {
            Route::get('/load', [JobController::class, 'index']);
            Route::get('/view/{id}', [JobController::class, 'show']);
        });


        Route::prefix('meetings')->group(function () {
            Route::get('/load', [MeetingController::class, 'index']);
            Route::post('/store', [MeetingController::class, 'store']);
            Route::get('/view/{id}', [MeetingController::class, 'show']);
            Route::put('/edit/{id}', [MeetingController::class, 'update']);
            Route::delete('/delete/{id}', [MeetingController::class, 'destroy']);
            Route::get('/meetings-calender-fetch-data', [MeetingController::class, 'fetchCalenderData']);
        });

        Route::prefix('events')->group(function () {
            Route::get('/load', [EventController::class, 'index']);
            Route::post('/store', [EventController::class, 'store']);
            Route::get('/view/{id}', [EventController::class, 'show']);
            Route::put('/edit/{id}', [EventController::class, 'update']);
            Route::delete('/delete/{id}', [EventController::class, 'destroy']);
            Route::get('/event-calender-fetch-data', [EventController::class, 'fetchCalenderData']);
        });


        Route::prefix('strategy')->group(function () {
            Route::get('/load', [StrategyController::class, 'index']);
            Route::post('/store', [StrategyController::class, 'store']);
            Route::get('/delete/{id}', [StrategyController::class, 'delete']);
            Route::get('/download/{id}', [StrategyController::class, 'download']);
            Route::get('/view/{id}', [StrategyController::class, 'view']);
        });


        Route::prefix('partners')->group(function () {
            Route::get('/list', [PartnerController::class, 'list']);
        });

        Route::prefix('roles')->group(function () {
            Route::get('/list', [RolePermissionsController::class, 'list']);
            Route::put('/edit/{id}', [RolePermissionsController::class, 'edit']);
        });


        Route::prefix('e-store')->group(function () {
            Route::get('/all-products', [EstoreProductController::class, 'products']);
        });





        //
    });
});
