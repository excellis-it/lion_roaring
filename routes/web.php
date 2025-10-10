<?php

use App\Http\Controllers\Admin\AboutUsController;
use Illuminate\Support\Facades\Route;
use App\Http\Controllers\Admin\AuthController;
use App\Http\Controllers\Admin\ForgetPasswordController;
use App\Http\Controllers\Admin\DashboardController;
use App\Http\Controllers\Admin\AdminController;
use App\Http\Controllers\Admin\ArticleOfAssociationController;
use App\Http\Controllers\Admin\ContactUsCmsController;
use App\Http\Controllers\Admin\ContactusController;
use App\Http\Controllers\Admin\ProfileController;
use App\Http\Controllers\Admin\CustomerController;
use App\Http\Controllers\Admin\DetailsController;
use App\Http\Controllers\Admin\DonationController as AdminDonationController;
use App\Http\Controllers\Admin\EcclesiaAssociationController;
use App\Http\Controllers\Admin\EcclessiaController;
use App\Http\Controllers\Admin\FaqController;
use App\Http\Controllers\Admin\FooterController;
use App\Http\Controllers\Admin\GalleryController;
use App\Http\Controllers\Admin\HomeCmsController;
use App\Http\Controllers\Admin\MemberController;
use App\Http\Controllers\Admin\MemberPrivacyPolicyContoller;
use App\Http\Controllers\Admin\NewsletterController;
use App\Http\Controllers\Admin\OrganizationCenterController;
use App\Http\Controllers\Admin\OrganizationController;
use App\Http\Controllers\Admin\OurGovernanceController;
use App\Http\Controllers\Admin\OurOrganizationController;
use App\Http\Controllers\Admin\PlanController;
use App\Http\Controllers\Admin\PmaDisclaimerController;
use App\Http\Controllers\Admin\PrincipleAndBusinessController;
use App\Http\Controllers\Admin\PrivacyPolicyController;
use App\Http\Controllers\Admin\RegisterAgreementController;
use App\Http\Controllers\Admin\RolePermissionController;
use App\Http\Controllers\Admin\SellerController;
use App\Http\Controllers\Admin\ServiceContoller;
use App\Http\Controllers\Admin\TestimonialController;
use App\Http\Controllers\Estore\HomeController;
use App\Http\Controllers\Estore\ProductController as EstoreProductController;
use App\Http\Controllers\Elearning\ElearningHomeController;
use App\Http\Controllers\Elearning\ElearningProductController as ElearningProductController;
use App\Http\Controllers\Frontend\CmsController;
use App\Http\Controllers\Frontend\DonationController;
use App\Http\Controllers\User\AuthController as UserAuthController;
use App\Http\Controllers\User\BecomingChristLikeController;
use App\Http\Controllers\User\BecomingSovereignController;
use App\Http\Controllers\User\BulletinBoardController;
use App\Http\Controllers\User\BulletinController;
use App\Http\Controllers\User\CategoryController;
use App\Http\Controllers\User\ElearningCategoryController;
use App\Http\Controllers\User\ChatController;
use App\Http\Controllers\User\EstoreCmsController;
use App\Http\Controllers\User\ElearningCmsController;
use App\Http\Controllers\User\DashboardController as UserDashboardController;
use App\Http\Controllers\User\EcclesiaContorller;
use App\Http\Controllers\User\FileController;
use App\Http\Controllers\User\ForgetPasswordController as UserForgetPasswordController;
use App\Http\Controllers\User\JobpostingController;
use App\Http\Controllers\User\LeadershipDevelopmentController;
use App\Http\Controllers\User\LiveEventController;
use App\Http\Controllers\User\MeetingSchedulingController;
use App\Http\Controllers\User\NewsletterController as UserNewsletterController;
use App\Http\Controllers\User\PartnerController;
use App\Http\Controllers\User\ProductController;
use App\Http\Controllers\User\ElearningController;
use App\Http\Controllers\User\RolePermissionsController;
use App\Http\Controllers\User\SendMailController;
use App\Http\Controllers\User\StrategyController;
use App\Http\Controllers\User\SubscriptionController;
use App\Http\Controllers\User\TeamChatController;
use App\Http\Controllers\User\TeamController;
use App\Http\Controllers\User\TopicController;
use App\Http\Controllers\Admin\SettingsController;
use App\Http\Controllers\Admin\TermsAndConditionController;
use App\Models\Category;
use App\Models\ElearningCategory;
use App\Models\EcomCmsPage;
use App\Models\ElearningEcomCmsPage;
use Illuminate\Routing\RouteRegistrar;
use Illuminate\Support\Facades\Artisan;
use App\Http\Controllers\User\ChatBotController;
use App\Http\Controllers\User\EmailVerificationController;
use App\Http\Controllers\User\PolicyGuidenceController;
use App\Http\Controllers\User\ColorController;
use App\Http\Controllers\User\SizeController;
use App\Http\Controllers\User\EstorePromoCodeController;
use App\Http\Controllers\User\EstoreSettingController;
use App\Http\Controllers\User\WareHouseController;
use App\Http\Controllers\User\WarehouseAdminController;



/*
|--------------------------------------------------------------------------
| Web Routes
|--------------------------------------------------------------------------
|
| Here is where you can register web routes for your application. These
| routes are loaded by the RouteServiceProvider within a group which
| contains the "web" middleware group. Now create something great!
|
*/

// Clear cache
Route::get('clear', function () {
    Artisan::call('optimize:clear');
    return "Optimize clear has been successfully";
});

// make migration
Route::get('dbmigrate', function () {
    Artisan::call('migrate');
    return "Migration has been successfully";
});



Route::get('/admin', [AuthController::class, 'redirectAdminLogin']);
Route::get('/admin/login', [AuthController::class, 'login'])->name('admin.login');
Route::post('/admin-login-check', [AuthController::class, 'loginCheck'])->name('admin.login.check');  //login check
Route::post('admin-forget-password', [ForgetPasswordController::class, 'forgetPassword'])->name('admin.forget.password');
Route::post('admin-change-password', [ForgetPasswordController::class, 'changePassword'])->name('admin.change.password');
Route::get('admin-forget-password/show', [ForgetPasswordController::class, 'forgetPasswordShow'])->name('admin.forget.password.show');
Route::get('admin-reset-password/{id}/{token}', [ForgetPasswordController::class, 'resetPassword'])->name('admin.reset.password');
Route::post('admin-change-password', [ForgetPasswordController::class, 'changePassword'])->name('admin.change.password');

