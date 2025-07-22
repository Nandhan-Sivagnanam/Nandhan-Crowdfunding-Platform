//.......... Dropdown for User Dashboard ....//

function setupUserMenu() {
    const userName = document.querySelector(".user-name");
    const dropdown = document.querySelector("#userDropdown");

    if (!userName || !dropdown) return; // Stop if elements don't exist

    // Toggle dropdown visibility
    function toggleDropdown(event) {
        event.stopPropagation(); // Prevents event from bubbling up
        dropdown.classList.toggle("show");
    }

    // Close dropdown if clicked outside
    function closeDropdown(event) {
        if (!userName.contains(event.target) && !dropdown.contains(event.target)) {
            dropdown.classList.remove("show");
        }
    }

    // Remove previous listeners (avoids duplicates)
    userName.removeEventListener("click", toggleDropdown);
    document.removeEventListener("click", closeDropdown);

    // Attach event listeners
    userName.addEventListener("click", toggleDropdown);
    document.addEventListener("click", closeDropdown);
}

// Run the function after the DOM loads
document.addEventListener("DOMContentLoaded", setupUserMenu);
