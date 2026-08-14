<?php

namespace App\Models\Aprende;

use Illuminate\Database\Eloquent\Model;


use DB, Auth, Validator, AWS, TuProyecto, Datatables;

use App\Models\Core\Estado,
	App\Models\Core\Formulario,
	App\Models\Core\PrivacidadContenido,
	App\Models\Core\TipoAdjunto,
	App\Models\Seguridad\Modulo,
	App\Models\Core\Adjunto,
	App\Models\Aprende\AdjuntoCurso,
	App\Models\Aprende\CursoInstitucion,
	App\Models\Param\Institucion,
	App\Models\Bita\RegistroSesion;

class Curso extends Model
{
	/**
	 * The database table used by the model.
	 *
	 * @var string
	 */
    protected $table = 'aprende.curso';

   	protected $primaryKey = 'codigo_curso';

	public $timestamps = false;

	/**
     * The attributes that are mass assignable.
     *
     * @var array
     */
	protected $fillable = [
		'nombre_curso',
		'descripcion_curso',
		'fecha_apertura_curso',
		'fecha_cierre_curso',
		'codigo_categoria_curso',
		'imagen_curso',
		'codigo_orientacion_curso',
		'codigo_privacidad_contenido',
		'tipo_curso'
	];

	/**
	* Valid create content.
	*/
	public function isValid($data)
	{
		$rules = [
			'nombre_curso'					=> 'required|max:100',
			'descripcion_curso'				=> 'required',
			'fecha_apertura_curso' 			=> 'required',
			'fecha_cierre_curso' 			=> 'required',
			'codigo_categoria_curso' 		=> 'required',
			'imagen_curso'					=> 'required',
			'codigo_orientacion_curso'		=> 'required',
			'codigo_privacidad_contenido'	=> 'required',
			'tipo_curso' 					=> 'required',
    ];

		$validator = Validator::make($data, $rules);
		if ($validator->passes()) {
			return true;
		}
		$this->errors = $validator->errors();
		return false;
	}

	public static function typeCourse($type) {
    	switch ($type) :
		    case 'course': return 1;
		    case 'webinar': return 2;
		    case 'lecture': return 3;
			default: return 'Not type course identified';
		endswitch;
    }

	public static function getCourses(array $filters = [])
   	{
		$query = Curso::query();
		$query->select('curso.codigo_curso', 'curso.orden_curso', 'curso.log_curso', 'curso.log_biofacial', 'nombre_curso', 'imagen_curso', 'descripcion_curso', 'url_curso', 'url_categoria_curso', 'url_tipo_curso', 'url_externa_curso', 'fecha_apertura_curso', 'fecha_cierre_curso', 'codigo_orientacion_curso', 'curso.tipo_curso', 'tipo_curso.nombre_tipo_curso', 'codigo_privacidad_contenido', 'curso.codigo_categoria_curso', 'categoria_curso.nombre_categoria_curso', 'curso_institucion.codigo_estado');
    	$query->join('aprende.categoria_curso', 'curso.codigo_categoria_curso', '=', 'categoria_curso.codigo_categoria_curso');
    	$query->join('aprende.tipo_curso', 'curso.tipo_curso', '=', 'tipo_curso.codigo_tipo_curso');
		$query->join('aprende.curso_institucion', function($join) {
       		$join->on('curso.codigo_curso', '=', 'curso_institucion.codigo_curso')
				->where('curso_institucion.codigo_institucion', '=', session('institution')->codigo_institucion);
      	});

		// Filters
		if(isset($filters['nombre_curso']) && !empty($filters['nombre_curso'])) {
 			$query->whereRaw("curso.nombre_curso ilike '%".$filters['nombre_curso']."%'");
 		}

		if(isset($filters['tipo_curso']) && !empty($filters['tipo_curso'])) {
 			$query->where('curso.tipo_curso', $filters['tipo_curso']);
 		}

		if(isset($filters['codigo_categoria_curso']) && !empty($filters['codigo_categoria_curso'])) {
 			$query->where("categoria_curso.codigo_categoria_curso", $filters['codigo_categoria_curso']);
 		}

		if(isset($filters['codigo_estado']) && !empty($filters['codigo_estado'])) {
 			$query->where("curso_institucion.codigo_estado", $filters['codigo_estado']);
 		}

		// Privacy
		if(isset($filters['codigo_privacidad_contenido']) && !empty($filters['codigo_privacidad_contenido'])){
			$query->where('curso.codigo_privacidad_contenido', $filters['codigo_privacidad_contenido']);
		}

 		// Order
   		$query->orderBy('curso_institucion.codigo_estado','asc');
		$query->orderBy('curso.codigo_curso','desc');
   		$query->orderBy('curso.fecha_registro_curso','asc');

		// Get total posts
		$total_entries = $query->get()->count();

		$data = $query->skip($filters['offset'])->take($filters['limit'])->get();

		$response = new \stdClass();
		$response->total_entries = $data->count() > 0 ? $total_entries: 0;
		$response->items = $data;
   		return $response;
   	}