Route::group(['middleware' => ['admin'], 'prefix' => 'admin'], function () {
    Route::get('dashboard', [DashboardController::class, 'index'])->name('admin.dashboard');
    Route::get('profile', [ProfileController::class, 'index'])->name('admin.profile');
    Route::post('profile/update', [ProfileController::class, 'profileUpdate'])->name('admin.profile.update');
    Route::get('logout', [AuthController::class, 'logout'])->name('admin.logout');

    Route::prefix('password')->group(function () {
        Route::get('/', [ProfileController::class, 'password'])->name('admin.password'); // password change
        Route::post('/update', [ProfileController::class, 'passwordUpdate'])->name('admin.password.update'); // password update
    });

    Route::get('settings', [SettingsController::class, 'edit'])->name('admin.settings.edit');
    Route::post('settings', [SettingsController::class, 'update'])->name('admin.settings.update');

    // admin index
    Route::prefix('detail')->group(function () {
        Route::get('/', [AdminController::class, 'index'])->name('admin.index');
        Route::get('/add', [AdminController::class, 'add'])->name('admin.add');
        Route::post('/store', [AdminController::class, 'store'])->name('admin.store');
        Route::post('/edit/{id}', [AdminController::class, 'edit'])->name('admin.edit');
        Route::get('/delete/{id}', [AdminController::class, 'delete'])->name('admin.delete');
        Route::post('/update', [AdminController::class, 'update'])->name('admin.update');
    });

    // admin index
    Route::prefix('ecclessias')->group(function () {
        Route::get('/', [EcclessiaController::class, 'index'])->name('ecclessias.index');
        Route::post('/store', [EcclessiaController::class, 'store'])->name('ecclessias.store');
        Route::post('/edit/{id}', [EcclessiaController::class, 'edit'])->name('ecclessias.edit');
        Route::get('/delete/{id}', [EcclessiaController::class, 'delete'])->name('ecclessias.delete');
        Route::post('/update', [EcclessiaController::class, 'update'])->name('ecclessias.update');
    });

    Route::prefix('members')->group(function () {
        Route::get('/', [MemberController::class, 'index'])->name('members.index');
        Route::put('/accept/{id}', [MemberController::class, 'accept'])->name('members.accept');
        Route::put('/rejected/{id}', [MemberController::class, 'rejected'])->name('members.reject');
        Route::get('/rejected-view/{id}', [MemberController::class, 'rejectedView'])->name('members.reject-view');
        Route::get('/fetch-data', [MemberController::class, 'fetchData'])->name('members.fetch-data');
    });

    Route::resources([
        'customers' => CustomerController::class,
        'testimonials' => TestimonialController::class,
        'our-governances' => OurGovernanceController::class,
        'our-organizations' => OurOrganizationController::class,
        'organization-centers' => OrganizationCenterController::class,
        'services' => ServiceContoller::class,
        'donations' => AdminDonationController::class,
        'plans' => PlanController::class,
    ]);

    Route::name('admin.')->group(function () {
        Route::resource('roles', RolePermissionController::class);
    });

    Route::prefix('roles')->group(function () {
        Route::get('/role-delete/{id}', [RolePermissionsController::class, 'delete'])->name('admin.roles.delete');
    });

    Route::prefix('plans')->group(function () {
        Route::get('/plan-delete/{id}', [PlanController::class, 'delete'])->name('plans.delete');
    });
    Route::get('/changePlanStatus', [PlanController::class, 'changePlansStatus'])->name('plans.change-status');
    Route::get('/plan-fetch-data', [PlanController::class, 'fetchData'])->name('plans.fetch-data');

    Route::get('/donations-fetch-data', [AdminDonationController::class, 'fetchData'])->name('donations.fetch-data');
    Route::get('/donations-delete/{id}', [AdminDonationController::class, 'delete'])->name('donations.delete');

    Route::prefix('organization-centers')->group(function () {
        Route::get('/organization-center-delete/{id}', [OrganizationCenterController::class, 'delete'])->name('organization-centers.delete');
    });
    Route::get('/organization-centers-fetch-data', [OrganizationCenterController::class, 'fetchData'])->name('organization-centers.fetch-data');

    Route::prefix('our-organizations')->group(function () {
        Route::get('/our-organization-delete/{id}', [OurOrganizationController::class, 'delete'])->name('our-organizations.delete');
    });
    Route::get('/our-organizations-fetch-data', [OurOrganizationController::class, 'fetchData'])->name('our-organizations.fetch-data');

    Route::prefix('our-governances')->group(function () {
        Route::get('/our-governance-delete/{id}', [OurGovernanceController::class, 'delete'])->name('our-governances.delete');
    });
    Route::get('/our-governances-fetch-data', [OurGovernanceController::class, 'fetchData'])->name('our-governances.fetch-data');

    Route::prefix('testimonials')->group(function () {
        Route::get('/testimonials-delete/{id}', [TestimonialController::class, 'delete'])->name('testimonials.delete');
    });
    Route::get('/testimonials-fetch-data', [TestimonialController::class, 'fetchData'])->name('testimonials.fetch-data');

    //  Customer Routes
    Route::prefix('customers')->group(function () {
        Route::get('/customer-delete/{id}', [CustomerController::class, 'delete'])->name('customers.delete');
    });
    Route::get('/changeCustomerStatus', [CustomerController::class, 'changeCustomersStatus'])->name('customers.change-status');
    Route::get('/customer-fetch-data', [CustomerController::class, 'fetchData'])->name('customers.fetch-data');

    Route::prefix('pages')->group(function () {
        Route::resources([
            'faq' => FaqController::class,
            'gallery' => GalleryController::class,
            'ecclesia-associations' => EcclesiaAssociationController::class,
            'principle-and-business' => PrincipleAndBusinessController::class,
            'contact-us-cms' => ContactUsCmsController::class,
            'organizations' => OrganizationController::class,
            'about-us' => AboutUsController::class,
            'home-cms' => HomeCmsController::class,
            'details' => DetailsController::class,
            'contact-us' => ContactusController::class,
            'newsletters' => NewsletterController::class,
            'articles-of-association' => ArticleOfAssociationController::class,
            'register-agreements' => RegisterAgreementController::class,
            'members-privacy-policies' => MemberPrivacyPolicyContoller::class,
            'pma-terms' => PmaDisclaimerController::class,

        ]);

        // privacy-policy
        Route::get('/privacy-policy', [PrivacyPolicyController::class, 'index'])->name('privacy-policy.index');
        Route::post('/privacy-policy/update', [PrivacyPolicyController::class, 'update'])->name('privacy-policy.update');
        // terms-and-conditions
        Route::get('/terms-and-condition', [TermsAndConditionController::class, 'index'])->name('terms-and-condition.index');
        Route::post('/terms-and-condition/update', [TermsAndConditionController::class, 'update'])->name('terms-and-condition.update');

        // principle-and-business.image.delete
        Route::get('/principle-and-business-image-delete', [PrincipleAndBusinessController::class, 'imageDelete'])->name('principle-and-business.image.delete');
        Route::get('/newsletter-fetch-data', [NewsletterController::class, 'fetchData'])->name('newsletters.fetch-data');
        // delete newsletter
        Route::get('/newsletter-delete/{id}', [NewsletterController::class, 'delete'])->name('newsletters.delete');
        Route::get('/contact-us-fetch-data', [ContactusController::class, 'fetchData'])->name('contact-us.fetch-data');
        Route::get('/contact-us-delete/{id}', [ContactusController::class, 'delete'])->name('contact-us.delete');

        Route::get('/organizations-image-delete', [OrganizationController::class, 'imageDelete'])->name('organization.image.delete');

        Route::prefix('faq')->group(function () {
            Route::get('/faq-delete/{id}', [FaqController::class, 'delete'])->name('faq.delete');
        });
        Route::get('/faq-fetch-data', [FaqController::class, 'fetchData'])->name('faq.fetch-data');

        Route::prefix('gallery')->group(function () {
            Route::get('/gallery-delete/{id}', [GalleryController::class, 'delete'])->name('gallery.delete');
        });

        Route::prefix('footer')->name('footer.')->group(function () {
            Route::get('/', [FooterController::class, 'index'])->name('index');
            Route::post('/update', [FooterController::class, 'update'])->name('update');
        });
    });
});

