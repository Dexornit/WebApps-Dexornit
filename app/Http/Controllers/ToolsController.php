<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;

class ToolsController extends Controller
{
    public function index()
    {
        return view('tools.index');
    }

    public function a2f()
    {
        return view('tools.a2f');
    }
}
