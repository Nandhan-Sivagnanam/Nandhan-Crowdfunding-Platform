document.addEventListener("DOMContentLoaded", () => {
    document.querySelectorAll(".delete-btn").forEach(button => {
        button.addEventListener("click", function () {
            const campaignId = this.getAttribute("data-id");
            if (confirm("Are you sure you want to delete this campaign?")) {
                fetch("delete_campaign.php", {
                    method: "POST",
                    headers: { "Content-Type": "application/json" },
                    body: JSON.stringify({ id: campaignId })
                })
                .then(response => response.json())
                .then(data => {
                    if (data.success) {
                        alert("Campaign deleted successfully!");
                        location.reload();
                    } else {
                        alert("Failed to delete campaign.");
                    }
                })
                .catch(error => console.error("Error:", error));
            }
        });
    });
});
