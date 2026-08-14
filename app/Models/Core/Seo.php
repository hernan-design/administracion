<?php

namespace App\Models\Core;

use Illuminate\Database\Eloquent\Model;

class Seo extends Model
{
    /**
	 * The database table used by the model.
	 *
	 * @var string
	 */
    protected $table = 'core.seo';

   	protected $primaryKey = 'codigo_seo';

    public $timestamps = false;
    
    /**
     * The attributes that are mass assignable.
     *
     * @var array
     */
	protected $fillable = ['nombre_facebook', 'nombre_twitter', 'descripcion_facebook', 'descripcion_twitter', 'imagen_facebook', 'imagen_twitter', 'url_canonica'];
}