	public static function getCourse($course_code)
   	{
		$query = Curso::query();
		$query->select('curso.codigo_curso', 'curso.orden_curso', 'curso.log_curso', 'curso.log_biofacial', 'nombre_curso', 'imagen_curso', 'descripcion_curso', 'url_curso', 'url_categoria_curso', 'url_tipo_curso', 'url_externa_curso', 'fecha_apertura_curso', 'fecha_cierre_curso', 'codigo_orientacion_curso', 'curso.tipo_curso', 'tipo_curso.url_tipo_curso', 'codigo_privacidad_contenido', 'curso.codigo_categoria_curso', DB::raw('lower(categoria_curso.url_categoria_curso) as nombre_categoria_curso'), 'curso_institucion.codigo_estado', 'curso_institucion.codigo_institucion', 'contador_inscritos', 'curso_gratis', 'seo.*');
    	$query->join('aprende.categoria_curso', 'curso.codigo_categoria_curso', '=', 'categoria_curso.codigo_categoria_curso');
    	$query->join('aprende.tipo_curso', 'curso.tipo_curso', '=', 'tipo_curso.codigo_tipo_curso');
		$query->join('aprende.curso_institucion', function($join) {
       		$join->on('curso.codigo_curso', '=', 'curso_institucion.codigo_curso')
				->where('curso_institucion.codigo_institucion', '=', session('institution')->codigo_institucion);
		  });

		$query->leftJoin('core.seo', function($join){
			$join->on('curso.codigo_curso', '=', 'seo.id_recurso')
				->where('seo.codigo_modulo', Modulo::getModule('learn'));
		});

		$query->where('curso.codigo_curso', $course_code);

   		return $query->first();
   	}

	public static function getCoursesList(array $filters = [])
	{
		$query = Curso::query();
		$query->select('curso.codigo_curso as id', DB::raw('initcap(nombre_curso) as text'));
		$query->join('aprende.curso_institucion', function($join) {
       		$join->on('curso.codigo_curso', '=', 'curso_institucion.codigo_curso')
				->where('curso_institucion.codigo_institucion', '=', session('institution')->codigo_institucion)
				->where('curso_institucion.codigo_estado', '=', Estado::getState('active'));
      	});

		// Filters
		if(isset($filters['q']) && !empty($filters['q'])) {
 			$query->whereRaw("nombre_curso ilike '%".$filters['q']."%'");
 		}

		// Order
   		$query->orderBy('nombre_curso','asc');
		return $query->get()->take(50);
  }




