document.addEventListener("DOMContentLoaded", function () {

    const backLink = document.getElementById("backLink");
    const contactForm = document.getElementById("contactForm");

    // Safety check
    if (!contactForm) {
        console.error("contactForm not found");
        return;
    }

    // Dynamic back link
    const urlParams = new URLSearchParams(window.location.search);
    const from = urlParams.get("from");

    let homeUrl = "index.html";

    if (from === "student") {
        homeUrl = "student_dashboard.php";
    } else if (from === "company") {
        homeUrl = "company_dashboard.php";
    }

    if (backLink) {
        backLink.href = homeUrl;
        backLink.innerHTML = `<img src="assets/svg/cancel.svg" alt="Back">`;
    }

    // Modal elements
    const modal = document.getElementById("responseModal");
    const modalTitle = document.getElementById("modalTitle");
    const modalMessage = document.getElementById("modalMessage");
    const modalClose = document.getElementById("modalClose");
    const modalOkBtn = document.getElementById("modalOkBtn");

    function showModal(title, message, isSuccess) {
        if (!modal) return;

        modalTitle.textContent = title;
        modalMessage.textContent = message;
        modalTitle.style.color = isSuccess ? "green" : "red";

        modal.style.display = "block";
    }

    function closeModal() {
        if (modal) modal.style.display = "none";
    }

    if (modalClose) modalClose.addEventListener("click", closeModal);
    if (modalOkBtn) modalOkBtn.addEventListener("click", closeModal);

    // Form submit
    contactForm.addEventListener("submit", async function (e) {
        e.preventDefault();
        console.log("Form submission started");

        const formData = new FormData(contactForm);

        try {
            const response = await fetch("backend/contact_submit.php", {
                method: "POST",
                body: formData
            });

            console.log("HTTP Status:", response.status);

            if (!response.ok) {
                throw new Error("Server returned " + response.status);
            }

            const text = await response.text();
            console.log("Raw response:", text);

            let result;
            try {
                result = JSON.parse(text);
            } catch (err) {
                throw new Error("Invalid JSON from server");
            }

            if (result.success) {
                showModal("Success", result.message, true);
                contactForm.reset();
            } else {
                showModal("Error", result.message || "Something went wrong", false);
            }

        } catch (error) {
            console.error("Fetch error:", error);
            showModal("Error", error.message, false);
        }
    });

});