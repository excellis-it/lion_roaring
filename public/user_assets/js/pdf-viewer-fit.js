(function (window) {
    function fitPdfViewerToViewport() {
        var wrapper = document.getElementById('pdf-viewer-wrapper');
        if (!wrapper || wrapper.style.display === 'none') {
            return;
        }

        var top = wrapper.getBoundingClientRect().top;
        var gap = 16;
        wrapper.style.height = Math.max(280, window.innerHeight - top - gap) + 'px';
    }

    window.fitPdfViewerToViewport = fitPdfViewerToViewport;
    window.addEventListener('resize', fitPdfViewerToViewport);
})(window);
