@extends('user.layouts.master')
@section('title')
    File View - {{ env('APP_NAME') }}
@endsection
@push('styles')
    <link rel="stylesheet" href="https://cdn.jsdelivr.net/npm/@fullcalendar/core@4.2.0/main.min.css">
    <link rel="stylesheet" href="https://cdn.jsdelivr.net/npm/@fullcalendar/daygrid@4.3.0/main.min.css">
    <style>
        .fc-button-primary {
            background-color: #7851a9 !important;
            border-color: #7851a9 !important;
        }
    </style>
       <style>
        /* ensure viewer occupies space and not show black bg */
        #pdf-container, #pdf-container embed, #pdf-container object, #pdf-container iframe {
            width: 100%;
            min-height: 80vh;           /* large PDF -> near-full viewport height */
            height: 1000px;            /* fallback height */
            background: #fff;          /* white background prevents black render frames */
            display: block;
            border: 0;
        }
        /* responsive full height option if you want full viewport */
        .pdf-fullscreen {
            height: calc(100vh - 160px); /* adjust 160px to account for header/nav */
            min-height: 600px;
        }
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
                                <div class="col-md-10">
                                </div>
                                <div class="row ">
                                    <div class="col-md-10">
                                        <h3 class="mb-3 float-left">File</h3>
                                    </div>
                                    <div class="col-md-2">
                                        <a href="{{ route('file.index') }}" class="btn btn-primary w-100">
                                            < Back</a>
                                    </div>
                                </div>
                                <div>
                                    @if ($file->file_extension == 'pdf')
                                        <div id="pdf-container" class="pdf-fullscreen" aria-label="PDF viewer">
                                            <!-- PDFObject will embed here; fallback content shown while JS loads -->
                                            <p style="padding:20px; text-align:center;">Loading PDF... If nothing appears,
                                                <a href="{{ Storage::url($file->file) }}" target="_blank"
                                                    rel="noopener">open in a new tab</a>.</p>
                                        </div>

                                        {{-- PDFObject (CDN). This will try to embed; if it fails we fallback to <embed>. --}}
                                        <script src="https://cdnjs.cloudflare.com/ajax/libs/pdfobject/2.2.8/pdfobject.min.js"></script>
                                        <script>
                                            (function() {
                                                // PDF url from Laravel Storage
                                                const pdfUrl = {!! json_encode(Storage::url($file->file)) !!};
                                                const containerSelector = "#pdf-container";

                                                // Options: height can be px or %; pdfOpenParams sets viewer defaults
                                                const options = {
                                                    height: "100%",
                                                    pdfOpenParams: {
                                                        view: "FitH", // try fit to width (horizontal)
                                                        pagemode: "none",
                                                        toolbar: 0
                                                    }
                                                };

                                                // Try embedding using PDFObject
                                                const embedded = PDFObject.embed(pdfUrl, containerSelector, options);

                                                // PDFObject.embed returns null/false on failure; provide fallback
                                                if (!embedded) {
                                                    // fallback: use <embed> which also works in many browsers
                                                    const container = document.querySelector(containerSelector);
                                                    container.innerHTML = '';
                                                    const embed = document.createElement('embed');
                                                    embed.src = pdfUrl;
                                                    embed.type = 'application/pdf';
                                                    embed.width = '100%';
                                                    embed.height = '100%';
                                                    embed.setAttribute('aria-label', 'PDF Document');
                                                    container.appendChild(embed);

                                                    // extra fallback link
                                                    const fallback = document.createElement('div');
                                                    fallback.style.padding = '10px 20px';
                                                    fallback.style.textAlign = 'center';
                                                    fallback.innerHTML = 'If the PDF does not display, <a href="' + pdfUrl +
                                                        '" target="_blank" rel="noopener">open it in a new tab</a>.';
                                                    container.appendChild(fallback);
                                                }

                                                // Optional: prevent right click on the viewer (if desired)
                                                document.addEventListener('contextmenu', function(e) {
                                                    const inViewer = e.target.closest(containerSelector);
                                                    if (inViewer) e.preventDefault();
                                                });
                                            })();
                                        </script>
                                    @elseif (in_array($file->file_extension, ['jpg', 'jpeg', 'png', 'gif', 'svg', 'webp']))
                                        <img src="{{ Storage::url($file->file) }}" alt="file"
                                            style="width:100%; height:auto; max-height:80vh; background:#fff;">
                                    @elseif (in_array($file->file_extension, ['mp4', 'webm', 'ogg']))
                                        <video width="100%" height="600px" controls>
                                            <source src="{{ Storage::url($file->file) }}">
                                            Your browser does not support the video tag.
                                        </video>
                                    @elseif (in_array($file->file_extension, ['mp3', 'wav', 'ogg']))
                                        <audio controls style="width:100%;">
                                            <source src="{{ Storage::url($file->file) }}">
                                            Your browser does not support the audio element.
                                        </audio>
                                    @endif
                                </div>
                            </div>
                        </div>

                    </div>
                </div>
            </div>
        </div>
    @endsection

    @push('scripts')
        {{-- don't right click --}}
        <script>
            document.addEventListener('contextmenu', event => event.preventDefault());
        </script>
        <script>
            // Get the iframe element by its ID
            const iframe = document.getElementById('myIframe');

            // Add an event listener to prevent the context menu from appearing on right-click
            iframe.addEventListener('contextmenu', (event) => {
                event.preventDefault();
            });
        </script>
    @endpush
