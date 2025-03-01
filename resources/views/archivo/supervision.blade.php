@extends('layouts.master', ['title' => 'Panel de Supervisión'])

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
                            // { name: 'area_nombre', map:'area>nombre' , type: 'string' },
                            { name: 'area_nombre', map:'nombre' , type: 'string' },
                            { name: 'semaforo' , type: 'int' },
                            { name: 'semaforo_texto' , type: 'string' },

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
                            text: 'Unidad Administrativa',
                            columntype: 'textbox',
                            datafield: 'area_nombre',
                        },
                        {
                            text: 'Resumen',
                            columntype: 'textbox',
                            cellsrenderer: (row, columnField, value, defaultHtml, columnProperties, data ) => {

                                const semaforo = Number( data.semaforo ) || 0;
                                /**
                                 *  0 = No capturo datos
                                 *  1 = Completo
                                 *  2 = Incompleto
                                 */
                                let clase = 'info';
                                switch( semaforo ){
                                    case 0:
                                        clase = 'danger';
                                        break;
                                    case 1:
                                        clase = 'success';
                                        break;
                                    case 2:
                                        clase = 'warning';
                                        break;
                                }

                                return `<div class="d-flex justify-content-center align-items-center h-100" style="">
                                             <div class="text-${ clase }"><div class="badge rounded-pill bg-${ clase } me-2">
                                             &nbsp;</div>${ data.semaforo_texto} </div>
                                        </div>`;
                            }
                        }
                    ]
                } );
            _GRID_SOURCE = source;
        };

        const defaultPanelSupervision = {
            anio: null,
            mes: null,
            fecha_cierre: null,
        }

        const app = new Vue({
            el: '#main',
            data: {
                {{-- areas:              {{ Js::from( $areas ) }}, --}}
                {{--calendarios:        {{ Js::from( $calendarios ) }},--}}
                anios_supervision:     {{ Js::from( $anios_reporte ) }},
                panelSupervision: defaultPanelSupervision,
                meses_supervision:      null,
                areas_resumen: null,

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
                    $("#grid").jqxGrid('exportview', 'xlsx', 'supervisión-semaforo');
                },
                obtenerMeses(){
                   $.ajax({
                            headers: {'X-CSRF-TOKEN': $('meta[name="csrf-token"]').attr('content')},
                            url: `{{ url('ccleh/admin/supervision/get-meses-captura') }}/${ this.panelSupervision.anio }`,
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
                                    this.meses_supervision = e.meses;
                                }
                        }
                    });
                },

                obtenerAreas(){

                    if( this.panelSupervision.mes != null && this.panelSupervision.anio != null ){
                        $.ajax({
                                headers: {'X-CSRF-TOKEN': $('meta[name="csrf-token"]').attr('content')},
                                url: `{{ url('ccleh/admin/supervision/areas-semaforo') }}/${this.panelSupervision.mes}/${this.panelSupervision.anio}`,
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
                                        this.areas_resumen  = e.areas;
                                        // console.log(this.areas_resumen);
                                        _GRID_SOURCE.localdata = this.areas_resumen;
                                        _GRID.updatebounddata();
                                        for (var i = 0; i < this.meses_supervision.length; i++) {
                                            if (this.meses_supervision[i].numero == this.panelSupervision.mes ){
                                                this.panelSupervision.fecha_cierre = this.meses_supervision[i].fecha_cierre;
                                            }
                                        }
                                    }
                            }
                        });

                    }else{
                        _GRID_SOURCE.localdata = [];
                        _GRID.updatebounddata();
                    }
                },
            }
        })
    </script>

@endsection


@section('content')
    <main id="main" class="main" v-cloak>
        <div {{-- class="col-md-8 offset-md-2" --}}>

            <div class="pagetitle">
                <h1>Panel de Supervisión</h1>
            </div>

            <div class="card">
                <div class="card-body">

                    <div class="row pt-3">
                        <div class="col-sm-2">
                            <div class="form-floating">
                                    <select class="form-select" id="anio"
                                            v-model="panelSupervision.anio"
                                            @change="obtenerMeses();obtenerAreas();"
                                            aria-label="Año">
                                        <option :value="null" selected="" disabled>Selecciona...</option>
                                        {{-- <option value="todos" selected="">Todos</option> --}}
                                        <option v-for="anios of anios_supervision" :value="anios">@{{ anios }}</option>
                                    </select>
                                    <label for="anio">Año</label>
                            </div>
                        </div>

                        <div class="col-sm-2">
                            <div class="form-floating">
                                    <select class="form-select" id="mes"
                                            v-model="panelSupervision.mes"
                                            @change="obtenerAreas();"
                                            aria-label="Mes">
                                        <option :value="null" selected="" disabled>Selecciona...</option>
                                        {{-- <option v-if="panelSupervision.anio" value="todos">Todos</option> --}}
                                        <option v-for="mes of meses_supervision" :value="mes.numero">
                                            @{{ mes.nombre  }}
                                        </option>
                                    </select>
                                    <label for="mes">Mes</label>
                            </div>
                        </div>
                        <div class="col-sm-2" v-if="panelSupervision.fecha_cierre">
                            <b>Fecha de cierre de captura:</b>
                            <p v-if="panelSupervision.fecha_cierre">@{{ panelSupervision.fecha_cierre }}</p>
                        </div>

                    </div>
                </div>
            </div>

            <div class="card pt-4">
                <div class="card-body">
                    {{-- <div class="row mb-2">
                        <div class="col-12 text-end">
                            <button class="btn btn-success" @click="exportarTabla()">
                                <i class="bi bi-file-earmark-excel"></i> Exportar
                            </button>
                        </div>
                    </div> --}}
                    <section class="section">
                        <div id="grid"></div>
                    </section>
                </div>
            </div>

        </div>
    </main>

@endsection
