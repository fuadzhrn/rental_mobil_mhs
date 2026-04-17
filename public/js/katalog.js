document.addEventListener("DOMContentLoaded", () => {
    const navLinks = Array.from(document.querySelectorAll(".main-nav a"));
    const sortSelect = document.querySelector("#urutkan");
    const resultCount = document.querySelector(".sorting-bar p strong");

    navLinks.forEach((link) => {
        link.addEventListener("click", () => {
            navLinks.forEach((item) => item.classList.remove("is-active"));
            link.classList.add("is-active");
        });
    });

    if (sortSelect && resultCount) {
        sortSelect.addEventListener("change", () => {
            resultCount.textContent = "12 kendaraan";
        });
    }
});
