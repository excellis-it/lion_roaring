/**
 * File Upload Modal Handler
 * Manages drag-and-drop file upload with previews and individual messages
 */

(function () {
    "use strict";

    // Store files with their associated messages
    // Use separate arrays for chat and team to avoid cross-modal interference
    let chatSelectedFiles = [];
    let teamSelectedFiles = [];
    // recursion guard counters
    let chatFileInputChangeDepth = 0;
    let teamFileInputChangeDepth = 0;

    var CHAT_IMAGE_EXTENSIONS = [
        "jpg",
        "jpeg",
        "jfif",
        "png",
        "gif",
        "webp",
        "heic",
        "heif",
        "bmp",
        "svg",
    ];

    function chatFileExtension(file) {
        var name = (file && file.name) || "";
        var parts = name.split(".");
        return parts.length > 1 ? parts.pop().toLowerCase() : "";
    }

    function isChatImageFile(file) {
        if (!file) return false;
        var ext = chatFileExtension(file);
        if (CHAT_IMAGE_EXTENSIONS.indexOf(ext) !== -1) {
            return true;
        }
        var type = (file.type || "").toLowerCase();
        return type.indexOf("image/") === 0;
    }

    var CHAT_VIDEO_EXTENSIONS = [
        "mp4",
        "mkv",
        "avi",
        "mov",
        "wmv",
        "webm",
        "flv",
        "mpeg",
        "mpg",
        "m4v",
        "3gp",
        "ogv",
    ];

    function isChatVideoFile(file) {
        if (!file) return false;
        var ext = chatFileExtension(file);
        if (CHAT_VIDEO_EXTENSIONS.indexOf(ext) !== -1) {
            return true;
        }
        var type = (file.type || "").toLowerCase();
        return type.indexOf("video/") === 0;
    }

    /**
     * Convert HEIC/HEIF to JPEG in-browser (browsers often can't preview HEIC).
     * JFIF is renamed to .jpg for wider compatibility.
     */
    function normalizeChatImageFile(file) {
        return new Promise(function (resolve) {
            var ext = chatFileExtension(file);
            var type = (file.type || "").toLowerCase();

            if (ext === "jfif") {
                resolve(
                    new File([file], file.name.replace(/\.jfif$/i, ".jpg"), {
                        type: "image/jpeg",
                        lastModified: file.lastModified,
                    })
                );
                return;
            }

            var isHeic =
                ["heic", "heif"].indexOf(ext) !== -1 ||
                type === "image/heic" ||
                type === "image/heif";

            if (!isHeic) {
                resolve(file);
                return;
            }

            if (typeof heic2any !== "function") {
                resolve(file);
                return;
            }

            heic2any({ blob: file, toType: "image/jpeg", quality: 0.85 })
                .then(function (result) {
                    var blob = Array.isArray(result) ? result[0] : result;
                    var newName = file.name.replace(/\.(heic|heif)$/i, ".jpg");
                    resolve(
                        new File([blob], newName, {
                            type: "image/jpeg",
                            lastModified: Date.now(),
                        })
                    );
                })
                .catch(function () {
                    if (typeof toastr !== "undefined") {
                        toastr.warning(
                            "Could not convert HEIC for in-app preview. It will upload as a downloadable file."
                        );
                    }
                    resolve(file);
                });
        });
    }

    function normalizeChatImageFiles(files, onProgress) {
        var list = Array.from(files);
        var total = list.length;
        var done = 0;
        if (typeof onProgress === "function") onProgress(0, total);
        return Promise.all(
            list.map(function (file) {
                return normalizeChatImageFile(file).then(function (result) {
                    done++;
                    if (typeof onProgress === "function") {
                        onProgress(done, total);
                    }
                    return result;
                });
            })
        );
    }

    // ---- Busy overlay (processing / uploading feedback) ----
    var BUSY_STYLE_ID = "chat-upload-busy-style";

    function ensureBusyStyles() {
        if (document.getElementById(BUSY_STYLE_ID)) return;
        var style = document.createElement("style");
        style.id = BUSY_STYLE_ID;
        style.textContent = [
            ".chat-upload-busy{position:absolute;top:0;left:0;right:0;bottom:0;display:none;align-items:center;justify-content:center;background:rgba(255,255,255,.94);z-index:10;border-radius:.5rem}",
            ".chat-upload-busy.show{display:flex}",
            ".chat-upload-busy-box{text-align:center;padding:20px;max-width:340px;width:100%}",
            ".chat-upload-spinner{width:48px;height:48px;margin:0 auto 14px;border:4px solid #e9e2f0;border-top-color:#643271;border-radius:50%;animation:chatUploadSpin 1s linear infinite}",
            "@keyframes chatUploadSpin{to{transform:rotate(360deg)}}",
            ".chat-upload-busy-text{font-weight:600;color:#3b2b46;margin-bottom:10px}",
            ".chat-upload-progress{height:8px;background:#ece6f2;border-radius:6px;overflow:hidden;display:none}",
            ".chat-upload-progress-bar{height:100%;width:0;background:#643271;transition:width .2s ease}",
        ].join("");
        document.head.appendChild(style);
    }

    function getBusyOverlay(modalId) {
        var modalEl = document.getElementById(modalId);
        if (!modalEl) return null;
        var content = modalEl.querySelector(".modal-content");
        if (!content) return null;
        var overlay = content.querySelector(".chat-upload-busy");
        if (!overlay) {
            ensureBusyStyles();
            overlay = document.createElement("div");
            overlay.className = "chat-upload-busy";
            overlay.innerHTML =
                '<div class="chat-upload-busy-box">' +
                '<div class="chat-upload-spinner"></div>' +
                '<div class="chat-upload-busy-text"></div>' +
                '<div class="chat-upload-progress"><div class="chat-upload-progress-bar"></div></div>' +
                "</div>";
            content.appendChild(overlay);
        }
        return overlay;
    }

    function toggleModalControls(modalId, disabled) {
        $("#" + modalId)
            .find("button")
            .not(".chat-upload-busy button")
            .prop("disabled", disabled);
    }

    function showModalBusy(modalId, text, percent) {
        var overlay = getBusyOverlay(modalId);
        if (!overlay) return;
        overlay.querySelector(".chat-upload-busy-text").textContent = text;
        var progress = overlay.querySelector(".chat-upload-progress");
        var bar = overlay.querySelector(".chat-upload-progress-bar");
        if (typeof percent === "number" && isFinite(percent)) {
            progress.style.display = "block";
            bar.style.width = Math.max(0, Math.min(100, percent)) + "%";
        } else {
            progress.style.display = "none";
        }
        overlay.classList.add("show");
        toggleModalControls(modalId, true);
    }

    function hideModalBusy(modalId) {
        var overlay = getBusyOverlay(modalId);
        if (overlay) overlay.classList.remove("show");
        toggleModalControls(modalId, false);
    }

    function isModalBusy(modalId) {
        var modalEl = document.getElementById(modalId);
        return !!(modalEl && modalEl.querySelector(".chat-upload-busy.show"));
    }

    // Block ESC / backdrop dismissal while processing or uploading
    function guardModalWhileBusy(modalId) {
        $(document)
            .off("hide.bs.modal", "#" + modalId)
            .on("hide.bs.modal", "#" + modalId, function (e) {
                if (isModalBusy(modalId)) {
                    e.preventDefault();
                }
            });
    }

    function processFilesWithFeedback(modalId, files, onDone) {
        if (!files.length) return;
        showModalBusy(
            modalId,
            "Processing files... (0 of " + files.length + ")",
            0
        );
        // Yield so the overlay actually paints before HEIC decoding hogs the
        // main thread (setTimeout rather than rAF: rAF is throttled in
        // background tabs and would stall processing there).
        setTimeout(function () {
            normalizeChatImageFiles(files, function (done, total) {
                showModalBusy(
                    modalId,
                    "Processing files... (" + done + " of " + total + ")",
                    (done / total) * 100
                );
            })
                .then(function (normalized) {
                    onDone(normalized);
                    hideModalBusy(modalId);
                })
                .catch(function (err) {
                    console.error("File processing failed", err);
                    hideModalBusy(modalId);
                });
        }, 50);
    }

    // Initialize for regular chat
    function initChatFileModal() {
        console.log("Initializing chat file modal...");

        guardModalWhileBusy("fileUploadModal");

        // Open modal when attachment icon is clicked (delegated event)
        $(document)
            .off("click", "#hit-chat-file")
            .on("click", "#hit-chat-file", function (e) {
                e.preventDefault();
                e.stopPropagation();
                console.log("Attachment icon clicked");

                const modalEl = document.getElementById("fileUploadModal");
                if (!modalEl) {
                    console.error("Modal #fileUploadModal not found in DOM");
                    return;
                }

                console.log("Modal found, opening...");
                chatSelectedFiles = [];
                resetChatModal();

                // Use Bootstrap 5 native API
                const modal = new bootstrap.Modal(modalEl);
                modal.show();

                // remove any stuck backdrops
                clearModalBackdrop();
            });

        // Select files button (delegated)
        $(document)
            .off("click", "#selectFilesBtn, #addMoreFiles")
            .on("click", "#selectFilesBtn, #addMoreFiles", function (e) {
                e.preventDefault();
                e.stopPropagation();
                console.log("select/addMore clicked:", e.target.id);
                const fileInput = $("#fileInput");
                if (fileInput.length) {
                    // For addMore, preserve existing value; but open native dialog
                    fileInput[0].click();
                }
            });

        // File input change (delegated)
        $(document)
            .off("change", "#fileInput")
            .on("change", "#fileInput", function (e) {
                chatFileInputChangeDepth++;
                console.log("fileInput change depth", chatFileInputChangeDepth);
                if (chatFileInputChangeDepth > 25) {
                    console.warn("fileInput change recursion guard activated");
                    chatFileInputChangeDepth = 0;
                    return;
                }
                try {
                    const files = Array.from(e.target.files);
                    processFilesWithFeedback(
                        "fileUploadModal",
                        files,
                        handleChatFiles
                    );
                } catch (ex) {
                    console.error("Error in fileInput change handler", ex);
                } finally {
                    // Reset input using native API to avoid triggering change event
                    this.value = "";
                    chatFileInputChangeDepth = 0;
                }
            });

        // Drag and drop events (delegated)
        $(document)
            .off("dragover", "#dropZone")
            .on("dragover", "#dropZone", function (e) {
                e.preventDefault();
                e.stopPropagation();
                $(this).addClass("dragover");
            });

        $(document)
            .off("dragleave", "#dropZone")
            .on("dragleave", "#dropZone", function (e) {
                e.preventDefault();
                e.stopPropagation();
                $(this).removeClass("dragover");
            });

        $(document)
            .off("drop", "#dropZone")
            .on("drop", "#dropZone", function (e) {
                e.preventDefault();
                e.stopPropagation();
                $(this).removeClass("dragover");

                const files = Array.from(e.originalEvent.dataTransfer.files);
                processFilesWithFeedback(
                    "fileUploadModal",
                    files,
                    handleChatFiles
                );
            });

        // Click on drop zone to select files (delegated)
        $(document)
            .off("click", "#dropZone")
            .on("click", "#dropZone", function (e) {
                // Only trigger if the drop zone itself was clicked (not its child elements)
                if (e.target !== this) return;
                const fileInput = $("#fileInput");
                if (fileInput.length) {
                    e.stopPropagation();
                    fileInput.click();
                }
            });

        // Send files button (delegated)
        $(document)
            .off("click", "#sendFilesBtn")
            .on("click", "#sendFilesBtn", function (e) {
                e.preventDefault();
                sendChatFiles();
            });

        // Reset modal on close (delegated)
        $(document)
            .off("hidden.bs.modal", "#fileUploadModal")
            .on("hidden.bs.modal", "#fileUploadModal", function () {
                resetChatModal();
            });

        // Message input change (delegated) - delegate from filesList container
        $("#filesList")
            .off("input", ".file-message-input")
            .on("input", ".file-message-input", function () {
                const index = $(this).data("index");
                if (chatSelectedFiles[index]) {
                    // avoid unnecessary recursion: only assign if changed
                    const newVal = $(this).val();
                    console.log("chat file message input", index, newVal);
                    if (chatSelectedFiles[index].message !== newVal) {
                        chatSelectedFiles[index].message = newVal;
                    }
                }
            });

        // Remove file button (delegated) - delegate from filesList container
        $("#filesList")
            .off("click", ".remove-file-btn")
            .on("click", ".remove-file-btn", function (e) {
                e.preventDefault();
                e.stopPropagation();
                const index = $(this).data("index");
                chatSelectedFiles.splice(index, 1);
                updateChatPreview();
            });

        function handleChatFiles(files) {
            console.log("handleChatFiles called with", files.length, "files");
            files.forEach((file) => {
                if (
                    !chatSelectedFiles.find(
                        (f) => f.name === file.name && f.size === file.size
                    )
                ) {
                    chatSelectedFiles.push({
                        file: file,
                        message: "",
                        preview: null,
                    });
                }
            });
            updateChatPreview();
        }

        function updateChatPreview() {
            const filesPreviewContainer = $("#filesPreviewContainer");
            const dropZone = $("#dropZone");
            const filesList = $("#filesList");
            const fileCount = $("#fileCount");

            if (chatSelectedFiles.length === 0) {
                if (filesPreviewContainer.length) filesPreviewContainer.hide();
                if (dropZone.length) dropZone.show();
                return;
            }

            if (dropZone.length) dropZone.hide();
            if (filesPreviewContainer.length) filesPreviewContainer.show();
            if (fileCount.length) fileCount.text(chatSelectedFiles.length);
            if (filesList.length) filesList.empty();

            chatSelectedFiles.forEach((fileObj, index) => {
                const file = fileObj.file;
                const fileSize = formatFileSize(file.size);
                const fileExt = chatFileExtension(file);
                const isImage = isChatImageFile(file);
                const isVideo = isChatVideoFile(file);

                let previewHTML = `
                    <div class="file-preview-item" data-index="${index}">
                        <button type="button" class="remove-file-btn" data-index="${index}">
                            <i class="fas fa-times"></i>
                        </button>
                        <div class="file-preview-content">
                `;

                if (isImage) {
                    const reader = new FileReader();
                    reader.onload = function (e) {
                        $(
                            `#filesList [data-index="${index}"] .file-preview-placeholder`
                        ).replaceWith(
                            `<img src="${
                                e.target.result
                            }" class="file-preview-thumbnail" alt="${escapeHtml(
                                file.name
                            )}">`
                        );
                    };
                    reader.onerror = function () {
                        $(
                            `#filesList [data-index="${index}"] .file-preview-placeholder`
                        ).replaceWith(
                            `<div class="file-preview-icon"><i class="fas fa-image"></i></div>`
                        );
                    };
                    reader.readAsDataURL(file);
                    previewHTML += `<div class="file-preview-placeholder" style="width: 80px; height: 80px; background: #f5f5f5; border-radius: 6px;"></div>`;
                } else if (isVideo) {
                    previewHTML += `<div class="file-preview-icon"><i class="fas fa-video"></i></div>`;
                } else {
                    const icon = getFileIcon(fileExt);
                    previewHTML += `<div class="file-preview-icon"><i class="fas fa-${icon}"></i></div>`;
                }

                previewHTML += `
                            <div class="file-preview-info">
                                <div class="file-preview-name">${escapeHtml(
                                    file.name
                                )}</div>
                                <div class="file-preview-size">${fileSize}</div>
                            </div>
                        </div>
                        <input type="text" class="file-message-input" placeholder="Add a caption for this file..."
                               data-index="${index}" value="${escapeHtml(
                    fileObj.message
                )}">
                    </div>
                `;

                if (filesList.length) {
                    filesList.append(previewHTML);
                }
            });
        }

        function sendChatFiles() {
            if (chatSelectedFiles.length === 0) {
                if (typeof toastr !== "undefined") {
                    toastr.warning("Please select at least one file");
                }
                return;
            }

            if (typeof window.sendChatFilesWithMessages !== "function") {
                console.error("sendChatFilesWithMessages function not found");
                return;
            }

            // Store files globally so the main send function can access them
            window.chatFilesToSend = chatSelectedFiles.slice(); // Create a copy

            const total = window.chatFilesToSend.length;
            showModalBusy(
                "fileUploadModal",
                "Uploading 0 of " + total + " file(s)...",
                0
            );

            Promise.resolve(
                window.sendChatFilesWithMessages(
                    window.chatFilesToSend,
                    function (done, count) {
                        showModalBusy(
                            "fileUploadModal",
                            "Uploading " + done + " of " + count + " file(s)...",
                            (done / count) * 100
                        );
                    }
                )
            )
                .then(function () {
                    hideModalBusy("fileUploadModal");
                    const modalEl = document.getElementById("fileUploadModal");
                    if (modalEl) {
                        const modalInstance =
                            bootstrap.Modal.getInstance(modalEl);
                        if (modalInstance) {
                            modalInstance.hide();
                        }
                    }
                })
                .catch(function (err) {
                    console.error("Chat file upload failed", err);
                    hideModalBusy("fileUploadModal");
                });
        }

        function resetChatModal() {
            chatSelectedFiles = [];
            const filesPreviewContainer = $("#filesPreviewContainer");
            const dropZone = $("#dropZone");
            const filesList = $("#filesList");
            const fileInput = $("#fileInput");

            if (filesPreviewContainer.length) filesPreviewContainer.hide();
            if (dropZone.length) dropZone.show();
            if (filesList.length) filesList.empty();
            if (fileInput.length) fileInput.val("");
        }
    }

    // Initialize for team chat
    function initTeamFileModal() {
        console.log("Initializing team file modal...");

        guardModalWhileBusy("teamFileUploadModal");

        // Open modal when attachment icon is clicked (delegated)
        $(document)
            .off("click", "#hit-team-chat-file")
            .on("click", "#hit-team-chat-file", function (e) {
                e.preventDefault();
                e.stopPropagation();
                console.log("Team attachment icon clicked");

                const modalEl = document.getElementById("teamFileUploadModal");
                if (!modalEl) {
                    console.error(
                        "Modal #teamFileUploadModal not found in DOM"
                    );
                    return;
                }

                console.log("Team modal found, opening...");
                teamSelectedFiles = [];
                resetTeamModal();

                // Use Bootstrap 5 native API
                const modal = new bootstrap.Modal(modalEl);
                modal.show();

                // remove any stuck backdrops
                clearModalBackdrop();
            });

        // Select files button (delegated)
        $(document)
            .off("click", "#teamSelectFilesBtn, #teamAddMoreFiles")
            .on(
                "click",
                "#teamSelectFilesBtn, #teamAddMoreFiles",
                function (e) {
                    e.preventDefault();
                    e.stopPropagation();
                    console.log("team select/addMore clicked:", e.target.id);
                    const fileInput = $("#teamFileInput");
                    if (fileInput.length) {
                        fileInput[0].click();
                    }
                }
            );

        // File input change (delegated)
        $(document)
            .off("change", "#teamFileInput")
            .on("change", "#teamFileInput", function (e) {
                teamFileInputChangeDepth++;
                console.log(
                    "teamFileInput change depth",
                    teamFileInputChangeDepth
                );
                if (teamFileInputChangeDepth > 25) {
                    console.warn(
                        "teamFileInput change recursion guard activated"
                    );
                    teamFileInputChangeDepth = 0;
                    return;
                }
                try {
                    const files = Array.from(e.target.files);
                    processFilesWithFeedback(
                        "teamFileUploadModal",
                        files,
                        handleTeamFiles
                    );
                } catch (ex) {
                    console.error("Error in teamFileInput change handler", ex);
                } finally {
                    // Reset input using native API to avoid triggering change event
                    this.value = "";
                    teamFileInputChangeDepth = 0;
                }
            });

        // Drag and drop events (delegated)
        $(document)
            .off("dragover", "#teamDropZone")
            .on("dragover", "#teamDropZone", function (e) {
                e.preventDefault();
                e.stopPropagation();
                $(this).addClass("dragover");
            });

        $(document)
            .off("dragleave", "#teamDropZone")
            .on("dragleave", "#teamDropZone", function (e) {
                e.preventDefault();
                e.stopPropagation();
                $(this).removeClass("dragover");
            });

        $(document)
            .off("drop", "#teamDropZone")
            .on("drop", "#teamDropZone", function (e) {
                e.preventDefault();
                e.stopPropagation();
                $(this).removeClass("dragover");

                const files = Array.from(e.originalEvent.dataTransfer.files);
                processFilesWithFeedback(
                    "teamFileUploadModal",
                    files,
                    handleTeamFiles
                );
            });

        // Click on drop zone to select files (delegated)
        $(document)
            .off("click", "#teamDropZone")
            .on("click", "#teamDropZone", function (e) {
                // Only trigger if drop zone itself was clicked (not child elements)
                if (e.target !== this) return;
                const fileInput = $("#teamFileInput");
                if (fileInput.length) {
                    e.stopPropagation();
                    fileInput.click();
                }
            });

        // Send files button (delegated)
        $(document)
            .off("click", "#teamSendFilesBtn")
            .on("click", "#teamSendFilesBtn", function (e) {
                e.preventDefault();
                sendTeamFiles();
            });

        // Reset modal on close (delegated)
        $(document)
            .off("hidden.bs.modal", "#teamFileUploadModal")
            .on("hidden.bs.modal", "#teamFileUploadModal", function () {
                resetTeamModal();
            });

        // Message input change (delegated) - delegate from teamFilesList container
        $("#teamFilesList")
            .off("input", ".file-message-input")
            .on("input", ".file-message-input", function () {
                const index = $(this).data("index");
                if (teamSelectedFiles[index]) {
                    const newVal = $(this).val();
                    console.log("team file message input", index, newVal);
                    if (teamSelectedFiles[index].message !== newVal) {
                        teamSelectedFiles[index].message = newVal;
                    }
                }
            });

        // Remove file button (delegated) - delegate from teamFilesList container
        $("#teamFilesList")
            .off("click", ".remove-file-btn")
            .on("click", ".remove-file-btn", function (e) {
                e.preventDefault();
                e.stopPropagation();
                const index = $(this).data("index");
                teamSelectedFiles.splice(index, 1);
                updateTeamPreview();
            });

        function handleTeamFiles(files) {
            console.log("handleTeamFiles called with", files.length, "files");
            files.forEach((file) => {
                if (
                    !teamSelectedFiles.find(
                        (f) => f.name === file.name && f.size === file.size
                    )
                ) {
                    teamSelectedFiles.push({
                        file: file,
                        message: "",
                        preview: null,
                    });
                }
            });
            updateTeamPreview();
        }

        function updateTeamPreview() {
            const filesPreviewContainer = $("#teamFilesPreviewContainer");
            const dropZone = $("#teamDropZone");
            const filesList = $("#teamFilesList");
            const fileCount = $("#teamFileCount");

            if (teamSelectedFiles.length === 0) {
                if (filesPreviewContainer.length) filesPreviewContainer.hide();
                if (dropZone.length) dropZone.show();
                return;
            }

            if (dropZone.length) dropZone.hide();
            if (filesPreviewContainer.length) filesPreviewContainer.show();
            if (fileCount.length) fileCount.text(teamSelectedFiles.length);
            if (filesList.length) filesList.empty();

            teamSelectedFiles.forEach((fileObj, index) => {
                const file = fileObj.file;
                const fileSize = formatFileSize(file.size);
                const fileExt = chatFileExtension(file);
                const isImage = isChatImageFile(file);
                const isVideo = isChatVideoFile(file);

                let previewHTML = `
                    <div class="file-preview-item" data-index="${index}">
                        <button type="button" class="remove-file-btn" data-index="${index}">
                            <i class="fas fa-times"></i>
                        </button>
                        <div class="file-preview-content">
                `;

                if (isImage) {
                    const reader = new FileReader();
                    reader.onload = function (e) {
                        $(
                            `#teamFilesList [data-index="${index}"] .file-preview-placeholder`
                        ).replaceWith(
                            `<img src="${
                                e.target.result
                            }" class="file-preview-thumbnail" alt="${escapeHtml(
                                file.name
                            )}">`
                        );
                    };
                    reader.onerror = function () {
                        $(
                            `#teamFilesList [data-index="${index}"] .file-preview-placeholder`
                        ).replaceWith(
                            `<div class="file-preview-icon"><i class="fas fa-image"></i></div>`
                        );
                    };
                    reader.readAsDataURL(file);
                    previewHTML += `<div class="file-preview-placeholder" style="width: 80px; height: 80px; background: #f5f5f5; border-radius: 6px;"></div>`;
                } else if (isVideo) {
                    previewHTML += `<div class="file-preview-icon"><i class="fas fa-video"></i></div>`;
                } else {
                    const icon = getFileIcon(fileExt);
                    previewHTML += `<div class="file-preview-icon"><i class="fas fa-${icon}"></i></div>`;
                }

                previewHTML += `
                            <div class="file-preview-info">
                                <div class="file-preview-name">${escapeHtml(
                                    file.name
                                )}</div>
                                <div class="file-preview-size">${fileSize}</div>
                            </div>
                        </div>
                        <input type="text" class="file-message-input" placeholder="Add a caption for this file..."
                               data-index="${index}" value="${escapeHtml(
                    fileObj.message
                )}">
                    </div>
                `;

                if (filesList.length) {
                    filesList.append(previewHTML);
                }
            });
        }

        function sendTeamFiles() {
            if (teamSelectedFiles.length === 0) {
                if (typeof toastr !== "undefined") {
                    toastr.warning("Please select at least one file");
                }
                return;
            }

            if (typeof window.sendTeamFilesWithMessages !== "function") {
                console.error("sendTeamFilesWithMessages function not found");
                return;
            }

            // Store files globally so the main send function can access them
            window.teamFilesToSend = teamSelectedFiles.slice(); // Create a copy

            const total = window.teamFilesToSend.length;
            showModalBusy(
                "teamFileUploadModal",
                "Uploading 0 of " + total + " file(s)...",
                0
            );

            Promise.resolve(
                window.sendTeamFilesWithMessages(
                    window.teamFilesToSend,
                    function (done, count) {
                        showModalBusy(
                            "teamFileUploadModal",
                            "Uploading " + done + " of " + count + " file(s)...",
                            (done / count) * 100
                        );
                    }
                )
            )
                .then(function () {
                    hideModalBusy("teamFileUploadModal");
                    const modalEl =
                        document.getElementById("teamFileUploadModal");
                    if (modalEl) {
                        const modalInstance =
                            bootstrap.Modal.getInstance(modalEl);
                        if (modalInstance) {
                            modalInstance.hide();
                        }
                    }
                })
                .catch(function (err) {
                    console.error("Team file upload failed", err);
                    hideModalBusy("teamFileUploadModal");
                });
        }

        function resetTeamModal() {
            teamSelectedFiles = [];
            const filesPreviewContainer = $("#teamFilesPreviewContainer");
            const dropZone = $("#teamDropZone");
            const filesList = $("#teamFilesList");
            const fileInput = $("#teamFileInput");

            if (filesPreviewContainer.length) filesPreviewContainer.hide();
            if (dropZone.length) dropZone.show();
            if (filesList.length) filesList.empty();
            if (fileInput.length) fileInput.val("");
        }
    }

    // Utility functions
    function formatFileSize(bytes) {
        if (bytes === 0) return "0 Bytes";
        const k = 1024;
        const sizes = ["Bytes", "KB", "MB", "GB"];
        const i = Math.floor(Math.log(bytes) / Math.log(k));
        return (
            Math.round((bytes / Math.pow(k, i)) * 100) / 100 + " " + sizes[i]
        );
    }

    function getFileIcon(extension) {
        const icons = {
            pdf: "file-pdf",
            doc: "file-word",
            docx: "file-word",
            xls: "file-excel",
            xlsx: "file-excel",
            ppt: "file-powerpoint",
            pptx: "file-powerpoint",
            txt: "file-alt",
            zip: "file-archive",
            rar: "file-archive",
        };
        return icons[extension] || "file";
    }

    function escapeHtml(text) {
        const map = {
            "&": "&amp;",
            "<": "&lt;",
            ">": "&gt;",
            '"': "&quot;",
            "'": "&#039;",
        };
        try {
            text = text == null ? "" : String(text);
            return text.replace(/[&<>\"']/g, (m) => map[m]);
        } catch (e) {
            console.error("escapeHtml error:", e, text);
            return String(text == null ? "" : text);
        }
    }

    // Helper function to clear stuck modal backdrops
    function clearModalBackdrop() {
        $(".modal-backdrop").remove();
        $("body").removeClass("modal-open");
        $("body").css("padding-right", "");
    }

    // Global function to reinitialize after AJAX load
    window.reinitFileUploadModals = function () {
        console.log("Reinitializing file upload modals...");
        initChatFileModal();
        initTeamFileModal();
    };

    // Global function to clear stuck backdrops
    window.clearStuckModalBackdrop = clearModalBackdrop;

    // Initialize on document ready
    $(document).ready(function () {
        console.log("Document ready, initializing file upload modals...");

        // Clear any stuck backdrops from previous page
        clearModalBackdrop();

        initChatFileModal();
        initTeamFileModal();

        // Emergency backdrop remover - click on backdrop to remove it
        $(document).on("click", ".modal-backdrop", function (e) {
            console.log("Backdrop clicked, force removing...");
            clearModalBackdrop();
        });

        // Also listen for ESC key to clear backdrop
        $(document).on("keydown", function (e) {
            if (e.key === "Escape" || e.keyCode === 27) {
                if (
                    $(".modal-backdrop").length > 0 &&
                    !$(".modal.show").length
                ) {
                    console.log("ESC pressed, clearing stuck backdrop...");
                    clearModalBackdrop();
                }
            }
        });

        // Debug: listen for clicks on file input
        $(document)
            .off("click", "#fileInput")
            .on("click", "#fileInput", function (e) {
                console.log("fileInput clicked (debug)");
            });
    });
})();
