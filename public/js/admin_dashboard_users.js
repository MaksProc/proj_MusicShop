document.addEventListener('DOMContentLoaded', () => {
    const table = document.getElementById('user-table');
    const formContainer = document.getElementById('user-form-container');
    // const addBtn = document.getElementById('add-user-btn');
  

    table.addEventListener('click', async (event) => {
      const row = event.target.closest('tr.user-row');
      if (!row) return;
  
      const userId = row.dataset.id;
  
      const response = await fetch(`/admin/user/role-form/${userId}`);
      const html = await response.text();
      formContainer.innerHTML = html;
      scrollToForm();
    });
  
    // // Load empty form for adding new user
    // addBtn.addEventListener('click', async () => {
    //   const response = await fetch('/admin/product-form');
    //   const html = await response.text();
    //   formContainer.innerHTML = html;
    //   scrollToForm();
    // });
  
    function scrollToForm() {
      formContainer.scrollIntoView({ behavior: 'smooth' });
    }
  });
  