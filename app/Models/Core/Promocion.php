<?php

namespace App\Models\Core;

use Illuminate\Database\Eloquent\Model;

use Datatables, DB;

use App\Models\Seguridad\Usuario;

class Promocion extends Model
{
	/**
	 * The database table used by the model.
	 *
	 * @var string
	 */
	protected $table = 'core.promocion';

	protected $primaryKey = 'codigo_promocion';

	public $timestamps = false;

	public static function getTypePromo($typePromo)
	{
		switch ($typePromo) {
			case 'promotion': return 1;
			case 'advertisement': return 2;
			default: return 'Not type promotion identified';
		}
	}

	public static function promotions(array $filters = [])
	{
		$query = Promocion::query();
		$query->select('promocion.codigo_promocion', 'usuario.correo_electronico', 'tipo_promocion',
			DB::raw("( CASE WHEN detalle_promocion.codigo_estado IS NULL THEN promocion.codigo_estado ELSE detalle_promocion.codigo_estado END ) as codigo_estado"),
			DB::raw("( CASE WHEN estado.nombre_estado IS NULL THEN estpro.nombre_estado ELSE estado.nombre_estado END ) as nombre_estado"),
			DB::raw("initcap( (CASE usuario.tipo_usuario WHEN ".Usuario::typeUser('person')." THEN usuario.nombre_usuario || ' ' || COALESCE(persona.primer_apellido_persona, '') || ' ' || COALESCE(persona.segundo_apellido_persona, '') ELSE usuario.nombre_usuario END) ) as nombre_usuario")
		);
		$query->join('core.usuario_institucion', function($join){
			$join->on('promocion.codigo_usuario_institucion', '=', 'usuario_institucion.codigo_usuario_institucion')
				->where('usuario_institucion.codigo_institucion', '=', session('institution')->codigo_institucion);
		});

		$query->join('seguridad.usuario', 'usuario_institucion.codigo_usuario', '=', 'usuario.codigo_usuario');
		$query->leftJoin('core.persona', 'usuario.codigo_usuario', '=', 'persona.codigo_usuario');
		
		$query->leftJoin('core.detalle_promocion', 'promocion.codigo_promocion', '=', 'detalle_promocion.codigo_promocion');
		$query->leftJoin('core.producto', function($join){
			$join->on('core.detalle_promocion.codigo_producto', '=', 'producto.codigo_producto')
			->where('producto.codigo_institucion', '=', session('institution')->codigo_institucion);
		});
		$query->leftJoin('core.estado', 'detalle_promocion.codigo_estado', '=', 'estado.codigo_estado');
		$query->leftJoin('core.estado as estpro', 'estpro.codigo_estado', '=', 'promocion.codigo_estado');

		if(isset($filters['codigo_estado']) && !empty($filters['codigo_estado'])){
			$query->where('detalle_promocion.codigo_estado', '=', $filters['codigo_estado']);
		}

		if(isset($filters['correo_electronico']) && !empty($filters['correo_electronico'])){
			$query->where("usuario.correo_electronico", '=', $filters['correo_electronico']);
		}

		return Datatables::of($query)->make(true);
	}
}
