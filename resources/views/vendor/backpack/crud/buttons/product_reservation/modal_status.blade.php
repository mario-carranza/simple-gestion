<div class="modal fade" tabindex="-1" role="dialog" id="statusModal">

</div>

@push('after_scripts')
<script>
    async function openStatusModal(id) {
        try {
            const config = {
                type: 'GET',
                url: '{{ url('admin/productreservation/change_status/') }}/' + id
            }
            const html = await $.ajax(config); 
            $('#statusModal').html(html)
            $('#statusModal').modal('show')
        } catch (error) {
            new Noty({type: 'danger', text: 'Ocurrio un error',}).show();
        }
    }

    $(document).ready(function () {
        $('body').append($('#statusModal'))
    })
</script>
@endpush