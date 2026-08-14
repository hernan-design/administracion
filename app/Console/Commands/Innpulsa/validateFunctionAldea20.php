<?php

namespace App\Console\Commands\Innpulsa;

use Illuminate\Console\Command;

use Custom\Innpulsa;

use App\Models\Core\RegistroFaseUsuario, 
App\Models\Core\Estado, 
App\Models\Core\RespuestaEncuesta, 
App\Models\Core\RegistroConvocatoria, 
App\Models\Core\UsuarioEncuesta, 
App\Models\Param\Institucion,
App\Models\Core\Voucher;

use DB, SendEmail, Log;

class validateFunctionAldea20 extends Command
{
    /**
     * The name and signature of the console command.
     *
     * @var string
     */
    protected $signature = 'innpulsa:validatealdea20';

    /**
     * The console command description.
     *
     * @var string
     */
    protected $description = 'Validar las funciones del score de la convocatoria';

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
    public function handle()
    {
        $query = RegistroConvocatoria::query();
        $query->select('registro_convocatoria.numero_registro_convocatoria', 'registro_convocatoria.codigo_usuario_encuesta', 'registro_convocatoria.codigo_registro_convocatoria');
        $query->join('core.registro_fase_usuario as rfu', 'registro_convocatoria.codigo_registro_convocatoria', '=', 'rfu.codigo_registro_convocatoria');
        $query->where('registro_convocatoria.codigo_convocatoria', '=', 504);
        $query->where('rfu.codigo_fase', '=', 255);
        $query->where('rfu.codigo_estado', '=', 25);
		// $query->where('respuesta_encuesta.codigo_estado', Estado::getState('active'));
        $query->whereNotNull('registro_convocatoria.codigo_usuario_encuesta');
        $query->orderBy('registro_convocatoria.numero_registro_convocatoria', 'ASC'); 
        $aplicaciones = $query->get();
        
        foreach($aplicaciones as $aplicacion){
            $codigo_usuario_encuesta = $aplicacion->codigo_usuario_encuesta;
            $numero_registro_convocatoria = $aplicacion->numero_registro_convocatoria;
            $codigo_registro_convocatoria = $aplicacion->codigo_registro_convocatoria;
            // $cantidad_hombres_aplicacion = $this->validarGenero($codigo_usuario_encuesta);
            // if($cantidad_hombres_aplicacion == 0){
            //     \Log::error('Numero de hombres '.$numero_registro_convocatoria);
            // }
            
            $cantidad_perfiles = $this->validarHabilidades($codigo_usuario_encuesta);
            if($cantidad_perfiles >= 3){
                \Log::error('Numero de perfiles '.$cantidad_perfiles.' - '.$numero_registro_convocatoria);
            }
        }
        dd("Finalizo - ".count($aplicaciones));
    }

    public function validarGenero($codigo_usuario_encuesta){
        $query = RespuestaEncuesta::query();
		$query->select(DB::raw('COUNT(respuesta_encuesta.texto_respuesta_encuesta::INTEGER) as gender'));
		$query->join('core.pregunta_encuesta', 'respuesta_encuesta.codigo_pregunta_encuesta', '=', 'pregunta_encuesta.codigo_pregunta_encuesta');
		$query->join('core.opcion_encuesta', 'opcion_encuesta.codigo_opcion_encuesta', '=', 'respuesta_encuesta.codigo_opcion_encuesta');
		$query->where('respuesta_encuesta.codigo_usuario_encuesta', '=', $codigo_usuario_encuesta);
		$query->where('respuesta_encuesta.codigo_estado', Estado::getState('active'));
		$query->where('respuesta_encuesta.codigo_pregunta_encuesta', '=', 4373);
		$query->where('respuesta_encuesta.codigo_opcion_encuesta', '=', 7252);
		$query->whereIn('respuesta_encuesta.cor_codigo_opcion_encuesta', [7269, 7270, 7271, 7272]);
		$query->where(DB::raw('respuesta_encuesta.texto_respuesta_encuesta::varchar(255)'), '=', '85');
		$data = $query->first();
		$hombres = 0;
		if($data->gender > 0 && $data->gender != ""){
            $hombres = $data->gender;
		}

		return $hombres;
    }

