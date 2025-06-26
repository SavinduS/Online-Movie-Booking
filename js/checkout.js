document.addEventListener('DOMContentLoaded', function() {
            const paymentMethods = document.querySelectorAll('input[name="payment_method"]');
            const cardDetails = document.getElementById('card-details');

            paymentMethods.forEach(method => {
                method.addEventListener('change', function() {
                    if (this.value === 'card') {
                        cardDetails.classList.remove('hidden');
                    } else {
                        cardDetails.classList.add('hidden');
                    }
                });
            });
        });