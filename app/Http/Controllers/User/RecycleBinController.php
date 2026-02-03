<?php

namespace App\Http\Controllers\User;

use App\Http\Controllers\Controller;
use App\Models\Category;
use App\Models\ChatbotKeyword;
use App\Models\Color;
use App\Models\Country;
use App\Models\Ecclesia;
use App\Models\EcomCmsPage;
use App\Models\EcomNewsletter;
use App\Models\ElearningProduct;
use App\Models\EstorePromoCode;
use App\Models\MembershipTier;
use App\Models\OurOrganization;
use App\Models\Plan;
use App\Models\Product;
use App\Models\Size;
use App\Models\User;
use App\Models\UserType;
use App\Models\WarehouseProduct;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Str;

class RecycleBinController extends Controller
{
    /**
     * List of tables with their model mappings
     */
    private array $recyclableModels = [
        'users' => User::class,
        'countries' => Country::class,
        'membership_tiers' => MembershipTier::class,
        'our_organizations' => OurOrganization::class,
        'plans' => Plan::class,
        'colors' => Color::class,
        'ecclesias' => Ecclesia::class,
        'ecom_cms_pages' => EcomCmsPage::class,
        'ecom_newsletters' => EcomNewsletter::class,
        'sizes' => Size::class,
        'categories' => Category::class,
        'products' => Product::class,
        'elearning_products' => ElearningProduct::class,
        'estore_promo_codes' => EstorePromoCode::class,
        'user_types' => UserType::class,
        'warehouse_products' => WarehouseProduct::class,
        'chatbot_keywords' => ChatbotKeyword::class,
        // Note: 'roles' removed - Spatie Role model doesn't use SoftDeletes by default
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
        $deletedItems = $model::onlyTrashed()
            ->orderBy('deleted_at', 'desc')
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

        $item->restore();

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
        return Str::title(str_replace('_', ' ', $table));
    }

    /**
     * Get columns to display for a table
     */
    private function getTableColumns($table)
    {
        $defaultColumns = ['id', 'created_at', 'updated_at', 'deleted_at'];

        $tableColumns = [
            'users' => ['id', 'first_name', 'last_name', 'email', 'phone'],
            'countries' => ['id', 'name', 'code', 'status'],
            'membership_tiers' => ['id', 'name', 'cost', 'pricing_type'],
            'our_organizations' => ['id', 'title', 'description'],
            'plans' => ['id', 'name', 'price', 'status'],
            'colors' => ['id', 'name', 'code'],
            'ecclesias' => ['id', 'name', 'description'],
            'ecom_cms_pages' => ['id', 'title', 'slug'],
            'ecom_newsletters' => ['id', 'email'],
            'sizes' => ['id', 'name', 'code'],
            'categories' => ['id', 'name', 'slug'],
            'products' => ['id', 'name', 'product_type', 'price'],
            'elearning_products' => ['id', 'title', 'price'],
            'estore_promo_codes' => ['id', 'code', 'discount_type', 'discount_value'],
            'user_types' => ['id', 'name', 'type'],
            'warehouse_products' => ['id', 'product_id', 'warehouse_id'],
            'chatbot_keywords' => ['id', 'keyword', 'response'],
        ];

        return $tableColumns[$table] ?? $defaultColumns;
    }
}
