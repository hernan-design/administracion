<?php

namespace App\Models\Core;

use Illuminate\Database\Eloquent\Model;

use TuProyecto, DB, Auth, App;

use App\Models\Seguridad\Usuario, App\Models\Core\ItemEvaluacionCompra, App\Models\Param\Institucion;

class AcuerdoCompra extends Model
{
    /**
	 * The database table used by the model.
	 *
	 * @var string
	 */
    protected $table = 'core.acuerdo_compra';

   	protected $primaryKey = 'codigo_acuerdo_compra';

	public $timestamps = false;

	protected $fillable = ['alcance_acuerdo_compra', 'resultados_acuerdo_compra'];

	public static function getPurchaseAgreement($agreement)
   	{
   		$query = AcuerdoCompra::query();
		$query->select('acuerdo_compra.codigo_acuerdo_compra', 'acuerdo_compra.codigo_compra', 'alcance_acuerdo_compra', 'resultados_acuerdo_compra', 'nombre_producto', 'imagen_producto', 'fecha_compra', 'valor_total_compra', 'resumen_producto', 'seller.correo_electronico as correo_vendedor', 'seller.identificacion_usuario as cedula_vendedor', 'tpseller.nombre_tipo_identificacion as tipo_identificacion_vendedor', 'buyer.correo_electronico as correo_comprador', 'useller.nombre_usuario as nombre_proveedor', 'nombre_categoria_producto', 'tpbuyer.nombre_tipo_identificacion as tipo_identificacion_comprador', 'useller.identificacion_usuario as nit_proveedor', 'buyer.identificacion_usuario as cedula_comprador',
			DB::raw("(CASE buyer.tipo_usuario WHEN ".Usuario::typeUser('person')." THEN buyer.nombre_usuario || ' ' || COALESCE(pbuyer.primer_apellido_persona, '') || ' ' || COALESCE(pbuyer.segundo_apellido_persona, '') ELSE buyer.nombre_usuario END) as nombre_comprador"), 'acuerdo_compra.codigo_estado',
			DB::raw("(CASE seller.tipo_usuario WHEN ".Usuario::typeUser('person')." THEN seller.nombre_usuario || ' ' || COALESCE(pseller.primer_apellido_persona, '') || ' ' || COALESCE(pseller.segundo_apellido_persona, '') ELSE seller.nombre_usuario END) as nombre_vendedor"),
			DB::raw("(CASE official.tipo_usuario WHEN ".Usuario::typeUser('person')." THEN official.nombre_usuario || ' ' || COALESCE(pofficial.primer_apellido_persona, '') || ' ' || COALESCE(pofficial.segundo_apellido_persona, '') ELSE official.nombre_usuario END) as nombre_funcionario"), 'acuerdo_compra.codigo_acuerdo_compra', 'fecha_acuerdo_aceptacion_compra', 'fecha_acuerdo_aprobacion_compra', 'fecha_aprobacion_asesor_acuerdo_compra', 'url_acuerdo_compra',
			'carta_compormiso_compra.nombre_empresa as comprador',
			'carta_compormiso_compra.nombre_representante_legal as nombre_representante_legal_comprador',
			'carta_compormiso_compra.apellidos_representante_legal as apellido_representante_legal_comprador',
			'carta_compormiso_compra.identificacion_rep_legal as identificador_representante_legal_comprador',
			'carta_compormiso_compra.nit_empresa as nit_comprador',
			DB::raw('upper(expedicion_id_rl.nombre_ciudad) as ciudad_id_representante_legal_comprador'),  'ciudad_empresa.nombre_ciudad as ciudad_comprador', 'modalidad_prestacion_servicio',
			DB::raw('upper(cseller.nombre_ciudad) as seller_city'),
			DB::raw("(CASE WHEN asignacion_marketplace.codigo_asignacion_mp IS NOT NULL THEN true ELSE false END) as assignment"), 'useller.numero_contrato', 'clase_emprendimiento_insti.codigo_clase_emprendimiento',
			DB::raw("(CASE seller.tipo_usuario WHEN ".Usuario::typeUser('person')." THEN seller.nombre_usuario || ' ' || COALESCE(pseller.primer_apellido_persona, '') || ' ' || COALESCE(pseller.segundo_apellido_persona, '') ELSE eseller.nombre_representante_legal END) as nombre_representante_legal_proveedor"),
			DB::raw("(CASE seller.tipo_usuario WHEN ".Usuario::typeUser('person')." THEN seller.identificacion_usuario ELSE eseller.identificacion_representate_legal END) as identificacion_representate_legal_proveedor"),
			'useller.tipo_usuario as tipo_usuario_proveedor', 'eseller.entidad_internacional as internacional_proveedor',
			DB::raw("(SELECT pgencu.texto_respuesta_encuesta FROM core.respuesta_encuesta as pgencu
				WHERE pgencu.codigo_pregunta_encuesta = 1928 and pgencu.codigo_usuario_encuesta = usuario_encuesta.codigo_usuario_encuesta) as direccion_empresario"),
			DB::raw("(SELECT nombre_ciudad FROM core.respuesta_encuesta as pgencu JOIN core.ciudad ON codigo_ciudad = pgencu.texto_respuesta_encuesta::integer WHERE pgencu.codigo_pregunta_encuesta = 1927 and pgencu.codigo_usuario_encuesta = usuario_encuesta.codigo_usuario_encuesta) as ciudad_residencia_empresario"),
			DB::raw("( SELECT ceil( ABS( ( extract(EPOCH FROM MAX(fecha_final_actividad_acuerdo)::date ) - extract(EPOCH FROM MIN(fecha_inicial_actividad_acuerdo)::date) ) )/30.43/24/3600) FROM core.actividad_acuerdo 
				WHERE actividad_acuerdo.codigo_acuerdo_compra = acuerdo_compra.codigo_acuerdo_compra 
				AND actividad_acuerdo.codigo_estado = ".Estado::getState('active').") AS plazo"),
			'eseller.codigo_usuario as codigo_usuario_entidad',
      DB::raw("(CASE WHEN acuerdo_compra.fecha_finalizacion_actividades IS NOT NULL THEN acuerdo_compra.fecha_finalizacion_actividades ELSE (  
        SELECT MAX(fecha_final_actividad_acuerdo) FROM core.actividad_acuerdo
          WHERE actividad_acuerdo.codigo_acuerdo_compra = acuerdo_compra.codigo_acuerdo_compra 
          	AND actividad_acuerdo.codigo_estado = ".Estado::getState('active')."
          ) END) as fecha_final_acuerdo_compra"),
      'acuerdo_compra.comentario_ampliacion_acuerdo as comentario_final',
      DB::raw("(SELECT MIN(fecha_inicial_actividad_acuerdo) FROM core.actividad_acuerdo
          WHERE actividad_acuerdo.codigo_acuerdo_compra = acuerdo_compra.codigo_acuerdo_compra 
          	AND actividad_acuerdo.codigo_estado = ".Estado::getState('active')."
          ) as fecha_inicial_acuerdo_compra"),
      DB::raw("(SELECT MAX(fecha_final_actividad_acuerdo) FROM core.actividad_acuerdo
          WHERE actividad_acuerdo.codigo_acuerdo_compra = acuerdo_compra.codigo_acuerdo_compra 
          	AND actividad_acuerdo.codigo_estado = ".Estado::getState('active')."
          ) as fecha_final_acuerdo_compra_actividades"),
      'acuerdo_compra.fecha_finalizacion_actividades as fecha_final_bool_acuerdo_compra',
      'acuerdo_compra.motivo_liquidacion_acuerdo as motivo_liquidacion',
      'acuerdo_compra.fecha_liquidacion_acuerdo as fecha_liquidacion_final',
      'acuerdo_compra.comentario_liquidacion_acuerdo as motivo_liquidacion_acuerdo'
		);
		$query->join('core.compra', 'acuerdo_compra.codigo_compra', '=', 'compra.codigo_compra');
		$query->join('core.consumo_voucher', 'compra.codigo_compra', '=', 'consumo_voucher.codigo_compra');
		$query->join('core.detalle_voucher', 'consumo_voucher.codigo_detalle_voucher', '=', 'detalle_voucher.codigo_detalle_voucher');
		$query->join('core.carta_compormiso_compra', 'detalle_voucher.codigo_registro_fase_usuario', '=', 'carta_compormiso_compra.codigo_registro_fase_usuario');
		$query->join('core.ciudad as expedicion_id_rl', 'expedicion_id_rl.codigo_ciudad', '=', 'carta_compormiso_compra.codigo_ciudad');
		$query->join('core.ciudad as ciudad_empresa', 'expedicion_id_rl.codigo_ciudad', '=', 'carta_compormiso_compra.codigo_ciudad');
		$query->join('core.producto', 'compra.codigo_producto', '=', 'producto.codigo_producto');

