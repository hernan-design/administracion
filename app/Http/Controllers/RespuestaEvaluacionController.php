<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use TuProyecto, DB;
use Custom\Innpulsa;
use App\Models\Core\RespuestaEncuesta, App\Models\Core\RegistroFaseUsuario, App\Models\Core\Entidad;

class RespuestaEvaluacionController extends Controller
{
    /**
     * Display a listing of the resource.
     *
     * @return \Illuminate\Http\Response
     */
    public function index()
    {
        dd('Entry Index function');
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
        dd('Entra');
    }

    /**
     * Display the specified resource.
     *
     * @param  int  $id
     * @return \Illuminate\Http\Response
     */
    public function show(Request $request, $id)
    {
        // Decrypt src-params
        list($codigo_usuario_encuesta) = TuProyecto::decryptSrcParams($id);
        if(!$codigo_usuario_encuesta || !is_numeric($codigo_usuario_encuesta)) {
            return response()->json(['success' => false, 'errors' => 'That code is not number']);
        }

        list($codigo_registro_fase_usuario) = TuProyecto::decryptSrcParams($request->register);
        if(!$codigo_registro_fase_usuario || !is_numeric($codigo_registro_fase_usuario)) {
            return response()->json(['success' => false, 'errors' => 'That code is not number']);
        }

        $program = $request->program;

        // Innovacion
        $propiedad_innovacion = Innpulsa::getResponseEvaluationAldea20__three__moment__innovacion($codigo_usuario_encuesta, 'propiedad');
        $motivo_innovacion = Innpulsa::getResponseEvaluationAldea20__three__moment__innovacion($codigo_usuario_encuesta, 'motivo');
        $tipo_innovacion = Innpulsa::getResponseEvaluationAldea20__three__moment__innovacion($codigo_usuario_encuesta, 'tipo');
        $calificacion_innovacion = Innpulsa::Calificacion__Aldea20__three_moment__innovacion($codigo_registro_fase_usuario);
        $total_innovacion = ( ( isset($propiedad_innovacion->propiedad_score) ? $propiedad_innovacion->propiedad_score : 0 ) + ( isset($motivo_innovacion->motivo_score) ? $motivo_innovacion->motivo_score : 0 ) + ( isset($tipo_innovacion->tipo_score) ? $tipo_innovacion->tipo_score : 0 ) );

        $total_cal_innovacion = 0;
        if (count($calificacion_innovacion) > 0) {
            foreach($calificacion_innovacion as $cal) {
                $total_cal_innovacion = $total_cal_innovacion + $cal->total_scoring;
            }
        }

        $total_innovacion = $total_innovacion + ($total_cal_innovacion * 0.6);
        $total_innovacion_res = $total_innovacion * 0.3;

        // Escalabilidad

        $idioma_escalabilidad = Innpulsa::getResponseEvaluationAldea20__three__moment__escalabilidad($codigo_usuario_encuesta, 'idioma');
        $metas_escalabilidad = Innpulsa::getResponseEvaluationAldea20__three__moment__escalabilidad($codigo_usuario_encuesta, 'metas');
        $calificacion_escalabilidad = Innpulsa::Calificacion__potential__Aldea20__three_moment__equipo($codigo_registro_fase_usuario);
        $total_escalabilidad = ( ( isset($idioma_escalabilidad->idioma_score) ? $idioma_escalabilidad->idioma_score : 0 ) + ( isset($metas_escalabilidad->metas_score) ? $metas_escalabilidad->metas_score : 0 ) );

        $total_cal_escalabilidad = 0;
        if (count($calificacion_escalabilidad) > 0) {
            foreach($calificacion_escalabilidad as $cal) {
                $total_cal_escalabilidad = $total_cal_escalabilidad + $cal->total_score;
            }
        }

        $total_escalabilidad = $total_escalabilidad + ($total_cal_escalabilidad * 0.8);
        $total_escalabilidad_res = $total_escalabilidad * 0.15;

        // Traccion

        $alcance_traccion = Innpulsa::getResponseEvaluationAldea20__three__moment__traccion($codigo_usuario_encuesta, 'alcance');
        $cuentas_traccion = Innpulsa::getResponseEvaluationAldea20__three__moment__traccion($codigo_usuario_encuesta, 'cuentas');
        $estados_traccion = Innpulsa::getResponseEvaluationAldea20__three__moment__traccion($codigo_usuario_encuesta, 'estados');
        $indicadores_traccion = Innpulsa::getResponseEvaluationAldea20__three__moment__traccion($codigo_usuario_encuesta, 'indicadores');
        $calificacion_traccion = Innpulsa::Calificacion__Aldea20__three_moment__traccion($codigo_registro_fase_usuario);
        $total_traccion = ( ( isset($alcance_traccion->alcance_score) ? $alcance_traccion->alcance_score : 0 ) + ( isset($cuentas_traccion->cuentas_score) ? $cuentas_traccion->cuentas_score : 0 ) + ( isset($estados_traccion->estados_score) ? $estados_traccion->estados_score : 0 ) + ( isset($indicadores_traccion->indicadores_score) ? $indicadores_traccion->indicadores_score : 0 ) );

        $total_cal_traccion = 0;
        if (count($calificacion_traccion) > 0) {
            foreach($calificacion_traccion as $cal_t) {
                $total_cal_traccion += (double)$cal_t->total_scoring;
            }
        }

        $total_traccion = $total_traccion + ( round($total_cal_traccion * 0.45, 2) );
        $total_traccion_res = $total_traccion * 0.1;

        // Equipo
        $experiencia_laboral_equipo = Innpulsa::getResponseEvaluationAldea20__three__moment__equipo($codigo_usuario_encuesta, 'laboral');
        $experiencia_sector_equipo = Innpulsa::getSectorExprerienceAldea20__three__moment__equipo($codigo_usuario_encuesta);
        $experiencia_emprendedora_equipo = Innpulsa::getEmprendorExprerienceAldea20__three__moment__equipo($codigo_usuario_encuesta);
        $roles_equipo = Innpulsa::getRolesAldea20__three__moment__equipo($codigo_usuario_encuesta);
        $genero_equipo = Innpulsa::getGenderAldea20__three__moment__equipo($codigo_usuario_encuesta);
        $riesgo_equipo = Innpulsa::getRiesgoAldea20__three__moment__equipo($codigo_usuario_encuesta);
        $decisiones_equipo = Innpulsa::getResponseEvaluationAldea20__three__moment__equipo($codigo_usuario_encuesta, 'decisiones');
        $calificacion_equipo = Innpulsa::Calificacion__Aldea20__three_moment__equipo($codigo_registro_fase_usuario);
        $total_equipo = ( ( isset($experiencia_laboral_equipo->laboral_score) ? $experiencia_laboral_equipo->laboral_score : 0 ) + ( isset($experiencia_sector_equipo['sector_scoring']) ? $experiencia_sector_equipo['sector_scoring'] : 0 ) + ( isset($experiencia_emprendedora_equipo['emprendor_scoring']) ? $experiencia_emprendedora_equipo['emprendor_scoring'] : 0 ) + ( isset($roles_equipo) ? $roles_equipo : 0 ) + ( isset($genero_equipo) ? $genero_equipo : 0 ) + ( isset($riesgo_equipo->riesgo_score) ? $riesgo_equipo->riesgo_score : 0 ) + ( isset($decisiones_equipo->decisiones_score) ?  $decisiones_equipo->decisiones_score : 0 ) );

        $total_cal_equipo= 0;
        if (count($calificacion_equipo) > 0) {
            foreach($calificacion_equipo as $cal) {
                $total_cal_equipo = $total_cal_equipo + (int)$cal->total_scoring;
            }
        }


        $total_equipo = $total_equipo + ($total_cal_equipo * 0.3);
        $total_equipo_res = $total_equipo * 0.45;

        $total_score = ( $total_equipo_res + $total_traccion_res + $total_escalabilidad_res + $total_innovacion_res );
        $total_score_res = $total_score * 10;

        // Llaves de accesso
        $response_keys = Innpulsa::get__Response__Keys__4501__Aldea20($codigo_usuario_encuesta);
        $response_keys_city = RespuestaEncuesta::select('nombre_ciudad')->join('core.ciudad', 'ciudad.codigo_ciudad', '=', DB::raw('texto_respuesta_encuesta::INTEGER'))->where('codigo_usuario_encuesta', '=', $codigo_usuario_encuesta)->where('codigo_pregunta_encuesta', '=', 4287)->first();
        $response_keys_ciiu = Entidad::select('nombre_sector_interes', 'sector_interes.codigo_sector_interes')->join('core.sector_interes', 'sector_interes.codigo_sector_interes', '=', 'entidad.codigo_sector_interes')->where('codigo_usuario', RegistroFaseUsuario::where('codigo_registro_fase_usuario', $codigo_registro_fase_usuario)->first()->codigo_usuario)->first();
        $balance_keys = Innpulsa::get__Balance__Aldea20($codigo_usuario_encuesta);

        return view('program.response.evaluation',
        compact(
            'propiedad_innovacion', 'motivo_innovacion', 'tipo_innovacion', 'calificacion_innovacion', 'program',
            'idioma_escalabilidad', 'metas_escalabilidad', 'calificacion_escalabilidad',
            'alcance_traccion', 'cuentas_traccion', 'estados_traccion', 'indicadores_traccion', 'calificacion_traccion',
            'experiencia_laboral_equipo', 'experiencia_sector_equipo', 'experiencia_emprendedora_equipo', 'roles_equipo', 'genero_equipo', 'riesgo_equipo', 'decisiones_equipo', 'calificacion_equipo',
            'total_score_res', 'total_equipo_res', 'total_traccion', 'total_traccion_res', 'total_escalabilidad_res', 'total_innovacion_res',
            'response_keys', 'response_keys_city', 'response_keys_ciiu', 'balance_keys'
        ));
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
        // Decrypt src-params
        list($codigo_usuario_encuesta) = TuProyecto::decryptSrcParams($id);
        if(!$codigo_usuario_encuesta || !is_numeric($codigo_usuario_encuesta)) {
            return response()->json(['success' => false, 'errors' => 'That code is not number']);
        }

        DB::beginTransaction();
        try {
            $repuesta_encuesta = RespuestaEncuesta::where('codigo_usuario_encuesta', '=', $codigo_usuario_encuesta)->where('codigo_pregunta_encuesta', '=', 4501)->first();
            $repuesta_encuesta->codigo_opcion_encuesta = $request->capitulo_aldea;
            $repuesta_encuesta->save();

            DB::commit();
            return response()->json(['success' => true, 'message' => 'Llaves editadas correctamente']);
        } catch(\Exception $e){
            DB::rollback();
            Log::error(sprintf('%s:%s', 'ConvocatoriaController:store', $e->getMessage()));
            return response()->json(['success' => false, 'errors' => trans('app.exception')]);
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
