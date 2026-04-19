document.addEventListener("DOMContentLoaded", () => {
    const body = document.body;
    const toggleButton = document.querySelector(".nav-toggle");
    const overlay = document.querySelector("[data-mobile-nav-overlay]");
    const closeButton = document.querySelector("[data-mobile-nav-close]");
    const panel = document.querySelector(".mobile-nav-panel");
    const menuLinks = Array.from(document.querySelectorAll(".mobile-nav-links a"));
    const mobileQuery = window.matchMedia("(max-width: 1024px)");

    if (!toggleButton || !overlay || !closeButton || !panel) {
        return;
    }

    if (!mobileQuery.matches) {
        return;
    }

    const openMenu = () => {
        body.classList.add("mobile-nav-open");
        toggleButton.setAttribute("aria-expanded", "true");
        overlay.removeAttribute("hidden");
        panel.removeAttribute("hidden");
        panel.setAttribute("aria-hidden", "false");
    };

    const closeMenu = () => {
        body.classList.remove("mobile-nav-open");
        toggleButton.setAttribute("aria-expanded", "false");
        overlay.setAttribute("hidden", "hidden");
        panel.setAttribute("hidden", "hidden");
        panel.setAttribute("aria-hidden", "true");
    };

    const toggleMenu = () => {
        if (body.classList.contains("mobile-nav-open")) {
            closeMenu();
            return;
        }

        openMenu();
    };

    if (mobileQuery.matches) {
        toggleButton.removeAttribute("hidden");
        overlay.setAttribute("hidden", "hidden");
        panel.setAttribute("hidden", "hidden");
    }

    toggleButton.addEventListener("click", toggleMenu);
    overlay.addEventListener("click", closeMenu);
    closeButton.addEventListener("click", closeMenu);

    menuLinks.forEach((link) => {
        link.addEventListener("click", () => {
            closeMenu();
        });
    });

    document.addEventListener("keydown", (event) => {
        if (event.key === "Escape") {
            closeMenu();
        }
    });
});
