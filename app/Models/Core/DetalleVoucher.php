<?php

namespace App\Models\Core;

use DB, Datatables;
use Illuminate\Database\Eloquent\Model;

class DetalleVoucher extends Model
{
    /**
	 * The database table used by the model.
	 *
	 * @var string
	 */
    protected $table = 'core.detalle_voucher';

   	protected $primaryKey = 'codigo_detalle_voucher';

   	public $timestamps = false;

	// get vouchers details institution
	public static function getVoucherDetailInstitution(array $filters = []){
		$query = DetalleVoucher::query();
		$query->select('detalle_voucher.codigo_detalle_voucher', 'valor_detalle_voucher', 'saldo_detalle_voucher', DB::raw("(usuario.nombre_usuario || ' ' || COALESCE(persona.primer_apellido_persona, '') || ' ' || COALESCE(persona.segundo_apellido_persona, '')) as nombre_usuario"), 'usuario.correo_electronico', DB::raw("(enterprise.nombre_usuario || ' ' || COALESCE(business.primer_apellido_persona, '') || ' ' || COALESCE(business.segundo_apellido_persona, '')) as nombre_emprendimiento"), 'estado.nombre_estado as estado');

		$query->leftJoin('core.registro_fase_usuario', 'detalle_voucher.codigo_registro_fase_usuario', '=', 'registro_fase_usuario.codigo_registro_fase_usuario');
		$query->leftJoin('core.registro_convocatoria', 'registro_fase_usuario.codigo_registro_convocatoria', '=', 'registro_convocatoria.codigo_registro_convocatoria');

		// get data user
		$query->leftJoin('seguridad.usuario', 'detalle_voucher.codigo_usuario', '=', 'usuario.codigo_usuario');
		$query->join('core.usuario_institucion', function($join){
			$join->on('usuario.codigo_usuario', '=', 'usuario_institucion.codigo_usuario')
				->where('usuario_institucion.codigo_institucion', '=', session('institution')->codigo_institucion);
		});
		$query->leftJoin('core.persona','usuario.codigo_usuario','=','persona.codigo_usuario');

		// get data enterprise
		$query->leftJoin('seguridad.usuario as enterprise', 'registro_convocatoria.codigo_usuario', '=', 'enterprise.codigo_usuario');
		$query->join('core.usuario_institucion as enterprise_institution', function($join){
			$join->on('enterprise.codigo_usuario', '=', 'enterprise_institution.codigo_usuario')
				->where('enterprise_institution.codigo_institucion', '=', session('institution')->codigo_institucion);
		});
		$query->leftJoin('core.persona as business','enterprise.codigo_usuario','=','business.codigo_usuario');

		$query->join('core.estado', 'detalle_voucher.codigo_estado', '=', 'estado.codigo_estado');

		if(isset($filters['correo_electronico']) && !empty($filters['correo_electronico'])){
            $query->whereRaw("trim(lower(usuario.correo_electronico)) = trim(lower('".$filters['correo_electronico']."'))");
        }

       	if(isset($filters['emprendimiento']) && !empty($filters['emprendimiento'])) {
			$query->whereRaw("enterprise.nombre_usuario ilike '%".trim($filters['emprendimiento'])."%'");
 		}

		if(isset($filters['voucher']) && !empty($filters['voucher'])){
			$query->where('detalle_voucher.codigo_detalle_voucher', '=', $filters['voucher']);
			return $query->first();
		}

		return Datatables::of($query)
            ->filter(function($query) {

        })->make(true);
	}
}
