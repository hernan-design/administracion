<?php

namespace App\Models\Core;

use Illuminate\Database\Eloquent\Model;

use Datatables, DB, Log;

use App\Models\Seguridad\Usuario;

class Asesoria extends Model
{
	/**
	 * The database table used by the model.
	 *
	 * @var string
	 */
    protected $table = 'core.asesoria';

   	protected $primaryKey = 'codigo_asesoria';

   	public $timestamps = false;



	public static function mentoring(array $filters = [])
	{
		$query = Asesoria::query();
		$query->select('asesoria_institucion.codigo_asesoria', 'descripcion_asesoria', 'fecha_asesoria', 'hora_asesoria', 'contador_discusiones_asesoria', 'asesoria_institucion.codigo_estado',
			DB::raw("initcap(nombre_estado) as nombre_estado"),
			DB::raw("initcap( (CASE mentor.tipo_usuario WHEN ".Usuario::typeUser('person')." THEN mentor.nombre_usuario || ' ' || COALESCE(pmentor.primer_apellido_persona, '') || ' ' || COALESCE(pmentor.segundo_apellido_persona, '') ELSE mentor.nombre_usuario END) ) as nombre_mentor"), 'mentor.codigo_usuario as codigo_mentor', 'mentor.imagen_usuario as imagen_mentor', 'mentor.correo_electronico as correo_mentor', 'mentor.nickname_usuario as nickname_mentor', 'cmentor.nombre_ciudad as ciudad_mentor',
			DB::raw("(CURRENT_DATE - fecha_asesoria ) as dias")
		);

		$query->join('aprende.asesoria_institucion', function($join) {
			$join->on('asesoria.codigo_asesoria', '=', 'asesoria_institucion.codigo_asesoria')
				->where('asesoria_institucion.codigo_institucion', '=', session('institution')->codigo_institucion)
				->whereNotIn('asesoria_institucion.codigo_estado', [ Estado::getState('inactive'), Estado::getState('removed') ] );
		});

		$query->join('core.estado', 'asesoria_institucion.codigo_estado', '=', 'estado.codigo_estado');

		$query->join('core.tipo_asesoria', 'asesoria.codigo_tipo_asesoria', '=', 'tipo_asesoria.codigo_tipo_asesoria');

		// Mentor
		$query->join('seguridad.usuario as mentor', 'asesoria.seg_codigo_usuario', '=', 'mentor.codigo_usuario');
		$query->leftJoin('core.persona as pmentor', 'mentor.codigo_usuario', '=', 'pmentor.codigo_usuario');
		$query->leftJoin('core.ciudad as cmentor', 'mentor.codigo_ciudad', '=', 'cmentor.codigo_ciudad');

		$query->join('core.entidad', 'asesoria.codigo_entidad', '=', 'entidad.codigo_entidad');
		$query->join('seguridad.usuario as entity', 'entidad.codigo_usuario', '=', 'entity.codigo_usuario');

		if(isset($filters['code_entity']) && !empty($filters['code_entity'])){
			$query->where('entidad.codigo_entidad', '=', $filters['code_entity']);
			return $query->first();
		}

		if(isset($filters['entity']) && !empty($filters['entity'])){
			$query->where('entity.nickname_usuario', '=', $filters['entity']);
		}

		if( isset($filters['correo_electronico']) && !empty($filters['correo_electronico']) ) {
			$query->whereRaw("mentor.correo_electronico ilike '%".$filters['correo_electronico']."%'" );
		}

		if(isset($filters['nombre_usuario']) && !empty($filters['nombre_usuario'])){
			$query->whereRaw("(COALESCE(trim(mentor.nombre_usuario), '') || ' ' || COALESCE(trim(pmentor.primer_apellido_persona), '') || ' ' || COALESCE(trim(pmentor.segundo_apellido_persona), '')) ilike '%".trim($filters['nombre_usuario'])."%'");
		}

		$query->orderBy('fecha_asesoria', 'desc');
		$query->orderBy('hora_asesoria', 'desc');

		// Get total posts
		$total_entries = $query->get()->count();

		// Get posts
		$data = $query->skip($filters['offset'])->take($filters['limit'])->get();

		$response = new \stdClass();
		$response->total_entries = $total_entries;
		$response->items = $data;
   		return $response;
	}

