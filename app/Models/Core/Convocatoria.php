<?php

namespace App\Models\Core;

use App\Models\Seguridad\Usuario;
use Illuminate\Database\Eloquent\Model;

use App\Models;

use Datatables;

use Validator, Input, Auth, DB, Lang, App, URL, Request, Cache, Log;

use App\Models\Core\Estado,
	App\Models\Core\UsuarioEncuesta,
	App\Models\Core\RegistroConvocatoria,
	App\Models\Core\RegistroFaseUsuario,
	App\Models\Core\Fase;

class Convocatoria extends Model
{
    /**
	 * The database table used by the model.
	 *
	 * @var string
	 */
    protected $table = 'core.convocatoria';

   	protected $primaryKey = 'codigo_convocatoria';

	public $timestamps = false;

    /**
     * Type program
     *
     * @var array
     */
    public static $type = [1 => 'Interno', 2  => 'Externo'];

	/**
     * The attributes that are mass assignable.
     *
     * @var array
     */
	protected $fillable = ['nombre_convocatoria', 'fecha_cierre_convocatoria', 'tipo_convocatoria', 'resumen_convocatoria', 'imagen_convocatoria', 'descripcion_convocatoria', 'codigo_orientacion_convocatoria', 'terminos_convocatoria', 'politicas_convocatoria', 'url_externa_convocatoria', 'codigo_privacidad_contenido', 'texto_boton', 'contenido_aceptacion_terminos', 'tiempo_espera_nueva_aplicacion', 'codigo_etapa_proyecto'];

	/**
	* Valid create content.
	*/
	public function isValid($data)
	{
		$rules = [
			'nombre_convocatoria'				=> 'required',
			'tipo_convocatoria' 				=> 'required',
			'resumen_convocatoria' 				=> 'required|max:512',
			'imagen_convocatoria' 				=> 'required',
			'descripcion_convocatoria'			=> 'required',
			'codigo_orientacion_convocatoria'	=> 'required',
			'codigo_privacidad_contenido'		=> 'required',
		];

		$validator = Validator::make($data, $rules);
		if ($validator->passes()) {
			return true;
		}
		$this->errors = $validator->errors();
		return false;
	}

	/**
	 * Program types
	 *
	 * @var array
	 */
	public static function getCallType($type) {
		switch ($type):
			case 'internal': return 1;
			case 'external': return 2;
			default: return 'Not type identified';
		endswitch;
	}

	/**
	 * Orientantion Program
	 */
	public static function getOrientation($type){
		switch ($type):
			case 'proyect': return 1;
			case 'person': return 2;
			default: return 'Not type identified';
		endswitch;
	}

	public static function getProgram($program) {
		switch ($program):
			case 'invitation_entrepreneurs': return 113;
			case 'innpulsa_empodera': return 198;
			case 'award_innova': return 121;
			case 'participants_only': return 122;
			case 'empodera': return 198;
			case 'application_proccess_entrepreneurship': return 341;
			case 'reg_mentores_provedores': return 350;
			case 'reg_entidades_provedores': return 351;
			case 'disena_cambio': return 203;
			case 'empodera__2020': return 471;
			case 'aldea__20': return 504;
			case 'terpel__20': return 436;
			case 'propace__course': return  579;
			case 'propace_pfc': return  617;
			case 'propace_pfc_abierto': return  623;
			case 'propace_pfc_abierta_particulares': return  628;
			case 'fase_modelo_negocios_programa_apps_co': return 631;
			case 'pfc_3_0': return 636;
			default: return 'Not program identified';
		endswitch;
	}

