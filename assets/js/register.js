const regform = document.querySelector('#registerForm');
const user = document.querySelector('#name');
const email = document.querySelector('#email');
const pass = document.querySelector('#password');

const usererr = document.querySelector('#usererr');
const passerr = document.querySelector('#passerr');
const emailerr = document.querySelector('#emailerr');
const otperror = document.querySelector('#otp-error');

const loader = document.getElementById("loaderOverlay");

const roleSelect = document.querySelector('#role');

roleSelect.addEventListener("change", function () {
  let emailInput = document.getElementById("email");
  let lblCompany = document.getElementById("lbl-company");
  let nameInput = document.getElementById("name");
  let role = this.value;

  if (role === "company") {
    emailInput.placeholder = "hr@Company.com"
    lblCompany.textContent = "Company Name";
    nameInput.placeholder = "Enter Company Name";
  } else {
    emailInput.placeholder = "Enter your email";
    lblCompany.textContent = "Name";
    nameInput.placeholder = "Enter your Name";
  }
});

const blockedDomains = [
  "gmail.com",
  "yahoo.com",
  "outlook.com",
  "hotmail.com",
  "icloud.com",
  "protonmail.com",
  "aol.com",
  "live.com"
];

function isCompanyEmail(email) {

  const parts = email.split("@");

  if (parts.length !== 2) return false;

  const domain = parts[1].toLowerCase();

  return !blockedDomains.includes(domain);

}



/* =====================================================
REGISTER SUBMIT
===================================================== */
regform.addEventListener("submit", async (e) => {

  e.preventDefault();

  if (!validation()) return;

  const formData = new FormData(regform);

  loader.classList.add("active");

  try {

    const res = await fetch(
      "/virtual_internship_hub/backend/actions/auth/register.php",
      {
        method: "POST",
        body: formData
      }
    );

    const result = await res.text();

    loader.classList.remove("active");

    /* =============================
       OTP SENT SUCCESS
    ============================= */

    if (result === "OTP_SENT") {

      localStorage.setItem("verify_email", email.value);

      showToast("OTP sent successfully to your email", "success");

      setTimeout(() => {
        window.location.href = "otp.html";
      }, 1500);

    }

    else if (result === "INVALID_COMPANY_EMAIL") {

      showToast("Please use your official company email", "error");

    }

    /* =============================
       MAIL FAILED
    ============================= */

    else if (result === "MAIL_FAILED") {

      showToast("OTP sending failed. Please try again.", "error");

    }



    /* =============================
       UNKNOWN ERROR
    ============================= */

    else {

      showToast("Registration failed. Please try again.", "error");

    }

  }
  catch (err) {

    loader.classList.remove("active");

    showToast("Server error. Please try again.", "error");

    console.error(err);

  }

});



/* =====================================================
VALIDATION FUNCTION
===================================================== */

