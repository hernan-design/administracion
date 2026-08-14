<?php

namespace App\Models\Aprende;

use Illuminate\Database\Eloquent\Model;

class CumpliItemsAsesoria extends Model
{
	/**
	 * The database table used by the model.
	 *
	 * @var string
	 */
	protected $table = 'aprende.cumpli_items_asesoria';

	protected $primaryKey = 'codigo_cumpli_items_asesoria';

	public $timestamps = false;
}
