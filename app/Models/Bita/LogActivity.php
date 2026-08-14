<?php

namespace App\Models\Bita;

use DB;

use App\Models\Seguridad\Usuario;

use Spatie\Activitylog\Models\Activity;

class LogActivity extends Activity
{
    protected $table = 'bita.activity_log';

	public static function getActivitiesList(array $filters = [])
	{
		$query = LogActivity::query();
		$query->select('activity_log.*', 'usuario.codigo_usuario',
			DB::raw("initcap( (usuario.nombre_usuario || ' ' || persona.primer_apellido_persona || ' ' || persona.segundo_apellido_persona) ) as nombre_usuario"));
		$query->join('seguridad.usuario', 'activity_log.causer_id', '=', 'usuario.codigo_usuario');
		$query->join('core.persona','usuario.codigo_usuario','=','persona.codigo_usuario');
		$query->where('activity_log.codigo_institucion', '=', session('institution')->codigo_institucion);

		if(isset($filters['causer']) && !empty($filters['causer'])) {

      $query->where(function ($query) use($filters) {
        $query->where('causer_id', '=', $filters['causer']);
        $query->orWhere( function($query) use($filters) {
          $query->where('subject_type', '=', 'App\Models\Seguridad\Usuario');
          $query->where('subject_id', '=', $filters['causer']);
        });
          
      });
		}

		if(isset($filters['subject']) && !empty($filters['subject'])) {
			$query->where('subject_id', '=', $filters['subject']);
		}

		if(isset($filters['subject_type']) && !empty($filters['subject_type'])) {
			$query->where('subject_type', '=', str_replace('.', '\\', $filters['subject_type']));
		}
    
		$total_entries = $query->count();

		$query->orderBy('created_at', 'desc');
		$data = $query->skip($filters['offset'])->take($filters['limit'])->get();

		foreach ($data as $item) {
			$changes = $item->changes();
			$attributes = isset($changes['attributes']) ? $changes['attributes'] : [];
			$old = isset($changes['old']) ? $changes['old'] : [];

			$fields = [];
			if(is_array($old) && count($old) > 0) {
				foreach ($attributes as $field => $value) {
					if($old[$field] != $attributes[$field]) {
						$fields[] = trans( 'activitylog.field_change', ['field' => transtp("fields.rol.$field"), 'old_value' => trim($old[$field]), 'new_value' => trim($attributes[$field]) ]);
					}
				}
			}

			$activity = sprintf('%s %s', ( (isset($filters['type']) && $filters['type'] == 'full') || count($old) <= 0) ? trans($item->description, $attributes) : '', implode(',', $fields));

			$item->description = trim($activity);
		}

		$response = new \stdClass();
		$response->total_entries = $total_entries;
		$response->items = $data;
		return $response;
	}

	public static function getActivityEvents($user) {
    $query = LogActivity::query();
    $query->select('activity_log.*', 'usuario.codigo_usuario',
      DB::raw("initcap( (usuario.nombre_usuario || ' ' || persona.primer_apellido_persona || ' ' || persona.segundo_apellido_persona) ) as nombre_usuario"));
    $query->join('seguridad.usuario', 'activity_log.causer_id', '=', 'usuario.codigo_usuario');
    $query->join('core.persona','usuario.codigo_usuario','=','persona.codigo_usuario');
    $query->where('activity_log.codigo_institucion', '=', session('institution')->codigo_institucion);
    $query->where('causer_id', '=', $user);
    $query->where('subject_type', '=', 'App\UsuarioEvento');
    $query->where('log_name', '=', 'platform');
    $query->orWhere(function ($query) use ($user) {
      $query->where('subject_type', '=', 'App\Models\Core\UsuarioEvento');
      $query->where('causer_id', '=', $user);
    });

    $total_entries = $query->count();
    $data = $query->get();

    foreach ($data as $item) {
      $changes = $item->changes();
      $attributes = isset($changes['attributes']) ? $changes['attributes'] : [];
      $old = isset($changes['old']) ? $changes['old'] : [];

      $fields = [];
      if(is_array($old) && count($old) > 0) {
        foreach ($attributes as $field => $value) {
          if($old[$field] != $attributes[$field]) {
            $fields[] = trans( 'activitylog.field_change', ['field' => transtp("fields.rol.$field"), 'old_value' => trim($old[$field]), 'new_value' => trim($attributes[$field]) ]);
          }
        }
      }

      $activity = sprintf('%s', trans($item->description, $attributes));

      $item->description = trim($activity);
    }

    $response = new \stdClass();
    $response->total_entries = $total_entries;
    $response->items = $data;
    return $response;
  }

