<?php

namespace App\Http\Controllers\User;

use App\Http\Controllers\Controller;
use App\Services\PmaDocumentationService;
use Illuminate\Support\Facades\File;
use Illuminate\View\View;
use Symfony\Component\HttpFoundation\BinaryFileResponse;
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
            'html' => $this->rewriteAttachmentUrls($document['html']),
            'status' => $status,
            'backUrl' => $backUrl,
            'backLabel' => $backLabel,
        ]);
    }

    public function attachment(string $path): BinaryFileResponse
    {
        $relativeFile = str_starts_with($path, 'attachments/') ? $path : 'attachments/'.$path;
        $realPath = $this->documentation->attachmentPath($relativeFile);

        if ($realPath === null) {
            throw new NotFoundHttpException('Documentation attachment not found.');
        }

        return response()->file($realPath, [
            'Content-Type' => File::mimeType($realPath) ?: 'application/octet-stream',
        ]);
    }

    private function rewriteAttachmentUrls(string $html): string
    {
        // url() / UrlGenerator strips a trailing slash, which glued
        // ".../attachments" onto the relative path ("attachmentsfoo.png").
        $prefix = substr(
            route('user.documentation.attachment', ['path' => '__att__']),
            0,
            -strlen('__att__')
        );

        return (string) preg_replace(
            '#(src|href)="attachments/#',
            '$1="'.$prefix,
            $html
        );
    }
}
