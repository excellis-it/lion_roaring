$(document).ready(function () {
    $.ajaxSetup({
        headers: {
            "X-CSRF-TOKEN": $('meta[name="csrf-token"]').attr("content"),
        },
    });
    let ip_address = window.Laravel.ipAddress;
    let socket_port = "3000";
    let socket = io(ip_address + ":" + socket_port);
    var sender_id = window.Laravel.authUserId;
    var role = window.Laravel.authUserRole;
    var groupDefaultImage = window.Laravel.assetUrls.groupDefaultImage;
    var storageUrl = window.Laravel.storageUrl;
    var fileIcon = window.Laravel.assetUrls.fileIcon;
    var profileDummy = window.Laravel.assetUrls.profileDummy;
    var csrfToken = window.Laravel.csrfToken;
    var authTimeZone = window.Laravel.authTimeZone;

    $("#create-team").submit(function (e) {
        e.preventDefault();
        var form = $(this);
        var url = form.attr("action");
        $("#loading").addClass("loading");
        $("#loading-content").addClass("loading-content");
        $.ajax({
            type: "POST",
            url: url,
            data: new FormData(this),
            processData: false,
            contentType: false,
            success: function (resp) {
                toastr.success(resp.message);
                var data = resp.team;
                var group_image = data.group_image;

                groupList(sender_id);
                // reset form
                $("#create-team")[0].reset();

                $("#previewImage01").attr("src", groupDefaultImage);
                $("#exampleModalToggle").modal("hide");

                $("#loading").removeClass("loading");
                $("#loading-content").removeClass("loading-content");
                // Send message to socket
                socket.emit("createTeam", {
                    user_id: sender_id,
                    chat_member_id: resp.chat_member_id,
                });
            },
            error: function (xhr) {
                $("#loading").removeClass("loading");
                $("#loading-content").removeClass("loading-content");
                $(".text-danger").html("");
                var errors = xhr.responseJSON.errors;
                $.each(errors, function (key, value) {
                    if (key.includes(".")) {
                        var fieldName = key.split(".")[0];
                        // Display errors for array fields
                        var num = key.match(/\d+/)[0];
                        toastr.error(value[0]);
                    } else {
                        // after text danger span
                        toastr.error(value[0]);
                    }
                });
            },
        });
    });

    function loadChat(teamId) {
        $.ajax({
            type: "POST",
            url: window.Laravel.routes.teamChatLoad,
            data: {
                team_id: teamId,
                _token: csrfToken,
            },
            success: function (resp) {
                $(".chat-body").html(resp.view);
                scrollChatToBottom(teamId);
                // remove unseen count
                $("#count-team-unseen-" + teamId).html(``);

                // Initialize EmojiOneArea on MessageInput
                var emojioneAreaInstance = $("#TeamMessageInput").emojioneArea({
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

                        if (event.key === "Enter") {
                            //
                        } else {
                            if (event.which === 13 && !event.shiftKey) {
                                event.preventDefault();
                                $("#TeamMessageForm").submit();
                            }
                        }
                    }
                );

                // scrollChatToBottom(teamId);
                getSidebarNotiCounts();
            },
            error: function (xhr) {
                toastr.error("Something went wrong");
            },
        });
    }

    $(document).on("click", ".group-data", function () {
        var teamId = $(this).data("id");
        loadChat(teamId);
        $(this).addClass("active").siblings().removeClass("active");
    });

    function scrollChatToBottom(team_id) {
        var messages = document.getElementById(
            "team-chat-container-" + team_id
        );
        if (messages) {
            messages.scrollTop = messages.scrollHeight;
        } else {
            console.error(
                "Element with ID 'team-chat-container-" +
                    team_id +
                    "' not found."
            );
        }
    }

    $(document).on("change", "#team-file", function (e) {
        var file = e.target.files[0];
        var team_id = $(this).data("team-id");
        var formData = new FormData();
        formData.append("file", file);
        formData.append("_token", csrfToken);
        formData.append("team_id", team_id);

        $.ajax({
            type: "POST",
            url: window.Laravel.routes.teamChatSend,
            data: formData,
            processData: false,
            contentType: false,
            success: function (res) {
                getSidebarNotiCounts();
                if (res.status == true) {
                    // groupList(sender_id);
                    let attachment = res.chat.attachment;
                    let fileUrl = storageUrl + attachment;
                    let attachement_extention = attachment.split(".").pop();
                    let created_at = res.chat.created_at;
                    let time_format_12 = moment
                        .tz(created_at, authTimeZone)
                        .format("hh:mm A");
                    let html = `<div class="message me" id="team-chat-message-${res.chat.id}">`;
                    if (
                        ["jpg", "jpeg", "png", "gif"].includes(
                            attachement_extention
                        )
                    ) {
                        html += `<div class="message-wrap"><p class="messageContent"><a href="${fileUrl}" target="_blank"><img src="${fileUrl}" alt="attachment" style="max-width: 200px; max-height: 200px;"></a></p>`;
                    } else if (
                        ["mp4", "webm", "ogg"].includes(attachement_extention)
                    ) {
                        html += ` <div class="message-wrap"><p class="messageContent"><a href="${fileUrl}" target="_blank"><video width="200" height="200" controls><source src="${fileUrl}" type="video/mp4"><source src="${fileUrl}" type="video/webm"><source src="${fileUrl}" type="video/ogg"></video></a></p>`;
                    } else {
                        html += `<div class="message-wrap"><p class="messageContent"><a href="${fileUrl}" download="${attachment}"><img src="${fileIcon}" alt=""></a></p>`;
                    }

                    html += `<div class="dropdown">
                                <button class="btn btn-secondary dropdown-toggle" type="button" id="dropdownMenuButton1"
                                    data-bs-toggle="dropdown" aria-expanded="false">
                                    <i class="fa-solid fa-ellipsis-vertical"></i>
                                </button>
                                <ul class="dropdown-menu" aria-labelledby="dropdownMenuButton1">
                                    <li><a class="dropdown-item team-remove-chat" data-chat-id="${res.chat.id}" data-del-from="me" data-team-id="${res.chat.team_id}">Remove For Me</a></li>
                                            <li><a class="dropdown-item team-remove-chat" data-chat-id="${res.chat.id}" data-del-from="everyone" data-team-id="${res.chat.team_id}">Remove For Everyone</a></li>
                                </ul>
                            </div></div><div class="messageDetails"><div class="messageTime">${time_format_12}</div></div></div>`;
                    $("#team-chat-container-" + team_id).append(html);
                    scrollChatToBottom(team_id);

                    // Send message to socket
                    socket.emit("sendTeamMessage", {
                        chat: res.chat,
                        file_url: fileUrl,
                        chat_member_id: res.chat_member_id,
                        created_at: res.chat.new_created_at,
                        time: res.created_at_formatted,
                    });
                    getSidebarNotiCounts();
                } else {
                    console.log(res.msg);
                }
            },
        });
    });

    $(document).on("submit", "#TeamMessageForm", function (e) {
        e.preventDefault();

        // Get the message from the input field emoji area
        var message = $("#TeamMessageInput")
            .emojioneArea()[0]
            .emojioneArea.getText();
        var team_id = $(".team_id").val();
        var url = window.Laravel.routes.teamChatSend;

        // Get the file data
        var fileInput = $("#team-file2")[0];
        var file = fileInput.files[0]; // The selected file

        // Create a FormData object to send both message and file
        var formData = new FormData();
        formData.append("_token", $("input[name=_token]").val());
        formData.append("message", message);
        formData.append("team_id", team_id);

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

        $.ajax({
            type: "POST",
            url: url,
            data: formData,
            processData: false, // Don't process the data
            contentType: false,
            success: function (resp) {
                loadChat($("#team_id").val());
                getSidebarNotiCounts();

                $("#TeamMessageInput")
                    .emojioneArea()[0]
                    .emojioneArea.setText("");
                $("#team-file2").val("");
                $("#file-name-display").hide();
                let created_at = resp.chat.created_at;
                let time = moment.tz(created_at, authTimeZone).format("h:mm A");

                // append new message to the chat
                var data = resp.chat;
                //  groupList(sender_id, data.team_id);
                let html = `<div class="message me" id="team-chat-message-${data.id}">
                <div class="message-wrap">`;

                let fileUrl = "";
                let attachment = data.attachment;

                if (attachment && attachment !== "") {
                    fileUrl = storageUrl + attachment;
                    let attachement_extention = attachment.split(".").pop();

                    if (
                        ["jpg", "jpeg", "png", "gif"].includes(
                            attachement_extention
                        )
                    ) {
                        html += `<p class="messageContent">
                    <a href="${fileUrl}" target="_blank">
                        <img src="${fileUrl}" alt="attachment" style="max-width: 200px; max-height: 200px;">
                    </a><br>
                    <span>${data.message.replace(/\n/g, "<br>")}</span>
                 </p>`;
                    } else if (
                        ["mp4", "webm", "ogg"].includes(attachement_extention)
                    ) {
                        html += `<p class="messageContent">
                    <a href="${fileUrl}" target="_blank">
                        <video width="200" height="200" controls>
                            <source src="${fileUrl}" type="video/mp4">
                            <source src="${fileUrl}" type="video/webm">
                            <source src="${fileUrl}" type="video/ogg">
                        </video>
                    </a><br>
                    <span>${data.message.replace(/\n/g, "<br>")}</span>
                 </p>`;
                    } else {
                        html += `<p class="messageContent">
                    <a href="${fileUrl}" download="${attachment}">
                        <img src="${fileIcon}" alt="file">
                    </a><br>
                    <span>${data.message.replace(/\n/g, "<br>")}</span>
                 </p>`;
                    }
                } else {
                    html += `<p class="messageContent">${data.message.replace(
                        /\n/g,
                        "<br>"
                    )}</p>`;
                }

                html += `<div class="messageDetails">
                                        <div class="messageTime">${time}</div>
                                    </div>
                                </div>
                                <div class="dropdown">
                                <button class="btn btn-secondary dropdown-toggle" type="button" id="dropdownMenuButton1"
                                    data-bs-toggle="dropdown" aria-expanded="false">
                                    <i class="fa-solid fa-ellipsis-vertical"></i>
                                </button>
                                <ul class="dropdown-menu" aria-labelledby="dropdownMenuButton1">
                                    <li><a class="dropdown-item team-remove-chat" data-chat-id="${data.id}" data-del-from="me" data-team-id="${data.team_id}">Remove For Me</a></li>
                                            <li><a class="dropdown-item team-remove-chat" data-chat-id="${data.id}" data-del-from="everyone" data-team-id="${data.team_id}">Remove For Everyone</a></li>
                                </ul>
                            </div></div>
                                    `;
                $("#team-chat-container-" + data.team_id).append(html);

                scrollChatToBottom(data.team_id);
                $("#loading").removeClass("loading");
                $("#loading-content").removeClass("loading-content");
                // Send message to socket
                socket.emit("sendTeamMessage", {
                    chat: data,
                    chat_member_id: resp.chat_member_id,
                    created_at: resp.chat.new_created_at,
                    time: resp.created_at_formatted,
                });
                getSidebarNotiCounts();
            },
            error: function (xhr) {
                toastr.error("Something went wrong");
            },
        });
    });

    $(document).on("submit", "#name-des-update", function (e) {
        e.preventDefault();
        var form = $(this);
        var url = form.attr("action");
        $("#loading").addClass("loading");
        $("#loading-content").addClass("loading-content");
        $.ajax({
            type: "POST",
            url: url,
            data: new FormData(this),
            processData: false,
            contentType: false,
            success: function (resp) {
                getSidebarNotiCounts();
                $("#loading").removeClass("loading");
                $("#loading-content").removeClass("loading-content");
                toastr.success(resp.message);
                $(".group-name-" + resp.team_id).html(resp.name);
                $(".group-des-" + resp.team_id).html(resp.description);
                $("#exampleModalToggle3").modal("hide");
                $("#groupInfo").modal("show");
            },
            error: function (xhr) {
                $("#loading").removeClass("loading");
                $("#loading-content").removeClass("loading-content");
                $(".text-danger").html("");
                var errors = xhr.responseJSON.errors;
                $.each(errors, function (key, value) {
                    if (key.includes(".")) {
                        var fieldName = key.split(".")[0];
                        // Display errors for array fields
                        var num = key.match(/\d+/)[0];
                        toastr.error(value[0]);
                    } else {
                        // after text danger span
                        toastr.error(value[0]);
                    }
                });
            },
        });
    });

    $(document).on("click", ".group-info", function () {
        var team_id = $(this).data("team-id");
        groupDetails(team_id);
    });

    $(document).on("click", ".back-to-group-info", function () {
        $("#exampleModalToggle3").modal("hide");
        var team_id = $(this).data("team-id");
        groupDetails(team_id);
    });
    // back-to-group-info-one
    $(document).on("click", ".back-to-group-info-one", function () {
        $("#exampleModalToggle2").modal("hide");
        var team_id = $(this).data("team-id");
        groupDetails(team_id);
    });

    function groupDetails(team_id) {
        $("#loading").addClass("loading");
        $("#loading-content").addClass("loading-content");
        $.ajax({
            type: "POST",
            url: window.Laravel.routes.teamChatGroupInfo,
            data: {
                team_id: team_id,
                _token: csrfToken,
            },
            success: function (resp) {
                // model open
                getSidebarNotiCounts();
                $("#group-information").html(resp.view);
                $("#groupInfo").modal("show");
                $("#loading").removeClass("loading");
                $("#loading-content").removeClass("loading-content");
            },
            error: function (xhr) {
                toastr.error("Something went wrong");
            },
        });
    }

    $(document).on("change", ".team-profile-picture", function () {
        var team_id = $(this).data("team-id");
        var file = $(this).prop("files")[0];
        var formData = new FormData();
        formData.append("group_image", file);
        formData.append("team_id", team_id);
        formData.append("_token", csrfToken);
        $("#loading").addClass("loading");
        $("#loading-content").addClass("loading-content");
        $.ajax({
            type: "POST",
            url: window.Laravel.routes.teamChatUpdateGroupImage,
            data: formData,
            processData: false,
            contentType: false,
            success: function (resp) {
                getSidebarNotiCounts();
                if (resp.status == true) {
                    var group_image = resp.group_image;
                    var group_image_url = storageUrl + group_image;
                    $(".team-image-" + team_id).html(
                        `<img src="${storageUrl}${group_image}" alt="">`
                    );

                    $("#loading").removeClass("loading");
                    $("#loading-content").removeClass("loading-content");
                    toastr.success(resp.message);

                    socket.emit("updateGroupImage", {
                        team_id: team_id,
                        group_image: group_image_url,
                    });
                } else {
                    toastr.error(resp.message);
                }
            },
            error: function (xhr) {
                $(".text-danger").html("");
                var errors = xhr.responseJSON.errors;
                $.each(errors, function (key, value) {
                    if (key.includes(".")) {
                        var fieldName = key.split(".")[0];
                        // Display errors for array fields
                        var num = key.match(/\d+/)[0];
                        toastr.error(value[0]);
                    } else {
                        // after text danger span
                        toastr.error(value[0]);
                    }
                });
            },
        });
    });

    $(document).on("click", ".edit-name-des", function () {
        var team_id = $(this).data("team-id");
        $("#loading").addClass("loading");
        $("#loading-content").addClass("loading-content");
        $.ajax({
            type: "POST",
            url: window.Laravel.routes.teamChatEditNameDes,
            data: {
                team_id: team_id,
                _token: csrfToken,
            },
            success: function (resp) {
                getSidebarNotiCounts();
                $("#change-group-details").html(resp.view);
                $("#groupInfo").modal("hide");
                $("#exampleModalToggle3").modal("show");
                $("#loading").removeClass("loading");
                $("#loading-content").removeClass("loading-content");
            },
            error: function (xhr) {
                toastr.error("Something went wrong");
            },
        });
    });

    $(document).on("click", ".remove-member-from-group", function () {
        var team_id = $(this).data("team-id");
        var user_id = $(this).data("user-id");
        var r = confirm("Are you sure you want to remove this member?");
        if (r == true) {
            $.ajax({
                type: "POST",
                url: window.Laravel.routes.teamChatRemoveMember,
                data: {
                    team_id: team_id,
                    user_id: user_id,
                    _token: csrfToken,
                },
                success: function (resp) {
                    getSidebarNotiCounts();
                    if (resp.status == true) {
                        $("#groupInfo").modal("hide");
                        loadChat(team_id);
                        toastr.success(resp.message);
                        $("#group-member-" + team_id + "-" + user_id).remove();

                        // socket emit
                        socket.emit("removeMemberFromGroup", {
                            team_id: team_id,
                            user_id: user_id,
                            sender_id: sender_id,
                            notification: resp.notification,
                        });

                        socket.emit("sendTeamMessage", {
                            chat: resp.chat,
                            chat_member_id: resp.chat_member_id,
                            created_at: resp.chat.new_created_at,
                        });
                        getSidebarNotiCounts();
                    } else {
                        toastr.error(resp.message);
                    }
                },
                error: function (xhr) {
                    toastr.error("Something went wrong");
                },
            });
        } else {
            return false;
        }
    });

    // make-admin
    $(document).on("click", ".make-admin", function () {
        var team_id = $(this).data("team-id");
        var user_id = $(this).data("user-id");
        var r = confirm("Are you sure you want to make this member admin?");
        if (r == true) {
            $.ajax({
                type: "POST",
                url: window.Laravel.routes.teamChatMakeAdmin,
                data: {
                    team_id: team_id,
                    user_id: user_id,
                    _token: csrfToken,
                },
                success: function (resp) {
                    getSidebarNotiCounts();
                    if (resp.status == true) {
                        toastr.success(resp.message);
                        $("#show-permission-" + team_id + "-" + user_id).html(
                            ` <span class="admin_name">Admin</span>`
                        );

                        // socket emit sendAdminNotification
                        socket.emit("sendAdminNotification", {
                            team_id: team_id,
                            user_id: user_id,
                            sender_id: sender_id,
                            notification: resp.notification,
                        });
                    } else {
                        toastr.error(resp.message);
                    }
                },
                error: function (xhr) {
                    toastr.error("Something went wrong");
                },
            });
        } else {
            return false;
        }
    });

    function groupList(user_id, team_id = null) {
        $.ajax({
            type: "POST",
            url: window.Laravel.routes.teamChatGroupList,
            data: {
                user_id: user_id,
                team_id: team_id,
                _token: csrfToken,
            },
            success: function (resp) {
                $(".group-list").html(resp.view);
                getSidebarNotiCounts();
            },
            error: function (xhr) {
                toastr.error("Something went wrong");
            },
        });
    }

    $(document).on("click", ".exit-from-group", function () {
        var team_id = $(this).data("team-id");
        var r = confirm("Are you sure you want to exit from this group?");
        if (r == true) {
            $.ajax({
                type: "POST",
                url: window.Laravel.routes.teamChatExitFromGroup,
                data: {
                    team_id: team_id,
                    _token: csrfToken,
                },
                success: function (resp) {
                    getSidebarNotiCounts();
                    if (resp.status == true) {
                        toastr.success(resp.message);
                        if (resp.team_delete == true) {
                            groupList(sender_id);
                            $(
                                "#group-member-" +
                                    resp.team_id +
                                    "-" +
                                    resp.user_id
                            ).remove();
                            $("#groupInfo").modal("hide");
                            html = `<div class="icon_chat">
                                        <span><img src="${window.Laravel.assetUrls.profileDummy.replace(
                                            "profile_dummy.png",
                                            "icon-chat.png"
                                        )}" alt=""></span>
                                        <h4>Seamless Real-Time Chat | Connect Instantly</h4>
                                        <p>Join our dynamic chat platform, where real-time communication is effortless. Engage in private and group
                                            conversations, manage your contacts, and stay connected with instant updates. Experience a secure and
                                            responsive interface, perfect for personal or professional use.</p>
                                    </div>`;
                            $(".chat-body").html(html);
                        } else {
                            loadChat(team_id);
                            $(
                                "#group-member-" +
                                    resp.team_id +
                                    "-" +
                                    resp.user_id
                            ).remove();
                            $("#groupInfo").modal("hide");
                        }

                        // socket emit
                        socket.emit("exitFromGroup", {
                            team_id: team_id,
                            user_id: resp.user_id,
                            team_member_name: resp.team_member_name,
                            team_delete: resp.team_delete,
                            team_member_id: resp.team_member_id,
                        });
                    } else {
                        toastr.error(resp.message);
                    }
                },
                error: function (xhr) {
                    toastr.error("Something went wrong");
                },
            });
        } else {
            return false;
        }
    });

    //add-member-team form submit
    $(document).on("submit", "#add-member-team", function (e) {
        e.preventDefault();
        var form = $(this);
        var url = form.attr("action");
        $.ajax({
            type: "POST",
            url: url,
            data: new FormData(this),
            processData: false,
            contentType: false,
            success: function (resp) {
                getSidebarNotiCounts();
                toastr.success(resp.message);
                $("#add-member-team")[0].reset();
                $("#exampleModalToggle2").modal("hide");
                loadChat(resp.team_id);
                // socket emit
                socket.emit("sendTeamMessage", {
                    chat: resp.chat,
                    chat_member_id: resp.chat_member_id,
                    created_at: resp.chat.new_created_at,
                });

                socket.emit("addMemberToGroup", {
                    team_id: resp.team_id,
                    user_id: resp.user_id,
                    team_member_name: resp.team_member_name,
                    chat_member_id: resp.chat_member_id,
                    already_member_arr: resp.already_member_arr,
                    only_added_members: resp.only_added_members,
                });

                getSidebarNotiCounts();
            },
            error: function (xhr) {
                $(".text-danger").html("");
                var errors = xhr.responseJSON.errors;
                $.each(errors, function (key, value) {
                    if (key.includes(".")) {
                        var fieldName = key.split(".")[0];
                        // Display errors for array fields
                        var num = key.match(/\d+/)[0];
                        toastr.error(value[0]);
                    } else {
                        // after text danger span
                        toastr.error(value[0]);
                    }
                });
            },
        });
    });

    // delete-group
    $(document).on("click", ".delete-group", function () {
        var team_id = $(this).data("team-id");
        var r = confirm("Are you sure you want to delete this group?");
        if (r == true) {
            $.ajax({
                type: "POST",
                url: window.Laravel.routes.teamChatDeleteGroup,
                data: {
                    team_id: team_id,
                    _token: csrfToken,
                },
                success: function (resp) {
                    getSidebarNotiCounts();
                    if (resp.status == true) {
                        toastr.success(resp.message);
                        groupList(sender_id);
                        html = `<div class="icon_chat">
                                        <span><img src="${window.Laravel.assetUrls.profileDummy.replace(
                                            "profile_dummy.png",
                                            "icon-chat.png"
                                        )}" alt=""></span>
                                        <h4>Seamless Real-Time Chat | Connect Instantly</h4>
                                        <p>Join our dynamic chat platform, where real-time communication is effortless. Engage in private and group
                                            conversations, manage your contacts, and stay connected with instant updates. Experience a secure and
                                            responsive interface, perfect for personal or professional use.</p>
                                    </div>`;
                        $(".chat-body").html(html);

                        // socket emit
                        socket.emit("deleteGroup", {
                            team_id: resp.team_id,
                            user_id: sender_id,
                            team_member_id: resp.team_member_id,
                        });
                    } else {
                        toastr.error(resp.message);
                    }
                },
                error: function (xhr) {
                    toastr.error("Something went wrong");
                },
            });
        } else {
            return false;
        }
    });

    // team-remove-chat
    $(document).on("click", ".team-remove-chat", function () {
        var chat_id = $(this).data("chat-id");
        var del_from = $(this).data("del-from");
        var team_id = $(this).data("team-id");
        var r = confirm("Are you sure you want to delete this message?");
        if (r == true) {
            $.ajax({
                type: "POST",
                url: window.Laravel.routes.teamChatRemoveChat,
                data: {
                    chat_id: chat_id,
                    del_from: del_from,
                    team_id: team_id,
                    _token: csrfToken,
                },
                success: function (resp) {
                    getSidebarNotiCounts();
                    if (resp.status == true) {
                        if (del_from == "me") {
                            $("#team-chat-message-" + chat_id).remove();
                            if (resp.last_message == true) {
                                $("#team-last-chat-time-" + chat_id).remove();
                                $(".team-last-chat-" + chat_id).html("");
                            }
                        } else {
                            $("#team-chat-message-" + chat_id).remove();
                            if (resp.last_message == true) {
                                $("#team-last-chat-time-" + chat_id).remove();
                                $(".team-last-chat-" + chat_id).html("");
                            }

                            socket.emit("team-remove-chat", {
                                chat_id: chat_id,
                                last_message: resp.last_message,
                                team_id: team_id,
                                last_message_data: resp.last_message_data,
                                past_message_data: resp.past_message_data,
                            });
                        }
                    } else {
                        toastr.error(resp.message);
                    }
                },
                error: function (xhr) {
                    toastr.error("Something went wrong");
                },
            });
        } else {
            return false;
        }
    });

    $(document).on("click", ".clear-all-conversation", function () {
        var teamId = $(this).data("team-id");
        r = confirm("Are you sure you want to clear all conversation?");
        if (r == true) {
            $.ajax({
                url: window.Laravel.routes.teamChatClearAllConversation,
                type: "POST",
                data: {
                    team_id: teamId,
                    _token: csrfToken,
                },
                success: function (response) {
                    getSidebarNotiCounts();
                    if (response.status == true) {
                        $("#team-chat-container-" + teamId).html("");
                        groupList(sender_id);
                        toastr.success(response.message);

                        // socket emit
                        socket.emit("clearAllConversation", {
                            team_id: teamId,
                            user_id: sender_id,
                        });
                    }
                },
            });
        }
    });

    // clearAllConversation
    socket.on("clearAllConversation", function (data) {
        getSidebarNotiCounts();
        if (data.user_id != sender_id) {
            $("#team-chat-container-" + data.team_id).html("");
            groupList(sender_id);
        }
    });

    // team-remove-chat
    socket.on("team-remove-chat", function (data) {
        getSidebarNotiCounts();
        $("#team-chat-message-" + data.chat_id).remove();
        if (data.last_message == true) {
            $("#team-last-chat-time-" + data.chat_id).remove();
            $(".team-last-chat-" + data.chat_id).html("");
        }
    });

    // deleteGroup

    socket.on("deleteGroup", function (data) {
        getSidebarNotiCounts();
        if (
            data.user_id != sender_id &&
            data.team_member_id.includes(sender_id)
        ) {
            groupList(sender_id);
            html = `<div class="icon_chat">
                                        <span><img src="${window.Laravel.assetUrls.profileDummy.replace(
                                            "profile_dummy.png",
                                            "icon-chat.png"
                                        )}" alt=""></span>
                                        <h4>Seamless Real-Time Chat | Connect Instantly</h4>
                                        <p>Join our dynamic chat platform, where real-time communication is effortless. Engage in private and group
                                            conversations, manage your contacts, and stay connected with instant updates. Experience a secure and
                                            responsive interface, perfect for personal or professional use.</p>
                                    </div>`;
            $(".chat-body").html(html);
        }
    });

    // addMemberToGroup
    socket.on("addMemberToGroup", function (data) {
        getSidebarNotiCounts();
        if (
            data.user_id != sender_id &&
            data.chat_member_id.includes(sender_id)
        ) {
            $("#all-member-" + data.team_id).html(
                data.team_member_name.length > 60
                    ? data.team_member_name.substring(0, 60) + "..."
                    : data.team_member_name
            );

            groupList(sender_id);
        }

        if (data.only_added_members.includes(sender_id)) {
            //  get count notification
            var count = $("#show-notification-count-" + sender_id).text();
            count = parseInt(count);
            count += 1;
            $("#show-notification-count-" + sender_id).text(count);
        }

        if (data.already_member_arr.includes(sender_id)) {
            html = `  <form id="TeamMessageForm">
            <input type="file" id="file" style="display: none" data-team-id="${data.team_id}">
            <div class="file-upload">
                <label for="file">
                    <svg xmlns="http://www.w3.org/2000/svg" width="24" height="24" fill="none"
                        viewBox="0 0 24 24">
                        <path fill="currentColor" fill-rule="evenodd"
                            d="M9 7a5 5 0 0 1 10 0v8a7 7 0 1 1-14 0V9a1 1 0 0 1 2 0v6a5 5 0 0 0 10 0V7a3 3 0 1 0-6 0v8a1 1 0 1 0 2 0V9a1 1 0 1 1 2 0v6a3 3 0 1 1-6 0z"
                            clip-rule="evenodd" style="color:black"></path>
                    </svg>
                </label>
            </div>
            <input type="text" id="TeamMessageInput" placeholder="Type a message...">
            <input type="hidden" id="team_id" value="${data.team_id}" class="team_id">
            <div>
                <button class="Send">
                    <svg width="22" height="22" viewBox="0 0 22 22" fill="none"
                        xmlns="http://www.w3.org/2000/svg">
                        <path
                            d="M0.82186 0.827412C0.565716 0.299519 0.781391 0.0763349 1.32445 0.339839L20.6267 9.70604C21.1614 9.96588 21.1578 10.4246 20.6421 10.7179L1.6422 21.526C1.11646 21.8265 0.873349 21.6115 1.09713 21.0513L4.71389 12.0364L15.467 10.2952L4.77368 8.9726L0.82186 0.827412Z"
                            fill="white" />
                    </svg>
                </button>
            </div>
        </form>`;
            $("#group-member-form-" + data.team_id + "-" + sender_id).html(
                html
            );
        }
    });

    // exitFromGroup
    socket.on("exitFromGroup", function (data) {
        getSidebarNotiCounts();
        if (data.user_id != sender_id) {
            $("#group-member-" + data.team_id + "-" + data.user_id).remove();
            $("#all-member-" + data.team_id).html(
                data.team_member_name.length > 60
                    ? data.team_member_name.substring(0, 60) + "..."
                    : data.team_member_name
            );
        }

        if (
            data.team_delete == true &&
            data.team_member_id.includes(sender_id) &&
            data.user_id != sender_id
        ) {
            groupList(sender_id);
            if ($("#team-chat-container-" + data.team_id).length > 0) {
                html = `<div class="icon_chat">
                                        <span><img src="${window.Laravel.assetUrls.profileDummy.replace(
                                            "profile_dummy.png",
                                            "icon-chat.png"
                                        )}" alt=""></span>
                                        <h4>Seamless Real-Time Chat | Connect Instantly</h4>
                                        <p>Join our dynamic chat platform, where real-time communication is effortless. Engage in private and group
                                            conversations, manage your contacts, and stay connected with instant updates. Experience a secure and
                                            responsive interface, perfect for personal or professional use.</p>
                                    </div>`;
                $(".chat-body").html(html);
            }
        }
    });

    socket.on("createTeam", function (data) {
        getSidebarNotiCounts();
        if (
            sender_id != data.user_id &&
            data.chat_member_id.includes(sender_id)
        ) {
            // get count notification
            var count = $("#show-notification-count-" + sender_id).text();
            count = parseInt(count);
            count += 1;
            $("#show-notification-count-" + sender_id).text(count);
            groupList(sender_id);
        }
    });

    socket.on("removeMemberFromGroup", function (data) {
        getSidebarNotiCounts();
        if (data.user_id == sender_id) {
            var notification = data.notification;
            var count = $("#show-notification-count-" + sender_id).text();
            count = parseInt(count);
            count += 1;
            $("#show-notification-count-" + sender_id).text(count);
            var route = window.Laravel.routes.notificationRead
                .replace("__TYPE__", "Team")
                .replace("__ID__", data.notification.id);
            var html = `<li>
                                    <a href="${route}" class="top-text-block">
                                        <div class="top-text-heading">${
                                            data.notification.message
                                        }</div>
                                        <div class="top-text-light">${moment(
                                            data.notification.created_at
                                        ).fromNow()}</div>
                                    </a>
                                </li>`;
            $("#show-notification-" + sender_id).prepend(html);
            loadChat(data.team_id);
        }

        if (data.sender_id != sender_id) {
            $("#group-member-" + data.team_id + "-" + data.user_id).remove();
        }
    });

    socket.on("updateGroupImage", function (data) {
        getSidebarNotiCounts();
        $(".team-image-" + data.team_id).html(
            `<img src="${data.group_image}" alt="">`
        );
    });

    // Updated UI update functions
    function updateGroupLastMessage(teamId, message, time, chatId) {
        // Update the last message text
        const messageElement = $(
            `.group-data[data-id="${teamId}"] .GroupDescrp`
        );
        messageElement.html(message);

        // Update the time element
        const timeElement = $(`.group-data[data-id="${teamId}"] .time_online`);
        timeElement.text(time);

        // Update the class and id for the message element to track the latest chat
        messageElement
            .removeClass()
            .addClass(`GroupDescrp team-last-chat-${chatId}`);
        timeElement.attr("id", `team-last-chat-time-${chatId}`);

        // Move group to top of list
        moveGroupToTop(teamId);
    }

    function updateUnseenCount(teamId, count = 0) {
        const unseenElement = $(`#count-team-unseen-${teamId}`);
        if (count > 0) {
            unseenElement.html(`<span><p>${count}</p></span>`);
        } else {
            unseenElement.html("");
        }
    }

    function moveGroupToTop(teamId) {
        const groupElement = $(`.group-data[data-id="${teamId}"]`);
        if (groupElement.length && groupElement.parent().length) {
            const parentContainer = groupElement.parent();
            parentContainer.prepend(groupElement);
        }
    }

    function incrementUnseenCount(teamId) {
        const unseenElement = $(`#count-team-unseen-${teamId}`);
        const currentText = unseenElement.find("p").text();
        const currentCount = parseInt(currentText) || 0;
        const newCount = currentCount + 1;
        unseenElement.html(`<span><p>${newCount}</p></span>`);
    }

    function getMessageDisplayText(chat) {
        if (chat.attachment && chat.attachment !== "") {
            return chat.message && chat.message.trim() !== ""
                ? chat.message
                : '<i class="fa-solid fa-file"></i> File uploaded';
        }
        return chat.message || "";
    }

    // Receive message from socket
    socket.on("sendTeamMessage", function (data) {
        let timezone = authTimeZone;
        let created_at = data.created_at;
        let time = moment.tz(created_at, timezone).format("h:mm A");
        let chat_member_id_array = data.chat_member_id;

        if (
            data.chat.user_id != sender_id &&
            chat_member_id_array.includes(sender_id)
        ) {
            let html = `
        <div class="message you" id="team-chat-message-${data.chat.id}">
            <div class="d-flex">
                <div class="member_image">
                    <span>`;
            if (data.chat.user.profile_picture) {
                html += `<img src="${storageUrl}${data.chat.user.profile_picture}" alt="">`;
            } else {
                html += `<img src="${profileDummy}" alt="">`;
            }

            html += `   </span>
                </div>
                <div class="message_group">
                    <p class="messageContent">
                        <span class="namemember">
    ${
        (data.chat.user.first_name ?? "") +
        " " +
        (data.chat.user.middle_name ?? "") +
        " " +
        (data.chat.user.last_name ?? "")
    }
</span>`;

            let fileUrl = "";
            let attachment = data.chat.attachment;
            if (attachment != "") {
                fileUrl = storageUrl + attachment;
                let attachement_extention = attachment.split(".").pop();

                if (
                    ["jpg", "jpeg", "png", "gif"].includes(
                        attachement_extention
                    )
                ) {
                    html += `<a href="${fileUrl}" target="_blank">
                        <img src="${fileUrl}" alt="attachment" style="max-width: 200px; max-height: 200px;">
                     </a><br><span class="">${data.chat.message.replace(
                         /\n/g,
                         "<br>"
                     )}</span>`;
                } else if (
                    ["mp4", "webm", "ogg"].includes(attachement_extention)
                ) {
                    html += `<a href="${fileUrl}" target="_blank">
                        <video width="200" height="200" controls>
                            <source src="${fileUrl}" type="video/mp4">
                            <source src="${fileUrl}" type="video/webm">
                            <source src="${fileUrl}" type="video/ogg">
                        </video>
                     </a><<br><span class="">${data.chat.message.replace(
                         /\n/g,
                         "<br>"
                     )}</span>`;
                } else {
                    html += `<a href="${fileUrl}" download="${attachment}">
                        <img src="${fileIcon}" alt="">
                     </a><br><span class="">${data.chat.message.replace(
                         /\n/g,
                         "<br>"
                     )}</span>`;
                }
            } else {
                html += `${data.chat.message.replace(/\n/g, "<br>")}`;
            }

            html += `</p>
                    <div class="messageDetails">
                        <div class="messageTime">${time}</div>
                    </div>
                </div>
            </div>
        </div>`;

            $("#team-chat-container-" + data.chat.team_id).append(html);
            scrollChatToBottom(data.chat.team_id);
        }

        if (
            data.chat.user_id != sender_id &&
            chat_member_id_array.includes(sender_id)
        ) {
            // Update group list UI instead of API call
            let messageText = getMessageDisplayText(data.chat);
            updateGroupLastMessage(
                data.chat.team_id,
                messageText,
                time,
                data.chat.id
            );

            if ($(".chat-body").length > 0) {
                if ($("#team-chat-container-" + data.chat.team_id).length > 0) {
                    // User is viewing this team's chat - mark as seen
                    updateUnseenCount(data.chat.team_id, 0);
                    $.ajax({
                        type: "POST",
                        url: window.Laravel.routes.teamChatSeen,
                        data: {
                            chat_id: data.chat.id,
                            _token: csrfToken,
                        },
                        success: function (res) {
                            getSidebarNotiCounts();
                            if (res.status == true) {
                                // socket.emit('teamSeenChat', {
                                //     last_chat: data.chat
                                // });
                            } else {
                                console.log(res.msg);
                            }
                        },
                    });
                    $.ajax({
                        type: "POST",
                        url: window.Laravel.routes.teamChatNotification,
                        data: {
                            user_id: sender_id,
                            team_id: data.chat.team_id,
                            chat_id: data.chat.id,
                            is_delete: 1,
                            _token: csrfToken,
                        },
                        success: function (res) {
                            console.log(res);
                        },
                    });
                } else {
                    // User is not viewing this team's chat - increment unseen count
                    incrementUnseenCount(data.chat.team_id);
                    $.ajax({
                        type: "POST",
                        url: window.Laravel.routes.teamChatNotification,
                        data: {
                            user_id: sender_id,
                            team_id: data.chat.team_id,
                            chat_id: data.chat.id,
                            _token: csrfToken,
                        },
                        success: function (res) {
                            if (res.status == true) {
                                $("#show-notification-count-" + sender_id).html(
                                    res.notification_count
                                );
                                var route =
                                    window.Laravel.routes.notificationRead
                                        .replace("__TYPE__", "Team")
                                        .replace("__ID__", res.notification.id);
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
                            }
                        },
                    });
                }
            } else {
                // User is not in chat page - increment unseen count
                incrementUnseenCount(data.chat.team_id);
                $.ajax({
                    type: "POST",
                    url: window.Laravel.routes.teamChatNotification,
                    data: {
                        user_id: sender_id,
                        team_id: data.chat.team_id,
                        chat_id: data.chat.id,
                        _token: csrfToken,
                    },
                    success: function (res) {
                        getSidebarNotiCounts();
                        if (res.status == true) {
                            $("#show-notification-count-" + sender_id).html(
                                res.notification_count
                            );
                            var route = window.Laravel.routes.notificationRead
                                .replace("__TYPE__", "Team")
                                .replace("__ID__", res.notification.id);
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
        }

        // Also update for sender's own messages
        if (data.chat.user_id == sender_id) {
            let messageText = getMessageDisplayText(data.chat);
            updateGroupLastMessage(
                data.chat.team_id,
                messageText,
                time,
                data.chat.id
            );
        }
        getSidebarNotiCounts();
    });
});
