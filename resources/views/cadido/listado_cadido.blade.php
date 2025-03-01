@extends('layouts.master', ['title' => 'CADIDO'])

@section('styles')

@endsection

@section('scripts')
{{--    <script type="text/javascript" src="{{ config('app.env') != 'production' ? asset('vendor/vue/vue.js') : asset('vendor/vue/vue.min.js') }}"></script>--}}
    @include('layouts.includes.jquery-js-base')

    <script type="text/javascript">
        let _GRID = null;
        let _GRID_SOURCE = null;
        const initGrid = (data) => {
            const source =
                {
                    localdata: data,
                    datatype: "array",
                    datafields:
                        [
                            { name: 'id', type: 'int' },
                            { name: 'seccion', map:'seccion>nombre' , type: 'string' },
                            { name: 'serie', map:'serie>nombre' , type: 'string' },
                            { name: 'area', map:'area>nombre' ,type: 'string' },
                            { name: 'serieClave', map:'serie>clave' ,type: 'string' },
                            { name: 'seccionClave', map:'seccion>clave' ,type: 'string' },
                            { name: 'archivoTramite', map:'tiempo_guarda_tramite' ,type: 'int' },
                            { name: 'archivoConcentracion', map:'tiempo_guarda_concentracion' ,type: 'int' },
                            { name: 'anioTransferenciaPrimaria', map:'anio_transferencia_primaria' ,type: 'int' },
                            { name: 'anioTransferenciaSecundaria', map:'anio_transferencia_secundaria' ,type: 'int' },
                            { name: 'anio', map:'anio' ,type: 'int' },
                            { name: 'anioActual', map:'anio_actual' ,type: 'int' },
                            { name: 'completo', map:'completo' ,type: 'boolean' },

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
                            text: 'Código',
                            columntype: 'textbox',
                            datafield: 'codigo',
                            width: '50',
                            cellsrenderer: (row, columnField, value, defaultHtml, columnProperties, data ) => {
                                return `<div class="d-flex align-items-center h-100">${data.seccionClave}.${data.serieClave}</div>`;
                            }
                        },
                        {
                            text: 'Serie',
                            datafield: 'serie',
                            columntype: 'textbox',
                            width: '360',
                            cellsrenderer: (row, columnField, value, defaultHtml, columnProperties, data ) => {
                                return `<div class="d-flex align-items-center h-100">${data.serie}</div>`;
                            }
                        },
                        {
                            text: 'Tiempo de guarda',
                            datafield: 'tiempo_guarda',
                            columntype: 'textbox',
                            width: '120',
                            cellsrenderer: (row, columnField, value, defaultHtml, columnProperties, data ) => {
                                return `<div class="d-flex align-items-center justify-content-center h-100">${data.archivoTramite+data.archivoConcentracion}</div>`;
                            }
                        },
                        {
                            text: 'Archivo de tramite',
                            datafield: 'archivo_tramite',
                            columntype: 'textbox',
                            cellsrenderer: (row, columnField, value, defaultHtml, columnProperties, data ) => {
                                return `<div class="d-flex align-items-center justify-content-center h-100">${data.archivoTramite}</div>`;
                            }
                        },
                        {
                            text: 'Archivo de concentración',
                            datafield: 'archivo_concentracion',
                            columntype: 'textbox',
                            cellsrenderer: (row, columnField, value, defaultHtml, columnProperties, data ) => {
                                return `<div class="d-flex align-items-center justify-content-center h-100">${data.archivoConcentracion}</div>`;
                            }
                        },
                        {
                            text: 'Año transferencia primaria',
                            datafield: 'transferencia_primaria',
                            columntype: 'textbox',
                            cellsrenderer: (row, columnField, value, defaultHtml, columnProperties, data ) => {
                                let clase = '';
                                if( data.completo  ){
                                    clase = 'bg-primary';
                                }else if( data.anioTransferenciaPrimaria === data.anioActual || data.anioTransferenciaPrimaria < data.anioActual ){//si la tranferencia primaria es el mismo año del cadido
                                    clase = 'bg-danger';
                                }else if( data.anioTransferenciaPrimaria === (data.anioActual+1) ){//si la transferencia primaria es el año siguiente del cadido
                                    clase = 'bg-warning';
                                }else if( data.anioTransferenciaPrimaria > (data.anioActual+1) ){//si la transferencia primaria es mayor al año del cadido
                                    clase = 'bg-success';
                                }
                                return `<div class="d-flex align-items-center justify-content-center h-100 ${clase}">${data.anioTransferenciaPrimaria}</div>`;
                            }
                        },
                        {
                            text: 'Año valoración secundaria',
                            datafield: 'valoracion_secundaria',
                            columntype: 'textbox',
                            cellsrenderer: (row, columnField, value, defaultHtml, columnProperties, data ) => {
                                let clase = '';
                                if( data.anioTransferenciaSecundaria === data.anioActual || data.anioTransferenciaSecundaria < data.anioActual){
                                    clase = 'bg-danger';
                                }else if( data.anioTransferenciaSecundaria === (data.anioActual+1) ){
                                    clase = 'bg-warning';
                                }else if( data.anioTransferenciaSecundaria > (data.anioActual+1) ){
                                    clase = 'bg-success';
                                }
                                return `<div class="d-flex align-items-center justify-content-center h-100 ${clase}">${data.anioTransferenciaSecundaria}</div>`;
                            }
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
                            width: '120',
                            cellsrenderer: (row, columnField, value, defaultHtml, columnProperties, data ) => {
                                let btn = `<button title="Descargar" class="btn m-1 btn-sm btn-outline-success border-0" onclick="app.descargarCadido(${ data.id })"> <i class="bi bi-file-earmark-excel"></i></button>`;
                                defaultHtml = `<div class="d-flex justify-content-center align-items-center h-100" style="">${ btn }</div>`;

                                return defaultHtml;

                            }
                        }
                    ]
                } );
            _GRID_SOURCE = source;
        };

        const CADIDO = {
            id: null,
            anio: null,

            seccion_id: null,
            serie_id: null,

            valor_primario_administrativa: null,
            valor_primario_fiscal: null,
            valor_primario_legal: null,

            valor_secundario_informativo: false,
            valor_secundario_evidencial: false,
            valor_secundario_testimonial: false,

            tiempo_guarda_tramite: null,
            tiempo_guarda_concentracion: null,

            fundamento_legal: '',

            clasificacion_publica: false,
            clasificacion_reservada: false,
            clasificacion_confidencial: false,

            destino_final: null,

            particularidades: '',
        };

        const app = new Vue({
            el: '#main',
            data: {
                area: {{ auth()->user()->persona->area->id }},
                departamento: {{ auth()->user()->persona->departamento->id }},
                anios: {{ Js::from( $anios ) }},
                secciones : {},
                series: {},
                cadidos: {},
                cadido: JSON.parse(JSON.stringify(CADIDO)),
                filtros: {
                    anio: null,
                    seccion: null,
                },
            },
            created(){},
            beforeMount(){},
            computed: {

            },
            mounted(){
                this.obtenerSecciones();
                initGrid();
            },
            methods: {
                exportarTabla() {
                    $("#grid").jqxGrid('exportview', 'xlsx', 'series autorizadas');
                },
                obtenerSecciones() {
                    let repetidas = false;
                    $.ajax({
                        headers: {'X-CSRF-TOKEN': $('meta[name="csrf-token"]').attr('content')},
                        url: `{{ url('ccleh/inventario/captura/get-series-autorizadas') }}/${this.area}/${this.departamento}/${repetidas}`,
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
                            if (e && e.hasOwnProperty('success') && e.success) {
                                if (e.hasOwnProperty('series')) {
                                    this.secciones = e.series;
                                }
                            }
                        });
                },

                obtenerListaCadido() {
                    if (!this.filtros.seccion || !this.filtros.anio) {
                        this.cadidos = {};
                        return;
                    }
                    $.ajax({
                        url: `{{ route('ccleh.cadido.listar.cadidos') }}`,
                        type: 'get',
                        data: this.filtros,
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
                                    }
                                } finally {
                                }
                            }
                        }
                    })
                        .done(e => {
                            if (e && e.hasOwnProperty('success') && e.success) {
                                if (e.hasOwnProperty('cadidos')) {
                                    this.cadidos = e.cadidos;
                                    _GRID_SOURCE.localdata = this.cadidos;
                                    _GRID.updatebounddata();
                                }
                            }
                        });
                },

                descargarCadido(id) {
                    window.open(`{{ url('ccleh/cadido/exportar-cadido/') }}/${ id }`);
                },

                descargarCadidoMultiple($porSeccion = false) {
                    window.open(`{{ url('ccleh/cadido/exportar-cadido-multiple/') }}/${ this.filtros.anio }/${ $porSeccion ? this.filtros.seccion : '' }`);
                },

                buscarCadidoPorId(id) {
                    return Object.values(this.cadidos).find(cadido => cadido.id == id);
                },

            }


        })
    </script>