    public function validarHabilidades($codigo_usuario_encuesta){

        $cantidad_gerenciales = 0;
        $cantidad_comerciales = 0;
        $cantidad_creativas = 0;
        $cantidad_tecnicas = 0;
        $total_obligatorias = 0;
        $total_opcionales = 0;

        $gerenciales = RespuestaEncuesta::query();
		$gerenciales->select(DB::raw('COUNT(respuesta_encuesta.texto_respuesta_encuesta::INTEGER) as cantidad'));
		$gerenciales->join('core.pregunta_encuesta', 'respuesta_encuesta.codigo_pregunta_encuesta', '=', 'pregunta_encuesta.codigo_pregunta_encuesta');
		$gerenciales->join('core.opcion_encuesta', 'opcion_encuesta.codigo_opcion_encuesta', '=', 'respuesta_encuesta.codigo_opcion_encuesta');
		$gerenciales->where('respuesta_encuesta.codigo_usuario_encuesta', '=', $codigo_usuario_encuesta);
		$gerenciales->where('respuesta_encuesta.codigo_estado', Estado::getState('active'));
		$gerenciales->where('respuesta_encuesta.codigo_pregunta_encuesta', '=', 4373);
		$gerenciales->where('respuesta_encuesta.codigo_opcion_encuesta', '=', 7263);
		$gerenciales->whereIn('respuesta_encuesta.cor_codigo_opcion_encuesta', [7269,7270,7271, 7272]);
		$gerenciales->whereIn(DB::raw('respuesta_encuesta.texto_respuesta_encuesta::varchar(255)'), ['108']);
        $i1 = $gerenciales->first(); 

        if($i1->cantidad > 0 && $i1->cantidad != ""){
            $cantidad_gerenciales = 1;
		}
        
        $comerciales = RespuestaEncuesta::query();
		$comerciales->select(DB::raw('COUNT(respuesta_encuesta.texto_respuesta_encuesta::INTEGER) as cantidad'));
		$comerciales->join('core.pregunta_encuesta', 'respuesta_encuesta.codigo_pregunta_encuesta', '=', 'pregunta_encuesta.codigo_pregunta_encuesta');
		$comerciales->join('core.opcion_encuesta', 'opcion_encuesta.codigo_opcion_encuesta', '=', 'respuesta_encuesta.codigo_opcion_encuesta');
		$comerciales->where('respuesta_encuesta.codigo_usuario_encuesta', '=', $codigo_usuario_encuesta);
		$comerciales->where('respuesta_encuesta.codigo_estado', Estado::getState('active'));
		$comerciales->where('respuesta_encuesta.codigo_pregunta_encuesta', '=', 4373);
		$comerciales->where('respuesta_encuesta.codigo_opcion_encuesta', '=', 7263);
		$comerciales->whereIn('respuesta_encuesta.cor_codigo_opcion_encuesta', [7269,7270,7271, 7272]);
		$comerciales->whereIn(DB::raw('respuesta_encuesta.texto_respuesta_encuesta::varchar(255)'), ['112']);
        $i2 = $comerciales->first(); 

        if($i2->cantidad > 0 && $i2->cantidad != ""){
            $cantidad_comerciales = 1;
        }
        
        $total_obligatorias = $cantidad_gerenciales + $cantidad_comerciales;
        
        $creativas = RespuestaEncuesta::query();
		$creativas->select(DB::raw('COUNT(respuesta_encuesta.texto_respuesta_encuesta::INTEGER) as cantidad'));
		$creativas->join('core.pregunta_encuesta', 'respuesta_encuesta.codigo_pregunta_encuesta', '=', 'pregunta_encuesta.codigo_pregunta_encuesta');
		$creativas->join('core.opcion_encuesta', 'opcion_encuesta.codigo_opcion_encuesta', '=', 'respuesta_encuesta.codigo_opcion_encuesta');
		$creativas->where('respuesta_encuesta.codigo_usuario_encuesta', '=', $codigo_usuario_encuesta);
		$creativas->where('respuesta_encuesta.codigo_estado', Estado::getState('active'));
		$creativas->where('respuesta_encuesta.codigo_pregunta_encuesta', '=', 4373);
		$creativas->where('respuesta_encuesta.codigo_opcion_encuesta', '=', 7263);
		$creativas->whereIn('respuesta_encuesta.cor_codigo_opcion_encuesta', [7269,7270,7271, 7272]);
		$creativas->whereIn(DB::raw('respuesta_encuesta.texto_respuesta_encuesta::varchar(255)'), ['116']);
        $i3 = $creativas->first();

        if($i3->cantidad > 0 && $i3->cantidad != ""){
            $cantidad_creativas = 1;
		}
        
        $tecnicas = RespuestaEncuesta::query();
		$tecnicas->select(DB::raw('COUNT(respuesta_encuesta.texto_respuesta_encuesta::INTEGER) as cantidad'));
		$tecnicas->join('core.pregunta_encuesta', 'respuesta_encuesta.codigo_pregunta_encuesta', '=', 'pregunta_encuesta.codigo_pregunta_encuesta');
		$tecnicas->join('core.opcion_encuesta', 'opcion_encuesta.codigo_opcion_encuesta', '=', 'respuesta_encuesta.codigo_opcion_encuesta');
		$tecnicas->where('respuesta_encuesta.codigo_usuario_encuesta', '=', $codigo_usuario_encuesta);
		$tecnicas->where('respuesta_encuesta.codigo_estado', Estado::getState('active'));
		$tecnicas->where('respuesta_encuesta.codigo_pregunta_encuesta', '=', 4373);
		$tecnicas->where('respuesta_encuesta.codigo_opcion_encuesta', '=', 7263);
		$tecnicas->whereIn('respuesta_encuesta.cor_codigo_opcion_encuesta', [7269,7270,7271, 7272]);
		$tecnicas->whereIn(DB::raw('respuesta_encuesta.texto_respuesta_encuesta::varchar(255)'), ['113']);
        $i4 = $tecnicas->first();
        
        if($i4->cantidad > 0 && $i4->cantidad != ""){
            $cantidad_tecnicas = 1;
        }

        if($cantidad_creativas > 0){
            $total_opcionales = 1;
        }else if($cantidad_tecnicas > 0){
            $total_opcionales = 1;
        }
        
        $total_perfiles = $total_obligatorias + $total_opcionales;

        return $total_perfiles;
    }

