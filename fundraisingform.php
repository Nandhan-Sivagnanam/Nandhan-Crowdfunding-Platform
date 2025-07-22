<?php
session_start();
include 'includes_db.php'; // Database connection

// Ensure user logged in
if (!isset($_SESSION['user']) || !isset($_SESSION['user']['id']) || !isset($_SESSION['user']['role'])) {
    header("Location: login.php");
    exit();
}

$user_id = $_SESSION['user']['id'];
$created_by = $_SESSION['user']['name'];
$user_role = $_SESSION['user']['role'];

if ($user_role !== 'admin' && $user_role !== 'user') {
    die("Error: Unauthorized access.");
}

$campaign_saved = false;
$campaign_id = null;

// --- Save Fundraiser Form ---
if ($_SERVER["REQUEST_METHOD"] == "POST" && isset($_POST['submit_fundraiser'])) {
    $campaign_title = trim(htmlspecialchars($_POST['campaign_title']));
    if ($campaign_title === 'other' && !empty($_POST['campaign_title_other'])) {
        $campaign_title = trim(htmlspecialchars($_POST['campaign_title_other']));
    }

    // Get correct description
    $campaign_description = '';
    if (!empty($_POST['medical_description'])) {
        $campaign_description = trim(htmlspecialchars($_POST['medical_description']));
    } elseif (!empty($_POST['financial_support_description'])) {
        $campaign_description = trim(htmlspecialchars($_POST['financial_support_description']));
    } elseif (!empty($_POST['environment_description'])) {
        $campaign_description = trim(htmlspecialchars($_POST['environment_description']));
    } elseif (!empty($_POST['education_description'])) {
        $campaign_description = trim(htmlspecialchars($_POST['education_description']));
    } elseif (!empty($_POST['other_description'])) {
        $campaign_description = trim(htmlspecialchars($_POST['other_description']));
    } else {
        die("Error: Campaign description is empty!");
    }

    $goal_amount = trim(htmlspecialchars($_POST['goal_amount']));
    if (empty($campaign_title) || empty($goal_amount)) {
        die("Error: Missing fundraiser fields.");
    }

    // Capture start and end dates
    $start_date = trim(htmlspecialchars($_POST['start_date']));
    $end_date = trim(htmlspecialchars($_POST['end_date']));

    // Check if the file was uploaded successfully
    if (isset($_FILES['fund_image']) && $_FILES['fund_image']['error'] == 0) {
        $target_dir = "uploads/";
        $target_file = $target_dir . basename($_FILES["fund_image"]["name"]);
        if (move_uploaded_file($_FILES["fund_image"]["tmp_name"], $target_file)) {
            $image_url = $target_file;
        } else {
            die("Error: File upload failed.");
        }
    }

    // Insert into campaigns table with start and end dates
    $stmt = $conn->prepare("INSERT INTO campaigns (title, description, goal_amount, image_url, created_by, user_id, start_date, end_date) VALUES (?, ?, ?, ?, ?, ?, ?, ?)");
    $stmt->bind_param("ssdsssss", $campaign_title, $campaign_description, $goal_amount, $image_url, $created_by, $user_id, $start_date, $end_date);
    if (!$stmt->execute()) {
        die("Error: Campaign insert failed.");
    }
    $campaign_id = $stmt->insert_id;
    $stmt->close();

    $campaign_saved = true;
}

