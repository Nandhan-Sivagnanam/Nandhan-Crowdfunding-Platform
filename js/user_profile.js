document.addEventListener("DOMContentLoaded", function () {
    const editBtn = document.getElementById("editProfileBtn");
    const modal = document.getElementById("profileModal");
    const closeBtn = document.querySelector(".close");
    const cancelBtn = document.getElementById("cancelEdit");
    const successMessage = document.getElementById("successMessage");

    if (editBtn) {
        editBtn.addEventListener("click", () => {
            modal.style.display = "flex";
        });
    }

    if (closeBtn) {
        closeBtn.addEventListener("click", () => {
            modal.style.display = "none";
        });
    }

    window.addEventListener("click", (event) => {
        if (event.target === modal) {
            modal.style.display = "none";
        }
    });

    if (cancelBtn) {
        cancelBtn.addEventListener("click", () => {
            modal.style.display = "none";
        });
    }

    // Hide success message after 2 seconds
    if (successMessage) {
        setTimeout(() => {
            successMessage.style.display = "none";
        }, 2000);
    }
});
