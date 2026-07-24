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
        return view('user.documentation.index', [
            'hubs' => $this->documentation->hubs(),
        ]);
    }

    public function show(string $section): View
    {
        $hub = $this->documentation->findHub($section);
        $detail = $hub === null ? $this->documentation->findSection($section) : null;

        if ($hub === null && $detail === null) {
            throw new NotFoundHttpException('Documentation section not found.');
        }

        $entry = $hub ?? $detail;
        $document = $this->documentation->loadDocument($entry['file']);
        $status = $document['meta']['status'] ?? $entry['status'] ?? 'coming_soon';

        $parentHub = null;
        $childSections = [];
        $backUrl = route('user.documentation.index');
        $backLabel = 'All documentation';

        if ($hub !== null) {
            $childSections = $this->documentation->sectionsForHub($hub['slug']);
        } else {
            $parentHub = $this->documentation->findHub($detail['hub'] ?? '');
            if ($parentHub !== null) {
                $backUrl = route('user.documentation.show', $parentHub['slug']);
                $backLabel = $parentHub['title'];
            }
        }

        return view('user.documentation.show', [
            'section' => $entry,
            'isHub' => $hub !== null,
            'parentHub' => $parentHub,
            'childSections' => $childSections,
            'meta' => $document['meta'],
            'html' => $document['html'],
            'status' => $status,
            'backUrl' => $backUrl,
            'backLabel' => $backLabel,
        ]);
    }
}