// --- Save Payment Form ---
if ($_SERVER["REQUEST_METHOD"] == "POST" && isset($_POST['submit_payment'])) {
    $campaign_id = intval($_POST['campaign_id']);
    $payment_method = trim(htmlspecialchars($_POST['payment_method']));

    if ($payment_method === 'upi') {
        $upi_id = trim(htmlspecialchars($_POST['upi_id']));
        $payment_details = "UPI ID: " . $upi_id;
    } elseif ($payment_method === 'credit_card' || $payment_method === 'debit_card') {
        $card_number = trim(htmlspecialchars($_POST['card_number']));
        $card_name = trim(htmlspecialchars($_POST['card_name']));
        $expiry_date = trim(htmlspecialchars($_POST['expiry_date']));
        $cvv = trim(htmlspecialchars($_POST['cvv']));
        $payment_details = "Card Number: $card_number, Name: $card_name, Expiry: $expiry_date";
    } elseif ($payment_method === 'net_banking') {
        $bank_name = trim(htmlspecialchars($_POST['bank_name']));
        $account_holder = trim(htmlspecialchars($_POST['account_holder']));
        $account_number = trim(htmlspecialchars($_POST['account_number']));
        $payment_details = "Bank: $bank_name, Account Holder: $account_holder, Account Number: $account_number";
    } else {
        die("Error: Invalid payment method selected.");
    }

    $stmt_payment = $conn->prepare("INSERT INTO payments (campaign_id, payment_method, payment_details) VALUES (?, ?, ?)");
    $stmt_payment->bind_param("iss", $campaign_id, $payment_method, $payment_details);
    if (!$stmt_payment->execute()) {
        die("Error: Payment insert failed.");
    }
    $stmt_payment->close();

    echo "<script>alert('Fundraiser and Payment Saved Successfully!'); window.location.href='index.php';</script>";
    exit();
}
?>

<!-- --- HTML Starts Here --- -->
<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <title>Start a Fundraiser</title>
    <link rel="stylesheet" href="css/fund_form.css">
</head>
<body>

<div class="form-container">
<?php if (!$campaign_saved): ?>
    <h1>Start a Fundraiser</h1>
    <form action="" method="POST" enctype="multipart/form-data">

        <label for="fund-purpose">Purpose of Raising Funds:</label>
        <select id="fund-purpose" name="campaign_title" required onchange="toggleFields()">
            <option value="">Select Purpose</option>
            <option value="medical">Medical Treatment</option>
            <option value="Financial Support">Financial Support</option>
            <option value="Environment">Environment</option>
            <option value="Education">Education</option>
            <option value="other">Other Cause</option>
        </select>

        <div id="medical-fields" style="display: none;">
            <label for="relationship-with-patient">Relationship with Patient:</label>
            <input type="text" id="relationship-with-patient" name="relationship-with-patient">
            <label for="medical-about">About:</label>
            <textarea id="medical-about" name="medical_description" placeholder="Medical campaign details"></textarea>
        </div>

        <div id="financial-support-fields" style="display: none;">
            <label for="financial-support-title">Financial Support Title:</label>
            <input type="text" id="financial-support-title" name="campaign_title_other">
            <label for="financial-support-description">About the Financial Support:</label>
            <textarea id="financial-support-description" name="financial_support_description" placeholder="Financial Support campaign details"></textarea>
        </div>

        <div id="environment-fields" style="display: none;">
            <label for="environment-title">Environment Initiative Title:</label>
            <input type="text" id="environment-title" name="campaign_title_other">
            <label for="environment-description">About the Environment Initiative:</label>
            <textarea id="environment-description" name="environment_description" placeholder="Environment campaign details"></textarea>
        </div>

        <div id="education-fields" style="display: none;">
            <label for="education-title">Education Campaign Title:</label>
            <input type="text" id="education-title" name="campaign_title_other">
            <label for="education-description">About the Education Campaign:</label>
            <textarea id="education-description" name="education_description" placeholder="Education campaign details"></textarea>
        </div>

        <div id="other-fields" style="display: none;">
            <label for="fundraiser-title">Fundraiser Title:</label>
            <input type="text" id="fundraiser-title" name="campaign_title_other">
            <label for="fundraiser-about">About:</label>
            <textarea id="fundraiser-about" name="other_description" placeholder="Other fundraising details"></textarea>
        </div>

        <label for="fund-amount">Goal Amount (₹):</label>
        <input type="number" id="fund-amount" name="goal_amount" required min="1" step="0.01" placeholder="₹">

        <label for="start-date">Start Date:</label>
        <input type="date" id="start-date" name="start_date" required>

        <label for="end-date">End Date:</label>
        <input type="date" id="end-date" name="end_date" required>

        <label for="fund-image">Fundraiser Image:</label>
        <label for="fund-image" class="custom-file-upload">Choose File</label>
        <input type="file" id="fund-image" name="fund_image" accept="image/*" required>

        <button type="submit" name="submit_fundraiser">Save & Continue to Payment</button>
    </form>