	public static function getDataEvaluations(array $filters = []) {

      $query = Formulario::query();
      $query->select('encuesta.codigo_encuesta', 'nombre_encuesta', 'descripcion_encuesta', 'encuesta_institucion.codigo_estado', 'codigo_version_encuesta');
      $query->join('core.encuesta_institucion', function ($join){
        $join->on('encuesta.codigo_encuesta', '=', 'encuesta_institucion.codigo_encuesta')
          ->where('encuesta_institucion.codigo_institucion', '=', session('institution')->codigo_institucion);
      });

      $query->join('core.adjunto', function ($join){
        $join->on('adjunto.direccion_adjunto', '=', 'encuesta.url_encuesta')
          ->where('adjunto.codigo_tipo_adjunto', '=', TipoAdjunto::getTypeAttached('survey'));
      });
      $query->join('aprende.adjunto_tema', 'adjunto_tema.codigo_adjunto', 'adjunto.codigo_adjunto');
      $query->join('aprende.tema_curso', 'tema_curso.codigo_tema_curso', '=', 'adjunto_tema.codigo_tema_curso');



      if(isset($filters['nombre_encuesta']) && !empty($filters['nombre_encuesta'])){
        $query->whereRaw("nombre_encuesta ilike '%".trim($filters['nombre_encuesta'])."%'");
      }

      if(isset($filters['codigo_estado']) && !empty($filters['codigo_estado'])){
        $query->where("encuesta_institucion.codigo_estado", $filters['codigo_estado']);
      }

      if(isset($filters['codigo_curso']) && !empty($filters['codigo_curso'])){
        $query->where("tema_curso.codigo_curso", $filters['codigo_curso']);
      }

      $query->orderby('fecha_encuesta', 'desc');

		return Datatables::of($query)->make(true);
  }


	public static function buidCourse($course)
	{
		$object = new \stdClass();
		$object->codigo_curso = $course->codigo_curso;
		$object->nombre_curso = $course->nombre_curso;
		$object->descripcion_curso = $course->descripcion_curso;
		$object->imagen_curso = $course->imagen_curso;
		$object->url_curso = $course->url_curso;
		$object->url_categoria_curso = $course->url_categoria_curso;
		$object->url_tipo_curso = $course->url_tipo_curso;
		$object->url_externa_curso = $course->url_externa_curso;
		$object->fecha_apertura_curso = $course->fecha_apertura_curso;
		$object->fecha_cierre_curso = $course->fecha_cierre_curso;
		$object->codigo_orientacion_curso = $course->codigo_orientacion_curso;
		$object->tipo_curso = $course->tipo_curso;
		$object->url_tipo_curso = $course->url_tipo_curso;
		$object->codigo_privacidad_contenido = $course->codigo_privacidad_contenido;
		$object->codigo_categoria_curso = $course->codigo_categoria_curso;
		$object->nombre_categoria_curso = $course->nombre_categoria_curso;
		$object->codigo_estado = $course->codigo_estado;
		$object->codigo_institucion = $course->codigo_institucion;
		$object->contador_inscritos = $course->contador_inscritos;
		$object->curso_gratis = $course->curso_gratis;
		$object->orden_curso = $course->orden_curso;
		$object->total_cursos = CursoInstitucion::where('codigo_institucion', '=', session('institution')->codigo_institucion)->where('codigo_estado', '=', Estado::getState('active'))->count();
    $object->log_curso = $course->log_curso;
    $object->log_biofacial = $course->log_biofacial;

		// SEO
		$object->nombre_facebook = $course->nombre_facebook;
		$object->nombre_twitter = $course->nombre_twitter;
		$object->descripcion_facebook = $course->descripcion_facebook;
		$object->descripcion_twitter = $course->descripcion_twitter;
		$object->imagen_facebook = $course->imagen_facebook;
		$object->imagen_twitter = $course->imagen_twitter;
		$object->url_canonica = $course->url_canonica;
		$object->enlaces_motores_busqueda = $course->enlaces_motores_busqueda;
		$object->indexar_motores = $course->indexar_motores;
		$object->tags = $course->tags()->get()->implode('texto_tag', ',');
		return $object;
	}

	public static function getAttachedCourse($course) {
		try {
			$code_course = $course;
			if(!$code_course || !is_numeric($code_course)) {
				abort(404);
			}

			$curse = Curso::find($code_course);
			if (!$curse instanceof Curso) {
				abort(404);
			}

			$attach = [];
			$query = AdjuntoCurso::query();
			$query->join('core.adjunto', 'adjunto.codigo_adjunto', '=', 'adjunto_curso.codigo_adjunto');
			$query->where('adjunto_curso.codigo_curso', '=', $code_course);
			$query->where('adjunto_curso.codigo_estado', '=', Estado::getState('active'));
			$data = $query->get();

			if (count($data) > 0) {
				foreach ($data as $item) {
					$attach[] = Curso::buildAttached($item);
				}
			} else {
				return [];
			}

			return $attach;
		} catch (\Exception $e) {
			abort(404);
			return 'Algo salio mal';
		}
	}

