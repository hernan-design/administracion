<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;

use App\Models\Core\LocalidadCiudad;

class LocalidadCiudadController extends Controller
{
    /**
     * Display a listing of the resource.
     *
     * @return \Illuminate\Http\Response
     */
    public function index(Request $request)
    {
		if($request->ajax()) {
            return response()->json( LocalidadCiudad::locations( $request->all() ) );
		}
    }
}
