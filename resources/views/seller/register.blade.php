@extends('layouts.base', ['collaboration' => true, 'header' => true, 'footer' => true])

@php
$selected = false;
$selected_category = "";
$selected_commune = "";
@endphp

@section('content')
<!-- Page Title-->
<div class="page-title-overlap bg-accent pt-4 bg-cp-gradient">
    <div class="container d-lg-flex justify-content-between">
        {{-- <div class="order-lg-1 pr-lg-4 text-center text-lg-left">
            <h1 class="h3 text-light mb-0">Solicitud de Inscripción</h1>
        </div> --}}
    </div>
</div>
<!-- Page Content-->
<div class="container mb-5 pb-3">
    <div class="bg-light box-shadow-lg rounded-lg overflow-hidden">
        <div class="row">
            <section class="col-12">
                <img src="{{ asset('img/steps-register.png') }}" alt="Pasos registro" class="img-fluid">
            </div>
            <!-- Content-->
            <section class="col-12 pt-2 pt-lg-4 pb-4 mb-3">
                <div class="pt-2 px-4 px-xl-5">
                    <img src="{{ asset('img/seller-register.png') }}" alt="Registro vendedor" class="img-fluid mt-5">

                    <!-- Title-->
                    <div class="row justify-content-md-center mt-3">
                        @if (isset($success))
                        <div class="alert alert-success alert-with-icon" role="alert">
                            <div class="alert-icon-box">
                                <i class="alert-icon czi-check-circle"></i>
                            </div>
                            {{ $success }}
                        </div>
                        @endif
                    </div>

                    <h2 class="h6 border-bottom pb-3 mt-3 mb-3">1 - Completa la Solicitud de Inscripción</h2>

                    <!-- Billing detail-->
                    <form method="POST" action="{{ route('seller.frontend.store') }}">
                        @csrf
                        <div class="row pb-4">
                            <div class="col-sm-6 form-group">
                                <label for="uid">RUT <span class='text-danger'>*</span></label>
                                <input class="form-control @error('uid') is-invalid @enderror" type="text" value="{{ old('uid') }}" id="uid" name="uid">
                                @error('uid')
                                <span class="invalid-feedback" role="alert">
                                    <strong>{{ $message }}</strong>
                                </span>
                                @enderror
                            </div>
                            <div class="col-sm-6 form-group">
                                <label for="name">Razón social <span class='text-danger'>*</span></label>
                                <input class="form-control @error('name') is-invalid @enderror" type="text" value="{{ old('name') }}" id="name" name="name">
                                @error('name')
                                <span class="invalid-feedback" role="alert">
                                    <strong>{{ $message }}</strong>
                                </span>
                                @enderror
                            </div>
                            <div class="col-sm-6 form-group">
                                <label for="visible_name">Nombre de tienda <span class='text-danger'>*</span></label>
                                <input class="form-control @error('visible_name') is-invalid @enderror" type="text" value="{{ old('visible_name') }}" id="visible_name" name="visible_name">
                                @error('visible_name')
                                <span class="invalid-feedback" role="alert">
                                    <strong>{{ $message }}</strong>
                                </span>
                                @enderror
                            </div>
                            <div class="col-sm-6 form-group">
                                <label for="mc-country">Categoría de tienda <span class='text-danger'>*</span></label>
                                <select class="custom-select @error('seller_category_id') is-invalid @enderror" id="visible_name" name="seller_category_id">
                                    <option value="" selected="{{ $selected_category }}" disabled>Selecciona una categoría...</option>
                                    @foreach(\App\Models\SellerCategory::orderBy('name','asc')->get() as $category)
                                    @if (!$selected && $category->id == old('seller_category_id'))
                                    <option selected value="{{ $category->id }}">{{ $category->name }}</option>
                                    @php
                                    $selected = true;
                                    @endphp
                                    @else
                                    <option value="{{ $category->id }}">{{ $category->name }}</option>
                                    @endif
                                    @endforeach
                                    @if (!$selected)
                                    @php
                                    $selected_category = "selected";
                                    @endphp
                                    @else
                                    @php
                                    $selected = false;
                                    @endphp
                                    @endif
                                </select>
                                @error('seller_category_id')
                                <span class="invalid-feedback" role="alert">
                                    <strong>{{ $message }}</strong>
                                </span>
                                @enderror
                            </div>
                            <div class="col-sm-6 form-group">
                                <label for="street">Calle <span class='text-danger'>*</span></label>
                                <input class="form-control @error('street') is-invalid @enderror" type="text" value="{{ old('street') }}" id="street" name="street">
                                @error('street')
                                <span class="invalid-feedback" role="alert">
                                    <strong>{{ $message }}</strong>
                                </span>
                                @enderror
                            </div>
                            <div class="col-sm-6 form-group">
                                <label for="number">Número <span class='text-danger'>*</span></label>
                                <input class="form-control @error('number') is-invalid @enderror" type="text" value="{{ old('number') }}" id="number" name="number">
                                @error('number')
                                <span class="invalid-feedback" role="alert">
                                    <strong>{{ $message }}</strong>
                                </span>
                                @enderror
                            </div>
                            <div class="col-sm-6 form-group">
                                <label for="subnumber">Casa/Dpto/Oficina</label>
                                <input class="form-control @error('subnumber') is-invalid @enderror" type="text" value="{{ old('subnumber') }}" id="subnumber" name="subnumber">
                                @error('subnumber')
                                <span class="invalid-feedback" role="alert">
                                    <strong>{{ $message }}</strong>
                                </span>
                                @enderror
                            </div>
                            <div class="col-sm-6 form-group">
                                <label for="commune_id">Comuna <span class='text-danger'>*</span></label>
                                <select class="custom-select @error('commune_id') is-invalid @enderror" id="commune_id" name="commune_id">
                                    <option value="" selected="{{$selected_commune}}" disabled>Selecciona una comuna...</option>
                                    @foreach(\App\Models\Commune::orderBy('name','asc')->get() as $commune)
                                    @if (!$selected && old('commune_id') == $commune->id)
                                    <option value="{{$commune->id}}" selected>{{$commune->name}}</option>
                                    @php
                                    $selected = true;
                                    @endphp
                                    @else
                                    <option value="{{$commune->id}}">{{$commune->name}}</option>
                                    @endif
                                    @endforeach
                                    @if (!$selected)
                                    @php
                                    $selected_commune = "selected";
                                    @endphp
                                    @endif
                                </select>
                                @error('address_commune_id')
                                <span class="invalid-feedback" role="alert">
                                    <strong>{{ $message }}</strong>
                                </span>
                                @enderror
                            </div>
                            <div class="col-sm-6 form-group">
                                <label for="legal_representative_name">Tu nombre <span class='text-danger'>*</span></label>
                                <input class="form-control @error('legal_representative_name') is-invalid @enderror" type="text" value="{{ old('legal_representative_name') }}" id="legal_representative_name" name="legal_representative_name">
                                @error('legal_representative_name')
                                <span class="invalid-feedback" role="alert">
                                    <strong>{{ $message }}</strong>
                                </span>
                                @enderror
                            </div>
                            <div class="col-sm-6 form-group">
                                <label for="email">Tu email <span class='text-danger'>*</span></label>
                                <input class="form-control @error('email') is-invalid @enderror" type="email" value="{{ old('email') }}" id="email" name="email">
                                @error('email')
                                <span class="invalid-feedback" role="alert">
                                    <strong>{{ $message }}</strong>
                                </span>
                                @enderror
                            </div>
                            <div class="col-sm-6 form-group">
                                <label for="phone">Tu teléfono</label>
                                <input class="form-control @error('phone') is-invalid @enderror" type="text" value="{{ old('phone') }}" id="phone" name="phone">
                                @error('phone')
                                <span class="invalid-feedback" role="alert">
                                    <strong>{{ $message }}</strong>
                                </span>
                                @enderror
                            </div>
                            <div class="col-sm-6 form-group">
                                <label for="web">Web</label>
                                <input class="form-control @error('web') is-invalid @enderror" type="text" value="{{ old('web') }}" id="web" name="web">
                                @error('web')
                                <span class="invalid-feedback" role="alert">
                                    <strong>{{ $message }}</strong>
                                </span>
                                @enderror
                            </div>
                            <div class="col-sm-6 form-group">
                                <label for="custom_1">¿Tienes contrato con Transbank? <span class='text-danger'>*</span></label><br>
                                <div class="custom-control custom-radio custom-control-inline">
                                    <input class="custom-control-input" type="radio" id="custom_1_si" value="1" name="custom_1" {{ (old('custom_1') == 1) ? 'checked' : '' }}>
                                    <label class="custom-control-label" for="custom_1_si">Sí</label>
                                </div>
                                <div class="custom-control custom-radio custom-control-inline">
                                    <input class="custom-control-input" type="radio" id="custom_1_no" value="0" name="custom_1" {{ (old('custom_1') == 0) ? 'checked' : ((old('custom_1') == 1) ? '' : 'checked') }}>
                                    <label class="custom-control-label" for="custom_1_no">No</label>
                                </div>
                                @error('custom_1')
                                <span class="invalid-feedback" role="alert">
                                    <strong>{{ $message }}</strong>
                                </span>
                                @enderror
                            </div>
                            <div class="col-sm-6 form-group">
                                <label for="custom_1">¿Tienes una oferta, producto, experiencia o servicio relacionado con el turismo rural en la región de Valparaíso? <span class='text-danger'>*</span></label><br>
                                <div class="custom-control custom-radio custom-control-inline">
                                    <input class="custom-control-input" type="radio" id="is_turismo_rural_yes" value="1" name="is_turismo_rural" {{ (old('is_turismo_rural') == 1) ? 'checked' : '' }}>
                                    <label class="custom-control-label" for="is_turismo_rural_yes">Sí</label>
                                </div>
                                <div class="custom-control custom-radio custom-control-inline">
                                    <input class="custom-control-input" type="radio" id="is_turismo_rural_no" value="0" name="is_turismo_rural" {{ (old('is_turismo_rural') == 0) ? 'checked' : ((old('is_turismo_rural') == 1) ? '' : 'checked') }}>
                                    <label class="custom-control-label" for="is_turismo_rural_no">No</label>
                                </div>
                                @error('custom_1')
                                <span class="invalid-feedback" role="alert">
                                    <strong>{{ $message }}</strong>
                                </span>
                                @enderror
                            </div>
                            <div class="col-sm-6 form-group">
                                <label for="custom_2">¿Qué despacho utilizas hoy? <span class='text-danger'>*</span></label>
                                <input class="form-control @error('custom_2') is-invalid @enderror" type="text" value="{{ old('custom_2') }}" id="custom_2" name="custom_2">
                                @error('custom_2')
                                <span class="invalid-feedback" role="alert">
                                    <strong>{{ $message }}</strong>
                                </span>
                                @enderror
                            </div>

                            <div class="col-12">
                                <p class="float-right">
                                    <button class="btn btn-primary" type="submit">Enviar</button>
                                </p>
                            </div>
                        </div>
                    </form>
                </div>
            </section>
        </div>
    </div>
</div>
@endsection

@push('scripts')
<script src="{{ asset('js/rut-formatter.js') }}"></script>
<script>
    $('#uid').rut();
</script>
@endpush
