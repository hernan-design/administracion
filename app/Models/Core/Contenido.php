<?php

namespace App\Models\Core;

use Illuminate\Database\Eloquent\Model;

use Validator, Auth, DB, Lang, App;

use TuProyecto, Datatables;

use App\Models\Seguridad\Usuario, App\Models\Core\Estado, App\Models\Seguridad\Modulo;

class Contenido extends Model
{
    /**
	 * The database table used by the model.
	 *
	 * @var string
	 */
	protected $table = 'core.contenido';

	protected $primaryKey = 'codigo_contenido';

	public $timestamps = false;

	/**
     * The attributes that are mass assignable.
     *
     * @var array
     */
	protected $fillable = ['codigo_institucion', 'codigo_usuario', 'nombre_contenido', 'resumen_contenido', 'texto_contenido', 'imagen_contenido', 'url_contenido', 'fuente_contenido', 'privacidad_contenido', 'landing_contenido'];

	/**
	* Valid create content.
	*/
	public function isValid($data)
	{
		$rules = [
			'nombre_contenido'		=> 'required',
			'resumen_contenido'		=> 'required|max:200',
			'texto_contenido' 		=> 'required',
			'imagen_contenido' 		=> 'required',
			'privacidad_contenido'	=> 'required',
			'landing_contenido' 	=> 'required|boolean',
            'codigo_tipo_cont_institucion'	=> 'required'
		];

		$validator = Validator::make($data, $rules);
		if ($validator->passes()) {
			return true;
		}
		$this->errors = $validator->errors();
		return false;
	}

	public static function getPrivacy($privacy)
	{
		switch ($privacy) :
			case 'private': return 1;
			case 'public': return 2;
			default: return 'Not privacy identified';
		endswitch;
	}

	public static function getArticles(array $filters = [])
   	{
		$query = Contenido::query();
		$query->select('contenido.codigo_contenido', 'nombre_contenido', 'imagen_contenido', 'texto_contenido', 'resumen_contenido', 'fecha_contenido', 'hora_contenido', 'contenido_institucion.codigo_estado', 'nombre_estado', 'url_contenido', 'privacidad_contenido', 'contenido.landing_contenido', 'contenido_institucion.codigo_tipo_cont_institucion', 'contenido.codigo_usuario');

		$query->join('core.contenido_institucion', function($join){
			$join->on('contenido.codigo_contenido', '=', 'contenido_institucion.codigo_contenido')
				->where('contenido_institucion.codigo_institucion', '=', session('institution')->codigo_institucion);
		});

		$query->join('param.tipo_contenido_insti', function($join) {
            $join->on('contenido_institucion.codigo_tipo_cont_institucion', '=', 'tipo_contenido_insti.codigo_tipo_cont_institucion')
                ->where('tipo_contenido_insti.codigo_institucion', '=', session('institution')->codigo_institucion)
                ->where('tipo_contenido_insti.codigo_estado', '=', Estado::getState('active'));
        });
    	$query->join('core.tipo_contenido', 'tipo_contenido_insti.codigo_tipo_contenido', '=', 'tipo_contenido.codigo_tipo_contenido');

		$query->join('core.estado', 'contenido_institucion.codigo_estado', '=', 'estado.codigo_estado');

		if(isset($filters['nombre_contenido']) && !empty($filters['nombre_contenido'])){
			$query->whereRaw("nombre_contenido ilike '%".trim($filters['nombre_contenido'])."%'");
		}

		if(isset($filters['landing_contenido']) && !empty($filters['landing_contenido'])){
			$query->where("contenido_institucion.landing_contenido", "=", $filters['landing_contenido']);
		}

		if(isset($filters['privacidad_contenido']) && !empty($filters['privacidad_contenido'])){
			$query->where("privacidad_contenido", "=", $filters['privacidad_contenido']);
		}

		if(isset($filters['codigo_tipo_cont_institucion']) && !empty($filters['codigo_tipo_cont_institucion'])){
			$query->where("contenido_institucion.codigo_tipo_cont_institucion", "=", $filters['codigo_tipo_cont_institucion']);
		}

		if(isset($filters['codigo_estado']) && !empty($filters['codigo_estado'])){
			$query->where("contenido_institucion.codigo_estado", "=", $filters['codigo_estado']);
		}

		$query->whereIn('contenido_institucion.codigo_estado', [Estado::getState('review'), Estado::getState('active'), Estado::getState('inactive')]);

		// Get total posts
		$total_entries = $query->get()->count();

		$query->orderby(DB::raw("(CASE contenido_institucion.codigo_estado WHEN ".Estado::getState('review')." THEN contenido_institucion.codigo_estado END)"));
		$query->orderby('fecha_contenido','desc');

		// Get posts
		$data = $query->skip($filters['offset'])->take($filters['limit'])->get();

		$response = new \stdClass();
		$response->total_entries = $data->count() > 0 ? $total_entries: 0;
		$response->items = $data;

		return $response;
   	}

