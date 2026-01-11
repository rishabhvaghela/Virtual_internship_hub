const form = document.getElementById("resetForm");
const password = document.getElementById("password");
const cpassword = document.getElementById("cpassword");
const passerr = document.getElementById("passerr");
const cpasserr = document.getElementById("cpasserr");

// token URL se uthao
const params = new URLSearchParams(window.location.search);
const token = params.get("token");

if (!token) {
  alert("Invalid reset link");
  window.location.href = "login.html";
}

form.addEventListener("submit", async (e) => {
  e.preventDefault();

  passerr.textContent = "";
  cpasserr.textContent = "";

  if (password.value.trim() === "") {
    passerr.textContent = "Password required";
    return;
  }

  

  if (password.value !== cpassword.value) {
    cpasserr.textContent = "Passwords do not match";
    return;
  }

  const fd = new FormData();
  fd.append("token", token);
  fd.append("password", password.value);

  const res = await fetch(
    "/virtual_internship_hub/backend/actions/auth/reset_password.php",
    {
      method: "POST",
      body: fd
    }
  );

  const result = (await res.text()).trim();

  if (result === "TOKEN_INVALID") {
    alert("Invalid or expired link");
  }
  else if (result === "PASSWORD_UPDATED") {
    alert("Password reset successful");
    window.location.href = "login.html";
  }
  else {
    alert("Something went wrong");
  }
});