/*************************************************************** Frontend ************************************************************************/
Route::get('/', [CmsController::class, 'index'])->name('home');
Route::get('/gallery', [CmsController::class, 'gallery'])->name('gallery');
Route::get('/faq', [CmsController::class, 'faq'])->name('faq');
Route::get('/contact-us', [CmsController::class, 'contactUs'])->name('contact-us');
Route::get('/account-delete-request', [CmsController::class, 'accountDeleteRequest'])->name('account-delete-request');
Route::get('/principle-and-business', [CmsController::class, 'principleAndBusiness'])->name('principle-and-business');
Route::get('/ecclesia-covenant', [CmsController::class, 'ecclesiaAssociations'])->name('ecclesia-associations');
Route::get('/organization', [CmsController::class, 'organization'])->name('organization');
Route::get('/service/{slug}', [CmsController::class, 'service'])->name('service');
Route::get('/our-organization/{slug}', [CmsController::class, 'ourOrganization'])->name('our-organization');
Route::get('/features/{slug}', [CmsController::class, 'features'])->name('features');
// our_governance
Route::get('/our-governance/{slug}', [CmsController::class, 'ourGovernance'])->name('our-governance');
Route::get('/about-us', [CmsController::class, 'aboutUs'])->name('about-us');
Route::get('/details', [CmsController::class, 'details'])->name('details');

Route::post('/newsletter', [CmsController::class, 'newsletter'])->name('newsletter');
Route::post('/contact-us', [CmsController::class, 'contactUsForm'])->name('contact-us.form');
Route::Post('/session', [CmsController::class, 'session'])->name('session.store');
Route::post('/donation', [DonationController::class, 'donation'])->name('donation');
Route::get('/thankyou', [DonationController::class, 'thankyou'])->name('thankyou');

// terms-and-conditions
Route::get('/terms-and-conditions', [CmsController::class, 'terms'])->name('terms-and-conditions');
// privacy-policy
Route::get('/privacy-policy', [CmsController::class, 'privacy_policy'])->name('privacy-policy');

/*********************************************************** USER ********************************************************************************************* */
// login
Route::get('/login', [UserAuthController::class, 'login'])->name('login');
Route::post('/login-check', [UserAuthController::class, 'loginCheck'])->name('login.check');  //login check

// register
Route::get('/register', [UserAuthController::class, 'register'])->name('register');
Route::post('/register-check', [UserAuthController::class, 'registerCheck'])->name('register.check');  //register check
Route::post('forget-password', [UserForgetPasswordController::class, 'forgetPassword'])->name('user.forget.password');
Route::post('password-change', [UserForgetPasswordController::class, 'changePassword'])->name('user.password-change');
Route::get('forget-password/show', [UserForgetPasswordController::class, 'forgetPasswordShow'])->name('user.forget.password.show');
Route::get('reset-password/{id}/{token}', [UserForgetPasswordController::class, 'resetPassword'])->name('user.reset.password');
// user.forget.username.show
Route::get('forget-username/show', [UserForgetPasswordController::class, 'forgetUsernameShow'])->name('user.forget.username.show');
Route::post('forget-username', [UserForgetPasswordController::class, 'forgetUsername'])->name('user.forget.username');
// show confirmation email page
Route::get('/confirmation-email/{id}', [UserForgetPasswordController::class, 'confirmationEmail'])->name('forget-username-confirmation');
Route::get('reset-username/{id}/{token}', [UserForgetPasswordController::class, 'resetUsername'])->name('user.reset.username');
// user.username-change
Route::post('username-change', [UserForgetPasswordController::class, 'changeUsername'])->name('user.username-change');

Route::get('/member-privacy-policy', [EstoreCmsController::class, 'memberPrivacyPolicy'])->name('member-privacy-policy');

// get.states
Route::get('/get-states', [UserAuthController::class, 'getStates'])->name('get.states');

Route::post('/send-otp', [EmailVerificationController::class, 'sendOtp'])->name('send.otp');
Route::post('/verify-otp', [EmailVerificationController::class, 'verifyOtp'])->name('verify.otp');
Route::post('/resend-otp', [EmailVerificationController::class, 'resendOtp'])->name('resend.otp');

