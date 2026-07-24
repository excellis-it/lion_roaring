<?php

namespace App\Http\Controllers\User;

use App\Http\Controllers\Controller;
use App\Services\PmaDocumentationService;
use Illuminate\View\View;
use Symfony\Component\HttpKernel\Exception\NotFoundHttpException;

class DocumentationController extends Controller
{
    public function __construct(
        private readonly PmaDocumentationService $documentation
    ) {
    }

    public function index(): View
    {
        $overview = $this->documentation->loadDocument('index.md');
        $sections = $this->documentation->sections();

        return view('user.documentation.index', [
            'overviewHtml' => $overview['html'],
            'overviewMeta' => $overview['meta'],
            'sections' => $sections,
        ]);
    }

    public function show(string $section): View
    {
        $configSection = $this->documentation->findSection($section);
        if ($configSection === null) {
            throw new NotFoundHttpException('Documentation section not found.');
        }

        $document = $this->documentation->loadDocument($configSection['file']);
        $status = $document['meta']['status'] ?? $configSection['status'] ?? 'coming_soon';

        return view('user.documentation.show', [
            'section' => $configSection,
            'sections' => $this->documentation->sections(),
            'meta' => $document['meta'],
            'html' => $document['html'],
            'status' => $status,
        ]);
    }
}
