<?php

namespace App\Http\Controllers;

use App\Models\Core\EtapaContEvent;
use App\Models\Core\Evento;
use Illuminate\Http\Request;
use DB;

class EventoEtapaController extends Controller
{
    /**
     * Display a listing of the resource.
     *
     * @return \Illuminate\Http\Response
     */
    public function index()
    {
        //
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
          DB::beginTransaction();
          try {
            $stage = $request->data['option_id'];

            $event = Evento::find($request->data['data_id']);
            if (!$event instanceof Evento) {
              throw new \Exception("Evento no encontrado {$request->data['data_id']}");
            }

            $eventStage = new EtapaContEvent();
            $eventStage->codigo_etapa_contenido = $stage;
            $eventStage->codigo_evento = $request->data['data_id'];
            $eventStage->save();

            DB::commit();
            return response()->json(['success' => true, 'message' => 'La etapa ha sido añadida correctamente!!', 'codigo_evento' => $request->data['data_id']]);
          } catch (\Exception $e) {
            DB::rollback();
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
        //
    }

    /**
     * Remove the specified resource from storage.
     *
     * @param  int  $id
     * @return \Illuminate\Http\Response
     */
    public function destroy(Request $request,$id)
    {
      if ($request->ajax()) {
        DB::beginTransaction();
        try {
          $stage = $request->etapa;

          $event = Evento::find($request->data['data_id']);
          if (!$event instanceof Evento) {
            throw new \Exception("Evento no encontrado {$request->data['data_id']}");
          }

          EtapaContEvent::where('codigo_evento', $request->data['data_id'])
            ->where('codigo_etapa_contenido', $stage)
            ->delete();

          DB::commit();
          return response()->json(['success' => true, 'message' => 'La etapa ha sido desvinculada correctamente!!', 'codigo_evento' => $request->data['data_id']]);
        } catch (\Exception $e) {
          DB::rollback();
          return response()->json(['success' => false, 'errors' => trans('app.exception')]);
        }
      }
    }
}
