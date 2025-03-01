@extends('layouts.master', ['title' => 'Transferencia primaria'])

@section('styles')

@endsection

@section('scripts')
    @include('layouts.includes.jquery-js-base')

    <script type="module" defer>
        import { IPaginate } from "{{ asset('js/types/IPaginate.js') }}";
        import Paginator from "{{ asset('js/components/Paginator.js') }}";

        const TRANSFERENCIA_PRIMARIA = {
            id: null,
            anio: null,
            departamento_id: null,
            seccion_id: null,
            serie_id: null,
            fecha_entrega: null,
            no_oficio_transferencia: null,
            no_caja: null,
            cerrado: false,
        };

        const EXPEDIENTE = {
            id: null,
            transferencia_primaria_id: null,
            inventario_documental_detalle_id: null,
            inventario_documental_detalle: null,
            no_expediente_legajo: null,
            no_legajo: null,
            no_fojas: null,
            descripcion: null,
            fecha_inicio: null,
            fecha_final: null,
            observaciones: null,
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
                {
                    nombre: '',
                    cargo: '',
                },
                {
                    nombre: '',
                    cargo: '',
                }
            ],
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

        const app = new Vue({
            el: '#main',
            components: {
                Paginator
            },
            data: {
                departamento_id: {{ auth()->user()->persona->departamento_id }},
                area_id: {{ auth()->user()->persona->area->id }},
                areas: {{ Js::from( $areas ) }},
                departamentos: {},
                anios: {{ Js::from( $anios ) }},
                secciones : {},
                series: {},
                transferenciasPrimarias: {},
                transferenciaPrimaria: JSON.parse(JSON.stringify(TRANSFERENCIA_PRIMARIA)),
                transferenciaPrimariaDetalles: new IPaginate(),
                transferenciaDetalleEditar: {},
                expedientes: {},
                eliminarExpedientes: [],
                filtros: {
                    area_id: {{ auth()->user()->persona->area->id }},
                    departamento_id: {{ auth()->user()->persona->departamento_id }},
                    anio: null,
                    seccion: null,
                    serie: null,
                    transferencia_id: null,
                },

                firmas: JSON.parse(JSON.stringify(FIRMAS)),
                cadido: JSON.parse(JSON.stringify(CADIDO)),
                expedientesBusqueda: new IPaginate(),
                noExpedienteBusqueda: null,
                expedientesNuevaTransferencia: [],

            },
            created(){},
            beforeMount(){},
            computed: {

            },
            mounted(){
                this.obtenerSecciones();
                this.obtenerDepartamentos();
            },
            methods: {
                obtenerSecciones() {
                    this.filtros.serie = null;
                    this.filtros.seccion = null;
                    this.series = {};
                    this.secciones = {};

                    if(!this.filtros.departamento_id){
                        return;
                    }

                    let repetidas = false;
                    $.ajax({
                        headers: {'X-CSRF-TOKEN': $('meta[name="csrf-token"]').attr('content')},
                        url: `{{ url('ccleh/inventario/captura/get-series-autorizadas') }}/${this.filtros.area_id}/${this.filtros.departamento_id}/${repetidas}`,
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

                obtenerSeries(event) {
                    this.filtros.serie = null;
                    let repetidas = "si";
                    $.ajax({
                        headers: {'X-CSRF-TOKEN': $('meta[name="csrf-token"]').attr('content')},
                        url: `{{ url('ccleh/inventario/captura/get-series-autorizadas') }}/${this.filtros.area_id}/${this.filtros.departamento_id}/${repetidas}/${this.filtros.seccion}`,
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

                buscarTransferenciaPrimaria(){
                    if (!this.filtros.transferencia_id) {
                        this.transferenciaPrimaria = JSON.parse(JSON.stringify(TRANSFERENCIA_PRIMARIA));
                        this.transferenciaPrimariaDetalles = new IPaginate();
                        this.firmas = JSON.parse(JSON.stringify(FIRMAS));
                        return;
                    }

                    $.ajax({
                        url: `{{ url('transferencia/primaria/buscar-transferencia') }}/${this.filtros.transferencia_id}`,
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
                            if ( e && e.hasOwnProperty('success') && e.success) {
                                if ( e.hasOwnProperty('transferenciaPrimaria') &&  e.transferenciaPrimaria) {
                                    this.transferenciaPrimaria = e.transferenciaPrimaria;
                                    this.firmas = e.firmas ?? JSON.parse(JSON.stringify(FIRMAS));
                                    this.moveToPage();
                                }else{
                                    this.transferenciaPrimaria = JSON.parse(JSON.stringify(TRANSFERENCIA_PRIMARIA));
                                    this.transferenciaPrimariaDetalles = new IPaginate();
                                    this.firmas = JSON.parse(JSON.stringify(FIRMAS));
                                }
                            }
                        });
                },

                async moveToPage(url = '') {
                    let page = '';
                    if (url.length) {
                        page = (new URL(url).searchParams).get('page');
                    } else {
                        page = this.transferenciaPrimariaDetalles.current_page
                    }

                    $.ajax({
                        url: `{{ route('transferencia.primaria.buscar.transferencia.detalles') }}`,
                        type: 'post',
                        data: {page, transferencia_id: this.transferenciaPrimaria.id},
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
                                if ( e.hasOwnProperty('transferenciaPrimariaDetalles')) {
                                    this.transferenciaPrimariaDetalles = e.transferenciaPrimariaDetalles;
                                }
                            }
                        });

                },

                generarTransferenciaPrimaria(){
                    if (!this.filtros.seccion || !this.filtros.serie || !this.filtros.anio) {
                        return;
                    }
                    let data = {
                        anio: this.filtros.anio,
                        seccion_id: this.filtros.seccion,
                        serie_id: this.filtros.serie,
                        departamento_id: this.filtros.departamento_id,
                    };

                    $.ajax({
                        url: `{{ route('transferencia.primaria.generar.transferencia') }}`,
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
                                    }
                                } finally {
                                }
                            }
                        }
                    })
                        .done(e => {
                            if ( e && e.hasOwnProperty('success') && e.success) {
                                toastr.success(e.message);
                                this.filtros.transferencia_id = e.transferenciaId;
                                this.buscarTransferenciaPrimaria();
                            }
                        });
                },


                guardarExpediente(){
                    let data = {
                        expedientes: this.expedientes,
                        eliminar_expedientes: this.eliminarExpedientes,
                    };

                    $.ajax({
                        url: `{{ route('transferencia.primaria.guardar.transferencia.detalle') }}`,
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
                                    }
                                } finally {
                                }
                            }
                        }
                    })
                        .done(e => {
                            if ( e && e .hasOwnProperty('success') && e.success) {
                                toastr.success(e.message);
                                this.buscarTransferenciaPrimaria();
                                this.wModalShowing.hide();
                                this.wModalShowing = null;
                            }
                        });
                },

                verEditarExpediente(transferenciaPrimariaDetalleId) {
                    this.eliminarExpediente = [];
                    this.expediente = {};
                    $.ajax({
                        url: `{{ url('transferencia/primaria/buscar-legajos/') }}/${ transferenciaPrimariaDetalleId }`,
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
                            if ( e && e.hasOwnProperty('success') && e.success) {
                                if ( e.hasOwnProperty('legajos') ) {
                                    this.expedientes = e.legajos;
                                    this.wModalShowing = new bootstrap.Modal(document.getElementById('wEditarExpediente'));
                                    this.wModalShowing.show();
                                }
                            }
                        });
                },

                obtenerDepartamentos(){
                    if( !this.filtros.area_id ){
                        return;
                    }
                    $.ajax({
                        headers: {'X-CSRF-TOKEN': $('meta[name="csrf-token"]').attr('content')},
                        url: `{{ url('ccleh/inventario/reporte/getDepartamentosArea') }}/${this.filtros.area_id}`,
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

                verEditarTransferencia(){
                    this.wModalShowing = new bootstrap.Modal(document.getElementById('wEditarTransferencia'));
                    this.wModalShowing.show();
                },

                guardarTransferencia(){
                    let data = {
                        'id': this.transferenciaPrimaria.id,
                        'fecha_entrega': this.transferenciaPrimaria.fecha_entrega,
                        'no_oficio_transferencia': this.transferenciaPrimaria.no_oficio_transferencia,
                        'no_caja': this.transferenciaPrimaria.no_caja,
                    };
                    $.ajax({
                        url: `{{ route('transferencia.primaria.editar.transferencia') }}`,
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
                                    }
                                } finally {
                                }
                            }
                        }
                    })
                        .done(e => {
                            if ( e && e .hasOwnProperty('success') && e.success) {
                                toastr.success(e.message);
                                this.buscarTransferenciaPrimaria();
                                this.wModalShowing.hide();
                                this.wModalShowing = null;
                            }
                        });
                },

                verEditarPortada(transferenciaDetalleId){
                    this.transferenciaDetalleEditar = JSON.parse(JSON.stringify(this.buscarExpedientePorId(transferenciaDetalleId)));
                    this.wModalShowing = new bootstrap.Modal(document.getElementById('wEditarPortada'));
                    this.wModalShowing.show();
                },

                guardarPortada(){
                    let data = {
                        'id': this.transferenciaDetalleEditar.id,
                        'portada_observaciones': this.transferenciaDetalleEditar.portada_observaciones,
                    };
                    $.ajax({
                        url: `{{ route('portada.guardar.observaciones') }}`,
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
                                    }
                                } finally {
                                }
                            }
                        }
                    })
                        .done(e => {
                            if ( e && e .hasOwnProperty('success') && e.success) {
                                this.transferenciaDetalleEditar = {};
                                toastr.success(e.message);
                                this.buscarTransferenciaPrimaria();
                                this.wModalShowing.hide();
                                this.wModalShowing = null;
                            }
                        });                },

                descargarTransferencia() {
                    window.open(`{{ url('transferencia/primaria/exportar-transferencia') }}/${ this.transferenciaPrimaria.id }`);
                },

                descargarEtiquetas() {
                    window.open(`{{ url('transferencia/primaria/generar-etiquetas') }}/${ this.transferenciaPrimaria.id }`);
                },

                buscarExpedientePorId(id) {
                    return Object.values(this.transferenciaPrimariaDetalles.data).find(transferencia => transferencia.id == id);
                },

                agregarLegajo(){
                    let nuevoLegajo = JSON.parse(JSON.stringify(EXPEDIENTE));
                    nuevoLegajo.transferencia_primaria_id = this.expedientes[0].transferencia_primaria_id;
                    nuevoLegajo.inventario_documental_detalle_id = this.expedientes[0].inventario_documental_detalle_id;
                    this.expedientes.push(nuevoLegajo);
                    this.asignarNoLegajos();
                },

                eliminarLegajo(indexLegajo){
                    let legajo = this.expedientes[indexLegajo];
                    if( legajo.id ){
                        this.eliminarExpedientes.push(legajo.id);
                    }
                    this.expedientes.splice(indexLegajo,1);
                    if(this.expedientes.length === 1){
                        this.expedientes[0]['descripcion'] = null;
                        this.expedientes[0]['fecha_inicio'] = null;
                        this.expedientes[0]['fecha_final'] = null;
                        this.expedientes[0]['observaciones'] = null;
                    }
                    this.asignarNoLegajos();
                },


                asignarNoLegajos(){
                    if( this.expedientes.length === 1 ){
                        this.expedientes[0].no_legajo = null;
                    }else{
                        this.expedientes.forEach((expediente, index) => {
                            expediente.no_legajo = index+1;
                        });
                    }
                },

                agregarFechaConsulta(){
                    let fecha = document.getElementById('portada_fechas_consulta').value;
                    console.log(fecha);
                },

                descargarPortadilla(transferenciaPrimariaDetalleId){
                    window.open(`{{ url('portada/exportar-portada') }}/${ transferenciaPrimariaDetalleId }`);
                },

                descargarPortadas(){
                    window.open(`{{ url('portada/exportar-multiples-portadas') }}/${ this.transferenciaPrimaria.id }`);
                },

                verEditarFirmas(){
                    this.wModalShowing = new bootstrap.Modal(document.getElementById('wFirmas'));
                    this.wModalShowing.show();
                },

                guardarFirmas() {
                    $.ajax({
                        headers: {'X-CSRF-TOKEN': $('meta[name="csrf-token"]').attr('content')},
                        url: `{{ url('ccleh/firmas/guardar-transferencia-primaria') }}/${this.filtros.departamento_id}`,
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

                enviarAValidar(id) {
                    cclehConfirm.fire({
                        text: 'No podrás realizar cambios, ¿Estas seguro?'
                    }).then( (r) => {
                        if (r.isConfirmed) {
                            $.ajax({
                                headers: {'X-CSRF-TOKEN': $('meta[name="csrf-token"]').attr('content')},
                                url: `{{ url('transferencia/primaria/enviar-validar') }}/${id}`,
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
                                        toastr.success('Transferencia enviada a validación');
                                        this.buscarTransferenciaPrimaria();
                                    }
                                });

                        }
                    });
                },

                validarTransferencia(id) {
                    cclehConfirm.fire({
                        title: 'Validar transferencia',
                        showDenyButton: true,
                        confirmButtonColor: "#28B463",
                        confirmButtonText: "Validar",
                        denyButtonText: "Rechazar",
                        cancelButtonText: "Cancelar",
                    }).then( (r) => {
                        if (r.isConfirmed) {
                            $.ajax({
                                headers: {'X-CSRF-TOKEN': $('meta[name="csrf-token"]').attr('content')},
                                url: `{{ url('transferencia/primaria/validar') }}/${id}`,
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
                                        toastr.success('Transferencia validada');
                                        this.buscarTransferenciaPrimaria();
                                    }
                                });

                        } else if (r.isDenied) {
                            $.ajax({
                                headers: {'X-CSRF-TOKEN': $('meta[name="csrf-token"]').attr('content')},
                                url: `{{ url('transferencia/primaria/rechazar') }}/${id}`,
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
                                        toastr.warning('Transferencia rechazada');
                                        this.buscarTransferenciaPrimaria();
                                    }
                                });
                        }
                    });
                },

                cancelarValidacion(id){
                    cclehConfirm.fire({
                        text: '¿Quitar validación de la transferencia?'
                    }).then( (r) => {
                        if (r.isConfirmed) {
                            $.ajax({
                                headers: {'X-CSRF-TOKEN': $('meta[name="csrf-token"]').attr('content')},
                                url: `{{ url('transferencia/primaria/cancelar-validacion') }}/${id}`,
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
                                        toastr.warning('Se elimino la validación de la transferencia');
                                        this.buscarTransferenciaPrimaria();
                                    }
                                });
                        }
                    });
                },

                buscarCadido(){
                    if (!this.filtros.seccion || !this.filtros.serie || !this.filtros.anio || !this.filtros.departamento_id) {
                        this.cadido = JSON.parse(JSON.stringify(CADIDO));
                        return;
                    }

                    let data = {
                        anio: this.filtros.anio,
                        seccion_id: this.filtros.seccion,
                        serie_id: this.filtros.serie,
                    };

                    $.ajax({
                        url: `{{ route('ccleh.cadido.buscar.cadido') }}`,
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
                                    }
                                } finally {
                                }
                            }
                        }
                    })
                        .done(e => {
                            if ( e && e.hasOwnProperty('success') && e.success) {
                                if ( e.hasOwnProperty('cadido') &&  e.cadido) {
                                    this.cadido = e.cadido;
                                    if(!this.cadido.candado){
                                        this.filtros.transferencia_id = this.transferenciasPrimarias[0] ? this.transferenciasPrimarias[0]['id'] : null;
                                        this.buscarTransferenciaPrimaria();
                                    }else{
                                        this.filtros.transferencia_id = null;
                                        this.buscarTransferenciaPrimaria();
                                    }
                                }else{
                                    this.cadido = JSON.parse(JSON.stringify(CADIDO));
                                }
                            }
                        });
                },

                buscarTransferenciasPrimarias(){
                    if (!this.filtros.seccion || !this.filtros.serie || !this.filtros.anio || !this.filtros.departamento_id) {
                        this.transferenciasPrimarias = {};
                        this.transferenciaPrimaria = JSON.parse(JSON.stringify(TRANSFERENCIA_PRIMARIA));
                        this.transferenciaPrimariaDetalles = new IPaginate();
                        return;
                    }

                    let data = {
                        anio: this.filtros.anio,
                        seccion_id: this.filtros.seccion,
                        serie_id: this.filtros.serie,
                        departamento_id: this.filtros.departamento_id,
                    };

                    $.ajax({
                        url: `{{ route('transferencia.primaria.buscar.transferencias.primarias') }}`,
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
                                    }
                                } finally {
                                }
                            }
                        }
                    })
                        .done(e => {
                            if ( e && e.hasOwnProperty('success') && e.success) {
                                if ( e.hasOwnProperty('transferenciasPrimarias') &&  e.transferenciasPrimarias) {
                                    this.transferenciasPrimarias = e.transferenciasPrimarias;
                                    this.firmas = e.firmas ?? JSON.parse(JSON.stringify(FIRMAS));
                                }else{
                                    this.transferenciasPrimarias = e.transferenciasPrimarias;
                                    this.transferenciaPrimaria = JSON.parse(JSON.stringify(TRANSFERENCIA_PRIMARIA));
                                    this.transferenciaPrimariaDetalles = new IPaginate();
                                    this.firmas = JSON.parse(JSON.stringify(FIRMAS));
                                }
                                this.buscarCadido();
                            }
                        });
                },


                verGenerarTransferenciaParcial(){
                    this.expedientesBusqueda = new IPaginate;
                    this.noExpedienteBusqueda = null;
                    this.expedientesNuevaTransferencia = [];
                    this.wModalShowing = new bootstrap.Modal(document.getElementById('wgenerarTransferenciaParcial'));
                    this.wModalShowing.show();
                },

                generarTransferenciaParcial(){
                    if(this.expedientesNuevaTransferencia.length < 1){
                        toastr.error('Debes seleccionar al menos un expediente');
                    }
                    let expedientes = [];
                    this.expedientesNuevaTransferencia.forEach(expediente => expedientes.push(expediente.id));
                    let data = {
                        expedientes: expedientes,
                        anio: this.filtros.anio,
                        seccion_id: this.filtros.seccion,
                        serie_id: this.filtros.serie,
                        departamento_id: this.filtros.departamento_id,
                    };

                    $.ajax({
                        url: `{{ route('transferencia.primaria.generar.transferencia.parcial') }}`,
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
                                    }
                                } finally {
                                }
                            }
                        }
                    })
                    .done(e => {
                        if ( e && e.hasOwnProperty('success') && e.success) {
                            toastr.success(e.message);

                            this.buscarTransferenciasPrimarias();

                            this.expedientesBusqueda = new IPaginate;
                            this.noExpedienteBusqueda = null;
                            this.expedientesNuevaTransferencia = [];
                            this.wModalShowing.hide();
                            this.wModalShowing = null;
                        }
                    });


                },

                buscarExpedientes(url = ''){

                    if (!this.filtros.seccion || !this.filtros.serie || !this.filtros.anio || !this.filtros.departamento_id) {
                        this.expedientesBusqueda = new IPaginate;
                        return;
                    }

                    let page = '';
                    if (url.length) {
                        page = (new URL(url).searchParams).get('page');
                    } else {
                        page = this.expedientesBusqueda.current_page
                    }

                    let data = {
                        anio: this.filtros.anio,
                        seccion_id: this.filtros.seccion,
                        serie_id: this.filtros.serie,
                        departamento_id: this.filtros.departamento_id,
                        no_expediente: this.noExpedienteBusqueda,
                        page: page,
                    };

                    $.ajax({
                        url: `{{ route('transferencia.primaria.buscar.expedientes') }}`,
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
                                    }
                                } finally {
                                }
                            }
                        }
                    })
                        .done(e => {
                            if ( e && e.hasOwnProperty('success') && e.success) {
                                if ( e.hasOwnProperty('expedientes') &&  e.expedientes) {
                                    this.expedientesBusqueda = e.expedientes;
                                }
                            }
                        });
                },


                eliminarTransferencia(){
                    if(!this.transferenciaPrimaria.id){
                        return;
                    }
                    cclehConfirm.fire({
                        text: '¿Seguro de eliminar la transferencia?'
                    }).then( (r) => {
                        if (r.isConfirmed) {
                            $.ajax({
                                headers: {'X-CSRF-TOKEN': $('meta[name="csrf-token"]').attr('content')},
                                url: `{{ url('transferencia/primaria/eliminar-transferencia') }}/${this.transferenciaPrimaria.id}`,
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
                                        toastr.success('Se elimino la transferencia correctamente');
                                        this.filtros.transferencia_id = null;
                                        if(this.cadido.candado){
                                            this.buscarTransferenciasPrimarias();
                                        }else{
                                            this.buscarTransferenciaPrimaria();
                                        }




                                    }
                                });
                        }
                    });
                },


                agregarExpedienteTransferencia(expediente){
                    if( this.expedientesNuevaTransferencia.find( (expedienteTransferencia) => expedienteTransferencia.id === expediente.id ) ){
                        toastr.error('El expediente ya ha sido agregado.');
                        return;
                    }
                    this.expedientesNuevaTransferencia.push(expediente);
                    toastr.success('Expediente '+expediente.no_expediente+' agregado.');
                },

                eliminarExpedienteTransferencia(expedienteId){
                    this.expedientesNuevaTransferencia = this.expedientesNuevaTransferencia.filter( expedienteTransferencia => expedienteTransferencia.id !== expedienteId);
                },

                cambioUnidadAdministrativa(){
                    this.filtros.departamento_id = null;
                    this.obtenerDepartamentos();
                    this.obtenerSecciones();
                    this.buscarTransferenciasPrimarias();
                },

            }


        })
    </script>

