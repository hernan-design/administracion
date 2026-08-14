<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;

use App\Models\Param\Dashboard;

class HomeController extends Controller
{
    /**
     * Create a new controller instance.
     *
     * @return void
     */
    public function __construct()
    {
        $this->middleware('auth');
    }

    /**
     * Show the application dashboard.
     *
     * @return \Illuminate\Http\Response
     */
    public function index(Request $request)
    {
		if($request->ajax()){
			return response()->json( Dashboard::data( $request->all() ) );
		}
        return view('dashboard.index');
    }
}
