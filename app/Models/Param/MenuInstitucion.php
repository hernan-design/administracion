<?php

namespace App\Models\Param;

use Illuminate\Database\Eloquent\Model;

class MenuInstitucion extends Model
{
   	/**
	 * The database table used by the model.
	 *
	 * @var string
	 */
    protected $table = 'param.menu_institucion';

   	protected $primaryKey = 'codigo_menu_institucion';

	public $timestamps = false;
}
