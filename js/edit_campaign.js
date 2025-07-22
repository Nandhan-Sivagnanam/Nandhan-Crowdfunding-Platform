document.addEventListener("DOMContentLoaded", function () {
    const editButtons = document.querySelectorAll(".edit-btn");
    const campaignSection = document.getElementById("campaignsSection");
    const editSection = document.getElementById("editCampaignSection");
    const editForm = document.getElementById("editCampaignForm");

    // ✅ Ensure form exists before adding event listeners
    if (!editForm) {
        console.error("Error: editCampaignForm not found.");
        return;
    }

    // Create Back Button
    let backButton = document.createElement("button");
    backButton.textContent = "Back";
    backButton.classList.add("back-button");
    editSection.prepend(backButton);

    // Show edit form and hide campaign list when "Edit" is clicked
    editButtons.forEach(button => {
        button.addEventListener("click", function () {
            campaignSection.classList.add("hidden"); // Hide campaign list
            editSection.classList.remove("hidden"); // Show edit form

            const campaignId = this.getAttribute("data-id");
            fetchCampaignDetails(campaignId); // Load campaign details into form
        });
    });

    // Go back to campaign list when "Back" is clicked
    backButton.addEventListener("click", function () {
        editSection.classList.add("hidden"); // Hide edit form
        campaignSection.classList.remove("hidden"); // Show campaign list
    });

    // Fetch campaign details and populate the form
    function fetchCampaignDetails(campaignId) {
        console.log("Fetching campaign with ID:", campaignId); // ✅ Debugging

        fetch(`admin_edit_campaign.php?id=${campaignId}`)
            .then(response => response.json())
            .then(data => {
                console.log("Response Data:", data); // ✅ Debugging

                if (data.success) {
                    document.getElementById("editCampaignId").value = data.data.id;
                    document.getElementById("editTitle").value = data.data.title || "";
                    document.getElementById("editCategory").value = data.data.category || "";
                    document.getElementById("editDescription").value = data.data.description || "";
                    document.getElementById("editGoal").value = data.data.goal_amount || "";

                    // ✅ Fix invalid date issue
                    document.getElementById("editStartDate").value = formatDate(data.data.start_date);
                    document.getElementById("editEndDate").value = formatDate(data.data.end_date);
                } else {
                    alert("Error fetching campaign details: " + data.error);
                }
            })
            .catch(error => console.error("Error:", error));
    }

    // ✅ Function to handle invalid date formats
    function formatDate(dateString) {
        if (!dateString || dateString === "0000-00-00") return ""; // Return empty string if invalid
        return dateString; // Otherwise, return the valid date
    }

    // ✅ Handle Campaign Update (Submit Form)
    editForm.addEventListener("submit", function (event) {
        event.preventDefault(); // Prevent default form submission

        let formData = new FormData(editForm);

        // Debugging: Log FormData before sending
        console.log("Submitting form data:");
        for (let pair of formData.entries()) {
            console.log(pair[0] + ": " + pair[1]);
        }

        fetch("admin_edit_campaign.php", {
            method: "POST",
            body: formData
        })
        .then(response => response.json())
        .then(data => {
            console.log("Server Response:", data); // ✅ Debugging
            if (data.success) {
                alert("Campaign updated successfully!");
                location.reload(); // Refresh to see changes
            } else {
                alert("Update failed: " + data.error);
            }
        })
        .catch(error => {
            console.error("Error:", error);
            alert("An error occurred. Check the console for details.");
        });
    });
});
