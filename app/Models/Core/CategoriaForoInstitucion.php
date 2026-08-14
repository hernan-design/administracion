<?php

namespace App\Models\Core;

use Illuminate\Database\Eloquent\Model;

class CategoriaForoInstitucion extends Model
{
	/**
	 * The database table used by the model.
	 *
	 * @var string
	 */
    protected $table = 'core.categoria_foro_institucion';

   	protected $primaryKey = 'codigo_cat_foro_insti';

   	public $timestamps = false;
}
