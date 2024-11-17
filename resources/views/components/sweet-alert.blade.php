@if (session('swal'))
    <script>
        document.addEventListener('DOMContentLoaded', function() {
            Swal.fire(@json(session('swal')));
        });
    </script>
@endif

@if ($errors->any())
    <script>
        document.addEventListener('DOMContentLoaded', function() {
            Swal.fire({
                icon: 'error',
                title: '¡Hay errores en el formulario!',
                html: `{!! implode('<br>', $errors->all()) !!}`,
                confirmButtonText: 'Entendido',
                customClass: {
                    confirmButton: 'btn btn-primary'
                }
            });
        });
    </script>
@endif
