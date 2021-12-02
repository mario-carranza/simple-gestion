@extends('layouts.base')

@push('styles')
<style>
    @media (min-width: 1000px) {
        .travel-img {
            width: 75%;
        }
    }
</style>
@endpush

@section('content')
<div class="row mt-5 mx-0 mx-md-5">
    <div class="col text-center">
        {{-- <i class="navbar-tool-icon czi-rocket" style="font-size: 150px; margin-top:50px; margin-bottom:100px"></i> --}}
        <div class="row">
            <div class="col-12 col-md-6 mb-4 mb-md-0">
                <a href="https://www.domosocoa.cl/reservas/">
                    <img class="travel-img" src="{{ asset('img/turismo/domos-ocoa.jpg') }}">
                </a>
            </div>
            <div class="col-12 col-md-6">
                <img class="travel-img" src="{{ asset('img/turismo/tour-exp-campesina.jpg') }}" alt="">
            </div>
        </div>
        <img src="{{ asset('img/turismo_rural.png') }}" alt="">
    </div>
</div>
@endsection
