    const roleSelect = document.getElementById('role');

roleSelect.addEventListener("change", function() {
  let emailInput = document.getElementById("email");
  let role = this.value;

  if (role === "company") {
    emailInput.placeholder = "hr@Company.com"
  } else {
    emailInput.placeholder = "Enter your email";
  }
});