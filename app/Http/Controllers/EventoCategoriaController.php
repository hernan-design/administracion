<?php

namespace App\Http\Controllers;

use App\Models\Core\CatContentEvento;
use App\Models\Core\Evento;
use Illuminate\Http\Request;
use DB;

class EventoCategoriaController extends Controller
{
    /**
     * Display a listing of the resource.
     *
     * @return \Illuminate\Http\Response
     */
    public function index(Request $request)
    {
        if ($request->ajax()) {
          dd('Hola');
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
        if ($request->ajax()) {
          DB::beginTransaction();
          try {
            $category = $request->data['option_id'];

            $event = Evento::find($request->data['data_id']);
            if (!$event instanceof Evento) {
              throw new \Exception("Evento no encontrado {$request->data['data_id']}");
            }

            $eventCategory = new CatContentEvento();
            $eventCategory->codigo_evento = $request->data['data_id'];
            $eventCategory->codigo_categoria_contenido = $category;
            $eventCategory->save();

            DB::commit();
            return response()->json(['success' => true, 'message' => 'La categoria ha sido añadida correctamente!!', 'codigo_evento' => $request->data['data_id']]);
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
    public function destroy(Request $request, $id)
    {
      if ($request->ajax()) {
        DB::beginTransaction();
        try {
          $category = $request->categoria;

          $event = Evento::find($request->data['data_id']);
          if (!$event instanceof Evento) {
            throw new \Exception("Evento no encontrado {$request->data['data_id']}");
          }

          CatContentEvento::where('codigo_evento', $request->data['data_id'])
            ->where('codigo_categoria_contenido', $category)
            ->delete();

          DB::commit();
          return response()->json(['success' => true, 'message' => 'La categoria ha sido desvinculada correctamente!!', 'codigo_evento' => $request->data['data_id']]);
        } catch (\Exception $e) {
          DB::rollback();
          return response()->json(['success' => false, 'errors' => trans('app.exception')]);
        }
      }
    }
}
