
@extends('layouts.master', ['title' => 'Departamentos'])

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
                            { name: 'clave', type: 'string' },
                            { name: 'nombre', type: 'string' },
                            { name: 'siglas', type: 'string' },
                            { name: 'activo', type: 'bool' },
                            { name: 'titular', map:'titular>nombre_completo_profesion' , type: 'string' },
                            { name: 'padre', map:'padre>nombre' ,type: 'string' },
                            { name: 'area', map:'area>nombre' ,type: 'string' },

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
                            text: 'Clave',
                            columntype: 'textbox',
                            datafield: 'clave',
                            width: 50
                        },
                        {
                            text: 'Nombre',
                            datafield: 'nombre',
                            columntype: 'textbox',
                        },
                        {
                            text: 'Dep. activo',
                            columntype: 'textbox',
                            width: 120,
                            cellsrenderer: (row, columnField, value, defaultHtml, columnProperties, data ) => {
                                let activo = '';
                                if( data.activo ){
                                    activo = `<h6><span class="badge badge-pill bg-success">Activo</span></h6>`;
                                }else{
                                    activo = `<h6><span class="badge badge-pill bg-danger">Inactivo</span</h6>`;
                                }
                                html = `<div class="d-flex justify-content-center align-items-center h-100 ps-1" style="">${ activo }</div>`;

                                return html;
                            },
                        },
                        {
                            text: 'Titular',
                            datafield: 'titular',
                            columntype: 'textbox',
                            width: 190
                        },
                        {
                            text: 'Departamento Superior',
                            datafield: 'padre',
                            columntype: 'textbox',
                            width: 220
                        },
                        {
                            text: 'Unidad administrativa',
                            datafield: 'area',
                            columntype: 'textbox',
                            width: 250
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

                                let btn1 = `<button title="Editar" class="btn m-1 btn-sm btn-outline-primary border-0" onclick="app.verEditarDepartamento(${ data.id })"> <i class="bi-pencil"></i></button>`;
                                let btn2 = `<button title="Eliminar" class="btn m-1 btn-sm btn-outline-danger border-0" onclick="app.eliminarDepartamento(${ data.id })"> <i class="bi-trash"></i></button>`;
                                defaultHtml = `<div class="d-flex justify-content-center align-items-center h-100" style="">${ btn1+btn2 }</div>`;

                                return defaultHtml;

                            }

                        }
                    ]
                } );
            _GRID_SOURCE = source;
        };

        const DepartamentoDefault = {
            isNew: true,
            updated_at: null,
            deleted_at: null,
            created_at: null,
            id: null,
            clave: null,
            nombre: null,
            iniciales: null,
            titular_id: null,
            padre_id: null,
            area_id: '',
            padre: null,
            titular: null,
            area: null,
            activo: true
        };

        const app = new Vue({
            el: '#main',
            data: {
                filas: {{ Js::from( $departamentos) }},
                filaActual: null,
                wModalShowing: null,
                wModalShowingId: null,
                editDepartamento: null,
                areas: [],
                titulares: [],
                padres: [],
                //departamentos: []
            },

            created(){},
            beforeMount(){},
            computed: {
            },
            mounted(){
                initGrid( this.filas );
                this.obtenerAreas();
                this.obtenerTitulares();
                {{-- var myModalEl = document.getElementById('wEditarDepartamento');
                myModalEl.addEventListener('shown.bs.modal', function (event) {
                    // console.log('shown.bs.modal');
                    $('#titular').select2({
                        dropdownParent: $('#wEditar')
                    });
                    if ( ! app.filaSeleccionada.hasOwnProperty('isNew') ) {
                        $('#wEditarDepartamento').val(app.filaSeleccionada.titular_id);
                        $('#wEditarDepartamento').trigger('change'); // Notify any JS components that the value changed
                    }
                }) --}}
                //this.obtenerDepartamentos();
            },
            methods: {
                exportarTabla(){
                    $("#grid").jqxGrid('exportview', 'xlsx', 'departamentos');
                },
                obtenerDepartamentos(){
                    $.ajax({
                        headers: {'X-CSRF-TOKEN': $('meta[name="csrf-token"]').attr('content')},
                        url: `{{ route('ccleh.departamentos.get') }}`,
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
                                this.filas = e.departamentos;
                                _GRID_SOURCE.localdata = this.filas;
                                _GRID.updatebounddata();
                            }
                        }
                    });
                },
                obtenerPadres(id = null){
                    let nuevo = this.editDepartamento.isNew || false;
                    $.ajax({
                        headers: {'X-CSRF-TOKEN': $('meta[name="csrf-token"]').attr('content')},
                        url: nuevo ? `{{ url('ccleh/departamentos/getPadres') }}` : `{{ url('ccleh/departamentos/getPadres') }}/${id}`,
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
                                this.padres = e.departamentos;
                            }
                        }
                    });
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
                obtenerTitulares(){
                    $.ajax({
                        headers: {'X-CSRF-TOKEN': $('meta[name="csrf-token"]').attr('content')},
                        url: `{{ route('ccleh.usuarios.get') }}`,
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
                            if ( e.hasOwnProperty('personas')) {
                                this.titulares = e.personas;
                            }
                        }
                    });
                },
                verEditarDepartamento( id ) {
                    if ( typeof id === 'undefined') {
                        return false;
                    }

                    let departamento = this.filas.find( a => Number(a.id) === Number(id) );

                    if ( ! departamento ) {
                        return false;
                    }
                    this.editDepartamento = {...departamento};
                    this.obtenerPadres(id);
                    this.wModalShowing = new bootstrap.Modal(document.getElementById('wEditarDepartamento'));
                    this.wModalShowingId = 'wEditarDepartamento';
                    this.wModalShowing.show();
                },
                verNuevoDepartamento(){

                    this.editDepartamento = {...DepartamentoDefault};
                    this.obtenerPadres();
                    this.wModalShowing = new bootstrap.Modal(document.getElementById('wEditarDepartamento'));
                    this.wModalShowingId = 'wEditarDepartamento';
                    this.wModalShowing.show();

                },
                eliminarDepartamento(id){
                    cclehConfirm.fire({ text: '¿Estas seguro de eliminar?'}).then( (r) => {
                        if ( r.isConfirmed ) {
                            $.ajax({
                                    headers: {'X-CSRF-TOKEN': $('meta[name="csrf-token"]').attr('content')},
                                    url: `{{ url('ccleh/departamentos/eliminar') }}/${ id }`,
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
                                        this.obtenerDepartamentos();
                                }
                            });
                        }
                    });
                },
                actualizarDepartamento() {
                    let crear = this.editDepartamento.isNew || false;
                    // this.filaSeleccionada.titular_id = $('#titular').select2('data');
                    let data = {...this.editDepartamento};
                    {{-- data.titular_id = $('#titular').select2('data')[0]?.id || null; --}}
                    $.ajax({
                        headers: {'X-CSRF-TOKEN': $('meta[name="csrf-token"]').attr('content')},
                        url: `{{ url('ccleh/departamentos') }}/${ crear ? 'nuevo' : `actualizar/` + this.editDepartamento.id }`,
                        type: 'post',
                        data: data,
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
                                toastr.success('Guardado');
                                this.wModalShowing.hide();
                                this.wModalShowingId = null;
                                this.wModalShowing = null;
                                this.editDepartamento = {...this.departamentoDefault};
                                this.obtenerDepartamentos();
                            }
                        });
                },

            }
        })

    </script>

