<table>
    <tbody>
        <tr>
            <th style=""> <img height="100px" src="img/logo-ccleh-small.png"> </th>
            <th colspan="6" style="text-align: center; font-weight: bold;">INVENTARIO DOCUMENTAL</th>
            <th style=""> <img height="100px" src="img/escudo.png"> </th>
        </tr>
        <tr>
            <th></th>
            <th style="font-weight: bold;">FONDO</th>
            <td style="font-weight: bold;" colspan="3" >{{ mb_strtoupper($inventario_documental->unidad_presupuestal,'UTF-8') }}</td>
            <th style="font-weight: bold;" colspan="2">FECHA DE EMISIÓN:
                @if(!empty($inventario_documental->fecha_cierre_captura))
                    {{ mb_strtoupper( config('app.meses')[$inventario_documental->fecha_cierre_captura->format('n')].' '.$inventario_documental->fecha_cierre_captura->format('Y'),'UTF-8' ) }}
                @endif
            </th>
        </tr>
        <tr >
            <th></th>
            <th style="font-weight: bold;">SUBFONDO</th>
            <td style="font-weight: bold;" colspan="3">N/A</td>
            <th colspan="2" style="font-weight: bold;">HOJA 1 DE 1</th>
        </tr>
        <tr >
            <th></th>
            <th style="font-weight: bold;">UNIDAD ADMINISTRATIVA</th>
            <td style="font-weight: bold;" colspan="3">{{ mb_strtoupper( $inventario_documental->area->codigo.'* '.$inventario_documental->area->nombre,'UTF-8' ) }}</td>
        </tr>
        <tr>
            <th></th>
            <th style="font-weight: bold;">ÁREA GENERADORA</th>
            <td style="font-weight: bold;" colspan="3">{{ mb_strtoupper( $inventario_documental->departamento->clave.'* '.$inventario_documental->departamento->nombre,'UTF-8' ) }}</td>
        </tr>
        <tr>
            <th></th>
            <th style="font-weight: bold;">NOMBRE DEL RESPONSABLE</th>
            <td style="font-weight: bold;" colspan="3">{{ mb_strtoupper( $inventario_documental->persona_responsable->nombre_completo_profesion,'UTF-8' ) }}</td>

            <th style="font-weight: bold;">CÓDIGO: {{ mb_strtoupper( config('app.acronyms_organization').'-'.$inventario_documental->departamento->clave.'*'.$inventario_documental->clave_seccion.'.'.$inventario_documental->clave_serie,'UTF-8' ) }}</th>
{{--            <td style="font-weight: bold;">{{ mb_strtoupper( config('app.acronyms_organization').'-'.$inventario_documental->area->codigo.'*'.$inventario_documental->clave_seccion.'.'.$inventario_documental->clave_serie,'UTF-8' ) }}</td>--}}
        </tr>
        <tr>
            <th></th>
            <th style="font-weight: bold;">SECCIÓN</th>
            <td style="font-weight: bold;" colspan="3">{{ mb_strtoupper( $inventario_documental->seccion->clave.'. '.$inventario_documental->seccion->nombre,'UTF-8' ) }}</td>
            <th style="font-weight: bold;">MES QUE REPORTA: {{ mb_strtoupper( config('app.meses')[$inventario_documental->mes_captura],'UTF-8' ) }}</th>
{{--            <td style="font-weight: bold;" colspan="3">{{ mb_strtoupper( config('app.meses')[$inventario_documental->mes_captura],'UTF-8' ) }}</td>--}}
        </tr>
        <tr>
            <th></th>
            <th style="font-weight: bold;">SERIE</th>
            <td style="font-weight: bold;" colspan="3">{{ mb_strtoupper( $inventario_documental->seccion->clave.'.'.$inventario_documental->serie->clave.'. '.$inventario_documental->serie->nombre,'UTF-8' ) }}</td>
        </tr>
        <tr>
            <th></th>
            <th style="font-weight: bold;">SUBSERIE</th>
            <td style="font-weight: bold;" colspan="3">N/A</td>
        </tr>
    </tbody>

    <thead>
    <tr>
        <th rowspan="2" style="text-align: center; font-weight: bold; width: 50px;">NO.</th>
        <th rowspan="2" style="text-align: center; font-weight: bold; width: 200px; background: gray;">UBICACIÓN FÍSICA</th>
        <th rowspan="2" style="text-align: center; font-weight: bold; width: 200px; background: gray;">UBICACIÓN TOPOGRÁFICA</th>
        <th rowspan="2" style="text-align: center; font-weight: bold; width: 200px; background: gray;">NO. EXPEDIENTE</th>
        <th rowspan="2" style="text-align: center; font-weight: bold; width: 500px; background: gray;">NOMBRE Y DESCRIPCIÓN DEL EXPEDIENTE</th>
        <th colspan="2" style="text-align: center; font-weight: bold; background: gray;">FECHAS EXTREMAS</th>
        <th rowspan="2" style="text-align: center; font-weight: bold; width: 200px; background: gray;">OBSERVACIÓNES</th>
    </tr>
    <tr>
        <th style="text-align: center; font-weight: bold; width: 100px; background: gray;">INICIO</th>
        <th style="text-align: center; font-weight: bold; width: 100px; background: gray;">FIN</th>
    </tr>
    </thead>
    <tbody>
    @foreach($inventario_detalle as $key => $fila)
        <tr>
            <td style="text-align: center;">{{ ($key+1) }}</td>
            <td>{{ $fila->ubicacion_fisica }}</td>
            <td>{{ $fila->ubicacion_topografica }}</td>
            <td>{{ $fila->no_expediente }}</td>
            <td>{{ $fila->descripcion }}</td>
            <td>{{ $fila->fecha_inicio->format('d/m/Y') }}</td>
            <td>{{ empty($fila->fecha_final) ? $fila->fecha_inicio->format('Y') :  $fila->fecha_final->format('d/m/Y') }}</td>
            <td>{{ $fila->observaciones }}</td>
        </tr>
    @endforeach
        <tr>
            <td></td>
            <td style="font-weight: bold; background: gray;">TOTAL</td>
            @if( !isset($key) )
                <td>0 EXPEDIENTES</td>
            @else
                <td>{{ ($key+1) }} EXPEDIENTES</td>
            @endif
        </tr>
    </tbody>
