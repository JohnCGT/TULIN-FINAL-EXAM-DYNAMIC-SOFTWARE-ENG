document.addEventListener('DOMContentLoaded', () => {
    const form = document.querySelector('form');

    if(form) {
        form.addEventListener('submit', async (e) => {
            const submitButton = document.activeElement;
            if(submitButton && submitButton.type === 'submit') {
                e.preventDefault();

                // Show confirmation first
                const confirmResult = await Swal.fire({
                    title: 'Are you sure you want to save these changes?',
                    icon: 'question',
                    showCancelButton: true,
                    confirmButtonText: 'Yes, save changes',
                    cancelButtonText: 'Cancel',
                    confirmButtonColor: '#198754',
                    cancelButtonColor: '#6c757d'
                });

                if(confirmResult.isConfirmed) {
                    // Show success alert
                    await Swal.fire({
                        title: 'Changes saved!',
                        icon: 'success',
                        timer: 1500,
                        showConfirmButton: false,
                        willClose: () => {
                            form.submit(); // submit the form after alert closes
                        }
                    });
                }
            }
        });
    }
});
