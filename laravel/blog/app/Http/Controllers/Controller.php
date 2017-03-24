<?php

namespace App\Http\Controllers;

use Illuminate\Foundation\Bus\DispatchesJobs;
use Illuminate\Routing\Controller as BaseController;
use Illuminate\Foundation\Validation\ValidatesRequests;
use Illuminate\Foundation\Auth\Access\AuthorizesRequests;

use Illuminate\Contracts\View\Factory as ViewFactory;
use Google\Cloud\Trace\RequestTracer;

/**
 * Get the evaluated view contents for the given view.
 *
 * @param  string  $view
 * @param  array   $data
 * @param  array   $mergeData
 * @return \Illuminate\View\View|\Illuminate\Contracts\View\Factory
 */
function view($view = null, $data = [], $mergeData = [])
{
    $factory = app(ViewFactory::class);

    if (func_num_args() === 0) {
        return $factory;
    }

    return RequestTracer::instrument(['name' => 'view/' . $view], function () use ($factory, $view, $data, $mergeData) {
        return $factory->make($view, $data, $mergeData);
    });
}

class Controller extends BaseController
{
    use AuthorizesRequests, DispatchesJobs, ValidatesRequests;
}
