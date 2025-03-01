<?php

namespace App\Http\Controllers;

use Illuminate\Foundation\Auth\Access\AuthorizesRequests;
use Illuminate\Foundation\Bus\DispatchesJobs;
use Illuminate\Foundation\Validation\ValidatesRequests;
use Illuminate\Http\Request;
use Illuminate\Routing\Controller as BaseController;

class Controller extends BaseController
{
    use AuthorizesRequests, DispatchesJobs, ValidatesRequests;

    public function responseError($message = 'Error', $code = '0', $data = null, $httpCode = 400, Request $request = null)
    {
        if ( ! empty( $request ) && $request->expectsJson() ) {
            return $this->responseJsonError($message, $code, $data, $httpCode);
        }

        $success = FALSE;
        return compact('message', 'code', 'success');
    }


    public function responseJsonSuccess($data = null, $message = null)
    {
        $ret = [
            'success' => true
        ];

        if(! empty($data)) {
            $ret = array_merge( $ret, $data);
        }

        if ( ! empty($message)) {
            $ret['message'] = $message;
        }

        return response()->json( $ret );
    }

    public function responseJsonError($message = 'Error', $code = '0', $data = null, $httpCode = 400)
    {
        $ret = [
            'success' => true
        ];

        if(! empty($data)) {
            $ret = array_merge( $ret, $data);
        }


        $ret['message'] = $message;
        $ret['code'] = $code;


        return response()->json( $ret, $httpCode );
    }

}
