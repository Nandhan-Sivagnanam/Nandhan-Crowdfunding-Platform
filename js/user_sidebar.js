document.addEventListener("DOMContentLoaded", function () {
    const sidebarLinks = document.querySelectorAll(".sidebar ul li a");
    const defaultPage = "user_profile.php"; // Default first page
    let lastVisitedPage = localStorage.getItem("lastVisitedPage") || defaultPage;
    
    // Set active class on last visited page
    sidebarLinks.forEach(link => {
        if (link.getAttribute("href") === lastVisitedPage) {
            link.classList.add("active");
        }

        // Add click event to update last visited page and set active class
        link.addEventListener("click", function () {
            localStorage.setItem("lastVisitedPage", this.getAttribute("href"));

            // Remove active class from all links
            sidebarLinks.forEach(l => l.classList.remove("active"));

            // Add active class to clicked link
            this.classList.add("active");
        });
    });

    // Redirect to last visited page if not already there
    if (!window.location.href.includes(lastVisitedPage)) {
        window.location.href = lastVisitedPage;
    }
});
