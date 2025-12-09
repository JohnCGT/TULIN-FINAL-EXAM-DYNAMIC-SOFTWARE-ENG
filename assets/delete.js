document.addEventListener('DOMContentLoaded', () => {

    // Helper function for SweetAlert2 confirmation
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

    // Helper function for SweetAlert2 toast alerts
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
    document.querySelectorAll('.delete-project').forEach(btn => {
        btn.addEventListener('click', async (e) => {
            e.preventDefault();
            if(!await confirmDelete('Are you sure you want to delete this project?')) return;

            const id = btn.dataset.id;
            const res = await fetch('api.php', {
                method: 'POST',
                headers: { 'Content-Type': 'application/json' },
                body: JSON.stringify({ action: 'delete_project', id })
            });

            const data = await res.json();
            if(data.success){
                showAlert('Project deleted', 'success');
                btn.closest('.col-lg-6')?.remove();
            } else {
                showAlert('Error: ' + data.message, 'error');
            }
        });
    });

    // Delete Certification
    document.querySelectorAll('.delete-cert').forEach(btn => {
        btn.addEventListener('click', async (e) => {
            e.preventDefault();
            if(!await confirmDelete('Are you sure you want to delete this certification?')) return;

            const id = btn.dataset.id;
            const res = await fetch('api.php', {
                method: 'POST',
                headers: { 'Content-Type': 'application/json' },
                body: JSON.stringify({ action: 'delete_cert', id })
            });

            const data = await res.json();
            if(data.success){
                showAlert('Certification deleted', 'success');
                btn.closest('.col-lg-4')?.remove(); // correct wrapper for cert cards
            } else {
                showAlert('Error: ' + data.message, 'error');
            }
        });
    });

    // Delete Tech Stack Item
    document.querySelectorAll('.delete-tech').forEach(btn => {
        btn.addEventListener('click', async (e) => {
            e.preventDefault();
            if(!await confirmDelete('Are you sure you want to delete this technology?')) return;

            const index = btn.dataset.index;
            const getRes = await fetch('api.php', {
                method: 'POST',
                headers: { 'Content-Type': 'application/json' },
                body: JSON.stringify({ action: 'get_tech' })
            });
            const getData = await getRes.json();

            if(getData.success && getData.data) {
                let techArray = JSON.parse(getData.data.value || '[]');
                techArray.splice(index, 1);

                const updateRes = await fetch('api.php', {
                    method: 'POST',
                    headers: { 'Content-Type': 'application/json' },
                    body: JSON.stringify({ action: 'update_tech', data: techArray })
                });
                const updateData = await updateRes.json();

                if(updateData.success){
                    showAlert('Technology deleted', 'success');
                    btn.closest('.col-6')?.remove();
                } else {
                    showAlert('Error: ' + updateData.message, 'error');
                }
            }
        });
    });

    // Delete Personality Trait
    document.querySelectorAll('.delete-trait').forEach(btn => {
        btn.addEventListener('click', async (e) => {
            e.preventDefault();
            if(!await confirmDelete('Are you sure you want to delete this trait?')) return;

            const index = btn.dataset.index;
            const getRes = await fetch('api.php', {
                method: 'POST',
                headers: { 'Content-Type': 'application/json' },
                body: JSON.stringify({ action: 'get_personality' })
            });
            const getData = await getRes.json();

            if(getData.success && getData.data) {
                let personalityData = JSON.parse(getData.data.value || '{}');
                let traits = personalityData.traits || personalityData || [];
                traits.splice(index, 1);

                const updateRes = await fetch('api.php', {
                    method: 'POST',
                    headers: { 'Content-Type': 'application/json' },
                    body: JSON.stringify({ action: 'update_personality', data: { traits } })
                });
                const updateData = await updateRes.json();

                if(updateData.success){
                    showAlert('Trait deleted', 'success');
                    btn.closest('.badge')?.remove();
                } else {
                    showAlert('Error: ' + updateData.message, 'error');
                }
            }
        });
    });

    // Delete Bucket List Item
    document.querySelectorAll('.delete-bucket').forEach(btn => {
        btn.addEventListener('click', async (e) => {
            e.preventDefault();
            if(!await confirmDelete('Are you sure you want to delete this bucket list item?')) return;

            const index = btn.dataset.index;
            const getRes = await fetch('api.php', {
                method: 'POST',
                headers: { 'Content-Type': 'application/json' },
                body: JSON.stringify({ action: 'get_about' })
            });
            const getData = await getRes.json();

            if(getData.success && getData.data) {
                let aboutData = JSON.parse(getData.data.value || '{}');
                let bucket = aboutData.bucket || [];
                bucket.splice(index, 1);
                aboutData.bucket = bucket;

                const updateRes = await fetch('api.php', {
                    method: 'POST',
                    headers: { 'Content-Type': 'application/json' },
                    body: JSON.stringify({ action: 'update_about', data: aboutData })
                });
                const updateData = await updateRes.json();

                if(updateData.success){
                    showAlert('Bucket list item deleted', 'success');
                    btn.closest('.list-group-item')?.remove();
                } else {
                    showAlert('Error: ' + updateData.message, 'error');
                }
            }
        });
    });

});
