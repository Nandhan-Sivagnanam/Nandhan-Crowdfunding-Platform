CREATE TABLE donations (
    id INT AUTO_INCREMENT PRIMARY KEY, -- Unique identifier for each donation
    user_id INT NULL, -- ID of the user making the donation (nullable for guest users)
    campaign_id INT NOT NULL, -- ID of the campaign being donated to
    amount DECIMAL(10, 2) NOT NULL, -- Donation amount with two decimal places
    donation_date DATETIME NOT NULL, -- Date and time of the donation
    payment_method VARCHAR(50) NOT NULL, -- Payment method used (e.g., Razorpay, GPay, etc.)
    donor_name VARCHAR(255) NOT NULL, -- Name of the donor
    donor_email VARCHAR(255) NOT NULL, -- Email of the donor
    donor_mobile VARCHAR(15) NOT NULL, -- Mobile number of the donor
    FOREIGN KEY (user_id) REFERENCES users(id) ON DELETE SET NULL, -- Foreign key to users table
    FOREIGN KEY (campaign_id) REFERENCES campaigns(id) ON DELETE CASCADE -- Foreign key to campaigns table
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4;
