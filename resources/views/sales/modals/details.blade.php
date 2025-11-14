<div class="modal fade" id="sale-details-modal" tabindex="-1" role="dialog" aria-labelledby="saleDetailsModalLabel" aria-hidden="true">
    <div class="modal-dialog modal-lg" role="document">
        <div class="modal-content">
            <div class="modal-header bg-primary">
                <h5 class="modal-title" id="sale-details-title">Detalles de Venta</h5>
                <button type="button" class="close" data-dismiss="modal" aria-label="Cerrar">
                    <span aria-hidden="true">&times;</span>
                </button>
            </div>
            <div class="modal-body">
                <div class="row mb-4">
                    <div class="col-md-6">
                        <h6 class="font-weight-bold">Información de la Venta</h6>
                        <p><strong>Fecha:</strong> <span id="sale-date"></span></p>
                        <p><strong>Estado:</strong> <span id="sale-status" class="badge"></span></p>
                    </div>
                    <div class="col-md-6">
                        <h6 class="font-weight-bold">Información del Cliente</h6>
                        <p><strong>Nombre:</strong> <span id="customer-name"></span></p>
                        <p><strong>Documento:</strong> <span id="customer-doc"></span></p>
                    </div>
                </div>
                
                <div class="table-responsive">
                    <table class="table table-bordered" id="sale-items">
                        <thead class="bg-light">
                            <tr>
                                <th>Código</th>
                                <th>Producto</th>
                                <th class="text-right">Cantidad</th>
                                <th class="text-right">Precio Unit.</th>
                                <th class="text-right">Subtotal</th>
                            </tr>
                        </thead>
                        <tbody>
                            <!-- Items will be loaded via JavaScript -->
                        </tbody>
                        <tfoot>
                            <tr>
                                <td colspan="4" class="text-right"><strong>Subtotal:</strong></td>
                                <td class="text-right" id="subtotal">S/ 0.00</td>
                            </tr>
                            <tr>
                                <td colspan="4" class="text-right"><strong>IGV (18%):</strong></td>
                                <td class="text-right" id="tax">S/ 0.00</td>
                            </tr>
                            <tr class="table-active">
                                <td colspan="4" class="text-right"><strong>Total:</strong></td>
                                <td class="text-right" id="total">S/ 0.00</td>
                            </tr>
                        </tfoot>
                    </table>
                </div>
                
                <div class="mt-3">
                    <h6 class="font-weight-bold">Observaciones:</h6>
                    <p id="sale-notes" class="mb-0"></p>
                </div>
            </div>
            <div class="modal-footer">
                <button type="button" class="btn btn-secondary" data-dismiss="modal">Cerrar</button>
                <button type="button" class="btn btn-primary" id="print-receipt">
                    <i class="fas fa-print"></i> Imprimir Comprobante
                </button>
            </div>
        </div>
    </div>
</div>

@push('scripts')
<script>
// Print receipt button
$('#print-receipt').click(function() {
    const saleId = $('#sale-details-title').text().replace('Venta #', '');
    if (saleId) {
        window.open(`/sales/${saleId}/receipt`, '_blank');
    }
});
</script>
@endpush
