@extends(backpack_view('blank'))

@section('content')
    
    <div class="row mt-3">
        <div class="col">
            <h3>Subir libros masivamente</h3>
        </div>
    </div>

    @if (session('error'))
    <div class="alert alert-danger pb-0">
        <ul class="list-unstyled">
                <li><i class="la la-info-circle"></i> {{ session('error') }}</li>
        </ul>
    </div>    
    @endif

    <form id="form-bulk" method="POST" action="{{ route('products.bulk-upload-preview') }}" enctype="multipart/form-data">
        @csrf
        <div class="card">
            <div class="card-body row">
                <div class="col-md-8 mt-2 mb-5" style="text-align: justify">
                    <span style="font-size: 17px">El primer paso es <a href="{{  asset('bulk/EXCEL_CARGA_MASIVA.xlsx') }}">descargar la plantilla EXCEL</a> donde podras agregar tus libros. 
                        La hoja de cálculos .csv contiene el texto de muestra, que te indica cómo formatear la información del producto. Es
                        Importante que no cambies ni borres los encabezados de las columnas, esto podria provocar problemas de compatibilidad
                        y evitaria la carga exitosa de los libros. 
                    </span>
                </div>

                <div class="form-group col-md-12 required">
                    <label><strong>Archivo CSV/EXCEL</strong></label>
                    <input required type="file" name="product-csv" value="" class="form-control" accept=".csv, application/vnd.openxmlformats-officedocument.spreadsheetml.sheet, application/vnd.ms-excel" >
                </div>

                <div class="form-group col-md-12 required">
                    <label><strong>Comprimido imagenes ZIP</strong></label>
                    <input required type="file" name="images-zip" value="" class="form-control" accept=".zip" >
                </div>

                @if ($admin)
                <div class="form-group col-md-12 required">
                    <label><strong>Vendedor</strong></label><br>
                    <select class="form-control"  name="seller_id">
                        @foreach ($sellers as $seller)
                            <option value="{{ $seller->id }}">{{ $seller->visible_name }}</option>
                        @endforeach
                    </select>
                </div>
                @endif

                @if ($userSeller)
                <input type="hidden" name="seller_id" value="{{ $userSeller->id }}">
                @endif

                <div class="col">
                    <div class="progress">
                        <div class="progress-bar progress-bar-striped bg-success" role="progressbar" style="width: 0%" aria-valuenow="25" aria-valuemin="0" aria-valuemax="100"></div>
                      </div>
                </div>
            </div>
        </div>

        <div class="btn-group" role="group">
            <button type="submit" class="btn btn-success">
                <span class="la la-save" role="presentation" aria-hidden="true"></span> &nbsp;
                <span>Vista previa de libros a cargar</span>
            </button>
        </div>
    </form>
@endsection
@push('after_scripts')
<script src="{{ asset('js/jquery.form.js') }}"></script> 
<script>
    $(function() {

        var bar = $('.progress-bar');
        var percent = $('.percent');
        var status = $('#status');

        $('#form-bulk').ajaxForm({
            target: 'body',
            beforeSend: function() {
                status.empty();
                var percentVal = '0%';
                percent.show();
                bar.width(percentVal);
                percent.html(percentVal);
            },
            uploadProgress: function(event, position, total, percentComplete) {
                var percentVal = percentComplete + '%';
                bar.width(percentVal);
                percent.html(percentVal);
            },
            complete: function(xhr) {
                status.html(xhr.responseText);
            }
        });
    }); 
</script>
@endpush