/* =========================
LOADER CONTROL
========================= */

function showLoader() {
  const loader = document.getElementById("pageLoader");
  if (loader) loader.style.display = "flex";
}

function hideLoader() {
  const loader = document.getElementById("pageLoader");
  if (loader) loader.style.display = "none";
}

/* =========================
TOAST FUNCTION
========================= */

function showToast(message, type = "success") {

  const container = document.getElementById("toastContainer");

  const toast = document.createElement("div");
  toast.className = "toast " + type;
  toast.innerText = message;

  container.appendChild(toast);

  setTimeout(() => {
    toast.remove();
  }, 3000);
}


document.addEventListener("DOMContentLoaded", () => {
  initBackButton();
  loadInternships();
  initSearch();
});


/* =========================
   GLOBAL STATE
========================= */

let internshipData = [];
let isStudentLoggedIn = false;

/* =========================
SEARCH SYSTEM
========================= */

function initSearch() {

  const input = document.getElementById("searchInput");
  const button = document.getElementById("searchBtn");

  if (!input) return;

  /* Real-time search */
  input.addEventListener("input", performSearch);

  /* Button search */
  button.addEventListener("click", performSearch);

}


function performSearch() {

  const query =
    document.getElementById("searchInput")
      .value
      .toLowerCase()
      .trim();

  const grid = document.getElementById("internshipGrid");
  const emptyBox = document.getElementById("noInternship");

  grid.innerHTML = "";

  const filtered = internshipData.filter(item => {

    return (
      item.title.toLowerCase().includes(query) ||
      item.company_name.toLowerCase().includes(query) ||
      (item.skills && item.skills.toLowerCase().includes(query)) ||
      (item.department && item.department.toLowerCase().includes(query)) ||
      (item.location && item.location.toLowerCase().includes(query))
    );

  });

  if (filtered.length === 0) {

    emptyBox.style.display = "block";
    return;

  }

  emptyBox.style.display = "none";

  filtered.forEach(item => {

    grid.appendChild(createCard(item));

  });

}


/* =========================
   LOAD INTERNSHIPS
========================= */

async function loadInternships() {

  showLoader();

  const grid = document.querySelector(".internship-grid");
  const emptyBox = document.getElementById("noInternship");

  if (!grid) return;

  /* SHOW SKELETON FIRST */
  grid.innerHTML = "";

  for (let i = 0; i < 6; i++) {

    const skeleton = document.createElement("div");

    skeleton.className = "skeleton-card";

    skeleton.innerHTML = `
      <div class="skeleton-line long"></div>
      <div class="skeleton-line medium"></div>
      <div class="skeleton-line short"></div>
      <div class="skeleton-line long"></div>
    `;

    grid.appendChild(skeleton);

  }

  try {

    const response = await fetch(
      "/virtual_internship_hub/backend/student/get_internships.php",
      {
        credentials: "include"
      }
    );

    const result = await response.json();

    grid.innerHTML = "";

    internshipData = result.data || [];

    if (internshipData.length > 0)
      isStudentLoggedIn = internshipData[0].can_apply === true;

    if (internshipData.length === 0) {

      emptyBox.style.display = "block";
      return;

    }

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

    console.error(error);

    showToast("Failed to load internships", "error");

    emptyBox.style.display = "block";

  }
  finally {

    hideLoader();

  }

}


/* =========================
   CREATE CARD
========================= */

function createCard(data) {

  const isClosed =
    data.status &&
    data.status.toLowerCase() === "closed";

  const workType =
    data.type &&
    data.type.toLowerCase() === "remote"
      ? "remote"
      : "onsite";

  const card = document.createElement("div");

  card.className = "internship-card";

  /* Skills formatting */
  let skillsHTML = "";

  if (data.skills) {

    const skills = data.skills.split(",");

    skillsHTML = skills
      .map(skill =>
        `<span class="skill-badge">${skill.trim()}</span>`
      )
      .join("");

  }

  /* Description preview */
  const description =
    data.description
      ? data.description.substring(0, 120) + "..."
      : "No description available";

  card.innerHTML = `

    <div class="card-header">

      <div class="company-logo">
        ${getInitials(data.company_name || "VIH")}
      </div>

      <div>
        <h3>${data.title}</h3>
        <div class="company-name">
          ${data.company_name}
        </div>
      </div>

    </div>


    <div class="card-body">

      <div class="card-meta">

        <span class="meta-item">
          🏢 ${data.department || "General"}
        </span>

        <span class="meta-item tag ${workType}">
          ${workType === "remote"
            ? "Remote"
            : "On Site"}
        </span>

      </div>


      <div class="card-description">
      <span>Description:</span>
        ${description}
      </div>


      <div class="skills-container">
        ${skillsHTML}
      </div>


      <div class="card-details">

        <div class="detail-item">
          ⏳ ${data.duration_weeks} weeks
        </div>

        <div class="detail-item">
          💰 ₹${data.stipend}/month
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
          ? "Closed"
          : "Apply Now"}

      </button>

    </div>

  `;

card.addEventListener("click", (e) => {

  /* ignore Apply button */
  if (e.target.closest(".apply-btn")) return;

  /* ignore back button */
  if (e.target.closest(".back-btn")) return;

  /* ignore links */
  if (e.target.tagName === "A") return;

  window.location.href =
    "internship-details.html?id=" + data.id;

});

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

        if (this.disabled) return;

        this.classList.add("loading");

        const internshipId = this.dataset.id;

        if (!isStudentLoggedIn) {

          this.classList.remove("loading");

          showLoginModal();
          return;

        }

        setTimeout(() => {

          window.location.href =
            "apply.php?id=" + internshipId;

        }, 500);

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