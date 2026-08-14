<?php


namespace App\Models\Core;

use Illuminate\Database\Eloquent\Model;

class Eje extends Model
{
  protected $table = 'core.eje';

  protected $primaryKey = 'codigo_eje';

  public $timestamps = false;

  public $incrementing = false;

  protected $fillable = ['codigo_eje', 'nombre_eje', 'codigo_institucion'];

  public static function getAllEje() {
    $query = Eje::query();
    $query->where('codigo_institucion', '=', session('institution')->codigo_institucion);
    return $query->get();
  }
}
