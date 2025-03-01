@extends('layouts.master', ['title' => 'Unidades Administrativas'])

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
                            { name: 'nombre', type: 'string' },
                            { name: 'siglas', type: 'string' },
                            { name: 'codigo', type: 'string' },
                            { name: 'tipo_area', type: 'string' },
                            { name: 'activo', type: 'bool' },
                            { name: 'titular_id', type: 'number' },
                            { name: 'titular_nombre', type: 'string', map: 'titular>nombre_completo_profesion' },

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
                            datafield: 'codigo',
                            width: 50
                        },
                        {
                            text: 'Nombre',
                            columntype: 'textbox',
                            datafield: 'nombre',
                            // width: 170
                        },
                        {
                            text: 'Área activa',
                            columntype: 'textbox',
                            width: 120,
                            cellsrenderer: (row, columnField, value, defaultHtml, columnProperties, data ) => {
                                let activo = '';
                                if( data.activo ){
                                    activo = `<h6><span class="badge badge-pill bg-success">Activa</span></h6>`;
                                }else{
                                    activo = `<h6><span class="badge badge-pill bg-danger">Inactiva</span</h6>`;
                                }
                                html = `<div class="d-flex justify-content-center align-items-center h-100 ps-1" style="">${ activo }</div>`;

                                return html;
                            },
                        },
                        {
                            text: 'Siglas',
                            columntype: 'textbox',
                            datafield: 'siglas',
                            width: 80
                        },
                        {
                            text: 'Titular',
                            columntype: 'textbox',
                            datafield: 'titular_nombre',
                            width: 190
                        },
                        {
                            text: 'Tipo',
                            columntype: 'textbox',
                            datafield: 'tipo_area',
                            width: 150,
                            cellsrenderer(row, columnField, value) {
                                const lst = {{ Js::from( $lista_tipo_area ) }};
                                let html = lst[ value ];
                                html = `<div class="d-flex align-items-center h-100 ps-1" style="">${ html }</div>`;

                                return html;
                            },
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
                                let btn1 = `<button title="Editar" class="btn m-1 btn-sm btn-outline-primary border-0" onclick="app.verEditar(${ data.id })"> <i class="bi-pencil"></i></button>`;
                                let btn2 = `<button title="Eliminar" class="btn m-1 btn-sm btn-outline-danger border-0" onclick="app.eliminar(${ data.id })"> <i class="bi-trash"></i></button>`;
                                defaultHtml = `<div class="d-flex justify-content-center align-items-center h-100" style="">${ btn1 }${ btn2 }</div>`;

                                return defaultHtml;

                            }

                        },
                    ]
                } );
            _GRID_SOURCE = source;
        };

        const NW = {
            isNew: true,
            id: null,
            nombre: '',
            siglas: '',
            tipo_area: '',
            titular_id: null,
            codigo: '',
            activo: true,

        };

        const app = new Vue({
            el: '#main',
            data: {
                filas: {{ Js::from( $areas ) }},
                wModalShowing: null,
                wModalShowingId: null,
                filaSeleccionada: {...NW},
                usuarios: {{ Js::from( $usuarios) }},
            },
            created(){},
            beforeMount(){},
            computed: {
                tipoAreas: function() {
                    return {{ Js::from( $lista_tipo_area ) }};
                },
            },
            mounted(){
                initGrid( this.filas );
                var myModalEl = document.getElementById('wEditar');
                myModalEl.addEventListener('shown.bs.modal', function (event) {
                    // console.log('shown.bs.modal');
                    // $('#titular').select2({
                    //     dropdownParent: $('#wEditar')
                    // });
                    if ( ! app.filaSeleccionada.hasOwnProperty('isNew') ) {
                        $('#wEditar').val(app.filaSeleccionada.titular_id);
                        $('#wEditar').trigger('change'); // Notify any JS components that the value changed
                    }
                })
            },
            methods: {
                exportarTabla(){
                    $("#grid").jqxGrid('exportview', 'xlsx', 'Unidades administrativas');
                },
                obtenerFilas() {
                    $.ajax({
                        headers: {'X-CSRF-TOKEN': $('meta[name="csrf-token"]').attr('content')},
                        url: `{{ route('ccleh.areas.get') }}`,
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
                                if ( e.hasOwnProperty('areas')) {
                                    this.filas = e.areas;
                                    _GRID_SOURCE.localdata = this.filas;
                                    _GRID.updatebounddata();
                                }
                            }
                        });
                },

                verEditar( id ) {
                    if ( typeof id === 'undefined') {
                        return false;
                    }

                    let fila = this.filas.find( a => Number(a.id) === Number(id) );

                    if ( ! fila ) {
                        return false;
                    }
                    // console.log(fila);
                    this.filaSeleccionada = {...fila};
                    this.wModalShowing = new bootstrap.Modal(document.getElementById('wEditar'));
                    this.wModalShowingId = 'wEditar';
                    this.wModalShowing.show();
                },
                eliminar(id){
                    // console.log(id);
                    cclehConfirm.fire({ text: '¿Estas seguro de eliminar?'}).then( (r) => {
                        // console.log(id);
                        if ( r.isConfirmed ) {
                            $.ajax({
                                    headers: {'X-CSRF-TOKEN': $('meta[name="csrf-token"]').attr('content')},
                                    url: `{{ url('ccleh/areas/eliminar/') }}/${ id }`,
                                    type: 'post',
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
                                        toastr.success('Registro borrado');
                                        this.obtenerFilas();
                                }
                            });
                        }
                    } );
                },

                actualizar() {
                    let crear = this.filaSeleccionada.isNew || false;
                    // this.filaSeleccionada.titular_id = $('#titular').select2('data')[0].id;
                    let data = {...this.filaSeleccionada};
                    // data.titular_id = $('#titular').select2('data')[0]?.id;
                    // console.log(data.titular_id);
                    // return;
                    $.ajax({
                        headers: {'X-CSRF-TOKEN': $('meta[name="csrf-token"]').attr('content')},
                        url: `{{ url('ccleh/areas') }}/${ crear ? 'nueva' : `actualizar/` + this.filaSeleccionada.id }`,
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
                                this.filaSeleccionada = {...NW};
                                this.obtenerFilas();
                            }
                        });
                },

                verNuevo(){

                    this.wModalShowing = new bootstrap.Modal(document.getElementById('wEditar'));
                    this.wModalShowingId = 'wEditar';

                    this.wModalShowing.show();

                    this.filaSeleccionada = {...NW};

                },

            }
        })

    </script>