</table>
<table>
    <tbody>
        <tr>
            <td colspan="2" style="text-align: center;">Elaboró</td>
            <td></td>
            <td style="text-align: center;">Revisó</td>
            <td></td>
            <td colspan="2" style="text-align: center;">Validó</td>
        </tr>
        <tr>
            <td colspan="2"></td>
            <td></td>
            <td></td>
            <td></td>
            <td colspan="2"></td>
        </tr>
        <tr>
            <td colspan="2" style="text-align: center;">_____________________________</td>
            <td></td>
            <td style="text-align: center;">_____________________________</td>
            <td></td>
            <td colspan="2" style="text-align: center;">_____________________________</td>
        </tr>
        <tr>
            <td colspan="2" style="text-align: center;">
                {{ $inventario_documental->persona_responsable->nombre_completo_profesion }}
            </td>
            <td></td>
            <td style="text-align: center;">
                <?php if($inventario_documental->persona_revisa): ?>
                    {{ $inventario_documental->persona_revisa->nombre_completo_profesion }}
                <?php endif; ?>
            </td>
            <td></td>
            <td colspan="2" style="text-align: center;">
                <?php if($inventario_documental->persona_autoriza): ?>
                    {{ $inventario_documental->persona_autoriza->nombre_completo_profesion }}
                <?php endif; ?>
            </td>
        </tr>
        <tr>
            <td colspan="2" style="text-align: center;">
                {{ $inventario_documental->persona_responsable->puesto }}
            </td>
            <td></td>
            <td style="text-align: center;">
                <?php if($inventario_documental->persona_revisa): ?>
                    {{ $inventario_documental->persona_revisa->puesto }}
                <?php endif; ?>
            </td>
            <td></td>
            <td colspan="2" style="text-align: center;">
                <?php if($inventario_documental->persona_autoriza): ?>
                    {{ $inventario_documental->persona_autoriza->puesto }}
                <?php endif; ?>
            </td>

        </tr>
    </tbody>
</table>>