  public static function getActivityCall($user) {
    $query = LogActivity::query();
    $query->select('activity_log.*', 'usuario.codigo_usuario',
      DB::raw("initcap( (usuario.nombre_usuario || ' ' || persona.primer_apellido_persona || ' ' || persona.segundo_apellido_persona) ) as nombre_usuario"));
    $query->join('seguridad.usuario', 'activity_log.causer_id', '=', 'usuario.codigo_usuario');
    $query->join('core.persona','usuario.codigo_usuario','=','persona.codigo_usuario');
    $query->where('activity_log.codigo_institucion', '=', session('institution')->codigo_institucion);
    $query->where('causer_id', '=', $user);
    $query->where('subject_id', '=', $user);
    $query->where('subject_type', '=', 'App\Usuario');
    $query->where('log_name', '=', 'platform');
    $query->where('description', '=', 'activitylog.call.inscription');

    $total_entries = $query->count();
    $data = $query->get();

    foreach ($data as $item) {
      $changes = $item->changes();
      $attributes = isset($changes['attributes']) ? $changes['attributes'] : [];
      $old = isset($changes['old']) ? $changes['old'] : [];

      $fields = [];
      if(is_array($old) && count($old) > 0) {
        foreach ($attributes as $field => $value) {
          if($old[$field] != $attributes[$field]) {
            $fields[] = trans( 'activitylog.field_change', ['field' => transtp("fields.rol.$field"), 'old_value' => trim($old[$field]), 'new_value' => trim($attributes[$field]) ]);
          }
        }
      }

      $activity = sprintf('%s', trans($item->description, $attributes));

      $item->description = trim($activity);
    }

    $response = new \stdClass();
    $response->total_entries = $total_entries;
    $response->items = $data;
    return $response;
  }

  public static function getActivityCurses($user) {
    $query = LogActivity::query();
    $query->select('activity_log.*', 'usuario.codigo_usuario',
      DB::raw("initcap( (usuario.nombre_usuario || ' ' || persona.primer_apellido_persona || ' ' || persona.segundo_apellido_persona) ) as nombre_usuario"));
    $query->join('seguridad.usuario', 'activity_log.causer_id', '=', 'usuario.codigo_usuario');
    $query->join('core.persona','usuario.codigo_usuario','=','persona.codigo_usuario');
    $query->where('activity_log.codigo_institucion', '=', session('institution')->codigo_institucion);
    $query->where('causer_id', '=', $user);
    $query->where('subject_type', '=', 'App\InscripcionCurso');
    $query->where('log_name', '=', 'platform');

    $total_entries = $query->count();
    $data = $query->get();

    foreach ($data as $item) {
      $changes = $item->changes();
      $attributes = isset($changes['attributes']) ? $changes['attributes'] : [];
      $old = isset($changes['old']) ? $changes['old'] : [];

      $fields = [];
      if(is_array($old) && count($old) > 0) {
        foreach ($attributes as $field => $value) {
          if($old[$field] != $attributes[$field]) {
            $fields[] = trans( 'activitylog.field_change', ['field' => transtp("fields.rol.$field"), 'old_value' => trim($old[$field]), 'new_value' => trim($attributes[$field]) ]);
          }
        }
      }

      $activity = sprintf('%s', trans($item->description, $attributes));

      $item->description = trim($activity);
    }

    $response = new \stdClass();
    $response->total_entries = $total_entries;
    $response->items = $data;
    return $response;
  }

