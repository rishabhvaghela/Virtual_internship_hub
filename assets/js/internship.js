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