	public static function buildAttached($course) {
		$object = new \stdClass();
		$object->uuid = TuProyecto::encryptSrcParams([$course->codigo_adjunto]);
		$object->codigo_adjunto = $course->codigo_adjunto;
		$object->codigo_tipo_adjunto = $course->codigo_tipo_adjunto;
		$object->direccion_adjunto = $course->direccion_adjunto;
		$object->privacidad_adjunto = $course->privaciodad_adjunto;
		$object->name = $course->nombre_adjunto;

		return $object;
	}

	public static function uploadAttachCourse($file_path, $file_name, $course) {
		if ($file_path) {
			$s3 = AWS::createClient('s3');

			// Dynamic bucket
			$bucket = env('AWS_BUCKET_NAME', 'static-dev.tuproyecto.com');

			$name = explode('.', $file_name);

			$key = sprintf('%s/%s/%d/%s', 'documents', 'cursos', $course, $name[0].'-'.date('YmdHis').'.'.$name[1]);

			// Upload file to S3
			$result = $s3->putObject([
				'Bucket' =>  $bucket,
				'Key'    =>  $key,
				'Body'   =>  fopen($file_path, 'rb'),
				'ACL'    => 'public-read'
			]);

			if ($result['ObjectURL']) {
				$attach = new Adjunto();
				$attach->codigo_tipo_adjunto = TipoAdjunto::getTypeAttached('document');
				$attach->direccion_adjunto = $result['ObjectURL'];
				$attach->nombre_adjunto = $file_name;
				$attach->fecha_adjunto = date('Y-m-d');
				$attach->save();

				if ($attach instanceof Adjunto) {
					$attachCourse = new AdjuntoCurso();
					$attachCourse->codigo_curso = $course;
					$attachCourse->codigo_adjunto = $attach->codigo_adjunto;
					$attachCourse->codigo_estado = Estado::getState('active');
					$attachCourse->save();
				}
			}

			$response = new \stdClass();
			$response->success = true;
			$response->file_path = $result['ObjectURL'];

			return $response;
		}
	}

	/*  Function to Show get certificate */

	public static function getTotalCertificate() {
		switch (RegistroSesion::getInstitution()) {
			case Institucion::getInstitution('propace'):
				$query = Curso::query();
				$query->select();
				$query->join('aprende.curso_institucion', function($join){
					$join->on('curso_institucion.codigo_curso', '=', 'curso.codigo_curso')
						->where('curso_institucion.codigo_institucion', '=', RegistroSesion::getInstitution())
						->where('curso_institucion.codigo_estado', '=', Estado::getState('active'));
				});
				$query->where('curso.codigo_estado', '=', Estado::getState('active'));
				$query->where('curso.tipo_curso', '=', Curso::typeCourse('course'));
				$countCourses = $query->count();

				$queryCompleted = Curso::query();
				$queryCompleted->select();
				$queryCompleted->join('aprende.curso_institucion', function($join){
					$join->on('curso_institucion.codigo_curso', '=', 'curso.codigo_curso')
						->where('curso_institucion.codigo_estado', Estado::getState('active'))
						->where('curso_institucion.codigo_institucion', '=', RegistroSesion::getInstitution());
				});
				$queryCompleted->join('aprende.inscripcion_curso', 'inscripcion_curso.codigo_curso', '=', 'curso.codigo_curso');
				$queryCompleted->where('inscripcion_curso.codigo_usuario', '=', Auth::user()->codigo_usuario);
				$queryCompleted->where('curso.tipo_curso', '=', Curso::typeCourse('course'));
				$queryCompleted->where('inscripcion_curso.porcentaje_progreso', '=', '100');
				$countCoursesCompleted = $queryCompleted->count();

				$dateInscriptionCourses = $queryCompleted->get();
				$dateInscription = [];

				foreach ($dateInscriptionCourses as $key => $value) {
					array_push($dateInscription, $value['fecha_inscripcion_curso']);
				}
				// $date = date_create(max($dateInscription));
				// $date = $date->format('Y-m');

				//se usa array $meses ya que no toma configuraciòn de idioma
				$meses = ["Enero","Febrero","Marzo","Abril","Mayo","Junio","Julio","Agosto","Septiembre","Octubre","Noviembre","Diciembre"];

				if ( !empty($dateInscription) ) {

					$newDate = date("Y-m", strtotime( max($dateInscription) ) );
					$date = strftime(" de %Y", strtotime($newDate));
					$date = $meses[ date('n', strtotime(max($dateInscription)) )-1] . $date ;

				}else {
					$date = '';
				}


				// $newDate = date("Y-m", strtotime(max($dateInscription)));
				// $date = strftime("%B de %Y", strtotime($newDate));

				if ($countCourses === $countCoursesCompleted) {
					return ['date' => $date, 'certificate' => true ];
				} else {
					return false;
				}

			break;
			default:
				return false;
			break;
		}
	}

