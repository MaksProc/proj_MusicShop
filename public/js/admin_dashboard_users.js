// Dashboard's Users table script

document.addEventListener('DOMContentLoaded', () => {
    const table = document.getElementById('user-table');
    const formContainer = document.getElementById('user-form-container');
  
    // Users table in dashboard
    // Here Event Bubbling is used on table (instead of adding an event listener to each button separately)
    // Button loads a form for editing user data (role)
    table.addEventListener('click', async (event) => {
      const row = event.target.closest('tr.user-row');
      if (!row) return;
  
      const userId = row.dataset.id;
  
      const response = await fetch(`/admin/user/role-form/${userId}`);
      const html = await response.text();
      formContainer.innerHTML = html;
      scrollToForm();
    });

  
    function scrollToForm() {
      formContainer.scrollIntoView({ behavior: 'smooth' });
    }
  });
  