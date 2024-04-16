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
use App\Http\Controllers\Admin\NewsletterController;
use App\Http\Controllers\Admin\OrganizationCenterController;
use App\Http\Controllers\Admin\OrganizationController;
use App\Http\Controllers\Admin\OurGovernanceController;
use App\Http\Controllers\Admin\OurOrganizationController;
use App\Http\Controllers\Admin\PlanController;
use App\Http\Controllers\Admin\PrincipleAndBusinessController;
use App\Http\Controllers\Admin\SellerController;
use App\Http\Controllers\Admin\ServiceContoller;
use App\Http\Controllers\Admin\TestimonialController;
use App\Http\Controllers\Frontend\CmsController;
use App\Http\Controllers\Frontend\DonationController;
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
Route::post('/login-check', [AuthController::class, 'loginCheck'])->name('admin.login.check');  //login check
Route::post('forget-password', [ForgetPasswordController::class, 'forgetPassword'])->name('admin.forget.password');
Route::post('change-password', [ForgetPasswordController::class, 'changePassword'])->name('admin.change.password');
Route::get('forget-password/show', [ForgetPasswordController::class, 'forgetPasswordShow'])->name('admin.forget.password.show');
Route::get('reset-password/{id}/{token}', [ForgetPasswordController::class, 'resetPassword'])->name('admin.reset.password');
Route::post('change-password', [ForgetPasswordController::class, 'changePassword'])->name('admin.change.password');

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
        'plans' => PlanController::class
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