  public static function getActivityAsignMentorProject($user) {
    $query = LogActivity::query();
    $query->select('activity_log.*', 'usuario.codigo_usuario',
      DB::raw("initcap( (usuario.nombre_usuario || ' ' || persona.primer_apellido_persona || ' ' || persona.segundo_apellido_persona) ) as nombre_usuario"));
    $query->join('seguridad.usuario', 'activity_log.causer_id', '=', 'usuario.codigo_usuario');
    $query->join('core.persona','usuario.codigo_usuario','=','persona.codigo_usuario');
    $query->where('activity_log.codigo_institucion', '=', session('institution')->codigo_institucion);
    $query->where('causer_id', '=', $user);
    $query->where('subject_type', '=', 'App\Models\Core\Entidad');
    $query->where('log_name', '=', 'administrator');

    $total_entries = $query->count();
    $data = $query->get();

    foreach ($data as $item) {
      $changes = $item->changes();
      $attributes = isset($changes['attributes']) ? $changes['attributes'] : [];
      $old = isset($changes['old']) ? $changes['old'] : [];

      $fields = [];
      if(is_array($old) && count($old) > 0) {
        foreach ($attributes as $field => $value) {
          if($old[$field] != $attributes[$field]) {
            $fields[] = trans( 'activitylog.field_change', ['field' => transtp("fields.rol.$field"), 'old_value' => trim($old[$field]), 'new_value' => trim($attributes[$field]) ]);
          }
        }
      }

      $activity = sprintf('%s', trans($item->description, $attributes));

      $item->description = trim($activity);
    }

    $response = new \stdClass();
    $response->total_entries = $total_entries;
    $response->items = $data;
    return $response;
  }

  public static function getActivityAsignJudgeProject($user) {
    $query = LogActivity::query();
    $query->select('activity_log.*', 'usuario.codigo_usuario',
      DB::raw("initcap( (usuario.nombre_usuario || ' ' || persona.primer_apellido_persona || ' ' || persona.segundo_apellido_persona) ) as nombre_usuario"));
    $query->join('seguridad.usuario', 'activity_log.causer_id', '=', 'usuario.codigo_usuario');
    $query->join('core.persona','usuario.codigo_usuario','=','persona.codigo_usuario');
    $query->where('activity_log.codigo_institucion', '=', session('institution')->codigo_institucion);
    $query->where('causer_id', '=', $user);
    $query->where('subject_type', '=', 'App\Models\Core\AsignacionJurado');
    $query->where('log_name', '=', 'administrator');
    $query->orWhere(function ($query) use ($user) {
      $query->where('subject_type', '=', 'App\AsignacionJurado');
      $query->where('causer_id', '=', $user);
    });

    $total_entries = $query->count();
    $data = $query->get();

    foreach ($data as $item) {
      $changes = $item->changes();
      $attributes = isset($changes['attributes']) ? $changes['attributes'] : [];
      $old = isset($changes['old']) ? $changes['old'] : [];

      $fields = [];
      if(is_array($old) && count($old) > 0) {
        foreach ($attributes as $field => $value) {
          if($old[$field] != $attributes[$field]) {
            $fields[] = trans( 'activitylog.field_change', ['field' => transtp("fields.rol.$field"), 'old_value' => trim($old[$field]), 'new_value' => trim($attributes[$field]) ]);
          }
        }
      }

      $activity = sprintf('%s', trans($item->description, $attributes));

      $item->description = trim($activity);
    }

    $response = new \stdClass();
    $response->total_entries = $total_entries;
    $response->items = $data;
    return $response;
  }
}
