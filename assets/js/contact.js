 document.addEventListener("DOMContentLoaded", function() {
            const backLink = document.getElementById("backLink");
            const urlParams = new URLSearchParams(window.location.search);
            const from = urlParams.get('from');

            let homeUrl = "index.html"; // Default fallback

            // Determine where to go back based on 'from' parameter
            if (from === 'student') {
                homeUrl = "student_dashboard.html";
            } else if (from === 'company') {
                homeUrl = "company_dashboard.html";
            } else if (from === 'home') {
                homeUrl = "index.html";
            }

            // Set the back button's destination and label
            backLink.href = homeUrl;
            backLink.textContent = '<img src="assets/svg/cancel.svg">' `${
                homeUrl === 'index.html' ? 'Home' :
                homeUrl === 'student_dashboard.html' ? 'Student Dashboard' :
                homeUrl === 'company_dashboard.html' ? 'Company Dashboard' :
                'Home' // Final fallback
            }`;

            // Optional: Handle form submission (you can connect to backend later)
            const contactForm = document.getElementById("contactForm");
            contactForm.addEventListener("submit", function(e) {
                e.preventDefault();
                alert("Thank you! Your message has been sent. We’ll reply soon.");
                contactForm.reset();
            });
        });