<?php

namespace App\Models\Core;

use Illuminate\Database\Eloquent\Model;

use Validator, Auth, DB, Lang, App, URL, Request, Cache;

use Datatables;

use TuProyecto;

use App\Models\Core\Estado, App\Models\Seguridad\Usuario;

class Producto extends Model
{
    /**
	 * The database table used by the model.
	 *
	 * @var string
	 */
	protected $table = 'core.producto';

	protected $primaryKey = 'codigo_producto';

	public $timestamps = false;

    public static function getModality($modality)
    {
        switch ($modality) {
            case 'virtual': return 1;
            case 'blended': return 2;
            case 'presencial': return 3;
            default: return 'Not modality identified';
        }
	}
	
	public static function getModalitySpanish($modality) {
		switch ($modality) {
			case 1:
				return 'Virtual';
			break;
			case 2:
				return 'Semipresencial';
			break;
			case 3:
				return 'Presencial';
			break;
			default:
				return 'Sin modalidad';
			break;
		}
	}

	/**
	 * SQL used to validate the state of product.
	 *
	 * @var string
	 */
	public static $raw_product = '(CASE
			WHEN (COALESCE(producto.codigo_estado, 0) = 8)  THEN \'published\'
			WHEN (COALESCE(producto.codigo_estado, 0) = 14) THEN \'pending\'
			WHEN (COALESCE(producto.codigo_estado, 0) = 2)  THEN \'review\'
		END)';


	public static function getDataProducts(array $filters = [])
	{
		$products = [];
        $query = Producto::query();
        $query->select('producto.*', 'nombre_estado', 'nombre_categoria_producto', 'nombre_cat_padre_producto',
		(Auth::check() ? DB::raw(sprintf('%s %s', Producto::$raw_product, 'as state_product')) : DB::raw('\'\' as state_product')), 'entity.nombre_usuario as entity_name', DB::raw("COALESCE(usuario.correo_electronico, '') as entity_email"), 'nombre_clase_producto'
		);

		$query->join('seguridad.usuario as entity', 'producto.seg_codigo_usuario', '=', 'entity.codigo_usuario');
		$query->join('seguridad.usuario', 'producto.codigo_usuario', '=', 'usuario.codigo_usuario');
		$query->join('core.clase_producto', 'producto.codigo_clase_producto', '=', 'clase_producto.codigo_clase_producto');
      	$query->leftJoin('core.categoria_producto_insti', function($join) {
            $join->on('producto.codigo_categoria_prod_insti', '=', 'categoria_producto_insti.codigo_categoria_prod_insti')
                ->where('categoria_producto_insti.codigo_institucion', '=', session('institution')->codigo_institucion)
                ->where('categoria_producto_insti.codigo_estado', '=', Estado::getState('active'));
		});

        $query->leftJoin('core.categoria_producto', 'categoria_producto_insti.codigo_categoria_producto', '=', 'categoria_producto.codigo_categoria_producto');
		$query->leftJoin('core.cat_padre_producto', 'categoria_producto_insti.codigo_cat_padre_producto', '=', 'cat_padre_producto.codigo_cat_padre_producto');
      	$query->join('core.estado', 'producto.codigo_estado', '=', 'estado.codigo_estado');

        if( isset($filters['name_product']) && !empty($filters['name_product']) ) {
            $query->whereRaw("nombre_producto ilike '%".trim($filters['name_product'])."%'");
        }

	    if( isset($filters['seg_codigo_usuario']) && !empty($filters['seg_codigo_usuario']) ) {
            $query->where("producto.seg_codigo_usuario", $filters['seg_codigo_usuario']);
        }

	    if( isset($filters['category']) && !empty($filters['category']) ) {
			$query->where('cat_padre_producto.codigo_cat_padre_producto', $filters['category']);
		}

	    if( isset($filters['subcategory']) && !empty($filters['subcategory']) ) {
			$query->where('categoria_producto_insti.codigo_categoria_prod_insti', $filters['subcategory']);
		}

	    if( isset($filters['codigo_ciudad']) && !empty($filters['codigo_ciudad']) ) {
			$query->where('entity.codigo_ciudad', $filters['codigo_ciudad']);
		}

	    if( isset($filters['codigo_estado']) && !empty($filters['codigo_estado']) ) {
			$query->where('producto.codigo_estado', $filters['codigo_estado']);
		}

		$query->where('producto.codigo_institucion', '=', session('institution')->codigo_institucion);

		return Datatables::of($query)
            ->addColumn('codigo_producto', function ($data) {
                return TuProyecto::encryptSrcParams([$data->codigo_producto]);
            })
			->addColumn('precio_producto', function ($data) {
				return number_format($data->precio_producto, 0, ',', '.');
			})
        ->make(true);
	}

	// get product for url product
	public static function getProduct($product){
		$query = Producto::query();
		$query->select('producto.*', 'nombre_estado',
		(Auth::check() ? DB::raw(sprintf('%s %s', Producto::$raw_product, 'as state_product')) : DB::raw('\'\' as state_product')));

		$query->join('core.estado', 'producto.codigo_estado', '=', 'estado.codigo_estado');
		$query->where('core.producto.url_producto', '=', $product);
		$query->where('producto.codigo_institucion', '=', session('institution')->codigo_institucion);
		return $query->first();
	}

	/**
   * Sync Algolia index
   */
	public function syncIndex(){
		\Artisan::queue('algolia:syncitem', [
			'type' => 'product',
			'id' => $this->codigo_producto
		]);

		$user = Usuario::find(\Auth::user()->codigo_usuario);
		if(!$user instanceof Usuario) {
		$errormsg = "App\Models\Core\Producto@syncIndex: Error recuperando usuario ".\Auth::user()->codigo_usuario;
		Log::error(['error'=> $errormsg]);
		throw new \Exception($errormsg);
		}
		activity()
		->performedOn($user)
		->withProperties([
			'attributes' => [
			'user' => $user->nombre_usuario,
			'user_id' => $user->codigo_usuario,
			'producto' => $this->nombre_producto
			]
		])
		->log('activitylog.product.sync');
	}

	/**
	 * Desync Algolia index
	 */
	public function desyncIndex(){
		\Artisan::queue('algolia:desyncitem', [
			'type' => 'product',
			'id' => $this->codigo_producto
		]);

		$user = Usuario::find(\Auth::user()->codigo_usuario);
		if(!$user instanceof Usuario) {
		$errormsg = "App\Models\Core\Producto@syncIndex: Error recuperando usuario ".\Auth::user()->codigo_usuario;
		Log::error(['error'=> $errormsg]);
		throw new \Exception($errormsg);
		}
		activity()
		->performedOn($user)
		->withProperties([
			'attributes' => [
			'user' => $user->nombre_usuario,
			'user_id' => $user->codigo_usuario,
			'producto' => $this->nombre_producto
			]
		])
		->log('activitylog.product.desync');
	}

	/* Defining Relations */
	public function usuario()
    {
        return $this->hasOne('App\Models\Seguridad\Usuario','codigo_usuario','codigo_usuario');
    }
}
