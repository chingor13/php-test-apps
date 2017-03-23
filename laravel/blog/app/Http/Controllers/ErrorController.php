<?php

namespace App\Http\Controllers;

use App\Post;
use Illuminate\Http\Request;

class ErrorController extends Controller
{
    public function index()
    {
        throw new \RuntimeException("Foobar");
    }

}