    public static function getRolesAldea20__three__moment__equipo($register) {
		$query = RespuestaEncuesta::query();
		$query->select(DB::raw('COUNT(respuesta_encuesta.texto_respuesta_encuesta::INTEGER) as cantidad'));
		$query->join('core.pregunta_encuesta', 'respuesta_encuesta.codigo_pregunta_encuesta', '=', 'pregunta_encuesta.codigo_pregunta_encuesta');
		$query->join('core.opcion_encuesta', 'opcion_encuesta.codigo_opcion_encuesta', '=', 'respuesta_encuesta.codigo_opcion_encuesta');
		$query->where('respuesta_encuesta.codigo_usuario_encuesta', '=', $register);
		$query->where('respuesta_encuesta.codigo_estado', Estado::getState('active'));
		$query->where('respuesta_encuesta.codigo_pregunta_encuesta', '=', 4373);
		$query->where('respuesta_encuesta.codigo_opcion_encuesta', '=', 7263);
		$query->whereIn('respuesta_encuesta.cor_codigo_opcion_encuesta', [7269,7270, 7271, 7272]);
		$query->whereIn(DB::raw('respuesta_encuesta.texto_respuesta_encuesta::varchar(255)'), ['108', '112', '116', '113']);
		$data = $query->first();

		if ($data->cantidad >= 3) {
			$score = '2.00';
		} else if ($data->cantidad == 2) {
			$score = '1.00';
		} else {
			$score = '0.0';
		}

		return $score;
	}
}
