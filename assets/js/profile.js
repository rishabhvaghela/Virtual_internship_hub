/* ===============================
   DOM ELEMENTS
================================ */
const profileForm = document.getElementById('profileForm');
const editBtn = document.getElementById('editBtn');
const saveBtn = document.getElementById('saveBtn');
const successModal = document.getElementById('successModal');
const closeModal = document.getElementById('closeModal');

const resumeUploadInput = document.getElementById('resumeUpload');
const resumeFileName = document.getElementById('resumeFileName');
const uploadBtn = document.getElementById('upload');

/* ===============================
   STATE
================================ */
let isEditing = false;

/* ===============================
   INITIAL STATE
================================ */
setReadOnlyMode();

/* ===============================
   FETCH PROFILE (GET)
================================ */
fetch('backend/student/student_profile.php')
    .then(res => res.json())
    .then(result => {

        if (result.status !== 'SUCCESS') return;

        const data = result.data;

        document.getElementById('email').value = data.email ?? '';
        document.getElementById('full_name').value = data.full_name ?? '';
        document.getElementById('phone').value = data.phone ?? '';
        document.getElementById('gender').value = data.gender ?? '';
        document.getElementById('skills').value = data.skills ?? '';
        document.getElementById('bio').value = data.bio ?? '';
        document.getElementById('address').value = data.address ?? '';


        if (data.resume) {
            resumeFileName.textContent = data.resume.split('/').pop();
        }
    });

/* ===============================
   EDIT BUTTON
================================ */
editBtn.addEventListener('click', () => {

    if (!isEditing) {
        setEditMode();
    } else {
        if (confirm('Discard changes?')) {
            setReadOnlyMode();
        }
    }
});

/* ===============================
   SAVE PROFILE (POST)
================================ */
profileForm.addEventListener('submit', (e) => {
    e.preventDefault();

    if (!isEditing) return; // 🔒 safety

    const formData = new FormData(profileForm);

    fetch('backend/student/student_profile.php', {
        method: 'POST',
        body: formData
    })
    .then(res => res.json())
    .then(result => {

        if (result.status === 'PROFILE_SAVED') {
            showSuccessModal();
            setReadOnlyMode();
        }
    });
});


/* ===============================
   RESUME FILE PREVIEW
================================ */
resumeUploadInput.addEventListener('change', (e) => {
    resumeFileName.textContent =
        e.target.files.length ? e.target.files[0].name : 'No file selected';
});

/* ===============================
   MODAL
================================ */
closeModal.addEventListener('click', () => {
    successModal.style.display = 'none';
});

/* ===============================
   UI STATE FUNCTIONS
================================ */
function setEditMode() {
    isEditing = true;
    editBtn.textContent = 'Cancel';
    saveBtn.style.display = 'block';

    profileForm.querySelectorAll('input, textarea, select').forEach(el => {
        if (el.id !== 'email') el.removeAttribute('readonly');
    });

    uploadBtn.style.display = 'block';

}

function setReadOnlyMode() {
    isEditing = false;
    editBtn.textContent = 'Edit';
    saveBtn.style.display = 'none';

    profileForm.querySelectorAll('input, textarea, select').forEach(el => {
        if (el.id !== 'email') el.setAttribute('readonly', true);
    });

    uploadBtn.style.display = 'none';

}

function showSuccessModal() {
    successModal.style.display = 'flex';
}
