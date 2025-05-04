document.addEventListener('DOMContentLoaded', () => {
  document.querySelectorAll('.change-status-btn').forEach(btn => {
    btn.addEventListener('click', () => {
      const id = btn.closest('.rental-row').dataset.rentalId;
      const formRow = document.getElementById(`extend-form-${id}`);
      formRow.classList.toggle('d-none');
    });
  });

  document.querySelectorAll('.change-status-form').forEach(form => {
    form.addEventListener('submit', async (e) => {
      e.preventDefault();
      const id = form.dataset.rentalId;
      const newStatus = form.querySelector('[name="new_status"]').value;

      const response = await fetch(`/admin/rental/${id}/change-status`, {
        method: 'POST',
        headers: {
          'Content-Type': 'application/json',
          'X-Requested-With': 'XMLHttpRequest'
        },
        body: JSON.stringify({ new_status: newStatus })
      });

      if (response.ok) {
        location.reload();
      } else {
        alert('Failed to update status');
        console.log(response.text());
      }
    });
  });
});