Route::prefix('user')->middleware(['user', 'preventBackHistory'])->group(function () {
    Route::get('/subscription', [SubscriptionController::class, 'subscription'])->name('user.subscription');
    Route::get('/subscription-payment/{id}', [SubscriptionController::class, 'payment'])->name('user.subscription.payment');
    Route::get('/stripe-checkout-success', [SubscriptionController::class, 'stripeCheckoutSuccess'])->name('stripe.checkout.success');

    // Route::middleware(['member.access'])->group(function () {
    // Route::get('/dashboard', [UserDashboardController::class, 'dashboard'])->name('user.dashboard');
    Route::get('/profile', [UserDashboardController::class, 'profile'])->name('user.profile');
    Route::post('/profile-update', [UserDashboardController::class, 'profileUpdate'])->name('user.profile.update');
    Route::get('/change-password', [UserDashboardController::class, 'password'])->name('user.change.password');
    Route::post('/change-password-update', [UserDashboardController::class, 'passwordUpdate'])->name('user.password.update');
    Route::get('/notifications', [UserDashboardController::class, 'notifications'])->name('notification.list');
    Route::get('/notification-read/{type}/{id}', [UserDashboardController::class, 'notificationRead'])->name('notification.read');
    // notification.clear
    Route::get('/notification-clear', [UserDashboardController::class, 'notificationClear'])->name('notification.clear');


    Route::get('unread-messages-count', [UserDashboardController::class, 'unreadMessagesCount'])->name('unread.messages.count');

    Route::get('/logout', [UserAuthController::class, 'logout'])->name('logout');

    Route::prefix('chats')->name('chats.')->group(function () {
        Route::get('/', [ChatController::class, 'chats'])->name('index');
        Route::post('/load', [ChatController::class, 'load'])->name('load');
        Route::post('/send', [ChatController::class, 'send'])->name('send');
        Route::post('/clear', [ChatController::class, 'clear'])->name('clear');
        Route::post('/seen', [ChatController::class, 'seen'])->name('seen');
        Route::post('/remove', [ChatController::class, 'remove'])->name('remove');
        Route::post('/notification', [ChatController::class, 'notification'])->name('notification');
        Route::get('/chat-list', [ChatController::class, 'chatsList'])->name('chat-list');
        Route::get('/load-chat-list', [ChatController::class, 'loadChatList'])->name('load-chat-list');
    });

    // Team Chat
    Route::prefix('team-chats')->name('team-chats.')->group(function () {
        Route::get('/', [TeamChatController::class, 'index'])->name('index');
        Route::post('/create', [TeamChatController::class, 'create'])->name('create');
        Route::post('/load', [TeamChatController::class, 'load'])->name('load');
        Route::post('/send', [TeamChatController::class, 'send'])->name('send');
        Route::post('/group-info', [TeamChatController::class, 'groupInfo'])->name('group-info');
        Route::post('/update-group-image', [TeamChatController::class, 'updateGroupImage'])->name('update-group-image');
        Route::post('/edit-name-des', [TeamChatController::class, 'editNameDes'])->name('edit-name-des');
        Route::post('/name-des-update', [TeamChatController::class, 'nameDesUpdate'])->name('name-des-update');
        Route::post('/remove-member', [TeamChatController::class, 'removeMember'])->name('remove-member');
        Route::post('/group-list', [TeamChatController::class, 'groupList'])->name('group-list');
        Route::post('/exit-from-group', [TeamChatController::class, 'exitFromGroup'])->name('exit-from-group');
        Route::post('/add-member-team', [TeamChatController::class, 'addMemberTeam'])->name('add-member-team');
        Route::post('/delete-group', [TeamChatController::class, 'deleteGroup'])->name('delete-group');
        Route::post('/make-admin', [TeamChatController::class, 'makeAdmin'])->name('make-admin');
        Route::post('/seen', [TeamChatController::class, 'seen'])->name('seen');
        Route::post('/notification', [TeamChatController::class, 'notification'])->name('notification');
        Route::post('/remove-chat', [TeamChatController::class, 'removeChat'])->name('remove-chat');
        // clear-all-conversation
        Route::post('/clear-all-conversation', [TeamChatController::class, 'clearAllConversation'])->name('clear-all-conversation');
    });

    Route::prefix('strategy')->group(function () {
        Route::get('/', [StrategyController::class, 'index'])->name('strategy.index');
        Route::get('/upload', [StrategyController::class, 'upload'])->name('strategy.upload');
        Route::post('/store', [StrategyController::class, 'store'])->name('strategy.store');
        Route::get('/delete/{id}', [StrategyController::class, 'delete'])->name('strategy.delete');
        Route::get('/download/{strategy}', [StrategyController::class, 'download'])->name('strategy.download');
        Route::get('/fetch-data', [StrategyController::class, 'fetchData'])->name('strategy.fetch-data');
        Route::get('/view/{id}', [StrategyController::class, 'view'])->name('strategy.view');
    });

    Route::prefix('policy-guidence')->group(function () {
        Route::get('/', [PolicyGuidenceController::class, 'index'])->name('policy-guidence.index');
        Route::get('/upload', [PolicyGuidenceController::class, 'upload'])->name('policy-guidence.upload');
        Route::post('/store', [PolicyGuidenceController::class, 'store'])->name('policy-guidence.store');
        Route::get('/delete/{id}', [PolicyGuidenceController::class, 'delete'])->name('policy-guidence.delete');
        Route::get('/download/{file}', [PolicyGuidenceController::class, 'download'])->name('policy-guidence.download');
        Route::get('/fetch-data', [PolicyGuidenceController::class, 'fetchData'])->name('policy-guidence.fetch-data');
        Route::get('/view/{id}', [PolicyGuidenceController::class, 'view'])->name('policy-guidence.view');
    });

    Route::prefix('file')->group(function () {
        Route::get('/', [FileController::class, 'index'])->name('file.index');
        Route::get('/upload', [FileController::class, 'upload'])->name('file.upload');
        Route::post('/store', [FileController::class, 'store'])->name('file.store');
        Route::get('/edit/{id}', [FileController::class, 'edit'])->name('file.edit');
        Route::post('/update/{id}', [FileController::class, 'update'])->name('file.update');
        Route::get('/delete/{id}', [FileController::class, 'delete'])->name('file.delete');
        Route::get('/download/{file}', [FileController::class, 'download'])->name('file.download');
        Route::get('/fetch-data', [FileController::class, 'fetchData'])->name('file.fetch-data');
    });
    // topics.getTopics
    Route::get('/get-topics/{type}', [FileController::class, 'getTopics'])->name('topics.getTopics');

    Route::prefix('becoming-sovereign')->group(function () {
        Route::get('/', [BecomingSovereignController::class, 'index'])->name('becoming-sovereign.index');
        Route::get('/upload', [BecomingSovereignController::class, 'upload'])->name('becoming-sovereign.upload');
        Route::post('/store', [BecomingSovereignController::class, 'store'])->name('becoming-sovereign.store');
        Route::get('/edit/{id}', [BecomingSovereignController::class, 'edit'])->name('becoming-sovereign.edit');
        Route::post('/update/{id}', [BecomingSovereignController::class, 'update'])->name('becoming-sovereign.update');
        Route::get('/delete/{id}', [BecomingSovereignController::class, 'delete'])->name('becoming-sovereign.delete');
        Route::get('/download/{file}', [BecomingSovereignController::class, 'download'])->name('becoming-sovereign.download');
        Route::get('/fetch-data', [BecomingSovereignController::class, 'fetchData'])->name('becoming-sovereign.fetch-data');
        Route::get('/view/{id}', [BecomingSovereignController::class, 'view'])->name('becoming-sovereign.view');
    });

    Route::prefix('becoming-christ-link')->group(function () {
        Route::get('/', [BecomingChristLikeController::class, 'index'])->name('becoming-christ-link.index');
        Route::get('/upload', [BecomingChristLikeController::class, 'upload'])->name('becoming-christ-link.upload');
        Route::post('/store', [BecomingChristLikeController::class, 'store'])->name('becoming-christ-link.store');
        Route::get('/edit/{id}', [BecomingChristLikeController::class, 'edit'])->name('becoming-christ-link.edit');
        Route::post('/update/{id}', [BecomingChristLikeController::class, 'update'])->name('becoming-christ-link.update');
        Route::get('/delete/{id}', [BecomingChristLikeController::class, 'delete'])->name('becoming-christ-link.delete');
        Route::get('/download/{file}', [BecomingChristLikeController::class, 'download'])->name('becoming-christ-link.download');
        Route::get('/fetch-data', [BecomingChristLikeController::class, 'fetchData'])->name('becoming-christ-link.fetch-data');
        Route::get('/view/{id}', [BecomingChristLikeController::class, 'view'])->name('becoming-christ-link.view');
    });

    // leadership development
    Route::prefix('leadership-development')->group(function () {
        Route::get('/', [LeadershipDevelopmentController::class, 'index'])->name('leadership-development.index');
        Route::get('/upload', [LeadershipDevelopmentController::class, 'upload'])->name('leadership-development.upload');
        Route::post('/store', [LeadershipDevelopmentController::class, 'store'])->name('leadership-development.store');
        Route::get('/edit/{id}', [LeadershipDevelopmentController::class, 'edit'])->name('leadership-development.edit');
        Route::post('/update/{id}', [LeadershipDevelopmentController::class, 'update'])->name('leadership-development.update');
        Route::get('/delete/{id}', [LeadershipDevelopmentController::class, 'delete'])->name('leadership-development.delete');
        Route::get('/download/{file}', [LeadershipDevelopmentController::class, 'download'])->name('leadership-development.download');
        Route::get('/fetch-data', [LeadershipDevelopmentController::class, 'fetchData'])->name('leadership-development.fetch-data');
        Route::get('/view/{id}', [LeadershipDevelopmentController::class, 'view'])->name('leadership-development.view');
    });


    Route::resources([
        'roles' => RolePermissionsController::class,
        'partners' => PartnerController::class,
        'bulletins' => BulletinController::class,
        'topics' => TopicController::class,
        'categories' => CategoryController::class,
        'elearning-categories' => ElearningCategoryController::class,
        'products' => ProductController::class,
        'elearning' => ElearningController::class,
        'ecclesias' => EcclesiaContorller::class,
        'jobs' => JobpostingController::class,
        'meetings' => MeetingSchedulingController::class,
        // 'meetings' => MeetingSchedulingController::class,
    ]);

    // e-store routes
    Route::prefix('products')->group(function () {
        // product variations manage route

        Route::get('/{id}/variations', [ProductController::class, 'variations'])->name('products.variations');
        Route::get('/{id}/product-stocks', [ProductController::class, 'variations'])->name('products.simple.stocks');


        Route::get('/product-delete/{id}', [ProductController::class, 'delete'])->name('products.delete');

        // generate product variations
        Route::post('/generate-variations', [ProductController::class, 'generateVariations'])->name('products.generate.variations');
        // delete a product variation
        Route::post('/variation-delete', [ProductController::class, 'deleteVariation'])->name('products.variation.delete');
        // update product variations
        Route::post('/variations-update', [ProductController::class, 'updateVariations'])->name('products.variations.update');
        // delete a product variation image
        Route::post('/variation-image-delete', [ProductController::class, 'deleteVariationImage'])->name('products.variation.image.delete');
    });
    Route::get('/products-image-delete', [ProductController::class, 'imageDelete'])->name('products.image.delete');
    Route::get('/warehouse-product-image-delete', [ProductController::class, 'warehouseImageDelete'])->name('warehouse-product.image.delete');
    Route::get('/products-fetch-data', [ProductController::class, 'fetchData'])->name('products.fetch-data');
    Route::post('/products-slug-check', [ProductController::class, 'checkSlug'])->name('products.slug.check');


    Route::get('/categories-fetch-data', [CategoryController::class, 'fetchData'])->name('categories.fetch-data');
    Route::prefix('categories')->group(function () {
        Route::get('/category-delete/{id}', [CategoryController::class, 'delete'])->name('categories.delete');
    });
    Route::resource('sizes', SizeController::class);
    Route::get('/sizes-delete/{id}', [SizeController::class, 'delete'])->name('sizes.delete');
    Route::resource('colors', ColorController::class);
    Route::get('/colors-delete/{id}', [ColorController::class, 'delete'])->name('colors.delete');
    Route::get('/store-page/{name}/{permission}', [EstoreCmsController::class, 'page'])->name('store-user.page');
    Route::get('/store-cms/dashboard', [EstoreCmsController::class, 'dashboard'])->name('user.store-cms.dashboard');
    Route::get('/store-cms/list', [EstoreCmsController::class, 'list'])->name('user.store-cms.list');
    Route::get('/store-cms/create', [EstoreCmsController::class, 'create'])->name('user.store-cms.create');
    Route::post('/store-cms/store', [EstoreCmsController::class, 'store'])->name('user.store-cms.store');
    Route::put('/store-cms/update/{id}', [EstoreCmsController::class, 'update'])->name('user.store-cms.update');
    Route::get('/store-cms-delete/{id}', [EstoreCmsController::class, 'delete'])->name('user.store-cms.delete');
    Route::get('/store-cms-page/{page}', [EstoreCmsController::class, 'cms'])->name('user.store-cms.edit');
    Route::post('/store-cms/home/update', [EstoreCmsController::class, 'homeCmsUpdate'])->name('user.store-cms.home.update');
    Route::post('/store-cms/footer/update', [EstoreCmsController::class, 'footerUpdate'])->name('user.store-cms.footer.update');
    // contact cms (e-store contact page)
    Route::get('/store-cms/contact', [EstoreCmsController::class, 'contactCms'])->name('user.store-cms.contact');
    Route::post('/store-cms/contact/update', [EstoreCmsController::class, 'contactCmsUpdate'])->name('user.store-cms.contact.update');
    Route::get('/store-orders/list', [EstoreCmsController::class, 'ordersList'])->name('user.store-orders.list');
    Route::get('/store-orders/fetch-data', [EstoreCmsController::class, 'fetchOrdersData'])->name('user.store-orders.fetch-data');
    Route::get('/store-orders/details/{id}', [EstoreCmsController::class, 'orderDetails'])->name('user.store-orders.details');
    Route::post('/store-orders/update-status', [EstoreCmsController::class, 'updateOrderStatus'])->name('user.store-orders.update-status');
    Route::delete('/store-orders/delete/{id}', [EstoreCmsController::class, 'deleteOrder'])->name('user.store-orders.delete');
    Route::post('/store-orders/export', [EstoreCmsController::class, 'exportOrders'])->name('user.store-orders.export');
    // Route::post('/orders-export', [EstoreCmsController::class, 'export'])->name('user.store-orders.export');

    // routes/web.php
    Route::get('/orders/{order}/invoice', [EstoreCmsController::class, 'downloadInvoice'])
        ->name('user.store-orders.invoice');
    Route::post('/orders/{order}/refund', [EstoreCmsController::class, 'refund'])->name('user.store-orders.refund');


    // products review routes
    Route::prefix('store-products')->name('user.store-products.')->group(function () {
        Route::get('/{product}/reviews', [EstoreCmsController::class, 'productReviews'])->name('reviews');
        Route::patch('/{product}/reviews/{review}/approve', [EstoreCmsController::class, 'approveProductReview'])->name('reviews.approve');
        Route::delete('/{product}/reviews/{review}', [EstoreCmsController::class, 'deleteProductReview'])->name('reviews.delete');
    });

    // Reports routes
    Route::get('/store-orders/reports', [EstoreCmsController::class, 'reportsIndex'])->name('user.store-orders.reports');
    Route::get('/store-orders/fetch-report', [EstoreCmsController::class, 'fetchReportData'])->name('user.store-orders.fetch-report');
    Route::get('/store-orders/export-report', [EstoreCmsController::class, 'exportReport'])->name('user.store-orders.export-report');
    // promo code management routes
    Route::resource('/store-promo-codes', EstorePromoCodeController::class);
    Route::get('/store-promo-codes-delete/{id}', [EstorePromoCodeController::class, 'delete'])->name('store-promo-codes.delete');
    // estore setting management
    Route::resource('/store-settings', EstoreSettingController::class);

    // ware house management
    Route::resource('/ware-houses', WareHouseController::class);
    Route::get('/ware-houses-delete/{id}', [WareHouseController::class, 'delete'])->name('ware-houses.delete');


    // select warehouse before warehouse product management from a product
    Route::get('/select-warehouse/{productId}', [WareHouseController::class, 'selectWarehouse'])->name('ware-houses.select-warehouse');
    // variations for warehouse admin
    Route::get('/warehouse-variations/{warehouseId}/{productId}', [WareHouseController::class, 'variationsWarehouse'])->name('products.variations.warehouse');
    // select warehouse product variation stock
    Route::post('/select-warehouse-variation-stock', [WareHouseController::class, 'selectWarehouseVariationStock'])->name('products.select.warehouse.variation.stock');
    Route::post('/warehouse-variation/update-quantity', [WareHouseController::class, 'updateWarehouseVariationQuantity'])->name('warehouse.variation.update-quantity');
    // warehouse product management
    Route::get('/ware-houses/{id}/products', [WareHouseController::class, 'products'])->name('ware-houses.products');
    Route::get('/ware-houses/{id}/products/add', [WareHouseController::class, 'addProduct'])->name('ware-houses.products.add');
    Route::post('/ware-houses/{id}/products', [WareHouseController::class, 'storeProduct'])->name('ware-houses.products.store');
    Route::get('/ware-houses/{warehouseId}/products/{productId}/edit', [WareHouseController::class, 'editProduct'])->name('ware-houses.products.edit');
    Route::put('/ware-houses/{warehouseId}/products/{productId}', [WareHouseController::class, 'updateProduct'])->name('ware-houses.products.update');
    Route::get('/ware-houses/{warehouseId}/products/{productId}/delete', [WareHouseController::class, 'deleteProduct'])->name('ware-houses.products.delete');
    // // // on change product get product's size and colors
    Route::get('/ware-houses/products/getDetails', [WareHouseController::class, 'getProductDetails'])->name('ware-houses.products.getDetails');
    // warehouse-products-list
    Route::get('/warehouse-products-list/{id}/products', [WareHouseController::class, 'warehouseProductsList'])->name('ware-houses.products.list');

    // warehouse admin management
    Route::resource('warehouse-admins', WarehouseAdminController::class);
    Route::get('/warehouse-admins-delete/{id}', [WarehouseAdminController::class, 'delete'])->name('warehouse-admins.delete');

    // Warehouse Manager Product Management Routes (new)
    Route::get('/warehouse-products', [WarehouseAdminController::class, 'listProducts'])->name('warehouse-admin.products');
    Route::get('/warehouse-products/create', [WarehouseAdminController::class, 'createProduct'])->name('warehouse-admin.products.create');
    Route::post('/warehouse-products', [WarehouseAdminController::class, 'storeProduct'])->name('warehouse-admin.products.store');
    Route::get('/warehouse-products/{id}/edit', [WarehouseAdminController::class, 'editProduct'])->name('warehouse-admin.products.edit');
    Route::put('/warehouse-products/{id}', [WarehouseAdminController::class, 'updateProduct'])->name('warehouse-admin.products.update');
    Route::get('/warehouse-products/{id}/delete', [WarehouseAdminController::class, 'deleteProduct'])->name('warehouse-admin.products.delete');

    // Warehouse Manager Size Management Routes (new)
    Route::get('/warehouse-sizes', [WarehouseAdminController::class, 'listSizes'])->name('warehouse-admin.sizes');
    Route::get('/warehouse-sizes/create', [WarehouseAdminController::class, 'createSize'])->name('warehouse-admin.sizes.create');
    Route::post('/warehouse-sizes', [WarehouseAdminController::class, 'storeSize'])->name('warehouse-admin.sizes.store');
    Route::get('/warehouse-sizes/{id}/edit', [WarehouseAdminController::class, 'editSize'])->name('warehouse-admin.sizes.edit');
    Route::put('/warehouse-sizes/{id}', [WarehouseAdminController::class, 'updateSize'])->name('warehouse-admin.sizes.update');
    Route::get('/warehouse-sizes/{id}/delete', [WarehouseAdminController::class, 'deleteSize'])->name('warehouse-admin.sizes.delete');

    // Warehouse Manager Color Management Routes (new)
    Route::get('/warehouse-colors', [WarehouseAdminController::class, 'listColors'])->name('warehouse-admin.colors');
    Route::get('/warehouse-colors/create', [WarehouseAdminController::class, 'createColor'])->name('warehouse-admin.colors.create');
    Route::post('/warehouse-colors', [WarehouseAdminController::class, 'storeColor'])->name('warehouse-admin.colors.store');
    Route::get('/warehouse-colors/{id}/edit', [WarehouseAdminController::class, 'editColor'])->name('warehouse-admin.colors.edit');
    Route::put('/warehouse-colors/{id}', [WarehouseAdminController::class, 'updateColor'])->name('warehouse-admin.colors.update');
    Route::get('/warehouse-colors/{id}/delete', [WarehouseAdminController::class, 'deleteColor'])->name('warehouse-admin.colors.delete');

    // Route::get('/estore-users-list', [PartnerController::class, 'estoreUsers'])->name('estore-users.list');
    // estore-users.fetch-data
    Route::get('/estore-users-fetch-data', [PartnerController::class, 'estoreFetchData'])->name('estore-users.fetch-data');

    // e-learning routes
    Route::prefix('elearning')->group(function () {
        Route::get('/elearning-product-delete/{id}', [ElearningController::class, 'delete'])->name('elearning.delete');
    });
    Route::get('/elearning-products-image-delete', [ElearningController::class, 'imageDelete'])->name('elearning.image.delete');
    Route::get('/elearning-products-fetch-data', [ElearningController::class, 'fetchData'])->name('elearning.fetch-data');
    Route::get('/elearning-categories-fetch-data', [ElearningCategoryController::class, 'fetchData'])->name('elearning-categories.fetch-data');
    Route::prefix('elearning-categories')->group(function () {
        Route::get('/elearning-category-delete/{id}', [ElearningCategoryController::class, 'delete'])->name('elearning-categories.delete');
    });
    Route::get('/elearning-page/{name}/{permission}', [ElearningCmsController::class, 'page'])->name('user.elearning-page');
    Route::get('/elearning-cms/dashboard', [ElearningCmsController::class, 'dashboard'])->name('user.elearning-cms.dashboard');
    Route::get('/elearning-cms/list', [ElearningCmsController::class, 'list'])->name('user.elearning-cms.list');
    Route::get('/elearning-cms/create', [ElearningCmsController::class, 'create'])->name('user.elearning-cms.create');
    Route::post('/elearning-cms/store', [ElearningCmsController::class, 'store'])->name('user.elearning-cms.store');
    Route::put('/elearning-cms/update/{id}', [ElearningCmsController::class, 'update'])->name('user.elearning-cms.update');
    Route::get('/elearning-cms-delete/{id}', [ElearningCmsController::class, 'delete'])->name('user.elearning-cms.delete');
    Route::get('/elearning-cms-page/{page}', [ElearningCmsController::class, 'cms'])->name('user.elearning-cms.edit');
    Route::post('/elearning-cms/home/update', [ElearningCmsController::class, 'homeCmsUpdate'])->name('user.elearning-cms.home.update');
    Route::post('/elearning-cms/footer/update', [ElearningCmsController::class, 'footerUpdate'])->name('user.elearning-cms.footer.update');

    Route::prefix('meetings')->group(function () {
        Route::get('/meeting-delete/{id}', [MeetingSchedulingController::class, 'delete'])->name('meetings.delete');
    });
    // show-single-meeting
    Route::get('/show-single-meeting', [MeetingSchedulingController::class, 'showSingleMeeting'])->name('meetings.show-single-meeting');
    // calender ajax fetch data
    Route::get('/view-calender', [MeetingSchedulingController::class, 'viewCalender'])->name('meetings.view-calender');
    Route::get('/meetings-calender-fetch-data', [MeetingSchedulingController::class, 'fetchCalenderData'])->name('meetings.calender-fetch-data');
    Route::get('/meetings-fetch-data', [MeetingSchedulingController::class, 'fetchData'])->name('meetings.fetch-data');

    Route::prefix('jobs')->group(function () {
        Route::get('/job-delete/{id}', [JobpostingController::class, 'delete'])->name('jobs.delete');
    });
    Route::get('/jobs-fetch-data', [JobpostingController::class, 'fetchData'])->name('jobs.fetch-data');

    Route::prefix('ecclesias')->group(function () {
        Route::get('/ecclesia-delete/{id}', [EcclesiaContorller::class, 'delete'])->name('ecclesias.delete');
    });
    Route::get('/ecclesias-fetch-data', [EcclesiaContorller::class, 'fetchData'])->name('ecclesias.fetch-data');






    Route::prefix('topics')->group(function () {
        Route::get('/topic-delete/{id}', [TopicController::class, 'delete'])->name('topics.delete');
    });

    Route::prefix('bulletins')->group(function () {
        Route::get('/bulletin-delete/{id}', [BulletinController::class, 'delete'])->name('bulletins.delete');
    });
    Route::get('/bulletins-fetch-data', [BulletinController::class, 'fetchData'])->name('bulletins.fetch-data');
    // bulletins.load-table
    Route::post('/bulletins-load-table', [BulletinController::class, 'loadTable'])->name('bulletins.load-table');
    Route::post('/bulletins-single', [BulletinController::class, 'single'])->name('bulletins.single');

    Route::prefix('bulletin-board')->group(function () {
        Route::get('/', [BulletinBoardController::class, 'list'])->name('bulletin-board.index');
        // load bulletin board
        Route::post('/load', [BulletinBoardController::class, 'load'])->name('bulletin-board.load');
    });

    Route::prefix('roles')->group(function () {
        Route::get('/role-delete/{id}', [RolePermissionsController::class, 'delete'])->name('roles.delete');
    });

    Route::prefix('partners')->group(function () {
        Route::get('/partner-delete/{id}', [PartnerController::class, 'delete'])->name('partners.delete');
    });

    Route::get('/changePartnerStatus', [PartnerController::class, 'changePartnerStatus'])->name('partners.change-status');
    Route::get('/partner-fetch-data', [PartnerController::class, 'fetchData'])->name('partners.fetch-data');

    // Mail
    Route::prefix('mail')->group(function () {
        Route::get('/', [SendMailController::class, 'list'])->name('mail.index');
        Route::get('/sent', [SendMailController::class, 'sentList'])->name('mail.sentList');
        Route::get('/star', [SendMailController::class, 'starList'])->name('mail.starList');
        Route::get('/trash', [SendMailController::class, 'trashList'])->name('mail.trashList');

        Route::get('/inbox-email-list', [SendMailController::class, 'inboxEmailList'])->name('mail.inbox-email-list');
        Route::get('/sent-email-list', [SendMailController::class, 'sentEmailList'])->name('mail.sent-email-list');
        Route::get('/star-email-list', [SendMailController::class, 'starEmailList'])->name('mail.star-email-list');
        Route::get('/trash-email-list', [SendMailController::class, 'trashEmailList'])->name('mail.trash-email-list');

        Route::get('/view/{id}', [SendMailController::class, 'view'])->name('mail.view');
        Route::get('/sent-mail-view/{id}', [SendMailController::class, 'sentMailView'])->name('mail.sent.view');
        Route::get('/star-mail-view/{id}', [SendMailController::class, 'starMailView'])->name('mail.star.view');
        Route::get('/trash-mail-view/{id}', [SendMailController::class, 'trashMailView'])->name('mail.trash.view');

        Route::get('/compose', [SendMailController::class, 'compose'])->name('mail.compose');
        Route::post('/send', [SendMailController::class, 'sendMail'])->name('mail.send');
        Route::post('/sendReply', [SendMailController::class, 'sendMailReply'])->name('mail.sendReply');
        Route::post('/sendForward', [SendMailController::class, 'sendMailForward'])->name('mail.sendForward');

        Route::post('/mail-delete', [SendMailController::class, 'delete'])->name('mail.delete');
        Route::post('/mail-delete-sent', [SendMailController::class, 'deleteSentsMail'])->name('mail.deleteSentsMail');
        Route::post('/mail-restore', [SendMailController::class, 'restore'])->name('mail.restore');
        Route::post('/mail-trash-empty', [SendMailController::class, 'trashEmpty'])->name('mail.trash-empty');
        Route::post('/mail-star', [SendMailController::class, 'star'])->name('mail.star');

        Route::post('/mail-delete-single', [SendMailController::class, 'deleteSingleMail'])->name('mail.deleteSingleMail');
        Route::post('/mail-restore-single', [SendMailController::class, 'restoreSingleMail'])->name('mail.restoreSingleMail');

        Route::get('/print/{id}', [SendMailController::class, 'printMail'])->name('mail.print');
    });

    // live-event
    Route::prefix('events')->group(function () {
        Route::get('/', [LiveEventController::class, 'list'])->name('events.index');
        Route::get('/calender', [LiveEventController::class, 'calender'])->name('events.calender');
        Route::post('/store', [LiveEventController::class, 'store'])->name('events.store');
        Route::put('/update/{id}', [LiveEventController::class, 'update'])->name('events.update');
        Route::delete('/destroy/{id}', [LiveEventController::class, 'destroy'])->name('events.destroy');
    });

    Route::prefix('newsletters')->group(function () {
        Route::get('/', [UserNewsletterController::class, 'list'])->name('user.newsletters.index');
        Route::post('/send-email', [UserNewsletterController::class, 'sendEmail'])->name('user.newsletters.send-mail');
        // newsletters.delete
        Route::get('/newsletter-delete/{id}', [UserNewsletterController::class, 'delete'])->name('user.newsletters.delete');
    });
    Route::get('/user-newsletter-fetch-data', [UserNewsletterController::class, 'fetchData'])->name('user.newsletters.fetch-data');

    Route::get('/mail-fetch-data', [SendMailController::class, 'fetchData'])->name('mail.fetch-data');
});
// });


