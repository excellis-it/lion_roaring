<!DOCTYPE html>
<html lang="en-US">

<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1">
    <meta name="description" content="Sign your PMA Agreement">
    <meta name="csrf-token" content="{{ csrf_token() }}">
    <title>{{ env('APP_NAME') }} - Sign Agreement</title>
    <link rel="preconnect" href="https://fonts.googleapis.com">
    <link rel="preconnect" href="https://fonts.gstatic.com" crossorigin>
    <link
        href="https://fonts.googleapis.com/css2?family=Playfair+Display:ital,wght@0,400..900;1,400..900&family=Roboto:ital,wght@0,100;0,300;0,400;0,500;0,700;0,900;1,100;1,300;1,400;1,500;1,700;1,900&display=swap"
        rel="stylesheet">
    <!-- Bootstrap core CSS -->
    <link href="{{ asset('user_assets/css/bootstrap.min.css') }}" rel="stylesheet">
    <link href="{{ asset('user_assets/css/style.css') }}" rel="stylesheet">
    <link href="{{ asset('user_assets/css/responsive.css') }}" rel="stylesheet">
    <link rel="stylesheet" type="text/css"
        href="https://cdnjs.cloudflare.com/ajax/libs/toastr.js/latest/toastr.min.css">
    <link rel="stylesheet" href="https://cdn.jsdelivr.net/npm/sweetalert2@11/dist/sweetalert2.min.css">
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.4.0/css/all.min.css">
    <style>
        body {
            background: #643271;
            min-height: 100vh;
        }

        .agreement-wrapper {
            max-width: 900px;
            margin: 30px auto;
            padding: 0 15px;
        }

        .agreement-card {
            background: #fff;
            border-radius: 20px;
            box-shadow: 0 15px 40px rgba(0, 0, 0, 0.2);
            overflow: hidden;
        }

        .agreement-header {
            background: linear-gradient(135deg, #643271 0%, #4a2454 100%);
            color: #fff;
            padding: 30px 40px;
            text-align: center;
        }

        .agreement-header h2 {
            font-family: 'Playfair Display', serif;
            font-weight: 700;
            font-size: 1.8rem;
            color: #d98b1c;
            margin-bottom: 10px;
        }

        .agreement-header p {
            color: rgba(255, 255, 255, 0.85);
            margin: 0;
            font-size: 0.95rem;
        }

        .agreement-body {
            padding: 40px;
        }

        .step-indicator {
            display: flex;
            justify-content: center;
            margin-bottom: 30px;
            gap: 8px;
        }

        .step-indicator .step {
            display: flex;
            align-items: center;
            gap: 8px;
            font-size: 0.85rem;
            color: #999;
            font-weight: 500;
        }

        .step-indicator .step.active {
            color: #643271;
            font-weight: 700;
        }

        .step-indicator .step.completed {
            color: #d98b1c;
        }

        .step-indicator .step .step-num {
            width: 30px;
            height: 30px;
            border-radius: 50%;
            display: flex;
            align-items: center;
            justify-content: center;
            font-weight: 700;
            font-size: 0.85rem;
            border: 2px solid #ddd;
            background: #fff;
        }

        .step-indicator .step.active .step-num {
            background: #643271;
            color: #fff;
            border-color: #643271;
        }

        .step-indicator .step.completed .step-num {
            background: #d98b1c;
            color: #fff;
            border-color: #d98b1c;
        }

        .step-indicator .step-line {
            width: 40px;
            height: 2px;
            background: #ddd;
            align-self: center;
        }

        .step-indicator .step-line.completed {
            background: #d98b1c;
        }

        /* Step panels */
        .step-panel {
            display: none;
        }

        .step-panel.active {
            display: block;
        }

        /* PDF Viewer (Step 1) */
        #pdf-viewer-container {
            max-height: 50vh;
            overflow-y: auto;
            display: flex;
            flex-direction: column;
            align-items: center;
            gap: 15px;
            padding: 10px;
            border: 1px solid #eee;
            border-radius: 12px;
            background: #fafafa;
            scrollbar-width: thin;
            scrollbar-color: #643271 #f1f1f1;
        }

        #pdf-viewer-container::-webkit-scrollbar {
            width: 6px;
        }

        #pdf-viewer-container::-webkit-scrollbar-track {
            background: #f1f1f1;
            border-radius: 3px;
        }

        #pdf-viewer-container::-webkit-scrollbar-thumb {
            background: #643271;
            border-radius: 3px;
        }

        .pdf-page-canvas {
            max-width: 100%;
            height: auto !important;
            box-shadow: 0 4px 15px rgba(0, 0, 0, 0.1);
            border-radius: 4px;
            border: 1px solid #dee2e6;
        }

        .scroll-hint {
            text-align: center;
            padding: 10px;
            background: linear-gradient(135deg, #fff3cd, #ffeeba);
            border-radius: 8px;
            color: #856404;
            font-size: 0.85rem;
            margin-bottom: 15px;
            display: flex;
            align-items: center;
            justify-content: center;
            gap: 8px;
        }

        .scroll-hint i {
            animation: bounce 2s infinite;
        }

        @keyframes bounce {

            0%,
            20%,
            50%,
            80%,
            100% {
                transform: translateY(0);
            }

            40% {
                transform: translateY(-5px);
            }

            60% {
                transform: translateY(-3px);
            }
        }

        /* Agreement Checkbox */
        .agreement-checkbox {
            background: #f8f9fa;
            padding: 20px;
            border-radius: 12px;
            border: 1px solid #e9ecef;
            margin-top: 15px;
        }

        .agreement-checkbox label {
            font-weight: 600;
            color: #333;
            cursor: pointer;
        }

        /* Signer Info (Step 2) */
        .signer-info-section label {
            font-weight: 600;
            color: #333;
            margin-bottom: 5px;
        }

        .signer-info-section .form-control {
            border-radius: 8px;
            padding: 12px 15px;
            border: 1px solid #ced4da;
        }

        .signer-info-section .form-control:focus {
            border-color: #643271;
            box-shadow: 0 0 0 3px rgba(100, 50, 113, 0.1);
        }

        /* Agreement description text (Step 2) */
        .member-text-div {
            max-height: 40vh;
            overflow-y: auto;
            padding: 20px;
            border: 1px solid #eee;
            border-radius: 12px;
            background: #fafafa;
            margin: 15px 0;
            font-size: 0.95rem;
            line-height: 1.8;
        }

        .member-text-div::-webkit-scrollbar {
            width: 6px;
        }

        .member-text-div::-webkit-scrollbar-track {
            background: #f1f1f1;
            border-radius: 3px;
        }

        .member-text-div::-webkit-scrollbar-thumb {
            background: #643271;
            border-radius: 3px;
        }

        /* PDF Preview (Step 3) */
        .pdf-preview-container {
            height: 50vh;
            border-radius: 12px;
            overflow: hidden;
            border: 1px solid #eee;
            margin-bottom: 20px;
            background: #f5f5f5;
        }

        .pdf-preview-container iframe {
            width: 100%;
            height: 100%;
            border: 0;
        }

        /* Signature Section (Step 4) */
        .signature-container {
            border: 2px dashed #643271;
            border-radius: 12px;
            background: linear-gradient(to bottom, #ffffff 0%, #f9f9f9 100%);
            position: relative;
            box-shadow: 0 2px 8px rgba(100, 50, 113, 0.1);
            padding: 10px;
        }

        #signature-pad {
            width: 100%;
            height: 200px;
            cursor: crosshair;
            touch-action: none;
            border-radius: 8px;
            background: white;
        }

        #signature-placeholder {
            position: absolute;
            bottom: 20px;
            left: 20px;
            color: #ddd;
            font-style: italic;
            pointer-events: none;
            font-size: 14px;
        }

        /* Buttons */
        .btn-next {
            background: linear-gradient(135deg, #643271 0%, #4a2454 100%);
            color: #fff;
            padding: 12px 30px;
            border-radius: 30px;
            font-weight: 600;
            border: none;
            box-shadow: 0 4px 15px rgba(100, 50, 113, 0.3);
            transition: all 0.3s;
            cursor: pointer;
        }

        .btn-next:hover {
            transform: translateY(-2px);
            box-shadow: 0 6px 20px rgba(100, 50, 113, 0.4);
            color: #fff;
        }

        .btn-next:disabled,
        .btn-next.disabled {
            background: #ccc;
            cursor: not-allowed;
            transform: none;
            box-shadow: none;
        }

        .btn-back {
            background: none;
            border: none;
            color: #777;
            font-weight: 600;
            padding: 10px 20px;
            cursor: pointer;
            transition: color 0.3s;
        }

        .btn-back:hover {
            color: #333;
        }

        .btn-submit {
            background: #643271;
            color: white;
            padding: 12px 35px;
            border-radius: 30px;
            font-weight: 600;
            border: none;
            box-shadow: 0 4px 15px rgba(40, 167, 69, 0.3);
            transition: all 0.3s;
            cursor: pointer;
            font-size: 1rem;
        }

        .btn-submit:hover {
            transform: translateY(-2px);
            box-shadow: 0 6px 20px rgba(40, 167, 69, 0.4);
        }

        .btn-submit:disabled {
            background: #ccc;
            cursor: not-allowed;
            transform: none;
            box-shadow: none;
        }

        .btn-clear-sig {
            background: linear-gradient(135deg, #dc3545 0%, #c82333 100%);
            color: white;
            border: none;
            padding: 8px 16px;
            border-radius: 6px;
            font-weight: 500;
            box-shadow: 0 2px 4px rgba(220, 53, 69, 0.3);
            transition: all 0.3s ease;
        }

        .btn-clear-sig:hover {
            transform: translateY(-2px);
            box-shadow: 0 4px 8px rgba(220, 53, 69, 0.4);
        }

        .btn-generate {
            background: linear-gradient(135deg, #d98b1c 0%, #b57012 100%);
            color: #fff;
            padding: 12px 30px;
            border-radius: 30px;
            font-weight: 600;
            border: none;
            box-shadow: 0 4px 15px rgba(217, 139, 28, 0.3);
            transition: all 0.3s;
            cursor: pointer;
        }

        .btn-generate:hover {
            transform: translateY(-2px);
            box-shadow: 0 6px 20px rgba(217, 139, 28, 0.4);
            color: #fff;
        }

        .btn-generate:disabled,
        .btn-generate.disabled {
            background: #ccc;
            cursor: not-allowed;
            transform: none;
            box-shadow: none;
        }

        .logout-link {
            color: rgba(255, 255, 255, 0.7);
            text-decoration: none;
            font-size: 0.85rem;
            transition: color 0.3s;
        }

        .logout-link:hover {
            color: #d98b1c;
        }

        .error-msg {
            color: #dc3545;
            background: #ffe6e6;
            padding: 10px;
            border-radius: 6px;
            border-left: 4px solid #dc3545;
            font-size: 13px;
            margin-top: 8px;
        }

        /* Loading spinner */
        .loading-overlay {
            display: none;
            position: fixed;
            top: 0;
            left: 0;
            width: 100%;
            height: 100%;
            background: rgba(0, 0, 0, 0.5);
            z-index: 9999;
            justify-content: center;
            align-items: center;
        }

        .loading-overlay.active {
            display: flex;
        }

        .loading-spinner {
            background: white;
            padding: 30px 40px;
            border-radius: 15px;
            text-align: center;
            box-shadow: 0 10px 30px rgba(0, 0, 0, 0.3);
        }
    </style>
</head>

<body>
    @php
        use App\Helpers\Helper;
    @endphp

    <!-- Loading Overlay -->
    <div class="loading-overlay" id="loadingOverlay">
        <div class="loading-spinner">
            <div class="spinner-border text-primary mb-3" role="status" style="width: 3rem; height: 3rem;">
                <span class="visually-hidden">Loading...</span>
            </div>
            <p class="mb-0 fw-bold">Generating Agreement PDF...</p>
        </div>
    </div>

    <main>
        <div class="agreement-wrapper">
            <div class="agreement-card">
                <!-- Header -->
                <div class="agreement-header">
                    <h2><i class="fas fa-file-signature me-2"></i> PMA Agreement Required</h2>
                    <p>Please read and sign the Private Members Association agreement to continue.</p>
                    <a href="{{ route('logout') }}" class="logout-link mt-2 d-inline-block">
                        <i class="fas fa-sign-out-alt me-1"></i> Logout
                    </a>
                </div>

                <!-- Body -->
                <div class="agreement-body">
                    <!-- Step Indicator -->
                    <div class="step-indicator">
                        <div class="step active" id="step-ind-1">
                            <span class="step-num">1</span>
                            <span>Read Article</span>
                        </div>
                        <div class="step-line" id="step-line-1"></div>
                        <div class="step" id="step-ind-2">
                            <span class="step-num">2</span>
                            <span>Review & Accept</span>
                        </div>
                        <div class="step-line" id="step-line-2"></div>
                        <div class="step" id="step-ind-3">
                            <span class="step-num">3</span>
                            <span>Preview</span>
                        </div>
                        <div class="step-line" id="step-line-3"></div>
                        <div class="step" id="step-ind-4">
                            <span class="step-num">4</span>
                            <span>Sign</span>
                        </div>
                    </div>

                    <!-- STEP 1: Read Articles of Association PDF -->
                    <div class="step-panel active" id="step-1">
                        <h4 class="mb-3 fw-bold" style="color: #643271;">
                            <i class="fas fa-book-open me-2"></i> Articles of Association
                        </h4>

                        <div class="scroll-hint" id="scrollHint">
                            <i class="fas fa-mouse-pointer"></i>
                            Please scroll to the bottom to read the entire article
                        </div>

                        <div id="pdf-viewer-container">
                            <div class="text-center p-5">
                                <div class="spinner-border text-primary" role="status"></div>
                                <p class="mt-3 text-muted fw-500">Preparing document...</p>
                            </div>
                        </div>

                        <div class="agreement-checkbox mt-3" id="agreementCheckboxSection" style="display: none;">
                            <div class="form-group mb-0">
                                <input type="checkbox" id="agreeCheck1">
                                <label for="agreeCheck1" class="ms-2">
                                    I have read and agree to the Articles of Association
                                </label>
                            </div>
                        </div>

                        <div class="d-flex justify-content-end mt-4">
                            <button class="btn-next" id="btnToStep2" disabled>
                                Next <i class="fas fa-arrow-right ms-2"></i>
                            </button>
                        </div>
                    </div>

                    <!-- STEP 2: Enter Name + Agreement Text -->
                    <div class="step-panel" id="step-2">
                        <h4 class="mb-3 fw-bold" style="color: #643271;">
                            <i class="fas fa-user-check me-2"></i>
                            {{ Helper::getAgreements()['agreement_title'] ?? 'Lion Roaring PMA (Private Members Association) Agreement' }}
                        </h4>

                        <div class="signer-info-section">
                            <div class="row g-3 mb-3">
                                <div class="col-12">
                                    <label for="signerName" class="form-label">Full Name *</label>
                                    <input type="text" class="form-control" id="signerName"
                                        value="{{ $user->first_name }} {{ $user->last_name }}"
                                        placeholder="Enter your full name" autocomplete="name">
                                </div>
                                <div class="col-12">
                                    <div class="form-group mb-0">
                                        <input type="checkbox" id="initialCheck">
                                        <label for="initialCheck" id="initialLabel" class="ms-2">
                                            I confirm my initials
                                        </label>
                                    </div>
                                </div>
                            </div>
                        </div>

                        <div class="member-text-div">
                            {!! Helper::getAgreements()['agreement_description'] ??
                                'This is the agreement for Lion Roaring PMA (Private Members Association)' !!}
                        </div>

                        <div class="d-flex justify-content-between mt-4">
                            <button class="btn-back" id="btnBackToStep1">
                                <i class="fas fa-arrow-left me-1"></i> Back
                            </button>
                            <button class="btn-generate" id="btnToStep3">
                                <i class="fas fa-file-pdf me-1"></i> Generate Preview & Next
                            </button>
                        </div>
                    </div>

                    <!-- STEP 3: PDF Preview + Agreement Checkbox -->
                    <div class="step-panel" id="step-3">
                        <h4 class="mb-3 fw-bold" style="color: #643271;">
                            <i class="fas fa-file-pdf me-2 text-danger"></i> Review & Agree
                        </h4>

                        <div class="pdf-preview-container">
                            <iframe id="agreementPdfIframe" src=""></iframe>
                        </div>

                        <div class="agreement-checkbox">
                            <div class="form-group mb-0">
                                <input type="checkbox" id="agreeCheck3">
                                <label for="agreeCheck3" class="ms-2">
                                    {{ Helper::getAgreements()['checkbox_text'] ?? 'I have read and agreed to the Lion Roaring PMA Agreement' }}
                                </label>
                            </div>
                        </div>

                        <div class="d-flex justify-content-between mt-4">
                            <button class="btn-back" id="btnBackToStep2">
                                <i class="fas fa-arrow-left me-1"></i> Back
                            </button>
                            <button class="btn-next" id="btnToStep4">
                                Next <i class="fas fa-arrow-right ms-2"></i>
                            </button>
                        </div>
                    </div>

                    <!-- STEP 4: Signature -->
                    <div class="step-panel" id="step-4">
                        <h4 class="mb-3 fw-bold" style="color: #643271;">
                            <i class="fas fa-signature me-2"></i> E-Signature
                        </h4>

                        <form action="{{ route('user.sign.agreement.submit') }}" method="POST" id="signatureForm">
                            @csrf
                            <p class="text-muted small mb-3">
                                <i class="fa fa-info-circle"></i> Please sign below using your mouse or finger
                            </p>

                            <div class="signature-container">
                                <canvas id="signature-pad"></canvas>
                                <div id="signature-placeholder">Sign here</div>
                            </div>

                            <div class="d-flex justify-content-between align-items-center mt-3">
                                <button type="button" class="btn-clear-sig" id="clearSignature">
                                    <i class="fa fa-eraser"></i> Clear & Redraw
                                </button>
                                <small class="text-muted" style="font-size: 12px;">
                                    <i class="fa fa-hand-pointer"></i> Use mouse, trackpad, or touch to sign
                                </small>
                            </div>

                            <input type="hidden" name="signature" id="signature-data">

                            @if ($errors->has('signature'))
                                <div class="error-msg mt-2">
                                    <i class="fa fa-exclamation-circle"></i>
                                    {{ $errors->first('signature') }}
                                </div>
                            @endif

                            @if ($errors->has('agreement'))
                                <div class="error-msg mt-2">
                                    <i class="fa fa-exclamation-circle"></i>
                                    {{ $errors->first('agreement') }}
                                </div>
                            @endif

                            <div class="d-flex justify-content-between mt-4">
                                <button type="button" class="btn-back" id="btnBackToStep3">
                                    <i class="fas fa-arrow-left me-1"></i> Back
                                </button>
                                <button type="submit" class="btn-submit" id="btnSubmit">
                                    <i class="fas fa-check-circle me-1"></i> Submit Agreement
                                </button>
                            </div>
                        </form>
                    </div>
                </div>
            </div>
        </div>
    </main>

    <script src="https://cdnjs.cloudflare.com/ajax/libs/jquery/3.4.1/jquery.min.js"></script>
    <script src="{{ asset('user_assets/js/bootstrap.bundle.min.js') }}"></script>
    <script src="https://cdnjs.cloudflare.com/ajax/libs/toastr.js/latest/js/toastr.min.js"></script>
    <script src="https://cdn.jsdelivr.net/npm/sweetalert2@11/dist/sweetalert2.min.js"></script>
    <script src="https://cdn.jsdelivr.net/npm/signature_pad@4.0.0/dist/signature_pad.umd.min.js"></script>
    {{-- PDF.js Library --}}
    <script src="https://cdnjs.cloudflare.com/ajax/libs/pdf.js/3.11.174/pdf.min.js"></script>

    <script>
        @if (Session::has('message'))
            toastr.options = {
                "closeButton": true,
                "progressBar": true
            }
            toastr.success("{{ session('message') }}");
        @endif

        @if (Session::has('error'))
            toastr.options = {
                "closeButton": true,
                "progressBar": true
            }
            toastr.error("{{ session('error') }}");
        @endif
    </script>

    <script>
        // PDF.js setup
        pdfjsLib.GlobalWorkerOptions.workerSrc = 'https://cdnjs.cloudflare.com/ajax/libs/pdf.js/3.11.174/pdf.worker.min.js';

        async function renderPDF(url, containerId) {
            const container = document.getElementById(containerId);
            try {
                const loadingTask = pdfjsLib.getDocument(url);
                const pdf = await loadingTask.promise;
                container.innerHTML = '';

                for (let i = 1; i <= pdf.numPages; i++) {
                    const page = await pdf.getPage(i);
                    const viewport = page.getViewport({
                        scale: 1.5
                    });

                    const canvas = document.createElement('canvas');
                    canvas.className = 'pdf-page-canvas';
                    const context = canvas.getContext('2d');
                    canvas.height = viewport.height;
                    canvas.width = viewport.width;

                    const renderContext = {
                        canvasContext: context,
                        viewport: viewport
                    };
                    await page.render(renderContext).promise;
                    container.appendChild(canvas);

                    // For the last page, add a trigger point
                    if (i === pdf.numPages) {
                        const sentinel = document.createElement('div');
                        sentinel.id = 'pdf-bottom-sentinel';
                        sentinel.style.height = '1px';
                        container.appendChild(sentinel);

                        const observer = new IntersectionObserver((entries) => {
                            if (entries[0].isIntersecting) {
                                $('#agreementCheckboxSection').fadeIn(800);
                                $('#scrollHint').fadeOut();
                                observer.disconnect();
                            }
                        }, {
                            threshold: 0.1
                        });
                        observer.observe(sentinel);
                    }
                }
            } catch (error) {
                console.error('PDF Rendering Error:', error);
                container.innerHTML = '<div class="alert alert-danger">Failed to load and render the document.</div>';
            }
        }

        $(document).ready(function() {
            // ===== Load PDF on page load for Step 1 =====
            var pdfUrl = "{{ $agreement ?? '' }}";
            if (pdfUrl) {
                renderPDF(pdfUrl, 'pdf-viewer-container');
            }

            // ===== STEP NAVIGATION =====
            function goToStep(stepNum) {
                $('.step-panel').removeClass('active');
                $('#step-' + stepNum).addClass('active');

                // Update indicators
                for (var i = 1; i <= 4; i++) {
                    var ind = $('#step-ind-' + i);
                    ind.removeClass('active completed');
                    if (i < stepNum) {
                        ind.addClass('completed');
                    } else if (i === stepNum) {
                        ind.addClass('active');
                    }
                }

                for (var j = 1; j <= 3; j++) {
                    var line = $('#step-line-' + j);
                    line.removeClass('completed');
                    if (j < stepNum) {
                        line.addClass('completed');
                    }
                }

                // Scroll to top
                window.scrollTo(0, 0);

                // Initialize signature pad when entering step 4
                if (stepNum === 4) {
                    initSignaturePad();
                }
            }

            // ===== STEP 1: Read Article =====
            $('#agreeCheck1').on('change', function() {
                $('#btnToStep2').prop('disabled', !$(this).is(':checked'));
            });

            $('#btnToStep2').on('click', function() {
                if ($('#agreeCheck1').is(':checked')) {
                    goToStep(2);
                } else {
                    toastr.error('Please check the agreement');
                }
            });

            // ===== STEP 2: Name + Agreement Text =====
            function computeInitials(name) {
                if (!name) return '';
                var parts = name.trim().split(/\s+/).filter(Boolean);
                return parts.map(function(p) {
                    return (p[0] || '').toUpperCase();
                }).join('').slice(0, 4);
            }

            function updateInitialLabel() {
                var name = $('#signerName').val() || '';
                var initials = computeInitials(name);
                if (initials) {
                    $('#initialLabel').text('I confirm my initials: ' + initials);
                } else {
                    $('#initialLabel').text('I confirm my initials');
                }
            }

            $('#signerName').on('input', function() {
                updateInitialLabel();
                $('#initialCheck').prop('checked', false);
            });
            // Trigger on load
            updateInitialLabel();

            $('#btnToStep3').on('click', function() {
                var signerName = ($('#signerName').val() || '').trim();
                if (!signerName) {
                    toastr.error('Please enter your full name');
                    return;
                }

                updateInitialLabel();

                if (!$('#initialCheck').is(':checked')) {
                    toastr.error('Please confirm your initials');
                    return;
                }

                var btn = $(this);
                btn.addClass('disabled');
                $('#loadingOverlay').addClass('active');

                $.ajax({
                    url: "{{ route('user.sign.agreement.preview') }}",
                    method: 'POST',
                    headers: {
                        'X-CSRF-TOKEN': "{{ csrf_token() }}"
                    },
                    data: {
                        signer_name: signerName
                    },
                    success: function(res) {
                        $('#loadingOverlay').removeClass('active');
                        if (!res || res.status !== true || !res.pdf_url) {
                            toastr.error(
                                'Could not generate agreement preview. Please try again.');
                            btn.removeClass('disabled');
                            return;
                        }

                        $('#agreementPdfIframe').attr('src', res.pdf_url);
                        $('#agreeCheck3').prop('checked', false);
                        goToStep(3);
                    },
                    error: function(xhr) {
                        $('#loadingOverlay').removeClass('active');
                        var msg = (xhr && xhr.responseJSON && (xhr.responseJSON.message || xhr
                                .responseJSON.error)) ||
                            'Could not generate agreement preview. Please try again.';
                        toastr.error(msg);
                    },
                    complete: function() {
                        btn.removeClass('disabled');
                    }
                });
            });

            // ===== STEP 3: PDF Preview -> Step 4 =====
            $('#btnToStep4').on('click', function() {
                if (!$('#agreeCheck3').is(':checked')) {
                    toastr.error('Please check the agreement');
                    return;
                }
                goToStep(4);
            });

            // ===== BACK BUTTONS =====
            $('#btnBackToStep1').on('click', function() {
                goToStep(1);
            });
            $('#btnBackToStep2').on('click', function() {
                goToStep(2);
            });
            $('#btnBackToStep3').on('click', function() {
                goToStep(3);
            });

            // ===== STEP 4: Signature Pad =====
            var signaturePad = null;

            function initSignaturePad() {
                if (signaturePad) return;

                var canvas = document.getElementById('signature-pad');
                signaturePad = new SignaturePad(canvas, {
                    backgroundColor: 'rgb(255, 255, 255)',
                    penColor: 'rgb(0, 0, 0)'
                });

                var placeholder = $('#signature-placeholder');

                function resizeCanvas(restoreData) {
                    var oldData = restoreData ? signaturePad.toData() : null;
                    var ratio = Math.max(window.devicePixelRatio || 1, 1);
                    canvas.width = canvas.offsetWidth * ratio;
                    canvas.height = canvas.offsetHeight * ratio;
                    canvas.getContext('2d').scale(ratio, ratio);
                    signaturePad.clear();
                    if (oldData) {
                        signaturePad.fromData(oldData);
                    }
                }

                resizeCanvas(false);
                $(window).on('resize', function() {
                    resizeCanvas(true);
                });

                signaturePad.addEventListener('beginStroke', function() {
                    placeholder.hide();
                });

                signaturePad.addEventListener('endStroke', function() {
                    if (signaturePad.isEmpty()) {
                        placeholder.show();
                    }
                });

                $('#clearSignature').on('click', function() {
                    signaturePad.clear();
                    $('#signature-data').val('');
                    placeholder.show();
                });
            }

            // Form submission
            $('#signatureForm').on('submit', function(e) {
                if (signaturePad && !signaturePad.isEmpty()) {
                    var dataURL = signaturePad.toDataURL('image/png');
                    $('#signature-data').val(dataURL);
                } else {
                    e.preventDefault();
                    Swal.fire({
                        icon: 'warning',
                        title: 'Signature Required',
                        text: 'Please provide your signature before submitting.',
                        confirmButtonColor: '#643271'
                    });
                    return false;
                }

                $('#btnSubmit').prop('disabled', true).html(
                    '<i class="fas fa-spinner fa-spin me-1"></i> Submitting...');
            });
        });
    </script>
</body>

</html>
