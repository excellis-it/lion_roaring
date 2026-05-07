<?php

namespace App\Http\Controllers\User;

use App\Http\Controllers\Controller;
use App\Models\AboutUs;
use App\Models\Article;
use App\Models\Bulletin;
use App\Models\Category;
use App\Models\ChatbotKeyword;
use App\Models\Color;
use App\Models\ContactUs;
use App\Models\ContactUsCms;
use App\Models\Country;
use App\Models\Detail;
use App\Models\Ecclesia;
use App\Models\EcclesiaAssociation;
use App\Models\EcomCmsPage;
use App\Models\EcomContactCms;
use App\Models\EcomFooterCms;
use App\Models\EcomHomeCms;
use App\Models\EcomNewsletter;
use App\Models\ElearningCategory;
use App\Models\ElearningEcomCmsPage;
use App\Models\ElearningEcomFooterCms;
use App\Models\ElearningEcomHomeCms;
use App\Models\ElearningEcomNewsletter;
use App\Models\ElearningProduct;
use App\Models\ElearningSubCategory;
use App\Models\ElearningTopic;
use App\Models\EstorePromoCode;
use App\Models\EstoreSetting;
use App\Models\Event;
use App\Models\Faq;
use App\Models\File;
use App\Models\Footer;
use App\Models\Gallery;
use App\Models\HomeCms;
use App\Models\Job;
use App\Models\Meeting;
use App\Models\MemberPrivacyPolicy;
use App\Models\MembershipPromoCode;
use App\Models\MembershipTier;
use App\Models\MenuItem;
use App\Models\Newsletter;
use App\Models\OrderEmailTemplate;
use App\Models\OrderStatus;
use App\Models\Organization;
use App\Models\OrganizationCenter;
use App\Models\OurGovernance;
use App\Models\OurOrganization;
use App\Models\Plan;
use App\Models\PmaTerm;
use App\Models\Policy;
use App\Models\PrincipalAndBusiness;
use App\Models\PrivacyPolicy;
use App\Models\PrivateCollaboration;
use App\Models\Product;
use App\Models\ProductImage;
use App\Models\ProductOtherCharge;
use App\Models\ProductVariation;
use App\Models\RegisterAgreement;
use App\Models\Service;
use App\Models\SignupRule;
use App\Models\SiteSetting;
use App\Models\Size;
use App\Models\Strategy;
use App\Models\TermsAndCondition;
use App\Models\Testimonial;
use App\Models\Topic;
use App\Models\User;
use App\Models\UserType;
use App\Models\WareHouse;
use App\Models\WarehouseProduct;
use App\Models\WarehouseProductVariation;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Str;

