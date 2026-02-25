<?php
require_once "backend/actions/auth/session_check.php";
?>
<!DOCTYPE html>
<html lang="en">

<head>
    <meta charset="UTF-8" />
    <meta name="viewport" content="width=device-width, initial-scale=1.0" />
    <title>View Applicants – Company Dashboard</title>
    <link rel="icon" type="image/png" href="assets/images/logo/Web Icon.png" />
    <link rel="stylesheet" href="assets/css/view_applicants.css">
    <style>
        /* ===============================
   COVER LETTER MODAL - MODERN UI
=================================*/

.modal {
  position: fixed;
  inset: 0;
  background: rgba(15, 23, 42, 0.75);
  backdrop-filter: blur(8px);
  display: flex;
  justify-content: center;
  align-items: center;
  padding: 20px;
  z-index: 9999;
  animation: fadeIn 0.3s ease forwards;
}

.modal-content {
  width: 100%;
  max-width: 650px;
  max-height: 85vh;
  overflow-y: auto;
  background: linear-gradient(145deg, #0f172a, #1e293b);
  border-radius: 20px;
  padding: 30px;
  color: #f1f5f9;
  box-shadow: 0 25px 50px rgba(0,0,0,0.4);
  border: 1px solid rgba(255,255,255,0.08);
  transform: translateY(20px);
  animation: slideUp 0.3s ease forwards;
}

/* Title */
.modal-content h3 {
  font-size: 22px;
  font-weight: 700;
  margin-bottom: 20px;
  color: #38bdf8;
  letter-spacing: 0.5px;
}

/* Cover Letter Text */
.modal-content p {
  font-size: 15px;
  line-height: 1.8;
  color: #cbd5e1;
  white-space: pre-wrap;
}

/* Close Button */
.modal-content button {
  margin-top: 25px;
  padding: 8px 18px;
  border-radius: 10px;
  border: none;
  font-weight: 600;
  cursor: pointer;
  background: linear-gradient(135deg, #3b82f6, #6366f1);
  color: white;
  transition: all 0.25s ease;
}

.modal-content button:hover {
  transform: translateY(-2px);
  box-shadow: 0 10px 20px rgba(59,130,246,0.4);
}

/* Scrollbar Styling */
.modal-content::-webkit-scrollbar {
  width: 6px;
}

.modal-content::-webkit-scrollbar-thumb {
  background: #475569;
  border-radius: 10px;
}

/* Animations */
@keyframes fadeIn {
  from { opacity: 0; }
  to { opacity: 1; }
}

@keyframes slideUp {
  from { 
    opacity: 0;
    transform: translateY(40px);
  }
  to { 
    opacity: 1;
    transform: translateY(0);
  }
}

/* ============================
   STATUS SUCCESS MODAL
=============================*/

.status-modal {
  position: fixed;
  inset: 0;
  background: rgba(15, 23, 42, 0.7);
  backdrop-filter: blur(6px);
  display: flex;
  justify-content: center;
  align-items: center;
  z-index: 10000;
  animation: fadeIn 0.25s ease forwards;
}

.status-modal-content {
  background: linear-gradient(145deg, #0f172a, #1e293b);
  padding: 30px 40px;
  border-radius: 18px;
  text-align: center;
  color: #f1f5f9;
  width: 90%;
  max-width: 400px;
  box-shadow: 0 25px 50px rgba(0,0,0,0.4);
  border: 1px solid rgba(255,255,255,0.08);
  animation: scaleIn 0.25s ease forwards;
}

.status-modal-content .icon {
  font-size: 45px;
  margin-bottom: 15px;
}

.status-modal-content.success .icon {
  color: #22c55e;
}

.status-modal-content.reject .icon {
  color: #ef4444;
}

.status-modal-content h4 {
  font-size: 18px;
  margin-bottom: 8px;
}

.status-modal-content p {
  font-size: 14px;
  color: #cbd5e1;
}

/* Animations */
@keyframes scaleIn {
  from {
    transform: scale(0.8);
    opacity: 0;
  }
  to {
    transform: scale(1);
    opacity: 1;
  }
}
    </style>
</head>

<body>

    <!-- Back Button -->
    <a id="backLink" class="back-btn" href="company_dashboard.php">
        <img src="assets/svg/cancel.svg" alt="">
    </a>

    <main>
        <div class="page-header">
            <h1>Applicants for Your Internships</h1>
            <p>Review, accept, or reject candidates.</p>
        </div>

        <!-- Applicants Grid -->
        <div class="applicants-grid" id="applicantsContainer">
            <!-- Dynamic Cards Will Load Here -->
        </div>

    </main>

<script src="assets/js/view-applicants.js"></script>
</body>
</html>