/**************************************************----------------------------ECOM--------------------------****************************************************************/

Route::prefix('e-store')->group(function () {

    Route::get('/', [HomeController::class, 'eStore'])->name('e-store');
    Route::post('/newsletter', [HomeController::class, 'newsletter'])->name('e-store.newsletter');
    Route::get('/contact', [HomeController::class, 'contactUs'])->name('e-store.contact');
    Route::get('/product/{slug}', [EstoreProductController::class, 'productDetails'])->name('e-store.product-details');
    Route::get('/all-products', [EstoreProductController::class, 'products'])->name('e-store.all-products');
    Route::get('/live-search', [EstoreProductController::class, 'liveSearch'])->name('e-store.live-search');
    Route::get('/products-filter', [EstoreProductController::class, 'productsFilter'])->name('e-store.products-filter');
    Route::post('/product-add-review', [EstoreProductController::class, 'productAddReview'])->name('e-store.product-add-review');
    Route::post('/add-to-cart', [EstoreProductController::class, 'addToCart'])->name('e-store.add-to-cart');
    Route::post('/remove-from-cart', [EstoreProductController::class, 'removeFromCart'])->name('e-store.remove-from-cart');
    Route::post('/update-cart', [EstoreProductController::class, 'updateCart'])->name('e-store.update-cart');
    Route::post('/clear-cart', [EstoreProductController::class, 'clearCart'])->name('e-store.clear-cart');
    Route::get('/cart-count', [EstoreProductController::class, 'cartCount'])->name('e-store.cart-count');
    Route::get('/cart-list', [EstoreProductController::class, 'cartList'])->name('e-store.cart-list');
    Route::get('/check-product-in-cart', [EstoreProductController::class, 'checkProductInCart'])->name('e-store.check-product-in-cart');
    Route::get('/estore-cart', [EstoreProductController::class, 'cart'])->name('e-store.cart');
    Route::get('/estore-checkout', [EstoreProductController::class, 'checkout'])->name('e-store.checkout');


    // e-store.apply-promo-code
    Route::post('/apply-promo-code', [EstoreProductController::class, 'applyPromoCode'])->name('e-store.apply-promo-code');
    // e-store.remove-promo-code
    Route::post('/remove-promo-code', [EstoreProductController::class, 'removePromoCode'])->name('e-store.remove-promo-code');

    Route::post('/process-checkout', [EstoreProductController::class, 'processCheckout'])->name('e-store.process-checkout');
    Route::get('/payment-success', [EstoreProductController::class, 'paymentSuccess'])->name('e-store.payment-success');
    Route::get('/payment-cancelled', [EstoreProductController::class, 'paymentCancelled'])->name('e-store.payment-cancelled');

    Route::get('/order-success/{orderId}', [EstoreProductController::class, 'orderSuccess'])->name('e-store.order-success');
    Route::get('/my-orders', [EstoreProductController::class, 'myOrders'])->name('e-store.my-orders')->middleware('user');
    Route::get('/order-details/{orderId}', [EstoreProductController::class, 'orderDetails'])->name('e-store.order-details')->middleware('user');

    // profile and change password page
    Route::get('/estore-profile', [HomeController::class, 'profile'])->name('e-store.profile')->middleware('user');
    Route::post('/estore-update-profile', [HomeController::class, 'updateProfile'])->name('e-store.update-profile')->middleware('user');
    Route::get('/estore-change-password', [HomeController::class, 'changePassword'])->name('e-store.change-password')->middleware('user');
    Route::post('/estore-update-password', [HomeController::class, 'passwordUpdate'])->name('e-store.password.update')->middleware('user');
    // order tracking page
    Route::get('/estore_track-order', [HomeController::class, 'orderTracking'])->name('e-store.order-tracking');
    Route::post('/estore-track-order-id', [HomeController::class, 'trackOrder'])->name('e-store.track-order');


    // e-store.cancel-order
    Route::post('/cancel-order', [EstoreProductController::class, 'cancelOrder'])->name('e-store.cancel-order');

    // add to wishlist
    Route::post('/product/add-to-wishlist', [EstoreProductController::class, 'addToWishlist'])->name('e-store.add-to-wishlist');
    // wishlist list
    Route::get('/estore/wishlist', [EstoreProductController::class, 'wishlist'])->name('e-store.wishlist');
    // remove from wishlist
    Route::post('/product/remove-from-wishlist', [EstoreProductController::class, 'removeFromWishlist'])->name('e-store.remove-from-wishlist');
    // // by ajax get warehouse product details by product id with optional size and color
    Route::post('/get-warehouse-product-details', [EstoreProductController::class, 'getWarehouseProductDetails'])->name('e-store.get-warehouse-product-details');
    // user-update.location
    Route::post('/user-update/location', [HomeController::class, 'updateLocation'])->name('user-update.location');
    // estore.register
    Route::post('/register', [HomeController::class, 'register'])->name('estore.register');

    $categories = Category::where('status', 1)->get();
    foreach ($categories as $category) {
        if ($category->slug) {
            Route::get($category->slug, [EstoreProductController::class, 'products'])
                ->name($category->slug . '.e-store.page')
                ->defaults('category_id', $category->id);
        }
    }

    $pages = EcomCmsPage::where('id', '<', 3)->get();
    foreach ($pages as $page) {
        if ($page->slug) {
            Route::get($page->slug, [EstoreCmsController::class, 'cmsPage'])
                ->name($page->slug . '.e-store.cms-page')
                ->defaults('page_id', $page->id);
        }
    }
});

