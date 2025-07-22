document.addEventListener("DOMContentLoaded", function () {
    const searchInput = document.getElementById("searchInput");
    const tableBody = document.getElementById("campaignTableBody");
    let overlay = document.createElement("div");
    overlay.classList.add("dialog-overlay");
    document.body.appendChild(overlay);

    let dialog = document.createElement("div");
    dialog.classList.add("confirmation-dialog");
    dialog.innerHTML = `
        <div class="header">Are you sure you want to proceed?</div>
        <div class="button-group">
            <button class="btn-approve">Yes</button>
            <button class="btn-decline">No</button>
        </div>
    `;
    document.body.appendChild(dialog);

    const approveBtn = dialog.querySelector(".btn-approve");
    const declineBtn = dialog.querySelector(".btn-decline");

    let selectedCampaignId = null;
    let selectedAction = null;
    let campaignStatus = {};

    // ✅ Search Functionality
    searchInput.addEventListener("input", function () {
        let filter = this.value.trim().toLowerCase();
        let rows = document.querySelectorAll("#campaignTableBody tr:not(#noResultsRow)");
        let found = false;

        rows.forEach(row => {
            let title = row.getAttribute("data-title").toLowerCase();
            let category = row.getAttribute("data-category").toLowerCase();
            let status = row.getAttribute("data-status").toLowerCase();

            if (title.includes(filter) || category.includes(filter) || status.includes(filter) || (filter === "pending" && status === "pending")) {
                row.style.display = "";
                found = true;
            } else {
                row.style.display = "none";
            }
        });

        let noResultsRow = document.getElementById("noResultsRow");

        if (!found && filter !== "") {
            if (!noResultsRow) {
                noResultsRow = document.createElement("tr");
                noResultsRow.id = "noResultsRow";
                noResultsRow.innerHTML = `<td colspan="7" style="text-align: center; color: red; font-weight: bold;">No results found</td>`;
                tableBody.appendChild(noResultsRow);
            }
        } else {
            if (noResultsRow) {
                noResultsRow.remove();
            }
        }

        if (filter === "") {
            rows.forEach(row => row.style.display = "");
            if (noResultsRow) {
                noResultsRow.remove();
            }
        }
    });

    // ✅ Campaign Approval & Rejection Logic
    document.querySelectorAll(".approve-btn, .reject-btn").forEach(button => {
        let row = button.closest("tr");
        let campaignId = button.getAttribute("data-id");
        let status = row.querySelector(".status").textContent.trim().toLowerCase();

        campaignStatus[campaignId] = status;

        button.addEventListener("click", function () {
            selectedCampaignId = this.getAttribute("data-id");
            selectedAction = this.classList.contains("approve-btn") ? "approve" : "reject";

            if (selectedAction === "approve") {
                if (campaignStatus[selectedCampaignId] === "approved") {
                    alert("This campaign is already approved!");
                    return;
                }
                dialog.querySelector(".header").innerHTML = `Are you sure you want to approve this campaign?`;
            } 
            else if (selectedAction === "reject") {
                if (campaignStatus[selectedCampaignId] === "rejected") {
                    alert("This campaign is already rejected!");
                    return;
                }
                dialog.querySelector(".header").innerHTML = `Are you sure you want to reject this campaign?`;
            }

            dialog.style.display = "block";
            overlay.style.display = "block";
        });
    });

    // ✅ Confirm Approval or Rejection
    approveBtn.addEventListener("click", function () {
        if (!selectedCampaignId || !selectedAction) return;

        fetch("campaign_action.php", {
            method: "POST",
            headers: { "Content-Type": "application/json" },
            body: JSON.stringify({ campaign_id: selectedCampaignId, action: selectedAction })
        })
        .then(response => response.json())
        .then(data => {
            if (data.success) {
                alert(`Campaign successfully ${selectedAction}d!`);
                campaignStatus[selectedCampaignId] = selectedAction === "approve" ? "approved" : "rejected";
                location.reload();
            } else {
                alert("Error: " + data.error);
            }
        })
        .catch(error => alert("An error occurred!"));

        dialog.style.display = "none";
        overlay.style.display = "none";
    });

    // ✅ Close Dialog on "No" Button Click
    declineBtn.addEventListener("click", function () {
        dialog.style.display = "none";
        overlay.style.display = "none";
    });

    // ✅ Close Dialog on Overlay Click
    overlay.addEventListener("click", function () {
        dialog.style.display = "none";
        overlay.style.display = "none";
    });

    /*** ✅ ADD CAMPAIGN FUNCTIONALITY ***/
    const addCampaignBtn = document.getElementById("addCampaignBtn");
    const backToCampaignsBtn = document.getElementById("backToCampaignsBtn");
    const campaignsSection = document.getElementById("campaignsSection");
    const addCampaignSection = document.getElementById("addCampaignSection");

    if (addCampaignBtn) {
        addCampaignBtn.addEventListener("click", function () {
            console.log("✅ Add Campaign Button Clicked"); // Debugging

            campaignsSection.style.display = "none";  // Hide main campaign list
            addCampaignSection.style.display = "block";  // Show the form
            addCampaignSection.classList.remove("hidden"); // Ensure it appears
        });
    }

    if (backToCampaignsBtn) {
        backToCampaignsBtn.addEventListener("click", function () {
            console.log("✅ Back Button Clicked"); // Debugging

            campaignsSection.style.display = "block"; // Show campaign list
            addCampaignSection.style.display = "none"; // Hide form
        });
    }

    // Handle Campaign Submission
    document.getElementById("addCampaignForm").addEventListener("submit", function (e) {
        e.preventDefault();

        let formData = new FormData(this);

        fetch("add_campaign.php", {
            method: "POST",
            body: formData
        })
        .then(response => response.json())
        .then(data => {
            if (data.success) {
                alert("Campaign added successfully!");
                location.reload();
            } else {
                alert("Error: " + data.error);
            }
        })
        .catch(error => alert("An error occurred!"));
    });
});
