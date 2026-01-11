const regform = document.querySelector('#registerForm');
const user = document.querySelector('#name');
const email = document.querySelector('#email');
const pass = document.querySelector('#password');
const usererr = document.querySelector('#usererr');
const passerr = document.querySelector('#passerr');
const emailerr = document.querySelector('#emailerr');
const otperror = document.querySelector('#otp-error');

const loader = document.getElementById("loaderOverlay");

regform.addEventListener("submit", async (e) => {
    e.preventDefault();

    if (!validation()) return;

    const formData = new FormData(regform);
    // ✅ SHOW LOADER
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

        if (!localStorage.getItem("verify_email")) {
            window.location.href = "register.html";
        }


        if (result === "OTP_SENT") {
            localStorage.setItem("verify_email", email.value);
            loader.classList.remove("active");
            window.location.href = "otp.html";
        }

        else if (result === "MAIL_FAILED") {
            loader.classList.remove("active");
            // alert("OTP sending failed. Try again.");
            otperror.textContent = "OTP sending failed. Try again.";
            otperror.classList.add("error");
        }

    } catch (err) {
        loader.classList.remove("active");
        alert("Server error");
        console.error(err);
    }
});

function showSuccessModal() {
    const overlay = document.getElementById("overlay");
    overlay.classList.add("active");

    setTimeout(() => {
        overlay.classList.remove("active");
    }, 2000);

    const login = document.getElementById('login-btn');
    login.addEventListener("click", () => {
        setTimeout(() => {
            window.location.href = "login.html";
        }, 700);
    })
}


