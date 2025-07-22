let selectedAmount = null; // Ensure selectedAmount is initialized
let selectedPaymentMethod = null; // Ensure selectedPaymentMethod is initialized
const campaignId = document.getElementById('donation-form').dataset.campaignId; // Ensure campaignId is retrieved from the form

function selectAmount(amount) {
    selectedAmount = amount;
    document.getElementById('other-amount-field').style.display = amount === 'other' ? 'block' : 'none';
    if (amount !== 'other') {
        document.getElementById('other-amount').value = ''; // Clear the "Other" amount field if not selected
    }
}

function selectPaymentMethod(method) {
    selectedPaymentMethod = method;
    alert(`Payment method selected: ${method}`); // Provide feedback to the user
}

function submitDonation(event) {
    event.preventDefault(); // Prevent default form submission

    // Validate if amount and payment method are selected
    if (selectedAmount === null) {
        alert('Please select a donation amount.');
        return;
    }
    if (selectedAmount === 'other') {
        const otherAmount = parseFloat(document.getElementById('other-amount').value.trim());
        if (isNaN(otherAmount) || otherAmount <= 0) {
            alert('Please enter a valid donation amount.');
            return;
        }
        selectedAmount = otherAmount; // Update selectedAmount with the entered "Other" amount
    }
    if (!selectedPaymentMethod) {
        alert('Please select a payment method.');
        return;
    }

    // Get the form details
    const name = document.getElementById('name').value.trim();
    const email = document.getElementById('email').value.trim();
    const mobile = document.getElementById('mobile').value.trim();

    // Validate if user provided valid details
    if (!name || !email || !mobile || isNaN(selectedAmount) || selectedAmount <= 0) {
        alert('Please provide valid donation details.');
        return;
    }

    // Prepare form data to send to the server
    let formData = new FormData();
    formData.append('campaign_id', campaignId); // Ensure campaignId is set properly
    formData.append('donation_amount', selectedAmount);
    formData.append('payment_method', selectedPaymentMethod);
    formData.append('name', name);
    formData.append('email', email);
    formData.append('mobile', mobile);

    fetch('process_donation.php', {
        method: 'POST',
        body: formData
    })
    .then(response => {
        console.log('Raw Response:', response); // Log the raw response object
        if (!response.ok) {
            throw new Error(`HTTP error! Status: ${response.status}`);
        }
        return response.text(); // Convert response to text
    })
    .then(data => {
        console.log('Server Response:', data); // Log the raw response for debugging
        try {
            const jsonResponse = JSON.parse(data); // Try parsing the response
            if (jsonResponse.success) {
                alert('Donation successful!');
                let raisedAmount = parseInt(document.getElementById('raised-amount').innerText.replace('₹', '').replace(',', '')) || 0;
                raisedAmount += selectedAmount;
                document.getElementById('raised-amount').innerText = '₹' + raisedAmount.toLocaleString();
                closePopup();
            } else {
                alert(jsonResponse.message || 'Donation failed! Please try again.');
            }
        } catch (error) {
            console.error('Error parsing JSON:', error, 'Raw Response:', data); // Log the error and raw response
            alert('An unexpected error occurred. Please try again later.');
        }
    })
    .catch(error => {
        console.error('Fetch Error:', error); // Log fetch errors
        alert('Error: ' + error.message);
    });
}

// Ensure the form submission event is properly bound
document.getElementById('donation-form').addEventListener('submit', submitDonation);
