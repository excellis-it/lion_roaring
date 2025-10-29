document.addEventListener("DOMContentLoaded", function () {
    document.querySelectorAll("select.cst-select").forEach(function (selectEl) {
        const wrapper = document.createElement("div");
        wrapper.style.position = "relative";
        wrapper.style.display = "inline-block";
        wrapper.style.fontFamily = "Arial, sans-serif";

        // visible display
        const display = document.createElement("div");
        display.style.border = "1px solid #ccc";
        display.style.padding = "6px 10px";
        display.style.borderRadius = "5px";
        display.style.cursor = "pointer";
        display.style.display = "flex";
        display.style.alignItems = "center";
        display.style.justifyContent = "space-between";
        display.style.minWidth = "180px";
        display.style.background = "#fff";
        display.style.gap = "6px";

        const content = document.createElement("div");
        content.style.display = "flex";
        content.style.alignItems = "center";
        content.style.gap = "6px";
        display.appendChild(content);

        const arrow = document.createElement("span");
        arrow.textContent = "▼";
        arrow.style.fontSize = "12px";
        arrow.style.color = "#555";
        display.appendChild(arrow);

        // dropdown container
        const list = document.createElement("div");
        list.style.position = "absolute";
        list.style.left = "0";
        list.style.right = "0";
        list.style.border = "1px solid #ccc";
        list.style.borderRadius = "5px";
        list.style.background = "#fff";
        list.style.zIndex = "9999";
        list.style.display = "none";
        list.style.maxHeight = "240px";
        list.style.overflowY = "auto";
        list.style.boxShadow = "0 4px 10px rgba(0,0,0,0.1)";
        list.style.paddingTop = "38px"; // space for fixed search
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

        // option container
        const optionContainer = document.createElement("div");
        list.appendChild(optionContainer);

        // build options
        Array.from(selectEl.options).forEach(function (opt) {
            const item = document.createElement("div");
            item.style.padding = "6px 10px";
            item.style.cursor = "pointer";
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

        // toggle dropdown
        display.addEventListener("click", function () {
            list.style.display =
                list.style.display === "none" ? "block" : "none";
            searchInput.focus();
            searchInput.value = "";
            filterOptions("");
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
        }
        searchInput.addEventListener("input", (e) =>
            filterOptions(e.target.value)
        );
    });
});
