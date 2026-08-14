<?php

namespace App\Models\Core;

use Illuminate\Database\Eloquent\Model;
use App\Models\Core\Estado;
use Validator, Input;

class CategoriaProceso extends Model
{
	/**
	 * The database table used by the model.
	 *
	 * @var string
	 */
    protected $table = 'core.categoria_proceso';

   	protected $primaryKey = 'codigo_categoria_proceso';

   	public $timestamps = false;

	/**
	* Valid create content.
	*/
	public function isValid($data)
	{
		$rules = [
			'nombre_categoria_proceso'	=> 'required'
		];

		$validator = Validator::make($data, $rules);
		if ($validator->passes()) {
			$query = self::query();
			$query->join('core.categoria_proceso_insti', function($join) use($data){
				$join->on('categoria_proceso.codigo_categoria_proceso', '=', 'categoria_proceso_insti.codigo_categoria_proceso')
					->where('categoria_proceso_insti.codigo_institucion', '=', session('institution')->codigo_institucion);
			});
			$query->whereRaw("nombre_categoria_proceso ilike trim('%".$data['nombre_categoria_proceso']."%')");
			$exist = $query->first();

			if($exist instanceof CategoriaProceso){
				$this->errors = "El nombre de la categoría ya se encuentra creado.";
				return false;
			}
			return true;
		}
		$this->errors = $validator->errors();
		return false;
	}

	public function isValidUpdate($data)
	{
		$rules = [
			'nombre_categoria_proceso'	=> 'required'
		];

		$validator = Validator::make($data, $rules);
		if ($validator->passes()) {
			$query = self::query();
			$query->join('core.categoria_proceso_insti', function($join) use($data){
				$join->on('categoria_proceso.codigo_categoria_proceso', '=', 'categoria_proceso_insti.codigo_categoria_proceso')
					->where('categoria_proceso_insti.codigo_institucion', '=', session('institution')->codigo_institucion);
			});
			$query->whereRaw("nombre_categoria_proceso ilike trim('%".$data['nombre_categoria_proceso']."%')");
			$query->where('categoria_proceso.codigo_categoria_proceso', '<>', $this->codigo_categoria_proceso);
			$exist = $query->first();

			if($exist instanceof CategoriaProceso){
				$this->errors = "El nombre de la categoría ya se encuentra creado.";
				return false;
			}
			return true;
		}
		$this->errors = $validator->errors();
		return false;
	}

	public static function getProcessCategory(array $filters = [])
	{
		$query = CategoriaProceso::query();
		$query->select('codigo_cat_proceso_insti', 'categoria_proceso.codigo_categoria_proceso', 'nombre_categoria_proceso', 'descripcion_categoria_proceso', 'codigo_estado');
		$query->join('core.categoria_proceso_insti', function($join) {
			$join->on('categoria_proceso.codigo_categoria_proceso', '=', 'categoria_proceso_insti.codigo_categoria_proceso')
				->where('categoria_proceso_insti.codigo_institucion', '=', session('institution')->codigo_institucion);
		});

		if(isset($filters['nombre_categoria_proceso']) && !empty($filters['nombre_categoria_proceso'])){
			$query->whereRaw("nombre_categoria_proceso ilike '%".$filters['nombre_categoria_proceso']."%'");
		}

		if(isset($filters['codigo_estado']) && !empty($filters['codigo_estado'])){
			$query->where('categoria_proceso_insti.codigo_estado', '=', $filters['codigo_estado']);
		}else{
			$query->where('categoria_proceso_insti.codigo_estado', '=', Estado::getState('active'));
		}

		$query->orderby('nombre_categoria_proceso', 'asc');
		return $query->get();
	}

	public static function getCategoriesProgram() {
	  $query = CategoriaProceso::query();
    $query->select( 'categoria_proceso.nombre_categoria_proceso', 'categoria_proceso.codigo_categoria_proceso');
    $query->join('core.categoria_proceso_insti', 'categoria_proceso_insti.codigo_categoria_proceso', '=', 'categoria_proceso.codigo_categoria_proceso');
    $query->where('categoria_proceso_insti.codigo_institucion', '=', session('institution')->codigo_institucion);
    $query->where('categoria_proceso_insti.codigo_estado', '=', Estado::getState('active'));
    return $query->get();
  }
}
