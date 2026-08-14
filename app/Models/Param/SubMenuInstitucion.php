<?php

namespace App\Models\Param;

use Illuminate\Database\Eloquent\Model;

class SubMenuInstitucion extends Model
{
    /**
	 * The database table used by the model.
	 *
	 * @var string
	 */
    protected $table = 'param.submenu_institucion';

   	protected $primaryKey = 'codigo_submenu_institucion';

	public $timestamps = false;
}