		$query->join('core.registro_fase_usuario', 'carta_compormiso_compra.codigo_registro_fase_usuario', '=', 'registro_fase_usuario.codigo_registro_fase_usuario');
		$query->join('core.registro_convocatoria', 'registro_fase_usuario.codigo_registro_convocatoria', '=', 'registro_convocatoria.codigo_registro_convocatoria');
		$query->join('seguridad.usuario', 'registro_convocatoria.codigo_usuario', '=', 'usuario.codigo_usuario');
		$query->join('core.entidad', 'entidad.codigo_usuario', '=', 'usuario.codigo_usuario');
		$query->join('core.clase_emprendimiento_insti', 'clase_emprendimiento_insti.codigo_clase_empre_insti', '=', 'entidad.codigo_clase_empre_insti');
		$query->join('core.usuario_encuesta', 'registro_convocatoria.codigo_usuario_encuesta', '=', 'usuario_encuesta.codigo_usuario_encuesta');

		// User official
		$query->leftJoin('seguridad.usuario as official', 'acuerdo_compra.codigo_usuario', '=', 'official.codigo_usuario');
		$query->leftJoin('core.persona as pofficial', 'official.codigo_usuario', '=', 'pofficial.codigo_usuario');

		// Users buy and sell
		$query->join('seguridad.usuario as buyer', 'compra.codigo_usuario', '=', 'buyer.codigo_usuario');
		$query->leftJoin('core.persona as pbuyer', 'buyer.codigo_usuario', '=', 'pbuyer.codigo_usuario');
		$query->leftJoin('core.tipo_identificacion as tpbuyer', 'buyer.tipo_identificacion_usuario', '=', 'tpbuyer.codigo_tipo_identificacion');

