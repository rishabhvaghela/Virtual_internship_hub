<?php
require_once "backend/actions/auth/session_check.php";
?>

<!DOCTYPE html>
<html lang="en">
<head>
  <meta charset="UTF-8" />
  <meta name="viewport" content="width=device-width, initial-scale=1.0"/>
  <title>Company Profile</title>

  <link rel="icon" type="image/png" href="assets/images/logo/Web Icon.png">
  <link rel="stylesheet" href="assets/css/company-profile.css" />
</head>
<body>

  <!-- Back Button -->
  <a class="back-btn" href="company_dashboard.php">
    <img src="assets/svg/cancel.svg" alt="Back">
  </a>

  <div class="container">
    <h1>Company Profile</h1>

<form id="companyForm">

  <div class="form-group">
    <label>Company Name</label>
    <input type="text" id="company_name" name="company_name" >
  </div>

  <div class="form-group">
    <label>Email</label>
    <input type="email" id="email" name="email" readonly>
  </div>

  <div class="form-group">
    <label>Industry</label>
    <input type="text" id="industry" name="industry" >
  </div>

  <div class="form-group">
    <label>Description</label>
    <textarea id="description" name="description" ></textarea>
  </div>

  <div class="btn-group">
    <button type="button" id="editBtn">Edit</button>
    <button type="submit" id="saveBtn" style="display:none;">Save</button>
  </div>

</form>
  </div>
  

<!-- SUCCESS MODAL -->
<div id="successModal" class="modal-overlay" style="display: none;">
  <div class="modal-box">
    <h2>Success</h2>
    <p>Company profile updated successfully.</p>
    <button id="closeModal">OK</button>
  </div>
</div>




<script src="assets/js/company-profile.js"></script>
</body>
</html>
