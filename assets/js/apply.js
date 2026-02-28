// ===============================
// APPLY PAGE LOGIC
// ===============================
document.addEventListener("DOMContentLoaded", () => {

  /* =========================
TOAST FUNCTION
========================= */

function showToast(message, type = "success") {

  const container = document.getElementById("toastContainer");

  const toast = document.createElement("div");
  toast.className = "toast " + type;
  toast.innerText = message;

  container.appendChild(toast);

  setTimeout(() => toast.remove(), 3000);
}

/* =========================
BUTTON LOADING STATE
========================= */

function setButtonLoading(button, loading) {

  if (loading) {
    button.classList.add("loading");
    button.disabled = true;
    button.dataset.original = button.innerText;
    button.innerText = "Submitting...";
  } else {
    button.classList.remove("loading");
    button.disabled = false;
    button.innerText = button.dataset.original;
  }

}



  /* ---------------- BACK BUTTON ---------------- */
  const backLink = document.getElementById("backLink");

  backLink.addEventListener("click", e => {
    e.preventDefault();

    // Clean URL hash
    if (window.location.hash) {
      history.replaceState(null, null, window.location.href.split('#')[0]);
    }

    if (window.history.length > 1) {
      window.history.back();
    } else {
      const params = new URLSearchParams(window.location.search);
      const from = params.get("from");
      window.location.href =
        from === "student" ? "student_dashboard.php" : "index.html";
    }
  });

  /* ---------------- GET INTERNSHIP ID ---------------- */
  const params = new URLSearchParams(window.location.search);
  const internshipId = params.get("internship_id") || params.get("id"); // fallback to 'id'

  console.log("URL Params:", window.location.search);
  console.log("Internship ID:", internshipId);

  if (!internshipId) {
    alert("Invalid internship");
    window.location.href = "student_dashboard.php";
    return;
  }

  const hiddenInput = document.getElementById("internship_id");
  if (hiddenInput) hiddenInput.value = internshipId;

  /* ---------------- AUTO-FILL PROFILE INFO ---------------- */
  async function fetchProfile() {
    try {
      const res = await fetch("backend/student/get_profile.php");
      const data = await res.json();
      if (data.success) {
        const nameInput = document.getElementById("student_name");
        const emailInput = document.getElementById("student_email");
        if (nameInput) nameInput.value = data.name;
        if (emailInput) emailInput.value = data.email;
      } else {
        console.warn("Profile fetch failed:", data.message);
      }
    } catch (err) {
      console.error("Error fetching profile info:", err);
    }
  }
  fetchProfile();

  /* ---------------- APPLY FORM ---------------- */
  const form = document.getElementById("applyForm");
  const submitBtn = document.getElementById("submitBtn");

form.addEventListener("submit", async e => {

  e.preventDefault();


  const cover = document.getElementById("cover").value.trim();

  if (!cover) {
    // msg.textContent = "Cover letter is required";
    showToast("Cover letter is required", "error");
    return;
  }

  setButtonLoading(submitBtn, true);

  const formData = new FormData();
  formData.append("internship_id", internshipId);
  formData.append("cover", cover);

  try {

    const res = await fetch("backend/student/apply_internship.php", {
      method: "POST",
      body: formData
    });

    const data = await res.json();

    if (data.success) {

      showToast("Application submitted successfully", "success");

      // msg.textContent = "Applied Successfully.";

      form.reset();

      setTimeout(() => {
        window.location.href = "student_dashboard.php";
      }, 3000);

    }
    else {

      showToast(data.message || "Application failed", "error");

      // msg.textContent = data.message || "Application failed";

    }

  }
  catch (err) {

    showToast("Server error. Please try again.", "error");

    // msg.textContent = "Server error.";

  }
  finally {

    setButtonLoading(submitBtn, false);

  }

});

});
