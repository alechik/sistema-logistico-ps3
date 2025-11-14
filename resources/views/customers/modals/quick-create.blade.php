<div class="modal fade" id="customer-modal" tabindex="-1" role="dialog" aria-labelledby="customerModalLabel" aria-hidden="true">
    <div class="modal-dialog" role="document">
        <div class="modal-content">
            <div class="modal-header">
                <h5 class="modal-title" id="customerModalLabel">Nuevo Cliente</h5>
                <button type="button" class="close" data-dismiss="modal" aria-label="Cerrar">
                    <span aria-hidden="true">&times;</span>
                </button>
            </div>
            <form id="quick-customer-form">
                @csrf
                <div class="modal-body">
                    <div class="form-group">
                        <label for="name">Nombre o Razón Social *</label>
                        <input type="text" class="form-control" id="name" name="name" required>
                    </div>
                    <div class="row">
                        <div class="col-md-6">
                            <div class="form-group">
                                <label for="document_type">Tipo de Documento *</label>
                                <select class="form-control" id="document_type" name="document_type" required>
                                    <option value="DNI">DNI</option>
                                    <option value="RUC">RUC</option>
                                    <option value="CE">Carné de Extranjería</option>
                                    <option value="PASSPORT">Pasaporte</option>
                                </select>
                            </div>
                        </div>
                        <div class="col-md-6">
                            <div class="form-group">
                                <label for="document_number">Número de Documento *</label>
                                <input type="text" class="form-control" id="document_number" name="document_number" required>
                            </div>
                        </div>
                    </div>
                    <div class="form-group">
                        <label for="address">Dirección</label>
                        <input type="text" class="form-control" id="address" name="address">
                    </div>
                    <div class="row">
                        <div class="col-md-6">
                            <div class="form-group">
                                <label for="phone">Teléfono</label>
                                <input type="text" class="form-control" id="phone" name="phone">
                            </div>
                        </div>
                        <div class="col-md-6">
                            <div class="form-group">
                                <label for="email">Email</label>
                                <input type="email" class="form-control" id="email" name="email">
                            </div>
                        </div>
                    </div>
                </div>
                <div class="modal-footer">
                    <button type="button" class="btn btn-secondary" data-dismiss="modal">Cancelar</button>
                    <button type="submit" class="btn btn-primary">Guardar Cliente</button>
                </div>
            </form>
        </div>
    </div>
</div>

@push('scripts')
<script>
$(document).ready(function() {
    // Quick customer form submission
    $('#quick-customer-form').submit(function(e) {
        e.preventDefault();
        
        $.ajax({
            url: '{{ route("customers.quick-store") }}',
            type: 'POST',
            data: $(this).serialize(),
            success: function(response) {
                // Add new customer to select
                const customer = response.data;
                const option = new Option(
                    customer.name + ' - ' + customer.document_type + ': ' + customer.document_number, 
                    customer.id, 
                    true, 
                    true
                );
                
                $('#customer_id').append(option).trigger('change');
                $('#customer-modal').modal('hide');
                
                // Show success message
                const Toast = Swal.mixin({
                    toast: true,
                    position: 'top-end',
                    showConfirmButton: false,
                    timer: 3000,
                    timerProgressBar: true
                });
                
                Toast.fire({
                    icon: 'success',
                    title: 'Cliente registrado correctamente'
                });
                
                // Reset form
                $('#quick-customer-form')[0].reset();
            },
            error: function(xhr) {
                const errors = xhr.responseJSON?.errors;
                let errorMessage = 'Error al guardar el cliente';
                
                if (errors) {
                    errorMessage = '';
                    Object.values(errors).forEach(error => {
                        errorMessage += error[0] + '\n';
                    });
                }
                
                Swal.fire({
                    icon: 'error',
                    title: 'Error',
                    text: errorMessage
                });
            }
        });
    });
    
    // Show customer modal when clicking "Nuevo Cliente" button
    $('.new-customer-btn').click(function(e) {
        e.preventDefault();
        $('#customer-modal').modal('show');
    });
});
</script>
@endpush