<?php else: ?>
    <h1>Payment Details</h1>
    <form action="" method="POST">
        <input type="hidden" name="campaign_id" value="<?php echo $campaign_id; ?>">

        <label for="payment-method">Select Payment Method:</label>
        <select name="payment_method" id="payment-method" required onchange="showPaymentFields()">
            <option value="">Choose Payment Method</option>
            <option value="upi">UPI</option>
            <option value="credit_card">Credit Card</option>
            <option value="debit_card">Debit Card</option>
            <option value="net_banking">Net Banking</option>
        </select>

        <div id="upi-fields" style="display: none;">
            <label for="upi-id">Enter UPI ID:</label>
            <input type="text" id="upi-id" name="upi_id" placeholder="example@upi">
        </div>

        <div id="card-fields" style="display: none;">
            <label for="card-number">Card Number:</label>
            <input type="text" id="card-number" name="card_number" maxlength="16" placeholder="XXXX-XXXX-XXXX-XXXX">

            <label for="card-name">Name on Card:</label>
            <input type="text" id="card-name" name="card_name" placeholder="Full Name">

            <label for="expiry-date">Expiry Date:</label>
            <input type="month" id="expiry-date" name="expiry_date">

            <label for="cvv">CVV:</label>
            <input type="password" id="cvv" name="cvv" maxlength="4" placeholder="CVV">
        </div>

        <div id="netbanking-fields" style="display: none;">
            <label for="bank-name">Select Bank:</label>
            <select id="bank-name" name="bank_name">
                <option value="">Choose your Bank</option>
                <option value="HDFC">HDFC Bank</option>
                <option value="ICICI">ICICI Bank</option>
                <option value="SBI">State Bank of India</option>
                <option value="AXIS">Axis Bank</option>
            </select>

            <label for="account-holder">Account Holder Name:</label>
            <input type="text" id="account-holder" name="account_holder" placeholder="Full Name">

            <label for="account-number">Account Number:</label>
            <input type="text" id="account-number" name="account_number" placeholder="Account Number">
        </div>

        <button type="submit" name="submit_payment">Submit Payment</button>
    </form>
<?php endif; ?>
</div>

<script>
// Toggle Fundraiser Fields
function toggleFields() {
    const purpose = document.getElementById('fund-purpose').value;
    
    // Hide all fields initially
    document.getElementById('medical-fields').style.display = 'none';
    document.getElementById('financial-support-fields').style.display = 'none';
    document.getElementById('environment-fields').style.display = 'none';
    document.getElementById('education-fields').style.display = 'none';
    document.getElementById('other-fields').style.display = 'none';

    // Show relevant fields based on selection
    if (purpose === 'medical') {
        document.getElementById('medical-fields').style.display = 'block';
    } else if (purpose === 'Financial Support') {
        document.getElementById('financial-support-fields').style.display = 'block';
    } else if (purpose === 'Environment') {
        document.getElementById('environment-fields').style.display = 'block';
    } else if (purpose === 'Education') {
        document.getElementById('education-fields').style.display = 'block';
    } else if (purpose === 'other') {
        document.getElementById('other-fields').style.display = 'block';
    }
}

// Show payment method fields
function showPaymentFields() {
    const paymentMethod = document.getElementById('payment-method').value;
    
    // Hide all fields initially
    document.getElementById('upi-fields').style.display = 'none';
    document.getElementById('card-fields').style.display = 'none';
    document.getElementById('netbanking-fields').style.display = 'none';

    // Show the relevant fields based on the selected payment method
    if (paymentMethod === 'upi') {
        document.getElementById('upi-fields').style.display = 'block';
    } else if (paymentMethod === 'credit_card' || paymentMethod === 'debit_card') {
        document.getElementById('card-fields').style.display = 'block';
    } else if (paymentMethod === 'net_banking') {
        document.getElementById('netbanking-fields').style.display = 'block';
    }
}
</script>

</body>
</html>
