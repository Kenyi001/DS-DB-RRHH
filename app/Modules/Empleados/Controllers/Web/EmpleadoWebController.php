<?php

namespace App\Modules\Empleados\Controllers\Web;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;

class EmpleadoWebController extends Controller
{
    public function index()
    {
        return view('empleados.index');
    }
}