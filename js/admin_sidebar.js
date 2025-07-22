document.addEventListener("DOMContentLoaded", function () {
    const sidebarLinks = document.querySelectorAll(".sidebar ul li a");

    // ✅ Force "dashboard.php" after logout
    if (!sessionStorage.getItem("loggedIn")) {
        sessionStorage.clear(); // Reset stored data
        sessionStorage.setItem("activePage", "dashboard.php");
        sessionStorage.setItem("loggedIn", "true"); // Mark session as active
    }

    // ✅ Get last active page or default to "dashboard.php"
    let currentPage = sessionStorage.getItem("activePage") || "dashboard.php";

    // ✅ Function to update sidebar highlighting
    function updateActiveSidebar() {
        sidebarLinks.forEach(link => {
            let page = link.getAttribute("href").split("/").pop();
            link.classList.toggle("active", page === currentPage);
        });
    }

    updateActiveSidebar(); // Apply active class on page load

    // ✅ Prevent flickering: Redirect **only if needed**
    let currentPath = window.location.pathname.split("/").pop();
    if (currentPath !== currentPage && currentPath !== "logout.php") {
        window.location.replace(currentPage);
    }

    // ✅ Click event to update sessionStorage and navigate
    sidebarLinks.forEach(link => {
        link.addEventListener("click", function (event) {
            event.preventDefault(); // Stop full-page reload
            let newPage = this.getAttribute("href").split("/").pop();
            sessionStorage.setItem("activePage", newPage);
            updateActiveSidebar();
            window.location.href = newPage; // Navigate
        });
    });
});