const validation = () => {

  let isValid = true;

  /* RESET STATES */

  user.classList.remove("error-input", "success-input");
  usererr.classList.remove("error");

  email.classList.remove("error-input", "success-input");
  emailerr.classList.remove("error");

  pass.classList.remove("error-input", "success-input");
  passerr.classList.remove("error");


  const useraouth = user.value.trim();
  const emailaouth = email.value.trim();
  const passaouth = pass.value.trim();


  /* =====================================================
  NAME VALIDATION  (FIXED FROM USERNAME → NAME)
  ===================================================== */

  if (useraouth === "") {

    usererr.textContent = "Name cann't be blanked";
    usererr.classList.add('error');
    user.classList.add('error-input');
    isValid = false;
    Erroranimate(user);

  }

  else if (useraouth.length < 5) {

    usererr.textContent = "Name must be at least 5 characters";
    usererr.classList.add('error');
    user.classList.add('error-input');
    isValid = false;
    Erroranimate(user);

  }

  // else if (useraouth.length > 12) {

  //     usererr.textContent = "You cann't enter more than 12 characters";
  //     usererr.classList.add('error');
  //     user.classList.add('error-input');
  //     isValid = false;
  //     Erroranimate(user);

  // }

  else if (/\d/.test(useraouth)) {

    usererr.textContent = "Name can't contain a number";
    usererr.classList.add('error');
    user.classList.add('error-input');
    isValid = false;
    Erroranimate(user);

  }

  else if (/[^a-zA-Z0-9]/.test(useraouth)) {

    usererr.textContent = "Special characters are not allowed";
    usererr.classList.add('error');
    user.classList.add('error-input');
    Erroranimate(user); isValid = false;

  }

  else {

    usererr.classList.remove('error');
    user.classList.add('success-input');

  }


  /* =====================================================
     PROFESSIONAL EMAIL VALIDATION
  ===================================================== */

  const emailPattern = /^[a-zA-Z0-9._%+-]+@[a-zA-Z0-9.-]+\.(com|org|net|edu|gov|in|co|io)$/i;

  if (emailaouth === "") {

    emailerr.textContent = "Email cannot be blank";
    emailerr.classList.add('error');
    email.classList.add('error-input');
    isValid = false;
    Erroranimate(email);

  }

  else if (emailaouth.includes(" ")) {

    emailerr.textContent = "Email cannot contain spaces";
    emailerr.classList.add('error');
    email.classList.add('error-input');
    isValid = false;
    Erroranimate(email);

  }

  else if (!emailPattern.test(emailaouth)) {

    emailerr.textContent = "Enter a valid professional email (example@gmail.com)";
    emailerr.classList.add('error');
    email.classList.add('error-input');
    isValid = false;
    Erroranimate(email);

  }
  /* ===============================
     COMPANY EMAIL EXTRA VALIDATION
  ================================ */

  else if (roleSelect.value === "company" && !isCompanyEmail(emailaouth)) {

    emailerr.textContent = "Please use your company email (example@company.com)";
    emailerr.classList.add('error');
    email.classList.add('error-input');
    isValid = false;
    Erroranimate(email);

  }

  else {

    emailerr.classList.remove('error');
    email.classList.remove('error-input');
    email.classList.add('success-input');

  }



  /* =====================================================
  PASSWORD VALIDATION
  ===================================================== */

  if (passaouth === "") {

    passerr.textContent = "Password cann't be blanked";
    passerr.classList.add('error');
    pass.classList.add('error-input');
    isValid = false;
    Erroranimate(pass);

  }

  else if (passaouth.length < 8) {

    passerr.textContent = "Password must be at least 8 characters";
    passerr.classList.add('error');
    pass.classList.add('error-input');
    isValid = false;
    Erroranimate(pass);

  }

  else if (passaouth.length > 12) {

    passerr.textContent = "You cann't enter more than 12 characters";
    passerr.classList.add('error');
    pass.classList.add('error-input');
    isValid = false;
    Erroranimate(pass);

  }

  else if (!/^[A-Z]/.test(passaouth)) {

    passerr.textContent = "Password must be start with the (A-Z) letters";
    passerr.classList.add('error');
    pass.classList.add('error-input');
    isValid = false;
    Erroranimate(pass);

  }

  else if (!/[@$_-]/.test(passaouth)) {

    passerr.textContent = "Password must contain at leaste one special character";
    passerr.classList.add('error');
    pass.classList.add('error-input');
    isValid = false;
    Erroranimate(pass);

  }

  else if (!/[a-z]/.test(passaouth)) {

    passerr.textContent = "Password must have at least one lowercase letter";
    passerr.classList.add('error');
    pass.classList.add('error-input');
    isValid = false;
    Erroranimate(pass);

  }

  else if (!/\d/.test(passaouth)) {

    passerr.textContent = "Password must contain at least one number";
    passerr.classList.add('error');
    pass.classList.add('error-input');
    isValid = false;
    Erroranimate(pass);

  }

  else if (/\s/.test(passaouth)) {

    passerr.textContent = "Password must not contain spaces";
    passerr.classList.add('error');
    pass.classList.add('error-input');
    isValid = false;
    Erroranimate(pass);

  }

  else {

    passerr.classList.remove('error');
    pass.classList.add('success-input');

  }


  return isValid;

};



/* =====================================================
ERROR ANIMATION
===================================================== */

function Erroranimate(el) {

  el.animate(
    [
      { transform: 'translateX(-7px)' },
      { transform: 'translateX(7px)' },
      { transform: 'translateX(-7px)' }
    ],
    {
      duration: 250,
      iterations: 1,
      easing: 'ease-in-out'
    }
  );

}