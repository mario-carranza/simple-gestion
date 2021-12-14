@if (!in_array($entry->reservation_status, [
        App\Models\ProductReservation::PAYED_STATUS,
        App\Models\ProductReservation::CANCELED_STATUS,
]))
<a href="#" onclick='openStatusModal({{ $entry->id }})' class="btn btn-sm btn-link">
    <i class="la la-calendar "></i> Cambiar estado
</a>
@endif
