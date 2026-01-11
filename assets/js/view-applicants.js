document.querySelectorAll(".btn-shortlist").forEach(btn => {
    btn.addEventListener("click", e => {
        const card = e.target.closest(".applicant-card");
        const status = card.querySelector(".status-badge");

        status.textContent = "Shortlisted";
        status.className = "status-badge status reviewed";
    });
});

document.querySelectorAll(".btn-interview").forEach(btn => {
    btn.addEventListener("click", e => {
        const date = prompt("Enter interview date & time:");
        if (!date) return;

        const card = e.target.closest(".applicant-card");
        const status = card.querySelector(".status-badge");

        status.textContent = "Interview Scheduled";
        status.className = "status-badge status selected";

        alert("Interview scheduled on: " + date);
    });
});
