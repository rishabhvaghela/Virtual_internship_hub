const form = document.getElementById("forgotForm");
const email = document.getElementById("email");
const emailerr = document.getElementById("emailerr");

form.addEventListener("submit", async (e) => {
  e.preventDefault();


  emailerr.textContent = "";
  email.classList.remove("error-input", "success-input");

  if (email.value.trim() === "") {
    emailerr.textContent = "Email required";
    email.classList.add("error-input");
    return;
  }
  // ✅ SHOW LOADER
  showLoader("Sending reset link to your email...");

  try {
    const fd = new FormData(form);

    const res = await fetch(
      "/virtual_internship_hub/backend/actions/auth/forgot_password.php",
      {
        method: "POST",
        body: fd
      }
    );

    const result = (await res.text()).trim();

     // ✅ HIDE LOADER (response mil gaya)
    hideLoader();

    if (result === "EMAIL_REQUIRED") {
      emailerr.textContent = "Email required";
      email.classList.add("error-input");

    } else if (result === "EMAIL_NOT_FOUND") {
      emailerr.textContent = "Email not registered";
      email.classList.add("error-input");

    } else if (result === "RESET_EMAIL_SENT") {
      console.log(result);
      email.classList.add("success-input");
      emailerr.textContent = "Reset link sent to your email";
      emailerr.style.color = "#6cf2c2";
    } else {
      emailerr.textContent = "Something went wrong";
    }

  } catch (err) {
        // ✅ HIDE LOADER (error case)
    hideLoader();
    console.error(err);
    emailerr.textContent = "Server error. Try again.";
  }

});


function showLoader(text = "Please wait...") {
  loadingText.innerText = text;
  loadingModal.classList.remove("hidden");
}

function hideLoader() {
  loadingModal.classList.add("hidden");
}
