<?php

namespace App\Http\Controllers;

use App\Models\Core\TipoAdjunto;
use Illuminate\Http\Request;

use DB, Auth, Log;

use App\Http\Requests;
use App\Http\Controllers\Controller;

use App\Models\Aprende\Curso, App\Models\Aprende\TipoCurso, App\Models\Core\Contenido, App\Models\Aprende\CategoriaCurso, App\Models\Aprende\CursoInstitucion, App\Models\Aprende\OrientacionCurso, App\Models\Core\Estado, App\Models\Core\PrivacidadContenido, App\Models\Core\Tag, App\Models\Core\Seo, App\Models\Seguridad\Modulo;
use App\Models\Seguridad\Usuario;
class CursoController extends Controller
{
	/**
     * Instantiate a new Controller instance.
     */
    public function __construct()
    {
        $this->middleware('ability:admin|super-admin|administrativo,view,curso', ['except' => ['search']]);
        $this->middleware('ability:admin|super-admin|administrativo,create,curso', ['only' => ['create', 'store']]);
        $this->middleware('ability:admin|super-admin|administrativo,edit,curso', ['only' => ['edit', 'update']]);
        $this->middleware('ability:admin|super-admin|administrativo,remove,curso', ['only' => ['destroy']]);
    }

    /**
     * Display a listing of the resource.
     *
     * @return \Illuminate\Http\Response
     */
    public function index(Request $request)
    {
		if($request->ajax()) {
			switch ($request->data) {
				case 'list':
					return response()->json(Curso::getCoursesList( $request->all() ));
					break;
				default:
					return response()->json(Curso::getCourses( $request->all() ));
			}
        }

		$courseTypes = TipoCurso::getCourseType();
		$courseCategories = CategoriaCurso::getCourseCategory();
		$privacyContent = PrivacidadContenido::getContentPrivacy();
		$states = Estado::getStates([], [Estado::getState('active'), Estado::getState('inactive')], $filter = null);

		// ' attachmentsType ' se genera error al incluir la variable dentro del Compact
		return view('course.index', compact('courseTypes', 'courseCategories', 'states', 'privacyContent'));
    }

    /**
     * Show the form for creating a new resource.
     *
     * @return \Illuminate\Http\Response
     */
    public function create()
    {
		$coursesType = TipoCurso::getCourseType();
		$courseCategories = CategoriaCurso::getCourseCategory();
		$privacyContent = PrivacidadContenido::getContentPrivacy();
		$courseOrientation = OrientacionCurso::getCoursOrientation();
		$public = PrivacidadContenido::getPrivacy('public');
		$attachmentTypes = TipoAdjunto::getAttachmentTypeList();

        return view('course.create-edit', compact('coursesType', 'courseCategories', 'privacyContent', 'courseOrientation', 'public', 'attachmentTypes'));
    }


    /**
     * Store a newly created resource in storage.
     *
     * @param  \Illuminate\Http\Request  $request
     * @return \Illuminate\Http\Response
     */
    public function store(Request $request)
    {
		if($request->ajax()){
			$data = $request->all();

			$course = new Curso;
			if ($course->isValid($data)) {
				DB::beginTransaction();
	            try {

					$course->fill($data);
					$course->url_curso = slugtp($request->nombre_curso, $course->getTable(), 'url_curso');
					$course->codigo_estado = Estado::getState('active');
					$course->fecha_registro_curso = date('Y-m-d');
					$course->codigo_usuario = Auth::user()->codigo_usuario;
					$course->url_externa_curso = $request->url_externa_curso ? $request->url_externa_curso : '';
					$course->curso_gratis = $request->curso_gratis ? TRUE : FALSE;
          $course->log_curso = $request->log_curso ? TRUE : FALSE;
          $course->log_biofacial= $request->log_biofacial ? TRUE : FALSE;


					$course->save();

					$institutionCourse = new CursoInstitucion;
                    $institutionCourse->codigo_curso = $course->codigo_curso;
                    $institutionCourse->codigo_institucion = session('institution')->codigo_institucion;
                    $institutionCourse->codigo_estado = Estado::getState('active');
					$institutionCourse->save();

                    // Commit Transaction
                    DB::commit();
                    return response()->json(['success' => true, 'message' => 'El curso ha sido creado correctamente!!', 'codigo_curso' => $course->codigo_curso, 'redirect' => true]);

				}catch(\Exception $e){
	                DB::rollback();
	                Log::error(sprintf('%s:%s', 'CursoController:store', $e->getMessage()));
	                return response()->json(['success' => false, 'errors' => trans('app.exception')]);
	            }
			}
			return response()->json(['success' => false, 'errors' => $course->errors]);
		}
		abort(404);
    }

    /**
     * Display the specified resource.
     *
     * @param  int  $id
     * @return \Illuminate\Http\Response
     */
    public function show(Request $request, $id)
    {

      $data = $request->all();

		if(!is_numeric($id)) {
            abort(404);
        }

    $course = Curso::getCourse($id);

		if(!$course instanceof Curso){
			abort(404);
		}

		$course = Curso::buidCourse( $course );

    $states = Estado::getStates([], [Estado::getState('active'), Estado::getState('inactive')], $filter = null);

    return view('course.detail', compact('course', 'states'));

    }

