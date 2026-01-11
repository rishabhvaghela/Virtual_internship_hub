    function switchToEditMode() {
      document.getElementById('viewMode').style.display = 'none';
      document.getElementById('editMode').style.display = 'block';
    }

    function switchToViewMode() {
      document.getElementById('editMode').style.display = 'none';
      document.getElementById('viewMode').style.display = 'block';
    }

    function saveProfile(event) {
      event.preventDefault();

      // Get form values
      const name = document.getElementById('companyName').value;
      const email = document.getElementById('email').value;
      const industry = document.getElementById('industry').value;
      const description = document.getElementById('description').value;

      // Update view mode fields
      document.getElementById('viewCompanyName').textContent = name;
      document.getElementById('viewEmail').textContent = email;
      document.getElementById('viewIndustry').textContent = industry;
      document.getElementById('viewDescription').textContent = description;

      // Switch back to view
      switchToViewMode();

      // In a real app, you'd send this data to a backend API here
      alert('Profile updated successfully!');
    }