	public static function getProgramList()
    {
        $cache = sprintf('_%s_%s', 'program_list', session('institution')->codigo_institucion);
        if (Cache::has($cache)) {
            return Cache::get($cache);
        }

        return Cache::rememberForever($cache, function() {
            $query = Convocatoria::query();
            $query->select('convocatoria.codigo_convocatoria as key', 'nombre_convocatoria as value');
            $query->join('core.convocatoria_institucion', function($join) {
                $join->on('convocatoria.codigo_convocatoria', '=', 'convocatoria_institucion.codigo_convocatoria')
                    ->where('convocatoria_institucion.codigo_institucion', '=', session('institution')->codigo_institucion)
                    ->where('convocatoria_institucion.codigo_estado', '=', Estado::getState('active'));
            });

			$query->where('convocatoria.codigo_estado', '=', Estado::getState('active'));
			$query->where('convocatoria.tipo_convocatoria', '=', Convocatoria::getCallType('internal'));
            $query->orderby('nombre_convocatoria','asc');
            return $query->get();
        });
    }

	public static function getDataProgram($program)
	{
		$query = Convocatoria::query();
		$query->select('convocatoria.*', 'convocatoria_institucion.codigo_estado', 'codigo_cat_proceso_insti');
		$query->join('core.convocatoria_institucion', function($join) {
       		$join->on('convocatoria.codigo_convocatoria', '=', 'convocatoria_institucion.codigo_convocatoria')
				->where('convocatoria_institucion.codigo_institucion', '=', session('institution')->codigo_institucion);
      	});
		$query->where('convocatoria.codigo_convocatoria', '=', $program);
		return $query->first();
	}

	public static function getDataCalls()
	{
		$calls = [];
        $query = Convocatoria::query();
        $query->select('convocatoria.codigo_convocatoria', 'codigo_usuario', 'nombre_convocatoria', 'fecha_creacion_convocatoria', 'descripcion_convocatoria', 'hora_creacion_convocatoria', 'terminos_convocatoria', 'imagen_convocatoria', 'url_convocatoria', 'banner_convocatoria', 'codigo_orientacion_convocatoria', 'url_externa_convocatoria', 'tipo_convocatoria', 'politicas_convocatoria', 'codigo_privacidad_contenido', 'convocatoria_institucion.contador_like', 'contador_inscritos', 'resumen_convocatoria', 'fecha_cierre_convocatoria', 'hora_cierre_convocatoria', 'convocatoria.codigo_institucion', 'texto_boton', 'codigo_encuesta', 'codigo_agrupacion_convocatoria', 'permanente_convocatoria', 'codigo_evaluacion_emprendimiento', 'contenido_aceptacion_terminos', 'visualizacion_usuario', 'evaluacion_convocatoria', 'fase_actual', 'siguiente_fase', 'tiempo_espera_nueva_aplicacion', 'convocatoria_institucion.codigo_estado');

		$query->join('core.convocatoria_institucion', function($join) {
       		$join->on('convocatoria.codigo_convocatoria', '=', 'convocatoria_institucion.codigo_convocatoria')
				->where('convocatoria_institucion.codigo_institucion', '=', session('institution')->codigo_institucion);
      	});
		$query->where('convocatoria.codigo_estado', '=', Estado::getState('active'));

		// Filters
		if(Input::has('nombre_convocatoria')) {
 			$query->whereRaw("nombre_convocatoria ilike '%".trim(Input::get('nombre_convocatoria'))."%'");
 		}

		if(Input::has('tipo_convocatoria')) {
 			$query->where('convocatoria.tipo_convocatoria', Input::get('tipo_convocatoria'));
 		}

		if(Input::has('privacidad_contenido')) {
 			$query->where("convocatoria.codigo_privacidad_contenido", Input::get('privacidad_contenido'));
 		}

		if(Input::has('codigo_estado')) {
 			$query->where("convocatoria_institucion.codigo_estado", Input::get('codigo_estado'));
 		}
		if (session('institution')->contenido_usuario) {
		  $query->where('codigo_usuario', '=', Auth::user()->codigo_usuario);
    }

		// Order
   		//$query->orderBy('convocatoria_institucion.codigo_estado','asc');
		$query->orderBy('fecha_creacion_convocatoria','desc');
		$query->orderBy('hora_creacion_convocatoria','desc');


		// Get total posts
		$total_entries = $query->get()->count();

		$data = $query->skip(Input::get('offset'))->take(Input::get('limit'))->get();

		$response = new \stdClass();
		$response->total_entries = $data->count() > 0 ? $total_entries: 0;
		$response->items = $data;
   		return $response;
	}

