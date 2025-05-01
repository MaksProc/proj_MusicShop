document.addEventListener('DOMContentLoaded', () => {
    const table = document.getElementById('product-table');
    const formContainer = document.getElementById('product-form-container');
    const addBtn = document.getElementById('add-product-btn');
  
    // Load form for editing a product
    table.addEventListener('click', async (event) => {
      const row = event.target.closest('tr.product-row');
      if (!row) return;
  
      const productId = row.dataset.id;
  
      const response = await fetch(`/admin/product-form/${productId}`);
      const html = await response.text();
      formContainer.innerHTML = html;
      scrollToForm();
    });
  
    // Load empty form for adding new product
    addBtn.addEventListener('click', async () => {
      const response = await fetch('/admin/product-form');
      const html = await response.text();
      formContainer.innerHTML = html;
      scrollToForm();
    });
  
    function scrollToForm() {
      formContainer.scrollIntoView({ behavior: 'smooth' });
    }
  });
  