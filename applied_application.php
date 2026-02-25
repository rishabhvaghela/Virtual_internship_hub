<?php
require_once "backend/actions/auth/session_check.php";
?>

<!DOCTYPE html>
<html lang="en">
<head>
  <meta charset="UTF-8" />
  <meta name="viewport" content="width=device-width, initial-scale=1.0" />
  <title>My Applications</title>
  <link rel="icon" type="image/png" href="assets/images/logo/Web Icon.png" />
  <!-- Font Awesome Icons / means class me fa-house likhne se home ka icon -->
  <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.5.0/css/all.min.css" />
  <link rel="stylesheet" href="assets/css/applied.css" />
</head>
<body>

        <!-- Dynamic Back Button -->
    <a id="backLink" class="back-btn" href="student_dashboard.php">
    <img src="assets/svg/cancel.svg" alt="">
    </a>


  <section class="saved-internships">
    <h2>My Applications</h2>

   <!-- Desktop: Table -->
<table class="applications-table">
  <thead>
    <tr>
      <th>SR. NO</th>
      <th>TITLE</th>
      <th>STATUS</th>
      <th>INTERVIEW</th>
    </tr>
  </thead>
  <tbody id="applicationsTableBody">
    <!-- Dynamic rows here -->
  </tbody>
</table>

<!-- Mobile: Cards -->
<div class="applications-cards" id="applicationsCards">
  <!-- Dynamic cards here -->
</div>

<script src="assets/js/applied_application.js"></script>

  </section>
</body>
</html>