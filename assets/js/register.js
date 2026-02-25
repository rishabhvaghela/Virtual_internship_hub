const roleSelect = document.getElementById('role');

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
    lblCompany.textContent = "UserName";
    nameInput.placeholder = "Enter your Username";
  }
});