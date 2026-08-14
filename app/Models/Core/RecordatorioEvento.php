<?php

namespace App\Models\Core;

use Illuminate\Database\Eloquent\Model;
use DB, Log, Lang;

class RecordatorioEvento extends Model
{
	/**
	 * The database table used by the model.
	 *
	 * @var string
	 */
    protected $table = 'core.recordatorios_eventos';

   	//protected $primaryKey = 'id';

   //	public $timestamps = false;

	/**
     * The attributes that are mass assignable.
     *
     * @var array
     */
	protected $fillable = [
		//'id',
		'codigo_evento',
		'enviar',
		'enviado'
	];


	/**
     * actualiza en tabla recordatorio eventos, colum enviado
     * @param int $id
     */
    public static function set_recordatorio_enviado(int $id) {
        
		DB::beginTransaction();
		try {	
			
			$recordatorio = RecordatorioEvento::find($id);
			$recordatorio->enviado = date('Y-m-d H:i:s');
			$recordatorio->save();
			
			DB::commit();
			Log::info(sprintf('%s id %s', 'Recordatorio actualizado -', $id));
			//return response()->json($response);
		}catch(\Exception $e){
			DB::rollback();
			Log::error(sprintf('%s:%s - id %s', 'Error al actualizar Recordatorio -', $id , $e->getMessage()));
			//return response()->json(['success' => false, 'errors' => Lang::get('app.exception')]);
		}

	}

}
