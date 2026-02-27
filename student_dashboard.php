<?php
require_once "backend/actions/auth/session_check.php";
?>

<!DOCTYPE html>
<html lang="en">

<head>
  <meta charset="UTF-8">
  <meta name="viewport" content="width=device-width, initial-scale=1.0">
  <title>Student Dashboard</title>
  <link rel="stylesheet" href="assets/css/student.css">
  <link rel="icon" type="image/png" href="assets/images/logo/Web Icon.png">
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
          <!-- <form class="search-form">
            <input type="text" placeholder="Search internships..." />
            <button type="submit"><img src="assets/svg/search.svg" alt=""></button>
          </form> -->
          <li><a href="student_dashboard.php">Home</a></li>
          <li><a href="student_profile.php">Profile</a></li>
          <li><a href="internship.html?from=student">Internships</a></li>
          <li><a href="applied_application.php">View applications</a></li>
          <li><a href="about.html?from=student">About</a></li>
          <li><a href="contact.html?from=student">Contact</a></li>

        </ul>
        <div>
          <a href="/virtual_internship_hub/backend//actions/auth/logout.php"> <button type="button" class="lgt">Logout</button></a>
        </div>
      </div>

      <div class="toggle" id="toggle">⋮</div>

    </nav>
  </header>

  <main>

    <!-- Hero Section -->
    <section class="welcome">
      <div class="welcome-container">
        <h1>Welcome, Students 👋</h1>
        <p>Glad to have you back! Explore internships, update your profile, and start your journey today.</p>
        <a href="internship.html?from=student" class="btn">Explore Internships</a>
      </div>
      <section class="student-actions">
        <h2>Quick Actions</h2>
        <div class="student-card-wrapper">

          <div class="student-card">
            <h3>My Profile</h3>
            <p>Update your personal details and resume.</p>
            <a href="student_profile.php">Go to Profile</a>
          </div>

          <div class="student-card">
            <h3>My Applications</h3>
            <p>Check the status of your internship applications.</p>
            <a href="applied_application.php">View Applications</a>
          </div>

          <div class="student-card">
            <h3>Find Internships</h3>
            <p>Search and apply for the latest internships.</p>
            <a href="internship.html?from=student">Explore</a>
          </div>


        </div>
      </section>
    </section>

    <section class="hero">
      <div class="hero-text">
        <h1>Kickstart Your Career with the Right Internship</h1>
        <p>Virtual Internship Hub makes it simple for students to learn, grow, and start their career journey.</p>
        <button id="find-internship" class="primary-btn">Find Internships</button>
      </div>
      <img src="assets/images/Hero section.jpg" alt="Internship Illustration">
    </section>


  </main>

  <footer class="site-footer">

  </footer>





  <script src="assets/js/toggle.js"></script>
  <script src="assets/js/internship.js"></script>

  <script>


    const find = document.getElementById('find-internship');

    find.addEventListener("click", () => {
      // Redirect with identifier (from=student)
      window.location.href = 'internship.html?from=student';
    });

    // Apply button alert
    document.querySelectorAll('.apply-btn').forEach(button => {
      button.addEventListener('click', function () {
        window.location.href = "apply.html?from=student";
      });
    });
  </script>

</body>

</html>