class RecycleBinController extends Controller
{
    /**
     * List of tables with their model mappings
     */
    private array $recyclableModels = [
        // Users / access
        'users' => User::class,
        'user_types' => UserType::class,
        'register_agreements' => RegisterAgreement::class,
        'signup_rules' => SignupRule::class,

        // Geography
        'countries' => Country::class,

        // Memberships
        'membership_tiers' => MembershipTier::class,
        'membership_promo_codes' => MembershipPromoCode::class,
        'plans' => Plan::class,

        // Organizations / Ecclesias
        'our_organizations' => OurOrganization::class,
        'our_governances' => OurGovernance::class,
        'organizations' => Organization::class,
        'organization_centers' => OrganizationCenter::class,
        'ecclesias' => Ecclesia::class,
        'ecclesia_associations' => EcclesiaAssociation::class,
        'private_collaborations' => PrivateCollaboration::class,
        'principal_and_businesses' => PrincipalAndBusiness::class,

        // CMS / Pages / Settings
        'home_cms' => HomeCms::class,
        'about_us' => AboutUs::class,
        'site_settings' => SiteSetting::class,
        'footers' => Footer::class,
        'menu_items' => MenuItem::class,
        'galleries' => Gallery::class,
        'contact_us' => ContactUs::class,
        'contact_us_cms' => ContactUsCms::class,
        'articles' => Article::class,
        'bulletins' => Bulletin::class,
        'faqs' => Faq::class,
        'jobs' => Job::class,
        'meetings' => Meeting::class,
        'details' => Detail::class,
        'services' => Service::class,
        'strategies' => Strategy::class,
        'testimonials' => Testimonial::class,
        'pma_terms' => PmaTerm::class,
        'policies' => Policy::class,
        'privacy_policies' => PrivacyPolicy::class,
        'terms_and_conditions' => TermsAndCondition::class,
        'member_privacy_policies' => MemberPrivacyPolicy::class,

        // Estore (catalog)
        'categories' => Category::class,
        'colors' => Color::class,
        'sizes' => Size::class,
        'topics' => Topic::class,
        'products' => Product::class,
        'ware_houses' => WareHouse::class,
        'warehouse_products' => WarehouseProduct::class,
        'estore_promo_codes' => EstorePromoCode::class,
        'estore_settings' => EstoreSetting::class,
        'order_email_templates' => OrderEmailTemplate::class,
        'order_statuses' => OrderStatus::class,

        // Ecom CMS
        'ecom_cms_pages' => EcomCmsPage::class,
        'ecom_contact_cms' => EcomContactCms::class,
        'ecom_footer_cms' => EcomFooterCms::class,
        'ecom_home_cms' => EcomHomeCms::class,
        'ecom_newsletters' => EcomNewsletter::class,

        // Elearning
        'elearning_categories' => ElearningCategory::class,
        'elearning_sub_categories' => ElearningSubCategory::class,
        'elearning_products' => ElearningProduct::class,
        'elearning_topics' => ElearningTopic::class,
        'elearning_ecom_cms_pages' => ElearningEcomCmsPage::class,
        'elearning_ecom_footer_cms' => ElearningEcomFooterCms::class,
        'elearning_ecom_home_cms' => ElearningEcomHomeCms::class,
        'elearning_ecom_newsletters' => ElearningEcomNewsletter::class,

        // Events
        'events' => Event::class,

        // Communications
        'newsletters' => Newsletter::class,

        // Chatbot
        'chatbot_keywords' => ChatbotKeyword::class,

        // Files
        'files' => File::class,
        // Excluded: transactional/audit/runtime data (orders, payments, refunds, carts, RSVPs,
        // chats, chatbot conversations/messages/analytics/feedback, notifications, OTPs, mail logs,
        // user activities/addresses/subscriptions, donations, subscription payments) - read-only
        // or generated by user actions, not managed via a CRUD module.
        // Excluded: child rows managed via parent forms (product images/variations/colors/sizes/
        // other charges, organization images/projects, principle business images, footer social
        // links, membership benefits/measurements, warehouse product variations) - these are
        // cascade-restored when the parent is restored (see restoreProductRelatedData).
        // Excluded: 'roles' (Spatie, no SoftDeletes), 'warehouse_product_images' (no migration).
    ];

    /**
     * Display name overrides for tables whose Str::title() output is awkward
     */
    private array $displayNameOverrides = [
        'home_cms' => 'Home CMS',
        'site_settings' => 'Site Settings',
        'contact_us' => 'Contact Us',
        'contact_us_cms' => 'Contact Us CMS',
        'about_us' => 'About Us',
        'pma_terms' => 'PMA Terms',
        'ecom_cms_pages' => 'Ecom CMS Pages',
        'ecom_contact_cms' => 'Ecom Contact CMS',
        'ecom_footer_cms' => 'Ecom Footer CMS',
        'ecom_home_cms' => 'Ecom Home CMS',
        'elearning_ecom_cms_pages' => 'Elearning Ecom CMS Pages',
        'elearning_ecom_footer_cms' => 'Elearning Ecom Footer CMS',
        'elearning_ecom_home_cms' => 'Elearning Ecom Home CMS',
        'estore_promo_codes' => 'Estore Promo Codes',
        'estore_settings' => 'Estore Settings',
        'ware_houses' => 'Warehouses',
    ];

    /**
     * Display the recycle bin dashboard
     */
    public function index()
    {
        $recycleCounts = $this->getRecycleCounts();

        return view('user.recycle-bin.index', compact('recycleCounts'));
    }

    /**
     * Get counts of deleted items for each table
     */
    private function getRecycleCounts()
    {
        $counts = [];

        foreach ($this->recyclableModels as $table => $model) {
            $counts[$table] = [
                'name' => $this->getTableDisplayName($table),
                'count' => $model::onlyTrashed()->count(),
                'table' => $table
            ];
        }

        return collect($counts)->sortByDesc('count');
    }

    /**
     * View deleted items for a specific table
     */
    public function show(Request $request, $table)
    {
        if (!isset($this->recyclableModels[$table])) {
            abort(404, 'Table not found in recycle bin');
        }

        $model = $this->recyclableModels[$table];
        $tableName = $this->getTableDisplayName($table);

        // Get deleted items with pagination
        $query = $model::onlyTrashed();

        // Load relationships for specific tables
        if ($table === 'warehouse_products') {
            $query->with(['product', 'warehouse']);
        }

        $deletedItems = $query->orderBy('deleted_at', 'desc')
            ->paginate(20);

        // Get columns for this table
        $columns = $this->getTableColumns($table);

        return view('user.recycle-bin.show', compact('deletedItems', 'tableName', 'table', 'columns'));
    }

