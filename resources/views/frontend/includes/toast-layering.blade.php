<style>
    :root {
        --lr-floating-chat-bottom: 30px;
        --lr-floating-chat-right: 30px;
        --lr-floating-chat-size: 60px;
        --lr-floating-chat-toast-gap: 12px;
    }

    /* BUG-004: toastr must stack above floating chat widgets and other fixed UI */
    #toast-container {
        position: fixed !important;
        z-index: 2147483646 !important;
        pointer-events: none;
    }

    #toast-container > div {
        opacity: 1 !important;
        pointer-events: auto;
    }

    /* Sit above the floating chat button instead of overlapping it */
    body.has-floating-chat #toast-container.toast-bottom-right {
        bottom: calc(
            var(--lr-floating-chat-bottom) + var(--lr-floating-chat-size) + var(--lr-floating-chat-toast-gap)
        ) !important;
        right: var(--lr-floating-chat-right) !important;
    }
</style>
