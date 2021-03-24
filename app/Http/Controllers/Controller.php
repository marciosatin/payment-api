<?php

namespace App\Http\Controllers;

use Illuminate\Http\Response;
use Laravel\Lumen\Routing\Controller as BaseController;

class Controller extends BaseController
{

    protected function responseErrorClient(array $data, $status = Response::HTTP_BAD_REQUEST)
    {
        return response()->json($data, $status);
    }

    protected function responseErrorServer(array $data, $status = Response::HTTP_INTERNAL_SERVER_ERROR)
    {
        return response()->json($data, $status);
    }

}