    /**
     * Restore a single item
     */
    public function restore(Request $request, $table, $id)
    {
        if (!isset($this->recyclableModels[$table])) {
            return response()->json(['success' => false, 'message' => 'Invalid table'], 400);
        }

        $model = $this->recyclableModels[$table];
        $item = $model::onlyTrashed()->find($id);

        if (!$item) {
            return response()->json(['success' => false, 'message' => 'Item not found'], 404);
        }

        // Restore the item
        $item->restore();

        // If restoring a product, also restore related data
        if ($table === 'products') {
            $this->restoreProductRelatedData($id);
        }

        if ($request->ajax()) {
            return response()->json([
                'success' => true,
                'message' => 'Item restored successfully'
            ]);
        }

        return redirect()->route('user.recycle-bin.show', $table)
            ->with('success', 'Item restored successfully');
    }

    /**
     * Permanently delete a single item
     */
    public function forceDelete(Request $request, $table, $id)
    {
        if (!isset($this->recyclableModels[$table])) {
            return response()->json(['success' => false, 'message' => 'Invalid table'], 400);
        }

        $model = $this->recyclableModels[$table];
        $item = $model::onlyTrashed()->find($id);

        if (!$item) {
            return response()->json(['success' => false, 'message' => 'Item not found'], 404);
        }

        $item->forceDelete();

        if ($request->ajax()) {
            return response()->json([
                'success' => true,
                'message' => 'Item permanently deleted'
            ]);
        }

        return redirect()->route('user.recycle-bin.show', $table)
            ->with('success', 'Item permanently deleted');
    }

    /**
     * Bulk restore items
     */
    public function bulkRestore(Request $request, $table)
    {
        $request->validate([
            'ids' => 'required|array',
            'ids.*' => 'required|integer'
        ]);

        if (!isset($this->recyclableModels[$table])) {
            return response()->json(['success' => false, 'message' => 'Invalid table'], 400);
        }

        $model = $this->recyclableModels[$table];
        $restored = 0;

        foreach ($request->ids as $id) {
            $item = $model::onlyTrashed()->find($id);
            if ($item) {
                $item->restore();

                // If restoring a product, also restore related data
                if ($table === 'products') {
                    $this->restoreProductRelatedData($id);
                }

                $restored++;
            }
        }

        return response()->json([
            'success' => true,
            'message' => "$restored item(s) restored successfully"
        ]);
    }

    /**
     * Bulk permanently delete items
     */
    public function bulkForceDelete(Request $request, $table)
    {
        $request->validate([
            'ids' => 'required|array',
            'ids.*' => 'required|integer'
        ]);

        if (!isset($this->recyclableModels[$table])) {
            return response()->json(['success' => false, 'message' => 'Invalid table'], 400);
        }

        $model = $this->recyclableModels[$table];
        $deleted = 0;

        foreach ($request->ids as $id) {
            $item = $model::onlyTrashed()->find($id);
            if ($item) {
                $item->forceDelete();
                $deleted++;
            }
        }

        return response()->json([
            'success' => true,
            'message' => "$deleted item(s) permanently deleted"
        ]);
    }

    /**
     * Restore all items for a table
     */
    public function restoreAll($table)
    {
        if (!isset($this->recyclableModels[$table])) {
            return response()->json(['success' => false, 'message' => 'Invalid table'], 400);
        }

        $model = $this->recyclableModels[$table];
        $count = $model::onlyTrashed()->count();

        $model::onlyTrashed()->restore();

        return redirect()->route('user.recycle-bin.show', $table)
            ->with('success', "All $count item(s) restored successfully");
    }

    /**
     * Empty recycle bin for a table (permanently delete all)
     */
    public function emptyBin(Request $request, $table)
    {
        if (!isset($this->recyclableModels[$table])) {
            return response()->json(['success' => false, 'message' => 'Invalid table'], 400);
        }

        $model = $this->recyclableModels[$table];
        $count = $model::onlyTrashed()->count();

        $model::onlyTrashed()->forceDelete();

        if ($request->ajax()) {
            return response()->json([
                'success' => true,
                'message' => "Recycle bin emptied. $count item(s) permanently deleted"
            ]);
        }

        return redirect()->route('user.recycle-bin.index')
            ->with('success', "Recycle bin emptied. $count item(s) permanently deleted");
    }

    /**
     * Get display name for table
     */
    private function getTableDisplayName($table)
    {
        return $this->displayNameOverrides[$table]
            ?? Str::title(str_replace('_', ' ', $table));
    }

