<?php

namespace App\Http\Controllers;

use Illuminate\Support\Facades\View;
use Illuminate\Foundation\Bus\DispatchesJobs;
use Illuminate\Routing\Controller as BaseController;
use Illuminate\Foundation\Validation\ValidatesRequests;
use Illuminate\Foundation\Auth\Access\AuthorizesRequests;

class Controller extends BaseController
{
    use AuthorizesRequests, DispatchesJobs, ValidatesRequests;
    protected $breadcrumbs = [];

    protected function addBreadcrumb($name, $url)
    {
        $this->breadcrumbs[$name] = [
            'url' => $url,
            'label' => ucfirst($name)
        ];

        View::share('breadcrumbs', $this->breadcrumbs);
    }
}
