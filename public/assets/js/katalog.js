document.addEventListener("DOMContentLoaded", () => {
    const sortSelect = document.querySelector("#urutkan");
    const resultCount = document.querySelector(".sorting-bar p strong");
    const filterOpenButton = document.querySelector("[data-filter-open]");
    const filterCloseButton = document.querySelector("[data-filter-close]");
    const filterOverlay = document.querySelector("[data-filter-overlay]");
    const filterPanel = document.querySelector(".catalog-filter");
    const mobileQuery = window.matchMedia("(max-width: 1024px)");

    if (sortSelect && resultCount) {
        sortSelect.addEventListener("change", () => {
            resultCount.textContent = "12 kendaraan";
        });
    }

    if (!filterOpenButton || !filterCloseButton || !filterOverlay || !filterPanel || !mobileQuery.matches) {
        return;
    }

    const openFilter = () => {
        document.body.classList.add("catalog-filter-open");
        filterOverlay.removeAttribute("hidden");
        filterPanel.removeAttribute("hidden");
        filterPanel.setAttribute("aria-hidden", "false");
    };

    const closeFilter = () => {
        document.body.classList.remove("catalog-filter-open");
        filterOverlay.setAttribute("hidden", "hidden");
        filterPanel.setAttribute("hidden", "hidden");
        filterPanel.setAttribute("aria-hidden", "true");
    };

    filterOpenButton.addEventListener("click", openFilter);
    filterCloseButton.addEventListener("click", closeFilter);
    filterOverlay.addEventListener("click", closeFilter);

    document.addEventListener("keydown", (event) => {
        if (event.key === "Escape") {
            closeFilter();
        }
    });

    const filterInputs = Array.from(filterPanel.querySelectorAll("input, select, button"));
    filterInputs.forEach((input) => {
        input.addEventListener("keydown", (event) => {
            if (event.key === "Escape") {
                closeFilter();
            }
        });
    });
});
