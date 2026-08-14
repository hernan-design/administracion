<?php

namespace App\Models\Core;

use Illuminate\Database\Eloquent\Model;

class ComentDefDocument extends Model
{
    protected $table = 'core.coment_def_document';

    protected $primaryKey = 'codigo_coment_def_document';

	public $incrementing = false;

    public $timestamps = false;

    public static function getTypeComment($type){
        switch ($type) {
            case 'general':
                return 1;
            break;
            case 'secondary':
                return 2;
            break;
        }
    }
}