	public static function  getDataCallsList()
	{
		$query = Convocatoria::query();
        $query->select('convocatoria.codigo_convocatoria as id', DB::raw('initcap(nombre_convocatoria) as text'));

		$query->join('core.convocatoria_institucion', function($join) {
       		$join->on('convocatoria.codigo_convocatoria', '=', 'convocatoria_institucion.codigo_convocatoria')
				->where('convocatoria_institucion.codigo_institucion', '=', session('institution')->codigo_institucion)
				->where('convocatoria_institucion.codigo_estado', '=', Estado::getState('active'));
      	});

		$query->where('convocatoria.codigo_estado', '=', Estado::getState('active'));

		// Filters
		if(Input::has('q')) {
 			$query->whereRaw("nombre_convocatoria ilike '%".Input::get('q')."%'");
 		}

		// Order
		   $query->orderBy('fecha_creacion_convocatoria','desc');
		   $query->orderBy('hora_creacion_convocatoria','desc');
		return $query->get()->take(50);
	}

	/**
	 * Build Call.
	*/
   	public static function buildCall($call)
   	{
		$object = new \stdClass();
		$object->codigo_convocatoria = $call->codigo_convocatoria;
		$object->codigo_usuario = $call->codigo_usuario;
		$object->codigo_encuesta = $call->codigo_encuesta;
		$object->nombre_convocatoria = $call->nombre_convocatoria;
		$object->contador_inscritos = $call->contador_inscritos;
		$object->descripcion_convocatoria = $call->descripcion_convocatoria;
		$object->terminos_convocatoria = $call->terminos_convocatoria;
		$object->imagen_convocatoria = $call->imagen_convocatoria;
		$object->url_convocatoria = $call->url_convocatoria;
		$object->codigo_orientacion_convocatoria = $call->codigo_orientacion_convocatoria;
		$object->codigo_cat_proceso_insti = $call->codigo_cat_proceso_insti;
		$object->codigo_estado = $call->codigo_estado;
		$object->contenido_aceptacion_terminos = $call->contenido_aceptacion_terminos;
		$object->politicas_convocatoria = $call->politicas_convocatoria;
    $object->ocultar_al_finalizar = $call->ocultar_al_finalizar;
		$object->phases = Fase::getPhaseProgram($call->codigo_convocatoria);

		return $object;
   	}

	/*
	* Get program by id
	*/
	public static function getProgramById($id)
	{
		$query = Convocatoria::query();
		$query->join('core.convocatoria_institucion', function($join) {
			$join->on('convocatoria.codigo_convocatoria', '=', 'convocatoria_institucion.codigo_convocatoria');
		});

		$query->where('convocatoria.codigo_convocatoria', '=', $id);
		return $query->first();
	}

	/**
	 * Get the institutions course for the blog post.
	 */
	public function institutions()
	{
		return $this->hasMany('App\Models\Core\ConvocatoriaInstitucion', 'codigo_convocatoria', 'codigo_convocatoria');
	}



  /**
   * Sync Algolia index
   */
  public function syncIndex(){

	\Artisan::queue('algolia:syncitem', [
		'type' => 'program',
		'id' => $this->codigo_convocatoria
	]);

    $user = Usuario::find(\Auth::user()->codigo_usuario);
    if(!$user instanceof Usuario) {
      $errormsg = "App\Models\Core\Convocatoria@syncIndex: Error recuperando usuario ".\Auth::user()->codigo_usuario;
      Log::error(['error'=> $errormsg]);
      throw new \Exception($errormsg);
    }

    /*activity()
      ->performedOn($user)
      ->withProperties([
        'attributes' => [
          'user' => $user->nombre_usuario,
          'user_id' => $user->codigo_usuario,
          'convocatoria' => $this->nombre_convocatoria
        ]
      ])
      ->log('activitylog.call.sync');*/
  }

