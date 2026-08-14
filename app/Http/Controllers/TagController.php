<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;

use App\Models\Core\Tag;

class TagController extends Controller
{
	/**
     * Display a listing of the resource.
     *
     * @return \Illuminate\Http\Response
     */
    public function index(Request $request)
    {
        if($request->ajax()) {
			return response()->json( Tag::tags( $request->all() ) );
        }
    }
}
