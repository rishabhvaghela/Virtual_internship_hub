document.addEventListener('DOMContentLoaded', () => {
    console.log('Fetching profile...');
    fetch('backend/student/student_profile.php')
        .then(res => {
            console.log('Response status:', res.status);
            return res.json();
        })
        .then(result => {
            console.log('Backend result:', result);

            if (result.status !== 'SUCCESS') return;

            const data = result.data;

            // Email (readonly)
            document.getElementById('email').value = data.email ?? '';

            // Full name
            // Priority:
            // 1. student_profile.full_name
            // 2. users.name (fallback)
            document.getElementById('fullName').value =
                data.full_name ?? data.name ?? '';

            document.getElementById('phone').value = data.phone ?? '';
            document.getElementById('gender').value = data.gender ?? '';
            document.getElementById('skills').value = data.skills ?? '';
            document.getElementById('bio').value = data.bio ?? '';
            document.getElementById('address').value = data.address ?? '';

            // Profile photo
            if (data.profile_photo) {
                document.getElementById('profileImage').src = data.profile_photo;
            }

            // Resume name
            if (data.resume) {
                document.getElementById('resumeFileName').textContent =
                    data.resume.split('/').pop();
            }

        })
        .catch(err => {
            console.error('Profile fetch error:', err);
        });

});


