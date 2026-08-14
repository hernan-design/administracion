<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use App\Models\Core\PreguntaFrecuente;
use App\Models\Core\Convocatoria;
use App\Models\Core\Estado;
use DB;
use Exception;

class PreguntasFrecuentesController extends Controller
{
    /**
     * Display a listing of the resource.
     *
     * @return \Illuminate\Http\Response
     */
    public function index(Request $request, $id)
    {
        abort_if(!$request->ajax(), 404);

        $program = Convocatoria::getDataProgram($id);
        abort_if(!$program instanceof Convocatoria, 404);

        return response()->json($program->preguntasFrecuentes()->orderBy('codigo_preguntas_frecuentes', 'ASC')->get(), 200);
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
    public function store(Request $request, $id)
    {
        abort_if(!$request->ajax(), 404);

        $program = Convocatoria::getDataProgram($id);
        abort_if(!$program, 404);

        $request->validate([
            'texto_preguntas_frecuentes' => 'required',
            'respuesta_preguntas_frecuentes' => 'required'
        ]);

        $faq = new PreguntaFrecuente;
        $faq->fill($request->only([
            'texto_preguntas_frecuentes', 'respuesta_preguntas_frecuentes'
        ]));
        $faq->codigo_estado = Estado::getState('active');
        $faq->save();

        $program->preguntasFrecuentes()->attach($faq->codigo_preguntas_frecuentes, [
            'codigo_estado' => Estado::getState('active')
        ]);

        return response()->json([
            'message' => 'Pregunta frecuente creada'
        ], 201);
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
    public function update(Request $request, $id, $faq)
    {
        abort_if(!$request->ajax(), 404);

        $program = Convocatoria::getDataProgram($id);
        abort_if(!$program, 404);

        $faq = $program->preguntasFrecuentes()->where('core.preguntas_frecuentes.codigo_preguntas_frecuentes', $faq)->first();
        abort_if(!$faq, 404);

        $request->validate([
            'texto_preguntas_frecuentes' => 'required',
            'respuesta_preguntas_frecuentes' => 'required'
        ]);

        $faq->update([
            'texto_preguntas_frecuentes' => $request->get('texto_preguntas_frecuentes'),
            'respuesta_preguntas_frecuentes' => $request->get('respuesta_preguntas_frecuentes')
        ]);

        return response()->json([
            'message' => 'Pregunta frecuente actualizada'
        ], 200);
    }

    /**
     * Remove the specified resource from storage.
     *
     * @param  int  $id
     * @return \Illuminate\Http\Response
     */
    public function destroy(Request $request, $program, $faq)
    {
        abort_if(!$request->ajax(), 404);

        $program = Convocatoria::getDataProgram($program);
        abort_if(!$program, 404);

        $faq = $program->preguntasFrecuentes()->where('core.preguntas_frecuentes.codigo_preguntas_frecuentes', $faq)->first();
        abort_if(!$faq, 404);

        $originalStatus = Estado::getNameState($faq->codigo_estado);
        $toChange = $faq->codigo_estado == Estado::getState('active') ? Estado::getState('inactive') : Estado::getState('active');
        $faq->update([
            'codigo_estado' => $toChange
        ]);

        DB::table('core.faq_convocatoria')
            ->where('codigo_preguntas_frecuentes', $faq->codigo_preguntas_frecuentes)
            ->where('codigo_convocatoria', $program->codigo_convocatoria)
            ->update([
                'codigo_estado' => $toChange
            ]);

        $newStatus = Estado::getNameState($toChange);

        return response()->json([
            'message' => "Pregunta frecuente cambiada de {$originalStatus} a {$newStatus}"
        ], 200);
    }
}
