<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;

use App\Http\Requests;
use App\Http\Controllers\Controller;
use Illuminate\Support\Collection as Collection;

use DB, Lang, App, Log, SendEmail, Response, Input;

use App\Models\Core\EvaluacionEmprendimiento, App\Models\Core\AsignacionJurado, App\Models\Core\Estado, App\Models\Core\RegistroFaseUsuario;

class AsignacionJuradoController extends Controller
{
	/**
	 * Display a listing of the resource.
	 *
	 * @return \Illuminate\Http\Response
	 */
	public function index(Request $request)
	{
		if ($request->ajax()) {
			if($request->has('evaluation')) {
				$evaluation = EvaluacionEmprendimiento::find($request->evaluation);
				if(!$evaluation instanceof EvaluacionEmprendimiento) {
					abort(404);
				}
				return AsignacionJurado::getAssignments($evaluation, $request->all());
			}else{
				// dd($request->all());
				return AsignacionJurado::getRegistersList($request->all());
			}
		}
		return view('evaluation.main');
	}

	/**
	 * Show the form for creating a new resource.
	 *
	 * @return \Illuminate\Http\Response
	 */
	public function create()
	{
		//
	}

	/**
	 * Store a newly created resource in storage.
	 *
	 * @param  \Illuminate\Http\Request  $request
	 * @return \Illuminate\Http\Response
	 */
	public function store(Request $request)
	{
		//
	}

	/**
	 * Display the specified resource.
	 *
	 * @param  int  $id
	 * @return \Illuminate\Http\Response
	 */
	public function show(Request $request,$id)
	{
		$assignment = RegistroFaseUsuario::getRegisterjury($id);
		if(!$assignment instanceof RegistroFaseUsuario) {
			abort(404);
		}

		if ($request->ajax()) {
			return response()->json(RegistroFaseUsuario::buildParticipant($assignment));
		}
		return view('evaluation/main');
	}

	/**
	 * Show the form for editing the specified resource.
	 *
	 * @param  int  $id
	 * @return \Illuminate\Http\Response
	 */
	public function edit($id)
	{
		//
	}

	/**
	 * Update the specified resource in storage.
	 *
	 * @param  \Illuminate\Http\Request  $request
	 * @param  int  $id
	 * @return \Illuminate\Http\Response
	 */
	public function update(Request $request, $id)
	{
		//
	}

	/**
	 * Remove the specified resource from storage.
	 *
	 * @param  int  $id
	 * @return \Illuminate\Http\Response
	 */
	public function destroy($id)
	{
		//
	}
}
