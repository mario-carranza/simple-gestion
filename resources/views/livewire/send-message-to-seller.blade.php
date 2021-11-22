<div>
    @if($enable !== false)
    <div wire:loading.remove wire:target="send">
    <p><strong>Para: </strong>{{ $seller->visible_name }}</p>
    @if ($enable=== 'error')
        <small class="text-danger">
            Parece que hubo un error al enviar el mensaje, reintentelo nuevamente
        </small>
    @endif
    <div>
        <textarea wire:model.debounce.700ms="order.arrange_messages.{{$seller->id}}.message"></textarea>
    </div>

    <div>
        @if($errors->has('order.arrange_messages.' . $seller->id . '.message'))
            <span class="text-danger">
                <small>{{$errors->first('order.arrange_messages.' . $seller->id . '.message')}}</small>
            </span>
        @endif
    </div>
    <a wire:click.prevent="send()" class="btn btn-primary mt-3" href="#">&nbsp; Enviar</a>
    </div>
    <div wire:loading wire:target="send">
        <p> Se está enviando el mensaje... </p>
    </div>
    @else
        <p> El mensaje para {{ $seller->visible_name }} se ha enviado con éxito</p>
    @endif
</div>
