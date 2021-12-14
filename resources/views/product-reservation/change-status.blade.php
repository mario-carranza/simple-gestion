<div class="modal-dialog modal-lg" role="document">
    <div class="modal-content">
      <div class="modal-header">
        <h5 class="modal-title">Cambiar estado de la reservación</h5>
        <button type="button" class="close" data-dismiss="modal" aria-label="Close">
          <span aria-hidden="true">&times;</span>
        </button>
      </div>
      <div class="modal-body">
          <div class="row mb-3">
              <div class="col-md-6">
                <ul class="list-group list-group-flush">
                    <li class="list-group-item">Servicio: {{ $productReservation->product->name }}</li>
                    <li class="list-group-item">Fecha de Check In: {{ $productReservation->check_in_date->format('d/m/Y') }}</li>
                    <li class="list-group-item">Numero de adultos: {{ $productReservation->adults_number }}</li>
                    <li class="list-group-item">Nombre: {{ $productReservation->name }}</li>
                    <li class="list-group-item">Teléfono: {{ $productReservation->cellphone }}</li>
                </ul>
              </div>
              <div class="col-md-6">
                <ul class="list-group list-group-flush">
                    <li class="list-group-item">Fecha de solicitud: {{ $productReservation->created_at->format('d/m/Y') }}</li>
                    <li class="list-group-item">Fecha de Check Out: {{ $productReservation->check_out_date->format('d/m/y') }}</li>
                    <li class="list-group-item">Numero de niños: {{ $productReservation->childrens_number }}</li>
                    <li class="list-group-item">Email: {{ $productReservation->email }}</li>
                    <li class="list-group-item">Precio: {{ currencyFormat($productReservation->price, 'CLP', true) }}</li>
                </ul>
              </div>
          </div>
        <div class="row mb-3">
            <div class="col">
                <label for="">Comentarios del cliente</label>
                <textarea rows="5" class="form-control" readonly>
                    {{ $productReservation->customer_comment }}
                </textarea>
            </div>
        </div>
        <div class="row mb-3">
            <div class="col">
                <select name="reservation_status" id="" class="form-control">
                    <option value="{{ App\Models\ProductReservation::PENDING_STATUS }}" @if ($productReservation->reservation_status === App\Models\ProductReservation::PENDING_STATUS) selected @endif>Pendiente</option>
                    <option value="{{ App\Models\ProductReservation::ACCEPTED_STATUS }}" @if ($productReservation->reservation_status === App\Models\ProductReservation::ACCEPTED_STATUS) selected @endif>Aceptada</option>
                    <option value="{{ App\Models\ProductReservation::REJECTED_STATUS }}" @if ($productReservation->reservation_status === App\Models\ProductReservation::REJECTED_STATUS) selected @endif>Rechazada</option>
                </select>
            </div>
        </div>
        <div class="row mb-3" style="display: none" id="sellerComment">
            <div class="col">
                <label for="">Dejar un mensaje para el cliente (opcional)</label>
                <textarea name="seller_comment" rows="5" class="form-control">{{ $productReservation->seller_comment }}</textarea>
            </div>
        </div>
      </div>
      <div class="modal-footer">
        <button onclick="changeStatus()" type="button" class="btn btn-primary">Cambiar estado</button>
        <button type="button" class="btn btn-secondary" data-dismiss="modal">Cerrar</button>
      </div>
    </div>
</div>

<script>

    $(document).ready(function () {
        checkCommentField()
    })
    
    $('select[name=reservation_status]').change(function () {
       checkCommentField()
    })

    function checkCommentField() {
        if ($('select[name=reservation_status]').val() !== '{{  App\Models\ProductReservation::PENDING_STATUS }}' ) {
            $('#sellerComment').show()
        } else {
            $('#sellerComment').hide()
        }
    }
    async function changeStatus() {
        const config = {
            url: "{{ url('admin/productreservation/change_status') }}/{{ $productReservation->id }}",
            data: {
                'reservation_status': $('select[name=reservation_status').val(),
                'seller_comment': $('textarea[name=seller_comment').val(),
            },
            type: 'POST',
            headers: {
                'X-CSRF-TOKEN': '{{ csrf_token() }}',
            }
        }

        try {
            const result = $.ajax(config)
            $('#statusModal').modal('hide')
            new Noty({type: 'success',text: 'Estado guardado con éxito',}).show();
            crud.table.ajax.reload();
        } catch (error) {
            console.log(error)
        }
    }
</script>