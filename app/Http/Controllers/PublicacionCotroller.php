<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;

use DB, Log, Auth;

use App\Models\Seguridad\Modulo, App\Models\Core\Muro, App\Models\Core\MuroInstitucion, App\Models\Core\Estado, App\Models\Aprende\Curso, App\Models\Aprende\TipoCurso, App\Models\Aprende\OrientacionCurso, App\Models\Core\Contenido, App\Models\Core\Convocatoria, App\Models\Core\Evento, App\Models\Core\TipoContenido;

class PublicacionCotroller extends Controller
{
    /**
     * Instantiate a new Controller instance.
     */
    public function __construct()
    {
        $this->middleware('ability:admin|super-admin|administrativo,view,comunidad/publicacion');
        $this->middleware('ability:admin|super-admin|administrativo,create,comunidad/publicacion', ['only' => ['create', 'store']]);
    }

    /**
     * Display a listing of the resource.
     *
     * @return \Illuminate\Http\Response
     */
    public function index()
    {
		$modules = Modulo::getModules([], [Modulo::getModule('calendar'), Modulo::getModule('blog'), Modulo::getModule('learn'), Modulo::getModule('program')], $filter = null);
        return view('comunity.publish.create-edit', compact('modules'));
    }

    /**
     * Show the form for creating a new resource.
     *
     * @return \Illuminate\Http\Response
     */
    public function create()
    {
        //
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
			DB::beginTransaction();
			try {
				$data = $request->all();

				switch ($request->codigo_modulo) {
					case Modulo::getModule('calendar'):

						$event = Evento::getEvent($request->codigo_recurso_modulo);
						$link = session('institution')->url_institucion.'/calendario/evento/'.$event->url_evento;

						$data['texto_muro'] = trans('app.text.calendar', ['name' => $event->nombre_evento, 'link' => $link]);
					break;

					case Modulo::getModule('learn'):

						$course = Curso::getCourse($request->codigo_recurso_modulo);
						$type_course = TipoCurso::getCourseTypeById($course->tipo_curso);
						$type = $type_course->url_tipo_curso;

						if($course->codigo_orientacion_curso == OrientacionCurso::courseOrientation('internal')){
							$link = session('institution')->url_institucion.'/aprende/'.$type.'/'.$course->url_categoria_curso.'/'.$course->url_curso;
							$target = '';
						}

						if($course->codigo_orientacion_curso == OrientacionCurso::courseOrientation('external')){
							$link = $course->url_externa_curso;
							$target = '_blank';
						}

						$data['texto_muro'] = trans('app.text.course', ['type' => $type, 'name' => $course->nombre_curso, 'link' => $link, 'target' => $target]);
					break;

					case Modulo::getModule('blog'):

						$content = Contenido::getArticle($request->codigo_recurso_modulo);
						$contenType = TipoContenido::getType($content->codigo_tipo_cont_institucion);
						$link = session('institution')->url_institucion.'/blog/'.$contenType->url_tipo_contenido.'/'.$content->url_contenido;

						$data['texto_muro'] = trans('app.text.blog', ['name' => $content->nombre_contenido, 'link' => $link]);
					break;

					case Modulo::getModule('program'):

						$program = Convocatoria::getProgramById($request->codigo_recurso_modulo);
						if($program->tipo_convocatoria == Convocatoria::getCallType('internal')){
							$link = session('institution')->url_institucion.'/convocatoria/'.$program->url_convocatoria;
							$target = '';
						}

						if($program->tipo_convocatoria == Convocatoria::getCallType('external')){
							$link = $program->url_externa_convocatoria;
							$target = '_blank';
						}

						$data['texto_muro'] = trans('app.text.program', ['name' => $program->nombre_convocatoria, 'link' => $link, 'target' => $target]);
					break;
				}

				if($request->channel == 'on' || !is_array($request->channel)){
					$wall = Muro::store($data);
					if( !$wall instanceof Muro ) {
						throw new \Exception("Error creando post muro");
					}
				}else {
					foreach ($request->channel as $value) {
						$data['channel'] = $value;
						$wall = Muro::store($data);
						if( !$wall instanceof Muro ) {
							throw new \Exception("Error creando post muro");
						}
					}
				}

				// Commit Transaction
				DB::commit();
				return response()->json(['success' => true, 'message' => 'Se ha publicado correctamente!!']);

			}catch(\Exception $e){
				DB::rollback();
				Log::error(sprintf('%s:%s', 'PublicacionController:store', $e->getMessage()));
				return response()->json(['success' => false, 'errors' => trans('app.exception')]);
			}
		}
		abort(404);
    }
}
