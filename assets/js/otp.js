const storedEmail = localStorage.getItem("verify_email");

if (!storedEmail) {
  alert("Session expired. Please login again.");
  window.location.href = "login.html";
}





const inputs = document.querySelectorAll(".otp-inputs input");
const form = document.getElementById("otpForm");
const errorText = document.getElementById("errorText");



inputs[0].focus();

inputs.forEach((input, idx) => {
  input.addEventListener("input", () => {
    input.value = input.value.replace(/[^0-9]/g, "");
    if (input.value && inputs[idx + 1]) {
      inputs[idx + 1].focus();
    }
  });

  input.addEventListener("keydown", (e) => {
    if (e.key === "Backspace" && !input.value && inputs[idx - 1]) {
      inputs[idx - 1].focus();
    }
  });
});

console.log(localStorage.getItem("verify_email"));


form.addEventListener("submit", async (e) => {
  e.preventDefault();
  errorText.textContent = "";

  let otp = "";
  inputs.forEach(i => otp += i.value);

  if (otp.length !== 6) {
    showError("Please enter complete OTP");
    return;
  }

  const email = localStorage.getItem("verify_email");// ✅ FIXED

  if (!email) {
    showError("Session expired. Please register again.");
    return;
  }

  const fd = new FormData();
  fd.append("email", email);
  fd.append("otp", otp);

  const res = await fetch(
    "/virtual_internship_hub/backend/actions/auth/otp.php",
    { method: "POST", body: fd }
  );

  const result = await res.text();

  if (result === "OTP_VERIFIED") {
    localStorage.removeItem("verify_email"); // ✅ cleanup
    window.location.href = "login.html";
  } 
  else if(result === "OTP_INVALID" || result === "OTP_EXPIRED"){
    showError("Invalid or expired OTP");
  }
  else{
    showError("which account you try to register is already registerd")
  }
});


function showError(msg) {
  errorText.textContent = msg;
  inputs.forEach(i => i.classList.add("error"));
}


const resendBtn = document.getElementById("resendBtn");
const resendMsg = document.getElementById("resendMsg");

resendBtn.addEventListener("click", async () => {
    resendMsg.textContent = "Resending OTP...";
    resendMsg.style.color = "#b8ababff";

    resendBtn.disabled = true;

    const email = localStorage.getItem("verify_email");
    if (!email) {
        resendMsg.textContent = "Session expired. Please login again.";
        resendBtn.disabled = false;
        return;
    }

    const fd = new FormData();
    fd.append("email", email);

    try {
        const res = await fetch(
            "/virtual_internship_hub/backend/actions/auth/resend_otp.php",
            { method: "POST", body: fd }
        );

        const result = await res.text();

        if (result === "OTP_RESENT") {
            resendMsg.textContent = "OTP sent to your email";
            resendMsg.style.color = "#6cf2c2";
            setTimeout(() => {
            resendMsg.textContent = "";
            }, 2500);
        } else {
            resendMsg.textContent = "Unable to resend OTP";
            resendMsg.style.color = "#ff5f6d";
        }
    } catch (e) {
        resendMsg.textContent = "Network error. Try again.";
        resendMsg.style.color = "#ff5f6d";
    }

    resendBtn.disabled = false;

});


