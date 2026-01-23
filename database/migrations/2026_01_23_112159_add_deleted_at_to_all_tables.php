<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

class AddDeletedAtToAllTables extends Migration
{

private array $tables = [
            'about_us',
            'articles',
            'bulletins',
            'categories',
            'chat_members',
            'chatbot_analytics',
            'chatbot_conversations',
            'chatbot_feedback',
            'chatbot_keywords',
            'chatbot_messages',
            'chats',
            'collaboration_invitations',
            'colors',
            'contact_us',
            'contact_us_cms',
            'countries',
            'country_translate_language',
            'details',
            'donations',
            'ecclesia_associations',
            'ecclesias',
            'ecom_cms_pages',
            'ecom_contact_cms',
            'ecom_footer_cms',
            'ecom_home_cms',
            'ecom_newsletters',
            'ecom_wish_lists',
            'elearning_categories',
            'elearning_ecom_cms_pages',
            'elearning_ecom_footer_cms',
            'elearning_ecom_home_cms',
            'elearning_ecom_newsletters',
            'elearning_product_images',
            'elearning_products',
            'elearning_reviews',
            'elearning_topics',
            'estore_carts',
            'estore_order_items',
            'estore_orders',
            'estore_payments',
            'estore_promo_codes',
            'estore_refunds',
            'estore_settings',
            'event_payments',
            'event_rsvps',
            'events',
            'faqs',
            'files',
            'footer_social_links',
            'footers',
            'galleries',
            'global_images',
            'home_cms',
            'jobs',
            'mail_users',
            'meetings',
            'member_privacy_policies',
            'membership_benefits',
            'membership_measurements',
            'membership_tiers',
            'menu_items',
            'model_has_permissions',
            'model_has_roles',
            'newsletters',
            'notifications',
            'order_email_templates',
            'order_statuses',
            'organization_centers',
            'organization_images',
            'organization_projects',
            'organizations',
            'our_governances',
            'our_organizations',
            'plans',
            'pma_terms',
            'policies',
            'principal_and_businesses',
            'principle_business_images',
            'privacy_policies',
            'private_collaborations',
            'product_color_images',
            'product_colors',
            'product_images',
            'product_other_charges',
            'product_sizes',
            'product_variation_images',
            'product_variations',
            'products',
            'register_agreements',
            'reviews',
            'roles',
            'send_mails',
            'services',
            'signup_rules',
            'site_settings',
            'sizes',
            'states',
            'strategies',
            'subscription_payments',
            'system_notifications',
            'team_chats',
            'team_members',
            'teams',
            'terms_and_conditions',
            'testimonials',
            'topics',
            'translate_languages',
            'user_activities',
            'user_addresses',
            'user_register_agreements',
            'user_subscriptions',
            'user_types',
            'user_warehouses',
            'users',
            'verify_o_t_p_s',
            'ware_houses',
            'warehouse_product_images',
            'warehouse_product_variations',
            'warehouse_products'
    ];

    public function up()
    {
        foreach ($this->tables as $tableName) {
            if (!Schema::hasColumn($tableName, 'deleted_at')) {
                Schema::table($tableName, function (Blueprint $table) {
                    $table->softDeletes();
                });
            }
        }
    }

    public function down()
    {
        foreach ($this->tables as $tableName) {
            if (Schema::hasColumn($tableName, 'deleted_at')) {
                Schema::table($tableName, function (Blueprint $table) {
                    $table->dropSoftDeletes();
                });
            }
        }
    }


    
}
