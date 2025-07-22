document.addEventListener("DOMContentLoaded", function () {
    const campaignsSection = document.querySelector(".campaigns-section");
    const addCampaignSection = document.getElementById("addCampaignSection");
    const editCampaignSection = document.getElementById("editCampaignSection");
    const headerContainer = document.querySelector(".header-container");
    const backToCampaignsBtnEdit = document.getElementById("backToCampaignsBtn");

    console.log("Back button found:", backToCampaignsBtnEdit !== null);

    // Ensure edit and add sections are hidden initially
    if (editCampaignSection) {
        editCampaignSection.style.display = "none";
        console.log("Edit campaign section hidden initially.");
    }
    if (addCampaignSection) {
        addCampaignSection.style.display = "none";
        console.log("Add campaign section hidden initially.");
    }

    // Fetch and populate campaign details for editing
    document.querySelectorAll(".edit-btn").forEach(button => {
        button.addEventListener("click", function () {
            const campaignId = this.getAttribute("data-id");
            console.log("Editing campaign ID:", campaignId);

            fetch(`user_edit_campaign.php?id=${campaignId}`)
                .then(response => response.json())
                .then(data => {
                    if (data.success) {
                        document.getElementById("editCampaignId").value = data.id;
                        document.getElementById("editTitle").value = data.title;
                        document.getElementById("editCategory").value = data.category;
                        document.getElementById("editDescription").value = data.description;
                        document.getElementById("editGoal").value = data.goal_amount;
                        document.getElementById("editStartDate").value = data.start_date;
                        document.getElementById("editEndDate").value = data.end_date;
                        document.getElementById("editCloseType").value = data.close_type;

                        // Hide main campaign list, add section, and header
                        campaignsSection.style.display = "none";
                        addCampaignSection.style.display = "none";
                        headerContainer.style.display = "none"; 
                        editCampaignSection.style.display = "block"; // Show edit form

                        console.log("Edit section displayed.");
                    } else {
                        alert("Error fetching campaign details.");
                    }
                })
                .catch(error => console.error("Error fetching:", error));
        });
    });

    // Handle campaign update form submission
    document.getElementById("editCampaignForm").addEventListener("submit", function (event) {
        event.preventDefault();

        const formData = new FormData(this);

        fetch("user_edit_campaign.php", {
            method: "POST",
            body: formData
        })
        .then(response => response.json())
        .then(data => {
            if (data.success) {
                alert("Campaign updated successfully!");
                location.reload(); // Reload the page to reflect changes
            } else {
                alert("Error updating campaign: " + data.message);
            }
        })
        .catch(error => console.error("Error updating campaign:", error));
    });

    // **✅ Debugging & Fix: Edit section back button**
    if (backToCampaignsBtnEdit) {
        backToCampaignsBtnEdit.addEventListener("click", function () {
            console.log("Back button clicked!");

            editCampaignSection.style.display = "none"; // Hide edit section
            addCampaignSection.style.display = "none"; // Ensure add section is hidden
            campaignsSection.style.display = "block";  // Show main campaign list
            headerContainer.style.display = "flex";  // Show header again

            console.log("Returned to campaigns list.");
        });
    } else {
        console.error("❌ Back button not found in the DOM.");
    }
});
