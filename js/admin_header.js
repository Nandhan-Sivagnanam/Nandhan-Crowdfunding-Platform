document.addEventListener("DOMContentLoaded", function () {
    const adminName = document.querySelector("#adminDropdownToggle");
    const dropdown = document.querySelector("#adminDropdown");
    const changePasswordBtn = document.getElementById("changePasswordBtn");
    const modal = document.getElementById("changePasswordModal");
    const closeBtn = document.querySelector(".close");


    if (!adminName || !dropdown) {
        console.error("Dropdown elements not found!");
        return;
    }
    // Toggle dropdown visibility
    adminName.addEventListener("click", (event) => {
        event.stopPropagation(); // Prevent immediate closure
        dropdown.classList.toggle("show");

        // Ensure no other dropdown is open
        document.querySelectorAll(".dropdown-menu").forEach((menu) => {
            if (menu !== dropdown) {
                menu.classList.remove("show");
            }
        });
    });

    // Prevent dropdown from closing when clicking inside it
    dropdown.addEventListener("click", (event) => {
        event.stopPropagation();
    });

    // Close dropdown when clicking outside
    document.addEventListener("click", (event) => {
        if (!adminName.contains(event.target) && !dropdown.contains(event.target)) {
            dropdown.classList.remove("show");
        }
    });

    // Close dropdown on Escape key press
    document.addEventListener("keydown", (event) => {
        if (event.key === "Escape") {
            dropdown.classList.remove("show");
            if (modal) modal.classList.add("hidden");
        }
    });

    // Open change password modal
    if (changePasswordBtn && modal) {
        changePasswordBtn.addEventListener("click", (event) => {
            event.preventDefault();
            modal.classList.remove("hidden");
        });
    }

    // Close modal when clicking on close button
    if (closeBtn && modal) {
        closeBtn.addEventListener("click", () => {
            modal.classList.add("hidden");
        });
    }

    // Close modal when clicking outside the modal content
    window.addEventListener("click", (event) => {
        if (modal && event.target === modal) {
            modal.classList.add("hidden");
        }
    });

    // Ensure dropdown does not auto-close on refresh
    window.addEventListener("load", () => {
        dropdown.classList.remove("show");
        if (modal) modal.classList.add("hidden");
    });
});