@endsection


@section('content')
    <main id="main" class="main" v-cloak>
        {{-- <div class="col-md-10 offset-md-1 col-lg-8 offset-lg-2">     --}}

            <div class="pagetitle">
                <h1>Catálogo de Disposición Documental</h1>
            </div>

            <div class="card">
                <div class="card-body">
                    <div class="row pt-3">
                        <div class="col-sm-4 col-md-2">
                            <div class="form-floating">
                                <select class="form-select" id="anio"
                                        v-model="filtros.anio"
                                        @change="obtenerListaCadido()"
                                        aria-label="Año">
                                    <option :value="null" selected="" disabled>Selecciona...</option>
                                    <option v-for="anios of anios" :value="anios">@{{ anios }}</option>
                                </select>
                                <label for="anio">Año</label>
                            </div>
                        </div>
                        <div class="col-sm-8 col-md-10">
                            <div class="form-floating">
                                <select class="form-select" id="seccion"
                                        v-model="filtros.seccion"
                                        aria-label="Sección"
                                        @change="obtenerListaCadido()">
                                    <option :value="null" selected="" disabled>Selecciona...</option>
                                    <option v-for="seccion of secciones" :value="seccion.seccion.id">@{{ seccion.seccion.clave }} - @{{ seccion.seccion.nombre }}</option>
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
                        <div v-if="filtros.anio && filtros.seccion" class="col-12 text-end">
                            <button v-if="filtros.anio && filtros.seccion && cadidos.length > 1"
                                    class="btn btn-secondary" @click="descargarCadidoMultiple(true)">
                                <i class="bi bi-files"></i> DESCARGAR SECCIÓN
                            </button>
                        </div>
                    </div>
                    <section class="section">
                        <div id="grid"></div>
                    </section>
                </div>
            </div>

    </main>

@endsection
