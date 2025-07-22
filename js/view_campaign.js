// Function to Open Popup and Store State
function openPopup() {
    document.getElementById('popup').style.display = 'flex';
    sessionStorage.setItem('popupShown', 'true'); // Store state in sessionStorage
}

// Function to Close Popup and Clear State
function closePopup() {
    document.getElementById('popup').style.display = 'none';
    sessionStorage.removeItem('popupShown'); // Remove state when closing
}

// Ensure Popup Does NOT Auto-Open on Refresh (Fix Flickering)
document.addEventListener('DOMContentLoaded', function () {
    const popup = document.getElementById('popup');
    if (!sessionStorage.getItem('popupShown')) {
        popup.style.display = 'none'; // Keep hidden unless manually opened
    }
});

// Function to Show/Hide "Other Amount" Input Field
function selectAmount(amount) {
    const otherAmountField = document.getElementById('other-amount-field');
    if (amount === 'other') {
        otherAmountField.style.display = 'block';
        document.getElementById('other-amount').focus(); // Auto-focus on input
    } else {
        otherAmountField.style.display = 'none';
        document.getElementById('other-amount').value = ''; // Clear input if hidden
    }
}

// JavaScript to handle "Preview More" and "Hide" functionality
document.addEventListener('DOMContentLoaded', function () {
    const toggleText = document.getElementById('toggle-description');
    const descriptionText = document.getElementById('description-text');

    let isExpanded = false;
    toggleText.addEventListener('click', () => {
        isExpanded = !isExpanded;
        descriptionText.style.maxHeight = isExpanded ? "none" : "150px"; // Toggle height
        toggleText.textContent = isExpanded ? "Hide" : "Preview More"; // Update button text
    });
});
