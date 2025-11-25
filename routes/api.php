<?php

use App\Http\Controllers\Api\AuthController;
use App\Http\Controllers\Api\BecomingChristLikeController;
use App\Http\Controllers\Api\BecomingSovereignController;
use App\Http\Controllers\Api\BulletinController;
use App\Http\Controllers\Api\ChatBotController;
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
use App\Http\Controllers\Api\ColorController;
use App\Http\Controllers\Api\TeamChatController;
use App\Http\Controllers\Api\EmailController;
use App\Http\Controllers\Api\EstoreProductController;
use App\Http\Controllers\Api\ElearningController;
use App\Http\Controllers\Api\EventController;
use App\Http\Controllers\Api\JobController;
use App\Http\Controllers\Api\LeadershipDevelopmentController;
use App\Http\Controllers\Api\MeetingController;
use App\Http\Controllers\Api\PartnerController;
use App\Http\Controllers\Api\RolePermissionsController;
use App\Http\Controllers\Api\StrategyController;
use App\Http\Controllers\Api\TopicController;
use App\Http\Controllers\Api\FileController;
use App\Http\Controllers\Api\PolicyGuidenceController;
use App\Http\Controllers\Api\RolePermissionController;
use App\Http\Controllers\Api\EcclesiaController;
use App\Http\Controllers\Api\EstoreCmsController;
use App\Http\Controllers\Api\EstoreController;
use App\Http\Controllers\Api\FCMController;
use App\Http\Controllers\Api\SizeController;
use App\Http\Controllers\Api\PrivateCollaborationController;
use App\Http\Controllers\Api\UserActivityController;

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

