<?php
require_once "backend/actions/auth/session_check.php";
?>

<!DOCTYPE html>
<html lang="en">
<head>
  <meta charset="UTF-8" />
  <meta name="viewport" content="width=device-width, initial-scale=1.0" />
  <title>Apply for Internship</title>

  <link rel="icon" type="image/png" href="assets/images/logo/Web Icon.png" />
  <link rel="stylesheet" href="assets/css/apply.css" />
</head>
<body>

<!-- Back Button -->
<a id="backLink" class="back-btn" href="#">
  <img src="assets/svg/cancel.svg" alt="">
</a>

<div class="form-container">
  <h2>Apply for Internship</h2>

  <form id="applyForm">
    <input type="hidden" id="internship_id" name="internship_id">

    <div class="form-group">
      <label>Full Name</label>
      <input type="text" id="student_name" placeholder="Auto-filled from profile" readonly>
    </div>

    <div class="form-group">
      <label>Email</label>
      <input type="email" id="student_email" placeholder="Auto-filled from profile" readonly>
    </div>

    <div class="form-group">
      <label>Resume</label>
      <p class="info">Your profile resume will be used automatically</p>
    </div>

    <div class="form-group">
      <label>Cover Letter</label>
      <textarea id="cover" name="cover" placeholder="Why should we hire you?" required></textarea>
    </div>

    <button type="submit" class="submit-btn">Submit Application</button>
    <p id="formMsg" class="form-msg"></p>
  </form>
</div>

<script src="assets/js/apply.js"></script>
</body>
</html>
