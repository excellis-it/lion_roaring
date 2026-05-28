document.addEventListener("DOMContentLoaded", function () {
    document.querySelectorAll("select.cst-select").forEach(function (selectEl) {
        const isDisabled = selectEl.disabled;
        const wrapper = document.createElement("div");
        wrapper.classList.add("cst-select-wrapper");
        wrapper.style.position = "relative";
        wrapper.style.display = "inline-block";
        wrapper.style.fontFamily = "Arial, sans-serif";

        // visible display
        const display = document.createElement("div");
        display.classList.add("cst-select-display");
        display.style.border = "1px solid #ccc";
        display.style.padding = "6px 10px";
        display.style.borderRadius = "5px";
        display.style.cursor = isDisabled ? "not-allowed" : "pointer";
        display.style.display = "flex";
        display.style.alignItems = "center";
        display.style.justifyContent = "space-between";
        display.style.minWidth = "180px";
        display.style.background = isDisabled ? "#e9ecef" : "#fff";
        display.style.gap = "6px";
        if (isDisabled) display.style.opacity = "0.7";

        const content = document.createElement("div");
        content.classList.add("cst-select-content");
        content.style.display = "flex";
        content.style.alignItems = "center";
        content.style.gap = "6px";
        display.appendChild(content);

        const arrow = document.createElement("span");
        arrow.classList.add("cst-select-arrow");
        arrow.textContent = "▼";
        arrow.style.fontSize = "12px";
        arrow.style.color = "#555";
        display.appendChild(arrow);

        // dropdown container
        const list = document.createElement("div");
        list.classList.add("cst-select-list");
        list.style.position = "absolute";
        list.style.left = "0";
        list.style.right = "0";
        list.style.border = "1px solid #ccc";
        list.style.borderRadius = "5px";
        list.style.background = "#fff";
        list.style.zIndex = "9999";
        list.style.display = "none";
        // Safari often auto-hides scrollbars; keep scroll functionality in an inner area
        // so the bottom hint can stay pinned and not scroll away.
        list.style.overflow = "hidden";
        list.style.boxShadow = "0 4px 10px rgba(0,0,0,0.1)";
        list.style.boxSizing = "border-box";

        // direction classes
        const isTop = selectEl.classList.contains("cst-select-top");
        const isLeft = selectEl.classList.contains("cst-select-left");
        const isRight = selectEl.classList.contains("cst-select-right");
        if (isTop) list.style.bottom = "100%";
        else if (isLeft) {
            list.style.right = "100%";
            list.style.top = "0";
            list.style.width = "200px";
        } else if (isRight) {
            list.style.left = "100%";
            list.style.top = "0";
            list.style.width = "200px";
        } else list.style.top = "100%"; // default bottom

        // fixed search box
        const searchInput = document.createElement("input");
        searchInput.classList.add("cst-select-search");
        searchInput.type = "text";
        searchInput.placeholder = "Search...";
        searchInput.style.position = "absolute";
        searchInput.style.top = "0";
        searchInput.style.left = "0";
        searchInput.style.right = "0";
        searchInput.style.width = "calc(100% - 16px)";
        searchInput.style.margin = "4px 8px";
        searchInput.style.padding = "5px 8px";
        searchInput.style.border = "1px solid #ddd";
        searchInput.style.borderRadius = "4px";
        searchInput.style.fontSize = "13px";
        searchInput.style.background = "#fff";
        searchInput.style.zIndex = "10000";

        list.appendChild(searchInput);

        // hide original select
        selectEl.style.display = "none";
        selectEl.parentNode.insertBefore(wrapper, selectEl);
        wrapper.appendChild(selectEl);
        wrapper.appendChild(display);
        wrapper.appendChild(list);

        // scroll area (options) — this is the element that scrolls
        const scrollArea = document.createElement("div");
        scrollArea.classList.add("cst-select-scroll-area");
        scrollArea.style.maxHeight = "var(--cst-select-max-height, 240px)";
        scrollArea.style.overflowY = "auto";
        scrollArea.style.paddingTop = "38px"; // space for fixed search
        scrollArea.style.boxSizing = "border-box";
        list.appendChild(scrollArea);

        // option container
        const optionContainer = document.createElement("div");
        optionContainer.classList.add("cst-select-options");
        scrollArea.appendChild(optionContainer);

        // Scroll hint (helps Safari users discover more items)
        const scrollHint = document.createElement("div");
        scrollHint.classList.add("cst-select-scroll-hint");
        scrollHint.innerHTML =
            "<span>Scroll for more</span><span class='cst-select-scroll-hint-icon'>▼</span>";
        list.appendChild(scrollHint);

        // build options
        Array.from(selectEl.options).forEach(function (opt) {
            const item = document.createElement("div");
            item.style.padding = "6px 10px";
            item.style.cursor = isDisabled ? "not-allowed" : "pointer";
            item.style.display = "flex";
            item.style.alignItems = "center";
            item.style.gap = "6px";

            if (opt.dataset.image) {
                const img = document.createElement("img");
                img.src = opt.dataset.image;
                img.style.height = "18px";
                img.style.maxWidth = "24px";
                img.style.width = "auto";
                img.style.borderRadius = "2px";
                img.style.objectFit = "contain";
                item.appendChild(img);
            }

            const span = document.createElement("span");
            span.textContent = opt.text;
            span.style.flex = "1";
            item.appendChild(span);

            if (!isDisabled) {
                item.addEventListener(
                    "mouseover",
                    () => (item.style.background = "#f2f2f2")
                );
                item.addEventListener(
                    "mouseout",
                    () => (item.style.background = "")
                );
                item.addEventListener("click", function () {
                    selectEl.value = opt.value;
                    updateDisplay();
                    list.style.display = "none";
                    selectEl.dispatchEvent(new Event("change"));
                });
            }

            optionContainer.appendChild(item);
        });

        // update selected visible
        function updateDisplay() {
            const selectedOpt = selectEl.options[selectEl.selectedIndex];
            content.innerHTML = "";
            if (selectedOpt.dataset.image) {
                const img = document.createElement("img");
                img.src = selectedOpt.dataset.image;
                img.style.height = "18px";
                img.style.maxWidth = "24px";
                img.style.width = "auto";
                img.style.borderRadius = "2px";
                img.style.objectFit = "contain";
                content.appendChild(img);
            }
            const span = document.createElement("span");
            span.textContent = selectedOpt.text;
            content.appendChild(span);
        }
        updateDisplay();

        // toggle dropdown — only if NOT disabled
        display.addEventListener("click", function () {
            if (isDisabled) return;
            list.style.display =
                list.style.display === "none" ? "block" : "none";
            searchInput.focus();
            searchInput.value = "";
            filterOptions("");
            updateScrollHint();
        });

        // close dropdown on outside click
        document.addEventListener("click", function (e) {
            if (!wrapper.contains(e.target)) list.style.display = "none";
        });

        // filter options
        function filterOptions(keyword) {
            keyword = keyword.toLowerCase();
            optionContainer.querySelectorAll("div").forEach((opt) => {
                const text = opt.innerText.toLowerCase();
                opt.style.display = text.includes(keyword) ? "flex" : "none";
            });
            // After filtering, recompute scroll hint visibility
            updateScrollHint();
        }
        searchInput.addEventListener("input", (e) =>
            filterOptions(e.target.value)
        );

        function updateScrollHint() {
            // show hint only when list is open and there is more content below
            if (list.style.display === "none") {
                scrollHint.style.display = "none";
                return;
            }
            const hasScroll = scrollArea.scrollHeight > scrollArea.clientHeight + 2;
            const atBottom =
                Math.ceil(scrollArea.scrollTop + scrollArea.clientHeight) >=
                scrollArea.scrollHeight - 2;
            scrollHint.style.display = hasScroll && !atBottom ? "flex" : "none";
        }

        scrollArea.addEventListener("scroll", updateScrollHint);
    });
});
