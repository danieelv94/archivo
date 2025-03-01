@extends('layouts.master', ['title' => 'Perfil'])

@section('styles')
    <link rel="stylesheet" href="{{  asset('vendor/jq/styles/jqx.base.css') }}" type="text/css" />
@endsection

@section('scripts')

    <script type="text/javascript" src="{{ config('app.env') != 'production' ? asset('vendor/vue/vue.js') : asset('vendor/vue/vue.min.js') }}"></script>
    <script type="text/javascript" src="{{  asset('vendor/parsley/parsley.min.js') }}"></script>

    <script>


        const app = new Vue({
            el: '#main',
            data: {
                usuario: {{ Js::from( $usuario ) }},
                resetPassword: {
                    current_password: '',
                    password: '',
                    password_confirmation: '',
                },
            },

            created(){},
            beforeMount(){},
            computed: {
            },
            mounted(){
                $('#frmChangePassword').parsley({
                    successClass: 'is-valid',
                    errorClass: 'is-invalid',
                    errorsWrapper: '<p class="mb-0  text-danger"></p>',
                    errorTemplate: '<p></p>',
                });
            },
            methods: {

                actualizar() {
                    let data = {...this.usuario};
                    $.ajax({
                        headers: {'X-CSRF-TOKEN': $('meta[name="csrf-token"]').attr('content')},
                        url: `{{ route('ccleh.perfil.new_password') }}/`,
                        type: 'post',
                        data: data,
                        // processData: false,
                        // contentType: false,
                        error(e) {

                            if (e.status === 413) {
                                toastr.error(`El archivo es demasiado pesado`);
                            } else {
                                try {
                                    let msgs = [];
                                    if (e.responseJSON.hasOwnProperty('errors')) {
                                        Object.values(e.responseJSON.errors).forEach(item => msgs.push(item));
                                        toastr.error(`Ocurrió un error y no se guardó la información <ul class="m-0"><li>${msgs.join('</li><li>')}</li></ul>`, '', {delay: 10000});
                                        app.errores = `<p>Se encontraron algunos errores</p><ul class="m-0"><li>${msgs.join('</li><li>')}</li></ul>`;
                                    } else {
                                        toastr.error(`Ocurrió un error y no se guardó la información <br>${e.responseJSON.message || e.responseJSON.exception || ''}`);
                                    }
                                } catch (ups) {
                                    if (e.status === 413) {
                                        toastr.error(`El archivo es demasiado pesado`);
                                    } else {
                                        toastr.error(`Ocurrió un error y no es posible guardar su registro`);
                                        console.error(e);
                                    }
                                } finally {
                                }
                            }



                        }


                    })
                        .done(e => {
                            if ( e && e.hasOwnProperty('success') && e.success) {
                                toastr.success('Guardado');
                            }
                        });
                },

                cambiarPassword(event) {
                    if ( ! $('#frmChangePassword').parsley().isValid() ) { return false;}

                    const data = {...this.resetPassword };

                    $.ajax({
                            headers: {'X-CSRF-TOKEN': $('meta[name="csrf-token"]').attr('content')},
                            url: `{{ route('ccleh.perfil.new_password') }}`,
                            type: 'post',
                            data: data,
                            // processData: false,
                            // contentType: false,
                            error(e) {
                                if (e.status === 413) {
                                    toastr.error(`El archivo es demasiado pesado`);
                                } else {
                                    try {
                                        let msgs = [];
                                        if (e.responseJSON.hasOwnProperty('errors')) {
                                            Object.values(e.responseJSON.errors).forEach(item => msgs.push(item));
                                            toastr.error(`Ocurrió un error y no se guardó la información <ul class="m-0"><li>${msgs.join('</li><li>')}</li></ul>`, '', {delay: 10000});
                                            app.errores = `<p>Se encontraron algunos errores</p><ul class="m-0"><li>${msgs.join('</li><li>')}</li></ul>`;
                                        } else {
                                            toastr.error(`Ocurrió un error y no se guardó la información <br>${e.responseJSON.message || e.responseJSON.exception || ''}`);
                                        }
                                            } catch (ups) {
                                                if (e.status === 413) {
                                                    toastr.error(`El archivo es demasiado pesado`);
                                                } else {
                                                    toastr.error(`Ocurrió un error y no es posible guardar su registro`);
                                                    console.error(e);
                                                }
                                            } finally {
                                        }
                                    }
                                }
                        })
                        .done(e => {
                            if ( e && e.hasOwnProperty('success') && e.success) {
                                toastr.success('Guardado');
                                if ( e.hasOwnProperty('personas')) {
                                    this.resetPassword =  {
                                        current_password: '',
                                            password: '',
                                            password_confirmation: '',
                                    };
                                }
                        }
                    });
                },


            }
        })

    </script>

@endsection


