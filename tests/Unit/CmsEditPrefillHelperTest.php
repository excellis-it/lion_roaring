<?php

namespace Tests\Unit;

use App\Helpers\Helper;
use App\Models\Faq;
use App\Models\HomeCms;
use App\Models\User;
use Illuminate\Foundation\Testing\DatabaseTransactions;
use Illuminate\Http\Request;
use Tests\TestCase;

class CmsEditPrefillHelperTest extends TestCase
{
    use DatabaseTransactions;

    public function test_can_select_cms_content_country_for_global_user(): void
    {
        $user = new class extends User {
            public function hasNewRole($roles): bool
            {
                return false;
            }
        };
        $user->user_type = 'Global';
        $this->actingAs($user);

        $this->assertTrue(Helper::canSelectCmsContentCountry());
    }

    public function test_can_select_cms_content_country_false_for_regional_non_sa(): void
    {
        $user = new class extends User {
            public function hasNewRole($roles): bool
            {
                return false;
            }
        };
        $user->user_type = 'Regional';
        $this->actingAs($user);

        $this->assertFalse(Helper::canSelectCmsContentCountry());
    }

    public function test_resolve_uses_request_when_selectable(): void
    {
        $user = new class extends User {
            public function hasNewRole($roles): bool
            {
                return false;
            }
        };
        $user->user_type = 'Global';
        $this->actingAs($user);

        $request = Request::create('/user/admin/home', 'GET', ['content_country_code' => 'IN']);
        $this->assertSame('IN', Helper::resolveCmsEditCountryCode($request, 'US'));
    }

    public function test_resolve_uses_regional_when_not_selectable(): void
    {
        $user = new class extends User {
            public function hasNewRole($roles): bool
            {
                return false;
            }
        };
        $user->user_type = 'Regional';
        $this->actingAs($user);

        $request = Request::create('/x', 'GET', ['content_country_code' => 'IN']);
        $this->assertSame('DE', Helper::resolveCmsEditCountryCode($request, 'DE'));
    }

    public function test_load_row_returns_country_row_when_present(): void
    {
        $us = HomeCms::query()->where('country_code', 'US')->orderByDesc('id')->first();
        if (!$us) {
            $this->markTestSkipped('No US HomeCms row');
        }

        $result = Helper::loadCmsRowForEdit(HomeCms::class, 'US');
        $this->assertFalse($result['isUsPrefill']);
        $this->assertNotNull($result['row']);
        $this->assertNotNull($result['row']->id);
        $this->assertSame('US', $result['countryCode']);
    }

    public function test_load_row_prefills_us_without_id_when_country_missing(): void
    {
        $us = HomeCms::query()->where('country_code', 'US')->orderByDesc('id')->first();
        if (!$us) {
            $this->markTestSkipped('No US HomeCms row');
        }

        $missingCode = 'ZZ';
        HomeCms::query()->where('country_code', $missingCode)->delete();

        $result = Helper::loadCmsRowForEdit(HomeCms::class, $missingCode);
        $this->assertTrue($result['isUsPrefill']);
        $this->assertNotNull($result['row']);
        $this->assertNull($result['row']->id);
        $this->assertSame($us->banner_title, $result['row']->banner_title);
        $this->assertSame($missingCode, $result['countryCode']);
        $this->assertSame($missingCode, $result['row']->country_code);
    }

    public function test_load_rows_prefills_us_drafts_without_ids(): void
    {
        $usCount = Faq::query()->where('country_code', 'US')->count();
        if ($usCount === 0) {
            $this->markTestSkipped('No US Faq rows');
        }

        $missingCode = 'ZZ';
        Faq::query()->where('country_code', $missingCode)->delete();

        $result = Helper::loadCmsRowsForEdit(Faq::class, $missingCode, 'id', 'asc');
        $this->assertTrue($result['isUsPrefill']);
        $this->assertGreaterThan(0, $result['rows']->count());
        foreach ($result['rows'] as $row) {
            $this->assertNull($row->id);
            $this->assertSame($missingCode, $row->country_code);
        }
    }
}
