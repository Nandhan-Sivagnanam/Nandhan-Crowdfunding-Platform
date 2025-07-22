console.log("d_board.js loaded!"); // This should appear in the console

document.addEventListener("DOMContentLoaded", function () {
    setupSidebar();
    setupAdminMenu();
    setupSectionNavigation();
});

// ✅ Sidebar Navigation Logic
function setupSidebar() {
    const sidebarLinks = document.querySelectorAll(".sidebar ul li a");

    // Get stored active page from sessionStorage (default to "dashboard")
    let activePage = sessionStorage.getItem("activePage") || "dashboard";

    // Function to update active sidebar link
    function updateActiveSidebar(newPage) {
        sidebarLinks.forEach(link => link.parentElement.classList.remove("active"));
        sidebarLinks.forEach(link => {
            if (link.href.includes(`?page=${newPage}`)) {
                link.parentElement.classList.add("active");
            }
        });
    }

    // Apply active class on page load
    updateActiveSidebar(activePage);

    // Add click event listeners to update content & sidebar instantly
    sidebarLinks.forEach(link => {
        link.addEventListener("click", function (event) {
            event.preventDefault(); // Prevent full page reload

            const newPage = new URL(this.href).searchParams.get("page");

            if (newPage !== activePage) {
                sessionStorage.setItem("activePage", newPage);
                updateActiveSidebar(newPage);
                loadPageContent(newPage);
            }
        });
    });
}

// ✅ Run after the page loads
document.addEventListener("DOMContentLoaded", () => {
    let storedPage = sessionStorage.getItem("activePage") || "dashboard";
    loadPageContent(storedPage);
    setupSidebar();
});
//.......... dropdown ....//

function setupAdminMenu() {
    const adminName = document.querySelector(".admin-name");
    const dropdown = document.querySelector("#adminDropdown");

    if (!adminName || !dropdown) return; // Stop if elements don't exist

    // Toggle dropdown visibility
    function toggleDropdown(event) {
        event.stopPropagation(); // Prevents event from bubbling up
        dropdown.classList.toggle("show");
    }

    // Close dropdown if clicked outside
    function closeDropdown(event) {
        if (!adminName.contains(event.target) && !dropdown.contains(event.target)) {
            dropdown.classList.remove("show");
        }
    }

    // Remove previous listeners (avoids duplicates)
    adminName.removeEventListener("click", toggleDropdown);
    document.removeEventListener("click", closeDropdown);

    // Attach event listeners
    adminName.addEventListener("click", toggleDropdown);
    document.addEventListener("click", closeDropdown);
}

// Run on first load
document.addEventListener("DOMContentLoaded", setupAdminMenu);

// Run again when content updates dynamically (e.g., user page loads)
document.addEventListener("click", function (event) {
    if (event.target.closest(".sidebar ul li a")) {
        setTimeout(setupAdminMenu, 100); // Delay ensures content is fully loaded
    }
});


//.........

// ✅ Section Navigation Handling
function setupSectionNavigation() {
    const sections = ["dashboardSection", "usersSection", "campaignsSection"];
    let activeSection = localStorage.getItem("activeSection") || "dashboardSection";

    showSection(activeSection); // Show last active section

    document.querySelectorAll(".sidebar ul li a").forEach(link => {
        link.addEventListener("click", function (event) {
            event.preventDefault();
            const sectionId = this.id.replace("Nav", "Section");
            showSection(sectionId);
        });
    });
}

// ✅ Show Section & Hide Others
function showSection(sectionId) {
    const sections = ["dashboardSection", "usersSection", "campaignsSection"];
    sections.forEach(section => {
        const sectionElement = document.getElementById(section);
        if (sectionElement) {
            sectionElement.style.display = section === sectionId ? "block" : "none";
        }
    });

    localStorage.setItem("activeSection", sectionId);

    // ✅ Reinitialize sidebar and header menu
    setupSidebar();
    setupAdminMenu();
}
