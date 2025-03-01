@extends('layouts.master', ['title' => 'Usuarios'])

@section('styles')

@endsection

@section('scripts')
{{--    <script type="text/javascript" src="{{ config('app.env') != 'production' ? asset('vendor/vue/vue.js') : asset('vendor/vue/vue.min.js') }}"></script>--}}
    @include('layouts.includes.jquery-js-base')

    <script>

        let _GRID = null;
        let _GRID_SOURCE = null;
        const initGrid = (data) => {
            const source =
                {
                    localdata: data,
                    datatype: "array",
                    datafields:
                        [
                            { name: 'id', type: 'string' },
                            { name: 'titulo', type: 'string' },
                            { name: 'nombres', type: 'string' },
                            { name: 'primer_apellido', type: 'string' },
                            { name: 'segundo_apellido', type: 'string' },
                            { name: 'nombre_completo_profesion', type: 'string' },
                            { name: 'nombre_completo', type: 'string' },
                            { name: 'email', type: 'string' },
                            { name: 'telefono', type: 'string' },
                            { name: 'curp', type: 'string' },
                            { name: 'rfc', type: 'string' },
                            { name: 'sexo', type: 'string' },
                            { name: 'fecha_nacimiento', type: 'string' },
                            { name: 'user_id', type: 'string' },
                            { name: 'created_at', type: 'string' },
                            { name: 'updated_at', type: 'string' },
                            { name: 'avatar', type: 'string', map: 'user>avatar' },
                            { name: 'rol', type: 'string' },
                            { name: 'puesto', type: 'string' },
                            { name: 'departamento', type: 'string' },

                        ],
                    updaterow: function ( rowid, rowdata ) {
                        // synchronize with the server - send update command
                    }
                };

            const  dataAdapter = new jqx.dataAdapter( source );
            _GRID = new jqxGrid( "#grid",
                {
                    width: '100%',
                    source: dataAdapter,
                    filterable: true,
                    sortable: true,
                    showfilterbar: true,
                    pageable: true,
                    //autoheight: true,
                    columnsresize: true,
                    localization: jqEsMX,
                    rowsheight: 42,
                    enablebrowserselection: true,
                    columns: [
                        {
                            text: '',
                            datafield: 'avatar',
                            columntype: 'textbox',
                            sortable: false,
                            filterable: false,
                            menu: false,
                            exportable: false,
                            resizable: false,
                            width: 50,
                            cellsrenderer: (row, columnField, value, defaultHtml) => {
                                let img = `<img  alt="Avatar" src="${ value }" style="width: 24px; height: 24px"/>`;
                                defaultHtml = `<div class="d-flex justify-content-center align-items-center h-100" style="">${ img }</div>`;
                                return defaultHtml;
                            },
                        },
                        {
                            text: 'Nombre',
                            columntype: 'textbox',
                            datafield: 'nombre_completo',
                            // width: 170
                        },
                        {
                            text: 'Departamento',
                            columntype: 'textbox',
                            datafield: 'departamento',
                            width: 170
                        },
                        {
                            text: 'Puesto',
                            columntype: 'textbox',
                            datafield: 'puesto',
                            width: 170
                        },
                        {
                            text: 'CURP',
                            datafield: 'curp',
                            columntype: 'textbox',
                            width: 190,
                        },
                        {
                            text: 'Correo',
                            datafield: 'email',
                            columntype: 'textbox',
                            width: 250,
                        },
                        {
                            text: 'Tipo Usuario',
                            datafield: 'rol',
                            columntype: 'textbox',
                            width: 150,
                        },
                        {
                            text: '',
                            datafield: 'id',
                            cellsalign: 'center',
                            sortable: false,
                            filterable: false,
                            menu: false,
                            exportable: false,
                            resizable: false,
                            width: '80',
                            cellsrenderer: (row, columnField, value, defaultHtml, columnProperties, data ) => {

                                let btn1 = `<button title="Editar" class="btn m-1 btn-sm btn-outline-primary border-0" onclick="app.verEditarPersona(${ data.id })"> <i class="bi-pencil"></i></button>`;
                                let btn2 = `<button title="Eliminar" class="btn m-1 btn-sm btn-outline-danger border-0" onclick="app.eliminarPersona(${ data.id })"> <i class="bi-trash"></i></button>`;
                                defaultHtml = `<div class="d-flex justify-content-center align-items-center h-100" style="">${ btn1 }${ btn2 }</div>`;

                                return defaultHtml;

                            }

                        }
                    ]
                } );
            _GRID_SOURCE = source;
        };
        const curp2date = (curp) => {
            let m = curp.match( /^\w{4}(\w{2})(\w{2})(\w{2})/ );
            //miFecha = new Date(año,mes,dia)
            let anyo = parseInt(m[1],10)+1900;
            if( anyo < 1950 ) anyo += 100;
            let mes = parseInt(m[2], 10)-1;
            let dia = parseInt(m[3], 10);
            return (new Date( anyo, mes, dia ));
        }

        const BP = {
            isNew: true,
            curp: null,
            deleted_at: null,
            email: null,
            fecha_nacimiento: null,
            id: null,
            nombre_completo: null,
            nombre_completo_profesion: null,
            nombres: null,
            primer_apellido: null,
            rfc: null,
            segundo_apellido: null,
            sexo: null,
            telefono: null,
            titulo: null,
            updated_at: null,
            user: null,
            user_id: null,
            area_id: null,
            unidad_presupuestal: "Centro de Conciliación Laboral del Estado de Hidalgo",
            departamento_id: null,
            puesto: null,
            rol: null,
        };
        const app = new Vue({
            el: '#main',
            data: {
                filas: {{ Js::from( $personas) }},
                roles: {{ Js::from( $roles) }},
                filaActual: null,
                wModalShowing: null,
                wModalShowingId: null,
                editPerson: null,
                areas: [],
                departamentos: [],
                rol: null,
            },

            created(){},
            beforeMount(){},
            computed: {

            },
            mounted(){
                initGrid( this.filas );
                this.obtenerAreas();
            },
            methods: {
                exportarTabla(){
                    $("#grid").jqxGrid('exportview', 'xlsx', 'usuarios');
                },
                calcFechaNacimiento() {

                    if ( this.editPerson.curp && this.editPerson.curp.length === 18 ) {
                        try {
                            let fn = curp2date(this.editPerson.curp);
                            this.editPerson.fecha_nacimiento = fn.toJSON().substr(0, 10);
                            this.editPerson.fn = fn.toJSON().substr(0, 10);
                        }catch (e) {
                            this.editPerson.fecha_nacimiento = null;
                        }
                    }
                },

                calcSexo() {
                    if ( this.editPerson.curp && this.editPerson.curp.length === 18 ) {
                        if( ! ['M', 'H'].includes(this.editPerson.curp.substr(10,1).toUpperCase() ) ) {
                            this.editPerson.sexo = null;
                        }
                        this.editPerson.sexo = this.editPerson.curp.substr(10,1);
                    }
                },

                obtenerAreas(){
                    $.ajax({
                            headers: {'X-CSRF-TOKEN': $('meta[name="csrf-token"]').attr('content')},
                            url: `{{ route('ccleh.areas.get.activas') }}`,
                            type: 'get',
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
                                if ( e.hasOwnProperty('areas')) {
                                    this.areas = e.areas;
                                }
                        }
                    });
                },
                obtenerDepartamentos(areaId){
                    $.ajax({
                        headers: {'X-CSRF-TOKEN': $('meta[name="csrf-token"]').attr('content')},
                        url: `{{ url('ccleh/departamentos/getDepartamentosArea') }}/${areaId}`,
                        type: 'get',
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
                                }
                            }
                        }
                    })
                    .done(e => {
                        if ( e && e.hasOwnProperty('success') && e.success) {
                            if ( e.hasOwnProperty('departamentos')) {
                                this.departamentos = e.departamentos;
                            }
                        }
                    });
                },
                onChangeArea(event){
                    this.obtenerDepartamentos(event.target.value);
                },

                obtenerFilas() {
                    $.ajax({
                        headers: {'X-CSRF-TOKEN': $('meta[name="csrf-token"]').attr('content')},
                        url: `{{ route('ccleh.usuarios.get') }}`,
                        type: 'get',
                        // data: data,
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
                                if ( e.hasOwnProperty('personas')) {
                                    this.filas = e.personas;
                                    _GRID_SOURCE.localdata = this.filas;
                                    _GRID.updatebounddata();
                                }
                            }
                        });
                },

                verEditarPersona( id ) {
                    if ( typeof id === 'undefined') {
                        return false;
                    }

                    let persona = this.filas.find( a => Number(a.id) === Number(id) );

                    if ( ! persona ) {
                        return false;
                    }
                    this.editPerson = {...persona};
                    this.obtenerDepartamentos(this.editPerson.area_id);
                    this.wModalShowing = new bootstrap.Modal(document.getElementById('wEditarPersona'));
                    this.wModalShowingId = 'wEditarPersona';
                    this.wModalShowing.show();
                },

                actualizarPersona() {
                    let crear = this.editPerson.isNew || false;
                    let data = {...this.editPerson};
                    $.ajax({
                        headers: {'X-CSRF-TOKEN': $('meta[name="csrf-token"]').attr('content')},
                        url: `{{ url('ccleh/usuarios') }}/${ crear ? 'nuevo' : `actualizar/` + this.editPerson.id }`,
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
                            this.wModalShowing.hide();
                            this.wModalShowingId = null;
                            this.wModalShowing = null;
                            this.editPerson = null;
                            this.obtenerFilas();
                            // console.log(e.persona);
                        }
                    });
                },

                verNuevaPersona(){

                    this.departamentos = [];
                    this.wModalShowing = new bootstrap.Modal(document.getElementById('wEditarPersona'));
                    this.wModalShowingId = 'wEditarPersona';
                    this.wModalShowing.show();

                    this.editPerson = {...BP};

                },
                eliminarPersona(id){
                    cclehConfirm.fire({ text: '¿Estas seguro de eliminar?'}).then( (r) => {
                        if ( r.isConfirmed ) {
                            $.ajax({
                                    headers: {'X-CSRF-TOKEN': $('meta[name="csrf-token"]').attr('content')},
                                    url: `{{ url('ccleh/usuarios/eliminar') }}/${ id }`,
                                    type: 'post',
                                    data: {},
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
                                            }
                                        }
                                    }
                                })
                                .done(e => {
                                    if ( e && e.hasOwnProperty('success') && e.success) {
                                        toastr.success('Registro borrado');
                                        this.obtenerFilas();
                                }
                            });
                        }
                    });
                },

            }
        })



    </script>