		$query->join('seguridad.usuario as seller', 'acuerdo_compra.seg_codigo_usuario', '=', 'seller.codigo_usuario');
		$query->leftJoin('core.entidad as eseller', 'seller.codigo_usuario', '=', 'eseller.seg_codigo_usuario');
		$query->leftJoin('core.persona as pseller', 'seller.codigo_usuario', '=', 'pseller.codigo_usuario');
		$query->leftJoin('core.ciudad as cseller', 'pseller.codigo_ciudad', '=', 'cseller.codigo_ciudad');
		$query->leftJoin('core.tipo_identificacion as tpseller', 'seller.tipo_identificacion_usuario', '=', 'tpseller.codigo_tipo_identificacion');

		$query->leftJoin('seguridad.usuario as useller', 'producto.seg_codigo_usuario', '=', 'useller.codigo_usuario');

		$query->leftJoin('core.categoria_producto_insti', function($join) {
			$join->on('producto.codigo_categoria_prod_insti', '=', 'categoria_producto_insti.codigo_categoria_prod_insti')
				->where('categoria_producto_insti.codigo_institucion', '=', Institucion::institution('innpulsa'));
		});
		$query->leftJoin('core.categoria_producto', 'categoria_producto_insti.codigo_categoria_producto', '=', 'categoria_producto.codigo_categoria_producto');