	public static function getMentoring(array $filters = [])
	{
		$query = Asesoria::query();
		$query->select('asesoria.codigo_asesoria', 'descripcion_asesoria', 'fecha_asesoria', 'hora_asesoria', 'contador_discusiones_asesoria', 'asesoria_institucion.codigo_estado', 'emprendimiento.nombre_usuario as nombre_emprendimiento',
			DB::raw("initcap(nombre_estado) as nombre_estado"),
			DB::raw("initcap( (CASE mentor.tipo_usuario WHEN ".Usuario::typeUser('person')." THEN mentor.nombre_usuario || ' ' || COALESCE(pmentor.primer_apellido_persona, '') || ' ' || COALESCE(pmentor.segundo_apellido_persona, '') ELSE mentor.nombre_usuario END) ) as nombre_mentor"),
			DB::raw("initcap( (CASE emprendedor.tipo_usuario WHEN ".Usuario::typeUser('person')." THEN emprendedor.nombre_usuario || ' ' || COALESCE(pemprendedor.primer_apellido_persona, '') || ' ' || COALESCE(pemprendedor.segundo_apellido_persona, '') ELSE emprendedor.nombre_usuario END) ) as nombre_emprendedor"),
			DB::raw("(CURRENT_DATE - fecha_asesoria ) as dias")
		);
		$query->join('aprende.asesoria_institucion', function($join) {
			$join->on('asesoria.codigo_asesoria', '=', 'asesoria_institucion.codigo_asesoria')
				->where('asesoria_institucion.codigo_institucion', '=', session('institution')->codigo_institucion);
		});

		$query->join('core.estado', 'asesoria_institucion.codigo_estado', '=', 'estado.codigo_estado');

		$query->join('core.tipo_asesoria', 'asesoria.codigo_tipo_asesoria', '=', 'tipo_asesoria.codigo_tipo_asesoria');

		// Mentor
		$query->join('seguridad.usuario as mentor', 'asesoria.seg_codigo_usuario', '=', 'mentor.codigo_usuario');
		$query->leftJoin('core.persona as pmentor', 'mentor.codigo_usuario', '=', 'pmentor.codigo_usuario');

		// Emprendedor
		$query->join('seguridad.usuario as emprendedor', 'asesoria.codigo_usuario', '=', 'emprendedor.codigo_usuario');
		$query->leftJoin('core.persona as pemprendedor', 'emprendedor.codigo_usuario', '=', 'pemprendedor.codigo_usuario');

		// Emprendimiento
		$query->join('core.entidad', 'asesoria.codigo_entidad', '=', 'entidad.codigo_entidad');
		$query->join('seguridad.usuario as emprendimiento', 'entidad.codigo_usuario', '=', 'emprendimiento.codigo_usuario');

	    if( isset($filters['codigo_estado']) && !empty($filters['codigo_estado']) ) {
			$query->where('asesoria_institucion.codigo_estado', '=', $filters['codigo_estado']);
		}

	    if( isset($filters['correo_electronico_mentor']) && !empty($filters['correo_electronico_mentor']) ) {
			$query->whereRaw("mentor.correo_electronico ilike '%".$filters['correo_electronico_mentor']."%'" );
		}

	    if( isset($filters['correo_electronico_emprendedor']) && !empty($filters['correo_electronico_emprendedor']) ) {
			$query->whereRaw("emprendedor.correo_electronico ilike '%".$filters['correo_electronico_emprendedor']."%'" );
		}

	    if( isset($filters['codigo_tipo_asesoria']) && !empty($filters['codigo_tipo_asesoria']) ) {
			$query->where('tipo_asesoria.codigo_tipo_asesoria', $filters['codigo_tipo_asesoria']);
		}


		 // Filter Order
		 $order = null;
		 $columns = null;
		 $dir = 'asc';
		//  dd($filters);


		 if( isset($filters['order']) && !empty($filters['order']) && isset($filters['columns']) && !empty($filters['columns']) ) {
			 $order = $filters['order'];
			 $columns = $filters['columns'];
			 $dir = (isset($order[0]['dir']) && !empty($order[0]['dir'])) ? $order[0]['dir'] : 'asc';
			if(isset($order[0]['column']) && !empty($order[0]['column']) && is_numeric($order[0]['column']) && isset($columns[$order[0]['column']]['data']) && !empty($columns[$order[0]['column']]['data']) ){
				if($columns[$order[0]['column']]['name'] === 'fecha_asesoria'){
					$query->orderBy('asesoria.fecha_asesoria', $dir);
					$query->orderBy('asesoria.hora_asesoria', $dir);
				}else{
					$query->orderBy($columns[$order[0]['column']]['data'], $dir);
					$query->orderBy('asesoria.fecha_asesoria', 'desc');
					$query->orderBy('asesoria.hora_asesoria', 'desc');
				}
			 }else {
				$query->orderBy('asesoria.fecha_asesoria', 'desc');
				$query->orderBy('asesoria.hora_asesoria', 'desc');

			 }
		 }else{
			$query->orderBy('asesoria.fecha_asesoria', 'desc');
			$query->orderBy('asesoria.hora_asesoria', 'desc');

		 }

		return Datatables::of($query)->make(true);
	}

