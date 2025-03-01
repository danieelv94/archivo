@extends('layouts.master', ['title' => 'Inventario documental'])

@section('styles')

@endsection

@section('scripts')
    @include('layouts.includes.jquery-js-base')
{{--    <script type="text/javascript" src="{{ config('app.env') != 'production' ? asset('vendor/vue/vue.js') : asset('vendor/vue/vue.min.js') }}"></script>--}}


    <script type="module" defer>
        import { IPaginate } from "{{ asset('js/types/IPaginate.js') }}";
        import Paginator from "{{ asset('js/components/Paginator.js') }}";


        const NW = {
            isNew: true,
            id: null,
            ubicacion_fisica: '',
            ubicacion_topografica: '',
            no_expediente: '',
            descripcion: '',
            fecha_inicio: '',
            fecha_final: '',
            observaciones: '',
            uuid: null,

        };

        const UBICACION = {
            isNew: true,
            id: null,
            ubicacion: '',
            bien_mueble: '',
            no_inventario: '',
            departamentos_id: {{ auth()->user()->persona->departamento->id }},

        };

        const _MASTER_ = {
            isNew: true,
            id: null,
            anio_captura: null,
            mes_captura: null,
            clave_seccion: null,
            clave_serie: null,
            area_id: null,
            departamento_id: null,
            persona_autoriza_id: null,
            persona_responsable_id: null,
            unidad_presupuestal: null,
            seccion: null,
            serie: null,
            fecha_cierre_captura: null,
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
                }
            ],
        };

        const app = new Vue({
            el: '#main',
            components: {
                Paginator
            },
            data: {
                dayjs: dayjs,
                secciones: [],
                anios_captura: {{ Js::from( $anios_captura ) }},
                meses_captura: [],
                capturaMaster: {..._MASTER_},
                inventarioActualDetalle: new IPaginate(),
                filaSeleccionada: {...NW},
                series: [],
                fecha_limite: null,
                mes_cerrado: false,
                area: {{ auth()->user()->persona->area->id }},
                departamento: {{ auth()->user()->persona->departamento->id }},
                noExpedientePreview: null,
                wModalShowing: null,
                wModalShowingId: null,
                ubicaciones: {},
                ubicacionSeleccionada: {...UBICACION},
                firmas: @if($firmas) {{Js::from($firmas)  }} @else JSON.parse(JSON.stringify(FIRMAS)) @endif,
            },

            created(){},
            beforeMount(){},
            computed: {
            },
            mounted(){
                this.getSeccionesAutorizadas(this.area,this.departamento);
                this.getUbicaciones();
            },
            methods: {
                validarNoExpediente(expediente){
                    let regExpediente = /CCLEH-{1}[0-9]{2}\*[0-9]{1,2}[S,C]\.[0-9]{1,2}\/[0-9]{1,5}\|?\-[0-9]{4}|CCLEH-{1}[0-9]{1}\.?[0-9]{1,2}\*[0-9]{1,2}[S,C]\.[0-9]{1,2}\/[0-9]{1,5}\|?\-[0-9]{4}/gi;
                    if( !regExpediente.test(expediente) ){
                        toastr.error(`El No. Expediente es incorrecto. Debes seguir el formato asignado para este campo.`, '', {delay: 3000});
                        $("#expediente").addClass("input-error");
                        setTimeout(function(){
                            $("#expediente").removeClass("input-error");
                        },3000);
                        return false;
                    }
                    return true;
                },

                setCapturaMaster( value ) {
                    let selectedSeccion = this.capturaMaster.seccion;
                    let selectedSerie = this.capturaMaster.serie;
                    let selectedAnio = this.capturaMaster.anio_captura;
                    let selectedMes = this.capturaMaster.mes_captura;
                    this.capturaMaster = value;
                    if ( ! value ) {
                        this.capturaMaster = {..._MASTER_};
                        this.capturaMaster.anio_captura = selectedAnio;
                        this.capturaMaster.mes_captura = selectedMes;
                    }
                    this.capturaMaster.seccion = selectedSeccion;
                    this.capturaMaster.serie = selectedSerie;
                },

                /**
                 * Al elegir el año, buscar los meses disponibles para captura
                 */
                getMesesDisponibles(){
                    $.ajax({
                            headers: {'X-CSRF-TOKEN': $('meta[name="csrf-token"]').attr('content')},
                            url: `{{ url('ccleh/inventario/captura/buscar-meses-disponibles') }}/${ this.capturaMaster.anio_captura }`,
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
                                    this.capturaMaster.mes_captura = null;
                                }
                        }
                    });
                },

                /**
                 * Al elegir el mes, buscar la fecha limite de captura
                 */
                getFechaLimite(event){
                    $.ajax({
                            headers: {'X-CSRF-TOKEN': $('meta[name="csrf-token"]').attr('content')},
                            url: `{{ url('ccleh/inventario/captura/buscar-fecha-limite') }}/${this.capturaMaster.anio_captura}/${ event.target.value }`,
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
                                                }
                                            } finally {
                                        }
                                    }
                                }
                        })
                        .done(e => {
                            if ( e && e.hasOwnProperty('success') && e.success) {
                                if ( e.hasOwnProperty('cierre')) {
                                    this.fecha_limite = e.cierre.fecha_limite;
                                    this.mes_cerrado = e.cierre.cerrado;
                                }
                        }
                    });
                },

                /**
                 * Solicitar que se busque en la BD, los datos capturados con los filtros de:
                 * año de captura, mes de captura, sección, serie
                 * En el back se determina la unidad administrativa a la que pertenece el usuario actual
                 */
                buscarCaptura(){
                    this.cancelarActualizar();
                    const {
                        anio_captura,
                        mes_captura,
                        seccion,
                        serie,
                    } =  this.capturaMaster;

                    if ((!serie) || (!anio_captura) || (!mes_captura)) {
                        this.inventarioActualDetalle = new IPaginate();
                        return;

                    }
                    this.inventarioActualDetalle = new IPaginate();
                    $.ajax({
                            url: `{{ url('ccleh/inventario/captura/buscar-inventario') }}/${anio_captura}/${mes_captura}/${seccion}/${serie}`,
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
                                                }
                                            } finally {
                                        }
                                    }
                                }
                        })
                        .done(e => {
                            if ( e && e.hasOwnProperty('success') && e.success) {
                                if ( e.hasOwnProperty('inventarioDocumental') ) {
                                    this.setCapturaMaster(e.inventarioDocumental);
                                    if( this.capturaMaster.id ){
                                        this.moveToPage();
                                    }
                                }
                                this.generarNoExpediente();
                            }
                    });
                },

                async moveToPage(url = '') {
                    let page = '';
                    if (url.length) {
                        page = (new URL(url).searchParams).get('page');
                    } else {
                        page = this.inventarioActualDetalle.current_page
                    }

                    $.ajax({
                        url: `{{ route('ccleh.inventario.captura.buscar.inventario.detalles') }}`,
                        type: 'post',
                        data: {page, inventario_id: this.capturaMaster.id},
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
                                if ( e.hasOwnProperty('inventario_detalle')) {
                                    this.inventarioActualDetalle = e.inventario_detalle;
                                }
                            }
                        });

                },

                /**
                 * Guardar datos maestros, fondo, unidad administrativa, nombre responsible, fecha de captura,sección, serie
                 * @param {boolean} showMessageSuccess si es false no muestra mensaje de guardado
                 * @returns {*}
                 */
                guardarMaestro( showMessageSuccess = true ){
                    let data = {...this.capturaMaster};
                    return $.ajax({
                            headers: {'X-CSRF-TOKEN': $('meta[name="csrf-token"]').attr('content')},
                            url: `{{ route('ccleh.inventario.captura.store.inventario') }}`,
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
                                            toastr.error(`Ocurrió un error y no se guardó la información <ul className="m-0"><li>${msgs.join('</li><li>')}</li></ul>`, '', {delay: 10000});
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
                                if ( showMessageSuccess ) {
                                    toastr.success('Guardado');
                                }
                                if ( e.hasOwnProperty('inventario')) {
                                    this.setCapturaMaster(e.inventario);
                                }
                            }
                    });
                },

                duplicarMaestro(){
                    const data = {
                        anio_captura: this.capturaMaster.anio_captura,
                        mes_captura: this.capturaMaster.mes_captura,
                        seccion: this.capturaMaster.seccion,
                        serie: this.capturaMaster.serie,
                        id: this.capturaMaster.id ? this.capturaMaster.id : null
                    };

                    return $.ajax({
                            headers: {'X-CSRF-TOKEN': $('meta[name="csrf-token"]').attr('content')},
                            url: `{{ route('ccleh.inventario.captura.duplicate.inventario') }}`,
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
                                            toastr.error(`Ocurrió un error y no se guardó la información <ul className="m-0"><li>${msgs.join('</li><li>')}</li></ul>`, '', {delay: 10000});
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
                                if ( e.hasOwnProperty('newInventarioDocumental')) {
                                    this.setCapturaMaster(e.newInventarioDocumental);
                                    this.buscarCaptura();
                                    toastr.success('Guardado');
                                }
                            }
                    });
                },

                guardarDetalle() {
                    if ( (! this.capturaMaster.hasOwnProperty('id')) || (!this.capturaMaster.id) ) {
                        cclehAlert({
                            title: 'Error',
                            text: 'No se permite guardar, code 5'
                        });

                        return;
                    }
                    const item = {...this.filaSeleccionada};

                    $.ajax({
                            headers: {'X-CSRF-TOKEN': $('meta[name="csrf-token"]').attr('content')},
                            url: `{{ url('ccleh/inventario/captura/agregar-detalle/') }}/${ this.capturaMaster.id }`,
                            type: 'post',
                            data: item,
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
                                this.filaSeleccionada = {...NW};
                                this.establecerInputExpediente();
                                this.generarNoExpediente();
                                toastr.success('Guardado');
                                this.buscarCaptura();
                        }
                    });
                },


                /**
                 * Guarda el registro, el dato maestro y el detalle
                 */
                guardarRegistro() {

                    if( !this.validarNoExpediente( this.filaSeleccionada.no_expediente ) ){
                        return;
                    }
                    // comprobar si no existe el dato maestro, mandar a guardar
                    if ( (! this.capturaMaster.hasOwnProperty('id')) || (!this.capturaMaster.id) ) {
                        this.guardarMaestro(false).done( () => {
                            this.guardarDetalle();
                        });
                    } else {
                        this.guardarDetalle();
                    }

                },

                /**
                 * Responde a la acción Editar del menú de opciones
                 * @param id
                 */
                editarFila(id,editable){
                    if(editable === false){
                        document.getElementById('emailInput').disabled = true;
                        document.getElementById('emailInput').disabled = true;
                        document.getElementById('emailInput').disabled = true;
                    }
                    this.filaSeleccionada = {...this.inventarioActualDetalle.data.find( a => Number(a.id) === Number(id) )};
                    this.establecerInputExpediente(this.filaSeleccionada.no_expediente);
                    this.generarNoExpediente();
                },


                /**
                 * Responde a la acción Eliminar del menú de opciones
                 * @param id
                 */
                eliminarFila(id){
                    cclehConfirm.fire({
                        text: '¿Estas seguro de eliminar?'
                    }).then( (r) => {
                        if ( r.isConfirmed ) {
                            $.ajax({
                                    headers: {'X-CSRF-TOKEN': $('meta[name="csrf-token"]').attr('content')},
                                    url: `{{ url('ccleh/inventario/captura/eliminar-detalle') }}/${ id }`,
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
                                                        }
                                                    } finally {
                                                }
                                            }
                                        }
                                })
                                .done(e => {
                                    if ( e && e.hasOwnProperty('success') && e.success) {
                                        toastr.success('Eliminado');
                                        this.buscarCaptura();
                                }
                            });

                        }
                    });
                },


                exportarInventario(){
                    if ( this.capturaMaster && this.capturaMaster.id ) {
                        window.open(`{{ url('ccleh/inventario/reporte/exportar-inventario-doc/') }}/${ this.capturaMaster.id }`);
                    }
                },

                exportarInventarioSabana(){
                    if ( this.capturaMaster && this.capturaMaster.id ) {
                        window.open(`{{ url('ccleh/inventario/reporte/exportar-sabana-inventario-doc/') }}/${ this.capturaMaster.id }`);
                    }
                },


                cerrarCaptura(){
                    const data = {
                        anio_captura: this.capturaMaster.anio_captura,
                        mes_captura: this.capturaMaster.mes_captura,
                        seccion: this.capturaMaster.seccion,
                        serie: this.capturaMaster.serie
                    };
                    if ( this.capturaMaster) {
                        cclehConfirm.fire({
                            title: 'Confirmar cerrar captura',
                            text: '¿Estas seguro de cerrar la captura?, no podrás agregar más registros'
                        }).then( (r) => {
                            if ( r.isConfirmed ) {
                                $.ajax({
                                        headers: {'X-CSRF-TOKEN': $('meta[name="csrf-token"]').attr('content')},
                                        url: this.capturaMaster.id ? `{{ url('ccleh/inventario/captura/cerrar-inventario') }}/${ this.capturaMaster.id }` : `{{ url('ccleh/inventario/captura/cerrar-inventario') }}`,
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
                                                }
                                            }
                                        }
                                    })
                                    .done(e => {
                                        if ( e && e.hasOwnProperty('success') && e.success) {
                                            toastr.success('Inventario Cerrado.');
                                        }
                                        if ( e.hasOwnProperty('inventario')) {
                                            this.setCapturaMaster(e.inventario);
                                        }
                                });
                            }
                        });
                    }
                },


                getSeccionesAutorizadas(area,departamento){
                    let repetidas = false;
                    $.ajax({
                            headers: {'X-CSRF-TOKEN': $('meta[name="csrf-token"]').attr('content')},
                            url: `{{ url('ccleh/inventario/captura/get-series-autorizadas') }}/${area}/${departamento}/${repetidas}`,
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
                                                }
                                            } finally {
                                        }
                                    }
                                }
                        })
                        .done(e => {
                            if ( e && e.hasOwnProperty('success') && e.success) {
                                if ( e.hasOwnProperty('series')) {
                                    this.secciones  = e.series;
                                }
                        }
                    });
                },

                getSeriesAutorizadas(area,departamento){
                    let repetidas = "si";
                    $.ajax({
                            headers: {'X-CSRF-TOKEN': $('meta[name="csrf-token"]').attr('content')},
                            url: `{{ url('ccleh/inventario/captura/get-series-autorizadas') }}/${area}/${departamento}/${repetidas}/${this.capturaMaster.seccion}`,
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
                                    this.capturaMaster.serie = null;
                                }
                        }
                    });
                },

                verImportar(){
                    this.wModalShowing = new bootstrap.Modal(document.getElementById('wImportar'));
                    this.wModalShowingId = 'wImportar';
                    this.wModalShowing.show();
                    document.getElementById('importarInventarioFrm').reset();

                },


                importarInventario(){
                    // comprobar si no existe el dato maestro, mandar a guardar
                    if ( (! this.capturaMaster.hasOwnProperty('id')) || (!this.capturaMaster.id) ) {
                        this.guardarMaestro(false).done( () => {
                            this.importarExcel();
                        });
                    } else {
                        this.importarExcel();
                    }
                },

                importarExcel(){

                    this.cancelarActualizar();
                    const {
                        anio_captura,
                        mes_captura,
                        seccion,
                        serie,
                    } =  this.capturaMaster;

                    const data = new FormData(document.getElementById('importarInventarioFrm'));

                    if ((!serie) || (!anio_captura) || (!mes_captura)) {
                        this.inventarioActualDetalle = new IPaginate();
                        return;

                    }
                    $.ajax({
                        headers: {'X-CSRF-TOKEN': $('meta[name="csrf-token"]').attr('content')},
                        url: `{{ url('ccleh/inventario/captura/importar-inventario') }}/${anio_captura}/${mes_captura}/${seccion}/${serie}`,
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
                                this.buscarCaptura();
                                this.wModalShowing.hide();
                                document.getElementById('importarInventarioFrm').reset();
                            }
                        });
                },

                vaciarInventario(){
                    cclehConfirm.fire({
                        title: '¿Estas seguro de vaciar este inventario?',
                        text: 'Serán eliminados todos los registros que este contenga.'
                    }).then( (r) => {
                        if ( r.isConfirmed ) {
                            $.ajax({
                                headers: {'X-CSRF-TOKEN': $('meta[name="csrf-token"]').attr('content')},
                                url: `{{ url('ccleh/inventario/captura/vaciar-inventario') }}/${ this.capturaMaster.id }`,
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
                                            }
                                        } finally {
                                        }
                                    }
                                }
                            })
                                .done(e => {
                                    if ( e && e.hasOwnProperty('success') && e.success) {
                                        toastr.success('Eliminado');
                                        this.buscarCaptura();
                                    }
                                });

                        }
                    });
                },

                verificarConsecutivos(){
                    $.ajax({
                        headers: {'X-CSRF-TOKEN': $('meta[name="csrf-token"]').attr('content')},
                        url: `{{ url('ccleh/inventario/captura/verificar-consecutivo') }}/${this.capturaMaster.id}`,
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
                                    }
                                } finally {
                                }
                            }
                        }
                    })
                        .done(e => {
                            if ( e && e.hasOwnProperty('success') && e.success) {
                                toastr.success('Registros correctos. Todos los números de expediente son consecutivos.');
                            }
                        });
                },

                cancelarActualizar(){
                    if( !this.filaSeleccionada.isNew ){
                        this.filaSeleccionada = {...NW};
                        this.establecerInputExpediente();
                        this.generarNoExpediente();
                    }

                },

                cancelarActualizacionUbicacion(){
                    this.ubicacionSeleccionada = {...UBICACION};
                },

                generarNoExpediente(){
                    let num_expediente = $('#expediente').val();
                    if( num_expediente < 9  && num_expediente.length < 2 && num_expediente !== ""){
                        num_expediente = `0${num_expediente}`;
                    }
                    let seccion = this.secciones.find( a => Number(a.seccion_id) === Number($("#seccion").val()) );
                    let serie = this.series.find( a => Number(a.serie_id) === Number($("#serie").val()) );

                    if( !seccion || !serie ){
                        return;
                    }
                    if( num_expediente == "" || typeof num_expediente == 'undefined' ){
                        num_expediente = '??';
                    }
                    this.filaSeleccionada.no_expediente = `{{ config('app.acronyms_organization') }}-{{ auth()->user()->persona->departamento->clave }}*${ seccion.seccion.clave ? seccion.seccion.clave : '--' }.${ serie.serie.clave ? serie.serie.clave : '--' }/${ num_expediente ? num_expediente : '??' }-${ this.capturaMaster.anio_captura  }`;
                    this.noExpedientePreview = `{{ config('app.acronyms_organization') }}-{{ auth()->user()->persona->departamento->clave }}*${ seccion.seccion.clave ? seccion.seccion.clave : '--' }.${ serie.serie.clave ? serie.serie.clave : '--' }/<span style="color:red;font-weight:bold;">${ num_expediente ? num_expediente : '??' }</span>-${ this.capturaMaster.anio_captura }`;

                },


                establecerInputExpediente(valor = ""){
                    if( valor != "" ){
                        valor = valor.split('/');
                        valor = valor[1].split('-');
                        valor = valor[0];
                    }
                    $('#expediente').val(valor);
                },


                getUbicaciones(){
                    $.ajax({
                        headers: {'X-CSRF-TOKEN': $('meta[name="csrf-token"]').attr('content')},
                        url: `{{ url('ccleh/inventario/captura/get-ubicaciones') }}/${this.departamento}`,
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
                                    }
                                } finally {
                                }
                            }
                        }
                    })
                        .done(e => {
                            if ( e && e.hasOwnProperty('success') && e.success) {
                                if ( e.hasOwnProperty('ubicaciones_topograficas')) {
                                    this.ubicaciones  = e.ubicaciones_topograficas;
                                }
                            }
                        });
                },


                verUbicaciones(){
                    this.ubicacionSeleccionada = {...UBICACION};
                    this.wModalShowing = new bootstrap.Modal(document.getElementById('wUbicaciones'));
                    this.wModalShowingId = 'wUbicaciones';
                    this.wModalShowing.show();
                    document.getElementById('ubicacionesFrm').reset();

                },


                eliminarUbicacion(id){
                    cclehConfirm.fire({
                        text: '¿Estas seguro de eliminar?'
                    }).then( (r) => {
                        if ( r.isConfirmed ) {
                            $.ajax({
                                headers: {'X-CSRF-TOKEN': $('meta[name="csrf-token"]').attr('content')},
                                url: `{{ url('ccleh/inventario/captura/delete-ubicacion') }}/${ id }`,
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
                                        toastr.success('Eliminado');
                                        this.getUbicaciones();
                                        this.ubicacionSeleccionada = {...UBICACION};
                                    }
                                });

                        }
                    });
                },


                editarUbicacion(id){
                    this.ubicacionSeleccionada = {...this.ubicaciones.find( a => Number(a.id) === Number(id) )};
                },


                guardarUbicacion() {
                    $.ajax({
                        headers: {'X-CSRF-TOKEN': $('meta[name="csrf-token"]').attr('content')},
                        url: `{{ url('ccleh/inventario/captura/update-ubicacion') }}`,
                        type: 'post',
                        data: this.ubicacionSeleccionada,
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
                                toastr.success('Guardado');
                                this.getUbicaciones();
                                this.ubicacionSeleccionada = {...UBICACION};

                            }
                        });
                },

                existeUbicacion(){
                    if( this.filaSeleccionada.ubicacion_topografica === '' ){
                        return false;
                    }
                    let ubicacion = this.ubicaciones.find( a => a.ubicacionCompleta === this.filaSeleccionada.ubicacion_topografica );
                    return ubicacion === undefined;
                },

                verEditarFirmas(){
                    this.wModalShowing = new bootstrap.Modal(document.getElementById('wFirmas'));
                    this.wModalShowingId = 'wFirmas';
                    this.wModalShowing.show();
                },

                guardarFirmas() {
                    $.ajax({
                        headers: {'X-CSRF-TOKEN': $('meta[name="csrf-token"]').attr('content')},
                        url: `{{ url('ccleh/firmas/guardar-inventario-documental') }}`,
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
                                this.wModalShowingId = null;
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
            <div class="row">
                <div class="col-12 col-sm-6 pagetitle" >
                    <h1>Inventario documental</h1>
                </div>
            </div>



            <div class="card">
                <div class="card-body ">

                    <div class="row pt-2">
                        <span class="offset-sm-1 col-sm-3 col-form-label text-end">Fondo: </span>
                        <div class="col-sm-7">
                            <div class="form-control-plaintext fw-bold">{{ config('app.name_organization') }}</div>
                        </div>
                    </div>

                    <div class="row">
                        <span class="offset-sm-1 col-sm-3 col-form-label text-end">Unidad Administrativa: </span>
                        <div class="col-sm-7">
                            <div class="form-control-plaintext fw-bold">{{ auth()->user()->persona->area->codigo.'. '.auth()->user()->persona->area->nombre }}</div>
                        </div>
                    </div>

                    <div class="row">
                        <span class="offset-sm-1 col-sm-3 col-form-label text-end">Área generadora: </span>
                        <div class="col-sm-7">
                            <div class="form-control-plaintext fw-bold">{{ auth()->user()->persona->departamento ? auth()->user()->persona->departamento->clave.'. '.auth()->user()->persona->departamento->nombre : 'N/A' }}</div>
                        </div>
                    </div>

                    <div class="row">
                        <span class="offset-sm-1 col-sm-3 col-form-label text-end">Nombre del responsable: </span>
                        <div class="col-sm-7">
                            <div class="form-control-plaintext fw-bold">{{ auth()->user()->persona->nombre_completo }}</div>
                        </div>
                    </div>

                    {{--Fecha del reporte--}}
                    <div class="row mb-3">

                        <span class="offset-sm-1 col-sm-3 col-form-label text-end">Fecha de reporte: </span>

                        <div class="col-sm-2">
                            <div class="form-group row">
                                <label class="col-sm-5 col-form-label text-end" for="anio_captura">Año</label>
                                <div class="col-sm-7">
                                    <select name="anio_captura"
                                            v-model="capturaMaster.anio_captura"
                                            id="anio_captura"
                                            @change="getMesesDisponibles();buscarCaptura();"
                                            class="form-select">
                                        <option selected disabled :value="null">Elige</option>
                                        <option v-for="item of anios_captura" :value="item">@{{ item }}</option>
                                    </select>
                                </div>
                            </div>
                        </div>


                        <div class="col-sm-2">
                            <div class="form-group row justify-content-end">
                                <label class="col-sm-5 col-form-label text-end" for="mes_captura">Mes</label>
                                <div class="col-sm-7">
                                    <select id="mes_captura" class="form-select"
                                            @change="buscarCaptura();getFechaLimite($event);"
                                            {{-- @change="onChangeArea($event)" --}}
                                            v-model="capturaMaster.mes_captura">
                                        <option selected :value="null" disabled>Elige</option>
                                        <option v-for="mes of meses_captura" :value="mes.numero">
                                            @{{ mes.nombre }}
                                        </option>
                                    </select>
                                </div>
                            </div>
                        </div>


                    </div>

                    <div class="row mb-3" v-if="fecha_limite != null">
                        <span class="offset-sm-1 col-sm-3 col-form-label text-end">Fecha limite de captura:</span>
                        <div class="col-sm-7">
                            <div class="form-control-plaintext fw-bold">@{{fecha_limite}}</div>
                        </div>
                    </div>

                    <div class="row mb-3" v-if="capturaMaster.anio_captura && capturaMaster.mes_captura && capturaMaster.serie">
                        <span class="offset-sm-1 col-sm-3 col-form-label text-end">Fecha de cierre del inventario:</span>
                        <div class="col-sm-7">
                            <div v-if="capturaMaster.fecha_cierre_captura" class="form-control-plaintext fw-bold">@{{capturaMaster.fecha_cierre_captura}}</div>
                            <div v-if="!capturaMaster.fecha_cierre_captura" class="form-control-plaintext fw-bold">La captura no ha sido cerrada.</div>
                        </div>
                    </div>

                    {{--Sección--}}
                    <div class="row mb-3">
                        <label class="offset-sm-1 col-sm-3 col-form-label text-end" for="seccion">Sección:</label>
                        <div class="col-sm-4">
                            <select id="seccion" class="form-select"
                                    @change="getSeriesAutorizadas(area,departamento);buscarCaptura();"
                                    v-model="capturaMaster.seccion">
                                <option selected :value="null" disabled>Elige una sección</option>
                                <option v-for="item of secciones" :value="item.seccion.id">
                                    @{{ item.seccion.clave }} @{{ item.seccion.nombre }}
                                </option>
                            </select>
                        </div>
                    </div>

                    {{--Serie--}}
                    <div class="row">

                        <label class="offset-sm-1 col-sm-3 col-form-label text-end" for="serie">Serie:</label>
                        <div class="col-sm-4">
                            <select id="serie" class="form-select"
                                    @change="buscarCaptura()"
                                    v-model="capturaMaster.serie">
                                <option selected disabled :value="null">Elige una serie</option>
                                <option v-for="item of series" :value="item.serie.id">
                                    @{{ item.serie.clave }} @{{ item.serie.nombre }}
                                </option>
                            </select>
                        </div>
                    </div>


                </div>
            </div>

            <div class="row" v-if="capturaMaster.anio_captura && capturaMaster.mes_captura && capturaMaster.serie && capturaMaster.fecha_cierre_captura && mes_cerrado != true">
                <div class="col-12">
                    <div class="alert alert-info text-center" role="alert">
                      <h5>El inventario de la serie ha sido cerrado.</h5>
                    </div>
                </div>
            </div>

            <div class="row" v-if="capturaMaster.anio_captura && capturaMaster.mes_captura && capturaMaster.serie && mes_cerrado != false">
                <div class="col-12">
                    <div class="alert alert-danger text-center" role="alert">
                      <h5>La fecha limite para de entrega ha vencido, no puedes hacer más modificaciones en este inventario.</h5>
                    </div>
                </div>
            </div>

{{--            <div class="row" v-if="capturaMaster.anio_captura && capturaMaster.mes_captura && capturaMaster.serie && mes_cerrado == 'extemporaneo' && !capturaMaster.fecha_cierre_captura">--}}
{{--                <div class="col-12">--}}
{{--                    <div class="alert alert-warning text-center" role="alert">--}}
{{--                      <h5>La fecha limite para de entrega ha vencido, pero tienes permitida captura extemporánea.</h5>--}}
{{--                    </div>--}}
{{--                </div>--}}
{{--            </div>--}}

            <section class="row">
            <div class="col">
                <div class="card">
                    <div class="card-body pt-3">

                        <div class="d-flex justify-content-end gap-3">
                            <button class="btn btn-teal"
                                    v-if="capturaMaster && capturaMaster.mes_captura && capturaMaster.serie && mes_cerrado == false && !capturaMaster.fecha_cierre_captura"
                                    @click="verImportar()"><i class="bi bi-file-earmark-arrow-up"></i>&nbsp;Importar</button>

                            <button class="btn btn-success"
                                    v-if="capturaMaster && capturaMaster.id"
                                    @click="exportarInventario()"><i class="bi bi-file-earmark-excel"></i>&nbsp;Exportar</button>

{{--                            <button class="btn btn-success"--}}
{{--                                    v-if="capturaMaster && capturaMaster.id"--}}
{{--                                    @click="exportarInventarioSabana()"><i class="bi bi-file-earmark-spreadsheet"></i>&nbsp;Exportar Sabana</button>--}}
                            <button class="btn btn-secondary"
                                    v-if="capturaMaster && capturaMaster.mes_captura && capturaMaster.serie"
                                    @click="verEditarFirmas()"><i class="bi bi-pencil-square"></i>&nbsp;Firmas</button>

                            <button class="btn btn-primary"
                                    v-if="capturaMaster && capturaMaster.mes_captura && capturaMaster.serie && mes_cerrado == false && !capturaMaster.fecha_cierre_captura"
                                    @click="duplicarMaestro()"><i class="bi bi-files"></i>&nbsp;Duplicar mes anterior</button>

                            <button class="btn btn-danger"
                                    v-if="capturaMaster && capturaMaster.mes_captura && capturaMaster.serie && mes_cerrado == false && !capturaMaster.fecha_cierre_captura && inventarioActualDetalle.data.length != 0"
                                    @click="vaciarInventario()"><i class="bi bi-trash"></i>&nbsp;Vaciar inventario</button>

                            <button class="btn btn-primary"
                                    v-if="capturaMaster && capturaMaster.mes_captura && capturaMaster.serie && mes_cerrado == false && !capturaMaster.fecha_cierre_captura && inventarioActualDetalle.data.length != 0"
                                    @click="verificarConsecutivos()"><i class="bi bi-check-square"></i>&nbsp;Verificar</button>

                            <button class="btn btn-secondary"
                                    v-if="capturaMaster.anio_captura && capturaMaster.mes_captura && capturaMaster.serie && !capturaMaster.fecha_cierre_captura && mes_cerrado == false"
                                    @click="cerrarCaptura()"><i class="bi bi-x-circle"></i>&nbsp;Cerrar captura</button>

                        </div>
                    </div>
                </div>
            </div>
        </section>

        <section class="row" v-if="(capturaMaster.fecha_cierre_captura == null || capturaMaster.isNew) && capturaMaster.serie && mes_cerrado == false && capturaMaster.mes_captura">
            <article class="col">
                <div class="card">
                    <div class="card-body">
                        <form class="row g-3" action="#" method="post" @submit.prevent="guardarRegistro()">

                            <div class="form-group pt-3 col-6 col-lg-3">
                                    <label class="form-label" for="ubicacion">Ubicación Topográfica</label>
                                <div class="d-flex">
                                    <select id="ubicacion" class="form-select" v-model="filaSeleccionada.ubicacion_topografica">
                                        <option selected value="" disabled>Elige una Ubicación...</option>
                                        <option v-for="item of ubicaciones" :value="item.ubicacionCompleta">
                                            @{{ item.ubicacionCompleta }}
                                        </option>
                                        <option v-if="existeUbicacion()" :value="filaSeleccionada.ubicacion_topografica">
                                            @{{ filaSeleccionada.ubicacion_topografica }}
                                        </option>
                                    </select>
                                    <button @click="verUbicaciones()" type="button" class="btn btn-teal ms-1"><i class="bi bi-pencil"></i></button>
                                </div>

                            </div>


{{--                            <div class="form-group pt-3 col-6 col-lg-3" >--}}
{{--                                <label class="form-label" for="ubicacion">Ubicación Topográfica</label>--}}
{{--                                <textarea rows="1" type="text" class="form-control" id="ubicacion" required minlength="5" maxlength="250"--}}
{{--                                          v-model="filaSeleccionada.ubicacion_topografica"></textarea>--}}
{{--                            </div>--}}

                            <div class="form-group pt-3 col-6 col-lg-3" >
                                <label class="form-label" for="expediente">No. expediente: </label>
                                {{--                                        <input type="hidden" v-model="filaSeleccionada.no_expediente">--}}
                                <span v-html="this.noExpedientePreview" id="preview_expediente"></span>
                                <input v-if="filaSeleccionada.editable === false" @input="generarNoExpediente()" class="form-control" type="number" id="expediente"  required min="1" max="99999" disabled>
                                <input v-else @input="generarNoExpediente()" class="form-control" type="number" id="expediente"  required min="1" max="99999">

                            </div>

                            <div class="form-group pt-3 col-12 col-lg-6" >
                                <label class="form-label" for="descripcion">Nombre y descripción</label>
                                <textarea rows="1" type="text" class="form-control" id="descripcion" required minlength="5"
                                          v-model="filaSeleccionada.descripcion"></textarea>
                            </div>

                            <div class="form-group pt-3 col-4" >
                                <label class="form-label " for="fecha_inicio">Fechas extremas</label>
                                <div class="row">
                                    <div class="col-sm-6">
                                        <input  v-if="filaSeleccionada.editable === false" type="date" required class="form-control" id="fecha_inicio"
                                               title="Fecha extrema inicio"
                                               v-model="filaSeleccionada.fecha_inicio" disabled>
                                        <input  v-else type="date" required class="form-control" id="fecha_inicio"
                                               title="Fecha extrema inicio"
                                               v-model="filaSeleccionada.fecha_inicio">
                                        <small>Fecha de Inicio</small>
                                    </div>
                                    <div class="col-sm-6">
                                        <input v-if="filaSeleccionada.editable === false" type="date" class="form-control" id="fecha_fin"
                                               title="Fecha extrema fin"
                                               v-model="filaSeleccionada.fecha_final" disabled>
                                        <input v-else type="date" class="form-control" id="fecha_fin"
                                               title="Fecha extrema fin"
                                               v-model="filaSeleccionada.fecha_final">
                                        <small>Fecha Final</small>
                                    </div>
                                </div>
                            </div>

                            <div class="form-group pt-3 col-8" >
                                <label class="form-label" for="observaciones">Observaciones</label>
                                <textarea rows="1" type="text" class="form-control" id="observaciones" v-model="filaSeleccionada.observaciones"></textarea>
                            </div>

                            <div class="form-group col-12 d-flex justify-content-end">
                                <button class="btn btn-teal align-self-end" type="submit" v-if="filaSeleccionada && ( !filaSeleccionada.id && !filaSeleccionada.uuid )">
                                    <i class="bi bi-plus"></i> Agregar
                                </button>
                                <div v-else>
                                    <button class="btn btn-teal align-self-end" type="submit">
                                        <i class="bi bi-check2-circle"></i> Actualizar
                                    </button>
                                    <button class="btn btn-danger align-self-end" @click="cancelarActualizar()">
                                        <i class="bi bi-x-circle"></i> Cancelar
                                    </button>
                                </div>

                            </div>
                        </form>
                    </div>
                </div>
            </article>
        </section>

            <section class="section">
                <div class="card">
                    <div class="card-body pt-4">
                        <div class="row">
                            <div class="col-12 table-responsive">
                                <table class="table table-sm table-striped table-hover">
                                    <thead class="table-light">
                                    <tr>
                                        <th style="min-width: 220px;">Expediente</th>
                                        <th>Ubicación topográfica</th>
                                        <th>Nombre y descripción</th>
                                        <th>Fecha Inicio</th>
                                        <th>Fecha final</th>
                                        <th>Observaciones</th>
                                        <th></th>
                                    </tr>
                                    </thead>
                                    <tbody>
                                    <tr v-for="(expediente,indexExpediente) of inventarioActualDetalle.data">
                                        <td>@{{ expediente.no_expediente }}</td>
                                        <td>@{{ expediente.ubicacion_topografica }}</td>
                                        <td>@{{ expediente.descripcion }}</td>
                                        <td>@{{ dayjs(expediente.fecha_inicio).format('DD/MM/YYYY') }}</td>
                                        <td>@{{ expediente.fecha_final ? dayjs(expediente.fecha_final).format('DD/MM/YYYY') : dayjs(expediente.fecha_inicio).format('YYYY') }}</td>
                                        <td>@{{ expediente.observaciones }}</td>
                                        <td>
                                            <button v-if="!mes_cerrado" title="Editar" @click="editarFila(expediente.id)" class="btn m-1 btn-sm btn-outline-primary border-0"> <i class="bi-pencil"></i></button>
                                            <button v-if="!mes_cerrado && expediente.editable" title="Eliminar" @click="eliminarFila(expediente.id)" class="btn m-1 btn-sm btn-outline-danger border-0"> <i class="bi-trash"></i></button>
                                        </td>
                                    </tr>
                                    </tbody>
                                </table>
                            </div>

                            <Paginator :paginate="inventarioActualDetalle" @listar-registros="moveToPage"></Paginator>
                        </div>
                    </div>
                </div>
            </section>

            <div class="modal fade" id="wImportar" tabindex="-1" role="dialog" aria-hidden="true">
                <div class="modal-dialog modal-lg" role="document">
                    <div class="modal-content" v-show="wModalShowingId==='wImportar'">
                        <form id="importarInventarioFrm" action="#" method="post" @submit.prevent="importarInventario()">
                            <div class="modal-header">
                                <h5 class="modal-title">Importar Registros</h5>
                                <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Cerrar"></button>

                            </div>
                            <div class="modal-body">
                                <div class="row">
                                    <input required id="inventarioExcel" name="inventarioExcel" type="file" accept=".xls,.xlsx, application/vnd.openxmlformats-officedocument.spreadsheetml.sheet,application/vnd.ms-excel">
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


            <div class="modal fade" id="wUbicaciones" tabindex="-1" role="dialog" aria-hidden="true">
                <div class="modal-dialog modal-lg" role="document">
                    <div class="modal-content">
                            <div class="modal-header">
                                <h5 class="modal-title">Ubicaciones Topográficas</h5>
                                <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Cerrar"></button>

                            </div>
                            <div class="modal-body">
                                <form id="ubicacionesFrm" action="#" method="post" @submit.prevent="guardarUbicacion()">
                                    <div class="row pb-3">
                                        <div class="form-group pt-3 col-12" >
                                            <label class="form-label" for="ubicacion">Ubicación</label>
                                            <textarea rows="1" type="text" class="form-control" id="ubicacion" required minlength="5"
                                                      v-model="ubicacionSeleccionada.ubicacion"></textarea>
                                        </div>
                                        <div class="form-group pt-3 col-12 col-lg-6" >
                                            <label class="form-label" for="bien_mueble">Bien mueble  (Opcional)</label>
                                            <textarea rows="1" type="text" class="form-control" id="bien_mueble" minlength="5"
                                                      v-model="ubicacionSeleccionada.bien_mueble"></textarea>
                                        </div>
                                        <div class="form-group pt-3 col-12 col-lg-6" >
                                            <label class="form-label" for="no_inventario">No. Inventario (Opcional)</label>
                                            <input class="form-control" type="text" id="no_inventario" v-model="ubicacionSeleccionada.no_inventario">
                                        </div>
                                        <div class="d-flex justify-content-end pt-1">
                                            <div v-if="ubicacionSeleccionada.id == null">
                                                <button type="submit" class="btn btn-teal"><i class="bi bi-plus"></i>Agregar</button>
                                            </div>
                                            <div v-else>
                                                <button class="btn btn-teal align-self-end" type="submit">
                                                    <i class="bi bi-check2-circle"></i> Actualizar
                                                </button>
                                                <button class="btn btn-danger align-self-end" @click="cancelarActualizacionUbicacion()">
                                                    <i class="bi bi-x-circle"></i> Cancelar
                                                </button>
                                            </div>
                                        </div>

                                    </div>
                                </form>
                                <div class="row">
                                    <div class="col-12 table-responsive">
                                        <table class="table table-sm table-striped table-hover">
                                            <thead>
                                                <tr>
                                                    <th>Ubicación</th>
                                                    <th>Bien mueble</th>
                                                    <th>No. inventario</th>
                                                    <th></th>
                                                </tr>
                                            </thead>
                                            <tbody>
                                                <tr v-for="(ubicacion,indexUbicacion) of ubicaciones">
                                                    <td>@{{ ubicacion.ubicacion }}</td>
                                                    <td>@{{ ubicacion.bien_mueble }}</td>
                                                    <td>@{{ ubicacion.no_inventario }}</td>
                                                    <td>
                                                        <button title="Editar" @click="editarUbicacion(ubicacion.id)" class="btn m-1 btn-sm btn-outline-primary border-0"> <i class="bi-pencil"></i></button>
                                                        <button title="Eliminar" @click="eliminarUbicacion(ubicacion.id)" class="btn m-1 btn-sm btn-outline-danger border-0"> <i class="bi-trash"></i></button>
                                                    </td>
                                                </tr>
                                            </tbody>
                                        </table>
                                    </div>
                                </div>

                            </div>
                            <div class="modal-footer">
                                <button type="button" class="btn btn-secondary" data-bs-dismiss="modal">Cerrar</button>
{{--                                <button type="submit" class="btn btn-teal">Guardar</button>--}}
                            </div>
                    </div>
                </div>
            </div>

            <div class="modal fade" id="wFirmas" tabindex="-1" role="dialog" aria-hidden="true">
            <div class="modal-dialog modal-lg" role="document">
                <div class="modal-content" v-show="wModalShowingId==='wFirmas'">
                    <form id="firmasFrm" action="#" method="post" @submit.prevent="guardarFirmas()">
                        <div class="modal-header">
                            <h5 class="modal-title">Firmas Inventario Documental</h5>
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
                                    <input v-model="firmas.reviso[0].nombre" class="form-control" type="text" id="firmas_reviso_nombre" placeholder="Nombre">
                                </div>
                                <div class="col-12 col-md-6">
                                    <input v-model="firmas.reviso[0].cargo" class="form-control" type="text" id="firmas_reviso_cargo" placeholder="Cargo" :required="firmas.reviso[0].nombre != null && firmas.reviso[0].nombre.trim() != ''">
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
