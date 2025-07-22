document.addEventListener("DOMContentLoaded", function () {
    const searchBox = document.getElementById("searchBox");
    const tableBody = document.querySelector("#campaignTable tbody");
    const addCampaignBtn = document.querySelector(".add-campaign-btn");
    const addCampaignSection = document.getElementById("addCampaignSection");
    const editCampaignSection = document.getElementById("editCampaignSection");
    const campaignTable = document.getElementById("campaignTable");
    const headerContainer = document.querySelector(".header-container");
    const addCampaignForm = document.getElementById("addCampaignForm");

    // Select all back buttons
    const backButtons = document.querySelectorAll(".back-button");

    // "No results found" row setup
    let noResultsRow = document.createElement("tr");
    let colSpan = campaignTable.querySelector("thead tr").children.length;
    noResultsRow.innerHTML = `<td colspan="${colSpan}" class="no-results">No results found</td>`;
    noResultsRow.style.display = "none"; // Initially hidden
    tableBody.appendChild(noResultsRow);

    function checkNoResults() {
        let visibleRows = 0;
        tableBody.querySelectorAll("tr:not(.no-results)").forEach(row => {
            if (row.style.display !== "none") {
                visibleRows++;
            }
        });

        noResultsRow.style.display = visibleRows === 0 ? "table-row" : "none";
    }

    let searchTimeout;

    searchBox.addEventListener("keyup", function () {
        clearTimeout(searchTimeout);

        searchTimeout = setTimeout(() => {
            let filter = searchBox.value.toLowerCase().trim();
            let visibleRows = 0;

            tableBody.querySelectorAll("tr:not(.no-results)").forEach(row => {
                if (row.cells.length < 4) return;

                let title = row.cells[0].textContent.toLowerCase();
                let category = row.cells[1].textContent.toLowerCase();
                let status = row.cells[3].textContent.toLowerCase();

                if (filter === "" || title.includes(filter) || category.includes(filter) || status.includes(filter)) {
                    row.style.display = "table-row";
                    visibleRows++;
                } else {
                    row.style.display = "none";
                }
            });

            noResultsRow.style.display = visibleRows === 0 ? "table-row" : "none";

            if (filter === "") {
                tableBody.querySelectorAll("tr").forEach(row => {
                    row.style.display = "table-row";
                });
                noResultsRow.style.display = "none";
            }
        }, 200);
    });

    // Show Add Campaign Section
    addCampaignBtn.addEventListener("click", function (e) {
        e.preventDefault();
        addCampaignSection.style.display = "block";
        editCampaignSection.style.display = "none"; // Ensure edit section is hidden
        campaignTable.parentElement.style.display = "none";
        searchBox.style.display = "none";
        headerContainer.style.display = "none";
    });

    // Handle Back Button Clicks
    backButtons.forEach(button => {
        button.addEventListener("click", function () {
            addCampaignSection.style.display = "none";
            editCampaignSection.style.display = "none";
            campaignTable.parentElement.style.display = "block";
            searchBox.style.display = "block";
            headerContainer.style.display = "flex";
        });
    });

    // AJAX Form Submission
    addCampaignForm.addEventListener("submit", function (e) {
        e.preventDefault();
        let formData = new FormData(addCampaignForm);

        fetch("user_add_campaign.php", {
            method: "POST",
            body: formData
        })
        .then(response => response.json())
        .then(data => {
            console.log("Server Response:", data);

            if (data.status === "success") { 
                alert("Campaign added successfully!");

                noResultsRow.style.display = "none";

                let newRow = document.createElement("tr");
                newRow.innerHTML = `
                    <td>${formData.get("title")}</td>
                    <td>${formData.get("category")}</td>
                    <td>₹${parseInt(formData.get("goal_amount")).toLocaleString()}</td>
                    <td>
                        <button class="status-btn pending">Pending</button>
                    </td>
                `;

                tableBody.insertBefore(newRow, tableBody.firstChild);

                addCampaignForm.reset();
                addCampaignSection.style.display = "none";
                campaignTable.parentElement.style.display = "block";
                searchBox.style.display = "block";
                headerContainer.style.display = "flex";

                checkNoResults();
            } else {
                alert("Error: " + data.message);
                console.error("Server Error Message:", data.message);
            }
        })
        .catch(error => {
            console.error("Fetch Error:", error);
            alert("An unexpected error occurred: " + error.message);
        });
    });
    
    document.addEventListener("DOMContentLoaded", function () {
        const tableBody = document.querySelector("#campaignTable tbody");
    
        // Function to handle delete button click
        tableBody.addEventListener("click", function (event) {
            if (event.target.classList.contains("delete-btn")) {
                let campaignId = event.target.getAttribute("data-id");
    
                // Show confirmation popup
                let confirmDelete = confirm("Are you sure you want to delete this campaign?");
                if (!confirmDelete) return; // If user cancels, exit function
    
                // Send AJAX request to delete campaign
                fetch("user_del_campaign.php", {
                    method: "POST",
                    headers: { "Content-Type": "application/x-www-form-urlencoded" },
                    body: "id=" + encodeURIComponent(campaignId),
                })
                .then(response => response.json())
                .then(data => {
                    if (data.status === "success") {
                        alert("Campaign deleted successfully!");
                        event.target.closest("tr").remove(); // Remove row from table
                    } else {
                        alert("Error: " + data.message);
                    }
                })
                .catch(error => {
                    console.error("Fetch Error:", error);
                    alert("An unexpected error occurred.");
                });
            }
        });
    });
    
});
