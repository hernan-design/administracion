<?php

namespace App\Models\Core;

use Illuminate\Database\Eloquent\Model;

use DB, Input, Auth, Validator, Lang, Log;

class Foro extends Model {

  /**
   * The database table used by the model.
   *
   * @var string
   */
  protected $table = 'core.foro';

  protected $primaryKey = 'codigo_foro';

  public $timestamps = false;

  protected $fillable = ['titulo_foro', 'descripcion_foro', 'codigo_categoria_foro'];

/**
*  retrieve a list of forums
*/
  public static function getForums(array $filters = [])
	{
		$query = Foro::query();
    $query->select('foro.codigo_foro', 'foro.descripcion_foro', 'foro.fecha_apertura_foro', 'foro.hora_apertura_foro',
    'foro.fecha_cierre_foro', 'foro.hora_cierre_foro','categoria_foro.nombre_categoria_foro', 'foro.url_foro', 'foro.titulo_foro',
    'foro.contador_like', 'foro.contador_comentario', 'foro.foro_grupo', 'usuario.nombre_usuario','usuario.correo_electronico','estado.nombre_estado', 'estado.codigo_estado');

		$query->join('core.estado', 'foro.codigo_estado', '=', 'estado.codigo_estado');
    $query->leftJoin('core.categoria_foro_institucion', function($join){
			$join->on('foro.codigo_categoria_foro', '=', 'categoria_foro_institucion.codigo_categoria_foro')
				->where('categoria_foro_institucion.codigo_institucion', '=', session('institution')->codigo_institucion)
				->where('categoria_foro_institucion.codigo_estado', '=', Estado::getState('active'));
    });

		$query->join('core.categoria_foro', function($join){
			$join->on('categoria_foro.codigo_categoria_foro', '=', 'categoria_foro_institucion.codigo_categoria_foro')
				->where('categoria_foro.codigo_estado', '=', Estado::getState('active'));
		});

    $query->join('core.foro_institucion', 'foro_institucion.codigo_foro', 'foro.codigo_foro');
    $query->where('foro_institucion.codigo_institucion', session('institution')->codigo_institucion);

		// Emprendedor
		$query->join('seguridad.usuario', 'foro.codigo_usuario', '=', 'usuario.codigo_usuario');


	    if( isset($filters['codigo_estado']) && !empty($filters['codigo_estado']) ) {
			$query->where('foro.codigo_estado', '=', $filters['codigo_estado']);
		}

	    if( isset($filters['correo_electronico_usuario']) && !empty($filters['correo_electronico_usuario']) ) {
			$query->whereRaw("usuario.correo_electronico ilike '%".$filters['correo_electronico_usuario']."%'" );
		}

	    if( isset($filters['codigo_categoria_foro']) && !empty($filters['codigo_categoria_foro']) ) {
			$query->where('categoria_foro.codigo_categoria_foro', $filters['codigo_categoria_foro']);
		}

		if( isset($filters['fecha_inicio_foro']) && !empty($filters['fecha_inicio_foro']) ) {
			$query->where('foro.fecha_apertura_foro', '>',$filters['fecha_inicio_foro']);
		}

		if( isset($filters['fecha_fin_foro']) && !empty($filters['fecha_fin_foro']) ) {
			$query->where('foro.fecha_apertura_foro', '<', $filters['fecha_fin_foro']);
		}


		 // Filter Order
		 $order = null;
		 $columns = null;
		 $dir = 'asc';



		 if( isset($filters['order']) && !empty($filters['order']) && isset($filters['columns']) && !empty($filters['columns']) ) {
			 $order = $filters['order'];
			 $columns = $filters['columns'];
			 $dir = (isset($order[0]['dir']) && !empty($order[0]['dir'])) ? $order[0]['dir'] : 'asc';
			if(isset($order[0]['column']) && !empty($order[0]['column']) && is_numeric($order[0]['column']) && isset($columns[$order[0]['column']]['data']) && !empty($columns[$order[0]['column']]['data']) ){
				if($columns[$order[0]['column']]['name'] === 'fecha_asesoria'){
					$query->orderBy('foro.fecha_apertura_foro', $dir);
					$query->orderBy('foro.hora_apertura_foro', $dir);
				}else{
					$query->orderBy($columns[$order[0]['column']]['data'], $dir);
					$query->orderBy('foro.fecha_apertura_foro', 'desc');
					$query->orderBy('foro.hora_apertura_foro', 'desc');
				}
			 }else {
				$query->orderBy('foro.fecha_apertura_foro', 'desc');
				$query->orderBy('foro.hora_apertura_foro', 'desc');

			 }
		 }else{
			$query->orderBy('foro.fecha_apertura_foro', 'desc');
			$query->orderBy('foro.hora_apertura_foro', 'desc');

		 }

		return $query->get();
	}


  public static function getForum($id)
	{

	$query = Foro::query();
    $query->select('foro.codigo_foro', 'foro.descripcion_foro', 'foro.fecha_apertura_foro', 'foro.hora_apertura_foro',
    'foro.fecha_cierre_foro', 'foro.hora_cierre_foro','categoria_foro.nombre_categoria_foro', 'foro.url_foro', 'foro.titulo_foro',
    'foro.contador_like', 'foro.contador_comentario', 'foro.foro_grupo', 'usuario.nombre_usuario','usuario.correo_electronico','estado.nombre_estado', 'estado.codigo_estado');

		$query->join('core.estado', 'foro.codigo_estado', '=', 'estado.codigo_estado');
		$query->leftJoin('core.categoria_foro_institucion', function($join){
				$join->on('foro.codigo_categoria_foro', '=', 'categoria_foro_institucion.codigo_categoria_foro')
					->where('categoria_foro_institucion.codigo_institucion', '=', session('institution')->codigo_institucion)
					->where('categoria_foro_institucion.codigo_estado', '=', Estado::getState('active'));
		});

		$query->join('core.categoria_foro', function($join){
			$join->on('categoria_foro.codigo_categoria_foro', '=', 'categoria_foro_institucion.codigo_categoria_foro')
				->where('categoria_foro.codigo_estado', '=', Estado::getState('active'));
		});


		// Emprendedor
		$query->join('seguridad.usuario', 'foro.codigo_usuario', '=', 'usuario.codigo_usuario');

		$query->where('foro.codigo_foro', $id);
		return $query->get();
	}

  /**
   * Defining Relations
   */

  public function usuario()
  {
    return $this->hasOne('App\Models\Seguridad\Usuario','codigo_usuario','codigo_usuario');
  }

  public function categoria()
  {
    return $this->hasOne('App\Models\Core\CategoriaForo','codigo_categoria_foro','codigo_categoria_foro');
  }

  public function tags()
  {
    return $this->belongsToMany('App\Models\Core\Tag', 'core.tag_foro', 'codigo_foro', 'codigo_tag');
  }

  public function institucion()
  {
    return $this->belongsTo('App\Models\Core\ForoInstitucion', 'codigo_foro', 'codigo_foro')
      ->where('codigo_institucion', RegistroSesion::getInstitution())
      ->where('codigo_estado', Estado::getState('active'));
  }
}
