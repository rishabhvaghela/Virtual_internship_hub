document.addEventListener("DOMContentLoaded", () => {
  initBackButton();
  loadInternships();
  initFilters();
});


/* =========================
   GLOBAL STATE
========================= */

let internshipData = [];
let isStudentLoggedIn = false;


/* =========================
   LOAD INTERNSHIPS
========================= */

async function loadInternships() {

  const grid = document.querySelector(".internship-grid");
  const emptyBox = document.getElementById("noInternship");

  try {

    const response = await fetch(
      "/virtual_internship_hub/backend/student/get_internships.php",
      {
        method: "GET",
        credentials: "include",
        headers: {
          "Accept": "application/json"
        }
      }
    );

    if (!response.ok)
      throw new Error("Server error");

    const result = await response.json();

    console.log("Internship API:", result);

    if (!grid) return;

    grid.innerHTML = "";

    internshipData = result.data || [];

    // ✅ detect login state
    if (internshipData.length > 0) {
      isStudentLoggedIn = internshipData[0].can_apply === true;
    }

    if (internshipData.length === 0) {

      if (emptyBox)
        emptyBox.style.display = "block";

      return;
    }

    if (emptyBox)
      emptyBox.style.display = "none";


    internshipData.forEach((item, index) => {

      const card = createCard(item);

      grid.appendChild(card);

      setTimeout(() => {
        card.classList.add("visible");
      }, index * 100);

    });


    initApplyButtons();

  }
  catch (error) {

    console.error("Load error:", error);

    if (emptyBox)
      emptyBox.style.display = "block";

  }

}


/* =========================
   CREATE CARD
========================= */

function createCard(data) {

  const isClosed =
    data.status &&
    data.status.toLowerCase() === "closed";

  const card = document.createElement("div");

  card.className = "internship-card";

  const workType =
    data.type &&
    data.type.toLowerCase() === "remote"
      ? "remote"
      : "onsite";

  card.dataset.type = workType;

  card.dataset.category =
    data.department
      ? data.department.toLowerCase()
      : "general";


  card.innerHTML = `
    
    <div class="card-header">

      <div class="company-logo">
        ${getInitials(data.company_name || "VIH")}
      </div>

      <div class="card-tags">

        <span class="tag ${workType}">
          ${workType === "remote"
            ? "Work From Home"
            : "On Site"}
        </span>

        <span class="tag paid">Paid</span>

        <span class="tag">
          ${data.skills || "General"}
        </span>

      </div>

    </div>


    <div class="card-body">

      <h3>${data.title}</h3>

      <span class="company-name">
        ${data.company_name}
      </span>


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

      <button 
        class="apply-btn"
        data-id="${data.id}"
        ${isClosed ? "disabled" : ""}
      >

        ${isClosed
          ? "Internship Closed"
          : "Apply"}

      </button>

    </div>

  `;

  return card;
}


/* =========================
   APPLY BUTTON
========================= */

function initApplyButtons() {

  document
    .querySelectorAll(".apply-btn")
    .forEach(btn => {

      btn.addEventListener("click", function () {

        if (this.disabled)
          return;

        const internshipId = this.dataset.id;

        // ❌ NOT LOGGED IN → SHOW MODAL
        if (!isStudentLoggedIn) {

          showLoginModal();

          return;
        }

        // ✅ LOGGED IN → APPLY PAGE
        window.location.href =
          "apply.php?id=" + internshipId;

      });

    });

}


/* =========================
   LOGIN MODAL
========================= */

function showLoginModal() {

  let modal = document.getElementById("loginModal");

  if (!modal) {

    modal = document.createElement("div");

    modal.id = "loginModal";

    modal.innerHTML = `
    
      <div class="login-modal-overlay">

        <div class="login-modal-box">

          <h2>Login Required</h2>

          <p>Please login as student to apply</p>

          <button onclick="goToLogin()">Login Now</button>

          <button onclick="closeLoginModal()">Cancel</button>

        </div>

      </div>
    
    `;

    document.body.appendChild(modal);

  }

  modal.style.display = "block";

}


function closeLoginModal() {

  const modal =
    document.getElementById("loginModal");

  if (modal)
    modal.style.display = "none";

}


function goToLogin() {

  window.location.href =
    "/virtual_internship_hub/login.html";

}


/* =========================
   FILTERS
========================= */

function initFilters() {

  document
    .querySelectorAll(".filter-btn")
    .forEach(button => {

      button.addEventListener("click", function () {

        document
          .querySelectorAll(".filter-btn")
          .forEach(btn =>
            btn.classList.remove("active")
          );

        this.classList.add("active");

        const filterValue =
          this.dataset.filter;

        const cards =
          document.querySelectorAll(
            ".internship-card"
          );

        let visibleCount = 0;

        cards.forEach(card => {

          const type =
            card.dataset.type;

          const category =
            card.dataset.category;

          if (
            filterValue === "all" ||
            filterValue === type ||
            filterValue === category
          ) {
            card.style.display = "flex";
            visibleCount++;
          }
          else {
            card.style.display = "none";
          }

        });

        const emptyBox =
          document.getElementById("noInternship");

        if (emptyBox)
          emptyBox.style.display =
            visibleCount === 0
              ? "block"
              : "none";

      });

    });

}


/* =========================
   BACK BUTTON
========================= */

function initBackButton() {

  const backLink =
    document.getElementById("backLink");

  if (!backLink)
    return;

  backLink.addEventListener("click", e => {

    e.preventDefault();

    if (window.history.length > 1)
      window.history.back();

    else
      window.location.href =
        "index.html";

  });

}


/* =========================
   HELPER
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