    /**
     * Get columns to display for a table
     */
    private function getTableColumns($table)
    {
        $defaultColumns = ['id', 'created_at', 'updated_at', 'deleted_at'];

        $tableColumns = [
            // Users / access
            'users' => ['id', 'first_name', 'last_name', 'email', 'phone'],
            'user_types' => ['id', 'name', 'type'],
            'register_agreements' => ['id', 'title'],
            'signup_rules' => ['id', 'title'],

            // Geography
            'countries' => ['id', 'name', 'code', 'status'],

            // Memberships
            'membership_tiers' => ['id', 'name', 'cost', 'pricing_type'],
            'membership_promo_codes' => ['id', 'code', 'discount_amount', 'status'],
            'plans' => ['id', 'name', 'price', 'status'],

            // Organizations / Ecclesias
            'our_organizations' => ['id', 'title', 'description'],
            'our_governances' => ['id', 'title'],
            'organizations' => ['id', 'name', 'description'],
            'organization_centers' => ['id', 'organization_id', 'name'],
            'ecclesias' => ['id', 'name', 'description'],
            'ecclesia_associations' => ['id', 'ecclesia_id', 'name'],
            'private_collaborations' => ['id', 'name'],
            'principal_and_businesses' => ['id', 'name', 'title'],

            // CMS / Pages / Settings
            'home_cms' => ['id', 'title'],
            'about_us' => ['id', 'title'],
            'site_settings' => ['id', 'site_name', 'site_email'],
            'footers' => ['id', 'title'],
            'menu_items' => ['id', 'name', 'slug'],
            'galleries' => ['id', 'title', 'image'],
            'contact_us' => ['id', 'name', 'email', 'subject'],
            'contact_us_cms' => ['id', 'title'],
            'articles' => ['id', 'title', 'slug'],
            'bulletins' => ['id', 'title', 'description'],
            'faqs' => ['id', 'question'],
            'jobs' => ['id', 'title', 'status'],
            'meetings' => ['id', 'title', 'start_time'],
            'details' => ['id', 'title'],
            'services' => ['id', 'title'],
            'strategies' => ['id', 'title'],
            'testimonials' => ['id', 'name', 'designation'],
            'pma_terms' => ['id', 'title'],
            'policies' => ['id', 'title'],
            'privacy_policies' => ['id', 'title'],
            'terms_and_conditions' => ['id', 'title'],
            'member_privacy_policies' => ['id', 'title'],

            // Estore (catalog)
            'categories' => ['id', 'name', 'slug'],
            'colors' => ['id', 'name', 'code'],
            'sizes' => ['id', 'name', 'code'],
            'topics' => ['id', 'topic_name', 'education_type'],
            'products' => ['id', 'name', 'product_type', 'price'],
            'ware_houses' => ['id', 'name', 'address'],
            'warehouse_products' => ['id', 'product_id', 'warehouse_id'],
            'estore_promo_codes' => ['id', 'code', 'discount_type', 'discount_value'],
            'estore_settings' => ['id', 'name'],
            'order_email_templates' => ['id', 'name', 'subject'],
            'order_statuses' => ['id', 'name'],

            // Ecom CMS
            'ecom_cms_pages' => ['id', 'title', 'slug'],
            'ecom_contact_cms' => ['id', 'title'],
            'ecom_footer_cms' => ['id', 'title'],
            'ecom_home_cms' => ['id', 'title'],
            'ecom_newsletters' => ['id', 'email'],

            // Elearning
            'elearning_categories' => ['id', 'name', 'slug', 'status'],
            'elearning_sub_categories' => ['id', 'name', 'slug', 'status'],
            'elearning_products' => ['id', 'name', 'affiliate_link'],
            'elearning_topics' => ['id', 'topic_name'],
            'elearning_ecom_cms_pages' => ['id', 'title', 'slug'],
            'elearning_ecom_footer_cms' => ['id', 'title'],
            'elearning_ecom_home_cms' => ['id', 'title'],
            'elearning_ecom_newsletters' => ['id', 'email'],

            // Events
            'events' => ['id', 'title', 'start_date', 'status'],

            // Communications
            'newsletters' => ['id', 'email'],

            // Chatbot
            'chatbot_keywords' => ['id', 'keyword', 'response'],

            // Files
            'files' => ['id', 'file_name', 'type'],
        ];

        return $tableColumns[$table] ?? $defaultColumns;
    }

    /**
     * Restore product related data
     */
    private function restoreProductRelatedData($productId)
    {
        // Restore product images
        ProductImage::onlyTrashed()
            ->where('product_id', $productId)
            ->restore();

        // Restore product other charges
        ProductOtherCharge::onlyTrashed()
            ->where('product_id', $productId)
            ->restore();

        // Restore product variations
        ProductVariation::onlyTrashed()
            ->where('product_id', $productId)
            ->restore();

        // Restore warehouse product variations
        WarehouseProductVariation::onlyTrashed()
            ->where('product_id', $productId)
            ->restore();
    }
}
