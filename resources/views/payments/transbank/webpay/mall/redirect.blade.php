@extends('layouts.base')
@section('content')
    <div class="container">
        <div class="row mt-5">
            <div class="col-12  text-center">

                <div class="display-4 text-center ">Redireccionando a WebPay Plus Mall </div>

            </div>
        </div>
        <div class="row">
            <div class="col-12 text-center">
                {{-- <img src="{{ asset('images/site/loading.gif') }}"
                    class="img-fluid w-50 mx-auto"> --}}
                <!-- Primary spinner -->
                <div class="spinner-grow text-primary" role="status">
                    <span class="sr-only">Loading...</span>
                </div>
            </div>
        </div>
        <div class="row">
            <div class="col-12 text-center">
                <form action="{{ $response['url'] }}" id="webpay_plus_mall_post" method="POST">
                    @csrf
                    <input class="btn btn-primary bg-light-blue" value="Haz click aquí si no has sido redireccionado..." type="submit">
                    <input type="hidden" name="TBK_TOKEN" value="{{ $response['token'] }}">
                </form>
            </div>
        </div>
    </div>
@endsection
@push('scripts')
    <script type="text/javascript">
        document.getElementById("webpay_plus_mall_post").submit();

    </script>
@endpush
