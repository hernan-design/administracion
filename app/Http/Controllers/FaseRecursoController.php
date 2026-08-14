<?php

namespace App\Http\Controllers;

use App\Models\Core\Adjunto;
use App\Models\Core\AdjuntoFase;
use App\Models\Core\Convocatoria;
use App\Models\Core\Estado;
use App\Models\Core\Fase;
use DB, Log;
use Illuminate\Http\Request;

class FaseRecursoController extends Controller
{
    /**
     * Display a listing of the resource.
     *
     * @return \Illuminate\Http\Response
     */
    public function index(Request $request)
    {
        if ($request->ajax()) {
          if ($request->has('program') && $request->has('phase') && !$request->has('codigo_recurso')) {
            $program = Convocatoria::find($request->program);
            if(!$program instanceof Convocatoria) {
              abort(404);
            }
            $phase = Fase::find($request->phase);
            if(!$phase instanceof Fase) {
              abort(404);
            }
            return response()->json(Fase::getResourceOfPhase($request->program, $request->phase));
          } else if ($request->has('program') && $request->has('phase') && $request->has('codigo_recurso')){

            $program = Convocatoria::find($request->program);
            if(!$program instanceof Convocatoria) {
              abort(404);
            }
            $phase = Fase::find($request->phase);
            if(!$phase instanceof Fase) {
              abort(404);
            }

            return response()->json(Fase::getResourceSpecificOfPhase($request->program, $request->phase, $request->codigo_recurso));
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
      if ($request->ajax()) {
        $codigo_fase = $request->codigo_fase;
        $phase = Fase::find($codigo_fase);
        if (!$phase instanceof Fase) {
          throw new \Exception("Fase no encontrada {$codigo_fase}");
        }

        DB::beginTransaction();
        try {
          $adjunto = new Adjunto();
          $adjunto->codigo_tipo_adjunto = $request->tipo_adjunto_phase;
          $adjunto->direccion_adjunto = $request->direccion_adjunto;
          $adjunto->nombre_adjunto = $request->nombre_adjunto;
          $adjunto->save();

          $codigo_adjunto = $adjunto->codigo_adjunto;
          $codigo_phase = $phase->codigo_fase;

          $resourcePhase = new AdjuntoFase();
          $resourcePhase->codigo_estado = Estado::getState('active');
          $resourcePhase->codigo_adjunto = $codigo_adjunto;
          $resourcePhase->codigo_fase = $codigo_phase;
          $resourcePhase->codigo_tipo_recurso_fase = $request->tipo_recurso_phase;
          $resourcePhase->descripcion_adjunto_fase = $request->descripcion_adjunto_fase;
          $resourcePhase->save();

          DB::commit();
          return response()->json(['success' => true, 'message' => trans('app.rest.success',['resource' => 'El recurso', 'event' => 'añadida']), 'codigo_fase' => $phase->codigo_fase, 'redirect' => true]);
        } catch (\Exception $e) {
          DB::rollback();
          Log::error(sprintf('%s:%s', 'RecursoFase:store', $e->getMessage()));
          return response()->json(['success' => false, 'errors' => trans('app.exception')]);
        }
      }
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
        if ($request->ajax()) {
          $phase = Fase::find($request->codigo_fase);
          if (!$phase instanceof Fase) {
            throw new \Exception("Fase no encontrada {$request->codigo_fase}");
          }

          $adjunto = Adjunto::find($id);
          if (!$adjunto instanceof Adjunto) {
            throw new \Exception("Adjunto no encontrado {$id}");
          }

          DB::beginTransaction();
          try {
            if ($request->has('id_resource_phase') && isset($request->id_resource_phase) && !empty($request->id_resource_phase)) {
              if ($request->id_resource_phase != "") {
//                dd($request->all());
                Adjunto::where('codigo_adjunto', $request->id_resource_phase)
                  ->update(['direccion_adjunto' => $request->direccion_adjunto,
                    'nombre_adjunto' => $request->nombre_adjunto,
                    'codigo_tipo_adjunto' => $request->tipo_adjunto_phase]);

                AdjuntoFase::where('codigo_adjunto', $request->id_resource_phase)
                  ->update(['codigo_tipo_recurso_fase' => $request->tipo_recurso_phase,
                    'descripcion_adjunto_fase' => $request->descripcion_adjunto_fase]);
              }
            }

            DB::commit();
            return response()->json(['success' => true, 'message' => trans('app.rest.success',['resource' => 'La fase', 'event' => 'editada']), 'codigo_fase' => $phase->codigo_fase]);
          } catch (\Exception $e) {
            DB::rollback();
            Log::error(sprintf('%s:%s', 'RecursoFase:update', $e->getMessage()));
            return response()->json(['success' => false, 'errors' => trans('app.exception')]);
          }
        }
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