@endsection

@section('content')
    <main id="main" class="main" v-cloak>
        <div class="pagetitle" >
            <h1>Departamentos</h1>
        </div>

        <div class="card pt-4">
            <div class="card-body">
                <div class="row mb-2">
                    <div class="col-6">
                        <button class="btn btn-teal" @click="verNuevoDepartamento()">
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
        <div class="modal fade" id="wEditarDepartamento" tabindex="-1" role="dialog" aria-hidden="true">
            <div class="modal-dialog modal-lg" role="document">
                <div class="modal-content" v-if="wModalShowingId==='wEditarDepartamento'">
                    <form action="#" method="post" @submit.prevent="actualizarDepartamento()">
                        <div class="modal-header">
                            <h5 class="modal-title">@{{ this.editDepartamento.isNew ? 'Crear nuevo' : 'Editar' }}</h5>
                            <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Cerrar"></button>

                        </div>
                        <div class="modal-body">

                            <div class="row g-3">
                                <div class="col-sm-2">
                                    <div class="form-floating">
                                        <input type="text" required minlength="1" maxlength="6" class="form-control"
                                               v-model="editDepartamento.clave"
                                               id="clave" placeholder="Clave">

                                        <label for="clave">clave</label>
                                    </div>
                                </div>
                                <div class="col-sm-6">
                                    <div class="form-floating">
                                        <input type="text" required minlength="5" maxlength="100" class="form-control"
                                               v-model="editDepartamento.nombre"
                                               id="nombre" placeholder="Nombre">
                                        <label for="nombre">Nombre</label>
                                    </div>
                                </div>
                                <div class="col-sm-2">
                                    <div class="form-floating">
                                        <input type="text" required minlength="1" maxlength="10" class="form-control"
                                               v-model="editDepartamento.iniciales"
                                               id="Siglas" placeholder="Siglas">
                                        <label for="Siglas">Siglas</label>
                                    </div>
                                </div>

                                <div class="col-sm-2">
                                    <div class="form-floating mb-3">
                                        <select class="form-select" id="activo"
                                                v-model="editDepartamento.activo"
                                                aria-label="Departamento Activo">
                                            <option :value="true">Activo</option>
                                            <option :value="false">Inactivo</option>
                                        </select>
                                        <label for="activo">Dep. activo</label>
                                    </div>
                                </div>

                                <div class="col-sm-4">
                                    <div class="form-floating mb-3">
                                        <select class="form-select" id="titular"
                                                v-model="editDepartamento.titular_id"
                                                aria-label="Titular">
                                            <option :value="null" selected="">Ninguno</option>
                                            <option v-for="titular of titulares" :value="titular.id">@{{ titular.nombre_completo_profesion }}</option>
                                        </select>
                                        <label for="titular">Titular</label>
                                    </div>
                                </div>
                                <div class="col-sm-4">
                                    <div class="form-floating mb-3">
                                        <select required class="form-select" id="area"
                                                v-model="editDepartamento.area_id"
                                                aria-label="Unidad administrativa">
                                            <option value="" selected="" disabled>Elige</option>
                                            <option v-for="area of areas" :value="area.id">@{{ area.nombre }}</option>
                                        </select>
                                        <label for="area">Unidad administrativa</label>
                                    </div>
                                </div>
                                <div class="col-sm-4">
                                    <div class="form-floating mb-3">
                                        <select class="form-select" id="padre"
                                                v-model="editDepartamento.padre_id"
                                                aria-label="Departamento Superior">
                                            <option :value="null" selected>Ninguno</option>
                                            <option v-for="padre of padres" :value="padre.id">@{{ padre.clave+' '+padre.nombre }}</option>
                                        </select>
                                        <label for="padre">Departamento Superior</label>
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
    </main>

@endsection
