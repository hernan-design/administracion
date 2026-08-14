<?php

namespace App\Models\Core;

use Validator;
use Illuminate\Database\Eloquent\Model;

class EntregableAcuerdo extends Model
{
    /**
	 * The database table used by the model.
	 *
	 * @var string
	 */
	protected $table = 'core.entregable_acuerdo';

	protected $primaryKey = 'codigo_entregable_acuerdo';

	public $timestamps = false;

	/**
     * The attributes that are mass assignable.
     *
     * @var array
     */
	protected $fillable = ['descripcion_entregable'];

	/**
	* Valid create agreement.
	*/
	public function isValid($data)
	{
		$rules = [
			'descripcion_entregable' => 'required',
		];

		$validator = Validator::make($data, $rules);
		if ($validator->passes()) {
			return true;
		}
		$this->errors = $validator->errors();
		return false;
	}

	public static function getDeliverableAttachment($agreement)
   	{
		$query = EntregableAcuerdo::query();
		$query->select(
			'entregable_acuerdo.codigo_acuerdo_compra',
			'entregable_acuerdo.codigo_estado',
			'descripcion_entregable',
			'direccion_adjunto',
			'privacidad_adjunto',
			'nombre_adjunto',
			'codigo_entregable_acuerdo',
			'acuerdo_compra.fecha_acuerdo_compra as fecha_ejecucion_entregables',
			'acuerdo_compra.comentario_finalizacion_actividades as observacion'
		);
		$query->leftjoin('core.adjunto', 'entregable_acuerdo.codigo_adjunto', '=', 'adjunto.codigo_adjunto');
		$query->leftjoin('core.acuerdo_compra', 'entregable_acuerdo.codigo_acuerdo_compra', '=', 'acuerdo_compra.codigo_acuerdo_compra');

		$query->where('entregable_acuerdo.codigo_acuerdo_compra', '=', $agreement);

		if (session('institution')->codigo_institucion == 61) {
			$query->whereIn('entregable_acuerdo.codigo_estado',[Estado::getState('active'), Estado::getState('confirmed')]);
		} else {
			$query->where('entregable_acuerdo.codigo_estado', '=', Estado::getState('active'));
		}
		$query->orderby('codigo_entregable_acuerdo','asc');

   		return $query->get();
   	}

   	public static function getDeliverableFinal($agreement) {
      $query = Adjunto::query();
      $query->select('adjunto.codigo_adjunto', 'adjunto.direccion_adjunto', 'adjunto.nombre_adjunto');
      $query->join('core.acuerdo_compra', 'acuerdo_compra.codigo_adjunto', '=', 'adjunto.codigo_adjunto');
      $query->where('acuerdo_compra.codigo_acuerdo_compra', '=', $agreement);
      return $query->get();
    }

  public static function getDeliverableBill($agreement) {
    $query = Adjunto::query();
    $query->select('adjunto.codigo_adjunto', 'adjunto.direccion_adjunto', 'adjunto.nombre_adjunto');
    $query->join('core.acuerdo_compra', 'acuerdo_compra.cor_codigo_adjunto', '=', 'adjunto.codigo_adjunto');
    $query->where('acuerdo_compra.codigo_acuerdo_compra', '=', $agreement);
    return $query->get();
  }

  public static function getCertificateComercio($agreement) {
    $query = Adjunto::query();
    $query->select('adjunto.codigo_adjunto', 'adjunto.direccion_adjunto', 'adjunto.nombre_adjunto');
    $query->join('core.acuerdo_compra', 'acuerdo_compra.camara_comercio_asesor', '=', 'adjunto.codigo_adjunto');
    $query->where('acuerdo_compra.codigo_acuerdo_compra', '=', $agreement);
    return $query->get();
  }
}
