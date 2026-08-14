<?php

namespace App\Models\Core;

use Illuminate\Database\Eloquent\Model;

class ItemOpcionEncuesta extends Model
{
	/**
     * The database table used by the model.
     *
     * @var string
     */
    protected $table = 'core.item_opcion_encuesta';

   	protected $primaryKey = 'codigo_item_opcion_encuesta';

    public $timestamps = false;
}
