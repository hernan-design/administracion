<?php

namespace App\Models\Core;

use Illuminate\Database\Eloquent\Model;

use Datatables;

Use TuProyecto;

Use DB, Auth;

use App\Models\Seguridad\Usuario;

class Compra extends Model
{
    /**
	 * The database table used by the model.
	 *
	 * @var string
	 */
	protected $table = 'core.compra';

	protected $primaryKey = 'codigo_compra';

	public $timestamps = false;

	/**
	 * Domain 1 Vendedor, 2 comprador.
	 *
	 * @var string $type
	 */
    public static function getTypeUser($type) {
        switch ($type):
            case 'seller': return 1;
            case 'purchaser': return 2;
            case 'admin': return 3;
            default: return 'Not type identified';
        endswitch;
    }


	public static function getDataPurchases(array $filters = [])
	{
		$query = Compra::query();
		$query->select('compra.codigo_compra', 'nombre_producto', 'url_producto', 'fecha_compra', 'hora_compra', 'valor_total_compra', 'acuerdo_compra.codigo_estado as state_agreement', 'estado.nombre_estado as state_purchase_name', 'compra.codigo_estado as state_purchase', 'acuerdo_producto', 'acuerdo_compra.fecha_inicio_revision_entregables as fecha_inicio_revision', 'acuerdo_compra.fecha_fin_revision_entregables as fecha_fin_revision',
			DB::raw("(CASE buyer.tipo_usuario WHEN ".Usuario::typeUser('person')." THEN buyer.nombre_usuario || ' ' || COALESCE(pbuyer.primer_apellido_persona, '') || ' ' || COALESCE(pbuyer.segundo_apellido_persona, '') ELSE buyer.nombre_usuario END) as nombre_comprador"),
			DB::raw("(CASE seller.tipo_usuario WHEN ".Usuario::typeUser('person')." THEN seller.nombre_usuario || ' ' || COALESCE(pseller.primer_apellido_persona, '') || ' ' || COALESCE(pseller.segundo_apellido_persona, '') ELSE seller.nombre_usuario END) as nombre_vendedor"), 'acuerdo_compra.codigo_acuerdo_compra','nombre_categoria_producto', 'state.nombre_estado as state_agreement_name', DB::raw("COALESCE(entity.nombre_usuario, '') as entity_name"), DB::raw("COALESCE(buyer_entity.nombre_usuario, '') as buyer_entity_name")
			);

		$query->join('core.estado', 'compra.codigo_estado', '=', 'estado.codigo_estado');
		$query->join('core.producto', 'compra.codigo_producto', '=', 'producto.codigo_producto');

		// Users buy and sell
		$query->join('seguridad.usuario as buyer', 'compra.codigo_usuario', '=', 'buyer.codigo_usuario');
		$query->leftJoin('core.persona as pbuyer', 'buyer.codigo_usuario', '=', 'pbuyer.codigo_usuario');

		$query->leftJoin('core.consumo_voucher', 'compra.codigo_compra', '=', 'consumo_voucher.codigo_compra');
		$query->leftJoin('core.detalle_voucher', 'consumo_voucher.codigo_detalle_voucher', '=', 'detalle_voucher.codigo_detalle_voucher');
		$query->leftJoin('core.registro_fase_usuario', 'detalle_voucher.codigo_registro_fase_usuario', '=', 'registro_fase_usuario.codigo_registro_fase_usuario');
		$query->leftJoin('core.registro_convocatoria', 'registro_fase_usuario.codigo_registro_convocatoria', '=', 'registro_convocatoria.codigo_registro_convocatoria');
		$query->leftJoin('seguridad.usuario as buyer_entity', 'registro_convocatoria.codigo_usuario', '=', 'buyer_entity.codigo_usuario');

		if( isset($filters['challenge_people']) && !empty($filters['challenge_people']) ) {
			$query->leftJoin('core.asignacion_marketplace', function($join) use($filters){
				$join->on('registro_fase_usuario.codigo_registro_fase_usuario', '=', 'asignacion_marketplace.codigo_registro_fase_usuario')
					->where('asignacion_marketplace.codigo_estado', '=', Estado::getState('active'));
			});

			$query->where('asignacion_marketplace.codigo_usuario', '=', $filters['challenge_people']);
        }

		$query->join('seguridad.usuario as seller', 'compra.seg_codigo_usuario', '=', 'seller.codigo_usuario');
		$query->leftJoin('core.persona as pseller', 'seller.codigo_usuario', '=', 'pseller.codigo_usuario');

		$query->leftJoin('seguridad.usuario as entity', 'producto.seg_codigo_usuario', '=', 'entity.codigo_usuario');
		$query->leftJoin('core.entidad', 'entity.codigo_usuario', '=', 'entidad.codigo_usuario');

      	$query->leftJoin('core.categoria_producto_insti', function($join) {
            $join->on('producto.codigo_categoria_prod_insti', '=', 'categoria_producto_insti.codigo_categoria_prod_insti')
                ->where('categoria_producto_insti.codigo_institucion', '=', session('institution')->codigo_institucion)
                ->where('categoria_producto_insti.codigo_estado', '=', Estado::getState('active'));
        });

        $query->leftJoin('core.categoria_producto', 'categoria_producto_insti.codigo_categoria_producto', '=', 'categoria_producto.codigo_categoria_producto');

		$query->leftJoin('core.clase_emprendimiento_insti', function($join) {
            $join->on('entidad.codigo_clase_empre_insti', '=', 'clase_emprendimiento_insti.codigo_clase_empre_insti')
                ->where('clase_emprendimiento_insti.codigo_institucion', '=', session('institution')->codigo_institucion)
                ->where('clase_emprendimiento_insti.codigo_estado', '=', Estado::getState('active'));
        });

        $query->leftJoin('core.clase_emprendimiento', 'clase_emprendimiento_insti.codigo_clase_emprendimiento', '=', 'clase_emprendimiento.codigo_clase_emprendimiento');

        $query->leftJoin('core.acuerdo_compra', 'compra.codigo_compra', '=', 'acuerdo_compra.codigo_compra');
		$query->leftJoin('core.estado as state', 'acuerdo_compra.codigo_estado', '=', 'state.codigo_estado');

	    if( isset($filters['name_product']) && !empty($filters['name_product']) ) {
            $query->whereRaw("nombre_producto ilike '%".trim($filters['name_product'])."%'");
        }

	    if( isset($filters['category']) && !empty($filters['category']) ) {
			$query->where('categoria_producto_insti.codigo_categoria_prod_insti', $filters['category']);
		}

	    if( isset($filters['state']) && !empty($filters['state']) ) {
			if($filters['state'] == Estado::getState('active')){
				$query->where('acuerdo_compra.codigo_estado', $filters['state'])->orwhereNull('acuerdo_compra.codigo_acuerdo_compra');
			}else{
				$query->where('acuerdo_compra.codigo_estado', $filters['state']);
			}
		}

	    if( isset($filters['state_purchase']) && !empty($filters['state_purchase']) ) {
			$query->where('compra.codigo_estado', $filters['state_purchase']);
		}

	    if( isset($filters['number_register']) && !empty($filters['number_register']) ) {
            $query->where('compra.codigo_compra', '=', $filters['number_register']);
        }

		if( isset($filters['number_postulation']) && !empty($filters['number_postulation']) ) {
			$query->where('registro_convocatoria.numero_registro_convocatoria', '=', $filters['number_postulation']);
		}

	    if( isset($filters['codigo_ciudad']) && !empty($filters['codigo_ciudad']) ) {
            $query->where('entity.codigo_ciudad', '=', $filters['codigo_ciudad']);
        }

	    if( isset($filters['seg_codigo_usuario']) && !empty($filters['seg_codigo_usuario']) ) {
            $query->where("producto.seg_codigo_usuario", $filters['seg_codigo_usuario']);
        }

	    if( isset($filters['correo_electronico_emprendedor']) && !empty($filters['correo_electronico_emprendedor']) ) {
            $query->where("buyer.correo_electronico", $filters['correo_electronico_emprendedor']);
        }

	    if( isset($filters['voucher']) && !empty($filters['voucher']) ) {
			$query->where('detalle_voucher.codigo_detalle_voucher', '=', $filters['voucher']);
			$query->orderby('compra.fecha_compra', 'desc');
			$query->orderby('compra.hora_compra', 'desc');
		}

        $query->where('producto.codigo_institucion', '=', session('institution')->codigo_institucion);
		$query->where('core.compra.codigo_institucion', '=', session('institution')->codigo_institucion);

	    if( isset($filters['datatables']) && !empty($filters['datatables']) ) {
			return Datatables::of($query)
			->addColumn('codigo_acuerdo_compra', function ($data) {
				if($data->codigo_acuerdo_compra != null){
					return TuProyecto::encryptSrcParams([$data->codigo_acuerdo_compra]);
				}
			})
			->make(true);
		}

		return $query->get();
	}

