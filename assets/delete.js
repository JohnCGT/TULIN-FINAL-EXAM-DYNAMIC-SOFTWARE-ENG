document.addEventListener('DOMContentLoaded', () => {

    // Shows a confirmation dialog and returns true if the user clicks "Yes"
    const confirmDelete = async (message) => {
        const result = await Swal.fire({
            title: message,
            icon: 'warning',
            showCancelButton: true,
            confirmButtonColor: '#d33',
            cancelButtonColor: '#3085d6',
            confirmButtonText: 'Yes',
            cancelButtonText: 'Cancel'
        });
        return result.isConfirmed;
    };

    // Displays a small toast message at the top-right corner
    const showAlert = (title, icon = 'success') => {
        Swal.fire({
            title: title,
            icon: icon,
            toast: true,
            position: 'top-end',
            showConfirmButton: false,
            timer: 1500,
            timerProgressBar: true,
            didOpen: (toast) => {
                toast.addEventListener('mouseenter', Swal.stopTimer)
                toast.addEventListener('mouseleave', Swal.resumeTimer)
            }
        });
    };

    // Delete Project
    // Listens for delete clicks on project delete buttons
    document.querySelectorAll('.delete-project').forEach(btn => {
        btn.addEventListener('click', async (e) => {
            e.preventDefault();

            // Ask for confirmation
            if(!await confirmDelete('Are you sure you want to delete this project?')) return;

            const id = btn.dataset.id;

            // Send delete request to API
            const res = await fetch('api.php', {
                method: 'POST',
                headers: { 'Content-Type': 'application/json' },
                body: JSON.stringify({ action: 'delete_project', id })
            });

            const data = await res.json();

            // If deletion successful, show toast and remove card
            if(data.success){
                showAlert('Project deleted', 'success');
                btn.closest('.col-lg-6')?.remove();
            } else {
                showAlert('Error: ' + data.message, 'error');
            }
        });
    });

    // Delete Certification
    // Handles deletion of certifications via API
    document.querySelectorAll('.delete-cert').forEach(btn => {
        btn.addEventListener('click', async (e) => {
            e.preventDefault();

            // Confirmation popup
            if(!await confirmDelete('Are you sure you want to delete this certification?')) return;

            const id = btn.dataset.id;

            // API request to delete certification
            const res = await fetch('api.php', {
                method: 'POST',
                headers: { 'Content-Type': 'application/json' },
                body: JSON.stringify({ action: 'delete_cert', id })
            });

            const data = await res.json();

            // Remove the correct card wrapper on success
            if(data.success){
                showAlert('Certification deleted', 'success');
                btn.closest('.col-lg-4')?.remove(); // correct wrapper for cert cards
            } else {
                showAlert('Error: ' + data.message, 'error');
            }
        });
    });

    // Delete Tech Stack Item
    // Removes an item stored in the tech array (saved in settings)
    document.querySelectorAll('.delete-tech').forEach(btn => {
        btn.addEventListener('click', function() {
            const url = this.getAttribute('data-delete-url');

            Swal.fire({
                title: 'Are you sure?',
                text: "This tech will be deleted!",
                icon: 'warning',
                showCancelButton: true,
                confirmButtonColor: '#d33',
                cancelButtonColor: '#3085d6',
                confirmButtonText: 'Yes, delete it!',
                reverseButtons: true
            }).then((result) => {
                if (result.isConfirmed) {
                    window.location.href = url;
                }
            });
        });
    });

});
