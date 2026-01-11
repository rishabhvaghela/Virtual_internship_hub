    const roleSelect = document.getElementById('role');
    const studentFields = document.getElementById('studentFields');
    const companyFields = document.getElementById('companyFields');

    studentFields.style.display = 'none';
    companyFields.style.display = 'none';

    roleSelect.addEventListener('change', () => {
      // Hide all

      // Show selected
      const role = roleSelect.value;
      if (role === 'student') {
        studentFields.style.display = 'block';
        companyFields.style.display = 'none';
      }
      else if (role === 'company') {
        studentFields.style.display = 'none';
        companyFields.style.display = 'block';
      }
    });
    

roleSelect.addEventListener("change", function() {
  let emailInput = document.getElementById("email");
  let role = this.value;

  if (role === "company") {
    emailInput.placeholder = "hr@Company.com"
  } else {
    emailInput.placeholder = "Enter your email";
  }
});