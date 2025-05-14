// "My basket" scripts

document.addEventListener("DOMContentLoaded", () => {
    const rentalsBtn = document.getElementById("btn-rentals");
    const purchasesBtn = document.getElementById("btn-purchases");
    const rentalsSection = document.getElementById("rentals-section");
    const purchasesSection = document.getElementById("purchases-section");


    // Fade-ins of Rentals and Puchases tabs
    rentalsBtn.addEventListener("click", () => {
      rentalsBtn.classList.add("active");
      purchasesBtn.classList.remove("active");
      rentalsSection.classList.remove("d-none");
      rentalsSection.classList.add("show");
      purchasesSection.classList.add("d-none");
      purchasesSection.classList.remove("show");
    });

    purchasesBtn.addEventListener("click", () => {
      purchasesBtn.classList.add("active");
      rentalsBtn.classList.remove("active");
      purchasesSection.classList.remove("d-none");
      purchasesSection.classList.add("show");
      rentalsSection.classList.add("d-none");
      rentalsSection.classList.remove("show");
    });


    // Clicking "Extend" button makes this row's form for extension fade in
    // Twig (shop/my_orders.html.twig) decides which rows have these form and button
    document.querySelectorAll('.extend-btn').forEach(button => {
        button.addEventListener('click', () => {
          const id = button.dataset.rentalId;
          const row = document.getElementById(`extend-form-${id}`);
          row.classList.toggle('d-none');
        });
      });
    
    // Submission of time extension form.
    document.querySelectorAll('.extend-form').forEach(form => {
      form.addEventListener('submit', async (e) => {
        e.preventDefault();
        const id = form.dataset.rentalId;
        const dateInput = form.querySelector('input[name="new_end_date"]');
        const newDate = dateInput.value;
  
        const response = await fetch(`/rental/${id}/extend`, {
          method: 'POST',
          headers: {
            'Content-Type': 'application/json',
            'X-Requested-With': 'XMLHttpRequest'
          },
          body: JSON.stringify({ new_end_date: newDate })
        });
  
        if (response.ok) {
          location.reload();
        } else {
          alert('Nie udało się wydłużyć wypożyczenie.');
        }
      });
    });
  });