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

    // Initialize for regular chat
    function initChatFileModal() {
        console.log("Initializing chat file modal...");

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
                    handleChatFiles(files);
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
                handleChatFiles(files);
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
                const fileExt = file.name.split(".").pop().toLowerCase();
                const isImage = file.type.startsWith("image/");
                const isVideo = file.type.startsWith("video/");

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

            // Store files globally so the main send function can access them
            window.chatFilesToSend = chatSelectedFiles.slice(); // Create a copy

            // Close modal using Bootstrap 5 API (same as newsletter example)
            const modalEl = document.getElementById("fileUploadModal");
            if (modalEl) {
                const modalInstance = bootstrap.Modal.getInstance(modalEl);
                if (modalInstance) {
                    modalInstance.hide();
                }
            }

            // Trigger the actual send after modal closes
            setTimeout(() => {
                if (typeof window.sendChatFilesWithMessages === "function") {
                    window.sendChatFilesWithMessages(window.chatFilesToSend);
                } else {
                    console.error(
                        "sendChatFilesWithMessages function not found"
                    );
                }
            }, 300);
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
                    handleTeamFiles(files);
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
                handleTeamFiles(files);
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
                const fileExt = file.name.split(".").pop().toLowerCase();
                const isImage = file.type.startsWith("image/");
                const isVideo = file.type.startsWith("video/");

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

            // Store files globally so the main send function can access them
            window.teamFilesToSend = teamSelectedFiles.slice(); // Create a copy

            // Close modal using Bootstrap 5 API (same as newsletter example)
            const modalEl = document.getElementById("teamFileUploadModal");
            if (modalEl) {
                const modalInstance = bootstrap.Modal.getInstance(modalEl);
                if (modalInstance) {
                    modalInstance.hide();
                }
            }

            // Trigger the actual send after modal closes
            setTimeout(() => {
                if (typeof window.sendTeamFilesWithMessages === "function") {
                    window.sendTeamFilesWithMessages(window.teamFilesToSend);
                } else {
                    console.error(
                        "sendTeamFilesWithMessages function not found"
                    );
                }
            }, 300);
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
