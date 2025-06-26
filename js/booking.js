// Add some interactivity
        document.addEventListener('DOMContentLoaded', function() {
            const form = document.querySelector('form');
            const submitBtn = form.querySelector('button[type="submit"]');
            
            form.addEventListener('change', function() {
                const formData = new FormData(form);
                const isComplete = formData.get('date') && formData.get('hall_id')&& formData.get('time');
                
                if (isComplete) {
                    submitBtn.classList.remove('opacity-50', 'cursor-not-allowed');
                    submitBtn.disabled = false;
                } else {
                    submitBtn.classList.add('opacity-50', 'cursor-not-allowed');
                    submitBtn.disabled = true;
                }
            });
            
            // Initially disable button
            submitBtn.classList.add('opacity-50', 'cursor-not-allowed');
            submitBtn.disabled = true;
        });