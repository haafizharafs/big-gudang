<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use Illuminate\Support\Facades\Storage;
use Illuminate\Support\Facades\Response;

class StorageController extends Controller
{
    /**
     * Handle the incoming request.
     *
     * @param  \Illuminate\Http\Request  $request
     * @return \Illuminate\Http\Response
     */
    public function __invoke($path)
    {
        $file = Storage::disk('private')->get($path);
        $type = Storage::disk('private')->mimeType($path);
        return Response::make($file, 200, ['Content-Type' => $type]);
    }
}
