<?php

namespace  App\Models\Core;

use Illuminate\Database\Eloquent\Model;
use Config, App, AWS, Validator;
use TuProyecto;

class AdjuntoConvocatoria extends Model
{
	protected $table = 'core.adjunto_convocatoria';

	public $timestamps = false;

	public $incrementing = false;

	protected $fillable = ['codigo_convocatoria', 'codigo_adjunto', 'codigo_estado'];

	public static function getAttachementForAnnouncement($id) {
		try {
			$codigo_convocatoria = $id;
			if(!$codigo_convocatoria || !is_numeric($codigo_convocatoria)) {
				abort(404);
			}
			$convocatoria = Convocatoria::find($codigo_convocatoria);
			if(!$convocatoria instanceof Convocatoria) {
				abort(404);
			}

			$Adjunto = [];
			$AdjuntoConvocatoria = Adjunto::query();
			$AdjuntoConvocatoria->select('adjunto.codigo_adjunto', 'adjunto.codigo_tipo_adjunto', 'adjunto.direccion_adjunto', 'adjunto.privacidad_adjunto', 'adjunto.nombre_adjunto', 'adjunto_convocatoria.codigo_convocatoria');
			$AdjuntoConvocatoria->join('core.adjunto_convocatoria', 'adjunto.codigo_adjunto', '=', 'adjunto_convocatoria.codigo_adjunto');
			$AdjuntoConvocatoria->where('adjunto_convocatoria.codigo_convocatoria', '=', $codigo_convocatoria);
			$AdjuntoConvocatoria->where('adjunto_convocatoria.codigo_estado', '=', Estado::getState('active'));
			$data = $AdjuntoConvocatoria->get();

			if(count($data) > 0) {
				foreach ($data as $item) {
					$Adjunto[] = AdjuntoConvocatoria::buildAttached($item);
				}
			}
			return $Adjunto;
		} catch (\Exception $e) {
			abort(404);
			return 'Algo salio mal';
		}
	}

	public static function buildAttached($item) {
		$object = new \stdClass();
		$object->uuid = TuProyecto::encryptSrcParams([$item->codigo_adjunto]);
		$object->codigo_adjunto = $item->codigo_adjunto;
		$object->codigo_tipo_adjunto = $item->codigo_tipo_adjunto;
		$object->direccion_adjunto = $item->direccion_adjunto;
		$object->privacidad_adjunto = $item->privaciodad_adjunto;
		$object->name = $item->nombre_adjunto;

		return $object;
	}

}
