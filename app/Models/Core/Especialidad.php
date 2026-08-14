<?php

namespace App\Models\Core;

use Illuminate\Database\Eloquent\Model;

use Cache, Log, DB;

use App\Models\Core\Estado;

class Especialidad extends Model
{
  /**
   * The database table used by the model.
   *
   * @var string
   */
  protected $table = 'core.especialidad';

  protected $primaryKey = 'codigo_especialidad';

  public $timestamps = false;

  public static function getType($type)
  {
    switch ($type) :
      case 'user':
        return 1;
      case 'theme':
        return 2;
      default:
        return 'Not type identified';
    endswitch;
  }

  public static function getSpecialty($specialty)
  {
    switch ($specialty) {
      case 'other':
        return 16;
      default:
        return 'Not type user identified';
    }
  }

  public static function getSpecialtyById($specialty)
  {
    $query = Especialidad::query();
    $query->where('codigo_especialidad', '=', $specialty);
    return $query->first();
  }

  public static function getSpecialties($type)
  {
    $query = self::query();
    $query->select('codigo_especialidad', 'nombre_especialidad');
    $query->where('tipo_especialidad', $type);
    $query->orderby('nombre_especialidad', 'asc');
    return $query->get();
  }

  public static function getSpecialtiesList( $other = null )
  {
    $specialties = [];
    $cache = sprintf('_%s_%s', '_get_user_specialties_institution', session('institution')->codigo_institucion);
    if (Cache::has($cache)) {
      $specialties = Cache::get($cache);
    }else{
      $specialties = Cache::rememberForever($cache, function() {
        $query = Especialidad::query();
        $query->select('especialidad.codigo_especialidad', 'nombre_especialidad');
        $query->join('core.especialidad_institucion', function($join){
          $join->on('especialidad.codigo_especialidad', '=', 'especialidad_institucion.codigo_especialidad')
            ->where('especialidad_institucion.codigo_institucion', '=', session('institution')->codigo_institucion );
        });
        $query->orderby('nombre_especialidad', 'asc');
        return $query->get();
      });
    }

    if( $other == Especialidad::getSpecialty('other') ){
      return $specialties->filter(function ($item) use($other)
      {
        return $item->codigo_especialidad != $other;
      })->pluck('nombre_especialidad', 'codigo_especialidad')->toArray();
    }
    $return = $specialties->pluck('nombre_especialidad', 'codigo_especialidad')->toArray();
    return $return;
  }


  public static function getSpecialitiesByUser($codigo_usuario)
  {
    $query = Especialidad::query();
    $query->select('especialidad.codigo_especialidad', 'especialidad.nombre_especialidad',
      DB::raw("(CASE WHEN especialidad_usuario.codigo_usuario IS NOT NULL THEN true ELSE false END) as comparator"));
    $query->join('core.especialidad_institucion', function($join){
      $join->on('especialidad.codigo_especialidad', '=', 'especialidad_institucion.codigo_especialidad')
        ->where('especialidad_institucion.codigo_institucion', '=', session('institution')->codigo_institucion);
    });

    $query->leftJoin('core.especialidad_usuario', function($join) use($codigo_usuario) {
      $join->on('especialidad.codigo_especialidad', '=', 'especialidad_usuario.codigo_especialidad')
        ->where('especialidad_usuario.codigo_estado', '=', Estado::getState('active'))
        ->where('especialidad_usuario.codigo_usuario', '=', $codigo_usuario);
    });
    $query->orderby('nombre_especialidad', 'asc');
    $return = $query->get();
    return $return;
  }

}
