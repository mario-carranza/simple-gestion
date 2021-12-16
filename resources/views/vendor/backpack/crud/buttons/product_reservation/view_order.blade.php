@if ($entry->order_id)
<a href="{{ route('order.edit', ['id' => $entry->order_id]) }}" class="btn btn-sm btn-link">
    <i class="la la-money-bill "></i> Ver orden
</a>
@endif
