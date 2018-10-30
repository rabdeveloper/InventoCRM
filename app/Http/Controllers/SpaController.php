<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;

class SpaController extends Controller
{
    public function index()
    {
        return view('v1/layouts/base');
    }
}
