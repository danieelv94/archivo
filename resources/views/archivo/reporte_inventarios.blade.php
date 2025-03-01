@extends('layouts.master', ['title' => 'Reporte de Inventarios'])

@section('styles')

@endsection

@section('scripts')
{{--    <script type="text/javascript" src="{{ config('app.env') != 'production' ? asset('vendor/vue/vue.js') : asset('vendor/vue/vue.min.js') }}"></script>--}}
    @include('layouts.includes.jquery-js-base')

    <script type="text/javascript">
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
                            { name: 'id', type: 'string' },
                            { name: 'seccion', map:'seccion>nombre' , type: 'string' },
                            { name: 'serie', map:'serie>nombre' , type: 'string' },
                            { name: 'mes', map:'mes_captura' ,type: 'string' },
                            { name: 'anio', map:'anio_captura' ,type: 'string' },
                            { name: 'serieClave', map:'serie>clave' ,type: 'string' },
                            { name: 'seccionClave', map:'seccion>clave' ,type: 'string' },
                            { name: 'estado' ,type: 'string' },
                            { name: 'persona_revisa_id' ,type: 'string' },
                            { name: 'fecha_cierre_captura' },
                            { name: 'departamentoClave', map:'departamento>clave' ,type: 'string' },
                            { name: 'departamentoNombre', map:'departamento>nombre' ,type: 'string' },
                            { name: 'expedienteConsecutivo',type: 'bool' },
                            { name: 'total_expedientes',type: 'int' },
                            { name: 'activo', map:'departamento>activo' ,type: 'bool' },


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
                    // autoheight: true,
                    columnsresize: true,
                    localization: jqEsMX,
                    rowsheight: 42,
                    enablebrowserselection: true,
                    columns: [
                        {
                            text: 'Mes',
                            columntype: 'textbox',
                            width: '100',
                            cellsrenderer: (row, columnField, value, defaultHtml, columnProperties, data ) => {
                                defaultHtml = `<div class="d-flex justify-content-left align-items-center h-100">${data.mes+' - '+_MESES_[data.mes]}</div>`;
                                return defaultHtml;
                            }
                        },
                        {
                            text: 'Área generadora',
                            columntype: 'textbox',
                            datafield: 'departamentoNombre',
                            cellsrenderer: (row, columnField, value, defaultHtml, columnProperties, data ) => {
                                let activo = data.activo ? `<span class="badge badge-pill bg-success me-2">Activo</span>` : `<span class="badge badge-pill bg-secondary me-2">Inactivo</span>`;
                                return `<div class="d-flex align-items-center h-100">${data.departamentoClave} - ${data.departamentoNombre}&nbsp;${activo}</div>`;
                            }
                        },
                        {
                            text: 'Sección',
                            columntype: 'textbox',
                            datafield: 'seccion',
                            cellsrenderer: (row, columnField, value, defaultHtml, columnProperties, data ) => {

                                return `<div class="d-flex align-items-center h-100 p-1">${data.seccionClave} - ${data.seccion}</div>`;
                            }
                        },
                        {
                            text: 'Serie',
                            datafield: 'serie',
                            columntype: 'textbox',
                            cellsrenderer: (row, columnField, value, defaultHtml, columnProperties, data ) => {
                                return `<div class="d-flex align-items-center h-100 p-1"> ${data.serieClave} - ${data.serie}</div>`;
                            }
                        },
                        {
                            text: 'Estado captura',
                            datafield: 'estado',
                            columntype: 'textbox',
                            width: '130'
                        },

                        {
                            text: 'Estado revisión',
                            columntype: 'textbox',
                            width: '150',
                            exportable: false,
                            cellsrenderer: (row, columnField, value, defaultHtml, columnProperties, data ) => {
                                defaultHtml = `<div class="d-flex justify-content-center align-items-center h-100">`;
                                if(data.persona_revisa_id){
                                    defaultHtml+= '<h6><div class="badge badge-pill bg-success me-2">Revisado</div></h6>';
                                }else{
                                    defaultHtml+= `<h6><div class="badge badge-pill bg-secondary me-2">Sin Revisar</div></h6>`;
                                }
                                defaultHtml+= `</div>`;
                                return defaultHtml;

                            }
                        },

                        {
                            text: 'Captura',
                            datafield: 'captura',
                            columntype: 'textbox',
                            width: '150',
                            exportable: false,
                            cellsrenderer: (row, columnField, value, defaultHtml, columnProperties, data ) => {
                                defaultHtml = `<div class="d-flex justify-content-center align-items-center h-100">`;
                                if(data.fecha_cierre_captura == null){
                                    defaultHtml+= `<h6><div class="badge badge-pill bg-success me-2">Abierta</div></h6>`;
                                }else{
                                    @can('reporte.openCapture')
                                        defaultHtml+= `<h6><div class="badge badge-pill bg-danger me-2">Cerrada</div></h6>`;
                                    @else
                                        defaultHtml+= `<h6><div class="badge badge-pill bg-danger me-2">Cerrada</div></h6>`;
                                    @endcan
                                }
                                defaultHtml += `</div>`;
                                return defaultHtml;
                            }
                        },
                        {
                            text: 'Expedientes',
                            datafield: 'expedienteConsecutivo',
                            columntype: 'textbox',
                            width: '150',
                            exportable: false,
                            cellsrenderer: (row, columnField, value, defaultHtml, columnProperties, data ) => {
                                defaultHtml = `<div class="d-flex justify-content-center align-items-center h-100">`;
                                if(data.expedienteConsecutivo){
                                    defaultHtml+= `<h6><div class="badge badge-pill bg-success me-2">Consecutivos</div></h6>`;
                                }else{
                                    defaultHtml+= `<h6><div class="badge badge-pill bg-danger me-2">No consecutivos</div></h6>`;
                                }
                                defaultHtml += `</div>`;
                                return defaultHtml;
                            }
                        },
                        {
                            text: 'Cantidad Expedientes',
                            datafield: 'total_expedientes',
                            columntype: 'textbox',
                            width: '100'
                        },
                        {
                            text: '',
                            datafield: 'id',
                            sortable: false,
                            filterable: false,
                            menu: false,
                            exportable: false,
                            width: '230',
                            cellsrenderer: (row, columnField, value, defaultHtml, columnProperties, data ) => {
                                let btn1 = `<button title="Descargar excel" class="btn btn-outline-success border-0 m-1" onclick="app.exportarInventario(${ data.id })"> <i class="bi bi-file-earmark-excel"></i></button>`;
                                let btn2 = `<button title="Descargar Sabana" class="btn btn-outline-success border-0 m-1" onclick="app.exportarInventarioSabana(${ data.id })"> <i class="bi bi-file-earmark-spreadsheet"></i></button>`;
                                let btn3 = ``;
                                let btn4 = ``;
                                @can('reporte.review')
                                    if(!data.persona_revisa_id){

                                        btn3 += `<button title="Marcar revisado" class="btn btn-outline-success border-0 m-1" onclick="app.revisarInventario(${data.id},${data.expedienteConsecutivo})"><i class="bi bi-check2-square"></i></button>`;
                                    }
                                @endcan
                                @can('reporte.openCapture')
                                    if(data.fecha_cierre_captura != null){

                                        btn4 += `<button title="Abrir captura" class="btn btn-outline-success border-0 m-1" onclick="app.abrirCaptura(${ data.id })"><i class="bi bi-unlock"></i></button>`;
                                    }
                                @endcan
                                let btn5 = `<button title="Generar Etiquetas" class="btn btn-outline-success border-0 m-1" onclick="app.generarEtiquetas(${ data.id })"> <i class="bi bi-tags"></i></button>`;
                                defaultHtml = `<div class="d-flex justify-content-center align-items-center h-100" style="">${ btn1 }${ btn2 }${ btn3 }${ btn4 }${ btn5 }</div>`;

                                return defaultHtml;
                            }

                        }
                    ]
                } );
            _GRID_SOURCE = source;
        };

        const defaultReporteInventarios = {
            @can('reporte.changeArea')
                area_id: null,
            @else
                area_id: {{ Js::from(auth()->user()->persona->area->id) }},
            @endcan
            anio: null,
            mes: null,
            seccion: null,
            serie: null,
            @can('reporte.changeArea')
                departamento: null,
            @else
                departamento: {{ Js::from(auth()->user()->persona->departamento->id) }},
            @endcan


        }

        const app = new Vue({
            el: '#main',
            data: {
                areas:              {{ Js::from( $areas ) }},
                departamentos:      [],
                anios_reportes:     {{ Js::from( $anios_reporte ) }},
                reporteInventarios: defaultReporteInventarios,
                meses_captura:      null,
                secciones:          null,
                series:             null,

            },

            created(){},
            beforeMount(){},
            computed: {

            },
            mounted(){
                initGrid();
            },
            methods: {
                exportarTabla(){
                    $("#grid").jqxGrid('exportview', 'xlsx', 'reporte de inventarios');
                },
                obtenerMeses(){
                   $.ajax({
                            headers: {'X-CSRF-TOKEN': $('meta[name="csrf-token"]').attr('content')},
                            url: `{{ url('ccleh/inventario/captura/buscar-meses-disponibles') }}/${ this.reporteInventarios.anio }`,
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
                                if ( e.hasOwnProperty('meses')) {
                                    this.meses_captura = e.meses;
                                }
                            }
                        });
                },

                obtenerSeccionesConRegistros() {
                    if (!this.reporteInventarios.mes || !this.reporteInventarios.area_id || this.reporteInventarios.departamento == null || !this.reporteInventarios.anio) {
                        return;
                    }

                    if( this.reporteInventarios.departamento === false ){

                    }

                    $.ajax({
                        headers: {'X-CSRF-TOKEN': $('meta[name="csrf-token"]').attr('content')},
                        url: `{{ url('ccleh/inventario/reporte/get-secciones-registradas') }}/${this.reporteInventarios.area_id}/${this.reporteInventarios.mes}/${this.reporteInventarios.anio}${this.reporteInventarios.departamento != null && this.reporteInventarios.departamento !== false ? '/' + this.reporteInventarios.departamento : ''}`,
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
                                if (e.hasOwnProperty('secciones')) {
                                    this.secciones = e.secciones;
                                }
                            }
                        });
                },
                obtenerSeriesConRegistro() {
                    if (this.reporteInventarios.area_id != null && !this.reporteInventarios.departamento != null && this.reporteInventarios.mes != null && this.reporteInventarios.anio != null && this.reporteInventarios.seccion != null) {
                        $.ajax({
                            headers: {'X-CSRF-TOKEN': $('meta[name="csrf-token"]').attr('content')},
                            url: `{{ url('ccleh/inventario/reporte/get-series-registradas') }}/${this.reporteInventarios.area_id}/${this.reporteInventarios.mes}/${this.reporteInventarios.anio}/${this.reporteInventarios.seccion}${this.reporteInventarios.departamento != null && this.reporteInventarios.departamento !== false ? '/' + this.reporteInventarios.departamento : ''}`,
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
                                if ( e.hasOwnProperty('series')) {
                                    this.series  = e.series;
                                    _GRID_SOURCE.localdata = this.series;
                                    _GRID.updatebounddata();
                                }
                            }
                        });
                    }else{
                        _GRID_SOURCE.localdata = [];
                        _GRID.updatebounddata();
                    }

                },


                obtenerDepartamentos(areaId){
                    $.ajax({
                        headers: {'X-CSRF-TOKEN': $('meta[name="csrf-token"]').attr('content')},
                        url: `{{ url('ccleh/inventario/reporte/getDepartamentosArea') }}/${areaId}`,
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


                exportarInventario(inventarioId){
                    window.open(`{{ url('ccleh/inventario/reporte/exportar-inventario-doc/') }}/${ inventarioId }`);
                },


                exportarInventarioSabana(inventarioId){
                    window.open(`{{ url('ccleh/inventario/reporte/exportar-sabana-inventario-doc/') }}/${ inventarioId }`);
                },



                revisarInventario(inventarioId,expedienteConsecutivo){
                    cclehConfirm.fire({
                        text: expedienteConsecutivo ? '¿Estas seguro de marcar como revisado el inventario?' : 'Este inventario cuenta con números de expediente no consecutivos, ¿Estas seguro de marcarlo como revisado?'
                    }).then( (r) => {
                        if ( r.isConfirmed ) {
                            $.ajax({
                                headers: {'X-CSRF-TOKEN': $('meta[name="csrf-token"]').attr('content')},
                                url: `{{ url('ccleh/inventario/reporte/revisar-inventario') }}/${inventarioId}`,
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
                                    toastr.success('Inventario marcado como revisado.');
                                    this.obtenerSeriesConRegistro();
                                }
                            });

                        }
                    });
                },

                abrirCaptura(inventarioId){
                    cclehConfirm.fire({
                        title: '¿Estas seguro de abrir la captura del inventario?',
                        text: 'Ten en cuenta que si la fecha de captura se ha excedido el usuario no podrá capturar a menos que se active la captura extemporánea del mes correspondiente.'

                    }).then( (r) => {
                        if ( r.isConfirmed ) {
                            $.ajax({
                                headers: {'X-CSRF-TOKEN': $('meta[name="csrf-token"]').attr('content')},
                                url: `{{ url('ccleh/inventario/reporte/abrir-inventario') }}/${inventarioId}`,
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
                                    toastr.success('Inventario Abierto.');
                                    this.obtenerSeriesConRegistro();
                                }
                            });

                        }
                    });
                },
                generarEtiquetas(inventarioId){
                    window.open(`{{ url('ccleh/inventario/reporte/generar-etiquetas') }}/${inventarioId}`);
                },

            }
        })
    </script>

