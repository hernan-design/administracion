<?php

namespace App\Models\Core;

use Illuminate\Database\Eloquent\Model;

use DB, Auth, Input, Validator;

use TuProyecto, Datatables;

use App\Models\Seguridad\Usuario, App\Models\Param\Documento;

class CartaCompromisoCompra extends Model
{
	/**
	 * The database table used by the model.
	 *
	 * @var string
	 */
	protected $table = 'core.carta_compormiso_compra';

	protected $primaryKey = 'codigo_carta_compormiso_compra';

	public $timestamps = false;


	public static function getRegisterCommitment($registro)
	{
		$query = RegistroFaseUsuario::query();

		$query->select('registro_fase_usuario.codigo_registro_fase_usuario', 'registro_fase_usuario.codigo_usuario', 'fase.codigo_evaluacion_emprendimiento', DB::raw("COALESCE(carta_compormiso_compra.nombre_empresa, nombre_usuario) as nombre_usuario"), DB::raw("COALESCE(carta_compormiso_compra.nit_empresa, identificacion_usuario) as identificacion_usuario"), 'convocatoria.codigo_convocatoria', 'observaciones_registro_fase_usuario',
			'carta_compormiso_compra.nombre_representante_legal', 'apellidos_representante_legal', 'identificacion_rep_legal', 'carta_compormiso_compra.codigo_ciudad', 'carta_compormiso_compra.cor_codigo_ciudad', 'nit_empresa', 'codigo_carta_compormiso_compra', 'carta_compormiso_compra.codigo_estado', 'codigo_clase_emprendimiento', 'entidad.codigo_clase_empre_insti', 'entidad.codigo_entidad'
		);
		$query->join('core.fase', 'fase.codigo_fase', '=', 'registro_fase_usuario.codigo_fase');
		$query->join('core.registro_convocatoria', 'registro_fase_usuario.codigo_registro_convocatoria', '=', 'registro_convocatoria.codigo_registro_convocatoria');
		$query->join('seguridad.usuario', 'registro_convocatoria.codigo_usuario', '=', 'usuario.codigo_usuario');
		$query->join('core.entidad', 'entidad.codigo_usuario', '=', 'usuario.codigo_usuario');
		$query->join('core.clase_emprendimiento_insti', 'clase_emprendimiento_insti.codigo_clase_empre_insti', '=', 'entidad.codigo_clase_empre_insti');

		// User Program
		$query->join('core.convocatoria', 'fase.codigo_convocatoria', '=', 'convocatoria.codigo_convocatoria');
		$query->join('core.convocatoria_institucion', function($join) {
			$join->on('convocatoria.codigo_convocatoria', '=', 'convocatoria_institucion.codigo_convocatoria')
				->where('convocatoria_institucion.codigo_institucion', '=', session('institution')->codigo_institucion)
				->where('convocatoria_institucion.codigo_estado', '=', Estado::getState('active'));
		});

		$query->leftJoin('core.carta_compormiso_compra', 'registro_fase_usuario.codigo_registro_fase_usuario', '=', 'carta_compormiso_compra.codigo_registro_fase_usuario');
		$query->where('registro_fase_usuario.codigo_registro_fase_usuario', $registro);
		$query->where('convocatoria.codigo_estado', Estado::getState('active'));
		return $query->first();
	}

	public static function getDocuments($codigo_usuario, $carta_compormiso_compra = null)
	{
		$query = Documento::query();
		$query->select('documento.codigo_documento', 'nombre_adjunto', 'direccion_adjunto', 'nombre_documento', DB::raw("(fecha_adjunto_usuario || ' ' || hora_adjunto_usuario) as fecha_adjunto"), ($carta_compormiso_compra != null ? DB::raw('(CASE WHEN carta_institucion_doc.codigo_carta_compormiso_compra IS NULL THEN false ELSE true END) as check') : DB::raw("false as check")));

		$query->join('param.institucion_documento', 'institucion_documento.codigo_documento', '=', 'documento.codigo_documento');
		$query->leftJoin('core.adjunto_usuario', function($join) use($codigo_usuario) {
			$join->on('adjunto_usuario.codigo_documento', '=', 'documento.codigo_documento')
				->where('adjunto_usuario.codigo_institucion', '=', session('institution')->codigo_institucion)
				->where('adjunto_usuario.codigo_usuario', '=', $codigo_usuario)
				->where('adjunto_usuario.codigo_estado', '=', Estado::getState('active'));
		});
		$query->leftJoin('core.adjunto', 'adjunto.codigo_adjunto', '=', 'adjunto_usuario.codigo_adjunto');

		if(!is_null($carta_compormiso_compra)) {
			$query->leftJoin('core.carta_institucion_doc', function($join) use($carta_compormiso_compra) {
			    $join->on('carta_institucion_doc.codigo_documento', '=', 'documento.codigo_documento')
			        ->where('carta_institucion_doc.codigo_estado', '=', Estado::getState('active'))
			        ->where('carta_institucion_doc.codigo_institucion', '=', session('institution')->codigo_institucion)
			        ->where('carta_institucion_doc.codigo_carta_compormiso_compra', '=', $carta_compormiso_compra);
			});
		}

		$query->where('institucion_documento.codigo_institucion', session('institution')->codigo_institucion);
		$query->where('institucion_documento.clasificacion_documento_institucion', Documento::getClass('entity'));
		$query->where('institucion_documento.codigo_estado', Estado::getState('active'));
		return $query->get();
	}

	/**
	* Build Commitment Purchase.
	*/
	public static function buildCommitmentPurchase($partcipant)
	{
//	  dd($partcipant->codigo_carta_compormiso_compra);
		$object = new \stdClass();
		$object->codigo_usuario = TuProyecto::encryptSrcParams([$partcipant->codigo_usuario]);
		$object->codigo_entidad = TuProyecto::encryptSrcParams([$partcipant->codigo_entidad]);
		$object->codigo_registro_fase_usuario = TuProyecto::encryptSrcParams([$partcipant->codigo_registro_fase_usuario]);
		$object->codigo_carta_compormiso = TuProyecto::encryptSrcParams([$partcipant->codigo_carta_compormiso_compra]);
		$object->codigo_carta_compormiso_compra = $partcipant->codigo_carta_compormiso_compra;
		$object->nombre_usuario = $partcipant->nombre_usuario;
		$object->codigo_convocatoria = $partcipant->codigo_convocatoria;
		$object->nombre_representante_legal = $partcipant->nombre_representante_legal;
		$object->apellidos_representante_legal = $partcipant->apellidos_representante_legal;
		$object->identificacion_rep_legal = $partcipant->identificacion_rep_legal;
		$object->ciudad_expedicion_id_rep_legal = $partcipant->codigo_ciudad;
		$object->ciudad_empresa = $partcipant->cor_codigo_ciudad;
		$object->nit_empresa = $partcipant->nit_empresa;
		$object->codigo_estado = $partcipant->codigo_estado;
		$object->identificacion_usuario = $partcipant->identificacion_usuario;
		$object->codigo_clase_emprendimiento = $partcipant->codigo_clase_emprendimiento;
		$object->codigo_clase_empre_insti = $partcipant->codigo_clase_empre_insti;

		// Documents
		$object->documents = self::getDocuments($partcipant->codigo_usuario, $partcipant->codigo_carta_compormiso_compra);

		return $object;
	}
}
