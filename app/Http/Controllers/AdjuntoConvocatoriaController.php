<?php

namespace App\Http\Controllers;



use App\Models\Core\Convocatoria;
use App\Models\Core\Adjunto, App\Models\Core\AdjuntoConvocatoria, App\Models\Core\Estado;
//use Illuminate\Support\Facades\DB;
//use Illuminate\Support\Facades\Input;
//use Illuminate\Support\Facades\Log;
use Request, Response, Auth, Input, App, DB, Log, Lang;
use TuProyecto;

class AdjuntoConvocatoriaController extends Controller
{

    /**
     * Display a listing of the resource.
     *
     * @return \Illuminate\Http\Response
     */
    public function index()
    {
      $adjuntoConvocatoria = AdjuntoConvocatoria::getAttachementForAnnouncement(Input::get('codigo_convocatoria'));
      return response()->json($adjuntoConvocatoria);
    }

    /**
     * Show the form for creating a new resource.
     *
     * @return \Illuminate\Http\Response
     */
    public function create()
    {
		return response()->json(['name' => 'create']);
    }

    /**
     * Store a newly created resource in storage.
     *
     * @param  \Illuminate\Http\Request  $request
     * @return \Illuminate\Http\Response
     */
    public function store()
    {
    	if (Request::ajax()) {
			DB::beginTransaction();
			try {

				$adjunto = new Adjunto();
				$adjunto->codigo_tipo_adjunto = 1;
				$adjunto->direccion_adjunto = Input::get('direccion_adjunto');
				$adjunto->nombre_adjunto = Input::get('nombre_adjunto');
				$adjunto->save();

				$adjuntoConvocatoria = new AdjuntoConvocatoria();
				$adjuntoConvocatoria->codigo_convocatoria = Input::get('codigo_convocatoria');
				$adjuntoConvocatoria->codigo_adjunto = $adjunto->codigo_adjunto;
				$adjuntoConvocatoria->codigo_estado = Estado::getState('active');
				$adjuntoConvocatoria->save();

				DB::commit();
				return response()->json(['success' => true, 'message' => 'La convocatoria ha sido editada correctamente!!', 'codigo_adjunto' => $adjunto->codigo_adjunto, 'redirect' => true]);
			} catch (\Exception $e) {
				DB::rollback();
				Log::error(sprintf('%s:%s', 'AdjuntoConvocatoria:store', $e->getMessage()));
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
		return response()->json(['name' => 'show']);
    }

    /**
     * Show the form for editing the specified resource.
     *
     * @param  int  $id
     * @return \Illuminate\Http\Response
     */
    public function edit($id)
    {
		return response()->json(['name' => 'edit']);
    }

    /**
     * Update the specified resource in storage.
     *
     * @param  \Illuminate\Http\Request  $request
     * @param  int  $id
     * @return \Illuminate\Http\Response
     */
    public function update($request, $id)
    {
		return response()->json(['name' => 'update']);
    }

    /**
     * Remove the specified resource from storage.
     *
     * @param  int  $id
     * @return \Illuminate\Http\Response
     */
    public function destroy($id)
    {
    	if(Request::ajax()) {
			DB::beginTransaction();
			try {
				$codigo_convocatoria = (int)Input::get('codigo_convocatoria');
				$codigo_adjunto = (int)Input::get('codigo_adjunto');
				$convocatoria =  Convocatoria::find($codigo_convocatoria);
				if (!$convocatoria instanceof Convocatoria) {
					abort(404);
				}

				if(!$codigo_convocatoria || !is_numeric($codigo_convocatoria)) {
					abort(404);
				}

				if(!$codigo_adjunto || !is_numeric($codigo_adjunto)) {
					abort(404);
				}

				AdjuntoConvocatoria::where('codigo_adjunto', '=', $codigo_adjunto)->where('codigo_convocatoria', '=', $codigo_convocatoria)->update(['codigo_estado' => Estado::getState('removed')]);
				DB::commit();
				return response()->json(['success' => true]);
			} catch (\Exception $e) {
				DB::rollback();
				Log::error($e->getMessage());
				return response()->json(['success' => false, 'Description' => 'Error inesperado' ]);
			}
		} else {
    		abort(404);
		}
    }
}
