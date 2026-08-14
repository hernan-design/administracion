<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;

use App\Http\Requests;
use App\Http\Controllers\Controller;

use Datatables, Log, DB;

use App\Models\Core\VinculoCanal, App\Models\Core\Estado, App\Models\Core\ProfesionPersona;

class VinculoCanalController extends Controller
{
    /**
     * Display a listing of the resource.
     *
     * @return \Illuminate\Http\Response
     */
    public function index(Request $request)
    {
        if($request->ajax()){
			// return Datatables::of( VinculoCanal::getChannelLink() )->make(true);
			return response()->json( VinculoCanal::getChannelLink( $request->all() ) );
		}
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
		if($request->ajax()){

			$channelLink = VinculoCanal::where('codigo_canal', '=', $request->channel)
				->where('codigo_recurso', '=', $request->codigo_recurso)
				->where('codigo_institucion', '=', session('institution')->codigo_institucion)
				->first();

			if($channelLink instanceof VinculoCanal){
				if($channelLink->codigo_estado == Estado::getState('active')){
					return response()->json(['success' => false, 'errors' => 'La profesión ya está asignada!!']);
				}else{
					$channelLink->codigo_estado = Estado::getState('active');
				}
			}else{
				$channelLink = new VinculoCanal;
			}

			DB::beginTransaction();
			try {

				if(!isset($codigo_estado)){
					$channelLink->codigo_institucion = session('institution')->codigo_institucion;
					$channelLink->codigo_canal = $request->channel;
					$channelLink->codigo_recurso = $request->codigo_recurso;
					$channelLink->codigo_estado = Estado::getState('active');
					$channelLink->codigo_tabla_vinculo_canal = $request->codigo_tabla_vinculo_canal;
				}

				$channelLink->save();

				switch ($channelLink->codigo_tabla_vinculo_canal) {
					case VinculoCanal::getTableLinkChannel('professions'):
						$resource = ProfesionPersona::find($channelLink->codigo_recurso);
						$channelLink->nombnre_profesion_persona = $resource->nombnre_profesion_persona;
				}

				$response = $channelLink;
				$response->success = true;
				$response->message = 'Se ha asignado la profesión correctamente!!';

				// Commit Transaction
				DB::commit();
				return response()->json($response);

			}catch(\Exception $e){
				DB::rollback();
				Log::error(sprintf('%s:%s', 'VinculoCanalController:store', $e->getMessage()));
				return response()->json(['success' => false, 'errors' => trans('app.exception')]);
			}
		}
		abort(404);
    }

    /**
     * Display the specified resource.
     *
     * @param  int  $id
     * @return \Illuminate\Http\Response
     */
    public function show($id)
    {
        //
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
		if(!is_numeric($id)){
			abort(404);
		}

		$channelLink = VinculoCanal::find($id);
		if(!$channelLink instanceof VinculoCanal){
			abort(404);
		}

		DB::beginTransaction();
		try {
			$channelLink->codigo_estado = Estado::getState('inactive');
			$channelLink->save();

			// Commit Transaction
			DB::commit();
			return response()->json(['success' => true, 'message' => 'La profesión ha sido eliminada del canal correctamente!!']);

		}catch(\Exception $e){
			DB::rollback();
			Log::error(sprintf('%s:%s', 'VinculoCanalController:destroy', $e->getMessage()));
			return response()->json(['success' => false, 'errors' => trans('app.exception')]);
		}
    }
}
