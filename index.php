
<!DOCTYPE html>
<html lang="en">
<head>
  <meta charset="UTF-8">
  <meta name="viewport" content="width=device-width, initial-scale=1.0">
  <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/bootstrap/5.3.0/css/bootstrap.min.css">
  <link rel="stylesheet" href="css/index_style.css?v=1.1">
  <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.4.2/css/all.min.css">
  <link rel="stylesheet" href="https://unpkg.com/aos@next/dist/aos.css" />
  <link rel="preconnect" href="https://fonts.googleapis.com">
  <link rel="preconnect" href="https://fonts.gstatic.com" crossorigin>
  <link href="https://fonts.googleapis.com/css2?family=Poppins:ital,wght@0,100;0,200;0,300;0,400;0,500;0,600;0,700;0,800;0,900;1,100;1,200;1,300;1,400;1,500;1,600;1,700;1,800;1,900&display=swap" rel="stylesheet">
  <title>Crowdfunding Platform</title>
</head>
<body class="index-page">
  <!-- header -->
  <?php include 'include/home_header.php'; ?>

  <!-- Hero Section -->
  <header class="hero section">
   
    <div class="container">
      <h1>Bring Your Ideas to Life</h1>
      <p class="lead">Join our community and fund innovative Campaigns</p>
      <a href="fundraisingform.php" class="h-button">Start Fundraiser</a>
    </div>
    <!-- image slider -->
    <div class="slider">
        <div class="slide">
            <!-- <img src="img/education.jpg" alt="Image 1"> -->
        </div>
        <div class="slide">
            <img src="img/Fhelp.jpg" alt="Image 2">
        </div>
        <div class="slide">
            <img src="img/sunrise.jpg" alt="Image 3">
        </div>
    </div>
    <a class="prev" onclick="changeSlide(-1)">&#10094;</a>
    <a class="next" onclick="changeSlide(1)">&#10095;</a>
</header>
  <!-- Featured Projects -->
  <section id="projects" class="py-5">
    <div class="container">
      <h2 class="text-left mb-4">Featured Campaigns</h2>
      <div class="row">
        <!-- Project Card -->
        <div data-aos="zoom-in-left" class="col-md-4">
          <div class="card shadow-sm">
            <img src="img/tablet.jpg" class="card-img-top" alt="Project Image" width="378" height="200">
            <div class="card-body">
              <h5 class="card-title">Campaign Title</h5>
              <p class="card-text">A short description of the project goes here. Exciting details entice users to fund the project!</p>
              <p><strong>Goal:</strong> ₹10,000</p>
              <a href="view_campaign.php" class="btn btn-primary mt-auto">View Campaign</a>
            </div>
          </div>
        </div>
        <?php
            include 'includes_db.php';

            // Check database connection
            if ($conn->connect_error) {
                die("Connection failed: " . $conn->connect_error);
            }

            // Fetch only approved campaigns
            $sql = "SELECT * FROM campaigns WHERE status = 'approved' ORDER BY created_at DESC";
            $result = $conn->query($sql);

            // Debugging information
            if ($result === FALSE) {
                echo "Error: " . $conn->error;
            }

            if ($result->num_rows > 0) {
                while ($row = $result->fetch_assoc()) {
                    echo "<div data-aos='zoom-in-left' class='col-md-4'>";
                    echo "<div class='card shadow-sm'>";
                    echo "<img src='" . $row['image_url'] . "' class='card-img-top' alt='Project Image' width='378' height='200'>";
                    echo "<div class='card-body'>";
                    echo "<h5 class='card-title'>" . $row['title'] . "</h5>";
                    echo "<p class='card-text'>" . $row['description'] . "</p>";
                    echo "<p><strong>Goal:</strong> ₹" . $row['goal_amount'] . "</p>";
                    echo "<a href='view_campaign.php?id=" . $row['id'] . "' class='btn btn-primary mt-auto'>View Campaign</a>";
                    echo "</div>";
                    echo "</div>";
                    echo "</div>";
                }
            } else {
                echo "<p>No approved campaigns found.</p>";
            }
            $conn->close();
        ?>

      </div>
    </div>
  </section>

  <!-- footer -->
  <?php include 'include/home_footer.php'; ?>

  <!-- script section -->
  <!-- <script src="https://cdnjs.cloudflare.com/ajax/libs/bootstrap/5.3.0/js/bootstrap.bundle.min.js"></script> -->
  <script src="js/script.js"></script>
  <script src="header.js"></script>
  <script src="https://unpkg.com/aos@next/dist/aos.js"></script>
  <script>
    AOS.init();
  </script>
</body>
</html>
