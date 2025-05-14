// Dashboard's Products table script

document.addEventListener('DOMContentLoaded', () => {
    const table = document.getElementById('product-table');
    const formContainer = document.getElementById('product-form-container');
    const addBtn = document.getElementById('add-product-btn');
  
    // Event Bubbling on table -> fetching a form with corresponding ProductID
    table.addEventListener('click', async (event) => {
      const row = event.target.closest('tr.product-row');
      if (!row) return;
  
      const productId = row.dataset.id;
  
      const response = await fetch(`/admin/product-form/${productId}`);
      const html = await response.text();
      formContainer.innerHTML = html;
      scrollToForm();
    });
  

    // addBtn loads form for addition of a new product, distinguished by lack of ID
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
  