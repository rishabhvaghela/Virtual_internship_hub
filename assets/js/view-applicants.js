document.addEventListener("DOMContentLoaded", async () => {

  const container = document.getElementById("applicantsContainer");

  async function loadApplicants() {
    try {
      const res = await fetch("backend/company/view_applicants.php");
      const result = await res.json();

      container.innerHTML = "";

      if (!result.success) {
        container.innerHTML = `<p>${result.message}</p>`;
        return;
      }

      if (result.data.length === 0) {
        container.innerHTML = `<p>No applicants yet.</p>`;
        return;
      }

      result.data.forEach(app => {

        const card = document.createElement("div");
        card.classList.add("applicant-card");

        card.innerHTML = `
            <div class="applicant-name">${app.student_name}</div>

            <div class="applicant-role">
                Applied for: ${app.internship_title}
            </div>

            <div class="applicant-detail">
                <span>Email:</span>
                <span>${app.student_email}</span>
            </div>

            <div class="applicant-detail">
                <span>Status:</span>
                <span class="status-badge status ${app.status}">
                    ${app.status}
                </span>
            </div>

            ${
              app.scheduled_at
                ? `
                <div class="applicant-detail">
                    <span>Interview:</span>
                    <span>${new Date(app.scheduled_at).toLocaleString()}</span>
                </div>
                `
                : ""
            }

            <div class="action-buttons">
                <button class="action-btn btn-view-resume">
                    View Resume
                </button>

                <button class="action-btn btn-cover">
                    Cover Letter
                </button>

${
  app.status !== "rejected"
    ? `<button class="action-btn btn-interview">
        ${app.scheduled_at ? "Reschedule Interview" : "Schedule Interview"}
       </button>`
    : ""
}

                <button class="action-btn btn-accept">
                    Accept
                </button>

                <button class="action-btn btn-reject">
                    Reject
                </button>
            </div>
        `;

        // Resume
        card.querySelector(".btn-view-resume").addEventListener("click", () => {
          window.open(
            "backend/company/download_resume.php?file=" + app.resume,
            "_blank"
          );
        });

        // Cover Letter
        card.querySelector(".btn-cover").addEventListener("click", () => {
          showCoverModal(app.cover_letter);
        });

        // Accept
        card.querySelector(".btn-accept").addEventListener("click", () => {
          updateStatus(app.application_id, "accepted");
        });

        // Reject
        card.querySelector(".btn-reject").addEventListener("click", () => {
          updateStatus(app.application_id, "rejected");
        });

        // Schedule Interview
        if (app.status !== "rejected") {
          card.querySelector(".btn-interview").addEventListener("click", () => {
            showInterviewModal(app.application_id);
          });
        }

        container.appendChild(card);
      });

    } catch (error) {
      container.innerHTML = `<p>Server error.</p>`;
    }
  }

  async function updateStatus(applicationId, status) {
    const res = await fetch("backend/company/update_application_status.php", {
      method: "POST",
      headers: { "Content-Type": "application/json" },
      body: JSON.stringify({
        application_id: applicationId,
        status: status
      })
    });

    const result = await res.json();

    if (result.success) {
      showStatusModal(
        status === "accepted" ? "success" : "reject",
        status === "accepted"
          ? "The applicant has been successfully accepted."
          : "The applicant has been rejected."
      );

      loadApplicants();
    } else {
      alert(result.message);
    }
  }

  function showCoverModal(text) {
    const modal = document.createElement("div");
    modal.classList.add("modal");

    modal.innerHTML = `
      <div class="modal-content">
        <h3>Cover Letter</h3>
        <p>${text}</p>
        <button onclick="this.closest('.modal').remove()">Close</button>
      </div>
    `;

    document.body.appendChild(modal);
  }

  function showInterviewModal(applicationId) {

    const modal = document.createElement("div");
    modal.classList.add("modal");

    modal.innerHTML = `
      <div class="modal-content">
        <h3>Schedule Interview</h3>

        <label>Date</label>
        <input type="date" id="interviewDate" required style="width:100%;margin-bottom:10px;padding:8px;border-radius:8px;border:none;">

        <label>Time</label>
        <input type="time" id="interviewTime" required style="width:100%;margin-bottom:10px;padding:8px;border-radius:8px;border:none;">

        <label>Note (Optional)</label>
        <textarea id="interviewNote" style="width:100%;padding:8px;border-radius:8px;border:none;"></textarea>

        <button id="scheduleBtn">Schedule</button>
        <button onclick="this.closest('.modal').remove()">Cancel</button>
      </div>
    `;

    document.body.appendChild(modal);

    document.getElementById("scheduleBtn").addEventListener("click", async () => {

      const date = document.getElementById("interviewDate").value;
      const time = document.getElementById("interviewTime").value;
      const note = document.getElementById("interviewNote").value;

      if (!date || !time) {
        alert("Please select date and time");
        return;
      }

      const formData = new FormData();
      formData.append("application_id", applicationId);
      formData.append("date", date);
      formData.append("time", time);
      formData.append("note", note);

      const res = await fetch("backend/company/schedule_interview.php", {
        method: "POST",
        body: formData
      });

      const result = await res.json();

      if (result.success) {
        modal.remove();
        showStatusModal("success", "Interview scheduled successfully.");
        loadApplicants();
      } else {
        alert(result.message);
      }

    });
  }

  function showStatusModal(type, message) {

    const modal = document.createElement("div");
    modal.classList.add("status-modal");

    modal.innerHTML = `
      <div class="status-modal-content ${type}">
          <div class="icon">
            ${type === "success" ? "✔" : "✖"}
          </div>
          <h4>${type === "success" ? "Success" : "Rejected"}</h4>
          <p>${message}</p>
      </div>
    `;

    document.body.appendChild(modal);

    setTimeout(() => {
      modal.remove();
    }, 2500);
  }

  loadApplicants();
});