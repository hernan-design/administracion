<?php
namespace App\Models\Aprende;

use Illuminate\Database\Eloquent\Model;

use DB, Input, Auth, Log, Artisan, Config, Validator, Lang, URL, SendEmail, Datatables;

use App\Models\Core\Estado;

class ItemMentoria extends Model {
	/**
	 * The database table used by the model.
	 *
	 * @var string
	 */
	protected $table = 'aprende.item_general_mentoria';

	protected $primaryKey = 'codigo_item_general_mentoria';

	public $timestamps = false;

    public static function itemsEvaluation(int $id_institution, int $id_type)
    {
        $query = ItemMentoria::query();
        $query->where('codigo_institucion', '=', $id_institution);
        $query->where('tipo_item_general_mentoria', '=', $id_type);
        $query->where('codigo_estado', '=', Estado::getState('active'));
        $query->orderBy('codigo_item_general_mentoria', 'asc');
        $data = $query->get();
        return $data;
    }

}