document.addEventListener("DOMContentLoaded", () => {
    const formContainer = document.querySelector(".transaction-form-container");
    const rentButton = document.getElementById("btn-rent");
    const purchaseButton = document.getElementById("btn-purchase");

    if (rentButton) {
        rentButton.addEventListener("click", async () => {
            const productID = rentButton.dataset.productid;

            const response = await fetch(`/product/${productID}/rental-form`);
            const html = await response.text();
            formContainer.innerHTML = html;
            scrollToForm();
        })
    }

    if (purchaseButton) {
        purchaseButton.addEventListener("click", async () => {
            const productID = purchaseButton.dataset.productid;

            const response = await fetch(`/product/${productID}/purchase-form`);
            const html = await response.text();
            formContainer.innerHTML = html;
            scrollToForm();
        })
    }

    function scrollToForm() {
        formContainer.scrollIntoView({ behavior: 'smooth' });
    }
})