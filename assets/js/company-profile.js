/* ===============================
   DOM
================================ */
const companyForm = document.getElementById('companyForm');
const editBtn = document.getElementById('editBtn');
const saveBtn = document.getElementById('saveBtn');
const successModal = document.getElementById('successModal');
const closeModal = document.getElementById('closeModal');

const companyName = document.getElementById('company_name');
const email = document.getElementById('email');
const industry = document.getElementById('industry');
const description = document.getElementById('description');

/* ===============================
   STATE
================================ */
let isEditing = false;
let originalData = {};

/* ===============================
   INITIAL MODE
================================ */
setReadOnlyMode();

/* ===============================
   LOAD PROFILE
================================ */
fetch('backend/company/company_profile.php')
  .then(res => res.json())
  .then(data => {

    originalData = data;

    companyName.value = data.company_name ?? '';
    email.value = data.email ?? '';
    industry.value = data.industry ?? '';
    description.value = data.description ?? '';
  })
  .catch(() => alert('Failed to load company profile'));

/* ===============================
   EDIT / CANCEL
================================ */
editBtn.addEventListener('click', () => {

  if (!isEditing) {
    setEditMode();
  } else {
    restoreOriginal();
    setReadOnlyMode();
  }
});

/* ===============================
   SAVE PROFILE
================================ */
companyForm.addEventListener('submit', (e) => {
  e.preventDefault();

  if (!isEditing) return; 

  const formData = new FormData(companyForm);

  fetch('backend/company/company_profile.php', {
    method: 'POST',
    body: formData
  })
    .then(res => res.json())
    .then(res => {

      if (!res.success) {
        alert(res.error || 'Update failed');
        return;
      }

      originalData = {
        company_name: companyName.value,
        email: email.value,
        industry: industry.value,
        description: description.value
      };

      if (res.success) {
        setReadOnlyMode();
        showSuccessModal();
      }
    });
});

function showSuccessModal() {
  successModal.style.display = 'flex';
}

closeModal.addEventListener('click', () => {
  successModal.style.display = 'none';
});


/* ===============================
   UI FUNCTIONS
================================ */
function setEditMode() {
  isEditing = true;
  editBtn.textContent = 'Cancel';
  saveBtn.style.display = 'inline-block';

  companyForm.querySelectorAll('input, textarea').forEach(el => {
    if (el.id !== 'email') {
      el.removeAttribute('readonly');
    }
  });
}

function setReadOnlyMode() {
  isEditing = false;
  editBtn.textContent = 'Edit';
  saveBtn.style.display = 'none';

  companyForm.querySelectorAll('input, textarea').forEach(el => {
    el.setAttribute('readonly', true);
  });
}

function restoreOriginal() {
  companyName.value = originalData.company_name ?? '';
  email.value = originalData.email ?? '';
  industry.value = originalData.industry ?? '';
  description.value = originalData.description ?? '';
}
