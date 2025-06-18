$(document).ready(function () {
    var sender_id = window.Laravel.authUserId;
    var receiver_id = null;
    $.ajaxSetup({
        headers: {
            "X-CSRF-TOKEN": $('meta[name="csrf-token"]').attr("content"),
        },
    });
    let ip_address = window.Laravel.ipAddress;
    let socket_port = "3000";
    let socket = io(ip_address + ":" + socket_port);

    $(document).on("click", ".user-list", function (e) {
        var getUserID = $(this).attr("data-id");
        receiver_id = getUserID;
        loadChats();
        // Remove "active" class from all user-list elements first
        $(".user-list").removeClass("active");

        socket.emit("read-chat", {
            read_chat: 1,
            receiver_id: receiver_id,
            sender_id: sender_id,
        });

        // Add "active" class to the clicked element
        $("#chat_list_user_" + getUserID).addClass("active");

        $("#last_activate_user").val(getUserID);
    });

    function loadChats() {
        $.ajax({
            type: "POST",
            url: window.Laravel.routes.chatLoad,
            data: {
                _token: $("input[name=_token]").val(),
                reciver_id: receiver_id,
                sender_id: sender_id,
            },
            success: function (resp) {
                if (resp.status === true) {
                    $(".chat-module").html(resp.view);

                    // unseen_chat count remove
                    removeUnseenCount(receiver_id);

                    socket.emit("multiple_seen", {
                        unseen_chat: resp.unseen_chat,
                    });

                    if (resp.chat_count > 0) {
                        scrollChatToBottom(receiver_id);
                    }

                    // Initialize EmojiOneArea on MessageInput
                    var emojioneAreaInstance = $("#MessageInput").emojioneArea({
                        pickerPosition: "top",
                        filtersPosition: "top",
                        tonesStyle: "bullet",
                    });

                    // Handle Enter key press within the emoji picker
                    emojioneAreaInstance[0].emojioneArea.on(
                        "keydown",
                        function (editor, event) {
                            if (event.which === 13 && !event.shiftKey) {
                                event.preventDefault();
                                $("#MessageForm").submit();
                            }
                        }
                    );
                } else {
                    toastr.error(resp.msg);
                }
            },
            error: function (xhr, status, error) {
                console.error(
                    "An error occurred: " + status + "\nError: " + error
                );
            },
        });
    }

    function scrollChatToBottom(receiver_id) {
        var messages = document.getElementById("chat-container-" + receiver_id);
        if (messages) {
            messages.scrollTop = messages.scrollHeight;
        }
    }

    function updateChatListItem(
        user,
        isNewMessage = false,
        messageText = "",
        isFile = false
    ) {
        let timeZone = window.Laravel.authTimeZone;
        let time_format =
            user.last_message && user.last_message.created_at
                ? moment
                      .tz(user.last_message.created_at, timeZone)
                      .format("hh:mm A")
                : moment().format("hh:mm A");

        let chatListItem = $("#chat_list_user_" + user.id);
        let chatContainer = $("#group-manage-" + sender_id);

        // If chat list item doesn't exist, create it
        if (chatListItem.length === 0) {
            createNewChatListItem(user, messageText, isFile, time_format);
            return;
        }

        // Update existing chat list item
        let messageContent = "";
        if (isNewMessage) {
            if (isFile) {
                messageContent =
                    "<span>📎 File </span>   <span>" + messageText + "</span>";
            } else {
                messageContent = messageText || "";
            }
        } else if (user.last_message && user.last_message.message) {
            messageContent = user.last_message.message;
        } else if (user.last_message && user.last_message.attachment) {
            messageContent = "<span>📎 File </span>";
        }

        // Update message content
        chatListItem.find("#message-app-" + user.id).html(messageContent);

        // Update time
        let timeElement = chatListItem.find('[id^="last-chat-time-"]');
        timeElement.html(`<p>${time_format}</p>`);

        // Update the time element ID if we have a new message
        if (isNewMessage && user.last_message) {
            timeElement.attr("id", "last-chat-time-" + user.last_message.id);
        }

        // Move to top of chat list (most recent conversation first)
        chatListItem.prependTo(chatContainer);
    }

    function createNewChatListItem(
        user,
        messageText = "",
        isFile = false,
        timeFormat = ""
    ) {
        let profileImage = user.profile_picture
            ? window.Laravel.storageUrl + user.profile_picture
            : window.Laravel.assetUrls.profileDummy;

        let messageContent = "";
        if (isFile) {
            messageContent = '<span><i class="ti ti-file"></i></span>';
        } else {
            messageContent = messageText || "";
        }

        let timeId = user.last_message
            ? "last-chat-time-" + user.last_message.id
            : "last-chat-time-default-" + user.id;

        let chatListHtml = `
            <li class="group user-list" id="chat_list_user_${
                user.id
            }" data-id="${user.id}">
                <div class="avatar">
                    <img src="${profileImage}" alt="">
                </div>
                <p class="GroupName">${user.first_name || ""} ${
            user.middle_name || ""
        } ${user.last_name || ""}</p>
                <p class="GroupDescrp" id="message-app-${user.id}">
                    ${messageContent}
                </p>
                <div class="time_online" id="${timeId}">
                    ${timeFormat ? `<p>${timeFormat}</p>` : ""}
                </div>
            </li>
        `;

        $("#group-manage-" + sender_id).prepend(chatListHtml);
    }

    function addUnseenCount(userId, count) {
        let unseenElement = $("#count-unseen-" + userId);

        if (unseenElement.length === 0 && count > 0) {
            let unseenHtml = `<div class="count-unseen" id="count-unseen-${userId}">
                <span><p>${count}</p></span>
            </div>`;
            $("#chat_list_user_" + userId).append(unseenHtml);
        } else if (unseenElement.length > 0) {
            // Update existing count
            let currentCount = parseInt(unseenElement.find("p").text()) || 0;
            unseenElement.find("p").text(currentCount + count);
        }
    }

    function removeUnseenCount(userId) {
        $("#count-unseen-" + userId).remove();
    }

    $(document).on("submit", "#MessageForm", function (e) {
        e.preventDefault();

        // Get the message from the input field emoji area
        var message = $("#MessageInput")
            .emojioneArea()[0]
            .emojioneArea.getText();
        var receiver_id = $(".reciver_id").val();
        var url = window.Laravel.routes.chatSend;

        // Get the file data
        var fileInput = $("#file2")[0];
        var file = fileInput.files[0];

        // Create a FormData object to send both message and file
        var formData = new FormData();
        formData.append("_token", $("input[name=_token]").val());
        formData.append("message", message);
        formData.append("reciver_id", receiver_id);
        formData.append("sender_id", sender_id);

        // Append the file if one is selected
        if (file) {
            formData.append("file", file);
        } else {
            if (message.trim() == "") {
                return false;
            }
        }

        //  $("#loading").addClass("loading");
        //  $("#loading-content").addClass("loading-content");

        const sendButton = $(".Send");
        sendButton.addClass("sendloading");

        // Perform Ajax request to send the message to the server
        $.ajax({
            type: "POST",
            url: url,
            data: formData,
            processData: false,
            contentType: false,
            success: function (res) {
                sendButton.removeClass("sendloading");
                if (res.success) {
                    $("#MessageInput").data("emojioneArea").setText("");
                    $("#file2").val("");
                    $("#file-name-display").hide();

                    let chat = res.chat.message || "";
                    let html = generateMessageHtml(res.chat, "me", chat);

                    // Append message to chat container
                    if (res.chat_count > 0) {
                        $("#chat-container-" + receiver_id).append(html);
                    } else {
                        $("#chat-container-" + receiver_id).html(html);
                    }
                    scrollChatToBottom(receiver_id);

                    // Update chat list immediately without API call
                    let receiverUser = {
                        id: parseInt(receiver_id),
                        first_name: $(".GroupName").text().split(" ")[0] || "",
                        last_name:
                            $(".GroupName")
                                .text()
                                .split(" ")
                                .slice(1)
                                .join(" ") || "",
                        profile_picture: null, // Will be handled by existing image
                        last_message: {
                            id: res.chat.id,
                            message: res.chat.attachment
                                ? null
                                : res.chat.message,
                            attachment: res.chat.attachment,
                            created_at: res.chat.created_at,
                        },
                    };

                    updateChatListItem(
                        receiverUser,
                        true,
                        res.chat.message,
                        !!res.chat.attachment
                    );

                    $("#loading").removeClass("loading");
                    $("#loading-content").removeClass("loading-content");

                    // Emit chat message to the server
                    let fileUrl = res.chat.attachment
                        ? window.Laravel.storageUrl + res.chat.attachment
                        : "";

                    socket.emit("chat", {
                        message: message,
                        sender_id: sender_id,
                        receiver_id: receiver_id,
                        // receiver_users: res.receiver_users,
                        chat_id: res.chat.id,
                        file_url: fileUrl,
                        time: res.chat.created_at_formatted,
                        created_at: res.chat.new_created_at,
                        // Add sender info for chat list updates
                        sender_info: {
                            id: sender_id,
                            first_name: window.Laravel.userInfo.firstName,
                            middle_name: window.Laravel.userInfo.middleName,
                            last_name: window.Laravel.userInfo.lastName,
                            profile_picture:
                                window.Laravel.userInfo.profilePicture,
                        },
                    });
                } else {
                    //  $("#loading").removeClass("loading");
                    //  $("#loading-content").removeClass("loading-content");
                    console.log(res.msg);
                }
            },
        });
    });

    function generateMessageHtml(chat, messageType, messageText) {
        let fileUrl = "";
        let html = `<div class="message ${messageType}" id="chat-message-${chat.id}">
                        <div class="message-wrap">`;

        if (chat.attachment) {
            fileUrl = window.Laravel.storageUrl + chat.attachment;
            let extension = chat.attachment.split(".").pop().toLowerCase();

            if (
                ["jpg", "jpeg", "png", "gif", "svg", "webp"].includes(extension)
            ) {
                html += `<p class="messageContent"><a href="${fileUrl}" target="_blank">
                    <img src="${fileUrl}" alt="attachment" style="max-width: 200px; max-height: 200px;">
                </a><br><span>${messageText.replace(/\n/g, "<br>")}</span></p>`;
            } else if (["mp4", "webm", "ogg"].includes(extension)) {
                html += `<p class="messageContent"><a href="${fileUrl}" target="_blank">
                    <video width="200" height="200" controls>
                        <source src="${fileUrl}" type="video/${extension}">
                    </video>
                </a><br><span>${messageText.replace(/\n/g, "<br>")}</span></p>`;
            } else {
                html += `<p class="messageContent"><a href="${fileUrl}" download="${
                    chat.attachment
                }">
                    <img src="${window.Laravel.assetUrls.fileIcon}" alt="">
                </a><br><span>${messageText.replace(/\n/g, "<br>")}</span></p>`;
            }
        } else {
            html += `<p class="messageContent">${messageText.replace(
                /\n/g,
                "<br>"
            )}</p>`;
        }

        if (messageType === "me") {
            html += `<div class="dropdown">
                        <button class="btn btn-secondary dropdown-toggle" type="button"
                            data-bs-toggle="dropdown" aria-expanded="false">
                            <i class="fa-solid fa-ellipsis-vertical"></i>
                        </button>
                        <ul class="dropdown-menu">
                            <li><a class="dropdown-item remove-chat" data-chat-id="${chat.id}" data-del-from="me">Remove For Me</a></li>
                            <li><a class="dropdown-item remove-chat" data-chat-id="${chat.id}" data-del-from="everyone">Remove For Everyone</a></li>
                        </ul>
                    </div>`;
        }

        html += `</div>
                <div class="messageDetails">
                    <div class="messageTime">${chat.created_at_formatted}</div>`;

        if (messageType === "me") {
            html += `<div id="seen_${chat.id}">
                        <i class="fas fa-check"></i>
                    </div>`;
        }

        html += `</div></div>`;
        return html;
    }

    $("#hit-chat-file").click(function (e) {
        e.preventDefault();
        $("#file2").click();
    });

    $(document).on("change", "#file2", function (e) {
        var file = $(this).prop("files")[0];
        var fileName = file?.name || "";
        var $fileNameDisplay = $("#file-name-display");

        if (file && fileName) {
            var isImage = file.type.startsWith("image/");
            var displayContent = "";

            if (isImage) {
                var reader = new FileReader();
                reader.onload = function (e) {
                    displayContent = `<img src="${e.target.result}" alt="Image preview"
                        style="max-width: 100px; max-height: 100px; margin-right: 10px;" />
                        ${fileName}
                        <i class="fas fa-times remove-file" style="cursor: pointer; color: red; margin-left: 10px;"></i>`;
                    $fileNameDisplay.html(displayContent).show();
                };
                reader.readAsDataURL(file);
            } else {
                displayContent = `<i class="fas fa-file"></i> ${fileName}
                    <i class="fas fa-times remove-file" style="cursor: pointer; color: red; margin-left: 10px;"></i>`;
                $fileNameDisplay.html(displayContent).show();
            }
        } else {
            $fileNameDisplay.hide();
        }
    });

    $(document).on("click", ".remove-file", function () {
        $("#file-name-display").hide();
        $("#file2").val("");
    });

    // Handle direct file upload
    $(document).on("change", "#file", function (e) {
        var file = e.target.files[0];
        var receiver_id = $(".reciver_id").val();

        if (!file || !receiver_id) return;

        var formData = new FormData();
        formData.append("file", file);
        formData.append("_token", $("meta[name='csrf-token']").attr("content"));
        formData.append("reciver_id", receiver_id);
        formData.append("sender_id", sender_id);

        $("#loading").addClass("loading");
        $("#loading-content").addClass("loading-content");

        $.ajax({
            type: "POST",
            url: window.Laravel.routes.chatSend,
            data: formData,
            processData: false,
            contentType: false,
            success: function (res) {
                if (res.success) {
                    let html = generateMessageHtml(res.chat, "me", "");

                    if (res.chat_count > 0) {
                        $("#chat-container-" + receiver_id).append(html);
                    } else {
                        $("#chat-container-" + receiver_id).html(html);
                    }
                    scrollChatToBottom(receiver_id);

                    // Update chat list immediately without API call
                    let receiverUser = {
                        id: parseInt(receiver_id),
                        first_name: $(".GroupName").text().split(" ")[0] || "",
                        last_name:
                            $(".GroupName")
                                .text()
                                .split(" ")
                                .slice(1)
                                .join(" ") || "",
                        profile_picture: null,
                        last_message: {
                            id: res.chat.id,
                            message: null,
                            attachment: res.chat.attachment,
                            created_at: res.chat.created_at,
                        },
                    };

                    updateChatListItem(receiverUser, true, "", true);

                    $("#loading").removeClass("loading");
                    $("#loading-content").removeClass("loading-content");

                    let fileUrl =
                        window.Laravel.storageUrl + res.chat.attachment;
                    socket.emit("chat", {
                        message: file.name,
                        file_url: fileUrl,
                        sender_id: sender_id,
                        receiver_id: receiver_id,
                        // receiver_users: res.receiver_users,
                        chat_id: res.chat.id,
                        time: res.chat.created_at_formatted,
                        created_at: res.chat.new_created_at,
                        sender_info: {
                            id: sender_id,
                            first_name: window.Laravel.userInfo.firstName,
                            middle_name: window.Laravel.userInfo.middleName,
                            last_name: window.Laravel.userInfo.lastName,
                            profile_picture:
                                window.Laravel.userInfo.profilePicture,
                        },
                    });
                } else {
                    $("#loading").removeClass("loading");
                    $("#loading-content").removeClass("loading-content");
                    console.log(res.msg);
                }
            },
        });
    });

    // clear-chat
    $(document).on("click", ".clear-chat", function (e) {
        var receiver_id = $(this).data("reciver-id");
        if (!confirm("Are you sure you want to clear chat?")) {
            return false;
        }

        $.ajax({
            type: "POST",
            url: window.Laravel.routes.chatClear,
            data: {
                _token: $("input[name=_token]").val(),
                reciver_id: receiver_id,
                sender_id: sender_id,
            },
            success: function (res) {
                if (res.success) {
                    $("#chat-container-" + receiver_id).html("");
                    $("#message-app-" + receiver_id).html("");

                    // Clear last message time in chat list
                    let chatListItem = $("#chat_list_user_" + receiver_id);
                    chatListItem.find('[id^="last-chat-time-"]').html("");

                    socket.emit("clear-chat", {
                        receiver_id: receiver_id,
                        sender_id: sender_id,
                    });
                } else {
                    console.log(res.msg);
                }
            },
        });
    });

    //remove-chat
    $(document).on("click", ".remove-chat", function (e) {
        var chat_id = $(this).data("chat-id");
        var del_from = $(this).data("del-from");

        if (!confirm("Are you sure you want to remove chat?")) {
            return false;
        }

        $.ajax({
            type: "POST",
            url: window.Laravel.routes.chatRemove,
            data: {
                _token: $("input[name=_token]").val(),
                chat_id: chat_id,
                del_from: del_from,
            },
            success: function (res) {
                if (res.status == true) {
                    $("#chat-message-" + chat_id).remove();

                    if (res.last_message_update) {
                        updateLastMessageInChatList(res);
                    }

                    if (del_from == "everyone") {
                        socket.emit("remove-chat", {
                            chat: res.chat,
                            last_message_update: res.last_message_update,
                            new_last_message: res.new_last_message,
                            other_user_id: res.other_user_id,
                        });
                    }
                } else {
                    console.log(res.msg);
                }
            },
        });
    });

    function updateLastMessageInChatList(res) {
        if (res.new_last_message) {
            $("#message-app-" + res.other_user_id).html(
                res.new_last_message.message ||
                    '<span><i class="ti ti-file"></i></span>'
            );
            let chatListItem = $("#chat_list_user_" + res.other_user_id);
            let timeFormat = moment(res.new_last_message.created_at).format(
                "h:mm A"
            );
            chatListItem
                .find('[id^="last-chat-time-"]')
                .html(`<p>${timeFormat}</p>`);
        } else {
            $("#message-app-" + res.other_user_id).html("");
            let chatListItem = $("#chat_list_user_" + res.other_user_id);
            chatListItem.find('[id^="last-chat-time-"]').html("");
        }
    }

    //remove-chat socket listener
    socket.on("remove-chat", function (data) {
        getSidebarNotiCounts();
        if (data.chat.reciver_id == sender_id) {
            $("#chat-message-" + data.chat.id).remove();

            if (data.last_message_update) {
                updateLastMessageInChatList(data);
            }
        }
    });

    // clear-chat socket listener
    socket.on("clear-chat", function (data) {
        getSidebarNotiCounts();
        if (data.receiver_id == sender_id) {
            $("#chat-container-" + data.sender_id).html("");
            $("#message-app-" + data.sender_id).html("");
            let chatListItem = $("#chat_list_user_" + data.sender_id);
            chatListItem.find('[id^="last-chat-time-"]').html("");
        }
    });

    socket.on("read-chat", function (data) {
        getSidebarNotiCounts();
        var receiver_id = $(".reciver_id").val();
        if (receiver_id == data.sender_id) {
            removeUnseenCount(data.receiver_id);
            if (sender_id == data.receiver_id) {
                // $("#seen_").html(
                //     '<i class="fas fa-check-double"></i>'
                // );
                $('[id^="seen_"]').html('<i class="fas fa-check-double"></i>');
            }
        }
    });

    function getNotificationReadUrl(type, id) {
        return window.Laravel.routes.notificationRead
            .replace("__TYPE__", type)
            .replace("__ID__", id);
    }

    // Listen for incoming chat messages from the server
    socket.on("chat", function (data) {
        let timeZone = window.Laravel.authTimeZone;


        if (data.receiver_id == sender_id) {
            // Generate incoming message HTML
            let html = `<div class="message you" id="chat-message-${data.chat_id}">
                            <div class="message-wrap">
                                <p class="messageContent">`;

            let attachment = data.file_url;
            if (attachment && attachment !== "") {
                let extension = attachment.split(".").pop().toLowerCase();

                if (
                    ["jpg", "jpeg", "png", "gif", "svg", "webp"].includes(
                        extension
                    )
                ) {
                    html += `<a href="${data.file_url}" target="_blank">
                        <img src="${
                            data.file_url
                        }" alt="attachment" style="max-width: 200px; max-height: 200px;">
                    </a><br><span>${data.message.replace(
                        /\n/g,
                        "<br>"
                    )}</span>`;
                } else if (["mp4", "webm", "ogg"].includes(extension)) {
                    html += `<a href="${data.file_url}" target="_blank">
                        <video width="200" height="200" controls>
                            <source src="${
                                data.file_url
                            }" type="video/${extension}">
                        </video>
                    </a><br><span>${data.message.replace(
                        /\n/g,
                        "<br>"
                    )}</span>`;
                } else {
                    html += `<a href="${data.file_url}" download="${
                        data.message
                    }">
                        <img src="${window.Laravel.assetUrls.fileIcon}" alt="">
                    </a><br><span>${data.message.replace(
                        /\n/g,
                        "<br>"
                    )}</span>`;
                }
            } else {
                html += `${data.message.replace(/\n/g, "<br>")}`;
            }

            html += `</p>
                        </div>
                        <div class="messageDetails">
                            <div class="messageTime">${moment
                                .tz(data.created_at, timeZone)
                                .format("hh:mm A")}</div>
                        </div>
                    </div>`;

            // Update chat list for incoming message
            if (data.sender_info) {
                let senderUser = {
                    id: data.sender_id,
                    first_name: data.sender_info.first_name || "",
                    middle_name: data.sender_info.middle_name || "",
                    last_name: data.sender_info.last_name || "",
                    profile_picture: data.sender_info.profile_picture,
                    last_message: {
                        id: data.chat_id,
                        message: data.file_url ? null : data.message,
                        attachment: data.file_url
                            ? data.file_url.split("/").pop()
                            : null,
                        created_at: data.created_at,
                    },
                };

                updateChatListItem(
                    senderUser,
                    true,
                    data.message,
                    !!data.file_url
                );
            }

            if ($(".chat-module").length > 0) {
                if ($("#chat-container-" + data.sender_id).length > 0) {
                    $("#chat-container-" + data.sender_id).append(html);
                    scrollChatToBottom(data.sender_id);
                    removeUnseenCount(data.sender_id);

                    // Mark message as seen
                    $.ajax({
                        type: "POST",
                        url: window.Laravel.routes.chatSeen,
                        data: {
                            _token: $("input[name=_token]").val(),
                            reciver_id: data.sender_id,
                            sender_id: sender_id,
                            chat_id: data.chat_id,
                        },
                        success: function (res) {
                            if (res.status == true) {
                                socket.emit("seen", {
                                    last_chat: res.last_chat,
                                });
                                getSidebarNotiCounts();
                            }
                        },
                    });
                } else {
                    addUnseenCount(data.sender_id, 1);
                    getSidebarNotiCounts();
                }
            } else {
                addUnseenCount(data.sender_id, 1);
                getSidebarNotiCounts();
            }

            // Handle notifications
            handleChatNotification(data);
        }
    });

    // Update socket listeners for better chat list management
    socket.on("remove-chat", function (data) {
        getSidebarNotiCounts();
        if (data.chat.reciver_id == sender_id) {
            $("#chat-message-" + data.chat.id).remove();

            if (data.last_message_update) {
                if (data.new_last_message) {
                    let otherUser = {
                        id: data.other_user_id,
                        last_message: data.new_last_message,
                    };
                    updateChatListItem(
                        otherUser,
                        true,
                        data.new_last_message.message,
                        !!data.new_last_message.attachment
                    );
                } else {
                    $("#message-app-" + data.other_user_id).html("");
                    let chatListItem = $(
                        "#chat_list_user_" + data.other_user_id
                    );
                    chatListItem.find('[id^="last-chat-time-"]').html("");
                }
            }
        }
    });

    socket.on("clear-chat", function (data) {
        getSidebarNotiCounts();
        if (data.receiver_id == sender_id) {
            $("#chat-container-" + data.sender_id).html("");
            $("#message-app-" + data.sender_id).html("");
            let chatListItem = $("#chat_list_user_" + data.sender_id);
            chatListItem.find('[id^="last-chat-time-"]').html("");
        }
    });

    // seen message
    socket.on("seen", function (data) {
        getSidebarNotiCounts();
        var receiver_id = $(".reciver_id").val();
        if (receiver_id == data.last_chat.reciver_id) {
            if (sender_id == data.last_chat.sender_id) {
                $("#seen_" + data.last_chat.id).html(
                    '<i class="fas fa-check-double"></i>'
                );
            }
        }
    });

    //multiple_seen
    socket.on("multiple_seen", function (data) {
        getSidebarNotiCounts();
        if (data.unseen_chat && Array.isArray(data.unseen_chat)) {
            data.unseen_chat.forEach(function (chat) {
                if (sender_id == chat.sender_id) {
                    $("#seen_" + chat.id).html(
                        '<i class="fas fa-check-double"></i>'
                    );
                }
            });
        }
    });
});
