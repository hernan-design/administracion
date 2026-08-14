<?php

namespace App\Console\Commands;

use Illuminate\Console\Command;

use App\Models\Core\Encuesta, App\Models\Core\EncuestaInstitucion, App\Models\Core\PreguntaEncuesta, App\Models\Core\OpcionPreguntaEncuesta, App\Models\Core\SubpreguntaEncuesta, App\Models\Core\VersionFormulario, App\Models\Core\VerPregEncuesta, App\Models\Core\GrupoPreguntaEncuesta, App\Models\Core\PaginaEncuesta;

use DB;

class DuplicateSurvey extends Command
{
    /**
     * The name and signature of the console command.
     *
     * @var string
     */
    protected $signature = 'admin:duplicatesurvey';

    /**
     * The console command description.
     *
     * @var string
     */
    protected $description = 'This command duclicate a survey';

    /**
     * Create a new command instance.
     *
     * @return void
     */
    public function __construct()
    {
        parent::__construct();
    }

    /**
     * Execute the console command.
     *
     * @return mixed
     */


    public function updatePagesSurvey(){
        $grupos_preguntas = GrupoPreguntaEncuesta::select('grupo_pregunta_encuesta.pagina_grupo_pregunta','grupo_pregunta_encuesta.codigo_encuesta')
        ->whereNotNull('grupo_pregunta_encuesta.pagina_grupo_pregunta')
        ->join('core.encuesta as e','grupo_pregunta_encuesta.codigo_encuesta','=','e.codigo_encuesta')
        ->groupBy('grupo_pregunta_encuesta.pagina_grupo_pregunta','grupo_pregunta_encuesta.codigo_encuesta')
        ->orderBy('grupo_pregunta_encuesta.codigo_encuesta','ASC')
        ->orderBy('grupo_pregunta_encuesta.pagina_grupo_pregunta','ASC')        
        ->get();

        foreach($grupos_preguntas as $grupo_preguntas){            
            $pagina = new PaginaEncuesta;
            $pagina->codigo_encuesta = $grupo_preguntas->codigo_encuesta;
            $pagina->pagina_grupo_pregunta = $grupo_preguntas->pagina_grupo_pregunta;
            $pagina->save();
            $codigo_pagina = $pagina->codigo_pagina_encuesta;
            
            GrupoPreguntaEncuesta::where('grupo_pregunta_encuesta.pagina_grupo_pregunta', '=', $grupo_preguntas->pagina_grupo_pregunta)->where('grupo_pregunta_encuesta.codigo_encuesta', '=', $grupo_preguntas->codigo_encuesta)->update(
				array(
					'codigo_pagina_encuesta' => $codigo_pagina
				)
			);

            \Log::info("Grupo: ".$grupo_preguntas->codigo_encuesta." -> ".$grupo_preguntas->pagina_grupo_pregunta);
        }
        
        dd("Finalizo");
    }

    public function handle()
    {        
        $this->duplicateSurvey();
    }

    public function testSurvey(){
        \Log::info("Esta es la prueba de test");
        dd("Finalizo");
    }