const validation = () => {

    let isValid = true;

    //  RESET STATE (VERY IMPORTANT)
    user.classList.remove("error-input", "success-input");
    usererr.classList.remove("error");
    email.classList.remove("error-input", "success-input");
    emailerr.classList.remove("error");
    pass.classList.remove("error-input", "success-input");
    passerr.classList.remove("error");

    const useraouth = document.querySelector('#name').value.trim();
    const emailaouth = document.querySelector('#email').value.trim();
    const passaouth = document.querySelector('#password').value.trim();


    /*------------------
  Username Validation 
 --------------------*/

    // for blank username
    if (useraouth === "") {
        usererr.textContent = "Username cann't be blanked"
        usererr.classList.add('error');
        user.classList.add('error-input');
        isValid = false; // ✅ IMPORTANT
        Erroranimate(user);
    }
    // for set minimum length of username
    else if (useraouth.length < 8) {
        usererr.textContent = "Username must be at least 8 characters";
        usererr.classList.add('error');
        user.classList.add('error-input');
        isValid = false; // ✅ IMPORTANT
        Erroranimate(user);
    }
    // for ser max length of username
    else if (useraouth.length > 12) {
        usererr.textContent = "You cann't enter more than 12 characters";
        usererr.classList.add('error');
        user.classList.add('error-input');
        Erroranimate(user);
        isValid = false; // ✅ IMPORTANT
    }
    // that username can't start with number
    else if (/^[0-9]/.test(useraouth)) {
        usererr.textContent = "Username can't start with a number";
        usererr.classList.add('error');
        user.classList.add('error-input');
        Erroranimate(user);
        isValid = false; // ✅ IMPORTANT
    }
    // that username can't contain special characters
    else if (/[^a-zA-Z0-9]/.test(useraouth)) {
        usererr.textContent = "Special characters are not allowed";
        usererr.classList.add('error');
        user.classList.add('error-input');
        Erroranimate(user);
        isValid = false; // ✅ IMPORTANT
    }
    // if all conditions are satisfied
    else {
        usererr.classList.remove('error');
        user.classList.add('success-input');
    }

    /*---------------
    Email validation
    --------------*/

    if (emailaouth == "") {
        emailerr.textContent = "Email can't be blank";
        emailerr.classList.add('error');
        email.classList.add('error-input');
        Erroranimate(email);
        isValid = false; // ✅ IMPORTANT
    }

    else if (emailaouth.includes(" ")) {
        emailerr.textContent = "Email must not contain spaces";
        emailerr.classList.add('error');
        email.classList.add('error-input');
        Erroranimate(email);
        isValid = false; // ✅ IMPORTANT
    }

    else if (!emailaouth.includes("@")) {
        emailerr.textContent = "Email must contain @ symbol";
        emailerr.classList.add('error');
        email.classList.add('error-input');
        Erroranimate(email);
        isValid = false; // ✅ IMPORTANT
    }

    else if (emailaouth.split("@").length !== 2) {
        emailerr.textContent = "Email can contain only one @ symbol";
        emailerr.classList.add('error');
        email.classList.add('error-input');
        Erroranimate(email);
        isValid = false; // ✅ IMPORTANT
    }

    else if (!emailaouth.includes(".")) {
        emailerr.textContent = "Email must contain domain (.)";
        emailerr.classList.add('error');
        email.classList.add('error-input');
        Erroranimate(email);
        isValid = false; // ✅ IMPORTANT
    }

    else if (emailaouth.includes("..")) {
        emailerr.textContent = "Email cannot contain consecutive dots";
        emailerr.classList.add('error');
        email.classList.add('error-input');
        Erroranimate(email);
        isValid = false; // ✅ IMPORTANT
    }

    else if (emailaouth.length < 6) {
        emailerr.textContent = "Email is too short";
        emailerr.classList.add('error');
        email.classList.add('error-input');
        Erroranimate(email);
        isValid = false; // ✅ IMPORTANT
    }

    else if (!/^[^\s@]+@[^\s@]+\.[^\s@]{3,}$/.test(emailaouth)) {
        emailerr.textContent = "Enter a valid email address";
        emailerr.classList.add('error');
        email.classList.add('error-input');
        Erroranimate(email);
        isValid = false; // ✅ IMPORTANT
    }

    else {
        emailerr.classList.remove('error');
        email.classList.add('success-input');
    }


    // Passsword Validation

    // check password blank or not.
    if (passaouth == "") {
        passerr.textContent = "Password cann't be blanked"
        passerr.classList.add('error')
        pass.classList.remove('success-input')
        pass.classList.add('error-input');
        Erroranimate(pass);
        isValid = false; // ✅ IMPORTANT
    }

    // for set minimum length of Password
    else if (passaouth.length < 8) {
        passerr.textContent = "Password must be at least 8 characters";
        passerr.classList.add('error');
        pass.classList.add('error-input');
        isValid = false; // ✅ IMPORTANT
        Erroranimate(pass);
    }

    //max length of password.
    else if (passaouth.length > 12) {
        passerr.textContent = "You cann't enter more than 12 characters";
        passerr.classList.add('error');
        pass.classList.add('error-input');
        Erroranimate(pass);
        isValid = false; // ✅ IMPORTANT
    }

    // Password must be start with uppercase letter.
    else if (!/^[A-Z]/.test(passaouth)) {
        passerr.textContent = "Password must be start with the (A-Z) letters";
        passerr.classList.add('error');
        pass.classList.add('error-input');
        Erroranimate(pass);
        isValid = false; // ✅ IMPORTANT
    }
    //password must have any special charecter.
    else if (!/[@$_-]/.test(passaouth)) {
        passerr.textContent = "Password must contain at leaste one special character";
        passerr.classList.add('error');
        pass.classList.add('error-input');
        Erroranimate(pass);
        isValid = false; // ✅ IMPORTANT
    }
    // Password must contain at least one lowercase letter.
    else if (!/[a-z]/.test(passaouth)) {
        passerr.textContent = "Password must have at least one lowercase letter";
        passerr.classList.add('error');
        pass.classList.add('error-input');
        Erroranimate(pass);
        isValid = false; // ✅ IMPORTANT
    }
    //Password must contain at least one number.
    else if (!/\d/.test(passaouth)) {
        passerr.textContent = "Password must contain at least one number";
        passerr.classList.add('error');
        pass.classList.add('error-input');
        Erroranimate(pass);
        isValid = false; // ✅ IMPORTANT
    }
    // Password must not contain spaces.
    else if (/\s/.test(passaouth)) {
        passerr.textContent = "Password must not contain spaces";
        passerr.classList.add('error');
        pass.classList.add('error-input');
        Erroranimate(pass);
        isValid = false; // ✅ IMPORTANT
    }
    // if all validation is correct then Password is right with green flag.
    else {
        passerr.classList.remove('error');
        pass.classList.remove('error-input');
        pass.classList.add('success-input')
    }

    return isValid;

}



function Erroranimate(el) {
    el.animate(
        [
            // { transform: 'translateX(0)' },
            { transform: 'translateX(-7px)' },
            { transform: 'translateX(7px)' },
            { transform: 'translateX(-7px)' },
            // { transform: 'translateX(0)' }
        ],
        {
            duration: 250,
            iterations: 1,
            easing: 'ease-in-out'
        }
    )
}