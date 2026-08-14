<?php

namespace App\Models\Core;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Http\Request;

use DB;
use App\Models\Core\Estado;

class TemaPrincipalEvento extends Model
{
	/**
	 * The database table used by the model.
	 *
	 * @var string
	 */
	protected $table = 'core.tema_principal_evento';

	protected $primaryKey = 'codigo_tema_principal_evento';

	public $incrementing = false;

	public $timestamps = false;


	/**
     * retorna la lista de temas para eventos o
     * sesiones de mentorías
     * @param Illuminate\Http\Request $request
     * @return \Illuminate\Http\Response
     */
    public static function temasEventoList(Request $request)
    {

		$data = [];

		$query = TemaPrincipalEvento::query();
		$query->select(
					'tema_principal_evento.codigo_tema_principal_evento as id',
					DB::raw("trim(COALESCE(initcap(nombre_tema_principal_evento), '')) as text")
				);
		$query->join('core.tema_principal_institucion', function($join) {
			$join->on('tema_principal_evento.codigo_tema_principal_evento', '=', 'tema_principal_institucion.codigo_tema_principal_evento')
				->where('tema_principal_institucion.codigo_institucion', '=', session('institution')->codigo_institucion)
				->where('tema_principal_institucion.codigo_estado', '=', Estado::getState('active'));
		});

		// if($request->has('id') ){
		// 	$query->where("tema_principal_evento.codigo_tema_principal_evento", "=", $request->id);
		// }

		if($request->has('q')) {
			$query->where( function($query) use($request) {
				$query->whereRaw("tema_principal_evento.nombre_tema_principal_evento ilike '%".$request->q."%'");
			});
		}

		// if($request->has('dataId') &&  is_numeric($request->dataId) && (int) $request->dataId > 0 ) {
		// 	$query->where("tema_principal_evento.codigo_tema_principal_evento", "=",(int) $request->dataId);
		// }

		$query->orderby('tema_principal_evento.nombre_tema_principal_evento','asc');

		$data = $query->limit( 50 )->get();
		$data = $query->get();
		// dd($data);

		return response()->json($data);
    }

}