    public function duplicateSurvey(){
        $codigo_encuesta = 117;
        $nombre_nueva_encuesta = "Autodiagnóstico empresas preseleccionadas Magdalena";
        $url_encuesta = "autodiagnostico-magdalena-2020";

        $encuesta = Encuesta::find($codigo_encuesta);        
        if($encuesta instanceof Encuesta){
            // Inserto la encuesta
            $nueva_encuesta = new Encuesta;
            $nueva_encuesta->codigo_estado = $encuesta->codigo_estado;
            $nueva_encuesta->codigo_usuario = $encuesta->codigo_usuario;
            $nueva_encuesta->codigo_tipo_encuesta = $encuesta->codigo_tipo_encuesta;
            $nueva_encuesta->nombre_encuesta = $nombre_nueva_encuesta;
            $nueva_encuesta->descripcion_encuesta = $encuesta->descripcion_encuesta;
            $nueva_encuesta->fecha_encuesta = date('Y-m-d');
            $nueva_encuesta->hora_encuesta = date('H:i:s');
            $nueva_encuesta->codigo_institucion = $encuesta->codigo_institucion;
            $nueva_encuesta->codigo_orientacion_encuesta = $encuesta->codigo_orientacion_encuesta;
            $nueva_encuesta->codigo_surveygizmo = $encuesta->codigo_surveygizmo;
            $nueva_encuesta->name_surveygizmo = $encuesta->name_surveygizmo;
            $nueva_encuesta->keyname_surveygizmo = $encuesta->keyname_surveygizmo;
            $nueva_encuesta->clase_encuesta = $encuesta->clase_encuesta;
            $nueva_encuesta->url_encuesta = $url_encuesta;
            $nueva_encuesta->paginacion_encuesta = $encuesta->paginacion_encuesta;
            $nueva_encuesta->label_boton_finalizar_encuesta = $encuesta->label_boton_finalizar_encuesta;
            $nueva_encuesta->color_fondo_encuesta = $encuesta->color_fondo_encuesta;
            $nueva_encuesta->color_encuesta = $encuesta->color_encuesta;
            $nueva_encuesta->respuesta_encuesta = $encuesta->respuesta_encuesta;
            $nueva_encuesta->imagen_encuesta = $encuesta->imagen_encuesta;
            $nueva_encuesta->codigo_privacidad_contenido = $encuesta->codigo_privacidad_contenido;
            $nueva_encuesta->editar_encuesta = $encuesta->editar_encuesta;
            $nueva_encuesta->preguardado_encuesta = $encuesta->preguardado_encuesta;
            $nueva_encuesta->cancelar_encuesta = $encuesta->cancelar_encuesta;
            $nueva_encuesta->tipo_paginacion_encuesta = $encuesta->tipo_paginacion_encuesta;            
            $nueva_encuesta->tipo_porcentaje_avance = $encuesta->tipo_porcentaje_avance;            
            $nueva_encuesta->redirect_encuesta = $encuesta->redirect_encuesta;            
            $nueva_encuesta->save();
            $codigo_nueva_encuesta = $nueva_encuesta->codigo_encuesta;
            // Fin inserto la encuesta
            $encuesta_insti_actual = EncuestaInstitucion::where('codigo_encuesta',$codigo_encuesta)->first();
            if($encuesta_insti_actual instanceof EncuestaInstitucion){
                // Inserto encuesta institucion
                $nueva_version = new EncuestaInstitucion;
                $nueva_version->codigo_institucion =  $encuesta_insti_actual->codigo_institucion;
                $nueva_version->codigo_encuesta = $codigo_nueva_encuesta;
                $nueva_version->codigo_estado = $encuesta_insti_actual->codigo_estado;
                $nueva_version->fecha_encuesta_institucion = date('Y-m-d');
                $nueva_version->redirect_encuesta = $encuesta_insti_actual->redirect_encuesta;                
                $nueva_version->save();                
                // Fin inserto encuesta institucion
            }
            $version_actual = VersionFormulario::where('codigo_version_encuesta',$encuesta->codigo_version_encuesta)->first();
            // Inserto version de la encuesta
            $nueva_version = new VersionFormulario;
            $nueva_version->codigo_encuesta =  $codigo_nueva_encuesta;
            $nueva_version->codigo_estado = $version_actual->codigo_estado;
            $nueva_version->fecha_version_encuesta = date('Y-m-d');
            $nueva_version->comentario_version_encuesta = $version_actual->comentario_version_encuesta;
            $nueva_version->version_encuesta_editable = $version_actual->version_encuesta_editable;            
            $nueva_version->save();
            $codigo_nueva_version = $nueva_version->codigo_version_encuesta;

            $encuesta_nueva = Encuesta::find($codigo_nueva_encuesta);
            $encuesta_nueva->codigo_version_encuesta = $codigo_nueva_version;
            $encuesta_nueva->save();

            $grupos_preguntas = GrupoPreguntaEncuesta::where('codigo_encuesta',$codigo_encuesta)->where('codigo_estado',8)->get();
            foreach($grupos_preguntas as $grupo_preguntas){
                $nuevo_grupo = new GrupoPreguntaEncuesta;
                $nuevo_grupo->codigo_encuesta =  $codigo_nueva_encuesta;
                $nuevo_grupo->nombre_grupo_pregunta_encuesta = $grupo_preguntas->nombre_grupo_pregunta_encuesta;
                $nuevo_grupo->desc_grupo_pregunta_encuesta = $grupo_preguntas->desc_grupo_pregunta_encuesta;
                $nuevo_grupo->pagina_grupo_pregunta = $grupo_preguntas->pagina_grupo_pregunta;
                $nuevo_grupo->codigo_estado = 8;            
                $nuevo_grupo->save();
            }
            
            // Fin inserto version de  la encuesta
            $preguntas_version_actual = VerPregEncuesta::where('codigo_version_encuesta',$encuesta->codigo_version_encuesta)->where('codigo_estado',8)->get();
            foreach($preguntas_version_actual as $pregunta_version_actual){
                $pregunta_actual = PreguntaEncuesta::find($pregunta_version_actual->codigo_pregunta_encuesta);
                // Inserto preguntas y preguntas en version de la encuesta
                $nueva_pregunta = new PreguntaEncuesta;
                $nueva_pregunta->codigo_encuesta =  $codigo_nueva_encuesta;
                $nueva_pregunta->codigo_estado =  $pregunta_actual->codigo_estado;
                $nueva_pregunta->tipo_pregunta_encuesta =  $pregunta_actual->tipo_pregunta_encuesta;
                $nueva_pregunta->texto_pregunta_encuesta =  $pregunta_actual->texto_pregunta_encuesta;
                $nueva_pregunta->numero_pregunta_encuesta =  $pregunta_actual->numero_pregunta_encuesta;
                $nueva_pregunta->opcional_pregunta_encuesta =  $pregunta_actual->opcional_pregunta_encuesta;
                $nueva_pregunta->texto_opcional_pregunta =  $pregunta_actual->texto_opcional_pregunta;
                $nueva_pregunta->clase_pregunta_encuesta_v1 =  $pregunta_actual->clase_pregunta_encuesta_v1;
                $nueva_pregunta->maximo_respuestas =  $pregunta_actual->maximo_respuestas;
                $nueva_pregunta->minimo_respuestas =  $pregunta_actual->minimo_respuestas;
                $nueva_pregunta->obligatoria_pregunta =  $pregunta_actual->obligatoria_pregunta;
                $nueva_pregunta->tipo_campo_pregunta =  $pregunta_actual->tipo_campo_pregunta;
                $nueva_pregunta->tipo_opcion_pregunta =  $pregunta_actual->tipo_opcion_pregunta;
                $nueva_pregunta->subpregunta_pregunta =  $pregunta_actual->subpregunta_pregunta;
                $grupo_antiguo = GrupoPreguntaEncuesta::where('codigo_grupo_pregunta_encuesta',$pregunta_actual->codigo_grupo_pregunta_encuesta)->first();
                $grupo_nuevo = GrupoPreguntaEncuesta::where('nombre_grupo_pregunta_encuesta',$grupo_antiguo->nombre_grupo_pregunta_encuesta)->where('codigo_encuesta',$codigo_nueva_encuesta)->first();
                if($grupo_nuevo instanceof GrupoPreguntaEncuesta)
                    $nueva_pregunta->codigo_grupo_pregunta_encuesta =  $grupo_nuevo->codigo_grupo_pregunta_encuesta;
                $nueva_pregunta->ancho_pregunta_encuesta =  $pregunta_actual->ancho_pregunta_encuesta;
                $nueva_pregunta->alto_pregunta_encuesta =  $pregunta_actual->alto_pregunta_encuesta;
                $nueva_pregunta->visibilidad_pregunta_encuesta =  $pregunta_actual->visibilidad_pregunta_encuesta;
                $nueva_pregunta->longitud_pregunta_encuesta =  $pregunta_actual->longitud_pregunta_encuesta;
                $nueva_pregunta->requisito_creacion_encuesta =  $pregunta_actual->requisito_creacion_encuesta;
                $nueva_pregunta->numero_orden_pregunta_encuesta =  $pregunta_actual->numero_orden_pregunta_encuesta;
                $nueva_pregunta->codigo_moneda =  $pregunta_actual->codigo_moneda;
                $nueva_pregunta->puntaje_pregunta_encuesta =  $pregunta_actual->puntaje_pregunta_encuesta;
                $nueva_pregunta->periodo_pregunta_encuesta =  $pregunta_actual->periodo_pregunta_encuesta;
                $nueva_pregunta->fecha_visibilidad_pregunta =  $pregunta_actual->fecha_visibilidad_pregunta;
                $nueva_pregunta->titulo_opciones_pregunta =  $pregunta_actual->titulo_opciones_pregunta;                
                $nueva_pregunta->save();
                $codigo_nueva_pregunta = $nueva_pregunta->codigo_pregunta_encuesta;

                $nueva_ver_pregunta = new VerPregEncuesta;
                $nueva_ver_pregunta->codigo_version_encuesta =  $codigo_nueva_version;
                $nueva_ver_pregunta->codigo_pregunta_encuesta =  $codigo_nueva_pregunta;
                $nueva_ver_pregunta->codigo_estado =  $pregunta_version_actual->codigo_estado;
                $nueva_ver_pregunta->numero_pregunta_version =  $pregunta_version_actual->numero_pregunta_version;
                $nueva_ver_pregunta->clase_pregunta_encuesta =  $pregunta_version_actual->clase_pregunta_encuesta;
                $nueva_ver_pregunta->save();

                // Fin inserto preguntas y preguntas en version de la encuesta
                if($pregunta_actual instanceof PreguntaEncuesta){
                    $opciones_actuales = OpcionPreguntaEncuesta::where('codigo_pregunta_encuesta', $pregunta_version_actual->codigo_pregunta_encuesta)->get();
                    foreach($opciones_actuales as $opcion_actual){
                        // Inserto opciones nueva pregunta
                        $nueva_opc_pregunta = new OpcionPreguntaEncuesta;
                        $nueva_opc_pregunta->codigo_estado =  $opcion_actual->codigo_estado;
                        $nueva_opc_pregunta->codigo_pregunta_encuesta =  $codigo_nueva_pregunta;
                        $nueva_opc_pregunta->texto_opcion_encuesta =  $opcion_actual->texto_opcion_encuesta;
                        $nueva_opc_pregunta->complemento =  $opcion_actual->complemento;
                        $nueva_opc_pregunta->todas =  $opcion_actual->todas;
                        $nueva_opc_pregunta->ninguna =  $opcion_actual->ninguna;
                        $nueva_opc_pregunta->visibilidad_opcion =  $opcion_actual->visibilidad_opcion;
                        $nueva_opc_pregunta->eje_opcion =  $opcion_actual->eje_opcion;
                        $nueva_opc_pregunta->calculado_opcion_encuesta =  $opcion_actual->calculado_opcion_encuesta;
                        $nueva_opc_pregunta->formula_opcion_encuesta =  $opcion_actual->formula_opcion_encuesta;
                        $nueva_opc_pregunta->orden_opcion_encuesta =  $opcion_actual->orden_opcion_encuesta;
                        $nueva_opc_pregunta->referencia_opcion_encuesta =  $opcion_actual->referencia_opcion_encuesta;
                        $nueva_opc_pregunta->tipo_campo_opcion =  $opcion_actual->tipo_campo_opcion;
                        $nueva_opc_pregunta->puntaje_opcion_encuesta =  $opcion_actual->puntaje_opcion_encuesta;
                        $nueva_opc_pregunta->equivalente_tabla =  $opcion_actual->equivalente_tabla;
                        $nueva_opc_pregunta->save();

                        $codigo_opcion_nueva = $nueva_opc_pregunta->codigo_opcion_encuesta;
                        // Fin Inserto opciones nueva pregunta

                        $subpreguntas_actuales = SubpreguntaEncuesta::where('codigo_opcion_encuesta', $opcion_actual->codigo_opcion_encuesta)->get();
                        foreach($subpreguntas_actuales as $subpregunt_actual){
                            $pregunta_hijo_antigua = PreguntaEncuesta::find($subpregunt_actual->codigo_pregunta_encuesta);
                            $pregunta_hijo_nueva = PreguntaEncuesta::where('texto_pregunta_encuesta','=',$pregunta_hijo_antigua->texto_pregunta_encuesta)->where('codigo_encuesta',$codigo_nueva_encuesta)->first();

                            // Inserto subpreguntas nueva pregunta
                            $nueva_subpre_pregunta = new SubpreguntaEncuesta;
                            if($pregunta_hijo_nueva instanceof PreguntaEncuesta)
                                $nueva_subpre_pregunta->codigo_pregunta_encuesta =  $pregunta_hijo_nueva->codigo_pregunta_encuesta;
                            $nueva_subpre_pregunta->cor_codigo_pregunta_encuesta =  $codigo_nueva_pregunta;
                            $nueva_subpre_pregunta->codigo_estado = $subpregunt_actual->codigo_estado;
                            $nueva_subpre_pregunta->codigo_opcion_encuesta = $codigo_opcion_nueva;
                            $nueva_subpre_pregunta->visibilidad_subpregunta_encuesta = $subpregunt_actual->visibilidad_subpregunta_encuesta;
                            $nueva_subpre_pregunta->codigo_tipo_cont_institucion = $subpregunt_actual->codigo_tipo_cont_institucion;                        
                            $nueva_subpre_pregunta->orden_subpregunta_encuesta = $subpregunt_actual->orden_subpregunta_encuesta;
                            $nueva_subpre_pregunta->codigo_version_encuesta =  $codigo_nueva_version;                        
                            $nueva_subpre_pregunta->save();
                            // Fin Inserto subpreguntas nueva pregunta
                        }     
                    }                    
                }            
                \Log::info("Insertio la pregunta: ".$pregunta_actual->texto_pregunta_encuesta);
            }
            \Log::info("finalizo");
        }          
        dd("Duplica encuestas");
    }
}
