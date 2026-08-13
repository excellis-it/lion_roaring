<?php

namespace Tests\Feature;

use Tests\TestCase;

class PdfViewerScrollbarTest extends TestCase
{
    /** @var list<string> */
    private array $viewerViews = [
        'views/user/becoming-sovereign/view.blade.php',
        'views/user/becoming-christ-link/view.blade.php',
        'views/user/leadership-development/view.blade.php',
        'views/user/file/view.blade.php',
        'views/user/policy/view.blade.php',
        'views/user/strategy/view.blade.php',
    ];

    public function test_pdf_viewer_pages_do_not_pin_document_pane_at_900px(): void
    {
        $partial = file_get_contents(resource_path('views/user/includes/pdf-document-viewer.blade.php'));
        $this->assertStringNotContainsString('max-height: 900px', $partial);

        foreach ($this->viewerViews as $relative) {
            $contents = file_get_contents(resource_path($relative));

            $this->assertStringNotContainsString(
                'max-height: 900px',
                $contents,
                $relative.' must not pin the PDF pane at 900px — that creates a page scrollbar plus a viewer scrollbar.'
            );
        }
    }

    public function test_all_pma_document_modules_use_the_shared_viewport_fitted_pdf_viewer(): void
    {
        $css = file_get_contents(public_path('user_assets/css/style.css'));
        $js = file_get_contents(public_path('user_assets/js/pdf-viewer-fit.js'));
        $partial = file_get_contents(resource_path('views/user/includes/pdf-document-viewer.blade.php'));

        $this->assertStringContainsString('BUG-0015', $css);
        $this->assertStringContainsString('body:has(#pdf-viewer-wrapper)', $css);
        $this->assertStringContainsString('function fitPdfViewerToViewport', $js);
        $this->assertStringContainsString('fitPdfViewerToViewport', $partial);
        $this->assertStringContainsString("wrapper.style.display  = 'flex'", $partial);

        foreach ($this->viewerViews as $relative) {
            $contents = file_get_contents(resource_path($relative));

            $this->assertStringContainsString(
                "user.includes.pdf-document-viewer",
                $contents,
                $relative.' must use the shared PDF viewer so Education, Strategy, Policy, and Files stay in sync.'
            );
        }
    }
}