@section('content')

    <main id="main" class="main">
        <div class="pagetitle" >
            <h1>Perfil</h1>
        </div>
        <section class="section">

            <div class="row">
                <div class="col">

                    <div class="card">
                        <div class="card-body">
                            <div class="card-body pt-3">
                                <!-- Bordered Tabs -->
                                <ul class="nav nav-tabs nav-tabs-bordered">

                                    <li class="nav-item">
                                        <button class="nav-link active" data-bs-toggle="tab" data-bs-target="#profile-edit">Perfil</button>
                                    </li>

                                    <li class="nav-item">
                                        <button class="nav-link" data-bs-toggle="tab" data-bs-target="#profile-change-password">Cambiar contraseña</button>
                                    </li>

                                </ul>
                                <div class="tab-content pt-2">

                                    <div class="tab-pane fade profile-edit pt-3 active show" id="profile-edit">

                                        <!-- Profile Edit Form -->
                                        <form>

                                            <div class="row mb-3">
                                                <label for="titulo" class="col-md-4 col-lg-3 col-form-label">Grado escolar</label>
                                                <div class="col-md-8 col-lg-9">
                                                    <input name="titulo" disabled="" type="text" class="form-control"
                                                           id="titulo" v-model="usuario.titulo">
                                                </div>
                                            </div>

                                            <div class="row mb-3">
                                                <label for="nombres" class="col-md-4 col-lg-3 col-form-label">Nombres</label>
                                                <div class="col-md-8 col-lg-9">
                                                    <input name="nombres" disabled="" type="text" class="form-control"
                                                           id="nombres" v-model="usuario.nombres">
                                                </div>
                                            </div>

                                            <div class="row mb-3">
                                                <label for="primer_apellido" class="col-md-4 col-lg-3 col-form-label">Primer apellido</label>
                                                <div class="col-md-8 col-lg-9">
                                                    <input name="primer_apellido" disabled="" type="text" class="form-control"
                                                           id="primer_apellido" v-model="usuario.primer_apellido">
                                                </div>
                                            </div>
                                            <div class="row mb-3">
                                                <label for="segundo_apellido" class="col-md-4 col-lg-3 col-form-label">Segundo apellido</label>
                                                <div class="col-md-8 col-lg-9">
                                                    <input name="segundo_apellido" disabled="" type="text" class="form-control"
                                                           id="segundo_apellido" v-model="usuario.segundo_apellido">
                                                </div>
                                            </div>

                                            <div class="row mb-3">
                                                <label for="email" class="col-md-4 col-lg-3 col-form-label">Email</label>
                                                <div class="col-md-8 col-lg-9">
                                                    <input name="email" disabled=""  type="email" class="form-control" id="email" v-model="usuario.email">
                                                </div>
                                            </div>

                                            <div class="row mb-3">
                                                <label for="telefono" class="col-md-4 col-lg-3 col-form-label">Teléfono</label>
                                                <div class="col-md-8 col-lg-9">
                                                    <input name="telefono" disabled="" type="text" class="form-control" id="telefono" v-model="usuario.telefono">
                                                </div>
                                            </div>

                                            <div class="row mb-3">
                                                <label for="curp" class="col-md-4 col-lg-3 col-form-label">CURP</label>
                                                <div class="col-md-8 col-lg-9">
                                                    <input name="curp" disabled="" type="text" class="form-control" id="curp" v-model="usuario.curp">
                                                </div>
                                            </div>

                                            <div class="row mb-3">
                                                <label for="rfc" class="col-md-4 col-lg-3 col-form-label">RFC</label>
                                                <div class="col-md-8 col-lg-9">
                                                    <input name="rfc" disabled="" type="text" class="form-control" id="rfc" v-model="usuario.rfc">
                                                </div>
                                            </div>

                                            <div class="row mb-3">
                                                <label for="sexo" class="col-md-4 col-lg-3 col-form-label">Sexo</label>
                                                <div class="col-md-8 col-lg-9">
                                                    <select name="sexo" disabled="" id="sexo" class="form-control" v-model="usuario.sexo">
                                                        <option value="" selected disabled>Elige</option>
                                                        <option value="M">Mujer</option>
                                                        <option value="H">Hombre</option>
                                                    </select>
                                                </div>
                                            </div>

                                            <div class="row mb-3">
                                                <label for="fecha_nacimiento" class="col-md-4 col-lg-3 col-form-label">Fecha nacimiento</label>
                                                <div class="col-md-8 col-lg-9">
                                                    <input name="fecha_nacimiento" disabled="" type="date" class="form-control" id="fecha_nacimiento" v-model="usuario.fecha_nacimiento">
                                                </div>
                                            </div>

{{--                                            <div class="text-center">--}}
{{--                                                <button type="submit" class="btn btn-primary" disabled>Guardar</button>--}}
{{--                                            </div>--}}
                                        </form><!-- End Profile Edit Form -->

                                    </div>


                                    <div class="tab-pane fade pt-3" id="profile-change-password">
                                        <!-- Change Password Form -->
                                        <form data-parsley-validate="" id="frmChangePassword" @submit.prevent="cambiarPassword($event)">

                                            <div class="row mb-3">
                                                <label for="currentPassword" class="col-md-4 col-lg-3 col-form-label">Contraseña actual</label>
                                                <div class="col-md-8 col-lg-9">
                                                    <input name="password" type="password"
                                                           v-model="resetPassword.current_password"
                                                           required
                                                           class="form-control" id="currentPassword">
                                                </div>
                                            </div>

                                            <div class="row mb-3">
                                                <label for="newPassword" class="col-md-4 col-lg-3 col-form-label">Nueva</label>
                                                <div class="col-md-8 col-lg-9">
                                                    <input name="newpassword" type="password"
                                                           v-model="resetPassword.password"
                                                           required
                                                           data-parsley-equalto="#renewPassword"
                                                           class="form-control" id="newPassword">

                                                </div>
                                            </div>

                                            <div class="row mb-3">
                                                <label for="renewPassword" class="col-md-4 col-lg-3 col-form-label">Confirmar</label>
                                                <div class="col-md-8 col-lg-9">
                                                    <input name="renewpassword" type="password"
                                                           v-model="resetPassword.password_confirmation"
                                                           required
                                                           data-parsley-equalto="#newPassword"
                                                           class="form-control" id="renewPassword">
                                                </div>
                                            </div>

                                            <div class="text-center">
                                                <button type="submit" class="btn btn-primary">Cambiar contraseña</button>
                                            </div>
                                        </form><!-- End Change Password Form -->

                                    </div>

                                </div><!-- End Bordered Tabs -->

                            </div>
                        </div>
                    </div>

                </div>
            </div>

        </section>


    </main>

@endsection
