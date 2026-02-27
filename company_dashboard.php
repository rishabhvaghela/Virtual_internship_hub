<?php
require_once "backend/actions/auth/session_check.php";
?>
<!DOCTYPE html>
<html lang="en">

<head>
  <meta charset="UTF-8">
  <meta name="viewport" content="width=device-width, initial-scale=1.0">
  <title>Company dasshboard</title>

  <link rel="stylesheet" href="assets/css/company.css">
  <link rel="stylesheet" href="assets/css/theme.css">
</head>

<body>
  <header>
    <!-- Nav Section -->
    <nav>
      <div class="logo">
        <img src="assets/images/logo/Virtual Internship Hub.jpeg" alt=""> Virtual Internship Hub
      </div>
      <div class="menu" id="menu">
        <ul>
          <li><a href="company_dashboard.php">Home</a></li>
          <li><a href="company_profile.html">Profile</a></li>
          <li><a href="post_internship.php">Post Internships</a></li>
          <li><a href="view_applicants.php">View Applicants</a></li>
          <li><a href="about.html?from=company">About</a></li>
          <li><a href="contact.html?from=company">Contact</a></li>

        </ul>
        <div>
          <a href="/virtual_internship_hub/backend/actions/auth/logout.php"> <button type="button" class="lgt">Logout</button></a>
        </div>
      </div>

      <div class="toggle" id="toggle">Ã¢â€¹Â®</div>

    </nav>
  </header>

  <main>
    <!-- Welcome Section -->
    <section class="company-welcome">
      <div class="welcome-container">
        <h1>Welcome, <span class="highlight">Company Name</span>!</h1>
        <p class="welcome-text">
          Manage your internships, track applicants, and update your company profile all in one place.
        </p>
      </div>
    </section>
    <!-- Quick Action Cards -->
    <section class="company-actions">
      <h2 class="section-title">Quick Actions</h2>
      <div class="company-card-container">

        <!-- Post Internship -->
        <div class="company-card">
          <div class="icon">
            <img src="assets/svg/plus.svg" alt="">
          </div>
          <h3 id="post_internship">Post New Internship</h3>
          <p>Create and publish a new internship opportunity.</p>
          <button id="post-internship" class="card-btn">Post Internship</button>
        </div>

        <!-- View Applicants -->
        <div class="company-card">
          <div class="icon">
            <img src="assets/svg/user.svg" alt="">
          </div>
          <h3>View Applicants</h3>
          <p>Check students who have applied to your internships.</p>
          <button id="view-applicants" class="card-btn">View Applicants</button>
        </div>
      </div>
    </section>
    <!-- Internships List / Table -->
    <section class="internship-list">
      <h2 class="section-title">My Internships</h2>
      <table class="internship-table">
        <thead>
          <tr>
            <th>Internship Title</th>
            <th>Applicants</th>
            <th>Status</th>
            <th>Actions</th>
          </tr>
        </thead>
        <tbody id="myInternshipsBody">
          <!-- JS will inject rows here -->
        </tbody>
      </table>
    </section>


  </main>

  <!-- EDIT INTERNSHIP MODAL -->
<div id="editModal" class="modal">
  <div class="modal-content">

    <h3>Edit Internship</h3>

    <form id="updateForm" action="/virtual_internship_hub/backend/company/update_internship.php" method="POST">
      <input type="hidden" id="internshipId">

      <input type="text" name="title" id="title" placeholder="Internship Title" required>
      <input type="text" name="department" id="department" placeholder="Department" required>

      <select name="location" id="location">
        <option value="remote">Remote</option>
      </select>

      <input type="date" name="startDate" id="startDate" required>
      <input type="date" name="endDate" id="endDate" required>
      <input type="text" name="duration" id="duration" placeholder="Duration" required>
      <input type="text" name="stipend" id="stipend" placeholder="Stipend">

      <input type="text" name="skills" id="skills" placeholder="Skills">
      <textarea name="description" id="description" placeholder="Description"></textarea>

     <button type="submit" class="table-btn save">Update</button>

    </form>

  </div>
</div>


  <footer></footer>

  <script src="assets/js/toggle.js"></script>
<script src="assets/js/company.js"></script>
</body>

</html>