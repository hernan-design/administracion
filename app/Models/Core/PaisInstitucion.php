<?php

namespace App\Models\Core;

use Illuminate\Database\Eloquent\Model;

use App\Models\Core\Estado;

class PaisInstitucion extends Model
{
	/**
	 * The database table used by the model.
	 *
	 * @var string
	 */
    protected $table = 'core.pais_institucion';

    public $incrementing = false;

	public $timestamps = false;
	
}
