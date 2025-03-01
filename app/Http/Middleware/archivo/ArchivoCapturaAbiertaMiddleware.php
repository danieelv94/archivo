<?php

namespace App\Http\Middleware\archivo;

use App\Models\archivo\ArchivoConfig;
use Closure;
use Illuminate\Http\Request;

class ArchivoCapturaAbiertaMiddleware
{
    public function handle(Request $request, Closure $next)
    {

        $estado_captura = ArchivoConfig::first()->captura_abierta;
        if ( ! $estado_captura ) {
            return abort(404);
        }

        return $next($request);
    }
}