   /**
   * Desync Algolia index
   */
  public function desyncIndex(){

	\Artisan::queue('algolia:desyncitem', [
		'type' => 'program',
		'id' => $this->codigo_convocatoria
	]);

    $user = Usuario::find(\Auth::user()->codigo_usuario);
    if(!$user instanceof Usuario) {
      $errormsg = "App\Models\Core\Convocatoria@syncIndex: Error recuperando usuario ".\Auth::user()->codigo_usuario;
      Log::error(['error'=> $errormsg]);
      throw new \Exception($errormsg);
    }

    /*activity()
      ->performedOn($user)
      ->withProperties([
        'attributes' => [
          'user' => $user->nombre_usuario,
          'user_id' => $user->codigo_usuario,
          'convocatoria' => $this->nombre_convocatoria
        ]
      ])
      ->log('activitylog.call.desync');*/
  }

  	/**
	 * método copiado de plataforma
	 * Permite registrar en convocatoria y en fase
	 * @return void
	 */
	public function apply(Usuario $user, UsuarioEncuesta $userSurvey)
	{
		// Register call
    $register = RegistroConvocatoria::where('codigo_convocatoria', $this->codigo_convocatoria)
                ->where('codigo_usuario', $userSurvey->codigo_usuario)
                ->where('codigo_estado', Estado::getState('active'))
                ->orderBy('codigo_registro_convocatoria', 'desc')
                ->first();
    if(!$register instanceof RegistroConvocatoria) {
		  $register = new RegistroConvocatoria;
    }
		$register->codigo_usuario_encuesta = $userSurvey->codigo_usuario_encuesta;
		$register->codigo_convocatoria = $this->codigo_convocatoria;
		$register->codigo_usuario = $user->codigo_usuario;
		$register->codigo_estado = Estado::getState('active');
		$register->fecha_registro_convocatoria = date('Y-m-d');
		$register->hora_registro_convocatoria = date('H:i:s');
		$register->save();

		// Phase register
		$fase = $this->fase_registro();
		if(!$fase instanceof Fase) {
			Log::critical("FATAL ERROR => No se encuentra la fase de registro de la convocatoria $this->codigo_convocatoria");
		}

    $registro_fase = RegistroFaseUsuario::where('codigo_registro_convocatoria', $register->codigo_registro_convocatoria)
    ->orderBy('codigo_registro_convocatoria', 'desc')
    ->first();
    if(!$registro_fase instanceof RegistroFaseUsuario) {
		  $registro_fase = new RegistroFaseUsuario;
    }
		$registro_fase->codigo_fase = $fase->codigo_fase;
		$registro_fase->codigo_registro_convocatoria = $register->codigo_registro_convocatoria;
		$registro_fase->codigo_usuario = $user->codigo_usuario;
		$registro_fase->visualizacion_usuario = $this->visualizacion_usuario;
		$registro_fase->fecha_registro_fase_usuario = date('Y-m-d');
		$registro_fase->hora_registro_fase_usuario = date('H:i:s');
		$registro_fase->codigo_estado = Estado::getState('active');
		$registro_fase->save();
	}

	public function fase_registro()
	{
		$query = $this->fases()
						->where('fase.codigo_estado', Estado::getState('active'))
						->where('fase.fase_registro', true);
        /*if(!$this->permanente_convocatoria) {
			$query->whereRaw('CURRENT_DATE between fase.fecha_inicio_fase and fase.fecha_fin_fase');
		} */
		//Log::info($query->first());
		return $query->first();
	}

	/**
	 * Defining Relations
	*/
	public function fases()
	{
		return $this->hasMany('App\Models\Core\Fase', 'codigo_convocatoria', 'codigo_convocatoria');
	}

  /**
   * The preguntasFrecuentes that belong to the Convocatoria
   *
   * @return \Illuminate\Database\Eloquent\Relations\BelongsToMany
   */
  public function preguntasFrecuentes()
  {
    return $this->belongsToMany(PreguntaFrecuente::class, 'core.faq_convocatoria', 'codigo_convocatoria', 'codigo_preguntas_frecuentes');
  }

}
