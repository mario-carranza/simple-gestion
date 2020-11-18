@extends('layouts.base')

@section('content')
<!-- Page title-->
    <div class="row d-flex justify-content-center pt-4">
        <div class="col-md-8">
            <h2 class="h4 mb-3">Servicio al cliente</h2>

            <form method="POST"">
                @csrf
                <div class="row">
                    <div class="col-sm-6">
                        <div class="input-group">
                            <label for="contact-type" class="control-label">Tipo de contacto <span class="text-danger">*</span></label><br>
                            <div class="w-100">
                                <select class="form-control" id="contact-type">
                                    <option selected>Seleccione una opción</option>
                                    <option value="1">Consulta</option>
                                    <option value="2">Reclamo</option>
                                    <option value="3">Sugerencia</option>
                                </select>
                            </div>
                          </div>
                    </div>
                    <div class="col-sm-6">
                        <div class="form-group">
                            <label for="first_name">Asunto <span class="text-danger">*</span></label>
                            <input class="form-control @error('first_name') is-invalid @enderror" type="text" name="first_name" id="first_name" placeholder="Escribe aquí tu nombre" value="{{ old('first_name') }}" required>
                            <div class="invalid-feedback">Por favor ingresa el asunto.</div>
                            @error('first_name')
                            <span class="invalid-feedback" role="alert">
                                <strong>{{ $message }}</strong>
                            </span>
                            @enderror
                        </div>
                    </div>
                    <div class="col-sm-6">
                        <div class="form-group">
                            <label for="first_name">Nombre <span class="text-danger">*</span></label>
                            <input class="form-control @error('first_name') is-invalid @enderror" type="text" name="first_name" id="first_name" placeholder="Escribe aquí tu nombre" value="{{ old('first_name') }}" required>
                            <div class="invalid-feedback">Por favor ingresa tu nombre.</div>
                            @error('first_name')
                            <span class="invalid-feedback" role="alert">
                                <strong>{{ $message }}</strong>
                            </span>
                            @enderror
                        </div>
                    </div>
                    <div class="col-sm-6">
                        <div class="form-group">
                            <label for="email">E-mail <span class="text-danger">*</span></label>
                            <input class="form-control @error('email') is-invalid @enderror" type="email" name="email" id="email" placeholder="Escribe aquí tu email" value="{{ old('email') }}" required>
                            <div class="invalid-feedback">Por favor ingresa un email válido.</div>
                            @error('email')
                            <span class="invalid-feedback" role="alert">
                                <strong>{{ $message }}</strong>
                            </span>
                            @enderror
                        </div>
                    </div>
                    <div class="col-sm-6">
                        <div class="form-group">
                            <label for="password">Teléfono <span class="text-danger">*</span></label>
                            <div class="password-toggle">
                                <input class="form-control @error('password') is-invalid @enderror" type="password" name="password" id="password" required>
                                <label class="password-toggle-btn">
                                    <input class="custom-control-input" type="checkbox"><span class="sr-only">Mostrar teléfono</span>
                                </label>
                            </div>
                            @error('password')
                            <span class="invalid-feedback" role="alert">
                                <strong>{{ $message }}</strong>
                            </span>
                            @enderror
                        </div>
                    </div>
                    <div class="col-sm-6">
                        <div class="form-group">
                            <label for="password_confirmation">N° de Orden <span class="text-danger">*</span></label>
                            <div class="password-toggle">
                                <input class="form-control @error('password_confirmation') is-invalid @enderror" type="password" name="password_confirmation" id="password_confirmation" required>
                                <label class="password-toggle-btn">
                                    <input class="custom-control-input" type="checkbox"><span class="sr-only">Mostrar contraseña</span>
                                </label>
                            </div>
                        </div>
                    </div>
                    <div class="col-sm-12">
                        <div class="form-group">
                            <label for="exampleFormControlTextarea1">Detalle <span class="text-danger">*</span></label>
                            <textarea class="form-control" id="exampleFormControlTextarea1" rows="3"></textarea>
                          </div>
                    </div>
                </div>
                <div class="text-right">
                    <button class="btn btn-primary" type="submit"><i class="czi-user mr-2 ml-n1"></i>Enviar</button>
                </div>
            </form>
        </div>
    </div>
@endsection
