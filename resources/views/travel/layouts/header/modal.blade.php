<!-- Sign in / sign up modal-->
<div class="modal fade" id="signin-modal" tabindex="-1" role="dialog">
    <div class="modal-dialog modal-dialog-centered" role="document">
        <div class="modal-content">
            <div class="modal-header">
                <ul class="nav nav-tabs card-header-tabs" role="tablist">
                    <li class="nav-item"><a class="nav-link active" href="#signin-tab" data-toggle="tab" role="tab" aria-selected="true"><i class="czi-unlocked mr-2 mt-n1"></i>Inicia sesión</a></li>
                    <li class="nav-item"><a class="nav-link" href="#signup-tab" data-toggle="tab" role="tab" aria-selected="false"><i class="czi-user mr-2 mt-n1"></i>Regístrate</a></li>
                </ul>
                <button class="close" type="button" data-dismiss="modal" aria-label="Close"><span aria-hidden="true">&times;</span></button>
            </div>
            <div class="modal-body tab-content py-4">
                <form method="POST" action="" class="needs-validation tab-pane fade show active" autocomplete="off" novalidate id="signin-tab">
                    @csrf
                    <div class="form-group">
                        <label for="si-email">Email</label>
                        <input class="form-control" name="email" type="email" id="si-email" placeholder="" required>
                        <div class="invalid-feedback">Por favor ingresa un email válido.</div>
                    </div>
                    <div class="form-group">
                        <label for="si-password">Contraseña</label>
                        <div class="password-toggle">
                            <input class="form-control" type="password" name="password" id="si-password" required>
                            <label class="password-toggle-btn">
                                <input class="custom-control-input" type="checkbox"><i class="czi-eye password-toggle-indicator"></i><span class="sr-only">Mostrar contraseña</span>
                            </label>
                        </div>
                    </div>
                    <div class="form-group d-flex flex-wrap justify-content-between">
                        <div class="custom-control custom-checkbox mb-2">
                            <input class="custom-control-input" type="checkbox" id="si-remember">
                            <label class="custom-control-label" for="si-remember">Recordarme</label>
                        </div><a class="font-size-sm" href="#">¿Olvidaste tu contraseña?</a>
                    </div>
                    <button class="btn btn-primary btn-block btn-shadow" type="submit">Iniciar sesión</button>
                </form>

                <form method="POST" action="" class="needs-validation tab-pane fade" autocomplete="off" novalidate id="signup-tab">
                    @csrf
                    <div class="form-group">
                        <label for="uid">RUT</label>
                        <input class="form-control" type="text" name="uid" id="uid" placeholder="Escribe aquí tu rut" required>
                        <div class="invalid-feedback">Por favor ingresa tu rut.</div>
                    </div>
                    <div class="form-group">
                        <label for="first_name">Nombre</label>
                        <input class="form-control" type="text" name="first_name" id="first_name" placeholder="Escribe aquí tu nombre" required>
                        <div class="invalid-feedback">Por favor ingresa tu nombre.</div>
                    </div>
                    <div class="form-group">
                        <label for="last_name">Apellido</label>
                        <input class="form-control" type="text" name="last_name" id="last_name" placeholder="Escribe aquí tu apellido" required>
                        <div class="invalid-feedback">Por favor ingresa tu apellido.</div>
                    </div>
                    <div class="form-group">
                        <label for="email">Email</label>
                        <input class="form-control" type="email" name="email" id="email" placeholder="Escribe aquí tu email" required>
                        <div class="invalid-feedback">Por favor ingresa un email válido.</div>
                    </div>
                    <div class="form-group">
                        <label for="password">Contraseña</label>
                        <div class="password-toggle">
                            <input class="form-control" type="password" name="password" id="password" required>
                            <label class="password-toggle-btn">
                                <input class="custom-control-input" type="checkbox"><i class="czi-eye password-toggle-indicator"></i><span class="sr-only">Mostrar contraseña</span>
                            </label>
                        </div>
                    </div>
                    <div class="form-group">
                        <label for="password_confirmation">Confirmar contraseña</label>
                        <div class="password-toggle">
                            <input class="form-control" type="password" name="password_confirmation" id="password_confirmation" required>
                            <label class="password-toggle-btn">
                                <input class="custom-control-input" type="checkbox"><i class="czi-eye password-toggle-indicator"></i><span class="sr-only">Mostrar contraseña</span>
                            </label>
                        </div>
                    </div>
                    <button class="btn btn-primary btn-block btn-shadow" type="submit">Registrar</button>
                </form>
            </div>
        </div>
    </div>
</div>
