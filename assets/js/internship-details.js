const params = new URLSearchParams(window.location.search);
const internshipId = params.get("id");

const container = document.getElementById("detailsCard");

if (!internshipId) {

  container.innerHTML = "Invalid internship";
  throw new Error("No ID");

}

loadDetails();

async function loadDetails() {

  try {

    const res = await fetch(
      `/virtual_internship_hub/backend/student/get_single_internship.php?id=${internshipId}`
    );

    const result = await res.json();

    if (result.status !== "SUCCESS") {

      container.innerHTML = "Internship not found";
      return;

    }

    const data = result.data;

    render(data);

  } catch {

    container.innerHTML = "Error loading details";

  }

}

function render(data) {

  container.innerHTML = `

    <h1>${data.title}</h1>

    <h3>${data.company_name}</h3>

    <p><b>Department:</b> ${data.department}</p>

    <p><b>Location:</b> ${data.location}</p>

    <p><b>Type:</b> ${data.type}</p>

    <p><b>Duration:</b> ${data.duration_weeks} weeks</p>

    <p><b>Stipend:</b> ₹${data.stipend}/month</p>

    <p><b>Skills:</b> ${data.skills}</p>

    <p><b>Description:</b></p>

    <p>${data.description}</p>

    <br>

    <button onclick="apply(${data.id})">
      Apply Now
    </button>

  `;

}

function apply(id) {

  window.location.href = "apply.php?id=" + id;

}

document.getElementById("backBtn")
.addEventListener("click", function(e){

  e.preventDefault();

  if (history.length > 1)
    history.back();
  else
    window.location.href = "internship.html";

});
