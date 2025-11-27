$(document).ready(function () {
    // Helper: Renders FAQ list (limit to first `limit` items) and removes existing FAQ elements
    function renderFaqList(faqs, limit = 8) {
        if (!Array.isArray(faqs) || faqs.length === 0) return;
        var showFaqs = faqs.slice(0, limit);
        // Remove any existing loaded faq list to avoid duplicates
        $("#chatboxBody .chatbot-faq-list").remove();
        var faqHtml = '<div class="chatbot-faq-list">';
        faqHtml +=
            '<div class="chatbot-message chatbot-bot-message"><p>Here are some common questions:</p></div>';
        faqHtml += '<div class="chatbot-faq-items">';
        showFaqs.forEach(function (faq) {
            faqHtml +=
                '<button type="button" class="chatbot-faq-item btn btn-sm btn-outline-secondary text-start m-1" data-question="' +
                faq.question.replace(/"/g, "&quot;") +
                '">' +
                faq.question +
                "</button>";
        });
        faqHtml += "</div></div>";
        $("#chatboxBody").append(faqHtml);
    }
    $("#chatbotBtn").click(function () {
        $("#chatbox").fadeIn();
        if (
            !window._faqsLoaded ||
            $("#chatboxBody .chatbot-faq-list").length === 0
        ) {
            window._faqsLoaded = true;
            $.get(window.Laravel.routes.chatbotFaqs, function (resp) {
                if (resp.status && resp.data && resp.data.length > 0) {
                    renderFaqList(resp.data);
                }
            });
        }
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

                        // If server returned FAQs with the not-understood message, show them
                        if (
                            response.faqs &&
                            Array.isArray(response.faqs) &&
                            response.faqs.length > 0
                        ) {
                            renderFaqList(response.faqs);
                            $("#chatboxBody").scrollTop(
                                $("#chatboxBody")[0].scrollHeight
                            );
                        }
                    }, 500);
                },
            });
        }
    });

    // allow Enter key to send
    $("#chatbotuserInput").keypress(function (e) {
        if (e.which === 13) {
            e.preventDefault();
            $("#chatbotsendBtn").click();
        }
    });

    // Hide FAQ suggestions when the user starts typing or focuses the input
    $(document).on("input focus", "#chatbotuserInput", function () {
        $("#chatboxBody .chatbot-faq-list").slideUp(100, function () {
            $(this).remove();
        });
    });

    // Click handler for FAQ items added dynamically
    $(document).on("click", ".chatbot-faq-item", function () {
        var question = $(this).data("question");
        // Hide faq list immediately
        $("#chatboxBody .chatbot-faq-list").slideUp(120, function () {
            $(this).remove();
        });
        if (!question) return;
        // Add user message (simulate user asked this question)
        var userMessage = $(
            '<div class="chatbot-message chatbot-user-message"><p>' +
                question +
                "</p></div>"
        );
        $("#chatboxBody").append(userMessage);
        // Add loading placeholder while we fetch an answer
        var loadingMessage = $(
            '<div class="chatbot-message chatbot-bot-message chatbot-loading"><p>Loading answer...</p></div>'
        );
        $("#chatboxBody").append(loadingMessage);
        $("#chatboxBody").scrollTop($("#chatboxBody")[0].scrollHeight);
        // Post to existing chatbot endpoint to get the answer
        $.ajax({
            type: "POST",
            url: window.Laravel.routes.chatbotMessage,
            data: {
                _token: window.Laravel.csrfToken,
                message: question,
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
                    loadingMessage.replaceWith(botMessage);
                    $("#chatboxBody").scrollTop(
                        $("#chatboxBody")[0].scrollHeight
                    );
                }, 400);
            },
        });
    });
});
