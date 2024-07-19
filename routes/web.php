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
use App\Http\Controllers\Admin\FaqController;
use App\Http\Controllers\Admin\FooterController;
use App\Http\Controllers\Admin\GalleryController;
use App\Http\Controllers\Admin\HomeCmsController;
use App\Http\Controllers\Admin\MemberPrivacyPolicyContoller;
use App\Http\Controllers\Admin\NewsletterController;
use App\Http\Controllers\Admin\OrganizationCenterController;
use App\Http\Controllers\Admin\OrganizationController;
use App\Http\Controllers\Admin\OurGovernanceController;
use App\Http\Controllers\Admin\OurOrganizationController;
use App\Http\Controllers\Admin\PlanController;
use App\Http\Controllers\Admin\PmaDisclaimerController;
use App\Http\Controllers\Admin\PrincipleAndBusinessController;
use App\Http\Controllers\Admin\RegisterAgreementController;
use App\Http\Controllers\Admin\SellerController;
use App\Http\Controllers\Admin\ServiceContoller;
use App\Http\Controllers\Admin\TestimonialController;
use App\Http\Controllers\Frontend\CmsController;
use App\Http\Controllers\Frontend\DonationController;
use App\Http\Controllers\User\AuthController as UserAuthController;
use App\Http\Controllers\User\BecomingChristLikeController;
use App\Http\Controllers\User\BecomingSovereignController;
use App\Http\Controllers\User\BulletinBoardController;
use App\Http\Controllers\User\BulletinController;
use App\Http\Controllers\User\CategoryController;
use App\Http\Controllers\User\ChatController;
use App\Http\Controllers\User\CmsController as UserCmsController;
use App\Http\Controllers\User\DashboardController as UserDashboardController;
use App\Http\Controllers\User\FileController;
use App\Http\Controllers\User\ForgetPasswordController as UserForgetPasswordController;
use App\Http\Controllers\User\LeadershipDevelopmentController;
use App\Http\Controllers\User\LiveEventController;
use App\Http\Controllers\User\PartnerController;
use App\Http\Controllers\User\ProductController;
use App\Http\Controllers\User\RolePermissionsController;
use App\Http\Controllers\User\SendMailController;
use App\Http\Controllers\User\SubscriptionController;
use App\Http\Controllers\User\TeamController;
use App\Http\Controllers\User\TopicController;
use Illuminate\Routing\RouteRegistrar;
use Illuminate\Support\Facades\Artisan;

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

    Route::prefix('plans')->group(function () {
        Route::get('/plan-delete/{id}', [PlanController::class, 'delete'])->name('plans.delete');
    });
    Route::get('/changePlanStatus', [PlanController::class, 'changePlansStatus'])->name('plans.change-status');
    Route::get('/plan-fetch-data', [PlanController::class, 'fetchData'])->name('plans.fetch-data');

    Route::get('/donations-fetch-data', [AdminDonationController::class, 'fetchData'])->name('donations.fetch-data');

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



        Route::get('/newsletter-fetch-data', [NewsletterController::class, 'fetchData'])->name('newsletters.fetch-data');
        Route::get('/contact-us-fetch-data', [ContactusController::class, 'fetchData'])->name('contact-us.fetch-data');

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
Route::get('/principle-and-business', [CmsController::class, 'principleAndBusiness'])->name('principle-and-business');
Route::get('/ecclesia-associations', [CmsController::class, 'ecclesiaAssociations'])->name('ecclesia-associations');
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
// member privacy policy
Route::get('/member-privacy-policy', [UserCmsController::class, 'memberPrivacyPolicy'])->name('member-privacy-policy');

Route::prefix('user')->middleware(['user'])->group(function () {
    Route::get('/subscription', [SubscriptionController::class, 'subscription'])->name('user.subscription');
    Route::get('/subscription-payment/{id}', [SubscriptionController::class, 'payment'])->name('user.subscription.payment');
    Route::get('/stripe-checkout-success', [SubscriptionController::class, 'stripeCheckoutSuccess'])->name('stripe.checkout.success');

    Route::middleware(['member.access'])->group(function () {
        Route::get('/dashboard', [UserDashboardController::class, 'dashboard'])->name('user.dashboard');
        Route::get('/profile', [UserDashboardController::class, 'profile'])->name('user.profile');
        Route::post('/profile-update', [UserDashboardController::class, 'profileUpdate'])->name('user.profile.update');
        Route::get('/change-password', [UserDashboardController::class, 'password'])->name('user.change.password');
        Route::post('/change-password-update', [UserDashboardController::class, 'passwordUpdate'])->name('user.password.update');
        Route::get('/logout', [UserAuthController::class, 'logout'])->name('logout');

        Route::prefix('chats')->name('chats.')->group(function () {
            Route::get('/', [ChatController::class, 'chats'])->name('index');
            Route::post('/load', [ChatController::class, 'load'])->name('load');
            Route::post('/send', [ChatController::class, 'send'])->name('send');
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
            'products' => ProductController::class,
        ]);

        Route::prefix('products')->group(function () {
            Route::get('/product-delete/{id}', [ProductController::class, 'delete'])->name('products.delete');
        });
        // products.image.delete
        Route::get('/products-image-delete', [ProductController::class, 'imageDelete'])->name('products.image.delete');
        Route::get('/products-fetch-data', [ProductController::class, 'fetchData'])->name('products.fetch-data');

        Route::get('/categories-fetch-data', [CategoryController::class, 'fetchData'])->name('categories.fetch-data');

        Route::prefix('categories')->group(function () {
            Route::get('/category-delete/{id}', [CategoryController::class, 'delete'])->name('categories.delete');
        });

        Route::prefix('topics')->group(function () {
            Route::get('/topic-delete/{id}', [TopicController::class, 'delete'])->name('topics.delete');
        });

        Route::prefix('bulletins')->group(function () {
            Route::get('/bulletin-delete/{id}', [BulletinController::class, 'delete'])->name('bulletins.delete');
        });
        Route::get('/bulletins-fetch-data', [BulletinController::class, 'fetchData'])->name('bulletins.fetch-data');


        Route::prefix('bulletin-board')->group(function () {
            Route::get('/', [BulletinBoardController::class, 'list'])->name('bulletin-board.index');
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
            Route::get('/compose', [SendMailController::class, 'compose'])->name('mail.compose');
            Route::post('/send', [SendMailController::class, 'sendMail'])->name('mail.send');
            Route::get('/view', [SendMailController::class, 'view'])->name('mail.view');
        });

        // live-event
        Route::prefix('events')->group(function () {
            Route::get('/', [LiveEventController::class, 'list'])->name('events.index');
            Route::get('/calender', [LiveEventController::class, 'calender'])->name('events.calender');
            Route::post('/store', [LiveEventController::class, 'store'])->name('events.store');
        });

        Route::get('/mail-fetch-data', [SendMailController::class, 'fetchData'])->name('mail.fetch-data');

        Route::get('/page/{name}/{permission}', [UserCmsController::class, 'page'])->name('user.page');
    });
});