	//reporte de curso individual
	public static function getCourseUsers(Institucion $institucion, int $codigo_curso)
	{
		$query = Curso::query();
		$query->select(
			'curso.codigo_curso', 'usuario.codigo_usuario',
			'curso.nombre_curso as nombre_curso', 'usuario.identificacion_usuario',
			'usuario.correo_electronico as correo_electronico', 'usuario.movil_usuario as movil_usuario',
			'usuario.telefono_usuario as telefono_usuario','inscripcion_curso.fecha_inscripcion_curso as fecha_inscripcion',
			'inscripcion_curso.hora_inscripcion_curso as hora_inscripcion',
			DB::raw('tipo_identificacion.nombre_tipo_identificacion as tipo_identificacion_usuario'),
			DB::raw('initcap(usuario.nombre_usuario) as nombre_persona'),
			DB::raw('initcap(persona.primer_apellido_persona) as primer_apellido'),
			DB::raw('initcap(persona.segundo_apellido_persona) as segundo_apellido'),
			DB::raw('initcap(ciudad.nombre_ciudad) as nombre_ciudad'),
			DB::raw('COALESCE(inscripcion_curso.porcentaje_progreso, 0) AS porcentaje_progreso')
		);
		$query->join('seguridad.usuario as creador','creador.codigo_usuario','=','curso.codigo_usuario');
		$query->join('aprende.curso_institucion as curso_institucion', function($join) use($institucion){
			$join->on('curso_institucion.codigo_curso','=','curso.codigo_curso')
				->where('curso_institucion.codigo_institucion','=',  $institucion->codigo_institucion );
				//->where('curso_institucion.codigo_estado','=',  Estado::getState('active') );
		});
		$query->join('aprende.inscripcion_curso as inscripcion_curso', function($join) use($institucion) {
			$join->on('inscripcion_curso.codigo_curso','=','curso.codigo_curso')
				->where('inscripcion_curso.codigo_institucion','=',$institucion->codigo_institucion)
				->where('inscripcion_curso.codigo_estado','=',  Estado::getState('active') );
		});
		$query->join('seguridad.usuario','usuario.codigo_usuario','=','inscripcion_curso.codigo_usuario');
			  $query->join('core.persona', 'usuario.codigo_usuario', '=', 'persona.codigo_usuario');
		$query->leftJoin('core.tipo_identificacion','tipo_identificacion.codigo_tipo_identificacion','=','usuario.tipo_identificacion_usuario');
		$query->leftJoin('core.ciudad','ciudad.codigo_ciudad','=','usuario.codigo_ciudad');
		$query->where('curso.codigo_estado','=', Estado::getState('active'));
		$query->where('curso.codigo_curso','=', $codigo_curso);
		$query->orderBy('fecha_inscripcion','desc');
		$query->orderBy('hora_inscripcion','desc');
		return $query->get();
	}



	/**
	 * Get the institutions course for the blog post.
	 */
	public function institutions()
	{
		return $this->hasMany('App\Models\Aprende\CursoInstitucion', 'codigo_curso', 'codigo_curso');
	}

	/**
	 * Get the institutions course for the blog post.
	 */
	public function themes()
	{
		return $this->hasMany('App\Models\Aprende\TemaCurso', 'codigo_curso', 'codigo_curso');
	}

	/**
	 * Get the tags course.
	 */
	public function tags()
    {
        return $this->belongsToMany('App\Models\Core\Tag', 'core.tag_curso', 'codigo_curso', 'codigo_tag');
    }
}
