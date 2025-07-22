document.addEventListener("DOMContentLoaded", function () {
    let form = document.getElementById("fundraising-form");

    if (!form) {
        console.error("🚨 ERROR: Form not found!");
        return;
    }

    console.log(" Form found!");

    // Function to update the hidden description field
    function updateDescription() {
        let purpose = document.getElementById("fund-purpose").value;
        let descriptionField = document.getElementById("final-description");

        if (purpose === "medical") {
            descriptionField.value = document.getElementById("medical-about").value.trim();
        } else if (purpose === "treatment") {
            descriptionField.value = document.getElementById("treatment-about").value.trim();
        } else if (purpose === "other") {
            descriptionField.value = document.getElementById("fundraiser-about").value.trim();
        }

        console.log(" Updated campaign_description:", descriptionField.value); // Debugging
    }

    // Function to show/hide form fields based on purpose selection
    function toggleFields() {
        let fundPurpose = document.getElementById("fund-purpose").value;
        document.getElementById("medical-fields").style.display = (fundPurpose === "medical") ? "block" : "none";
        document.getElementById("treatment-fields").style.display = (fundPurpose === "treatment") ? "block" : "none";
        document.getElementById("other-fields").style.display = (fundPurpose === "other") ? "block" : "none";
    }

    // Attach input event listeners to update description in real-time
    document.getElementById("medical-about").addEventListener("input", updateDescription);
    document.getElementById("treatment-about").addEventListener("input", updateDescription);
    document.getElementById("fundraiser-about").addEventListener("input", updateDescription);

    // Handle form submission
    form.addEventListener("submit", function (event) {
        console.log("📢 Form submission detected!");

        // Ensure the description is updated before submitting
        updateDescription();

        // Fetch form data
        let fundPurpose = document.getElementById("fund-purpose").value;
        let fundAmount = document.getElementById("fund-amount").value;
        let fundImage = document.getElementById("fund-image").files[0];
        let relationshipWithPatient = document.getElementById("relationship-with-patient").value;
        let medicalAbout = document.getElementById("medical-about").value.trim();
        let treatmentAbout = document.getElementById("treatment-about").value.trim();
        let fundraiserTitle = document.getElementById("fundraiser-title").value.trim();
        let fundraiserAbout = document.getElementById("fundraiser-about").value.trim();
        let descriptionValue = document.getElementById("final-description").value.trim();

        // Simple validation
        if (!fundPurpose || !fundAmount || !fundImage || descriptionValue === "") {
            alert(" Error: Please fill out all fields and upload an image.");
            event.preventDefault();
            return;
        }

        if (fundPurpose === "medical" && (!relationshipWithPatient || !medicalAbout)) {
            alert(" Error: Please provide patient relationship and medical details.");
            event.preventDefault();
            return;
        }

        if (fundPurpose === "treatment" && !treatmentAbout) {
            alert(" Error: Please provide treatment details.");
            event.preventDefault();
            return;
        }

        if (fundPurpose === "other" && (!fundraiserTitle || !fundraiserAbout)) {
            alert(" Error: Please provide a fundraiser title and details.");
            event.preventDefault();
            return;
        }

        // Display form data in console for debugging
        console.log({
            fundPurpose,
            fundAmount,
            fundImage,
            relationshipWithPatient,
            medicalAbout,
            treatmentAbout,
            fundraiserTitle,
            fundraiserAbout,
            descriptionValue
        });

        alert(" Fundraising data saved successfully!");
    });

    // Image preview function
    document.getElementById("fund-image").addEventListener("change", function (event) {
        let file = event.target.files[0];
        if (!file) return;

        let reader = new FileReader();
        reader.onload = function (e) {
            let img = document.createElement("img");
            img.src = e.target.result;
            img.style.maxWidth = "100%";
            img.style.height = "auto";

            let preview = document.getElementById("image-preview");
            preview.innerHTML = "";
            preview.appendChild(img);
        };
        reader.readAsDataURL(file);
    });

    // Attach event listener to update fields when selecting fund purpose
    document.getElementById("fund-purpose").addEventListener("change", toggleFields);
});
