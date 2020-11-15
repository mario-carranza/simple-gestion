@extends(backpack_view('blank'))

@section('content')
    @if (session('error'))
    <div class="alert alert-danger pb-0">
        <ul class="list-unstyled">
                <li><i class="la la-info-circle"></i> {{ session('error') }}</li>
        </ul>
    </div>    
    @endif
    
    <div class="row mt-3">
        <div class="col">
            <h3>Subir productos masivamente</h3>
        </div>
    </div>

    <form method="POST" action="{{ route('products.bulk-upload-preview') }}" enctype="multipart/form-data">
        @csrf
        <div class="card">
            <div class="card-body row">
                <div class="form-group col-md-12 required">
                    <label>Archivo excel</label>
                    <input type="file" name="product-csv" value="" class="form-control">
                </div>

                @if ($admin)
                <div class="form-group col-md-12 required">
                    <label>Vendedor</label>
                    <select name="seller_id">
                        @foreach ($sellers as $seller)
                            <option value="{{ $seller->id }}">{{ $seller->visible_name }}</option>
                        @endforeach
                    </select>
                </div>
                @endif
            </div>
        </div>

        <div class="btn-group" role="group">
            <button type="submit" class="btn btn-success">
                <span class="la la-save" role="presentation" aria-hidden="true"></span> &nbsp;
                <span>Cargar productos</span>
            </button>
        </div>
    </form>
@endsection