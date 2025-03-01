@extends('layouts.master', ['title' => 'Series Autorizadas'])

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
                            { name: 'id', type: 'string' },
                            { name: 'seccion', map:'seccion>nombre' , type: 'string' },
                            { name: 'serie', map:'serie>nombre' , type: 'string' },
                            { name: 'area', map:'area>nombre' ,type: 'string' },
                            { name: 'serieClave', map:'serie>clave' ,type: 'string' },
                            { name: 'seccionClave', map:'seccion>clave' ,type: 'string' },

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
                            text: 'Sección',
                            columntype: 'textbox',
                            datafield: 'seccion',
                            cellsrenderer: (row, columnField, value, defaultHtml, columnProperties, data ) => {
                                return `<div class="d-flex align-items-center h-100">${data.seccionClave} - ${data.seccion}</div>`;
                            }
                        },
                        {
                            text: 'Serie',
                            datafield: 'serie',
                            columntype: 'textbox',
                            cellsrenderer: (row, columnField, value, defaultHtml, columnProperties, data ) => {
                                return `<div class="d-flex align-items-center h-100">${data.serieClave} - ${data.serie}</div>`;
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
                            width: '80',
                            cellsrenderer: (row, columnField, value, defaultHtml, columnProperties, data ) => {
                                let btn1 = `<button title="Eliminar" class="btn btn-sm btn-outline-danger border-0" onclick="app.eliminarSerieAutorizada(${ data.id })"> <i class="bi-trash"></i></button>`;
                                defaultHtml = `<div class="d-flex justify-content-center align-items-center h-100" style="">${ btn1 }</div>`;

                                return defaultHtml;

                            }

                        }
                    ]
                } );
            _GRID_SOURCE = source;
        };

        const serieAutorizada = {
            isNew:           true,
            updated_at:      null,
            created_at:      null,
            id:              null,
            area_id:         null,
            departamento_id: null,
            seccion_id:      null,
            serie_id:        null,
        }

        const app = new Vue({
            el: '#main',
            data: {
                unidadesAdministrativas: {{ Js::from( $unidades_administrativas ) }},
                departamentos: {},
                secciones : {},
                series: {},
                seriesAutorizadas: {},
                serieSeleccionada: serieAutorizada,

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
                exportarTabla(){
                    $("#grid").jqxGrid('exportview', 'xlsx', 'series autorizadas');
                },
                obtenerSecciones(){

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
                            if ( e && e.hasOwnProperty('success') && e.success) {
                                if ( e.hasOwnProperty('secciones')) {
                                    this.secciones  = e.secciones;
                                }
                        }
                    });
                },

                obtenerSeries(event){
                    this.serieSeleccionada.serie_id = null;

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
                            if ( e && e.hasOwnProperty('success') && e.success) {
                                if ( e.hasOwnProperty('series')) {
                                    this.series  = e.series;
                                }
                        }
                    });
                },
                obtenerSeriesAutorizadas(){
                    if(!this.serieSeleccionada.departamento_id && !this.serieSeleccionada.area_id){
                        return;
                    }
                    let repetidas = true;
                    $.ajax({
                            headers: {'X-CSRF-TOKEN': $('meta[name="csrf-token"]').attr('content')},
                            url: `{{ url('ccleh/admin/series-autorizadas/get-series-autorizadas') }}/${this.serieSeleccionada.area_id}/${this.serieSeleccionada.departamento_id}/${repetidas}`,
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
                                if ( e.hasOwnProperty('series')) {
                                    this.seriesAutorizadas  = e.series;
                                    _GRID_SOURCE.localdata = this.seriesAutorizadas;
                                    _GRID.updatebounddata();
                                }
                        }
                    });
                },
                eliminarSerieAutorizada(id){

                    cclehConfirm.fire({ text: '¿Estas seguro de eliminar?'}).then( (r) => {
                        if ( r.isConfirmed ) {
                            $.ajax({
                                    headers: {'X-CSRF-TOKEN': $('meta[name="csrf-token"]').attr('content')},
                                     url: `{{ url('ccleh/admin/series-autorizadas/eliminar-serie-autorizada') }}/${id}`,
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
                                            }
                                        }
                                    }
                                })
                                .done(e => {
                                    if ( e && e.hasOwnProperty('success') && e.success) {
                                        toastr.success('Registro borrado');
                                        this.obtenerSeriesAutorizadas();
                                }
                            });
                        }
                    });

                },

                agregarSerieAutorizada(){
                    $.ajax({
                        headers: {'X-CSRF-TOKEN': $('meta[name="csrf-token"]').attr('content')},
                        url: `{{ url('ccleh/admin/series-autorizadas/agregar-serie-autorizada') }}`,
                        type: 'post',
                        data: this.serieSeleccionada,
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
                            // console.log(e.data);
                            this.obtenerSeriesAutorizadas();
                        }
                    });

                },
                obtenerDepartamentos(){
                    this.serieSeleccionada.departamento_id = null;
                    $.ajax({
                            headers: {'X-CSRF-TOKEN': $('meta[name="csrf-token"]').attr('content')},
                            url: `{{ url('ccleh/admin/series-autorizadas/get-departamentos') }}/${this.serieSeleccionada.area_id}`,
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
                                if ( e.hasOwnProperty('departamentos')) {
                                    this.departamentos  = e.departamentos;
                                }
                        }
                    });
                },

                serieActual(){
                    let ua = this.unidadesAdministrativas.find( ua => ua.id === this.serieSeleccionada.area_id );
                    return ua.nombre;
                },
                limpiarSeries(){
                    this.seriesAutorizadas = {};
                    _GRID_SOURCE.localdata = this.seriesAutorizadas;
                    _GRID.updatebounddata();
                }


            }
        })
    </script>

