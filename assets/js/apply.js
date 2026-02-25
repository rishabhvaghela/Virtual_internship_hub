// ===============================
// APPLY PAGE LOGIC
// ===============================
document.addEventListener("DOMContentLoaded", () => {

  console.log("apply.js loaded");

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
  const msg  = document.getElementById("formMsg");

  form.addEventListener("submit", async e => {
    e.preventDefault();
    msg.textContent = "";

    const cover = document.getElementById("cover").value.trim();

    if (!cover) {
      msg.textContent = "Cover letter is required";
      return;
    }

    const formData = new FormData();
    formData.append("internship_id", internshipId);
    formData.append("cover", cover);

    console.log("Submitting form:", { internshipId, cover });

    try {
      const res = await fetch("backend/student/apply_internship.php", {
        method: "POST",
        body: formData
      });

      // Safely parse JSON
      let data;
      try {
        data = await res.json();
      } catch (jsonErr) {
        console.error("JSON parse error:", jsonErr);
        msg.textContent = "Server returned invalid response. Please try again.";
        return;
      }

      console.log("Server response:", data);

      if (data.success) {
        msg.textContent = "Applied Successfully.";
      } else {
        msg.textContent = data.message || "Application failed";
      }

    } catch (err) {
      console.error("Fetch error:", err);
      msg.textContent = "Server error. Please try again.";
    }
  });

});
