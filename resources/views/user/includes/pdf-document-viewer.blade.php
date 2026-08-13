{{-- Shared PMA PDF.js viewer (Education, Strategy, Policy, Files). Expects $pdfUrl. --}}
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
        <button type="button" id="pdf-prev" disabled>&#8249; Prev</button>
        <span id="pdf-page-info">Page 1 of ...</span>
        <button type="button" id="pdf-next" disabled>Next &#8250;</button>
        <span class="tb-spacer"></span>
        <button type="button" id="pdf-zoom-out">&#8722;</button>
        <span id="pdf-zoom-label">100%</span>
        <button type="button" id="pdf-zoom-in">&#43;</button>
    </div>
    <div class="pdf-pages-container" id="pdf-pages-container"></div>
</div>

<script src="{{ asset('user_assets/js/pdf-viewer-fit.js') }}?v={{ @filemtime(public_path('user_assets/js/pdf-viewer-fit.js')) }}"></script>
<script src="https://cdnjs.cloudflare.com/ajax/libs/pdf.js/3.11.174/pdf.min.js"></script>
<script>
(function () {
    pdfjsLib.GlobalWorkerOptions.workerSrc =
        'https://cdnjs.cloudflare.com/ajax/libs/pdf.js/3.11.174/pdf.worker.min.js';
    const pdfUrl    = {!! json_encode($pdfUrl) !!};
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
            wrapper.style.display  = 'flex';
            if (typeof fitPdfViewerToViewport === 'function') {
                fitPdfViewerToViewport();
            }
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