    /**
     * Show the form for editing the specified resource.
     *
     * @param  int  $id
     * @return \Illuminate\Http\Response
     */
    public function edit($id)
    {
		if(!is_numeric($id)) {
			Log::error(sprintf('%s: id = %s is not numeric', 'CursoController:edit', $id));
            abort(404);
        }

		$course = Curso::getCourse($id);
		if(!$course instanceof Curso){
			abort(404);
		}

		$course = Curso::buidCourse( $course );

		$coursesType = TipoCurso::getCourseType();
		$courseCategories = CategoriaCurso::getCourseCategory();
		$privacyContent = PrivacidadContenido::getContentPrivacy();
		$courseOrientation = OrientacionCurso::getCoursOrientation();
		$attachmentTypes = TipoAdjunto::getAttachmentTypeList();


        return view('course.create-edit', compact('course', 'coursesType', 'courseCategories', 'privacyContent', 'courseOrientation', 'attachmentTypes'));
    }

    /**
     * Update the specified resource in storage.
     *
     * @param  \Illuminate\Http\Request  $request
     * @param  int  $id
     * @return \Illuminate\Http\Response
     */
    public function update(Request $request, $id)
    {
		if($request->ajax()){
			$data = $request->all();

			$course = Curso::find($id);
			if(!$course instanceof Curso) {
				return response()->json(['success' => false, 'errors' => 'El código del curso no se encuentra en el sistema ']);
			}

			$institutionCourse = $course->institutions->where('codigo_institucion', session('institution')->codigo_institucion)->first();

			if(!$institutionCourse instanceof CursoInstitucion) {
				return response()->json(['success' => false, 'errors' => 'El curso no se encuentra en el sistema ']);
			}

			DB::beginTransaction();
            try {
				$course->fill($data);
				$course->fecha_registro_curso = date('Y-m-d');
				$course->codigo_usuario = Auth::user()->codigo_usuario;
				$course->url_externa_curso = $request->url_externa_curso ? $request->url_externa_curso : '';
				$course->curso_gratis = $request->curso_gratis ? TRUE : FALSE;
				$course->orden_curso = $request->orden_curso != "" ? $request->orden_curso : null;
        $course->log_curso = $request->log_curso ? $request->log_curso : FALSE;
        $course->log_biofacial= $request->log_biofacial ? $request->log_biofacial : FALSE;
				$course->save();

				$codigo_estado = $request->has('change_state') ? ($institutionCourse->codigo_estado == Estado::getState('active') ? Estado::getState('inactive') : Estado::getState('active')) : $institutionCourse->codigo_estado;

				if(!$request->has('seo')){
					CursoInstitucion::where('codigo_institucion', $institutionCourse->codigo_institucion)
						->where('codigo_curso', $institutionCourse->codigo_curso)
						->update([
							'codigo_estado' => $codigo_estado
						]);
				}

				if($request->has('seo')){
					// Info seo
					$seo = Seo::where('id_recurso', $course->codigo_curso)->where('codigo_modulo', Modulo::getModule('learn'))->first();
					if(!$seo instanceof Seo){
						$seo = new Seo;
					}

					$seo->fill($data);
					$seo->id_recurso = $course->codigo_curso;
					$seo->codigo_modulo = Modulo::getModule('learn');
					$seo->codigo_estado = Estado::getState('active');

					if($request->has('seo_general') && $request->seo_general){
						$seo->indexar_motores = $request->indexar_motores ? TRUE : FALSE;
						$seo->enlaces_motores_busqueda = $request->enlaces_motores_busqueda ? TRUE : FALSE;
					}

					$seo->codigo_institucion = session('institution')->codigo_institucion;
					$seo->save();

					// Tags user
					$inputTags = [];
					if($request->update_tags){
						$inputTags = explode(',', $request->tags_curso);
						$tags = $course->tags()->get();
						foreach ($tags as $tag) {
							if(!in_array($tag->texto_tag, $inputTags)) {
								$course->tags()->detach($tag);
							}
						}
					}

					if($request->has('tags_curso') && is_array($inputTags) && count($inputTags) > 0) {
						foreach($inputTags as $item) {
							if(!$tags->contains('texto_tag', $item)){
								$tag = Tag::firstOrCreate(['texto_tag' => $item]);
								if(!$tag instanceof Tag) {
									throw new \Exception('tag not found');
								}
								$course->tags()->attach($tag);
							}
						}
					}
				}

				if ($codigo_estado == Estado::getState('inactive')) {
					$log = 'activitylog.course.deactivated';
				} elseif ($codigo_estado == Estado::getState('active')) {
					$log = 'activitylog.course.activated';
				}

				$user = Usuario::find(\Auth::user()->codigo_usuario);

				activity()
				->performedOn($user)
				->withProperties([
					'attributes' => [
					'user' => $user->nombre_usuario,
					'user_id' => $user->codigo_usuario,
					'course' => $course->nombre_curso
					]
				])
				->log($log);

                // Commit Transaction
                DB::commit();
                return response()->json(['success' => true, 'message' => 'El curso ha sido editado correctamente!!', 'codigo_curso' => $course->codigo_curso, 'codigo_estado' => $codigo_estado]);

			}catch(\Exception $e){
                DB::rollback();
                Log::error(sprintf('%s:%s', 'CursoController:update', $e->getMessage()));
                return response()->json(['success' => false, 'errors' => trans('app.exception')]);
            }
		}
		abort(404);
    }

    /**
     * Remove the specified resource from storage.
     *
     * @param  int  $id
     * @return \Illuminate\Http\Response
     */
    public function destroy($id)
    {
        //
    }

	public function search(Request $request)
	{
		if($request->ajax()){
			return response()->json(Curso::getCoursesList($request->all()));
		}
		abort(403);
	}
}