@endsection


@section('content')

    <main id="main" class="main" v-cloak>
        <div class="pagetitle" >
            <h1>Unidades Administrativas</h1>
        </div>

        <div class="card pt-4">
            <div class="card-body">
                <div class="row mb-2">
                    <div class="col-6">
                        <button class="btn btn-teal" @click="verNuevo()">
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
        <div class="modal fade" id="wEditar" tabindex="-1" role="dialog" aria-hidden="true">
            <div class="modal-dialog modal-lg" role="document">
                <div class="modal-content" v-show="wModalShowingId==='wEditar'">
                    <form action="#" method="post" @submit.prevent="actualizar()">
                        <div class="modal-header">
                            <h5 class="modal-title">@{{ this.filaSeleccionada.isNew ? 'Crear nuevo' : 'Editar' }}</h5>
                            <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Cerrar"></button>

                        </div>
                        <div class="modal-body">

                            <div class="row g-3">
                                <div class="col-sm-6">
                                    <div class="form-floating">
                                        <input type="text" required minlength="5" maxlength="120" class="form-control"
                                               v-model="filaSeleccionada.nombre"
                                               id="titulo" placeholder="Nombre">

                                        <label for="titulo">Nombre</label>
                                    </div>
                                </div>
                                <div class="col-sm-3">
                                    <div class="form-floating">
                                        <input type="text" required minlength="1" maxlength="10" class="form-control"
                                               v-model="filaSeleccionada.codigo"
                                               id="cod" placeholder="Clave">
                                        <label for="cod">Clave</label>
                                    </div>
                                </div>
                                <div class="col-sm-3">
                                    <div class="form-floating">
                                        <input type="text" required minlength="1" maxlength="20" class="form-control"
                                               v-model="filaSeleccionada.siglas"
                                               id="nombres" placeholder="Siglas">
                                        <label for="nombres">Siglas</label>
                                    </div>
                                </div>

                                <div class="col-sm-5">
                                    <div class="form-floating mb-3">
                                        <select required class="form-select"
                                                id="tipo_area"
                                                v-model="filaSeleccionada.tipo_area"
                                                aria-label="Tipo Area">
                                            <option value="" selected disabled>Elige</option>
                                            <option v-for="(area,ix) in tipoAreas" :value="ix">
                                                @{{ area }}
                                            </option>
                                        </select>
                                        <label for="tipo_area">Tipo Area</label>
                                    </div>
                                </div>

                                <div class="col-sm-5">
                                    <div class="form-floating mb-3">
                                        <select required class="form-select"
                                                id="titular"
                                                v-model="filaSeleccionada.titular_id"
                                                {{-- @change="alert('cambio');" --}}
                                                aria-label="Titular">
                                            <option :value="null" selected disabled>Elige</option>
                                            <option v-for="(usuario,ix) in usuarios" :value="usuario.id" :data-ix="ix">
                                                @{{ usuario.nombre_completo }}
                                            </option>
                                        </select>
                                        <label for="titular">Titular</label>
                                    </div>
                                </div>

                                <div class="col-sm-2">
                                    <div class="form-floating mb-3">
                                        <select required class="form-select"
                                                id="activo"
                                                v-model="filaSeleccionada.activo"
                                                {{-- @change="alert('cambio');" --}}
                                                aria-label="Activo">
                                            <option :value="true">Activa</option>
                                            <option :value="false">Inactiva</option>
                                        </select>
                                        <label for="activo">Área activa</label>
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
