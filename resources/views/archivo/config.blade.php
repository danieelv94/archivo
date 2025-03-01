@extends('layouts.master', ['title' => 'Calendario'])

@section('styles')

@endsection

@section('scripts')
    <script src="https://cdnjs.cloudflare.com/ajax/libs/dayjs/1.10.7/dayjs.min.js"></script>
    <script src="https://cdnjs.cloudflare.com/ajax/libs/dayjs/1.10.7/locale/es-mx.min.js"></script>
    <script>dayjs.locale('es-mx')</script>

{{--    <script type="text/javascript" src="{{ config('app.env') != 'production' ? asset('vendor/vue/vue.js') : asset('vendor/vue/vue.min.js') }}"></script>--}}
    @include('layouts.includes.jquery-js-base')

    <script>
        let _GRID = null;
        let _GRID_SOURCE = null;
        const _MESES_ = [
            '',
            'Enero',
            'Febrero',
            'Marzo',
            'Abril',
            'Mayo',
            'Junio',
            'Julio',
            'Agosto',
            'Septiembre',
            'Octubre',
            'Noviembre',
            'Diciembre',
        ];
        const initGrid = (data) => {
            const source =
                {
                    localdata: data,
                    datatype: "array",
                    datafields:
                        [
                            { name: 'id', type: 'number' },
                            { name: 'anio_captura', type: 'number' },
                            { name: 'mes_captura', type: 'number' },
                            { name: 'nombre_mes_captura', type: 'string' },
                            { name: 'fecha_hora_limite_captura', type: 'date'},
                            { name: 'permite_captura', type: 'string'},

                        ],
                    updaterow: function ( rowid, rowdata ) {
                        // synchronize with the server - send update command
                    },
                    beforeLoadComplete: function (data ){
                        data = data.map( (row) => {
                            row.nombre_mes_captura = _MESES_[ row.mes_captura ];
                            return row;
                        } );
                        return data;
                    }
                };

            const  dataAdapter = new jqx.dataAdapter( source );
            _GRID = new jqxGrid( "#grid",
                {
                    width: '100%',
                    source: dataAdapter,
                    filterable: true,
                    sortable: true,
                    filterbarmode: 'simple',
                    showfilterbar: true,
                    pageable: true,
                    //autoheight: true,
                    columnsresize: true,
                    localization: jqEsMX,
                    rowsheight: 42,
                    enablebrowserselection: true,
                    columns: [
                        {
                            text: 'ID',
                            datafield: 'id',
                            columntype: 'textbox',
                            sortable: true,
                            filterable: true,
                            menu: true,
                            exportable: true,
                            resizable: false,
                            width: 50,
                        },
                        {
                            text: 'Año captura',
                            columntype: 'textbox',
                            datafield: 'anio_captura',
                            // width: 170
                        },
                        {
                            text: 'Mes de captura',
                            columntype: 'textbox',
                            cellsrenderer: (row, columnField, value, defaultHtml, columnProperties, data ) => {
                                defaultHtml = `<div class="d-flex justify-content-left align-items-center h-100">${data.mes_captura+' - '+_MESES_[data.mes_captura]}</div>`;
                                return defaultHtml;
                            }
                        },
                        {
                            text: 'Fecha límite de captura',
                            columntype: 'textbox',
                            datafield: 'fecha_hora_limite_captura',
                            cellsformat: 'dd/MM/yyyy hh:mm:ss tt'
                            // width: 170
                        },
                        {
                            text: 'Captura extemporánea',
                            columntype: 'textbox',
                            exportable: false,
                            cellsrenderer: (row, columnField, value, defaultHtml, columnProperties, data ) => {
                                if(data.permite_captura === 'true' ){
                                    defaultHtml = `<div class="d-flex justify-content-center align-items-center h-100"><h6><div class="badge badge-pill bg-success me-2">Abierta</div></h6></div>`;
                                }else{
                                    defaultHtml = `<div class="d-flex justify-content-center align-items-center h-100"><h6><div class="badge badge-pill bg-danger me-2">Cerrada</div></h6></div>`;
                                }
                                return defaultHtml;
                            }
                            // datafield: 'permite_captura',
                            // width: 170
                        },
                        {
                            text: '',
                            datafield: 'null',
                            sortable: false,
                            filterable: false,
                            menu: false,
                            exportable: false,
                            resizable: false,
                            width: '150',
                            cellsrenderer: (row, columnField, value, defaultHtml, columnProperties, data ) => {

                                let btn1 = `<button title="Editar" class="btn m-1 btn-outline-primary border-0" onclick="app.verEditar(${ data.id })"> <i class="bi-pencil"></i></button>`;
                                let btn2 = `<button title="Eliminar" class="btn m-1 btn-outline-danger border-0" onclick="app.eliminarFila(${ data.id })"> <i class="bi-trash"></i></button>`;
                                let btn3 = ``;
                                if(data.permite_captura === 'true' ){
                                    btn3 += `<button title="Cerrar captura extemporánea" class="btn m-1 btn-outline-danger border-0" onclick="app.cambiarEstado(${ data.id },${ data.permite_captura })"><i class="bi bi-lock"></i></button>`;
                                }else{
                                    btn3 += `<button title="Abrir captura extemporánea" class="btn m-1 btn-outline-success border-0" onclick="app.cambiarEstado(${ data.id },${ data.permite_captura })"><i class="bi bi-unlock"></i></button>`;
                                }
                                defaultHtml = `<div class="d-flex justify-content-center align-items-center h-100" style="">${ btn1 + btn2 + btn3 }</div>`;

                                return defaultHtml;

                            }

                        }
                    ]
                } );
            _GRID_SOURCE = source;
        };

        const NW = {
            isNew: true,
            id: null,
            anio_captura: '',
            mes_captura: '',
            fecha_hora_limite_captura: '',
        };

        const app = new Vue({
            el: '#main',
            data: {
                dayjs: dayjs,
                estadoCaptura: {{ Js::from( $estado_captura )  }},
                filas: {{ Js::from( $calendarios  ) }},
                wModalShowing: null,
                wModalShowingId: null,
                filaSeleccionada: {...NW},
                anio_old: null,
                anio_new: null,
                anios: null,
            },

            created(){},
            beforeMount(){},
            computed: {
                fecha_hora_limite_captura(){
                    if( (!  this.wModalShowing.isNew && this.wModalShowing.fecha_hora_limite_captura ) ) {
                        let x = this.wModalShowing.fecha_hora_limite_captura;
                        return `${ x.getDay() }/${ x.getMonth() }`
                    }
                }
            },
            mounted(){
                initGrid( this.filas );
                // console.log(this.filas);
                this.wModalShowing = new bootstrap.Modal(document.getElementById('wEditar'));

                document.getElementById('wEditar').addEventListener('show.bs.modal', function () {});
                this.obtenerAnios();

            },
            methods: {
                exportarTabla(){
                    $("#grid").jqxGrid('exportview', 'xlsx', 'calendario');
                },
                eliminarFila(id){
                    cclehConfirm.fire({ text: '¿Estas seguro de eliminar?'}).then( (r) => {
                        if ( r.isConfirmed ) {
                            $.ajax({
                                    headers: {'X-CSRF-TOKEN': $('meta[name="csrf-token"]').attr('content')},
                                    url: `{{ url('ccleh/admin/calendario/delete/') }}/${ id }`,
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
                cambiarCaptura(){
                    const data = { estado: !this.estadoCaptura};
                    $.ajax({
                            headers: {'X-CSRF-TOKEN': $('meta[name="csrf-token"]').attr('content')},
                            url: `{{ route('ccleh.admin.calendario.update.estado_captura') }}`,
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
                                            } finally {
                                        }
                                    }
                                }
                        })
                        .done(e => {
                            if ( e && e.hasOwnProperty('success') && e.success) {
                                toastr.success('Guardado');
                                this.estadoCaptura = !this.estadoCaptura;
                        }
                    });
                },
                onConfirmCambioCaptura(){
                    cclehConfirm.fire({ text: '¿Estas seguro? '})
                        .then( r => {
                            if ( r.isConfirmed ) {
                                this.cambiarCaptura();
                            }

                        });
                },
                obtenerFilas() {
                    $.ajax({
                        headers: {'X-CSRF-TOKEN': $('meta[name="csrf-token"]').attr('content')},
                        url: `{{ route('ccleh.admin.calendario.get_calendarios') }}`,
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
                                if ( e.hasOwnProperty('calendarios')) {
                                    this.filas = e.calendarios;
                                    // console.log(this.filas);
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
                    this.filaSeleccionada = {...fila};
                    this.wModalShowingId = 'wEditar';
                    this.wModalShowing.show();
                },

                verNuevo(){
                    this.filaSeleccionada = {...NW};
                    this.wModalShowing = new bootstrap.Modal(document.getElementById('wEditar'));
                    this.wModalShowingId = 'wEditar';
                    this.wModalShowing.show();
                },

                verDuplicar(){
                    this.anio_old = null;
                    this.anio_new = null;
                    this.wModalShowing = new bootstrap.Modal(document.getElementById('wEditar'));
                    this.wModalShowingId = 'wDuplicar';
                    this.wModalShowing.show();
                },

                guardarCalendario(){
                    let x = document.getElementById('fecha_hora_limite_captura').value;
                    this.filaSeleccionada.fecha_hora_limite_captura = x;
                    this.actualizarCalendario();
                },
                actualizarCalendario(){
                        const data = {...this.filaSeleccionada};
                        $.ajax({
                            headers: {'X-CSRF-TOKEN': $('meta[name="csrf-token"]').attr('content')},
                            url: `{{ url('ccleh/admin/calendario/') }}/${ this.filaSeleccionada.isNew ? 'create' : `update/${ this.filaSeleccionada.id }` }`,
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
                                if (e && e.hasOwnProperty('success') && e.success) {
                                    toastr.success('Guardado');
                                    this.filaSeleccionada = {...NW};;
                                    this.wModalShowing.hide();
                                    this.wModalShowingId = null;
                                    this.obtenerFilas();
                                }
                            });
                },
                cambiarEstado(id,estado){
                    let mensaje = estado ? '¿Estas seguro de Cerrar la captura extemporánea de este mes?' : '¿Estas seguro de Abrir la captura extemporánea de este mes?' ;
                    cclehConfirm.fire({ text: mensaje}).then( (r) => {
                        if ( r.isConfirmed ) {
                            $.ajax({
                                    headers: {'X-CSRF-TOKEN': $('meta[name="csrf-token"]').attr('content')},
                                    url: `{{ url('ccleh/admin/calendario/cambiarEstado/') }}/${ id }/${ estado }`,
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
                                        toastr.success('Captura actualizada');
                                        this.obtenerFilas();
                                }
                            });
                        }
                    });
                },
                duplicarCalendario(){
                    let data = {
                        anioOld: this.anio_old,
                        anioNew: this.anio_new,
                    };
                    $.ajax({
                            headers: {'X-CSRF-TOKEN': $('meta[name="csrf-token"]').attr('content')},
                            url: `{{ url('ccleh/admin/calendario/duplicar-calendario/') }}`,
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
                                            } finally {
                                        }
                                    }
                                }
                        })
                        .done(e => {
                            if ( e && e.hasOwnProperty('success') && e.success) {
                                toastr.success('Calendario duplicado');
                                this.wModalShowing.hide();
                                this.wModalShowingId = null;
                                this.obtenerFilas();
                                this.obtenerAnios();
                        }
                    });
                },
                obtenerAnios() {
                    $.ajax({
                        headers: {'X-CSRF-TOKEN': $('meta[name="csrf-token"]').attr('content')},
                        url: `{{ route('ccleh.admin.calendario.listar.anios') }}`,
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
                                if ( e.hasOwnProperty('anios')) {
                                    this.anios = e.anios;

                                }
                            }
                        });
                },
            }
        })

    </script>

@endsection


@section('content')

    <main id="main" class="main" v-cloak>
        {{-- <div class="col-md-8 offset-md-2"> --}}
            <div class="pagetitle" >
                <h1>Calendario</h1>
            </div>

            <section class="section">
                <div class="row">
                    <div class="col">
                        <div class="card">
                            <div class="card-body">
                                <form action="#" method="post">

                                    <div class="row mt-3">
                                        <div class="col-md-3">
                                            <label for="inputText" class="col-form-label">Captura de inventario documental: </label>
                                        </div>
                                        <div class="col-md-9">
                                            <div class="row">
                                                <div class="form-control-plaintext">
                                                    <span class="text-success" v-if="estadoCaptura">
                                                        <span class="badge rounded-pill bg-success me-2">&nbsp;</span>
                                                        Abierto
                                                    </span>
                                                    <span class="text-danger" v-else>
                                                        <span class="badge rounded-pill bg-danger me-2">&nbsp;</span>
                                                        Cerrado
                                                    </span>
                                                    <button class="btn btn-primary" type="button" @click="onConfirmCambioCaptura()">
                                                        Cambiar
                                                    </button>
                                                </div>

{{--                                                <div class="form-control-plaintext text-danger col-6" v-else>--}}
{{--                                                    <div class="badge rounded-pill bg-danger me-2">&nbsp;</div>--}}
{{--                                                    Cerrado--}}
{{--                                                </div>--}}
                                            </div>

                                        </div>

                                    </div>

                                </form>
                            </div>
                        </div>
                    </div>
                </div>
            </section>

            <div class="card pt-4">
                <div class="card-body">
                    <div class="row mb-2">
                        <div class="col-6">
                            <button class="btn btn-teal" @click="verNuevo()">
                                <i class="bi bi-plus"></i> Nuevo
                            </button>
                            <button class="btn btn-secondary" @click="verDuplicar()">
                                <i class="bi bi-files"></i> Duplicar año
                            </button>
                        </div>
                        {{-- <div class="col-6 text-end">
                            <button class="btn btn-success" @click="exportarTabla()">
                                <i class="bi bi-file-earmark-excel"></i> Exportar
                            </button>
                        </div> --}}
                    </div>
                    <section class="section">
                        <div id="grid"></div>
                    </section>
                </div>
            </div>


            <!--region Modal-->
            <div class="modal" id="wEditar" tabindex="-1" role="dialog" aria-hidden="true">
                <div class="modal-dialog modal-lg" role="document">
                    <div class="modal-content" v-show="wModalShowingId==='wEditar'">
                        <form action="#" method="post" @submit.prevent="guardarCalendario()">
                            <div class="modal-header">
                                <h5 class="modal-title">@{{ this.filaSeleccionada.isNew ? 'Crear nuevo' : 'Editar' }}</h5>
                                <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Cerrar"></button>

                            </div>
                            <div class="modal-body row g-3">

                                <div class="col-sm-4">
                                    <div class="form-floating">
                                        <input type="number" required class="form-control"
                                               v-model="filaSeleccionada.anio_captura"
                                               id="anio_captura" placeholder="Año captura">
                                        <label for="anio_captura">Año captura</label>
                                    </div>
                                </div>

                                <div class="col-sm-4">
                                    <div class="form-floating">
                                        <select required class="form-select" id="mes_captura"
                                                v-model="filaSeleccionada.mes_captura"
                                                aria-label="Mes de captura">
                                            <option value="" disabled selected>Elige</option>
                                            <option value="1">Enero</option>
                                            <option value="2">Febrero</option>
                                            <option value="3">Marzo</option>
                                            <option value="4">Abril</option>
                                            <option value="5">Mayo</option>
                                            <option value="6">Junio</option>
                                            <option value="7">Julio</option>
                                            <option value="8">Agosto</option>
                                            <option value="9">Septiembre</option>
                                            <option value="10">Octubre</option>
                                            <option value="11">Noviembre</option>
                                            <option value="12">Diciembre</option>
                                        </select>
                                        <label for="mes_captura">Mes captura</label>
                                    </div>
                                </div>

                                <div class="col-sm-4">
                                    <div class="form-floating" id="dt1"r>
                                        <input type="datetime-local" required class="form-control"
                                               :value="filaSeleccionada.isNew ? '' : dayjs(filaSeleccionada.fecha_hora_limite_captura).format('YYYY-MM-DDTHH:mm')"
                                               id="fecha_hora_limite_captura" placeholder="Fecha límite">
                                        <label for="fecha_hora_limite_captura">Fecha límite</label>
                                    </div>
                                </div>

                            </div>
                            <div class="modal-footer">
                                <button type="button" class="btn btn-secondary" data-bs-dismiss="modal">Cerrar</button>
                                <button type="submit" class="btn btn-teal">Guardar</button>
                            </div>
                        </form>
                    </div>


                    <div class="modal-content" v-show="wModalShowingId==='wDuplicar'">
                        <form action="#" method="post" @submit.prevent="duplicarCalendario()">
                            <div class="modal-header">
                                <h5 class="modal-title">Duplicar calendario</h5>
                                <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Cerrar"></button>

                            </div>
                            <div class="modal-body row g-3">

                                <div class="col-sm-6">
                                    <div class="form-floating">
                                        <select required class="form-select" id="anio_duplicar"
                                                v-model="anio_old"
                                                aria-label="Año a duplicar">
                                            <option :value="null" selected="" disabled>Selecciona...</option>
                                            <option v-for="anio of anios" :value="anio">@{{ anio }}</option>
                                        </select>
                                        <label for="anio_duplicar">Año a duplicar</label>
                                    </div>
                                </div>
                                <div class="col-sm-6">
                                    <div class="form-floating">
                                        <input type="number" required class="form-control"
                                               v-model="anio_new"
                                               id="anio_nuevo" placeholder="Nuevo año">
                                        <label for="anio_nuevo">Nuevo año</label>
                                    </div>
                                </div>
                            </div>
                            <div class="modal-footer">
                                <button type="button" class="btn btn-secondary" data-bs-dismiss="modal">Cerrar</button>
                                <button type="submit" class="btn btn-teal">Duplicar</button>
                            </div>
                        </form>
                    </div>
                </div>
            </div>
            <!--endregion Modal-->

        {{-- </div> --}}
    </main>

@endsection