Route::prefix('v3')->middleware(['userActivity'])->group(function () {
    Route::post('contact-us', [ContactUsController::class, 'store']);

    Route::prefix('e-learning')->group(function () {
        // Route::get('/all-products', [EstoreProductController::class, 'products']);
        Route::get('/store-home', [ElearningController::class, 'storeHome']);
        Route::get('/category-products/{slug}', [ElearningController::class, 'productsByCategorySlug']);
        Route::get('/product/{slug}', [ElearningController::class, 'productDetails']);
        Route::get('/products-filter', [ElearningController::class, 'productsFilter']);
    });


    // Public E-Store (Ecom) APIs — Home, header/footer, menu, newsletter
    Route::prefix('e-store')->group(function () {
        // Public home endpoint that returns home CMS content and featured/new products
        Route::get('/store-home', [EstoreController::class, 'storeHome']);
        // Header (logo & menu categories)
        Route::get('/header', [EstoreController::class, 'header']);
        // Footer CMS
        Route::get('/footer', [EstoreController::class, 'footer']);
        // Newsletter subscribe (public)
        Route::post('/newsletter', [EstoreController::class, 'newsletterStore']);
    });


    Route::prefix('cms')->group(function () {
        Route::post('home', [CmsController::class, 'home']);
        Route::post('gallery', [CmsController::class, 'gallery']);
        Route::post('contact-us', [CmsController::class, 'contactUs']);
        Route::post('about-us-page', [CmsController::class, 'aboutUs']);
        Route::post('details-page', [CmsController::class, 'detailsPage']);
        Route::get('contact-us-page', [ContactUsController::class, 'contactUs']);
        Route::post('faq', [CmsController::class, 'faq']);
        Route::post('principle-and-business', [CmsController::class, 'principleAndBusiness']);
        Route::post('ecclesia-associations', [CmsController::class, 'ecclesiaAssociations']);
        Route::post('our-organization', [CmsController::class, 'ourOrganization']);
        Route::post('common', [CmsController::class, 'common']);
        Route::post('our-governance', [CmsController::class, 'ourGovernance']);
        Route::post('organization-center', [CmsController::class, 'organizationCenter']);
        Route::post('organization-center-details', [CmsController::class, 'organizationCenterDetails']);
        Route::post('members-privacy-policy', [CmsController::class, 'membersPrivacyPolicy']);

        Route::post('pma-disclaimer-policy', [CmsController::class, 'pmaDisclaimerPolicy']);
        Route::post('privacy-policy', [CmsController::class, 'privacy_policy']);
        Route::post('terms-and-conditions', [CmsController::class, 'terms']);
        Route::post('article-of-association', [CmsController::class, 'article_of_association']);
        Route::post('newsletter', [CmsController::class, 'newsletter']);
        // site settings
        Route::get('site-settings', [CmsController::class, 'siteSettings']);
    });

    // donation
    Route::post('donation', [DonationController::class, 'donation']);
    Route::post('country-list', [DonationController::class, 'countryList']);
    // getCountryById
    Route::post('country-by-id', [AuthController::class, 'getCountryById']);

    Route::post('register-ecclesi-list', [AuthController::class, 'ecclesiList']);
    Route::post('register-country-list', [AuthController::class, 'countryList']);
    Route::post('register-states-list', [AuthController::class, 'getStates']);

    Route::post('register', [AuthController::class, 'register']);
    Route::post('login', [AuthController::class, 'login']);
    Route::post('verify-otp', [AuthController::class, 'verifyOtp']);
    Route::post('register-agreement', [AuthController::class, 'registerAgreement']);
    Route::post('forget-password', [ForgetPasswordController::class, 'forgetPassword']);
    // logout




    Route::group(['middleware' => ['auth:api', 'user'], 'prefix' => 'user'], function () {
        Route::post('create-partner', [PartnerController::class, 'storePartner']);
        Route::post('profile', [ProfileController::class, 'profile']);
        Route::post('chatbot', [ChatBotController::class, 'chatbot']);
        Route::post('update-profile', [ProfileController::class, 'updateProfile']);
        Route::post('profile-picture-update', [ProfileController::class, 'profilePictureUpdate']);
        Route::post('change-password', [ProfileController::class, 'changePassword']);
        Route::post('check-role-permission', [ProfileController::class, 'checkUserHasPermission']);

        Route::post('check-menu-permission', [ProfileController::class, 'checkUserMenuPermission']);

        Route::get('/notifications', [ProfileController::class, 'notifications'])->name('notification.list');
        Route::get('/notification-read/{type}/{id}', [ProfileController::class, 'notificationRead'])->name('notification.read');
        // notification.clear
        Route::get('/notification-clear', [ProfileController::class, 'notificationClear'])->name('notification.clear');

        // Get total unread messages count mail,chat,team-chat
        Route::get('unread-messages-count', [ProfileController::class, 'unreadMessagesCount'])->name('unread.messages.count');

        // Update Fcm Token
        Route::post('update-fcm-token', [ProfileController::class, 'updateFcmToken']);

        Route::post('logout', [AuthController::class, 'logout']);

        Route::prefix('sizes')->group(function () {
            Route::get('/', [SizeController::class, 'index']);
            Route::post('/store', [SizeController::class, 'store']);
            Route::post('/edit', [SizeController::class, 'edit']);
            Route::post('/update', [SizeController::class, 'update']);
            Route::post('/delete', [SizeController::class, 'delete']);
        });

        Route::prefix('colors')->group(function () {
            Route::get('/', [ColorController::class, 'index']);
            Route::post('/store', [ColorController::class, 'store']);
            Route::post('/edit', [SizeController::class, 'edit']);
            Route::post('/update', [ColorController::class, 'update']);
            Route::post('/delete', [ColorController::class, 'delete']);
        });


        Route::prefix('estore-cms')->group(function () {
            Route::post('/dashboard', [EstoreCmsController::class, 'dashboard']);
            Route::post('/list', [EstoreCmsController::class, 'list']);
            Route::post('/store', [EstoreCmsController::class, 'store']);
            Route::put('/update/{id}', [EstoreCmsController::class, 'update']);
            Route::post('/delete', [EstoreCmsController::class, 'delete']);
            Route::post('/store-cms-page', [EstoreCmsController::class, 'cms']);
            Route::post('/home/update', [EstoreCmsController::class, 'homeCmsUpdate']);
            Route::post('/footer/update', [EstoreCmsController::class, 'footerUpdate']);
            Route::get('/contact', [EstoreCmsController::class, 'contactCms']);
            Route::post('/contact/update', [EstoreCmsController::class, 'contactCmsUpdate']);
        });


        Route::prefix('chats')->name('chats.')->group(function () {
            Route::post('/list', [ChatController::class, 'chats']);
            Route::post('/load', [ChatController::class, 'load']);
            Route::post('/send', [ChatController::class, 'send']);
            Route::post('/clear', [ChatController::class, 'clear']);
            Route::post('/seen', [ChatController::class, 'seen']);
            Route::post('/remove', [ChatController::class, 'remove']);
            Route::post('/notification', [ChatController::class, 'notification']);
            // search api
            Route::post('/search', [ChatController::class, 'search']);
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
            Route::post('/mail-delete-single-trash', [EmailController::class, 'deleteSingleTrashMail']);
            Route::post('/mail-restore-single', [EmailController::class, 'restoreSingleMail']);

            Route::post('/mail-trash-empty', [EmailController::class, 'trashEmpty']);

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

        Route::prefix('topics')->group(function () {
            Route::get('/delete/{id}', [TopicController::class, 'delete']);
        });

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

        Route::prefix('becoming-christ-like')->group(function () {
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

        Route::prefix('files')->group(function () {
            Route::get('/', [FileController::class, 'index']);
            Route::get('/list-by-topics', [FileController::class, 'listByTopic']);
            Route::get('/topics', [FileController::class, 'topics']);
            Route::post('/store', [FileController::class, 'store']);
            Route::get('/view/{id}', [FileController::class, 'view']);
            Route::post('/update/{id}', [FileController::class, 'update']);
            Route::get('/delete/{id}', [FileController::class, 'delete']);
            Route::get('/download/{file}', [FileController::class, 'download']);
        });


        Route::prefix('bulletins')->group(function () {
            Route::get('/load', [BulletinController::class, 'index']);
            Route::get('/view/{id}', [BulletinController::class, 'show']);
            Route::get('/board', [BulletinController::class, 'allBulletins']);
            Route::post('/store', [BulletinController::class, 'store']);
            Route::put('/edit/{id}', [BulletinController::class, 'update']);
            Route::post('/delete/{id}', [BulletinController::class, 'destroy']);
        });

        Route::prefix('jobs')->group(function () {
            Route::get('/load', [JobController::class, 'index']);
            Route::get('/view/{id}', [JobController::class, 'show']);
            Route::post('/store', [JobController::class, 'store']);
            Route::post('/edit/{id}', [JobController::class, 'update']);
            Route::post('/delete/{id}', [JobController::class, 'delete']);
            Route::post('/search', [JobController::class, 'search']);
        });


        Route::prefix('meetings')->group(function () {
            Route::get('/load', [MeetingController::class, 'index']);
            Route::post('/store', [MeetingController::class, 'store']);
            Route::get('/view/{id}', [MeetingController::class, 'show']);
            Route::post('/edit/{id}', [MeetingController::class, 'update']);
            Route::post('/delete/{id}', [MeetingController::class, 'destroy']);
            // Zoom SDK signature for API clients
            Route::post('/zoom-signature', [MeetingController::class, 'zoomSignature']);
            Route::get('/meetings-calender-fetch-data', [MeetingController::class, 'fetchCalenderData']);
        });

        // User activity APIs
        Route::prefix('user-activity')->group(function () {
            // Log an activity (requires auth)
            Route::post('/log', [UserActivityController::class, 'log']);
            // List activities (requires 'Manage User Activity' permission)
            Route::post('/list', [UserActivityController::class, 'list']);
            // Summary endpoints
            Route::post('/by-country', [UserActivityController::class, 'byCountry']);
            Route::post('/by-user', [UserActivityController::class, 'byUser']);
            Route::post('/by-type', [UserActivityController::class, 'byType']);
        });

        Route::prefix('private-collaborations')->group(function () {
            Route::get('/load', [PrivateCollaborationController::class, 'index']);
            Route::post('/store', [PrivateCollaborationController::class, 'store']);
            Route::get('/view/{id}', [PrivateCollaborationController::class, 'show']);
            Route::post('/edit/{id}', [PrivateCollaborationController::class, 'update']);
            Route::post('/delete/{id}', [PrivateCollaborationController::class, 'destroy']);
            Route::post('/accept-invitation/{id}', [PrivateCollaborationController::class, 'acceptInvitation']);
            Route::post('/zoom-signature', [PrivateCollaborationController::class, 'zoomSignature']);
            Route::get('/private-collaborations-calender-fetch-data', [PrivateCollaborationController::class, 'fetchCalenderData']);
        });

        Route::prefix('events')->group(function () {
            Route::get('/load', [EventController::class, 'index']);
            Route::post('/store', [EventController::class, 'store']);
            Route::get('/view/{id}', [EventController::class, 'show']);
            Route::post('/edit/{id}', [EventController::class, 'update']);
            Route::post('/delete/{id}', [EventController::class, 'destroy']);
            Route::get('/event-calender-fetch-data', [EventController::class, 'fetchCalenderData']);
        });


        Route::prefix('strategy')->group(function () {
            Route::get('/load', [StrategyController::class, 'index']);
            Route::post('/store', [StrategyController::class, 'store']);
            Route::get('/delete/{id}', [StrategyController::class, 'delete']);
            Route::get('/download/{id}', [StrategyController::class, 'download']);
            Route::get('/view/{id}', [StrategyController::class, 'view']);
        });

        Route::prefix('policy')->group(function () {
            Route::get('/load', [PolicyGuidenceController::class, 'index']);
            Route::post('/store', [PolicyGuidenceController::class, 'store']);
            Route::get('/delete/{id}', [PolicyGuidenceController::class, 'delete']);
            Route::get('/download/{id}', [PolicyGuidenceController::class, 'download']);
            Route::get('/view/{id}', [PolicyGuidenceController::class, 'view']);
        });


        Route::prefix('partners')->group(function () {
            Route::get('/list', [PartnerController::class, 'list']);
            Route::get('/create-form-data', [PartnerController::class, 'loadCreateData']);
            // Route::post('/store', [PartnerController::class, 'storePartner']);
            Route::get('/view/{id}', [PartnerController::class, 'viewPartner']);
            Route::post('/update/{id}', [PartnerController::class, 'updatePartner']);
            Route::post('/delete/{id}', [PartnerController::class, 'deletePartner']);
            // changePartnerStatus
            Route::post('/change-status', [PartnerController::class, 'changePartnerStatus']);
        });

        Route::prefix('roles')->group(function () {
            Route::get('/list', [RolePermissionsController::class, 'list']);
            Route::post('/edit/{id}', [RolePermissionsController::class, 'edit']);
        });


        // Route::prefix('e-store')->group(function () {
        //     Route::get('/all-products', [EstoreProductController::class, 'products']);
        //     Route::get('/store-home', [EstoreProductController::class, 'storeHome']);
        //     Route::get('/product/{id}', [EstoreProductController::class, 'productDetails']);
        // });


        Route::prefix('role-permissions')->group(function () {
            Route::get('/', [RolePermissionController::class, 'index']);
            Route::get('/permissions', [RolePermissionController::class, 'getAllPermissions']);
            Route::get('/{id}', [RolePermissionController::class, 'show']);
            Route::post('/', [RolePermissionController::class, 'store']);
            Route::put('/{id}', [RolePermissionController::class, 'update']);
            Route::delete('/{id}', [RolePermissionController::class, 'destroy']);
        });

        // Ecclesia routes
        Route::prefix('ecclesias-manage')->group(function () {
            Route::get('/', [EcclesiaController::class, 'index']);
            Route::get('/create', [EcclesiaController::class, 'create']);
            Route::post('/', [EcclesiaController::class, 'store']);
            Route::get('/{id}', [EcclesiaController::class, 'show']);
            Route::put('/{id}', [EcclesiaController::class, 'update']);
            Route::delete('/{id}', [EcclesiaController::class, 'destroy']);
        });




        // FCM Routes
        Route::prefix('fcm')->group(function () {
            Route::post('update-token', [FCMController::class, 'updateToken']);
            Route::post('remove-token', [FCMController::class, 'removeToken']);
            Route::post('test-notification', [FCMController::class, 'sendTestNotification']);
        });
    });
});
