<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;

use App\Http\Requests;
use App\Http\Controllers\Controller;

use DB, Log;

use App\Models\Core\ContenidoCanal, App\Models\Core\Estado;

class ContenidoCanalController extends Controller
{
	/**
     * Instantiate a new Controller instance.
     */
    public function __construct()
    {
        $this->middleware('ability:admin|super-admin|administrativo,view,contenidocanal');
        $this->middleware('ability:admin|super-admin|administrativo,create,contenidocanal', ['only' => ['store']]);
        $this->middleware('ability:admin|super-admin|administrativo,remove,contenidocanal', ['only' => ['destroy']]);
    }

    /**
     * Display a listing of the resource.
     *
     * @return \Illuminate\Http\Response
     */
    public function index(Request $request)
    {
        if($request->ajax()){
			switch ($request->type) {
				case 'list':
					return ContenidoCanal::getChannelsContentList($request->tipo_contenido, $request->id_contenido);
					break;

				default:
					return ContenidoCanal::getChannelsContent($request->tipo_contenido, $request->id_contenido);
					break;
			}
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
			$channelContent = ContenidoCanal::getChannelContent($request->data['option_id'], $request->data['data_id'], $request->data['data_param']);
			if($channelContent instanceof ContenidoCanal){
				return response()->json(['success' => false, 'errors' => 'El canal ya se encuentra asignado, verifique!!']);
			}

			DB::beginTransaction();
			try {
				$channelContent = new ContenidoCanal;
				$channelContent->codigo_canal = $request->data['option_id'];
				$channelContent->codigo_estado = Estado::getState('active');
				$channelContent->codigo_recurso_modulo = $request->data['data_id'];
				$channelContent->codigo_modulo = $request->data['data_param'];
				$channelContent->save();

				// Commit Transaction
				DB::commit();
				return response()->json(['success' => true, 'message' => 'El canal se ha asignado correctamente!!', 'codigo_contenido_canal' => $channelContent->codigo_contenido_canal]);

			}catch(\Exception $e){
				DB::rollback();
				Log::error(sprintf('%s:%s', 'ContenidoCanalController:store', $e->getMessage()));
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
    public function destroy(Request $request, $id)
    {
		if($request->ajax()){
			$channelContent = ContenidoCanal::getChannelContent($id, $request->data['data_id'], $request->data['data_param']);
			if(!$channelContent instanceof ContenidoCanal){
				throw new \Exception("Contenido canal no encontrado");
			}

			DB::beginTransaction();
			try {
				$channelContent->delete();

				// Commit Transaction
				DB::commit();
				return response()->json(['success' => true, 'message' => 'Se ha removido el canal  correctamente!!']);

			}catch(\Exception $e){
				DB::rollback();
				Log::error(sprintf('%s:%s', 'ContenidoCanalController:destroy', $e->getMessage()));
				return response()->json(['success' => false, 'errors' => trans('app.exception')]);
			}
		}
		abort(404);
    }
}