@endsection



@section('content')
    <main id="main" class="main" v-cloak>
        {{-- <div class="col-lg-8 offset-lg-2 col-md-10 offset-md-1"> --}}

            <div class="pagetitle" >
                <h1>Usuarios</h1>
            </div>

            <div class="card pt-4">
                <div class="card-body">
                    <div class="row mb-2">
                        <div class="col-6">
                            <button class="btn btn-teal" @click="verNuevaPersona()">
                               <i class="bi bi-plus"></i> Nuevo
                            </button>
                        </div>
                        <div class="col-6 text-end">
                            <button class="btn btn-success" @click="exportarTabla()">
                                <i class="bi bi-file-earmark-excel"></i> Exportar
                            </button>
                        </div>
                    </div>
                    <section class="section">
                        <div id="grid"></div>
                    </section>
                </div>
            </div>

            <!--region Modal-->
            <div class="modal fade" id="wEditarPersona" tabindex="-1" role="dialog" aria-hidden="true">
                <div class="modal-dialog modal-lg" role="document">
                    <div class="modal-content" v-if="wModalShowingId==='wEditarPersona'">
                        <form action="#" method="post" @submit.prevent="actualizarPersona()">
                            <div class="modal-header">
                                <h5 class="modal-title">@{{ this.editPerson.isNew ? 'Crear nuevo' : 'Editar' }}</h5>
                                <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Cerrar"></button>

                            </div>
                            <div class="modal-body">

                                <div class="row g-3">
                                    <div class="col-sm-2">
                                        <div class="form-floating">
                                            <input type="text" class="form-control"
                                                   v-model="editPerson.titulo"
                                                   id="titulo" placeholder="Titulo">

                                            <label for="titulo">Titulo</label>
                                        </div>
                                    </div>
                                    <div class="col-sm-4">
                                        <div class="form-floating">
                                            <input type="text" required minlength="3" maxlength="120" class="form-control"
                                                   v-model="editPerson.nombres"
                                                   id="nombres" placeholder="Nombre(s)">
                                            <label for="nombres">Nombre(s)</label>
                                        </div>
                                    </div>
                                    <div class="col-sm-3">
                                        <div class="form-floating">
                                            <input type="text" required minlength="3" maxlength="120" class="form-control"
                                                   v-model="editPerson.primer_apellido"
                                                   id="primer_apellido" placeholder="Primer apellido">
                                            <label for="primer_apellido">Primer apellido</label>
                                        </div>
                                    </div>
                                    <div class="col-sm-3">
                                        <div class="form-floating">
                                            <input type="text" required minlength="3" maxlength="120" class="form-control"
                                                   v-model="editPerson.segundo_apellido"
                                                   id="segundo_apellido" placeholder="Segundo apellido">
                                            <label for="segundo_apellido">Segundo apellido</label>
                                        </div>
                                    </div>
                                    <div class="col-md-6">
                                        <div class="form-floating">
                                            <input type="email" required class="form-control" id="email"
                                                   v-model="editPerson.email"
                                                   placeholder="Correo electrónico">
                                            <label for="email">Correo electrónico</label>
                                        </div>
                                    </div>
                                    <div class="col-md-6">
                                        <div class="form-floating">
                                            <input type="text" maxlength="10" minlength="10" class="form-control" id="telefono"
                                                   v-model="editPerson.telefono"
                                                   placeholder="Teléfono">
                                            <label for="telefono">Teléfono</label>
                                        </div>
                                    </div>
                                    <div class="col-md-6">
                                        <div class="form-floating">
                                            <input type="text" required class="form-control" id="curp"
                                                   v-model.trim="editPerson.curp"
                                                   @change="calcFechaNacimiento();calcSexo();"
                                                   maxlength="18"
                                                   minlength="18"
                                                   style="text-transform: uppercase"
                                                   placeholder="CURP">
                                            <label for="curp">CURP</label>
                                        </div>
                                    </div>
                                    <div class="col-md-6">
                                        <div class="form-floating">
                                            <input type="text" class="form-control" id="rfc"
                                                   v-model="editPerson.rfc"
                                                   placeholder="RFC">
                                            <label for="rfc">RFC</label>
                                        </div>
                                    </div>

                                    <div class="col-md-6">
                                        <div class="form-floating mb-3">
                                            <select class="form-select" id="sexo"
                                                    v-model="editPerson.sexo"
                                                    aria-label="Sexo">
                                                <option selected disabled>Elige</option>
                                                <option value="M">Mujer</option>
                                                <option value="H">Hombre</option>
                                            </select>
                                            <label for="sexo">Sexo ( @{{ editPerson.sexo }} ) </label>
                                        </div>
                                    </div>


                                    <div class="col-md-6">
                                        <div class="form-floating">
                                            <input type="date" class="form-control" id="fecha_nacimiento"
                                                   v-model="editPerson.fecha_nacimiento"
                                                   placeholder="Fecha nacimiento">
                                            <label for="fecha_nacimiento">Fecha nacimiento( @{{ editPerson.fecha_nacimiento }} )</label>
                                        </div>
                                    </div>

                                    <hr>


                                    <div class="col-md-6">
                                        <div class="form-floating">
                                            <input type="password" minlength="4" class="form-control" id="clave"
                                                   v-model="editPerson.password"
                                                   placeholder="Contraseña">
                                            <label for="clave">Contraseña</label>
                                        </div>
                                    </div>

                                    <div class="col-md-6">
                                        <div class="form-floating mb-3">
                                            <select required class="form-select" id="rol"
                                                    v-model="editPerson.rol"
                                                    aria-label="Rol del usuario">
                                                <option selected="" disabled :value="null">Elige una opción</option>
                                                <option v-for="rol of roles" :value="rol">@{{ rol }}</option>

                                            </select>
                                            <label for="rol">Rol</label>
                                        </div>
                                    </div>

                                    <hr>

                                    <div class="col-md-6">
                                        <div class="form-floating mb-3">
                                            <input type="text" class="form-control" id="unidad_presupuestal"
                                                   readonly
                                                   v-model="editPerson.unidad_presupuestal"
                                                   placeholder="Unidad Presupuestal">
                                            <label for="unidad_presupuestal">Unidad Presupuestal</label>
                                        </div>
                                    </div>

                                    <div class="col-md-6">
                                        <div class="form-floating mb-3">
                                            <select required class="form-select" id="area_nombre"
                                                    @change="onChangeArea($event)"
                                                    v-model="editPerson.area_id"
                                                    aria-label="Unidad administrativa">
                                                <option selected="" disabled :value="null">Elige una opción</option>
                                                <option v-for="area of areas" :value="area.id">@{{ area.nombre }}</option>

                                            </select>
                                            <label for="area_nombre">Unidad administrativa</label>
                                        </div>
                                    </div>

                                    <div class="col-md-6">
                                        <div class="form-floating mb-3">
                                            <select required class="form-select" id="departamento"
                                                    v-model="editPerson.departamento_id"
                                                    aria-label="Departamento">
                                                <option selected="" disabled value="">Elige una opción</option>
                                                <option v-for="departamento of departamentos" :value="departamento.id">@{{ departamento.clave+' '+departamento.nombre }}</option>
                                            </select>
                                            <label for="departamento">Departamento</label>
                                        </div>
                                    </div>

                                    <div class="col-md-6">
                                        <div class="form-floating mb-3">
                                            <input type="text" minlength="5" maxlength="250" class="form-control" id="puesto"
                                                   v-model="editPerson.puesto"
                                                   placeholder="Puesto">
                                            <label for="puesto">Puesto</label>
                                        </div>
                                    </div>




                                </div>

                            </div>
                            <div class="modal-footer">
                                <button type="button" class="btn btn-secondary" data-bs-dismiss="modal">Cerrar</button>
                                <button type="submit" class="btn btn-teal">Guardar</button>
                            </div>
                        </form>
                    </div>
                </div>
            </div>
            <!--endregion Modal-->

        {{-- </div> --}}

    </main>
@endsection