		// Assignment
		$query->leftJoin('core.asignacion_marketplace', function($join) {
			$join->on('asignacion_marketplace.codigo_registro_fase_usuario', '=','carta_compormiso_compra.codigo_registro_fase_usuario')
				->where('asignacion_marketplace.codigo_usuario', '=', Auth::user()->codigo_usuario)
				->where('asignacion_marketplace.codigo_estado', '=', Estado::getState('active'));
		});

		$query->where('acuerdo_compra.codigo_acuerdo_compra', '=', $agreement);
		$query->orderBy('acuerdo_compra.codigo_acuerdo_compra','desc');

		return $query->first();
   	}

   	public static function getAgreement($purchase) {
      $query = AcuerdoCompra::query();
      $query->select('codigo_estado', 'cor_codigo_adjunto', 'numero_factura', 'fecha_radicacion_factura', 'fecha_gestion_factura', 'numero_radicacion', 'valor_factura', 'fecha_inicio_revision_entregables', 'fecha_fin_revision_entregables');
      $query->where('codigo_compra', '=', $purchase);
      $data = $query->first();
      return $data;
    }

  public static function documentEnlarge($id, $date_enlarge, $commentary) {
      $codigo_acuerdo_compra = $id;

      $purchaseAgreement = AcuerdoCompra::getPurchaseAgreement($codigo_acuerdo_compra);

      $data = [];
      $data['title'] = 'ACTA DE AMPLIACIÓN ACUERDO DE NEGOCIACIÓN';
      $data['company_name'] = $purchaseAgreement->comprador;
      $data['name_rep_leg_purchaser'] = $purchaseAgreement->nombre_representante_legal_comprador . ' ' . $purchaseAgreement->apellido_representante_legal_comprador;
      $data['name_provider'] = $purchaseAgreement->nombre_proveedor;
      $data['name_rep_leg_seller'] = $purchaseAgreement->nombre_representante_legal_proveedor;
      $data['product'] = $purchaseAgreement->nombre_producto;
      $data['value_product'] = $purchaseAgreement->valor_total_compra;
      $data['date_initial'] = $purchaseAgreement->fecha_inicial_acuerdo_compra;
      $data['data_final'] = $purchaseAgreement->fecha_final_acuerdo_compra_actividades;
      $data['data_final_enlarge'] = $date_enlarge;
      $data['commentary'] = $commentary;
      $data['official_name'] = $purchaseAgreement->nombre_funcionario;

      return $data;
  }

  public static function documentLiquidate($id, $commentary) {
    $codigo_acuerdo_compra = $id;

    $purchaseAgreement = AcuerdoCompra::getPurchaseAgreement($codigo_acuerdo_compra);
    $data['title'] = 'ACTA DE LIQUIDACIÓN ACUERDO DE NEGOCIACIÓN';
    $data['company_name_liq'] = $purchaseAgreement->comprador;
    $data['name_rep_leg_purchaser_liq'] = $purchaseAgreement->nombre_representante_legal_comprador . ' ' . $purchaseAgreement->apellido_representante_legal_comprador;
    $data['name_provider_liq'] = $purchaseAgreement->nombre_proveedor;
    $data['name_rep_leg_seller_liq'] = $purchaseAgreement->nombre_representante_legal_proveedor;
    $data['product_liq'] = $purchaseAgreement->nombre_producto;
    $data['value_product_liq'] = $purchaseAgreement->valor_total_compra;
    $data['date_initial_liq'] = $purchaseAgreement->fecha_inicial_acuerdo_compra;
    $data['data_final_liq'] = $purchaseAgreement->fecha_final_acuerdo_compra;
    $data['commentary_liq'] = $commentary;
    $data['official_name_liq'] = $purchaseAgreement->nombre_funcionario;

    return $data;
  }

    /**
	* Build Purchase Agreement.
	*/
   	public static function buildPurchaseAgreement( AcuerdoCompra $agreement)
   	{
		$object = new \stdClass();
		$object->codigo_acuerdo_compra = TuProyecto::encryptSrcParams([$agreement->codigo_acuerdo_compra]);
		$object->numero_acuerdo_compra = $agreement->codigo_acuerdo_compra;
		$object->url_acuerdo_compra = $agreement->url_acuerdo_compra;
		$object->codigo_compra = TuProyecto::encryptSrcParams([$agreement->codigo_compra]);
		$object->alcance_acuerdo_compra = $agreement->alcance_acuerdo_compra;
		$object->resultados_acuerdo_compra = $agreement->resultados_acuerdo_compra;
		$object->codigo_estado = $agreement->codigo_estado;
		$object->nombre_producto = $agreement->nombre_producto;
		$object->imagen_producto = $agreement->imagen_producto;
		$object->modalidad_prestacion_servicio = $agreement->modalidad_prestacion_servicio;
		$object->nombre_comprador = ucwords( strtolower($agreement->nombre_comprador) );
		$object->nombre_vendedor = ucwords( strtolower($agreement->nombre_vendedor) );
		$object->fecha_compra = $agreement->fecha_compra;
		$object->fecha_acuerdo_aceptacion_compra = $agreement->fecha_acuerdo_aceptacion_compra;
		$object->fecha_acuerdo_aprobacion_compra = $agreement->fecha_acuerdo_aprobacion_compra;
		$object->fecha_aprobacion_asesor_acuerdo_compra = $agreement->fecha_aprobacion_asesor_acuerdo_compra;
		$object->fecha_final_acuerdo_compra = $agreement->fecha_final_acuerdo_compra;
		$object->fecha_final_acuerdo_compra_actividad = $agreement->fecha_final_acuerdo_compra_actividades;
		$object->fecha_inicial_acuerdo_compra = $agreement->fecha_inicial_acuerdo_compra;
		$object->fecha_final_bool_acuerdo_compra = $agreement->fecha_final_bool_acuerdo_compra ? true : false;
		$object->motivo_liquidacion = $agreement->motivo_liquidacion != null ? true : false;
		$object->comentario_final = $agreement->comentario_final;
		$object->valor_total_compra = number_format($agreement->valor_total_compra, 0,'.','.');
		$object->resumen_producto = $agreement->resumen_producto;
		$object->numero_contrato = $agreement->numero_contrato;
		$object->codigo_clase_emprendimiento = $agreement->codigo_clase_emprendimiento;
		$object->cedula_vendedor = $agreement->cedula_vendedor;
		$object->nombre_funcionario = $agreement->nombre_funcionario;
		$object->cedula_comprador = $agreement->cedula_comprador;
		$object->direccion_empresario = $agreement->direccion_empresario;
		$object->ciudad_residencia_empresario = $agreement->ciudad_residencia_empresario;
		$object->plazo = $agreement->plazo;
		$object->nombre_categoria_producto = $agreement->nombre_categoria_producto;

		$object->nombre_representante_legal_proveedor = $agreement->nombre_representante_legal_proveedor;
		$object->identificacion_representate_legal_proveedor = $agreement->identificacion_representate_legal_proveedor;
		$object->nombre_proveedor = $agreement->nombre_proveedor;
		$object->nit_proveedor = $agreement->nit_proveedor;
		$object->tipo_usuario_proveedor = $agreement->tipo_usuario_proveedor;
		$object->tipo_identificacion_vendedor = $agreement->tipo_identificacion_vendedor;
		$object->internacional_proveedor = $agreement->internacional_proveedor;
		$object->seller_city = $agreement->seller_city;

		$object->nombre_representante_legal_comprador = $agreement->nombre_representante_legal_comprador;
		$object->apellido_representante_legal_comprador = $agreement->apellido_representante_legal_comprador;
		$object->identificador_representante_legal_comprador = $agreement->identificador_representante_legal_comprador;
		$object->ciudad_id_representante_legal_comprador = $agreement->ciudad_id_representante_legal_comprador;
		$object->ciudad_comprador = $agreement->ciudad_comprador;
		$object->nit_comprador = $agreement->nit_comprador;
		$object->tipo_identificacion_comprador = $agreement->tipo_identificacion_comprador;
		$object->comprador = $agreement->comprador;
		$object->assignment = $agreement->assignment ? $agreement->assignment : (Auth::user()->hasRole(['admin','super-admin','administrador-marketplace']));
		$object->codigo_usuario_entidad = $agreement->codigo_usuario_entidad ? TuProyecto::encryptSrcParams([$agreement->codigo_usuario_entidad]) : '';

		// Get activitys agreement
		$object->actividad_acuerdo = $agreement->activityAgreement()->where('codigo_estado', '=', Estado::getState('active'))->get();
		// Get users agreement
		$object->asesor_acuerdo = $agreement->userTeamAgreement();
		// Get deliverables agreement
		$object->entregable_acuerdo = EntregableAcuerdo::getDeliverableAttachment($agreement->codigo_acuerdo_compra);
		// Get request agreement
		$object->respuesta_eval_compra = ItemEvaluacionCompra::getItemEvalPurchaseList();
		// Get request users businessman
		$object->empresario_acuerdo = $agreement->userBusinessman()->where('codigo_estado', '=', Estado::getState('active'))->get();
		return $object;
   	}

	public function userTeamAgreement()
	{
		$query = UsuarioAcuerdo::query();
		$query->select('usuario_acuerdo.codigo_usuario_acuerdo', 'grupo_usuario.codigo_grupo_usuario', 'correo_electronico as correo_usuario_acuerdo', 'nombre_rol_usuario_grupo as cargo_usuario_acuerdo',
			DB::raw("(nombre_usuario || ' ' || COALESCE(persona.primer_apellido_persona, '') || ' ' || COALESCE(persona.segundo_apellido_persona, '')) as nombre_usuario_acuerdo"));
		$query->join('core.grupo_usuario', 'usuario_acuerdo.codigo_grupo_usuario', '=', 'grupo_usuario.codigo_grupo_usuario');
		$query->join('seguridad.usuario', 'grupo_usuario.seg_codigo_usuario', '=', 'usuario.codigo_usuario');
		$query->leftjoin('core.persona', 'usuario.codigo_usuario', '=', 'persona.codigo_usuario');
		$query->leftjoin('core.rol_usuario_grupo_insti', 'grupo_usuario.codigo_rol_usuario_grupo_insti', '=', 'rol_usuario_grupo_insti.codigo_rol_usuario_grupo_insti');
		$query->leftjoin('core.rol_usuario_grupo', 'rol_usuario_grupo_insti.codigo_rol_usuario_grupo', '=', 'rol_usuario_grupo.codigo_rol_usuario_grupo');

		$query->where('usuario_acuerdo.codigo_acuerdo_compra', $this->codigo_acuerdo_compra);
		$query->where('usuario_acuerdo.codigo_estado', '=', Estado::getState('active'));
		return $query->get();
	}

   	public function pdf()
   	{
        // File data
		$data['data'] = $this->buildPurchaseAgreement( $this::getPurchaseAgreement($this->codigo_acuerdo_compra) );
        $data['title'] = "ACUERDO DE NEGOCIACIÓN ENTRE EL EMPRESARIO Y EL ASESOR";
        $data['type'] = 'PDF';

        // File save
        $pdf = App::make('dompdf.wrapper');
		$pdf->loadHTML(view('reports.marketplace.innpulsa.acuerdocompra', $data));

		return $pdf;
   	}

   	public function activityAgreement()
    {
        return $this->hasMany('App\Models\Core\ActividadAcuerdo','codigo_acuerdo_compra','codigo_acuerdo_compra')->orderBy('codigo_actividad_acuerdo', 'asc');
    }

    public function userAgreement()
    {
        return $this->hasMany('App\Models\Core\UsuarioAcuerdo','codigo_acuerdo_compra','codigo_acuerdo_compra');
    }

    public function deliverableAgreement()
    {
        return $this->hasMany('App\Models\Core\EntregableAcuerdo','codigo_acuerdo_compra','codigo_acuerdo_compra');
    }

	public function userBusinessman()
	{
		return $this->hasMany('App\Models\Core\UsuarioEmpresario','codigo_acuerdo_compra','codigo_acuerdo_compra');
	}
}
