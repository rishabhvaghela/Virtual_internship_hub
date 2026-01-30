document.addEventListener("DOMContentLoaded", () => {
  initBackButton();
  loadInternships();
  initFilters();
});

/* =========================
   LOAD INTERNSHIPS
========================= */
function loadInternships() {
  fetch("backend/student/get_internships.php")
  
    .then(res => {
      if (!res.ok) throw new Error("Network error");
      return res.json();
    })
    .then(result => {
      const grid = document.querySelector(".internship-grid");
      const emptyBox = document.getElementById("noInternship");

      if (!grid) return;
      grid.innerHTML = "";

      if (result.status !== "SUCCESS" || result.count === 0) {
        if (emptyBox) emptyBox.style.display = "block";
        return;
      }

      if (emptyBox) emptyBox.style.display = "none";

      result.data.forEach((item, index) => {
        const card = createCard(item);
        grid.appendChild(card);

        setTimeout(() => {
          card.classList.add("visible");
        }, index * 100);
      });

      initApplyButtons();
    })
    .catch(err => {
      console.error("Internship load failed:", err);
      const emptyBox = document.getElementById("noInternship");
      if (emptyBox) emptyBox.style.display = "block";
    });
    
}

/* =========================
   CREATE CARD
========================= */
function createCard(data) {
  const card = document.createElement("div");
  card.className = "internship-card";

  const workType = data.type?.toLowerCase() === "remote" ? "remote" : "onsite";

  card.dataset.type = workType;
  card.dataset.category = data.department?.toLowerCase() || "general";

  card.innerHTML = `
    <div class="card-header">
      <div class="company-logo">
        ${getInitials(data.company_name || "VIH")}
      </div>

      <div class="card-tags">
        <span class="tag ${workType}">
          ${workType === "remote" ? "Work From Home" : "On Site"}
        </span>
        <span class="tag paid">Paid</span>
        <span class="tag">${data.skills || "General"}</span>
      </div>
    </div>

    <div class="card-body">
      <h3>${data.title}</h3>
      <span class="company-name">${data.company_name}</span>

      <div class="card-details">
        <div class="detail-item">
          ⏳ ${data.duration_weeks} Weeks
        </div>
        <div class="detail-item">
          💰 ₹${data.stipend}
        </div>
      </div>
    </div>

    <div class="card-footer">
      <button class="apply-btn" data-id="${data.id}">
        Apply Now
      </button>
    </div>
  `;

  return card;
}


/* =========================
   FILTERS
========================= */
function initFilters() {
  document.querySelectorAll(".filter-btn").forEach(button => {
    button.addEventListener("click", function () {
      document.querySelectorAll(".filter-btn").forEach(btn =>
        btn.classList.remove("active")
      );
      this.classList.add("active");

      const filterValue = this.dataset.filter;
      const cards = document.querySelectorAll(".internship-card");
      let visibleCount = 0;

      cards.forEach(card => {
        const type = card.dataset.type;
        const category = card.dataset.category;

        if (
          filterValue === "all" ||
          filterValue === type ||
          filterValue === category
        ) {
          card.style.display = "flex";
          visibleCount++;
        } else {
          card.style.display = "none";
        }
      });

      const emptyBox = document.getElementById("noInternship");
      if (emptyBox) {
        emptyBox.style.display = visibleCount === 0 ? "block" : "none";
      }
    });
  });
}

/* =========================
   APPLY BUTTONS
========================= */
function initApplyButtons() {
  const cameFromHome = document.referrer.includes("index.html");

  document.querySelectorAll(".apply-btn").forEach(btn => {
    btn.addEventListener("click", function () {
      if (cameFromHome) {
        alert("⚠️ Please login as a student before applying!");
        return;
      }

      const internshipId = this.dataset.id;
      window.location.href =
        "apply.html?internship_id=" + internshipId + "&from=internship";
    });
  });
}

/* =========================
   BACK BUTTON
========================= */
function initBackButton() {
  const backLink = document.getElementById("backLink");
  if (!backLink) return;

  backLink.addEventListener("click", e => {
    e.preventDefault();

    // If history exists → go back
    if (window.history.length > 1) {
      window.history.back();
    } else {
      // fallback (direct access case)
      const params = new URLSearchParams(window.location.search);
      const from = params.get("from");
      window.location.href =
        from === "student" ? "student_dashboard.html" : "index.html";
    }
  });
}


/* =========================
   HELPERS
========================= */
function getInitials(text) {
  return text
    .trim()
    .split(" ")
    .map(w => w[0])
    .join("")
    .substring(0, 2)
    .toUpperCase();
}
