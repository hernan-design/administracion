<?php

namespace  App\Models\Core;

use Illuminate\Database\Eloquent\Model;
use Config, App, AWS, Validator;
use TuProyecto;

class AdjuntoProducto extends Model
{
	protected $table = 'core.adjunto_producto';

	public $timestamps = false;

	public $incrementing = false;

	protected $fillable = ['codigo_producto', 'codigo_adjunto', 'codigo_estado'];

	public static function getAttached($codigo_producto)
	{
		$query = AdjuntoProducto::query();
		$query->select('direccion_adjunto');
    	$query->join('core.adjunto', 'adjunto_producto.codigo_adjunto', '=', 'adjunto.codigo_adjunto');
		$query->where('adjunto_producto.codigo_estado', '=', Estado::getState('active'));
		$query->where('adjunto.codigo_privacidad_contenido', '=', PrivacidadContenido::getPrivacy('public'));
		$query->where('adjunto_producto.codigo_producto', '=', $codigo_producto);
		$query->orderBy('adjunto.codigo_adjunto', 'asc');

        // Get posts
		return $query->get()->toArray();
   	}

}
