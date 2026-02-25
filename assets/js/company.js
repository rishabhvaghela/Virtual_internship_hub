  const view_applicants = document.getElementById('view-applicants');
const post_internship = document.getElementById('post-internship');

view_applicants.addEventListener('click', ()=>{
  window.location.href = "view_applicants.php";
})

post_internship.addEventListener('click', ()=>{
  window.location.href = "post_internship.php";
})


document.addEventListener("DOMContentLoaded", () => {
  loadMyInternships();

  const updateForm = document.getElementById("updateForm");
  if (updateForm) {
    updateForm.addEventListener("submit", updateInternship);
  }
});

/* =========================
   LOAD MY INTERNSHIPS
========================= */
function loadMyInternships() {
  fetch("backend/company/get_my_internships.php")
    .then(res => res.json())
    .then(result => {
      const tbody = document.getElementById("myInternshipsBody");
      tbody.innerHTML = "";

      if (result.status !== "SUCCESS" || !result.data.length) {
        tbody.innerHTML = `
          <tr>
            <td colspan="4" style="text-align:center;">No internships posted yet</td>
          </tr>`;
        return;
      }

      result.data.forEach(item => {
        const row = document.createElement("tr");
        row.innerHTML = `
          <td>${item.title}</td>
          <td>${item.applicants}</td>
          <td>
            <span class="status ${item.status === "open" ? "active" : "closed"}">
              ${item.status}
            </span>
          </td>
          <td>
            <button class="table-btn edit" data-id="${item.id}">Edit</button>
            <button class="table-btn delete" data-id="${item.id}">Delete</button>
          </td>`;
        tbody.appendChild(row);
      });
    });
}

/* =========================
   EDIT + DELETE
========================= */
document.addEventListener("click", e => {

  /* ----- EDIT ----- */
if (e.target.classList.contains("edit")) {
  e.preventDefault();
  e.stopPropagation();

  const id = e.target.dataset.id;

  fetch(`backend/company/get_internship.php?id=${id}`)
    .then(res => res.json())
    .then(res => {
      if (res.status !== "SUCCESS") {
        alert("Failed to load internship");
        return;
      }

      const d = res.data;
      document.getElementById("internshipId").value = d.id;
      document.getElementById("title").value = d.title;
      document.getElementById("department").value = d.department;
      document.getElementById("location").value = d.location;
      document.getElementById("startDate").value = d.start_date;
      document.getElementById("duration").value = d.duration_weeks;
      document.getElementById("stipend").value = d.stipend;
      document.getElementById("skills").value = d.skills;
      document.getElementById("description").value = d.description;

      document.getElementById("editModal").style.display = "flex";
    });
}


  /* ----- DELETE ----- */
  if (e.target.classList.contains("delete")) {
    const id = e.target.dataset.id;
    if (!confirm("Delete this internship?")) return;

    const fd = new FormData();
    fd.append("internship_id", id);

    fetch("backend/company/delete_internship.php", {
      method: "POST",
      body: fd
    })
    .then(res => res.json())
    .then(res => {
      if (res.status === "DELETED") {
        e.target.closest("tr").remove();
      }
    });
  }
});

/* =========================
   UPDATE SUBMIT
========================= */
function updateInternship(e) {
  e.preventDefault();

  const data = {
    id: document.getElementById("internshipId").value,
    title: document.getElementById("title").value,
    department: document.getElementById("department").value,
    location: document.getElementById("location").value,
    startDate: document.getElementById("startDate").value,
    endDate: document.getElementById("endDate").value, 
    duration: document.getElementById("duration").value,
    stipend: document.getElementById("stipend").value,
    skills: document.getElementById("skills").value,
    description: document.getElementById("description").value
  };

  fetch("/virtual_internship_hub/backend/company/update_internship.php", {
    method: "POST",
    headers: { "Content-Type": "application/json" },
    body: JSON.stringify(data)
  })
  .then(res => res.json())
  .then(res => {
    if (res.status === "UPDATED") {
      alert("Internship updated successfully");
      document.getElementById("editModal").style.display = "none";
      loadMyInternships();
    } else {
      alert(res.message);
    }
  });
}


