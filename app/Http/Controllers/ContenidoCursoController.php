<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;

use DB, Log;

use App\Http\Requests;
use App\Http\Controllers\Controller;

use App\Models\Aprende\ContenidoCurso, App\Models\Core\Adjunto, App\Models\Aprende\AdjuntoTema, App\Models\Core\Estado,  App\Models\Core\LugarAplicacion, App\Models\Core\EncuestaInstitucion, App\Models\Core\Encuesta, App\Models\Core\PrivacidadContenido, App\Models\Core\TipoAdjunto;

class ContenidoCursoController extends Controller
{
	/**
     * Instantiate a new Controller instance.
     */
    public function __construct()
    {
        $this->middleware('ability:admin|super-admin|administrativo,view,curso');
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
		if($request->has('themeCourse')){
			if ($request->ajax()) {

				$courseContents = ContenidoCurso::getCourseContents( $request->all() );
				if(!$courseContents instanceof ContenidoCurso) {
					response()->json(['success' => false, 'errors' => 'El código del Tema no se encuentra en el sistema, verifique!!! ']);
				}
				return response()->json($courseContents);
			}
		}
		abort(404);
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
			$data = $request->all();

			$course_content_order = 1;
			$validationCodeAttachment = '';

			$courseContentOrder = ContenidoCurso::getOrderMax( $request->all() );
			if(isset($courseContentOrder)){
				$course_content_order = $courseContentOrder + 1;
			}

			$coursecontent = new ContenidoCurso;
			DB::beginTransaction();
			try {
				$coursecontent->codigo_tema_curso = $request->codigo_tema_curso;
				$coursecontent->titulo_contenido_curso = $request->nombre_adjunto;
				$coursecontent->url_contenido_curso = slugtp($request->nombre_adjunto, $coursecontent->getTable(), 'url_contenido_curso');

				if( $request->tipo_contenido_curso == ContenidoCurso::getTypeCourseContent('article') ){
					$coursecontent->url_contenido_curso = $request->direccion_adjunto;
					//Log::debug(['store'=>$data]);
					//$coursecontent->descripcion_contenido_curso = $request->descripcion_contenido_curso;
				}

				$coursecontent->tipo_contenido_curso = $request->tipo_contenido_curso;
				$coursecontent->orden_contenido_curso = $course_content_order;
				$coursecontent->codigo_estado = Estado::getState('active');
				$coursecontent->save();

                if( $request->tipo_contenido_curso == ContenidoCurso::getTypeCourseContent('attachment') ){
					$attachment = new Adjunto;
					$attachment->fill($data);
					$attachment->codigo_privacidad_contenido = PrivacidadContenido::getPrivacy('public');
					$attachment->save();

					$validationCodeAttachment = $attachment->codigo_adjunto;

					$attachmentTheme = new AdjuntoTema;
					$attachmentTheme->codigo_adjunto = $attachment->codigo_adjunto;
					$attachmentTheme->codigo_tema_curso = $request->codigo_tema_curso;
					$attachmentTheme->fehca_adjunto_tema = date('Y-m-d');
					$attachmentTheme->hora_adjunto_tema =  date('H:m:s');
					$attachmentTheme->url_adjunto = $coursecontent->url_contenido_curso;
					$attachmentTheme->codigo_contenido_curso = $coursecontent->codigo_contenido_curso;

					if($attachment->codigo_tipo_adjunto ==  TipoAdjunto::getTypeAttached('survey')){
						$survey = Encuesta::find($request->codigo_encuesta);
						if(!$survey instanceof Encuesta){
							throw new \Exception("Encuesta no encontrada {$request->codigo_encuesta}");
						}
						$survey->respuesta_encuesta = $survey->respuesta_encuesta ?? 'Ya fue respondida :v';
						$coursecontent->url_contenido_curso = $survey->url_encuesta;
						$attachment->direccion_adjunto = $survey->url_encuesta;
						$attachmentTheme->url_adjunto = $survey->url_encuesta;

						EncuestaInstitucion::where('codigo_institucion', session('institution')->codigo_institucion)
						->where('codigo_encuesta', $survey->codigo_encuesta)
						->update([
							'codigo_lugar_aplicacion' => LugarAplicacion::getPlace('aprende'),
							'codigo_recurso' => $attachment->codigo_adjunto
						]);

						$survey->save();

					}


					$coursecontent->save();
					$attachmentTheme->save();
					$attachment->save();


				}

                // if( $request->tipo_contenido_curso == ContenidoCurso::getTypeCourseContent('evaluation') ){
                //
				// }
                //
                // if( $request->tipo_contenido_curso == ContenidoCurso::getTypeCourseContent('homework') ){
                //
				// }

				// Commit Transaction
				DB::commit();
				return response()->json(['success' => true, 'message' => 'El contenido ha sido creado correctamente!!', 'codigo_contenido_curso' => $coursecontent->codigo_contenido_curso, 'orden_contenido_curso' => $coursecontent->orden_contenido_curso, 'titulo_contenido_curso' => $coursecontent->titulo_contenido_curso, 'codigo_adjunto' => $validationCodeAttachment, 'tipo_contenido_curso' => $coursecontent->tipo_contenido_curso, 'codigo_estado' => $coursecontent->codigo_estado]);

			}catch(\Exception $e){
				DB::rollback();
				Log::error(sprintf('%s:%s', 'ContenidoCursoController:store', $e->getMessage()));
				return response()->json(['success' => false, 'errors' => trans('app.exception')]);
			}
		}
		abort(404);
    }

    /**
     * Display the specified resource.
     *
     * @param  int  $id
     * @return \Illuminate\Http\Response
     */
    public function show($id)
    {
        //
    }

    /**
     * Show the form for editing the specified resource.
     *
     * @param  int  $id
     * @return \Illuminate\Http\Response
     */
    public function edit($id)
    {
        //
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
			$attachmentCode = '';
			$data = $request->all();
			DB::beginTransaction();
			try {
				$coursecontent = ContenidoCurso::find($request->codigo_contenido_curso);
				if(!$coursecontent instanceof ContenidoCurso){
					throw new \Exception("Contenido curso no encontrado {$request->codigo_contenido_curso}");
				}

				if($request->has('change_state') && !empty($request->change_state)){
					$coursecontent->codigo_estado =  $request->codigo_estado == Estado::getState('active') ? Estado::getState('inactive') : Estado::getState('active');

					$event = $coursecontent->codigo_estado == Estado::getState('active') ? 'activado' : 'inactivado';
				}else{
					$coursecontent->titulo_contenido_curso = $request->nombre_adjunto;
					if( $request->tipo_contenido_curso == ContenidoCurso::getTypeCourseContent('article') ){

						$coursecontent->url_contenido_curso = $request->direccion_adjunto;
					}
					$event = 'editado';
				}
				$coursecontent->save();
				$attachmentCode = null;
				if( $request->tipo_contenido_curso == ContenidoCurso::getTypeCourseContent('attachment') ){
					$attachment = Adjunto::find($request->codigo_adjunto);
					if ($attachment instanceof Adjunto)
					{
						$attachment->fill($data);
						$attachment->save();
						$attachmentCode = $attachment->codigo_adjunto;

						$attachmentTheme = AdjuntoTema::where('codigo_contenido_curso', $coursecontent->codigo_contenido_curso)->first();


						if($attachment->codigo_tipo_adjunto ==  TipoAdjunto::getTypeAttached('survey')){
							$survey = Encuesta::find($request->codigo_encuesta);
							if($survey instanceof Encuesta){
								$survey->respuesta_encuesta = $survey->respuesta_encuesta ?? '';
								$coursecontent->url_contenido_curso = $survey->url_encuesta;
								$attachment->direccion_adjunto = $survey->url_encuesta;
								$attachmentTheme->url_adjunto = $survey->url_encuesta;

								AdjuntoTema::where('codigo_contenido_curso', $coursecontent->codigo_contenido_curso)
								->where('codigo_tema_curso', $request->codigo_tema_curso)
								->update([
										'url_adjunto' =>  $survey->url_encuesta,
										'codigo_adjunto'=>$attachment->codigo_adjunto,
										'fehca_adjunto_tema' => date('Y-m-d'),
										'hora_adjunto_tema' =>  date('H:m:s')
									]);

								EncuestaInstitucion::where('codigo_institucion', session('institution')->codigo_institucion)
										->where('codigo_lugar_aplicacion',LugarAplicacion::getPlace('aprende'))
										->where('codigo_recurso', $attachment->codigo_adjunto)
										->update([
											'codigo_lugar_aplicacion' => NULL,
											'codigo_recurso' => NULL
										]);

								EncuestaInstitucion::where('codigo_institucion', session('institution')->codigo_institucion)
										->where('codigo_encuesta', $survey->codigo_encuesta)
										->update([
											'codigo_lugar_aplicacion' => LugarAplicacion::getPlace('aprende'),
											'codigo_recurso' => $attachment->codigo_adjunto
										]);
							}

							$survey->save();

						}

						$coursecontent->save();
						$attachment->save();

					}

				}

				// Commit Transaction
				DB::commit();
				return response()->json(['success' => true, 'message' => trans('app.rest.success', ['resource' => 'El contenido', 'event' => $event]), 'codigo_contenido_curso' => $coursecontent->codigo_contenido_curso, 'titulo_contenido_curso' => $coursecontent->titulo_contenido_curso, 'codigo_estado' => $coursecontent->codigo_estado, 'codigo_adjunto' => $attachmentCode]);

			}catch(\Exception $e){
				DB::rollback();
				Log::error(sprintf('%s:%s', 'ContenidoCursoController:update', $e->getMessage()));
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

	/**
     * Order all resources in storage.
     *
     * @param  \Illuminate\Http\Request  $request
     * @return \Illuminate\Http\Response
     */
	public function order(Request $request)
	{
		if($request->ajax()){
			DB::beginTransaction();
			try {

				foreach ($request->data as $value) {
					$courseContent = ContenidoCurso::find($value['codigo_contenido_curso']);

					if(!$courseContent instanceof ContenidoCurso){
						return response()->json(['success' => false, 'errors' => 'El contendio no se encuentra en el sistema, vrifique!!']);
					}

					$courseContent->orden_contenido_curso = $value['orden_contenido_curso'];
					$courseContent->save();
				}

				// Commit Transaction
				DB::commit();
				return response()->json(['success' => true, 'message' => 'Se ha cambiado de posición el contenido correctamente!!']);

			}catch(\Exception $e){
				DB::rollback();
				Log::error(sprintf('%s:%s', 'ContenidoCursoController:order', $e->getMessage()));
				return response()->json(['success' => false, 'errors' => trans('app.exception')]);
			}
		}
		abort(404);
	}
}
