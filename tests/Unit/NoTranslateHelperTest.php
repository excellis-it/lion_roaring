<?php

namespace Tests\Unit;

use App\Helpers\Helper;
use Illuminate\Support\HtmlString;
use Tests\TestCase;

class NoTranslateHelperTest extends TestCase
{
    public function test_helper_wraps_text_with_notranslate_markers(): void
    {
        $html = Helper::noTranslate('Daud Smith')->toHtml();

        $this->assertStringContainsString('class="notranslate"', $html);
        $this->assertStringContainsString('translate="no"', $html);
        $this->assertStringContainsString('Daud Smith', $html);
    }

    public function test_helper_escapes_html_in_names(): void
    {
        $html = Helper::noTranslate('<script>alert(1)</script>')->toHtml();

        $this->assertStringNotContainsString('<script>', $html);
        $this->assertStringContainsString('&lt;script&gt;', $html);
    }

    public function test_global_helper_function_matches_helper_class(): void
    {
        $viaFunction = no_translate('Jane Doe');
        $viaClass = Helper::noTranslate('Jane Doe');

        $this->assertInstanceOf(HtmlString::class, $viaFunction);
        $this->assertSame($viaClass->toHtml(), $viaFunction->toHtml());
    }
}
