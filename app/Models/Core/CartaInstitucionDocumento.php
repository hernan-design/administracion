<?php

namespace App\Models\Core;

use Illuminate\Database\Eloquent\Model;

class CartaInstitucionDocumento extends Model
{
    /**
	 * The database table used by the model.
	 *
	 * @var string
	 */
    protected $table = 'core.carta_institucion_doc';

   	protected $primaryKey = 'codigo_carta_insti_doc';

	public $timestamps = false;
}
