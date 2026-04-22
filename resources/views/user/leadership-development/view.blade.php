@extends('user.layouts.master')
@section('title')
Becoming a Leader View - {{ env('APP_NAME') }}
@endsection
@push('styles')
    <link rel="stylesheet" href="https://cdn.jsdelivr.net/npm/@fullcalendar/core@4.2.0/main.min.css">
    <link rel="stylesheet" href="https://cdn.jsdelivr.net/npm/@fullcalendar/daygrid@4.3.0/main.min.css">
    <style>
        .fc-button-primary { background-color: #7851a9 !important; border-color: #7851a9 !important; }

        .file-loader-overlay {
            display: flex; flex-direction: column; align-items: center; justify-content: center;
            min-height: 420px; padding: 40px 20px; background: #f8f8f8; border-radius: 4px;
        }
        .file-loader-overlay .spinner-border { width: 3rem; height: 3rem; color: #7851a9; }
        .pdf-progress-wrap { width: 340px; max-width: 90%; margin-top: 18px; }
        .pdf-progress-meta { display: flex; justify-content: space-between; font-size: 13px; color: #555; margin-bottom: 6px; }
        .pdf-progress-track { background: #ddd; border-radius: 6px; height: 9px; overflow: hidden; }
        .pdf-progress-bar { background: #7851a9; height: 100%; width: 0%; border-radius: 6px; transition: width 0.25s ease; }

        .pdf-viewer-wrapper { background: #525659; border-radius: 4px; overflow: hidden; }
        .pdf-toolbar {
            background: #323639; padding: 8px 16px; display: flex; align-items: center;
            gap: 10px; color: #fff; font-size: 14px; flex-wrap: wrap;
        }
        .pdf-toolbar button {
            background: #4a4d50; color: #fff; border: none; border-radius: 3px;
            padding: 5px 12px; cursor: pointer; font-size: 14px;
        }
        .pdf-toolbar button:hover:not(:disabled) { background: #6a6d70; }
        .pdf-toolbar button:disabled { opacity: 0.4; cursor: not-allowed; }
        .pdf-toolbar .tb-spacer { flex: 1; }
        #pdf-zoom-label { min-width: 42px; text-align: center; }
        .pdf-pages-container {
            overflow-y: auto; max-height: 900px; padding: 20px;
            display: flex; flex-direction: column; align-items: center; gap: 12px;
        }
        .pdf-page-canvas { display: block; box-shadow: 0 2px 10px rgba(0,0,0,0.6); max-width: 100%; }
    </style>
@endpush
@section('content')
    <div class="container-fluid">
        <div class="bg_white_border">
            <div class="row">
                <div class="col-lg-12">
                    <div class="row">
                        <div class="col-md-12">
                            <div class="row mb-3">
                                <div class="col-md-10"></div>
                                <div class="row">
                                    <div class="col-md-10">
                                        <h3 class="mb-3 float-left">Becoming a Leader File</h3>
                                    </div>
                                    <div class="col-md-2">
                                        <a href="{{ route('leadership-development.index') . '?topic=' . ($new_topic ?? '') }}" class="btn btn-primary w-100">< Back</a>
                                    </div>
                                </div>
                                <div>
                                    @if ($file->file_extension == 'pdf')

                                        <div id="fileLoaderOverlay" class="file-loader-overlay">
                                            <div class="spinner-border" role="status"><span class="visually-hidden">Loading...</span></div>
                                            <p class="mt-3 mb-0 text-muted" id="pdf-loading-text">Loading PDF...</p>
                                            <div class="pdf-progress-wrap" id="pdf-progress-wrap" style="display:none;">
                                                <div class="pdf-progress-meta">
                                                    <span id="pdf-progress-pct">0%</span>
                                                    <span id="pdf-file-size"></span>
                                                </div>
                                                <div class="pdf-progress-track">
                                                    <div class="pdf-progress-bar" id="pdf-progress-bar"></div>
                                                </div>
                                            </div>
                                        </div>

                                        <div id="pdf-viewer-wrapper" class="pdf-viewer-wrapper" style="display:none;">
                                            <div class="pdf-toolbar">
                                                <button id="pdf-prev" disabled>&#8249; Prev</button>
                                                <span id="pdf-page-info">Page 1 of ...</span>
                                                <button id="pdf-next" disabled>Next &#8250;</button>
                                                <span class="tb-spacer"></span>
                                                <button id="pdf-zoom-out">&#8722;</button>
                                                <span id="pdf-zoom-label">100%</span>
                                                <button id="pdf-zoom-in">&#43;</button>
                                            </div>
                                            <div class="pdf-pages-container" id="pdf-pages-container"></div>
                                        </div>

                                        <script src="https://cdnjs.cloudflare.com/ajax/libs/pdf.js/3.11.174/pdf.min.js"></script>
                                        <script>
                                        (function () {
                                            pdfjsLib.GlobalWorkerOptions.workerSrc =
                                                'https://cdnjs.cloudflare.com/ajax/libs/pdf.js/3.11.174/pdf.worker.min.js';
                                            const pdfUrl    = {!! json_encode(Storage::url($file->file)) !!};
                                            const overlay   = document.getElementById('fileLoaderOverlay');
                                            const wrapper   = document.getElementById('pdf-viewer-wrapper');
                                            const container = document.getElementById('pdf-pages-container');
                                            const pageInfo  = document.getElementById('pdf-page-info');
                                            const prevBtn   = document.getElementById('pdf-prev');
                                            const nextBtn   = document.getElementById('pdf-next');
                                            const zoomLabel = document.getElementById('pdf-zoom-label');
                                            const loadText  = document.getElementById('pdf-loading-text');
                                            const progWrap  = document.getElementById('pdf-progress-wrap');
                                            const progBar   = document.getElementById('pdf-progress-bar');
                                            const progPct   = document.getElementById('pdf-progress-pct');
                                            const fileSize  = document.getElementById('pdf-file-size');
                                            let pdfDoc = null, currentPage = 1, zoomPct = 100;
                                            const BASE = 1.5;
                                            function scale() { return BASE * (zoomPct / 100); }
                                            function formatBytes(b) {
                                                if (b < 1024) return b + ' B';
                                                if (b < 1048576) return (b / 1024).toFixed(1) + ' KB';
                                                return (b / 1048576).toFixed(2) + ' MB';
                                            }
                                            function updateControls() {
                                                prevBtn.disabled = currentPage <= 1;
                                                nextBtn.disabled = currentPage >= pdfDoc.numPages;
                                                pageInfo.textContent = 'Page ' + currentPage + ' of ' + pdfDoc.numPages;
                                                zoomLabel.textContent = zoomPct + '%';
                                            }
                                            function renderPage(num) {
                                                pdfDoc.getPage(num).then(function (page) {
                                                    const vp = page.getViewport({ scale: scale() });
                                                    const canvas = document.createElement('canvas');
                                                    canvas.className = 'pdf-page-canvas';
                                                    canvas.width = vp.width; canvas.height = vp.height;
                                                    container.innerHTML = '';
                                                    container.appendChild(canvas);
                                                    return page.render({ canvasContext: canvas.getContext('2d'), viewport: vp }).promise;
                                                }).then(function () {
                                                    overlay.style.display = 'none';
                                                    wrapper.style.display  = 'block';
                                                    updateControls();
                                                });
                                            }
                                            const loadingTask = pdfjsLib.getDocument(pdfUrl);
                                            loadingTask.onProgress = function (data) {
                                                if (data.total > 0) {
                                                    progWrap.style.display = 'block';
                                                    const pct = Math.min(Math.round((data.loaded / data.total) * 100), 100);
                                                    progBar.style.width  = pct + '%';
                                                    progPct.textContent  = pct + '%';
                                                    fileSize.textContent = formatBytes(data.total);
                                                }
                                            };
                                            loadingTask.promise.then(function (pdf) {
                                                pdfDoc = pdf;
                                                loadText.textContent   = 'Rendering page...';
                                                progWrap.style.display = 'none';
                                                renderPage(currentPage);
                                            }).catch(function () {
                                                overlay.innerHTML = '<p class="text-danger p-4">Failed to load PDF. <a href="' + pdfUrl + '" target="_blank" rel="noopener">Open in new tab</a></p>';
                                            });
                                            prevBtn.addEventListener('click', function () { if (currentPage > 1) { currentPage--; renderPage(currentPage); } });
                                            nextBtn.addEventListener('click', function () { if (currentPage < pdfDoc.numPages) { currentPage++; renderPage(currentPage); } });
                                            document.getElementById('pdf-zoom-in').addEventListener('click', function () { zoomPct = Math.min(zoomPct + 25, 200); renderPage(currentPage); });
                                            document.getElementById('pdf-zoom-out').addEventListener('click', function () { zoomPct = Math.max(zoomPct - 25, 25); renderPage(currentPage); });
                                            document.addEventListener('contextmenu', e => e.preventDefault());
                                        })();
                                        </script>

                                    @elseif ($file->file_extension == 'jpg' || $file->file_extension == 'jpeg' || $file->file_extension == 'png' || $file->file_extension == 'gif' || $file->file_extension == 'svg' || $file->file_extension == 'webp')
                                        <img src="{{ Storage::url($file->file) }}" alt="file" width="100%" height="600px">
                                    @elseif ($file->file_extension == 'mp4' || $file->file_extension == 'webm')
                                        <video width="100%" height="600px" controls>
                                            <source src="{{ Storage::url($file->file) }}" type="video/mp4">
                                        </video>
                                    @elseif ($file->file_extension == 'mp3' || $file->file_extension == 'wav')
                                        <audio width="100%" controls>
                                            <source src="{{ Storage::url($file->file) }}" type="audio/mp3">
                                        </audio>
                                    @endif
                                </div>
                            </div>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </div>
@endsection

@push('scripts')
<script>
    document.addEventListener('contextmenu', event => event.preventDefault());
</script>
@endpush
