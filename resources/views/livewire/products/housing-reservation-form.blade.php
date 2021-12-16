<div>
    <button wire:click="initModal" class="btn btn-primary btn-block" data-toggle="modal" data-target="#reservation-modal">Solicitar reserva</button>

    <div wire:ignore.self class="modal fade" id="reservation-modal" data-backdrop="false">
        <div class="modal-dialog modal-lg modal-dialog-scrollable">
            <div class="modal-content">
                <div class="modal-header">
                    <h5 class="modal-title">Reservar</h5>
                    <button class="close" type="button" data-dismiss="modal" aria-label="Close"><span aria-hidden="true">&times;</span></button>
                </div>
                <div class="modal-body pb-2">
                    @if ($step === 1)
                        @if ($product->is_housing)
                            <div class="row mb-3">
                                <div class="col-md-6">
                                    <label for="">Fecha de Check In</label>
                                    <input type="date" class="form-control @error('checkInDate') is-invalid @enderror" wire:model="checkInDate" wire:change="resetCalculation" name="checkInDate">
                                </div>
                                <div class="col-md-6">
                                    <label for="">Fecha de Check Out</label>
                                    <input type="date" class="form-control @error('checkOutDate') is-invalid @enderror" wire:model="checkOutDate" wire:change="resetCalculation" name="checkOutDate">
                                </div>
                            </div>
                        @endif
                        <div class="row mb-3">
                            <div class="col-md-4">
                                <label for="">Nombre completo</label>
                                <input wire:model="name" name="name" type="text" class="form-control @error('name') is-invalid @enderror" placeholder="Ingresa tu nombre">
                            </div>
                            <div class="col-md-4">
                                <label for="">Email</label>
                                <input wire:model="email" name="email" type="email" class="form-control @error('email') is-invalid @enderror" placeholder="Ingresa tu correo">
                            </div>
                            <div class="col-md-4">
                                <label for="">Número celular</label>
                                <input wire:model="cellphone" name="cellphone" type="text" class="form-control @error('cellphone') is-invalid @enderror" placeholder="Ingresa tu número">
                            </div>
                        </div>
                        <div class="row mb-3">
                            <div class="col-md-4">
                                <label for="">Numero de adultos</label>
                                <input wire:model="adultsNumber" wire:change="resetCalculation" name="adultsNumber" type="number" class="form-control @error('adultsNumber') is-invalid @enderror" placeholder="Numero de adultos">
                            </div>
                            <div class="col-md-4">
                                <label for="">Numero de niños</label>
                                <input wire:model="childrensNumber" wire:change="resetCalculation" name="childrensNumber" type="number" class="form-control @error('childrensNumber') is-invalid @enderror" placeholder="Numero de niños">
                            </div>
                            <div class="col-md-4">
                                <button wire:click="calculatePrice" class="btn btn-info btn-block" style="margin-top: 29px">{{ $priceLabel }}</button>
                            </div>
                        </div>
                        <div class="row mb-3">
                            <div class="col">
                                <textarea class="form-control" wire:model="comments" name="comments" placeholder="Comentarios..." rows="5"></textarea>
                            </div>
                        </div>
                        <div class="row">
                            <div class="col">
                                <button {{ $canMakeReservation ? '' : 'disabled' }} wire:click="makeReservationEvent" class="btn btn-primary btn-block">Solicitar Reserva</button>
                            </div>
                        </div>
                    @elseif ($step === 2)
                        <div class="row">
                            <div class="col text-center"><h4>Solicitud de reserva creada exitosamente</h4></div>
                        </div>
                        <div class="row">
                            <div class="col">
                                Tu solicitud de reserva ha sido enviada al vendedor. En unos días tu reserva será revisada y en caso de ser aprobada, recibirás un correo en tu email con las instruciones para completar el pago y confirmar tu reserva
                            </div>
                        </div>
                    @endif
                </div>
                <!-- Footer-->
                <div class="modal-footer flex-wrap justify-content-between bg-secondary font-size-md">
                    <div class="px-2 py-1"><span class="text-muted"></span></div>
                </div>
            </div>
        </div>
    </div>
</div>
