<?php

namespace App\Models\Core;

use App\Models\BaseModel;

use App\Models\Seguridad\Usuario;
use DateTime;
use DB, Validator, Auth;


class ModuloDiagnostico extends BaseModel
{
	/**
	 * The database table used by the model.
	 *
	 * @var string
	 */
    protected $table = 'core.modulo_diagnostico';

   	protected $primaryKey = 'codigo_modulo_diagnostico';

   	public $timestamps = false;

	/**
     * The attributes that are mass assignable.
     *
     * @var array
     */
	protected $fillable = [
		'codigo_diagnosico',
		'codigo_menu_institucion'
	];

	/**
	* Valid create content.
	*/
	public function isValid($data)
	{
		$rules = [
			'codigo_diagnosico'	=> 'required',
			'codigo_menu_institucion'	=> 'required'
		];

		$validator = Validator::make($data, $rules);
		if ($validator->passes()) {
			return true;
		}
		$this->errors = $validator->errors();
		return false;
	}

	public static function getDiagnostics(array $filters = [])
	{


		$query = ModuloDiagnostico::query();
		$query->select('modulo_diagnostico.codigo_modulo_diagnostico', 'modulo_diagnostico.codigo_diagnostico', 'modulo_diagnostico.codigo_menu_institucion', 'diagnostico.nombre_diagnostico', 'menu_institucion.nombre_menu_institucion' );
		$query->join('core.diagnostico', function ($join){
			$join->on('modulo_diagnostico.codigo_diagnostico', '=', 'diagnostico.codigo_diagnostico')
				->where('diagnostico.codigo_institucion', '=', session('institution')->codigo_institucion);
		});

		$query->join('param.menu_institucion', function($join){
			$join->on('modulo_diagnostico.codigo_menu_institucion', '=', 'menu_institucion.codigo_menu_institucion');
		});

    if(isset($filters['codigo_diagnostico']) && !empty($filters['codigo_diagnostico'])){
			$query->where("modulo_diagnostico.codigo_diagnostico", $filters['codigo_diagnostico']);
		}

    if(isset($filters['codigo_menu_institucion']) && !empty($filters['codigo_menu_institucion'])){
			$query->where('modulo_diagnostico.codigo_menu_institucion', $filters['codigo_menu_institucion']);
		}

		$query->orderBy('modulo_diagnostico.codigo_modulo_diagnostico','desc');
		return $query->get();
	}

	public static function getDiagnostic($diagnostic_code)
	{
		$query = ModuloDiagnostico::query();
		$query->select(
			'modulo_diagnostico.codigo_modulo_diagnostico',
			'modulo_diagnostico.codigo_diagnostico',
			'modulo_diagnostico.codigo_menu_institucion'
		);
		$query->join('core.diagnostico', function ($join){
			$join->on('modulo_diagnostico.codigo_diagnostico', '=', 'diagnostico.codigo_diagnostico')
				->where('diagnostico.codigo_institucion', '=', session('institution')->codigo_institucion);
		});

		$query->join('param.menu_institucion', function($join){
			$join->on('modulo_diagnostico.codigo_menu_institucion', '=', 'menu_institucion.codigo_menu_institucion');
		});

		return $query->first();
	}

	public static function getDiagnosticsList(array $filters = [])
	{
		$query = ModuloDiagnostico::query();
		$query->select('modulo_diagnostico.codigo_modulo_diagnostico as id', DB::raw('initcap(diagnostico.nombre_diagnostico) as text'));
		$query->join('param.institucion', function ($join){
			$join->on('diagnostico.codigo_institucion', '=', 'institucion.codigo_institucion')
				->where('institucion.codigo_institucion', '=', session('institution')->codigo_institucion);
		});

		// // Filters
		// if(isset($filters['q']) && !empty($filters['q'])) {
 		// 	$query->whereRaw("nombre_evento ilike '%".$filters['q']."%'");
 		// }

		// Order
		$query->orderBy('diagnostico.codigo_diagnostico','desc');
		return $query->get();
	}

	public static function buidDiagnostic($diagnostic)
	{
		$object = new \stdClass();
		$object->codigo_modulo_diagnostico = $diagnostic->codigo_modulo_diagnostico;
		$object->codigo_diagnostico = $diagnostic->codigo_diagnostico;
		$object->codigo_menu_institucion = $diagnostic->codigo_menu_institucion;

		return $object;
	}

	public function institutions ()
	{
		return $this->hasMany('App\Models\Core\EventoInstitucion', 'codigo_evento', 'codigo_evento');
	}

	public function tags()
    {
        return $this->belongsToMany('App\Models\Core\Tag', 'core.tag_evento', 'codigo_evento', 'codigo_tag');
    }

	public function usuario()
    {
        return $this->hasOne('App\Models\Seguridad\Usuario','codigo_usuario','codigo_usuario');
    }
}