@endsection


@section('content')
    <main id="main" class="main" v-cloak>
        {{-- <div class="col-md-10 offset-md-1 col-lg-8 offset-lg-2">     --}}

            <div class="pagetitle" >
                <h1>Series Autorizadas por Unidad Administrativa</h1>
            </div>

            <div class="card">
                <div class="card-body">

                    <div class="row pt-3 pb-3">
                        <div class="col-sm-6">
                            <div class="form-floating">
                                <select class="form-select" id="ua"
                                        v-model="serieSeleccionada.area_id"
                                        @change="limpiarSeries();obtenerDepartamentos();"
                                        aria-label="Unidad Administrativa">
                                    <option :value="null" selected="" disabled>Selecciona...</option>
                                    <option v-for="ua of unidadesAdministrativas" v-if="ua.activo" :value="ua.id">@{{ ua.codigo }} @{{ ua.nombre }} - <span>Activo</span></option>
                                    <option v-else :value="ua.id">@{{ ua.codigo }} @{{ ua.nombre }} - <span>Inactivo</span></option>
                                </select>
                                <label for="ua">Unidad Administrativa</label>
                            </div>
                        </div>
                        <div class="col-sm-6">
                            <div class="form-floating">
                                <select class="form-select" id="ag"
                                        v-model="serieSeleccionada.departamento_id"
                                        @change="obtenerSeriesAutorizadas();"
                                        aria-label="Área generadora">
                                    <option :value="null" selected="" disabled>Selecciona...</option>
                                    <option v-for="ag of departamentos" v-if="ag.activo" :value="ag.id">@{{ ag.clave }} @{{ ag.nombre }} - <span>Activo</span></option>
                                    <option v-else :value="ag.id">@{{ ag.clave }} @{{ ag.nombre }} - <span>Inactivo</span></option>
                                </select>
                                <label for="ag">Área Generadora</label>
                            </div>
                        </div>
                    </div>

                    <div class="row">
                        <h5>Autorizar Serie</h5>
                        <div class="col-sm-6">
                            <div class="form-floating">
                                <select class="form-select" id="seccion"
                                        v-model="serieSeleccionada.seccion_id"
                                        aria-label="Sección"
                                        @change="obtenerSeries($event);">
                                    <option :value="null" selected="" disabled>Elige</option>
                                    <option v-for="seccion of secciones" :value="seccion.id">@{{ seccion.clave }} - @{{ seccion.nombre }}</option>
                                </select>
                                <label for="seccion">Sección</label>
                            </div>
                        </div>

                        <div class="col-sm-6">
                            <div class="form-floating">
                                <select class="form-select" id="serie"
                                        v-model="serieSeleccionada.serie_id"
                                        aria-label="Serie">
                                    <option :value="null" selected="" disabled>Elige</option>
                                    <option v-for="serie of series" :value="serie.id">@{{ serie.clave }} - @{{ serie.nombre }}</option>
                                </select>
                                <label for="serie">Serie</label>
                            </div>
                        </div>

                        <div class="form-group col-12 d-flex justify-content-end mt-3">
                                <button class="btn btn-teal align-self-end" @click="agregarSerieAutorizada();">
                                    <i class="bi bi-plus"></i>Autorizar
                                </button>
                        </div>

                    </div>

                </div>
            </div>

            <div class="card pt-4">
                <div class="card-body">
                    <div class="row">
                        <div class="col-12 text-center" v-if="serieSeleccionada.area_id">
                            <h5>Series autorizadas para @{{ serieActual() }}</h5>
                        </div>
                    </div>
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

        {{-- </div> --}}
    </main>

@endsection