@endsection


@section('content')
    <main id="main" class="main" v-cloak>

            <div class="pagetitle">
                <h1>Transferencia primaria</h1>
            </div>

            <div class="card">
                <div class="card-body">
                    <div class="row pt-3">
                        @can('transferenciaPrimaria.editarOtros')
                                <div class="col-sm-12 col-md-6 mb-2">
                                    <div class="form-floating">
                                        <select class="form-select" id="unidadAdministrativa"
                                                v-model="filtros.area_id"
                                                @change="cambioUnidadAdministrativa();"
                                                aria-label="Unidad Administrativa">
                                            <option :value="null" selected="" disabled>Selecciona...</option>
                                            <option v-for="ua of areas" v-if="ua.activo" :value="ua.id">@{{ ua.codigo }} @{{ ua.nombre }} - <span>Activo</span></option>
                                            <option v-else :value="ua.id">@{{ ua.codigo }} @{{ ua.nombre }} - <span>Inactivo</span></option>
                                        </select>
                                        <label for="unidadAdministrativa">Unidad Administrativa</label>
                                    </div>
                                </div>
                                <div class="col-sm-12 col-md-6 mb-2">
                                    <div class="form-floating">
                                        <select class="form-select" id="areaGeneradora"
                                                v-model="filtros.departamento_id"
                                                @change="obtenerSecciones();buscarTransferenciasPrimarias();"
                                                aria-label="Área Generadora">
                                            <option :value="null" selected="" disabled>Selecciona...</option>
                                            <option v-for="ag of departamentos" v-if="ag.activo" :value="ag.id">@{{ ag.clave }} @{{ ag.nombre }} - <span>Activo</span></option>
                                            <option v-else :value="ag.id">@{{ ag.clave }} @{{ ag.nombre }} - <span>Inactivo</span></option>
                                        </select>
                                        <label for="areaGeneradora">Área Generadora</label>
                                    </div>
                                </div>
                        @endcan

                        <div class="col-sm-4 col-md-2">
                            <div class="form-floating">
                                <select class="form-select" id="anio"
                                        v-model="filtros.anio"
                                        @change="buscarTransferenciasPrimarias();"
                                        aria-label="Año">
                                    <option :value="null" selected="" disabled>Selecciona...</option>
                                    <option v-for="anios of anios" :value="anios">@{{ anios }}</option>
                                </select>
                                <label for="anio">Año</label>
                            </div>
                        </div>
                        <div class="col-sm-4 col-md-5">
                            <div class="form-floating">
                                <select class="form-select" id="seccion"
                                        v-model="filtros.seccion"
                                        aria-label="Sección"
                                        @change="obtenerSeries($event);buscarTransferenciasPrimarias();">
                                    <option :value="null" selected="" disabled>Selecciona...</option>
                                    <option v-for="seccion of secciones" :value="seccion.seccion.id">@{{ seccion.seccion.clave }} - @{{ seccion.seccion.nombre }}</option>
                                </select>
                                <label for="seccion">Sección</label>
                            </div>
                        </div>

                        <div class="col-sm-4 col-md-5">
                            <div class="form-floating">
                                <select class="form-select" id="serie"
                                        v-model="filtros.serie"
                                        @change="buscarTransferenciasPrimarias();"
                                        aria-label="Serie">
                                    <option :value="null" selected="" disabled>Elige</option>
                                    <option v-for="serie of series" :value="serie.serie.id">@{{ serie.serie.clave }} - @{{ serie.serie.nombre }}</option>
                                </select>
                                <label for="serie">Serie</label>
                            </div>
                        </div>

                    </div>

                </div>
            </div>

            <div class="card pt-4" v-if="filtros.anio && filtros.seccion && filtros.serie">
                <div class="card-body" v-if="cadido.id">
                    <div v-if="cadido.candado" class="row">
                        <div class="col-12 col-md-8">
                            <div class="form-floating">
                                <select class="form-select mb-3" id="transferencias_primarias"
                                        v-model="filtros.transferencia_id"
                                        @change="buscarTransferenciaPrimaria();"
                                        aria-label="Transferencias Primarias">
                                    <option :value="null" selected="" disabled>Elige</option>
                                    <option v-for="transferencia of transferenciasPrimarias" :value="transferencia.id">No expedientes: @{{ transferencia.no_expedientes }} - @{{ this.dayjs(transferencia.created_at).format('DD/MM/YYYY') }}</option>
                                </select>
                                <label for="transferencias_primarias">Transferencias Generadas</label>
                            </div>
                        </div>
                        <div class="col-12 col-md-4">
                            <button type="button" v-if="departamento_id == filtros.departamento_id" @click="verGenerarTransferenciaParcial()" class="btn btn-teal w-100 mb-3">
                                <i class="bi bi-plus"></i> Generar nueva transferencia
                            </button>
                        </div>
                    </div>
                    <div v-if="!cadido.candado && !transferenciaPrimaria.id" class="row">
                        <div class="col-12 col-md-4">
                            <button type="button" v-if="departamento_id == filtros.departamento_id" @click="generarTransferenciaPrimaria()" class="btn btn-teal w-100 mb-3">
                                <i class="bi bi-plus"></i> Generar nueva transferencia
                            </button>
                        </div>
                    </div>

                    <div class="row mb-2">
                        <div class="col-12 text-end">

                            @can('transferenciaPrimaria.editarOtros')

                            <button type="button" v-if="transferenciaPrimaria.validada"  @click="cancelarValidacion(transferenciaPrimaria.id)" class="btn btn-danger m-1">
                                <i class="bi bi-x-circle"></i> Quitar validación
                            </button>

                            <button type="button" v-if="transferenciaPrimaria.estado == 2"  @click="validarTransferencia(transferenciaPrimaria.id)" class="btn btn-primary m-1">
                                <i class="bi bi-clipboard-check"></i> Validar
                            </button>

                            @endcan


                            <button type="button" v-if="transferenciaPrimaria.editable && departamento_id == filtros.departamento_id" @click="enviarAValidar(transferenciaPrimaria.id)" class="btn btn-primary m-1">
                                <i class="bi bi-send-check"></i> Enviar a validar
                            </button>

                            <button type="button" v-if="transferenciaPrimaria.editable && departamento_id == filtros.departamento_id" @click="eliminarTransferencia()" class="btn btn-danger m-1">
                                <i class="bi bi-trash"></i> Borrar Transferencia
                            </button>

                            <button type="button" class="btn btn-secondary m-1"
                                    v-if="transferenciaPrimaria.id && departamento_id == filtros.departamento_id && transferenciaPrimaria.validada"
                                    @click="verEditarFirmas()">
                                <i class="bi bi-pencil-square"></i>&nbsp;Firmas
                            </button>
                            <button type="button" v-if="transferenciaPrimaria.id && departamento_id == filtros.departamento_id && transferenciaPrimaria.validada" @click="verEditarTransferencia()" class="btn btn-secondary m-1">
                                <i class="bi bi-pencil-square"></i> Datos Transferencia
                            </button>
                            <button type="button" v-if="(transferenciaPrimaria.id && transferenciaPrimaria.validada) || (transferenciaPrimaria.id && {{Js::from(auth()->user()->can('transferenciaPrimaria.editarOtros'))}})" class="btn btn-success m-1" @click="descargarEtiquetas()">
                                <i class="bi bi-stickies"></i> Descargar Etiquetas
                            </button>
                            <button type="button" v-if="(transferenciaPrimaria.id && transferenciaPrimaria.validada) || (transferenciaPrimaria.id && {{Js::from(auth()->user()->can('transferenciaPrimaria.editarOtros'))}})" class="btn btn-success m-1" @click="descargarTransferencia()">
                                <i class="bi bi-file-earmark-excel"></i> Descargar Transferencia
                            </button>
                            <button type="button" v-if="(transferenciaPrimaria.id && transferenciaPrimaria.validada) || (transferenciaPrimaria.id && {{Js::from(auth()->user()->can('transferenciaPrimaria.editarOtros'))}})" class="btn btn-success m-1" @click="descargarPortadas()">
                                <i class="bi bi-files-alt"></i> Descargar Portadas
                            </button>
                        </div>
                    </div>

                    <div class="row pt-3" v-if="transferenciaPrimaria.estado == {{config('enums.estados_transferencias.abierto.valor')}}">
                        <div class="alert alert-secondary text-center" role="alert">
                            Transferencia en captura
                        </div>
                    </div>

                    <div class="row pt-3" v-if="transferenciaPrimaria.estado == {{config('enums.estados_transferencias.pendiente_revision.valor')}}">
                        <div class="alert alert-warning text-center" role="alert">
                            Transferencia pendiente de validación
                        </div>
                    </div>

                    <div class="row pt-3" v-if="transferenciaPrimaria.estado == {{config('enums.estados_transferencias.rechazado.valor')}}">
                        <div class="alert alert-danger text-center" role="alert">
                            Transferencia rechazada
                        </div>
                    </div>

                    <div class="row pt-3" v-if="transferenciaPrimaria.validada">
                        <div class="alert alert-success text-center" role="alert">
                            Transferencia validada
                        </div>
                    </div>

                    <section class="section" v-if="filtros.anio && filtros.seccion && filtros.serie">
                        <div class="card">
                            <div class="card-body pt-4">
                                <div class="row">
                                    <div class="col-12 table-responsive">
                                        <table class="table table-sm table-striped table-hover">
                                            <thead class="table-light">
                                            <tr>
                                                <th style="min-width: 250px; max-width: 250px;">No. Expediente</th>
                                                <th>No. Fojas</th>
                                                <th>Titulo y descripción</th>
                                                <th>Fecha Inicio</th>
                                                <th>Fecha Final</th>
                                                <th>Observaciones</th>
                                                <th></th>
                                            </tr>
                                            </thead>
                                            <tbody v-if="transferenciaPrimaria.id">
                                            <tr v-for="(expediente,indexExpediente) of transferenciaPrimariaDetalles.data">
                                                <td>@{{ expediente.no_expediente_legajo }}</td>
                                                <td>@{{ expediente.no_fojas ?? '-' }}</td>
                                                <td>@{{ expediente.descripcion ?? expediente.inventario_documental_detalle.descripcion }}</td>
                                                <td>@{{ expediente.fecha_inicio ? this.dayjs(expediente.fecha_inicio).format('DD/MM/YYYY') : this.dayjs(expediente.inventario_documental_detalle.fecha_inicio).format('DD/MM/YYYY') }}</td>
                                                <td>@{{ expediente.fecha_final ? this.dayjs(expediente.fecha_final).format('DD/MM/YYYY') : expediente.inventario_documental_detalle.fecha_final ? this.dayjs(expediente.inventario_documental_detalle.fecha_final).format('DD/MM/YYYY') : this.dayjs(expediente.fecha_inicio ?? expediente.inventario_documental_detalle.fecha_inicio).format('YYYY') }}</td>
                                                <td>@{{ expediente.observaciones ?? expediente.inventario_documental_detalle.observaciones }}</td>
                                                <td>
                                                    <button type="button" v-if="departamento_id == filtros.departamento_id  && transferenciaPrimaria.editable"  title="Editar" @click="verEditarExpediente(expediente.id)" class="btn m-1 btn-sm btn-outline-primary border-0"> <i class="bi-pencil"></i></button>

                                                    <button type="button" v-if="transferenciaPrimaria.validada" title="Descargar portadilla" @click="descargarPortadilla(expediente.id)" class="btn m-1 btn-sm btn-outline-success border-0"> <i class="bi bi-file-earmark-medical"></i></button>

                                                    <button type="button" v-if="departamento_id == filtros.departamento_id && transferenciaPrimaria.validada" title="Observaciones portada" @click="verEditarPortada(expediente.id)" class="btn m-1 btn-sm btn-outline-secondary border-0"> <i class="bi bi-pencil-square"></i></button>
                                                </td>
                                            </tr>
                                            </tbody>
                                            <tbody v-else>
                                                <tr>
                                                    <td colspan="7">
                                                        <div class="alert alert-warning text-center" role="alert">
                                                            Sin registros
                                                        </div>
                                                    </td>
                                                </tr>
                                            </tbody>
                                        </table>
                                    </div>
                                    <Paginator :paginate="transferenciaPrimariaDetalles" @listar-registros="moveToPage"></Paginator>
                                </div>
                            </div>
                        </div>
                    </section>
                </div>
                <div class="card-body" v-else>
                    <div class="alert alert-warning" role="alert">
                        Aun no se ha registrado el CADIDO correspondiente a la serie seleccionada, intenta mas tarde.
                    </div>
                </div>
            </div>


        <!--region Modal-->
        <div class="modal fade" id="wEditarExpediente" tabindex="-1" role="dialog" aria-hidden="true" data-bs-backdrop="static" data-bs-keyboard="false">
            <div class="modal-dialog modal-lg" role="document">
                <div class="modal-content">
                    <form action="#" method="post" @submit.prevent="guardarExpediente()">
                        <div class="modal-header">
                            <h5 class="modal-title">Editar expediente</h5>
                            <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="cancelar"></button>

                        </div>
                        <div class="modal-body">

                            <div class="row g-3">
                                <div class="col-sm-8">
                                    <div class="form-floating">
                                        <input type="text" class="form-control"
                                               :value="expedientes[0]?.inventario_documental_detalle?.no_expediente"
                                               disabled
                                               id="expediente_no_expediente_origen" placeholder="No. Expediente">

                                        <label for="expediente_no_expediente_origen">No. Expediente</label>
                                    </div>
                                </div>
                                <div class="col-sm-4">
                                    <button @click="agregarLegajo()" type="button" class="btn btn-teal w-100 h-100"><i class="bi bi-plus"></i>Agregar Legajo</button>
                                </div>
                                <div v-for="(expediente,index) of expedientes" class="col-12 mt-3">
                                    <hr>
                                    <div class="row">
                                        <div v-if="expedientes.length > 1" class="col-sm-6 pt-2">
                                            <div class="form-floating">
                                                <input type="text" class="form-control form-control-sm"
                                                       v-model="expediente.no_legajo"
                                                       max="10"
                                                       min="0"
                                                       disabled
                                                       :id="'expediente_no_legajo_'+index">

                                                <label :for="'expediente_no_legajo_'+index">No. Legajo</label>
                                            </div>
                                        </div>

                                        <div class="col-sm-6 pt-2">
                                            <div class="form-floating">
                                                <input type="text" class="form-control form-control-sm"
                                                       v-model="expediente.no_fojas"
                                                       max="9999"
                                                       min="0"
                                                       required
                                                       :id="'expediente_no_fojas_'+index">
                                                <label :for="'expediente_no_fojas_'+index">No. Fojas</label>
                                            </div>
                                        </div>
                                    </div>
                                    <div class="row" v-if="expediente.no_legajo">
                                        <div class="col-sm-12 pt-2">
                                            <div class="form-floating">
                                                <textarea class="form-control form-control-sm"
                                                          v-model="expediente.descripcion"
                                                          :id="'expediente_descripcion_'+index">
                                                </textarea>

                                            <label :for="'expediente_descripcion_'+index">Titulo y Descripción</label>
                                            </div>
                                        </div>

                                        <div class="col-sm-6 pt-2">
                                            <div class="form-floating">
                                                <input type="date" class="form-control form-control-sm"
                                                       v-model="expediente.fecha_inicio"
                                                       :required="expediente.no_legajo"
                                                       :id="'expediente_fecha_inicio_'+index">
                                                <label :for="'expediente_fecha_inicio_'+index">Fecha Inicio</label>
                                            </div>
                                        </div>

                                        <div class="col-sm-6 pt-2">
                                            <div class="form-floating">
                                                <input type="date" class="form-control form-control-sm"
                                                       v-model="expediente.fecha_final"
                                                       :required="expediente.no_legajo"
                                                       :id="'expediente_fecha_final_'+index">
                                                <label :for="'expediente_fecha_final_'+index">Fecha Final</label>
                                            </div>
                                        </div>

                                        <div class="col-sm-12 pt-2">
                                            <div class="form-floating">
                                                <textarea class="form-control form-control-sm"
                                                          v-model="expediente.observaciones"
                                                          :id="'expediente_observaciones_'+index">
                                                </textarea>

                                                <label :for="'expediente_observaciones_'+index">Observaciones</label>
                                            </div>
                                        </div>
                                    </div>
                                    <div class="row" v-else>
                                        <div class="col-sm-12 pt-2">
                                            <div class="form-floating">
                                                <textarea class="form-control form-control-sm"
                                                          :value="expediente.inventario_documental_detalle.descripcion"
                                                          :id="'expediente_descripcion_'+index" disabled>
                                                </textarea>

                                            <label :for="'expediente_descripcion_'+index">Titulo y Descripción</label>
                                            </div>
                                        </div>

                                        <div class="col-sm-6 pt-2">
                                            <div class="form-floating">
                                                <input type="date" class="form-control form-control-sm"
                                                       :value="expediente.inventario_documental_detalle.fecha_inicio"
                                                       :required="expediente.no_legajo"
                                                       :id="'expediente_fecha_inicio_'+index" disabled>
                                                <label :for="'expediente_fecha_inicio_'+index">Fecha Inicio</label>
                                            </div>
                                        </div>

                                        <div class="col-sm-6 pt-2">
                                            <div class="form-floating">
                                                <input type="date" class="form-control form-control-sm"
                                                       :value="expediente.inventario_documental_detalle.fecha_final"
                                                       :required="expediente.no_legajo"
                                                       :id="'expediente_fecha_final_'+index" disabled>
                                                <label :for="'expediente_fecha_final_'+index">Fecha Final</label>
                                            </div>
                                        </div>

                                        <div class="col-sm-12 pt-2">
                                            <div class="form-floating">
                                                <textarea class="form-control form-control-sm"
                                                          :value="expediente.inventario_documental_detalle.observaciones"
                                                          :id="'expediente_observaciones_'+index" disabled>
                                                </textarea>

                                                <label :for="'expediente_observaciones_'+index">Observaciones</label>
                                            </div>
                                        </div>
                                    </div>

                                    <div v-if="expedientes.length > 1 && expedientes.length == index+1" class="col-12 pt-2">
                                        <button @click="eliminarLegajo(index)" type="button" class="btn btn-danger float-end"><i class="bi bi-plus"></i>Eliminar ultimo</button>
                                    </div>

                                </div>


                            </div>

                        </div>
                        <div class="modal-footer">
                            <button type="button" class="btn btn-secondary" data-bs-dismiss="modal">Cancelar</button>
                            <button type="submit" class="btn btn-teal">Guardar</button>
                        </div>
                    </form>
                </div>
            </div>
        </div>
        <!--endregion Modal-->

        <!--region Modal-->
        <div class="modal fade" id="wEditarTransferencia" tabindex="-1" role="dialog" aria-hidden="true" data-bs-backdrop="static" data-bs-keyboard="false">
            <div class="modal-dialog modal-lg" role="document">
                <div class="modal-content">
                    <form action="#" method="post" @submit.prevent="guardarTransferencia()">
                        <div class="modal-header">
                            <h5 class="modal-title">Editar Transferencia</h5>
                            <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="cancelar"></button>

                        </div>
                        <div class="modal-body">

                            <div class="row g-3">
                                <div class="col-sm-4">
                                    <div class="form-floating">
                                        <input type="date" class="form-control"
                                               v-model="transferenciaPrimaria.fecha_entrega"
                                               id="transferencia_fecha_entrega" placeholder="Fecha entrega">
                                        <label for="transferencia_fecha_entrega">Fecha entrega</label>
                                    </div>
                                </div>
                                <div class="col-sm-4">
                                    <div class="form-floating">
                                        <input type="text" class="form-control"
                                               v-model="transferenciaPrimaria.no_oficio_transferencia"
                                               id="transferencia_no_oficio_transferencia" placeholder="No. Oficio">
                                        <label for="transferencia_no_oficio_transferencia">No. Oficio</label>
                                    </div>
                                </div>
                                <div class="col-sm-4">
                                    <div class="form-floating">
                                        <input type="number" class="form-control"
                                               v-model="transferenciaPrimaria.no_caja"
                                               id="transferencia_no_caja" placeholder="No. Caja">
                                        <label for="transferencia_no_caja">No. Caja</label>
                                    </div>
                                </div>

                            </div>

                        </div>
                        <div class="modal-footer">
                            <button type="button" class="btn btn-secondary" data-bs-dismiss="modal">Cancelar</button>
                            <button type="submit" class="btn btn-teal">Guardar</button>
                        </div>
                    </form>
                </div>
            </div>
        </div>
        <!--endregion Modal-->

        <!--region Modal-->
        <div class="modal fade" id="wEditarPortada" tabindex="-1" role="dialog" aria-hidden="true" data-bs-backdrop="static" data-bs-keyboard="false">
            <div class="modal-dialog modal-lg" role="document">
                <div class="modal-content">
                    <form action="#" method="post" @submit.prevent="guardarPortada()">
                        <div class="modal-header">
                            <h5 class="modal-title">Portada</h5>
                            <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="cancelar"></button>

                        </div>
                        <div class="modal-body">

                            <div class="row g-3">
                                <div class="col-12">
                                    <div class="form-floating">
                                        <textarea class="form-control"
                                                  v-model="transferenciaDetalleEditar.portada_observaciones"
                                                  id="portada_observaciones" placeholder="Observaciones">
                                        </textarea>
                                        <label for="portada_observaciones">Observaciones</label>
                                    </div>
                                </div>

                            </div>

                        </div>
                        <div class="modal-footer">
                            <button type="button" class="btn btn-secondary" data-bs-dismiss="modal">Cancelar</button>
                            <button type="submit" class="btn btn-teal">Guardar</button>
                        </div>
                    </form>
                </div>
            </div>
        </div>
        <!--endregion Modal-->

        <div class="modal fade" id="wFirmas" tabindex="-1" role="dialog" aria-hidden="true">
            <div class="modal-dialog modal-lg" role="document">
                <div class="modal-content">
                    <form id="firmasFrm" action="#" method="post" @submit.prevent="guardarFirmas()">
                        <div class="modal-header">
                            <h5 class="modal-title">Firmas Transferencia primaria</h5>
                            <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Cerrar"></button>

                        </div>
                        <div class="modal-body">
                            <div class="row">
                                <p class="mt-3 mb-0">Elaboró</p>
                                <div class="col-12 col-md-6">
                                    <input v-model="firmas.elaboro[0].nombre" class="form-control" type="text" id="firmas_elaboro_nombre" placeholder="Nombre">
                                </div>
                                <div class="col-12 col-md-6">
                                    <input v-model="firmas.elaboro[0].cargo" class="form-control" type="text" id="firmas_elaboro_cargo" placeholder="Cargo" :required="firmas.elaboro[0].nombre != null && firmas.elaboro[0].nombre.trim() != ''">
                                </div>
                                <p class="mt-3 mb-0">Revisó</p>
                                <div class="col-12 col-md-6">
                                    <input v-model="firmas.reviso[0].nombre" class="form-control" type="text" id="firmas_reviso_0_nombre" placeholder="Nombre">
                                </div>
                                <div class="col-12 col-md-6">
                                    <input v-model="firmas.reviso[0].cargo" class="form-control" type="text" id="firmas_reviso_0_cargo" placeholder="Cargo" :required="firmas.reviso[0].nombre != null && firmas.reviso[0].nombre.trim() != ''">
                                </div>
                                <div class="col-12 col-md-6 mt-1">
                                    <input v-model="firmas.reviso[1].nombre" class="form-control" type="text" id="firmas_reviso_1_nombre" placeholder="Nombre">
                                </div>
                                <div class="col-12 col-md-6 mt-1">
                                    <input v-model="firmas.reviso[1].cargo" class="form-control" type="text" id="firmas_reviso_1_cargo" placeholder="Cargo" :required="firmas.reviso[1].nombre != null && firmas.reviso[1].nombre.trim() != ''">
                                </div>
                                <div class="col-12 col-md-6 mt-1">
                                    <input v-model="firmas.reviso[2].nombre" class="form-control" type="text" id="firmas_reviso_2_nombre" placeholder="Nombre">
                                </div>
                                <div class="col-12 col-md-6 mt-1">
                                    <input v-model="firmas.reviso[2].cargo" class="form-control" type="text" id="firmas_reviso_2_cargo" placeholder="Cargo" :required="firmas.reviso[2].nombre != null && firmas.reviso[2].nombre.trim() != ''">
                                </div>
                                <p class="mt-3 mb-0">Recibió</p>
                                <div class="col-12 col-md-6">
                                    <input v-model="firmas.recibio[0].nombre" class="form-control" type="text" id="firmas_recibio_nombre" placeholder="Nombre">
                                </div>
                                <div class="col-12 col-md-6">
                                    <input v-model="firmas.recibio[0].cargo" class="form-control" type="text" id="firmas_recibio_cargo" placeholder="Cargo" :required="firmas.recibio[0].nombre != null && firmas.recibio[0].nombre.trim() != ''">
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


        <div class="modal fade" id="wgenerarTransferenciaParcial" data-bs-backdrop="static" data-bs-keyboard="false" tabindex="-1" role="dialog" aria-hidden="true">
            <div class="modal-dialog modal-lg" role="document">
                <div class="modal-content">
                    <form id="generarTransferenciaParcialFrm" action="#" method="post" @submit.prevent="generarTransferenciaParcial()">
                        <div class="modal-header">
                            <h5 class="modal-title">Generar Transferencia primaria</h5>
                            <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Cerrar"></button>

                        </div>
                        <div class="modal-body">
                            <div class="row">
                                <div class="col-8 col-md-9 col-lg-10">
                                    <div class="form-floating">
                                        <input type="text" class="form-control"
                                               v-model="noExpedienteBusqueda"
                                               id="buscar_transferencia_no_expediente" placeholder="Buscar expediente">
                                        <label for="buscar_transferencia_no_expediente">Buscar expediente</label>
                                    </div>
                                </div>
                                <div class="col-4 col-md-3 col-lg-2">
                                    <button @click="buscarExpedientes()" type="button" class="btn btn-primary"><i class="bi bi-search"></i> Buscar</button>
                                </div>
                                <button type="submit" disabled hidden aria-hidden="true"></button>
                                <div class="col-12 table-responsive pt-3">
                                        <table class="table table-sm table-striped table-hover">
                                            <thead class="table-light">
                                            <tr>
                                                <th style="min-width: 250px; max-width: 250px;">No. Expediente</th>
                                                <th>Titulo y descripción</th>
                                                <th>Fecha Inicio</th>
                                                <th>Fecha Final</th>
                                                <th>Observaciones</th>
                                                <th></th>
                                            </tr>
                                            </thead>
                                            <tbody v-if="expedientesBusqueda.data.length > 0">
                                            <tr v-for="(expediente,indexExpediente) of expedientesBusqueda.data">
                                                <td>@{{ expediente.no_expediente }}</td>
                                                <td>@{{ expediente.descripcion}}</td>
                                                <td>@{{ this.dayjs(expediente.fecha_inicio).format('DD/MM/YYYY') }}</td>
                                                <td>@{{ expediente.fecha_final ? this.dayjs(expediente.fecha_final).format('DD/MM/YYYY') : this.dayjs(expediente.fecha_inicio).format('YYYY') }}</td>
                                                <td>@{{ expediente.observaciones }}</td>
                                                <td>
                                                    <button type="button" title="Agregar" @click="agregarExpedienteTransferencia(expediente)" class="btn btn-sm btn-success"><i class="bi bi-plus"></i></button>
                                                </td>
                                            </tr>
                                            </tbody>
                                            <tbody v-else>
                                            <tr>
                                                <td colspan="7">
                                                    <div class="alert alert-warning text-center" role="alert">
                                                        Sin resultados
                                                    </div>
                                                </td>
                                            </tr>
                                            </tbody>
                                        </table>
                                </div>
                                <Paginator :paginate="expedientesBusqueda" @listar-registros="buscarExpedientes"></Paginator>

                                <h6 class="pt-3">Expedientes Seleccionados</h6>

                                <div class="col-12 table-responsive">
                                    <table class="table table-sm table-striped table-hover">
                                        <thead class="table-light">
                                        <tr>
                                            <th style="min-width: 250px; max-width: 250px;">No. Expediente</th>
                                            <th>Titulo y descripción</th>
                                            <th>Fecha Inicio</th>
                                            <th>Fecha Final</th>
                                            <th>Observaciones</th>
                                            <th></th>
                                        </tr>
                                        </thead>
                                        <tbody v-if="expedientesNuevaTransferencia.length > 0">
                                        <tr v-for="(expediente,indexExpediente) of expedientesNuevaTransferencia">
                                            <td>@{{ expediente.no_expediente }}</td>
                                            <td>@{{ expediente.descripcion}}</td>
                                            <td>@{{ this.dayjs(expediente.fecha_inicio).format('DD/MM/YYYY') }}</td>
                                            <td>@{{ expediente.fecha_final ? this.dayjs(expediente.fecha_final).format('DD/MM/YYYY') : this.dayjs(expediente.fecha_inicio).format('YYYY') }}</td>
                                            <td>@{{ expediente.observaciones }}</td>
                                            <td>
                                                <button type="button" title="Quitar" @click="eliminarExpedienteTransferencia(expediente.id)" class="btn m-1 btn-sm btn-outline-danger border-0"><i class="bi bi-trash"></i></button>
                                            </td>
                                        </tr>
                                        </tbody>
                                        <tbody v-else>
                                        <tr>
                                            <td colspan="7">
                                                <div class="alert alert-warning text-center" role="alert">
                                                    Sin selecciones
                                                </div>
                                            </td>
                                        </tr>
                                        </tbody>
                                    </table>
                                </div>

                            </div>

                        </div>
                        <div class="modal-footer">
                            <button type="button" class="btn btn-secondary" data-bs-dismiss="modal">Cerrar</button>
                            <button type="submit" class="btn btn-teal">Generar</button>
                        </div>
                    </form>
                </div>
            </div>
        </div>


    </main>

@endsection
