document.addEventListener("DOMContentLoaded", async () => {

  const tableBody = document.getElementById("applicationsTableBody");
  const cardsContainer = document.getElementById("applicationsCards");

  try {
    const res = await fetch("backend/student/get_my_applications.php");
    const data = await res.json();

    if (!data.success) {
      tableBody.innerHTML = `<tr><td colspan="4">${data.message}</td></tr>`;
      cardsContainer.innerHTML = `<p>${data.message}</p>`;
      return;
    }

    if (data.data.length === 0) {
      tableBody.innerHTML = `
        <tr>
          <td colspan="4">You have not applied to any internships yet.</td>
        </tr>
      `;
      cardsContainer.innerHTML = `
        <div class="empty-state">
          <p>You have not applied to any internships yet.</p>
        </div>
      `;
      return;
    }

    let tableHTML = "";
    let cardsHTML = "";

    data.data.forEach((app, index) => {

      const statusClass = app.status.toLowerCase().replace("_", "-");

      const interviewDate = app.interview_date
        ? new Date(app.interview_date).toLocaleString()
        : "—";

      // ======================
      // DESKTOP TABLE
      // ======================

      tableHTML += `
        <tr>
          <td>${index + 1}</td>
          <td>${app.title}</td>
          <td>
            <span class="status ${statusClass}">
              ${app.status.replace("_", " ")}
            </span>
          </td>
          <td>${interviewDate}</td>
        </tr>
      `;

      // ======================
      // MOBILE CARDS
      // ======================

      cardsHTML += `
        <div class="application-card">

          <div class="card-row">
            <span class="card-label">SR. NO</span>
            <span class="card-value">${index + 1}</span>
          </div>

          <div class="card-row">
            <span class="card-label">Title</span>
            <span class="card-value">${app.title}</span>
          </div>

          <div class="card-row">
            <span class="card-label">Status</span>
            <span class="card-value">
              <span class="status ${statusClass}">
                ${app.status.replace("_", " ")}
              </span>
            </span>
          </div>

          <div class="card-row">
            <span class="card-label">Interview</span>
            <span class="card-value">${interviewDate}</span>
          </div>

        </div>
      `;
    });

    tableBody.innerHTML = tableHTML;
    cardsContainer.innerHTML = cardsHTML;

  } catch (err) {
    console.log("JS Error:", err);
    tableBody.innerHTML = `
      <tr>
        <td colspan="4">Server error. Please try again.</td>
      </tr>
    `;
    cardsContainer.innerHTML = `<p>Server error. Please try again.</p>`;
  }

});