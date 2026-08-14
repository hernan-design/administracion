<?php


namespace App\Models\Core;

use Illuminate\Database\Eloquent\Model;

class CatContentEvento  extends Model
{
  protected $table = 'core.cat_conten_evento';

  protected $primaryKey = 'codigo_cat_conten_evento';

  public $timestamps = false;

  public $incrementing = false;

  protected $fillable = ['codigo_cat_conten_evento', 'codigo_evento', 'codigo_categoria_contenido'];
}
