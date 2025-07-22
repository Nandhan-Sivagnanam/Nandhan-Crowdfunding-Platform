document.addEventListener("DOMContentLoaded", function () {
    const userMenu = document.querySelector(".user-menu");
    const dropdownMenu = document.getElementById("adminDropdown");

    if (!userMenu || !dropdownMenu) return; // Ensure elements exist

    // Toggle dropdown on click
    userMenu.addEventListener("click", function (event) {
        dropdownMenu.classList.toggle("show"); // Use 'show' class instead of 'hidden'
        event.stopPropagation();
    });

    // Close dropdown when clicking outside
    document.addEventListener("click", function (event) {
        if (!userMenu.contains(event.target) && !dropdownMenu.contains(event.target)) {
            dropdownMenu.classList.remove("show");
        }
    });

    // Close dropdown with Escape key
    document.addEventListener("keydown", function (event) {
        if (event.key === "Escape") {
            dropdownMenu.classList.remove("show");
        }
    });
});
