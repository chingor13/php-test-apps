<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;

class EnvironmentController extends Controller
{
    public function index()
    {
        return json_encode($_ENV);
    }

}
