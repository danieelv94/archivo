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
                                let btn1 = `<button title="Eliminar" class="btn btn-sm btn-outline-danger border-0" onclick="app.eliminarCadido(${ data.id })"> <i class="bi-trash"></i></button>`;
                                let btn2 = `<button title="Editar" class="btn m-1 btn-sm btn-outline-primary border-0" onclick="app.verEditarCadido(${ data.id })"> <i class="bi-pencil"></i></button>`;
                                let btn3 = `<button title="Descargar" class="btn m-1 btn-sm btn-outline-success border-0" onclick="app.descargarCadido(${ data.id })"> <i class="bi bi-file-earmark-excel"></i></button>`;
                                defaultHtml = `<div class="d-flex justify-content-center align-items-center h-100" style="">${ btn3 } ${ btn2 } ${ btn1 }</div>`;

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

            candado: null,
        };

        const FIRMAS = {
            id: null,
            departamento_id: null,
            tipo_documento: null,
            elaboro: [
                {
                    nombre: '',
                    cargo: '',
                }
            ],
            valido: [
                {
                    nombre: '',
                    cargo: '',
                }
            ],
            recibio: [
                {
                    nombre: '',
                    cargo: '',
                }
            ],
            autorizo: [
                {
                    nombre: '',
                    cargo: '',
                }
            ],
            reviso: [
                {
                    nombre: '',
                    cargo: '',
                },
            ],
        };

        const app = new Vue({
            el: '#main',
            data: {
                anios: {{ Js::from( $anios ) }},
                secciones : {},
                series: {},
                cadidos: {},
                cadido: JSON.parse(JSON.stringify(CADIDO)),
                filtros: {
                    anio: null,
                    seccion: null,
                },
                firmas: @if($firmas) {{ Js::from( $firmas ) }} @else JSON.parse(JSON.stringify(FIRMAS)) @endif,
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
                obtenerSecciones() {

                    $.ajax({
                        headers: {'X-CSRF-TOKEN': $('meta[name="csrf-token"]').attr('content')},
                        url: `{{ url('ccleh/admin/series-autorizadas/get-secciones') }}`,
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
                                if (e.hasOwnProperty('secciones')) {
                                    this.secciones = e.secciones;
                                }
                            }
                        });
                },

                obtenerSeries(event) {
                    this.filtros.serie = null;

                    $.ajax({
                        headers: {'X-CSRF-TOKEN': $('meta[name="csrf-token"]').attr('content')},
                        url: `{{ url('ccleh/admin/series-autorizadas/get-series') }}/${event.target.value}`,
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
                                    this.series = e.series;
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

                verEditarCadido(id = null) {
                    if (id) {
                        let cadido = this.buscarCadidoPorId(id);
                        this.cadido = cadido;
                    } else {
                        this.cadido = JSON.parse(JSON.stringify(CADIDO));
                    }
                    this.cadido.seccion_id = this.filtros.seccion;
                    this.cadido.anio = this.filtros.anio;
                    this.wModalShowing = new bootstrap.Modal(document.getElementById('wEditarCadido'));
                    this.wModalShowing.show();
                },

                guardarCadido() {
                    $.ajax({
                        headers: {'X-CSRF-TOKEN': $('meta[name="csrf-token"]').attr('content')},
                        url: `{{ route('ccleh.cadido.guardar.cadido') }}`,
                        type: 'post',
                        data: this.cadido,
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

                                this.wModalShowing.hide();
                                this.wModalShowing = null;

                                this.cadido = JSON.parse(JSON.stringify(CADIDO));
                                this.obtenerListaCadido();
                            }
                        });
                },

                eliminarCadido(id) {
                    cclehConfirm.fire({
                        text: '¿Estas seguro de eliminar?'
                    }).then( (r) => {
                        if (r.isConfirmed) {
                            $.ajax({
                                headers: {'X-CSRF-TOKEN': $('meta[name="csrf-token"]').attr('content')},
                                url: `{{ url('ccleh/cadido/eliminar-cadido') }}/${id}`,
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
                                            }
                                        } finally {
                                        }
                                    }
                                }
                            })
                                .done(e => {
                                    if (e && e.hasOwnProperty('success') && e.success) {
                                        toastr.success('Eliminado');
                                        this.obtenerListaCadido();
                                    }
                                });

                        }
                    });
                },

                descargarCadido(id) {
                    window.open(`{{ url('ccleh/cadido/exportar-cadido/') }}/${ id }`);
                },

                descargarCadidoMultiple($porSeccion = false) {
                    window.open(`{{ url('ccleh/cadido/exportar-cadido-multiple/') }}/${ this.filtros.anio }`);
                },

                buscarCadidoPorId(id) {
                    return Object.values(this.cadidos).find(cadido => cadido.id == id);
                },

                verImportar(){
                    this.wModalShowing = new bootstrap.Modal(document.getElementById('wImportar'));
                    this.wModalShowing.show();
                    document.getElementById('importarCADIDOFrm').reset();
                },

                importarCadido(){
                    const data = new FormData(document.getElementById('importarCADIDOFrm'));
                    data.append('anio', this.filtros.anio);
                    //data.append('seccion_id', this.filtros.seccion);


                    $.ajax({
                        headers: {'X-CSRF-TOKEN': $('meta[name="csrf-token"]').attr('content')},
                        url: `{{ route('ccleh.cadido.importar.cadido') }}`,
                        type: 'post',
                        data: data,
                        processData: false,
                        contentType: false,
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
                            if ( e && e.hasOwnProperty('success') && e.success) {
                                toastr.success(e.message);
                                this.obtenerListaCadido();
                                this.wModalShowing.hide();
                                document.getElementById('importarCADIDOFrm').reset();
                            }
                        });
                },

                verEditarFirmas(){
                    this.wModalShowing = new bootstrap.Modal(document.getElementById('wFirmas'));
                    this.wModalShowing.show();
                },

                guardarFirmas() {
                    $.ajax({
                        headers: {'X-CSRF-TOKEN': $('meta[name="csrf-token"]').attr('content')},
                        url: `{{ url('ccleh/firmas/guardar-cadido') }}`,
                        type: 'post',
                        data: this.firmas,
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
                            if ( e && e.hasOwnProperty('success') && e.success) {
                                this.wModalShowing.hide();
                                this.wModalShowing = null;
                                this.firmas = e.firma;
                                toastr.success('Firmas guardadas.');
                            }
                        });
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

                        <div class="col-sm-12 col-md-10">
                            <div class="form-floating">
                                <select class="form-select" id="seccion"
                                        v-model="filtros.seccion"
                                        aria-label="Sección"
                                        @change="obtenerSeries($event);obtenerListaCadido()">
                                    <option :value="null" selected="" disabled>Selecciona...</option>
                                    <option v-for="seccion of secciones" :value="seccion.id">@{{ seccion.clave }} - @{{ seccion.nombre }}</option>
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
                            <button class="btn btn-secondary" @click="verEditarFirmas()">
                                <i class="bi bi-pencil-square"></i> FIRMAS
                            </button>
                            <button v-if="filtros.anio"
                                    class="btn btn-primary" @click="descargarCadidoMultiple()">
                                <i class="bi bi-files"></i> DESCARGAR AÑO
                            </button>
                            <button v-if="filtros.anio" class="btn btn-success" @click="verImportar()">
                                <i class="bi bi-file-earmark-excel"></i> IMPORTAR
                            </button>
                            <button v-if="filtros.anio && filtros.seccion" class="btn btn-teal" @click="verEditarCadido()">
                                <i class="bi bi-plus"></i> REGISTRAR CADIDO
                            </button>
                        </div>
                    </div>
                    <section class="section">
                        <div id="grid"></div>
                    </section>
                </div>
            </div>


        <!--region Modal-->
        <div class="modal fade" id="wEditarCadido" tabindex="-1" role="dialog" aria-hidden="true">
            <div class="modal-dialog modal-lg" role="document">
                <div class="modal-content">
                    <form action="#" method="post" @submit.prevent="guardarCadido()">
                        <div class="modal-header">
                            <h5 class="modal-title">@{{ this.cadido.id ? 'Editar CADIDO' : 'Registrar CADIDO' }}</h5>
                            <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Cerrar"></button>

                        </div>
                        <div class="modal-body">

                            <div class="row g-3">
                                <div class="col-sm-2">
                                    <div class="form-floating">
                                        <input type="number" class="form-control"
                                               :value="filtros.anio"
                                               max="9999"
                                               min="1111"
                                               disabled
                                               id="cadido_anio" placeholder="cadido_anio">

                                        <label for="cadido_anio">Año</label>
                                    </div>
                                </div>

                                <div class="col-sm-5">
                                    <div class="form-floating">
                                        <select class="form-select" id="cadido_seccion" name="cadido_seccion"
                                                :value="filtros.seccion"
                                                aria-label="Sección"
                                                disabled>
                                            <option :value="null" selected="" disabled>Selecciona...</option>
                                            <option v-for="seccion of secciones" :value="seccion.id">@{{ seccion.clave }} - @{{ seccion.nombre }}</option>
                                        </select>
                                        <label for="cadido_seccion">Sección</label>
                                    </div>
                                </div>

                                <div class="col-sm-5">
                                    <div class="form-floating">
                                        <select class="form-select" id="cadido_serie" name="cadido_serie"
                                                v-model="cadido.serie_id"
                                                aria-label="Serie"
                                                required>
                                            <option :value="null" selected="" disabled>Elige</option>
                                            <option v-for="serie of series" :value="serie.id">@{{ serie.clave }} - @{{ serie.nombre }}</option>
                                        </select>
                                        <label for="cadido_serie">Serie</label>
                                    </div>
                                </div>

                                <h6>Valor Documental Primario</h6>
                                <div class="col-sm-4">
                                    <div class="form-floating">
                                        <input type="number" class="form-control"
                                               v-model="cadido.valor_primario_administrativa"
                                               max="99"
                                               min="0"
                                               id="cadido_valor_primario_administrativa" placeholder="cadido_valor_primario_administrativa">

                                        <label for="cadido_valor_primario_administrativa">Administrativa</label>
                                    </div>
                                </div>

                                <div class="col-sm-4">
                                    <div class="form-floating">
                                        <input type="number" class="form-control"
                                               v-model="cadido.valor_primario_fiscal"
                                               max="99"
                                               min="0"
                                               id="cadido_valor_primario_fiscal" placeholder="cadido_valor_primario_fiscal">

                                        <label for="cadido_valor_primario_fiscal">Fiscal</label>
                                    </div>
                                </div>

                                <div class="col-sm-4">
                                    <div class="form-floating">
                                        <input type="number" class="form-control"
                                               v-model="cadido.valor_primario_legal"
                                               max="99"
                                               min="0"
                                               id="cadido_valor_primario_legal" placeholder="cadido_valor_primario_legal">

                                        <label for="cadido_valor_primario_legal">Legal</label>
                                    </div>
                                </div>

                                <h6>Valor Documental Secundario</h6>

                                <div class="col-sm-4">
                                    <div class="form-check">
                                        <input class="form-check-input"
                                               type="checkbox"
                                               id="cadido_valor_secundario_evidencial"
                                               name="cadido_valor_secundario_evidencial"
                                               v-model="cadido.valor_secundario_evidencial">
                                        <label class="form-check-label" for="cadido_valor_secundario_evidencial">
                                            Evidencial
                                        </label>
                                    </div>
                                </div>

                                <div class="col-sm-4">
                                    <div class="form-check">
                                        <input class="form-check-input"
                                               type="checkbox"
                                               id="cadido_valor_secundario_testimonial"
                                               name="cadido_valor_secundario_testimonial"
                                               v-model="cadido.valor_secundario_testimonial">
                                        <label class="form-check-label" for="cadido_valor_secundario_testimonial">
                                            Testimonial
                                        </label>
                                    </div>
                                </div>

                                <div class="col-sm-4">
                                    <div class="form-check">
                                        <input class="form-check-input"
                                               type="checkbox"
                                               id="cadido_valor_secundario_informativo"
                                               name="cadido_valor_secundario_informativo"
                                               v-model="cadido.valor_secundario_informativo">
                                        <label class="form-check-label" for="cadido_valor_secundario_informativo">
                                            Informativo
                                        </label>
                                    </div>
                                </div>

                                <h6>Tiempo de Guarda</h6>
                                <div class="col-sm-4">
                                    <div class="form-floating">
                                        <input type="number" class="form-control"
                                               v-model="cadido.tiempo_guarda_tramite"
                                               max="99"
                                               min="1"
                                               required
                                               id="cadido_tiempo_guarda_tramite" placeholder="cadido_tiempo_guarda_tramite">

                                        <label for="cadido_tiempo_guarda_tramite">Trámite</label>
                                    </div>
                                </div>

                                <div class="col-sm-4">
                                    <div class="form-floating">
                                        <input type="number" class="form-control"
                                               v-model="cadido.tiempo_guarda_concentracion"
                                               max="99"
                                               min="1"
                                               required
                                               id="cadido_tiempo_guarda_concentracion" placeholder="cadido_tiempo_guarda_concentracion">

                                        <label for="cadido_tiempo_guarda_concentracion">Concentración</label>
                                    </div>
                                </div>

                                <div class="col-sm-4">
                                    <div class="form-floating">
                                        <input type="number" class="form-control"
{{--                                               v-model="cadido.tiempo_guarda_concentracion"--}}
                                               :value="(parseInt(cadido.tiempo_guarda_tramite ?? 0) + parseInt(cadido.tiempo_guarda_concentracion ?? 0))"
                                               max="99"
                                               min="0"
                                               disabled
                                               id="cadido_tiempo_guarda_total" placeholder="cadido_tiempo_guarda_total">

                                        <label for="cadido_tiempo_guarda_total">Total</label>
                                    </div>
                                </div>

                                <div class="col-12 mt-4">
                                    <div class="form-floating">
                                        <textarea class="form-control"
                                                  v-model="cadido.fundamento_legal"
                                                  id="cadido_fundamento_legal" placeholder="cadido_fundamento_legal"
                                                  required>
                                        </textarea>

                                        <label for="cadido_fundamento_legal">Fundamento Legal</label>
                                    </div>
                                </div>

                                <h6>Clasificación de la Información</h6>
                                <div class="col-sm-4">
                                    <div class="form-check">
                                        <input class="form-check-input"
                                               type="checkbox"
                                               id="cadido_clasificacion_publica"
                                               name="cadido_clasificacion_publica"
                                               v-model="cadido.clasificacion_publica">
                                        <label class="form-check-label" for="cadido_clasificacion_publica">
                                            Pública
                                        </label>
                                    </div>
                                </div>

                                <div class="col-sm-4">
                                    <div class="form-check">
                                        <input class="form-check-input"
                                               type="checkbox"
                                               id="cadido_clasificacion_reservada"
                                               name="cadido_clasificacion_reservada"
                                               v-model="cadido.clasificacion_reservada">
                                        <label class="form-check-label" for="cadido_clasificacion_reservada">
                                            Reservada
                                        </label>
                                    </div>
                                </div>

                                <div class="col-sm-4">
                                    <div class="form-check">
                                        <input class="form-check-input"
                                               type="checkbox"
                                               id="cadido_clasificacion_confidencial"
                                               name="cadido_clasificacion_confidencial"
                                               v-model="cadido.clasificacion_confidencial">
                                        <label class="form-check-label" for="cadido_clasificacion_confidencial">
                                            Confidencial
                                        </label>
                                    </div>
                                </div>

                                <h6>Destino Final</h6>
                                <div class="col-sm-4">
                                    <div class="form-check">
                                        <input class="form-check-input"
                                               type="radio"
                                               id="cadido_destino_final_baja"
                                               name="cadido_destino_final"
                                               value="B"
                                               v-model="cadido.destino_final">
                                        <label class="form-check-label" for="cadido_destino_final_baja">
                                            Baja
                                        </label>
                                    </div>
                                </div>
                                <div class="col-sm-4">
                                    <div class="form-check">
                                        <input class="form-check-input"
                                               type="radio"
                                               id="cadido_destino_final_archivo_historico"
                                               name="cadido_destino_final"
                                               value="AH"
                                               v-model="cadido.destino_final">
                                        <label class="form-check-label" for="cadido_destino_final_archivo_historico">
                                            Archivo Histórico
                                        </label>
                                    </div>
                                </div>
                                <div class="col-sm-4">
                                    <div class="form-check">
                                        <input class="form-check-input"
                                               type="radio"
                                               id="cadido_destino_final_muestreo"
                                               name="cadido_destino_final"
                                               value="M"
                                               v-model="cadido.destino_final">
                                        <label class="form-check-label" for="cadido_destino_final_muestreo">
                                            Muestreo
                                        </label>
                                    </div>
                                </div>

                                <div class="col-12 mt-4">
                                    <div class="form-floating">
                                        <textarea class="form-control"
                                                  v-model="cadido.particularidades"
                                                  id="cadido_particularidades" placeholder="cadido_particularidades">
                                        </textarea>

                                        <label for="cadido_particularidades">Particularidades</label>
                                    </div>
                                </div>

                                <div class="col-12 mt-4">
                                    <div class="form-floating">
                                        <select required class="form-select" id="cadido_candado"
                                                v-model="cadido.candado">
                                            <option selected :value="false">No</option>
                                            <option :value="true">Si</option>
                                        </select>

                                        <label for="cadido_candado">Candado(Transferencias parciales)</label>
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

        <div class="modal fade" id="wImportar" tabindex="-1" role="dialog" aria-hidden="true">
            <div class="modal-dialog modal-lg" role="document">
                <div class="modal-content">
                    <form id="importarCADIDOFrm" action="#" method="post" @submit.prevent="importarCadido()">
                        <div class="modal-header">
                            <h5 class="modal-title">Importar CADIDO</h5>
                            <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Cerrar"></button>
                        </div>
                        <div class="modal-body">
                            <div class="row">
                                <input required id="importarCADIDO" name="importarCADIDO" type="file" accept=".xls,.xlsx, application/vnd.openxmlformats-officedocument.spreadsheetml.sheet,application/vnd.ms-excel">
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

        <div class="modal fade" id="wFirmas" tabindex="-1" role="dialog" aria-hidden="true">
            <div class="modal-dialog modal-lg" role="document">
                <div class="modal-content">
                    <form id="firmasFrm" action="#" method="post" @submit.prevent="guardarFirmas()">
                        <div class="modal-header">
                            <h5 class="modal-title">Firmas Inventario Documental</h5>
                            <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Cerrar"></button>

                        </div>
                        <div class="modal-body">
                            <div class="row">
                                <p class="mt-3 mb-0">Realizó</p>
                                <div class="col-12 col-md-6">
                                    <input v-model="firmas.elaboro[0].nombre" class="form-control" type="text" id="firmas_elaboro_nombre" placeholder="Nombre">
                                </div>
                                <div class="col-12 col-md-6">
                                    <input v-model="firmas.elaboro[0].cargo" class="form-control" type="text" id="firmas_elaboro_cargo" placeholder="Cargo" :required="firmas.elaboro[0].nombre != null && firmas.elaboro[0].nombre.trim() != ''">
                                </div>
                                <p class="mt-3 mb-0">Autorizó</p>
                                <div class="col-12 col-md-6">
                                    <input v-model="firmas.autorizo[0].nombre" class="form-control" type="text" id="firmas_autorizo_nombre" placeholder="Nombre">
                                </div>
                                <div class="col-12 col-md-6">
                                    <input v-model="firmas.autorizo[0].cargo" class="form-control" type="text" id="firmas_autorizo_cargo" placeholder="Cargo" :required="firmas.autorizo[0].nombre != null && firmas.autorizo[0].nombre.trim() != ''">
                                </div>
                                <p class="mt-3 mb-0">Validó</p>
                                <div class="col-12 col-md-6">
                                    <input v-model="firmas.valido[0].nombre" class="form-control" type="text" id="firmas_valido_nombre" placeholder="Nombre">
                                </div>
                                <div class="col-12 col-md-6">
                                    <input v-model="firmas.valido[0].cargo" class="form-control" type="text" id="firmas_valido_cargo" placeholder="Cargo" :required="firmas.valido[0].nombre != null && firmas.valido[0].nombre.trim() != ''">
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

    </main>

@endsection
