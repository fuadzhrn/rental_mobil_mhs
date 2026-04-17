document.addEventListener("DOMContentLoaded", () => {
    const mainImage = document.querySelector("#mainVehicleImage");
    const thumbs = Array.from(document.querySelectorAll(".gallery-thumbnails .thumb"));

    thumbs.forEach((thumb) => {
        thumb.addEventListener("click", () => {
            const imageUrl = thumb.dataset.image;
            if (mainImage && imageUrl) {
                mainImage.src = imageUrl;
            }

            thumbs.forEach((item) => item.classList.remove("is-active"));
            thumb.classList.add("is-active");
        });
    });
});
