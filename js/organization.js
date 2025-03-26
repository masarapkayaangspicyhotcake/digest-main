function openModal(action, orgId = null) {
    $.ajax({
        url: '../superadmin/organization_modal.php',
        type: 'GET',
        data: { action, orgId },
        success: function(response) {
            $('#modal-content').html(response);
            $('#modal').show();
        },
        error: function(xhr) {
            console.error('Error loading modal:', xhr);
        }
    });
}

function closeModal() {
    $('#modal').hide();
}

function submitForm(action) {
    const formData = new FormData($('#orgForm')[0]);
    formData.append('action', action);

    $.ajax({
        url: '../superadmin/org.php',
        type: 'POST',
        data: formData,
        contentType: false,
        processData: false,
        success: function(response) {
            alert(response);
            closeModal();
            location.reload();
        },
        error: function(xhr) {
            console.error('Error submitting form:', xhr);
        }
    });
}

function deleteOrganization(orgId) {
    if (confirm('Are you sure you want to delete this member?')) {
        $.ajax({
            url: 'org.php',
            type: 'POST',
            data: { action: 'delete', org_id: orgId },
            success: function(response) {
                alert(response);
                location.reload();
            },
            error: function(xhr) {
                console.error('Error deleting member:', xhr);
            }
        });
    }
}

// Close modal when clicking outside
window.onclick = function(event) {
    if (event.target === document.getElementById('modal')) {
        closeModal();
    }
};
