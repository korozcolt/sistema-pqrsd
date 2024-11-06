// Function to get the status label based on the response.status
function getStatusLabel(status) {
    switch (status) {
        case 'in_progress':
            return 'En Proceso';
        case 'pending':
            return 'Pendiente';
        case 'resolved':
            return 'Resuelto';
        case 'closed':
            return 'Cerrado';
        default:
            return status;
    }
}

// Function to get the status badge class based on the response.status
function getStatusBadgeClass(status) {
    switch (status) {
        case 'in_progress':
            return 'badge-warning'; // Yellow
        case 'pending':
            return 'badge-primary'; // Blue
        case 'resolved':
            return 'badge-success'; // Green
        case 'closed':
            return 'badge-danger'; // Red
        default:
            return 'badge-secondary'; // Default gray badge
    }
}

// Document ready function
$(document).ready(function () {
    $('#verifyStatusForm').on('submit', function (e) {
        e.preventDefault();
        const id = $('#id').val();
        // Ajax request
        $.ajax({
            url: window.route('support.verify', {id: id}),
            type: 'GET',
            dataType: 'json',
            success: function (response) {
                if (response) {
                    const createdAt = new Date(response.created_at);
                    const updatedAt = new Date(response.updated_at);

                    const options = {
                        year: 'numeric',
                        month: '2-digit',
                        day: '2-digit',
                        hour: '2-digit',
                        minute: '2-digit',
                        second: '2-digit',
                        hour12: true,
                        timeZoneName: 'short',
                    };

                    const formattedCreatedAt = createdAt.toLocaleString('es-CO', options);
                    const formattedUpdatedAt = updatedAt.toLocaleString('es-CO', options);

                    $('#status_pqr').html(`
                        <span class="badge ${getStatusBadgeClass(response.status)}">${getStatusLabel(response.status)}</span>
                        <h3 class="mt-4">Mensaje: </h3><p>${response.message}</p>
                        <p><b>Teléfono: </b>${response.phone}</p>
                        <p><b>Email: </b>${response.email}</p>
                        <p><b>Creado:</b> ${formattedCreatedAt}</p>
                        <p><b>Última Actualización:</b> ${formattedUpdatedAt}</p>
                    `);
                } else {
                    $('#status_pqr').html('<p>No se encontró ningún PQR con ese número de radicado.</p>');
                }
            },
            error: function () {
                $('#status_pqr').html('<p>No se encontró ningún PQR con ese número de radicado.</p>');
            }
        });
    });
});
