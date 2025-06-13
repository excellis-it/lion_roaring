$(document).ready(function () {
    $("#chatbotBtn").click(function () {
        $("#chatbox").fadeIn();
    });

    $("#closeChatbox").click(function () {
        $("#chatbox").fadeOut();
    });

    $("#chatbotsendBtn").click(function () {
        var message = $("#chatbotuserInput").val().trim();
        if (message !== "") {
            var userMessage = $(
                '<div class="chatbot-message chatbot-user-message"><p>' +
                    message +
                    "</p></div>"
            );
            $("#chatboxBody").append(userMessage);
            $("#chatbotuserInput").val("");

            $.ajax({
                type: "POST",
                url: window.Laravel.routes.chatbotMessage,
                data: {
                    _token: window.Laravel.csrfToken,
                    message: message,
                },
                dataType: "json",
                success: function (response) {
                    var dataMessage = response.message;

                    setTimeout(function () {
                        var botMessage = $(
                            '<div class="chatbot-message chatbot-bot-message d-block"><p>' +
                                dataMessage +
                                '</p></div><span class="chatbot-bot-message-lable"> - Lion Roaring AI</span>'
                        );
                        $("#chatboxBody").append(botMessage);
                        $("#chatboxBody").scrollTop(
                            $("#chatboxBody")[0].scrollHeight
                        ); // Auto scroll to the bottom
                    }, 500);
                },
            });
        }
    });
});