// Dynamic routes for categories
$categories = Category::whereNull('parent_id')->get();
foreach ($categories as $category) {
    if ($category->slug) {
        Route::get($category->slug, [EstoreProductController::class, 'products'])
            ->name($category->slug . '.page')
            ->defaults('category_id', $category->id);
    }
}

// Dynamic routes for subcategories
$subcategories = Category::whereNotNull('parent_id')->get();
foreach ($subcategories as $subcategory) {
    if ($subcategory->slug) {
        Route::get($subcategory->slug, [EstoreProductController::class, 'products'])
            ->name($subcategory->slug . '.page')
            ->defaults('category_id', $subcategory->category_id)
            ->defaults('id', $subcategory->id);
    }
}


/**************************************************----------------------------ELEARNING--------------------------****************************************************************/

Route::prefix('e-learning')->middleware(['user'])->group(function () {
    Route::get('/', [ElearningHomeController::class, 'eStore'])->name('e-learning');
    Route::post('/newsletter', [ElearningHomeController::class, 'newsletter'])->name('e-learning.newsletter');
    Route::get('/product/{slug}', [ElearningProductController::class, 'productDetails'])->name('e-learning.product-details');
    Route::get('/all-products', [ElearningProductController::class, 'products'])->name('e-learning.all-products');
    Route::get('/products-filter', [ElearningProductController::class, 'productsFilter'])->name('e-learning.products-filter');
    Route::post('/product-add-review', [ElearningProductController::class, 'productAddReview'])->name('e-learning.product-add-review');

    $categories = ElearningCategory::where('status', 1)->get();
    foreach ($categories as $category) {
        if ($category->slug) {
            Route::get($category->slug, [ElearningProductController::class, 'products'])
                ->name($category->slug . '.e-learning.page')
                ->defaults('category_id', $category->id);
        }
    }

    $pages = ElearningEcomCmsPage::get();
    foreach ($pages as $page) {
        if ($page->slug) {
            Route::get($page->slug, [ElearningCmsController::class, 'cmsPage'])
                ->name($page->slug . '.e-learning.cms-page')
                ->defaults('page_id', $page->id);
        }
    }
});

Route::post('/chatbot', [ChatBotController::class, 'FaqChat'])->name('chatbot.message');
