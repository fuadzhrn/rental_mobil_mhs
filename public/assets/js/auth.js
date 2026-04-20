document.addEventListener("DOMContentLoaded", function () {
    var toggleButtons = document.querySelectorAll(".toggle-password");

    toggleButtons.forEach(function (button) {
        button.addEventListener("click", function () {
            var targetId = button.getAttribute("data-target");
            var input = document.getElementById(targetId);

            if (!input) {
                return;
            }

            var isHidden = input.type === "password";
            input.type = isHidden ? "text" : "password";
            button.textContent = isHidden ? "Sembunyi" : "Lihat";
        });
    });
});