	public static function getArticle($codigo_contenido)
	{
		$query = Contenido::query();
		$query->select('contenido.codigo_contenido', 'nombre_contenido', 'resumen_contenido', 'texto_contenido', 'url_contenido', 'fuente_contenido', 'contenido.landing_contenido', 'privacidad_contenido', 'contenido_institucion.codigo_tipo_cont_institucion', 'nombre_tipo_contenido', 'imagen_contenido', 'seo.*');

		$query->join('core.contenido_institucion', function($join){
			$join->on('contenido.codigo_contenido', '=', 'contenido_institucion.codigo_contenido')
				->where('contenido_institucion.codigo_institucion', '=', session('institution')->codigo_institucion);
		});

		$query->join('param.tipo_contenido_insti', function($join) {
            $join->on('contenido_institucion.codigo_tipo_cont_institucion', '=', 'tipo_contenido_insti.codigo_tipo_cont_institucion')
                ->where('tipo_contenido_insti.codigo_institucion', '=', session('institution')->codigo_institucion)
                ->where('tipo_contenido_insti.codigo_estado', '=', Estado::getState('active'));
        });
		$query->join('core.tipo_contenido', 'tipo_contenido_insti.codigo_tipo_contenido', '=', 'tipo_contenido.codigo_tipo_contenido');
		
		$query->leftJoin('core.seo', function($join){
			$join->on('contenido.codigo_contenido', '=', 'seo.id_recurso')
				->where('seo.codigo_modulo', Modulo::getModule('blog'));
		});

		$query->where('contenido.codigo_contenido', '=', $codigo_contenido);
		return $query->first();
	}

    public static function getArticlesList(array $filters = [])
    {
        $query = Contenido::query();
		$query->select('contenido.codigo_contenido as id', DB::raw('initcap(nombre_contenido) as text'));

		$query->join('core.contenido_institucion', function($join){
			$join->on('contenido.codigo_contenido', '=', 'contenido_institucion.codigo_contenido')
				->where('contenido_institucion.codigo_institucion', '=', session('institution')->codigo_institucion)
				->where('contenido_institucion.codigo_estado', '=', Estado::getState('active'));
		});

		// Filters
		if(isset($filters['q']) && !empty($filters['q'])) {
 			$query->whereRaw("nombre_contenido ilike '%".$filters['q']."%'");
 		}

		// Order
   		$query->orderBy('nombre_contenido','asc');
		return $query->get()->take(50);
    }

	public static function buidContent($content)
	{
		$object = new \stdClass();
		$object->codigo_contenido = $content->codigo_contenido;
		$object->nombre_contenido = $content->nombre_contenido;
		$object->resumen_contenido = $content->resumen_contenido;
		$object->texto_contenido = $content->texto_contenido;
		$object->url_contenido = $content->url_contenido;
		$object->fuente_contenido = $content->fuente_contenido;
		$object->landing_contenido = $content->landing_contenido;
		$object->privacidad_contenido = $content->privacidad_contenido;
		$object->codigo_tipo_cont_institucion = $content->codigo_tipo_cont_institucion;
		$object->nombre_tipo_contenido = $content->nombre_tipo_contenido;
		$object->imagen_contenido = $content->imagen_contenido;
		$object->nombre_facebook = $content->nombre_facebook;
		$object->nombre_twitter = $content->nombre_twitter;
		$object->descripcion_facebook = $content->descripcion_facebook;
		$object->descripcion_twitter = $content->descripcion_twitter;
		$object->imagen_facebook = $content->imagen_facebook;
		$object->imagen_twitter = $content->imagen_twitter;
		$object->url_canonica = $content->url_canonica;
		$object->enlaces_motores_busqueda = $content->enlaces_motores_busqueda;
		$object->indexar_motores = $content->indexar_motores;
		$object->tags = $content->tags()->get()->implode('texto_tag', ',');
		return $object;
	}

	/**
	 * Get the institutions content for the blog post.
	 */
	public function institutions()
	{
		return $this->hasMany('App\Models\Core\ContenidoInstitucion', 'codigo_contenido', 'codigo_contenido');
	}

	public function tags()
    {
        return $this->belongsToMany('App\Models\Core\Tag', 'core.tag_contenido', 'codigo_contenido', 'codigo_tag');
    }
}