@endsection


@section('content')
    <main id="main" class="main" v-cloak>
        <div {{-- class="col-md-8 offset-md-2" --}}>

            <div class="pagetitle" >
                <h1>Reporte de inventarios</h1>
            </div>

            <div class="card">
                <div class="card-body">

                    @can('reporte.changeArea')
                        <div class="row pt-3">
                            <div class="col-sm-12 col-md-6">
                                <div class="form-floating">
                                        <select class="form-select" id="unidadAdministrativa"
                                                v-model="reporteInventarios.area_id"
                                                @change="obtenerSeccionesConRegistros();obtenerSeriesConRegistro();obtenerDepartamentos(reporteInventarios.area_id);"
                                                aria-label="Unidad Administrativa">
                                            <option :value="null" selected="" disabled>Selecciona...</option>
                                            <option v-for="ua of areas" v-if="ua.activo" :value="ua.id">@{{ ua.codigo }} @{{ ua.nombre }} - <span>Activo</span></option>
                                            <option v-else :value="ua.id">@{{ ua.codigo }} @{{ ua.nombre }} - <span>Inactivo</span></option>
                                        </select>
                                        <label for="unidadAdministrativa">Unidad Administrativa</label>
                                </div>
                            </div>
                            <div class="col-sm-12 col-md-6">
                                <div class="form-floating">
                                        <select class="form-select" id="areaGeneradora"
                                                v-model="reporteInventarios.departamento"
                                                @change="obtenerSeccionesConRegistros();obtenerSeriesConRegistro();"
                                                aria-label="Área Generadora">
                                            <option :value="null" selected="" disabled>Selecciona...</option>
                                            <option v-if="reporteInventarios.area_id" :value="false">Todos</option>
                                            <option v-for="ag of departamentos" v-if="ag.activo" :value="ag.id">@{{ ag.clave }} @{{ ag.nombre }} - <span>Activo</span></option>
                                            <option v-else :value="ag.id">@{{ ag.clave }} @{{ ag.nombre }} - <span>Inactivo</span></option>
                                        </select>
                                        <label for="areaGeneradora">Área Generadora</label>
                                </div>
                            </div>
                        </div>
                    @else
                        {{-- <div class="row pt-3">
                            <div class="col-sm-12">
                                <div class="form-floating">
                                        <select disabled class="form-select" id="unidadAdministrativa"
                                                v-model="reporteInventarios.area_id"
                                                aria-label="Unidad Administrativa">
                                            <option :value="{{ auth()->user()->persona->area->id }}" selected>{{ auth()->user()->persona->area->nombre }}</option>
                                        </select>
                                        <label for="unidadAdministrativa">Unidad Administrativa</label>
                                </div>
                            </div>
                        </div> --}}
                    @endcan

                    <div class="row pt-3">
                        <div class="col-sm-3">
                            <div class="form-floating">
                                    <select class="form-select" id="anio"
                                            v-model="reporteInventarios.anio"
                                            @change="obtenerMeses($event);obtenerSeriesConRegistro();obtenerSeccionesConRegistros();"
                                            aria-label="Año">
                                        <option :value="null" selected="" disabled>Selecciona...</option>
                                        {{-- <option value="todos" selected="">Todos</option> --}}
                                        <option v-for="anios of anios_reportes" :value="anios">@{{ anios }}</option>
                                    </select>
                                    <label for="anio">Año</label>
                            </div>
                        </div>

                        <div class="col-sm-3">
                            <div class="form-floating">
                                    <select class="form-select" id="mes"
                                            v-model="reporteInventarios.mes"
                                            @change="obtenerSeccionesConRegistros();obtenerSeriesConRegistro();"
                                            aria-label="Mes">
                                        <option :value="null" selected="" disabled>Selecciona...</option>
                                        <option v-if="reporteInventarios.anio" value="todos">Todos</option>
                                        <option v-for="mes of meses_captura" :value="mes.numero">
                                            @{{ mes.nombre }}
                                        </option>
                                    </select>
                                    <label for="mes">Mes</label>
                            </div>
                        </div>

                        <div class="col-sm-6">
                            <div class="form-floating">
                                <select id="seccion" class="form-select"
                                        @change="obtenerSeriesConRegistro();"
                                        v-model="reporteInventarios.seccion"
                                        >
                                    <option v-if="!secciones || secciones.length != 0" selected :value="null" disabled>Selecciona...</option>
                                    <option v-if="secciones != null && secciones.length == 0" selected :value="null" disabled>Sin registros</option>
                                    <option v-if="secciones && secciones.length != 0" value="todos">Todos</option>
                                    <option v-for="item of secciones" :value="item.id">
                                        @{{ item.clave_seccion }} @{{ item.nombre }}
                                    </option>
                                </select>
                                <label for="seccion">Sección</label>
                            </div>
                        </div>

                    </div>
                </div>
            </div>

            <div class="card pt-4">
                <div class="card-body">
                    <div class="row mb-2">
                        <div class="col-12 text-end">
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

        </div>
    </main>

@endsection
