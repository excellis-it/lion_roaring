$(document).ready(function () {
    var sender_id = window.Laravel.authUserId;
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
                    $("#count-unseen-" + receiver_id).remove();

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
                            const $messageInput = $(".emojionearea-editor");

                            console.log("key is : " + event.key);

                            var message = $("#MessageInput")
                                .emojioneArea()[0]
                                .emojioneArea.getText();
                            console.log("message is " + message);

                            if (event.key === "Enter") {
                                //
                            } else {
                                if (event.which === 13 && !event.shiftKey) {
                                    event.preventDefault();
                                    $("#MessageForm").submit();
                                }
                            }

                            // var formattedMessage = message.replace(
                            //     /\b[a-zA-Z0-9._-]+\.[a-zA-Z]{2,}\b/g,
                            //     function(word) {
                            //         var url = word.startsWith('http') ? word :
                            //             'http://' + word; // Add http:// if not present
                            //         return `<a href="${url}" target="_blank">${word}</a>`;
                            //     });

                            // var message = $("#MessageInput").emojioneArea()[0].emojioneArea
                            //     .setText(formattedMessage);
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
        messages.scrollTop = messages.scrollHeight;
    }

    $(document).on("submit", "#MessageForm", function (e) {
        e.preventDefault();

        // Get the message from the input field emoji area
        var message = $("#MessageInput")
            .emojioneArea()[0]
            .emojioneArea.getText();
        var receiver_id = $(".reciver_id").val();
        //  var sender_id = $("input[name=sender_id]").val(); // Assuming sender_id is available
        var url = window.Laravel.routes.chatSend;

        // Get the file data
        var fileInput = $("#file2")[0];
        var file = fileInput.files[0]; // The selected file

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
        $("#loading").addClass("loading");
        $("#loading-content").addClass("loading-content");

        // Perform Ajax request to send the message to the server
        $.ajax({
            type: "POST",
            url: url,
            data: formData,
            processData: false, // Don't process the data
            contentType: false,
            success: function (res) {
                if (res.success) {
                    $("#MessageInput").data("emojioneArea").setText("");
                    $("#file2").val("");
                    $("#file-name-display").hide();

                    let chat = res.chat.message;
                    let created_at = res.chat.created_at_formatted;
                    // use timezones to format the time America/New_York
                    let time_format_12 = moment(
                        created_at,
                        "YYYY-MM-DD HH:mm:ss"
                    ).format("hh:mm A");

                    let html = ` <div class="message me" id="chat-message-${res.chat.id}">
                                  <div class="message-wrap">`;
                    let fileUrl = "";
                    let attachment = res.chat.attachment;
                    if (attachment != "") {
                        fileUrl = window.Laravel.storageUrl + attachment;
                        let attachement_extention = attachment.split(".").pop();

                        if (
                            ["jpg", "jpeg", "png", "gif"].includes(
                                attachement_extention
                            )
                        ) {
                            html += ` <p class="messageContent"><a href="${fileUrl}" target="_blank"><img src="${fileUrl}" alt="attachment" style="max-width: 200px; max-height: 200px;"></a><br><span class="">${chat.replace(
                                /\n/g,
                                "<br>"
                            )}</span></p>`;
                        } else if (
                            ["mp4", "webm", "ogg"].includes(
                                attachement_extention
                            )
                        ) {
                            html += ` <p class="messageContent"><a href="${fileUrl}" target="_blank"><video width="200" height="200" controls><source src="${fileUrl}" type="video/mp4"><source src="${fileUrl}" type="video/webm"><source src="${fileUrl}" type="video/ogg"></video></a><br><span class="">${chat.replace(
                                /\n/g,
                                "<br>"
                            )}</span></p>`;
                        } else {
                            html += ` <p class="messageContent"><a href="${fileUrl}" download="${attachment}"><img src="${
                                window.Laravel.assetUrls.fileIcon
                            }" alt=""></a><br><span class="">${chat.replace(
                                /\n/g,
                                "<br>"
                            )}</span></p>`;
                        }
                    } else {
                        html += ` <p class="messageContent">${chat.replace(
                            /\n/g,
                            "<br>"
                        )}</p>`;
                    }

                    html += `<div class="dropdown">
                                            <button class="btn btn-secondary dropdown-toggle" type="button" id="dropdownMenuButton1"
                                                data-bs-toggle="dropdown" aria-expanded="false">
                                                <i class="fa-solid fa-ellipsis-vertical"></i>
                                            </button>
                                            <ul class="dropdown-menu" aria-labelledby="dropdownMenuButton1">
                                                <li><a class="dropdown-item remove-chat" data-chat-id="${res.chat.id}" data-del-from="me">Remove For Me</a></li>
                                                        <li><a class="dropdown-item remove-chat" data-chat-id="${res.chat.id}" data-del-from="everyone">Remove For Everyone</a></li>
                                            </ul>
                                        </div>
                                        </div>
                                 <div class="messageDetails">
                                     <div class="messageTime">${res.chat.created_at_formatted}</div>
                                     <div id="seen_${res.chat.id}">
                                     <i class="fas fa-check"></i>
                                     </div>
                                 </div>
                             </div>
                         `;
                    $("#message-app-" + receiver_id).html(chat);
                    if (res.chat_count > 0) {
                        $("#chat-container-" + receiver_id).append(html);
                        scrollChatToBottom(receiver_id);
                    } else {
                        $("#chat-container-" + receiver_id).html(html);
                    }

                    var users = res.users;
                    $("#group-manage-" + sender_id).html("");
                    var new_html = "";

                    users.forEach((user) => {
                        // let timezone = 'America/New_York';
                        let time_format_13 =
                            user.last_message && user.last_message.created_at
                                ? moment
                                      .tz(user.last_message.created_at)
                                      .format("hh:mm A")
                                : "";

                        new_html += `
                                 <li class="group user-list ${
                                     user.id == receiver_id ? "active" : ""
                                 }" data-id="${user.id}">
                                     <div class="avatar">`;

                        if (user.profile_picture) {
                            var profile_picture =
                                window.Laravel.storageUrl +
                                user.profile_picture;
                            new_html += `<img src="${profile_picture}" alt="">`;
                        } else {
                            new_html += `<img src="${window.Laravel.assetUrls.profileDummy}" alt="">`;
                        }

                        new_html += `</div>
                                     <p class="GroupName">${user.first_name} ${
                            user.middle_name ? user.middle_name : ""
                        } ${user.last_name ? user.last_name : ""}</p>
                                     <p class="GroupDescrp last-chat-${
                                         user.last_message
                                             ? user.last_message.id
                                             : ""
                                     }">${
                            user.last_message && user.last_message.message
                                ? user.last_message.message
                                : ""
                        }</p>
                                     <div class="time_online" id="last-chat-time-${
                                         user.last_message
                                             ? user.last_message.id
                                             : ""
                                     }">
                                         <p>${
                                             user.last_message
                                                 ? user.last_message.time
                                                 : ""
                                         }</p>
                                     </div>
                                 </li>`;
                    });

                    $("#group-manage-" + sender_id).append(new_html);
                    $("#loading").removeClass("loading");
                    $("#loading-content").removeClass("loading-content");
                    // Emit chat message to the server
                    socket.emit("chat", {
                        message: message,
                        sender_id: sender_id,
                        receiver_id: receiver_id,
                        receiver_users: res.receiver_users,
                        chat_id: res.chat.id,
                        file_url: fileUrl,
                        time: res.chat.created_at_formatted,
                        created_at: res.chat.new_created_at,
                    });
                } else {
                    $("#loading").removeClass("loading");
                    $("#loading-content").removeClass("loading-content");
                    console.log(res.msg);
                }
            },
        });
    });

    $("#hit-chat-file").click(function (e) {
        e.preventDefault();
        $("#file2").click();
        $("#team-file2").click();
    });

    $(document).on("change", "#file2, #team-file2", function (e) {
        var file = $(this).prop("files")[0]; // Get the selected file
        var fileName = file?.name || ""; // Get the file name
        var $fileNameDisplay = $("#file-name-display");

        if (file && fileName) {
            // Check if the file is an image
            var isImage = file.type.startsWith("image/"); // Check if it's an image
            var displayContent = "";

            if (isImage) {
                // Create an image preview
                var reader = new FileReader();
                reader.onload = function (e) {
                    // Display image preview with remove button
                    displayContent =
                        '<img src="' +
                        e.target.result +
                        '" alt="Image preview" style="max-width: 100px; max-height: 100px; margin-right: 10px;" />' +
                        fileName +
                        ' <i class="fas fa-times remove-file" style="cursor: pointer; color: red; margin-left: 10px;"></i>';
                    $fileNameDisplay.html(displayContent).show();
                };
                reader.readAsDataURL(file); // Read the file as a data URL
            } else {
                // Display default file icon with remove button
                displayContent =
                    '<i class="fas fa-file"></i> ' +
                    fileName +
                    ' <i class="fas fa-times remove-file" style="cursor: pointer; color: red; margin-left: 10px;"></i>';
                $fileNameDisplay.html(displayContent).show();
            }
        } else {
            $fileNameDisplay.hide(); // Hide if no file selected
        }
    });

    // Remove file on click of cross icon
    $(document).on("click", ".remove-file", function () {
        var $fileNameDisplay = $("#file-name-display");
        $fileNameDisplay.hide(); // Hide the file display area

        // Clear the file input
        $("#file2").val("");
        $("#team-file2").val("");
    });

    $(document).on("change", "#file", function (e) {
        var file = e.target.files[0];
        var receiver_id = $(".reciver_id").val();
        var formData = new FormData();
        formData.append("file", file);
        formData.append("_token", $("meta[name='csrf-token']").attr("content")); // Retrieve CSRF token from meta tag
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
                    let attachment = res.chat.attachment;
                    let fileUrl = window.Laravel.storageUrl + attachment;
                    let attachement_extention = attachment.split(".").pop();
                    let created_at = res.chat.created_at;
                    // let timeZome = 'America/New_York';
                    let time_format_12 = moment
                        .tz(created_at)
                        .format("hh:mm A");
                    let html = `<div class="message me">`;
                    if (
                        ["jpg", "jpeg", "png", "gif"].includes(
                            attachement_extention
                        )
                    ) {
                        html += ` <div class="message-wrap"><p class="messageContent"><a href="${fileUrl}" target="_blank"><img src="${fileUrl}" alt="attachment" style="max-width: 200px; max-height: 200px;"></a></p>`;
                    } else if (
                        ["mp4", "webm", "ogg"].includes(attachement_extention)
                    ) {
                        html += ` <div class="message-wrap"><p class="messageContent"><a href="${fileUrl}" target="_blank"><video width="200" height="200" controls><source src="${fileUrl}" type="video/mp4"><source src="${fileUrl}" type="video/webm"><source src="${fileUrl}" type="video/ogg"></video></a></p>`;
                    } else {
                        html += ` <div class="message-wrap"><p class="messageContent"><a href="${fileUrl}" download="${attachment}"><img src="${window.Laravel.assetUrls.fileIcon}" alt=""></a></p>`;
                    }

                    html += ` <div class="dropdown">
                         <button class="btn btn-secondary dropdown-toggle" type="button" id="dropdownMenuButton1"
                             data-bs-toggle="dropdown" aria-expanded="false">
                             <i class="fa-solid fa-ellipsis-vertical"></i>
                         </button>
                         <ul class="dropdown-menu" aria-labelledby="dropdownMenuButton1">
                             <li><a class="dropdown-item remove-chat" data-chat-id="${res.chat.id}" data-del-from="me">Remove For Me</a></li>
                                     <li><a class="dropdown-item remove-chat" data-chat-id="${res.chat.id}" data-del-from="everyone">Remove For Everyone</a></li>
                         </ul>
                     </div></div><div class="messageDetails"><div class="messageTime">${res.chat.created_at_formatted}</div>
                                 <div id="seen_${res.chat.id}">
                                 <i class="fas fa-check">
                                     </i>
                                 </div>
                                 </div></div>`;

                    if (res.chat_count > 0) {
                        $("#chat-container-" + receiver_id).append(html);
                        scrollChatToBottom(receiver_id);
                    } else {
                        $("#chat-container-" + receiver_id).html(html);
                    }

                    // Update the user list
                    var users = res.users;
                    $("#group-manage-" + sender_id).html("");
                    var new_html = "";
                    users.forEach((user) => {
                        // let timeZome = 'America/New_York';
                        let time_format_13 =
                            user.last_message && user.last_message.created_at
                                ? moment
                                      .tz(user.last_message.created_at)
                                      .format("hh:mm A")
                                : "";

                        new_html += `<li class="group user-list ${
                            user.id == receiver_id ? "active" : ""
                        }" data-id="${user.id}"><div class="avatar">`;

                        if (user.profile_picture) {
                            var profile_picture =
                                window.Laravel.storageUrl +
                                user.profile_picture;
                            new_html += `<img src="${profile_picture}" alt="">`;
                        } else {
                            new_html += `<img src="${window.Laravel.assetUrls.profileDummy}" alt="">`;
                        }

                        new_html += `</div><p class="GroupName">${
                            user.first_name
                        } ${user.middle_name ? user.middle_name : ""} ${
                            user.last_name ? user.last_name : ""
                        }</p><p class="GroupDescrp last-chat-${
                            user.last_message ? user.last_message.id : ""
                        }">${
                            user.last_message && user.last_message.message
                                ? user.last_message.message
                                : ""
                        }</p><div class="time_online" id="last-chat-time-${
                            user.last_message ? user.last_message.id : ""
                        }"><p>${time_format_13}</p></div></li>`;
                    });

                    $("#group-manage-" + sender_id).append(new_html);
                    $("#loading").removeClass("loading");
                    $("#loading-content").removeClass("loading-content");

                    socket.emit("chat", {
                        message: file.name,
                        file_url: fileUrl,
                        sender_id: sender_id,
                        receiver_id: receiver_id,
                        receiver_users: res.receiver_users,
                        chat_id: res.chat.id,
                        time: res.chat.created_at_formatted,
                        created_at: res.chat.new_created_at,
                    });
                } else {
                    $("#loading").removeClass("loading");
                    $("#loading-content").removeClass("loading-content");
                    console.log(res.msg);
                }
            },
        });
    });

    function setChatListLastActive(activeid = 0) {
        if (activeid != 0) {
            var lastActiveUserId = $("#last_activate_user").val();
        } else {
            var lastActiveUserId = $("#last_activate_user").val();
        }

        if (lastActiveUserId && lastActiveUserId != "0") {
            $("#chat_list_user_" + lastActiveUserId).addClass("active");
            // alert('active set done');
        }
    }

    // load left chat list

    function load_chat_list() {
        var lastActiveUserId = $("#last_activate_user").val();
        $.ajax({
            type: "GET",
            url: window.Laravel.routes.chatList,
            data: {
                _token: $("input[name=_token]").val(),
            },
            success: function (res) {
                if (res) {
                    $(".main-sidebar-chat-list").html(res);

                    setChatListLastActive(lastActiveUserId);
                } else {
                    console.log(res.msg);
                }
            },
        });
    }

    // clear-chat

    $(document).on("click", ".clear-chat", function (e) {
        var receiver_id = $(this).data("reciver-id");
        r = confirm("Are you sure you want to clear chat?");
        if (r == false) {
            return false;
        } else {
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

                        //socket.emit("clear-chat", {
                        //   receiver_id: receiver_id,
                        // sender_id: sender_id,
                        // });
                    } else {
                        console.log(res.msg);
                    }
                },
            });
        }
    });

    //remove-chat
    $(document).on("click", ".remove-chat", function (e) {
        var chat_id = $(this).data("chat-id");
        var del_from = $(this).data("del-from");
        r = confirm("Are you sure you want to remove chat?");
        if (r == false) {
            return false;
        } else {
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
                        if (del_from == "me") {
                            $("#chat-message-" + chat_id).remove();
                            $("#last-chat-time-" + chat_id).remove();
                            $(".last-chat-" + chat_id).html("");
                        } else {
                            $("#chat-message-" + chat_id).remove();
                            $("#last-chat-time-" + chat_id).remove();
                            $(".last-chat-" + chat_id).html("");

                            socket.emit("remove-chat", {
                                chat: res.chat,
                            });

                            //   socket.emit('chat', res.chat.message);
                        }
                    } else {
                        console.log(res.msg);
                    }
                },
            });
        }
    });

    //remove-chat
    socket.on("remove-chat", function (data) {
        // console.log(data);
        if (data.chat.reciver_id == sender_id) {
            $("#chat-message-" + data.chat.id).remove();
            $("#last-chat-time-" + data.chat.id).remove();
            $(".last-chat-" + data.chat.id).html("");
        }
        load_chat_list();
    });

    // clear-chat
    socket.on("clear-chat", function (data) {
        if (data.reciver_id == sender_id) {
            $("#chat-container-" + data.sender_id).html("");
            $("#message-app-" + data.sender_id).html("");
            load_chat_list();
        }
    });

    socket.on("read-chat", function (data) {
        load_chat_list();
    });

    function getNotificationReadUrl(type, id) {
        return window.Laravel.routes.notificationRead
            .replace("__TYPE__", type)
            .replace("__ID__", id);
    }

    // Listen for incoming chat messages from the server
    socket.on("chat", function (data) {
        let timeZome = window.Laravel.authTimeZone;

        console.log(timeZome);

        load_chat_list();
        setChatListLastActive();
        html = `
                         <div class="message you" id="chat-message-${data.chat_id}">
                             <p class="messageContent">`;

        let attachment = data.file_url;
        if (attachment != "") {
            let attachement_extention = attachment.split(".").pop();

            if (["jpg", "jpeg", "png", "gif"].includes(attachement_extention)) {
                html += ` <a href="${
                    data.file_url
                }" target="_blank"><img src="${
                    data.file_url
                }" alt="attachment" style="max-width: 200px; max-height: 200px;"></a><br><span class="">${data.message.replace(
                    /\n/g,
                    "<br>"
                )}</span>`;
            } else if (["mp4", "webm", "ogg"].includes(attachement_extention)) {
                html += ` <a href="${
                    data.file_url
                }" target="_blank"><video width="200" height="200" controls><source src="${
                    data.file_url
                }" type="video/mp4"><source src="${
                    data.file_url
                }" type="video/webm"><source src="${
                    data.file_url
                }" type="video/ogg"></video></a><br><span class="">${data.message.replace(
                    /\n/g,
                    "<br>"
                )}</span>`;
            } else {
                html += ` <a href="${data.file_url}" download="${
                    data.message
                }"><img src="${
                    window.Laravel.assetUrls.fileIcon
                }" alt=""></a><br><span class="">${data.message.replace(
                    /\n/g,
                    "<br>"
                )}</span>`;
            }
        } else {
            html += ` ${data.message.replace(/\n/g, "<br>")}`;
        }
        console.log(moment.tz(data.created_at).format("hh:mm A"));
        html += `</p>
                        <div class="messageDetails">
                                 <div class="messageTime">${moment
                                     .tz(data.created_at, timeZome)
                                     .format("hh:mm A")}</div>
                             </div>
                         </div>
                     `;
        if (data.receiver_id == sender_id) {
            if ($(".chat-module").length > 0) {
                if ($("#chat-container-" + data.sender_id).length > 0) {
                    $("#chat-container-" + data.sender_id).append(html);
                    scrollChatToBottom(data.sender_id);
                    // remove unseen count
                    $("#count-unseen-" + data.sender_id).remove();
                    // seen message
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
                            } else {
                                console.log(res.msg);
                            }
                        },
                    });
                }
            }

            $("#message-app-" + data.sender_id).html(data.message);
            var users = data.receiver_users;
            $("#group-manage-" + sender_id).html("");
            var new_html = "";
        }

        if (data.receiver_id == sender_id) {
            if ($(".chat-module").length > 0) {
                if ($("#chat-container-" + data.sender_id).length > 0) {
                    $("#count-unseen-" + data.sender_id).remove();
                    $.ajax({
                        type: "POST",
                        url: window.Laravel.routes.chatNotification,
                        data: {
                            _token: $("input[name=_token]").val(),
                            user_id: sender_id,
                            sender_id: data.sender_id, // sender_id
                            chat_id: data.chat_id,
                            is_delete: true,
                        },
                        success: function (res) {},
                    });
                } else {
                    $.ajax({
                        type: "POST",
                        url: window.Laravel.routes.chatNotification,
                        data: {
                            _token: $("input[name=_token]").val(),
                            user_id: sender_id,
                            sender_id: data.sender_id, // sender_id
                            chat_id: data.chat_id,
                        },
                        success: function (res) {
                            console.log("go");

                            if (res.status == true) {
                                $("#show-notification-count-" + sender_id).html(
                                    res.notification_count
                                );
                                var route = getNotificationReadUrl(
                                    "Chat",
                                    res.notification.id
                                );
                                var html = `<li>
                                                 <a href="${route}" class="top-text-block">
                                                     <div class="top-text-heading">${
                                                         res.notification
                                                             .message
                                                     }</div>
                                                     <div class="top-text-light">${moment(
                                                         res.notification
                                                             .created_at
                                                     ).fromNow()}</div>
                                                 </a>
                                             </li>`;
                                $("#show-notification-" + sender_id).prepend(
                                    html
                                ); // Use prepend to add new notification at the top
                            } else {
                                console.log(res.msg);
                            }
                        },
                    });
                }
            } else {
                $.ajax({
                    type: "POST",
                    url: window.Laravel.routes.chatNotification,
                    data: {
                        _token: $("input[name=_token]").val(),
                        user_id: sender_id,
                        sender_id: data.sender_id, // sender_id
                        chat_id: data.chat_id,
                    },
                    success: function (res) {
                        console.log("yes");

                        if (res.status == true) {
                            $("#show-notification-count-" + sender_id).html(
                                res.notification_count
                            );
                            var route = getNotificationReadUrl(
                                "Chat",
                                res.notification.id
                            );
                            var html = `<li>
                                                 <a href="${route}" class="top-text-block">
                                                     <div class="top-text-heading">${
                                                         res.notification
                                                             .message
                                                     }</div>
                                                     <div class="top-text-light">${moment(
                                                         res.notification
                                                             .created_at
                                                     ).fromNow()}</div>
                                                 </a>
                                             </li>`;
                            $("#show-notification-" + sender_id).prepend(html); // Use prepend to add new notification at the top
                        } else {
                            console.log(res.msg);
                        }
                    },
                });
            }
        } else {
            $.ajax({
                type: "POST",
                url: window.Laravel.routes.chatNotification,
                data: {
                    _token: $("input[name=_token]").val(),
                    user_id: data.receiver_id,
                    sender_id: data.sender_id, // sender_id
                    chat_id: data.chat_id,
                },
                success: function (res) {
                    if (res.status == true) {
                        $(
                            "#show-notification-count-" +
                                res.notification.user_id
                        ).html(res.notification_count);
                        var route = getNotificationReadUrl(
                            "Chat",
                            res.notification.id
                        );
                        var html = `<li>
                                                 <a href="${route}" class="top-text-block">
                                                     <div class="top-text-heading">${
                                                         res.notification
                                                             .message
                                                     }</div>
                                                     <div class="top-text-light">${moment(
                                                         res.notification
                                                             .created_at
                                                     ).fromNow()}</div>
                                                 </a>
                                             </li>`;
                        $(
                            "#show-notification-" + res.notification.user_id
                        ).prepend(html); // Use prepend to add new notification at the top
                    }
                },
            });
        }
        // load_chat_list();
    });

    // seen message
    socket.on("seen", function (data) {
        if (sender_id == data.last_chat.sender_id) {
            $("#seen_" + data.last_chat.id).html(
                '<i class="fas fa-check-double"></i>'
            );
            load_chat_list();
        }
    });

    //multiple_seen
    socket.on("multiple_seen", function (data) {
        data.unseen_chat.forEach(function (chat) {
            if (sender_id == chat.sender_id) {
                $("#seen_" + chat.id).html(
                    '<i class="fas fa-check-double"></i>'
                );
            }
        });
        load_chat_list();
    });
});
