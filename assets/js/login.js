const form = document.getElementById("loginForm");

const role = document.getElementById("role");
const email = document.getElementById("email");
const password = document.getElementById("password");

const roleErr = document.getElementById("roleErr");
const emailErr = document.getElementById("emailErr");
const passErr = document.getElementById("passErr");

function resetValidation() {
    [role, email, password].forEach(el => el.classList.remove("error-input", "success-input"));
    [roleErr, emailErr, passErr].forEach(el => el.classList.remove("error"));
    [roleErr, emailErr, passErr].forEach(el => el.textContent = "");
}

// form.addEventListener("submit", function (e) {
//   e.preventDefault();
//   resetErrors();

//   let isValid = true;


//   // EMAIL
//   if (email.value.trim() === "") {
//     emailErr.textContent = "Email is required";
//     emailErr.classList.add('error')
//     email.classList.add("error-input");
//     Erroranimate(email)
//     isValid = false;
//   }
//   else if (!/^[^\s@]+@[^\s@]+\.[^\s@]+$/.test(email.value)) {
//     emailErr.textContent = "Invalid email format";
//     email.classList.add("error-input");
//     isValid = false;
//   }

//   // PASSWORD
//   if (password.value.trim() === "") {
//     passErr.textContent = "Password is required";
//     passErr.classList.add('error');
//     password.classList.add("error-input");
//     isValid = false;
//   }

//   if (!isValid) return;

//   // ✅ Backend call
//   fetch("backend/actions/auth/login.php", {
//     method: "POST",
//     headers: {
//       "Content-Type": "application/x-www-form-urlencoded"
//     },
//     body: `role=${role.value}&email=${email.value}&password=${password.value}`
//   })
//   .then(res => res.text())
//   .then(data => {
//     if (data === "STUDENT_LOGIN") {
//       window.location.href = "student_dashboard.html";
//     }
//     else if (data === "COMPANY_LOGIN") {
//       window.location.href = "company_dashboard.html";
//     }
//     else {
//       alert(data);
//     }
//   });
// });

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




form.addEventListener("submit", async (e) => {
    e.preventDefault();
    resetValidation()



    try {
        const res = await fetch(
            "/virtual_internship_hub/backend/actions/auth/login.php",
            {
                method: "POST",
                headers: {
                    "Content-Type": "application/x-www-form-urlencoded"
                },
                body: `role=${role.value}&email=${email.value}&password=${password.value}`
            }
        );

        const result = await res.text();

        if (result === "All fields are required") {
            emailErr.textContent = "Email can't be blanked";
            emailErr.classList.add('error');
            email.classList.add('error-input');
            Erroranimate(email)

            passErr.textContent = "password can't be blanked";
            passErr.classList.add('error');
            password.classList.add('error-input');
            Erroranimate(password)
        }

        else if (result === "Email is required") {
            password.classList.add("success-input");
            emailErr.textContent = "Email can't be blanked";
            emailErr.classList.add('error');
            email.classList.add('error-input');
            Erroranimate(email)
        }

        else if (result === "Email not registered") {

            //  VERY IMPORTANT RESET
            email.classList.remove("success-input");
            email.classList.add('error-input');
            emailErr.textContent = "Email Not registered";
            emailErr.classList.add('error');
            Erroranimate(email);
        }

        else if (result === "Password is required") {
            // Email is correct
            email.classList.add("success-input");
            passErr.textContent = "password can't be blanked";
            passErr.classList.add('error');
            password.classList.add('error-input');
            Erroranimate(password)
        }

        else if (result === "Incorrect password") {
            // Email is correct
            email.classList.add("success-input");
            passErr.textContent = "Incorrect Password";
            passErr.classList.add('error');
            password.classList.add('error-input');
            Erroranimate(password)
        }
        else if (result == "Role mismatch") {
            email.classList.add("success-input");
            password.classList.add("success-input");
            role.classList.add("error-input");
            roleErr.textContent = "Role mismatch";
            roleErr.classList.add("error");
            Erroranimate(role);
        }

        else if (result == "STUDENT_LOGIN") {
            email.classList.add("success-input");
            password.classList.add("success-input");
            role.classList.add("success-input");

            setTimeout(() => {

                window.location.href = "student_dashboard.php"
            }, 700);
        }
        else if (result == "COMPANY_LOGIN") {
            email.classList.add("success-input");
            password.classList.add("success-input");
            role.classList.add("success-input");
            setTimeout(() => {
                window.location.href = "company_dashboard.php"
            }, 700);
        }

        else {
            alert("Login failed")
        }
    } catch (err) {
        alert("Server error");
        console.error(err);
    }
});