	public static function getMentor($codigo_asesoria)
	{
		\Log::info('codigo asesoria en Asesoria: ' . $codigo_asesoria);
		\Log::info('Sesión: ' . session('institution'));
		$query = Asesoria::query();
		$query->select('asesoria.codigo_asesoria', 'descripcion_asesoria', 'fecha_asesoria', DB::raw("initcap(nombre_estado) as nombre_estado"), 'mentor.nickname_usuario as nickname_mentor', 'emprendedor.nickname_usuario as nickname_emprendedor', 'mentor.imagen_usuario as imagen_mentor', 'emprendedor.imagen_usuario as imagen_emprendedor', 'mentor.correo_electronico as correo_electronico_mentor', 'emprendedor.correo_electronico as correo_electronico_emprendedor',
			DB::raw("initcap( (CASE mentor.tipo_usuario WHEN ".Usuario::typeUser('person')." THEN mentor.nombre_usuario || ' ' || COALESCE(pmentor.primer_apellido_persona, '') || ' ' || COALESCE(pmentor.segundo_apellido_persona, '') ELSE mentor.nombre_usuario END) ) as nombre_mentor"),
			DB::raw("initcap( (CASE emprendedor.tipo_usuario WHEN ".Usuario::typeUser('person')." THEN emprendedor.nombre_usuario || ' ' || COALESCE(pemprendedor.primer_apellido_persona, '') || ' ' || COALESCE(pemprendedor.segundo_apellido_persona, '') ELSE emprendedor.nombre_usuario END) ) as nombre_emprendedor"), 'mentor_institution.promedio_evaluacion_mentor', 'entrepreneur_institution.promedio_evaluacion_emprendedor', 'emprendimiento.nombre_usuario as nombre_emprendimiento'
		);
		$query->join('aprende.asesoria_institucion', function($join) {
			$join->on('asesoria.codigo_asesoria', '=', 'asesoria_institucion.codigo_asesoria')
				->where('asesoria_institucion.codigo_institucion', '=', session('institution')->codigo_institucion);
		});

		$query->join('core.estado', 'asesoria.codigo_estado', '=', 'estado.codigo_estado');

		$query->join('core.tipo_asesoria', 'asesoria.codigo_tipo_asesoria', '=', 'tipo_asesoria.codigo_tipo_asesoria');

		// Mentor
		$query->join('seguridad.usuario as mentor', 'asesoria.seg_codigo_usuario', '=', 'mentor.codigo_usuario');

		// Mentor institution
		$query->join('core.usuario_institucion AS mentor_institution', function($join) {
			$join->on('mentor_institution.codigo_usuario', '=', 'mentor.codigo_usuario')
				->where('mentor_institution.codigo_institucion', '=', session('institution')->codigo_institucion);
		});
		$query->leftJoin('core.persona as pmentor', 'mentor.codigo_usuario', '=', 'pmentor.codigo_usuario');

		// Emprendedor
		$query->join('seguridad.usuario as emprendedor', 'asesoria.codigo_usuario', '=', 'emprendedor.codigo_usuario');

		// Emprendedor institution
		$query->join('core.usuario_institucion AS entrepreneur_institution', function($join) {
			$join->on('entrepreneur_institution.codigo_usuario', '=', 'emprendedor.codigo_usuario')
				->where('entrepreneur_institution.codigo_institucion', '=', session('institution')->codigo_institucion);
		});
		$query->leftJoin('core.persona as pemprendedor', 'emprendedor.codigo_usuario', '=', 'pemprendedor.codigo_usuario');

		// Emprendimiento
		$query->join('core.entidad', 'asesoria.codigo_entidad', '=', 'entidad.codigo_entidad');
		$query->join('seguridad.usuario as emprendimiento', 'entidad.codigo_usuario', '=', 'emprendimiento.codigo_usuario');


		if($codigo_asesoria){
			$query->where('asesoria.codigo_asesoria', '=', $codigo_asesoria);
			return $query->first();
		}
	}

	public function attachedAdvice()
	{
		return $this->belongsToMany('App\Models\Core\Adjunto', 'core.adjunto_asesoria', 'codigo_asesoria', 'codigo_adjunto')->where('codigo_estado', '=', Estado::getState('active'));
	}

	public function usuario ()
	{
		return $this->hasOne('App\Models\Seguridad\Usuario', 'codigo_usuario', 'codigo_usuario');
	}

	public function mentoriamasiva ()
	{
		return $this->hasOne('App\Models\Core\MentoriaMasiva', 'codigo_mentoria_masiva', 'codigo_mentoria_masiva');
	}
}
