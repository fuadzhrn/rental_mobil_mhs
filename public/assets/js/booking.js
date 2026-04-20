document.addEventListener("DOMContentLoaded", () => {
    const activeRadio = document.querySelectorAll('.radio-pill input[type="radio"]');

    activeRadio.forEach((radio) => {
        radio.addEventListener("change", () => {
            document.querySelectorAll(".radio-pill").forEach((label) => {
                label.classList.remove("is-active");
            });

            if (radio.checked && radio.closest(".radio-pill")) {
                radio.closest(".radio-pill").classList.add("is-active");
            }
        });

        if (radio.checked && radio.closest(".radio-pill")) {
            radio.closest(".radio-pill").classList.add("is-active");
        }
    });
});
