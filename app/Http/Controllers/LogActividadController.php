<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;

use App\Models\Bita\LogActivity;

class LogActividadController extends Controller
{
	/**
     * Display a listing of the resource.
     *
     * @return \Illuminate\Http\Response
     */
    public function index(Request $request)
    {
		if($request->ajax()){
			return response()->json(LogActivity::getActivitiesList($request->all()));
		}
        abort(404);
    }
}
