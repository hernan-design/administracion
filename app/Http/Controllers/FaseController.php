<?php

namespace App\Http\Controllers;

use App\Models\Core\Adjunto;
use App\Models\Core\AdjuntoFase;
use Illuminate\Http\Request;

use DB, Log;

use App\Http\Requests;
use App\Http\Controllers\Controller;

use App\Models\Core\Convocatoria, App\Models\Core\Fase, App\Models\Core\Estado;

class FaseController extends Controller
{
	/**
	 * Instantiate a new Controller instance.
	 */
	public function __construct()
	{
		$this->middleware('ability:admin|super-admin|administrativo,view,convocatoria');
		$this->middleware('ability:admin|super-admin|administrativo,create,convocatoria', ['only' => ['create', 'store']]);
		$this->middleware('ability:admin|super-admin|administrativo,edit,convocatoria', ['only' => ['edit', 'update']]);
		$this->middleware('ability:admin|super-admin|administrativo,remove,convocatoria', ['only' => ['destroy']]);
	}

    /**
     * Display a listing of the resource.
     *
     * @return \Illuminate\Http\Response
     */
    public function index(Request $request)
    {
        if ($request->ajax()) {
             if ($request->has('program') && !$request->has('phase') && !$request->has('codigo_recurso')) {
                $program = Convocatoria::find($request->program);
                if(!$program instanceof Convocatoria) {
                    abort(404);
                }
                return response()->json(Fase::getPhaseProgram($program->codigo_convocatoria));
             } else if ($request->has('phase') && !$request->has('program')) {
                $phase = Fase::find($request->phase);
                if (!$phase instanceof Fase) {
                  abort(404);
                }
                $code_phase = $phase->codigo_fase;

                return response()->json(Fase::getPhase($code_phase));
             }
        }
        abort(404);
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
			$data = $request->all();
			$phase_order = 1;

			$phaseOrder = Fase::getMaxPhase();
			if(isset($phaseOrder)){
				$phase_order = $phaseOrder + 1;
			}

			$phase = new Fase;
			if ($phase->isValid($data)) {
				DB::beginTransaction();
        try {
          if($request->tipo_contenido_phase) {
              $phase->fill($data);
              $phase->url_fase = slugtp($request->nombre_fase, $phase->getTable(), 'url_fase');
              $phase->codigo_estado = Estado::getState('active');
              $phase->fase_registro = $request->fase_registro ? TRUE : FALSE;
              $phase->fase_entidad_editable = $request->fase_entidad_editable ? TRUE : FALSE;
              $phase->asignacion_especialidad = $request->asignacion_especialidad ? TRUE : FALSE;
              $phase->edicion_formulario_registro = $request->edicion_formulario_registro ? TRUE : FALSE;
              if ($request->fecha_edicion_formulario_registro) {
                $phase->fecha_edicion_formulario_registro = $request->fecha_edicion_formulario_registro;
              }
              $phase->orden_fase = $phase_order;
              $phase->save();

          } else {
            $phase->fill($data);
            $phase->url_fase = slugtp($request->nombre_fase, $phase->getTable(), 'url_fase');
            $phase->codigo_estado = Estado::getState('active');
            $phase->fase_registro = $request->fase_registro ? TRUE : FALSE;
            $phase->fase_entidad_editable = $request->fase_entidad_editable ? TRUE : FALSE;
            $phase->asignacion_especialidad = $request->asignacion_especialidad ? TRUE : FALSE;
            $phase->edicion_formulario_registro = $request->edicion_formulario_registro ? TRUE : FALSE;
            if ($request->fecha_edicion_formulario_registro) {
              $phase->fecha_edicion_formulario_registro = $request->fecha_edicion_formulario_registro;
            }
            $phase->orden_fase = $phase_order;
            $phase->save();
          }

            // Commit Transaction
            DB::commit();
            return response()->json(['success' => true, 'message' => 'La fase ha sido creada correctamente!!', 'codigo_fase' => $phase->codigo_fase]);
				}catch(\Exception $e){
            DB::rollback();
            Log::error(sprintf('%s:%s', 'FaseController:store', $e->getMessage()));
            return response()->json(['success' => false, 'errors' => trans('app.exception')]);
        }
			}
			return response()->json(['success' => false, 'errors' => $phase->errors]);
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
		if($request->ajax()){
			$data = $request->all();
			$phase = Fase::find($id);
			if(!$phase instanceof Fase ){
				throw new \Exception("Fase no encontrada {$id}");
			}

			if ($phase->isValid($data)) {
				DB::beginTransaction();
        try {
					if($request->has('change_state')){
						$phase->codigo_estado = $request->codigo_estado == Estado::getState('active') ? Estado::getState('inactive') : Estado::getState('active');

						$event = $phase->codigo_estado == Estado::getState('active') ? 'activada' : 'inactivada';
          }else{
						$phase->fill($data);
						$phase->fase_registro = $request->fase_registro ? TRUE : FALSE;
						$phase->fase_entidad_editable = $request->fase_entidad_editable ? TRUE : FALSE;
						$phase->asignacion_especialidad = $request->asignacion_especialidad ? TRUE : FALSE;
						$phase->edicion_formulario_registro = $request->edicion_formulario_registro ? TRUE : FALSE;

            if ($request->has('url_adjunto_phase') && isset($request->url_adjunto_phase) && !empty($request->url_adjunto_phase)) {
              $adjunto = new Adjunto();
              $adjunto->codigo_tipo_adjunto = $request->tipo_adjunto_phase;
              $adjunto->nombre_adjunto = $request->titulo_adjunto_phase;
              $adjunto->direccion_adjunto = $request->url_adjunto_phase;
              $adjunto->save();

              $codigo_adjunto = $adjunto->codigo_adjunto;
              $adjuntoFase = new AdjuntoFase();
              $adjuntoFase->codigo_estado = Estado::getState('active');
              $adjuntoFase->codigo_adjunto = $codigo_adjunto;
              $adjuntoFase->codigo_fase = $id;
              $adjuntoFase->codigo_tipo_recurso_fase = $request->tipo_recurso_phase;
              $adjuntoFase->descripcion_adjunto_fase = $request->descripcion_recurso_phase;
              $adjuntoFase->save();
            }



						if( isset($request->fecha_edicion_formulario_registro) ){
							$phase->fecha_edicion_formulario_registro = $request->fecha_edicion_formulario_registro;
						}
						$event = 'editada';
					}

					$phase->save();

          // Commit Transaction
          DB::commit();
          return response()->json(['success' => true, 'redirect' => true, 'message' => trans('app.rest.success', ['resource' => 'La fase', 'event' => $event]), 'codigo_fase' => $phase->codigo_fase, 'codigo_estado' => $phase->codigo_estado, 'fase_registro' => $phase->fase_registro, 'fase_entidad_editable' => $phase->fase_entidad_editable, 'asignacion_especialidad' => $phase->asignacion_especialidad, 'fase_cancelacion' => $phase->fase_cancelacion, 'edicion_formulario_registro' => $phase->edicion_formulario_registro]);

				}catch(\Exception $e){
          DB::rollback();
          Log::error(sprintf('%s:%s', 'FaseController:update', $e->getMessage()));
          return response()->json(['success' => false, 'errors' => trans('app.exception')]);
        }
			}
			return response()->json(['success' => false, 'errors' => $phase->errors]);
		}
		abort(404);
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

	/**
     * Order all resources in storage.
     *
     * @param  \Illuminate\Http\Request  $request
     * @return \Illuminate\Http\Response
     */
	public function order(Request $request)
	{
		if($request->ajax()){
			DB::beginTransaction();
			try {

				foreach ($request->data as $value) {
					$phase = Fase::find($value['codigo_fase']);

					if(!$phase instanceof Fase){
						return response()->json(['success' => false, 'errors' => 'La fase no se encuentra en el sistema, verifique.']);
					}

					$phase->orden_fase = $value['orden_fase'];
					$phase->save();
				}

				// Commit Transaction
				DB::commit();
				return response()->json(['success' => true, 'message' => 'Se ha cambiado la posición de la fase correctamente.']);

			}catch(\Exception $e){
				DB::rollback();
				Log::error(sprintf('%s:%s', 'FaseController:order', $e->getMessage()));
				return response()->json(['success' => false, 'errors' => trans('app.exception')]);
			}
		}
		abort(404);
	}

  public function orderAdjuntoFase(Request $request)
  {
    if($request->ajax()){
      DB::beginTransaction();
      try {

        foreach ($request->data as $value) {
          $adjunto = Adjunto::find($value['codigo_adjunto']);

          if(!$adjunto instanceof Adjunto){
            return response()->json(['success' => false, 'errors' => 'El adjunto no se encuentra en el sistema, verifique.']);
          }

          $adjuntoFase = AdjuntoFase::find($value['codigo_adjunto_fase']);

          if(!$adjuntoFase instanceof AdjuntoFase){
            return response()->json(['success' => false, 'errors' => 'El adjunto de la fase no se encuentra en el sistema, verifique.']);
          }

          $adjuntoFase->orden_adjunto_fase = $value['orden_adjunto'];
          $adjuntoFase->save();
        }

        // Commit Transaction
        DB::commit();
        return response()->json(['success' => true, 'message' => 'Se ha cambiado la posición del adjunto correctamente.']);

      }catch(\Exception $e){
        DB::rollback();
        Log::error(sprintf('%s:%s', 'FaseController:orderAdjuntoFase', $e->getMessage()));
        return response()->json(['success' => false, 'errors' => trans('app.exception')]);
      }
    }
    abort(404);
  }
}
