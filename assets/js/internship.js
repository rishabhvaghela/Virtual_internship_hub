// Animate cards on load
document.addEventListener("DOMContentLoaded", function () {
  setTimeout(() => {
    document.querySelectorAll('.internship-card').forEach((card, index) => {
      setTimeout(() => {
        card.classList.add('visible');
      }, index * 100);
    });
  }, 300);

  // Filter functionality
  document.querySelectorAll('.filter-btn').forEach(button => {
    button.addEventListener('click', function () {
      // Set active state
      document.querySelectorAll('.filter-btn').forEach(btn => btn.classList.remove('active'));
      this.classList.add('active');

      // Get filter value
      const filterValue = this.getAttribute('data-filter');

      // Get all cards
      const cards = document.querySelectorAll('.internship-card');

      // Loop through cards and show/hide based on filter
      cards.forEach(card => {
        const type = card.getAttribute('data-type');
        const category = card.getAttribute('data-category');

        if (
          filterValue === 'all' ||
          (filterValue === type) ||
          (filterValue === category)
        ) {
          card.style.display = 'flex';
        } else {
          card.style.display = 'none';
        }
      });
    });
  });

  // ✅ Detect if user came from index.html
  const cameFromHome = document.referrer.includes("index.html");

  // Apply button functionality
  document.querySelectorAll('.apply-btn').forEach(button => {
    button.addEventListener('click', function () {

      // If came from home, show alert and stop redirect
      if (cameFromHome) {
        alert("⚠️ Please login as a student before applying!");
        // optional: redirect to login page
        // window.location.href = "login.html";
        return;
      }

      // Otherwise go to apply page
      window.location.href = "apply.html?from=internship";
    });
  });
});


// Back button logic
document.addEventListener("DOMContentLoaded", function () {
  const backLink = document.getElementById("backLink");

  // Get 'from' value from the URL
  const urlParams = new URLSearchParams(window.location.search);
  const from = urlParams.get('from');

  // Default back location
  let backUrl = "index.html";

  // If user came from student dashboard
  if (from === "student") {
    backUrl = "student_dashboard.html";
  }

  // Set the cancel/back button destination
  backLink.href = backUrl;

  // Optional confirmation before going back
  backLink.addEventListener("click", function (e) {
    e.preventDefault();
    window.location.href = backUrl;
  });
});