	public static function getPurchase($purchase){
		$query = Compra::query();
		$query->select('compra.codigo_usuario', 'compra.seg_codigo_usuario', 'compra.codigo_compra', 'compra.valor_total_compra', 'compra.valor_total_iva', 'compra.fecha_compra', 'compra.hora_compra', 'compra.codigo_estado', 'imagen_producto', 'nombre_producto', 'unidad_producto', 'resumen_producto', 'acuerdo_compra.codigo_acuerdo_compra', 'acuerdo_compra.codigo_estado as state_agreement', 'acuerdo_producto', 'fecha_acuerdo_aceptacion_compra',
		DB::raw("(CASE buyer.tipo_usuario WHEN ".Usuario::typeUser('person')." THEN buyer.nombre_usuario || ' ' || COALESCE(pbuyer.primer_apellido_persona, '') || ' ' || COALESCE(pbuyer.segundo_apellido_persona, '') ELSE buyer.nombre_usuario END) as nombre_comprador"),
		DB::raw("(CASE seller.tipo_usuario WHEN ".Usuario::typeUser('person')." THEN seller.nombre_usuario || ' ' || COALESCE(pseller.primer_apellido_persona, '') || ' ' || COALESCE(pseller.segundo_apellido_persona, '') ELSE seller.nombre_usuario END) as nombre_vendedor")
		);

		$query->join('core.producto', 'compra.codigo_producto', '=', 'producto.codigo_producto');

		// Users buy and sell
		$query->join('seguridad.usuario as buyer', 'compra.codigo_usuario', '=', 'buyer.codigo_usuario');
		$query->leftJoin('core.persona as pbuyer', 'buyer.codigo_usuario', '=', 'pbuyer.codigo_usuario');

		$query->join('seguridad.usuario as seller', 'compra.seg_codigo_usuario', '=', 'seller.codigo_usuario');
		$query->leftJoin('core.persona as pseller', 'seller.codigo_usuario', '=', 'pseller.codigo_usuario');

		$query->leftJoin('core.acuerdo_compra', 'compra.codigo_compra', '=', 'acuerdo_compra.codigo_compra');

		$query->where('compra.codigo_compra', '=', $purchase);
		return $query->first();
	}

	public static function getPurchaseTotal(){
		$query = Compra::query();
		$query->where('codigo_institucion', '=', session('institution')->codigo_institucion);
		$query->where('codigo_estado', '=', Estado::getState('confirmed'));
		return $query->sum('valor_total_compra');
	}
}
