<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;

use App\Http\Requests;
use App\Http\Controllers\Controller;

use TuProyecto;
use DB, Lang, Log, Auth, App;
use App\Models\Core\Contenido, App\Models\Core\TipoContenido, App\Models\Seguridad\Usuario, App\Models\Core\Estado, App\Models\Core\ContenidoInstitucion, App\Models\Core\TipoAdjunto, App\Models\Core\PrivacidadContenido, App\Models\Core\Tag, App\Models\Seguridad\Modulo, App\Models\Core\Seo;

class ContenidoController extends Controller
{
	/**
     * Instantiate a new Controller instance.
     */
    public function __construct()
    {
        $this->middleware('ability:admin|super-admin|administrativo,view,contenido', ['except' => ['search']]);
        $this->middleware('ability:admin|super-admin|administrativo,create,contenido', ['only' => ['create', 'store']]);
        $this->middleware('ability:admin|super-admin|administrativo,edit,contenido', ['only' => ['edit', 'update']]);
        $this->middleware('ability:admin|super-admin|administrativo,remove,contenido', ['only' => ['destroy']]);
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
					return response()->json(Contenido::getArticlesList( $request->all() ));
                    break;

                default:
                    return response()->json(Contenido::getArticles( $request->all() ));
            }
        }

		$contentTypes = TipoContenido::getTypes();
		$privacyContent = PrivacidadContenido::getContentPrivacy();
		$states = Estado::getStates([], [Estado::getState('active'), Estado::getState('inactive'), Estado::getState('review')], $filter = null);

        return view('article.index', compact('contentTypes', 'states', 'privacyContent'));
    }

    /**
     * Show the form for creating a new resource.
     *
     * @return \Illuminate\Http\Response
     */
    public function create()
    {
		$contentTypes = TipoContenido::getTypes();
        $public = PrivacidadContenido::getPrivacy('public');
		$privacyContent = PrivacidadContenido::getContentPrivacy();
        return view('article.create-edit', compact('contentTypes', 'privacyContent', 'public'));
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

			$content = new Contenido;
			if ($content->isValid($data)) {
				DB::beginTransaction();
	            try {
					$content->fill($data);
					$content->url_contenido = slugtp($request->nombre_contenido, $content->getTable(), 'url_contenido');
					$content->codigo_institucion = session('institution')->codigo_institucion;
					$content->codigo_estado = Estado::getState('active');
					$content->codigo_usuario = Auth::user()->codigo_usuario;
					$content->fecha_contenido = date('Y-m-d');
					$content->hora_contenido = date('H:m:s');
					$content->save();

					$institutionContent = new ContenidoInstitucion;
					$institutionContent->codigo_institucion = session('institution')->codigo_institucion;
					$institutionContent->codigo_estado = Estado::getState('active');
					$institutionContent->codigo_contenido = $content->codigo_contenido;
					$institutionContent->landing_contenido = $content->landing_contenido;
					$institutionContent->codigo_tipo_cont_institucion = $request->codigo_tipo_cont_institucion;
					$institutionContent->save();

                    // Commit Transaction
                    DB::commit();
                    return response()->json(['success' => true, 'message' => 'El contenido ha sido creado correctamente!!', 'codigo_contenido' => $content->codigo_contenido, 'redirect' => true]);

				}catch(\Exception $e){
	                DB::rollback();
	                Log::error(sprintf('%s:%s', 'ContenidoController:store', $e->getMessage()));
	                return response()->json(['success' => false, 'errors' => trans('app.exception')]);
	            }
			}
			return response()->json(['success' => false, 'errors' => $content->errors]);
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
		if(!is_numeric($id)) {
            abort(404);
        }

		$content = Contenido::getArticle($id);
		if(!$content instanceof Contenido){
			abort(404);
		}

		$content = Contenido::buidContent( $content );

		if($request->ajax()){
			return response()->json($content);
		}

		$contentTypes = TipoContenido::getTypes();
		return view('article.detail', compact('content', 'contentTypes'));
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
            abort(404);
        }

		$content = Contenido::getArticle($id);
		if(!$content instanceof Contenido){
			abort(404);
		}

		$content = Contenido::buidContent( $content );

		$contentTypes = TipoContenido::getTypes();
        $public = PrivacidadContenido::getPrivacy('public');
		$privacyContent = PrivacidadContenido::getContentPrivacy();
        return view('article.create-edit', compact('content', 'contentTypes', 'privacyContent', 'public'));
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
		if($request->ajax()) {
			$data = $request->all();

			$content = Contenido::find($id);
			if(!$content instanceof Contenido) {
				return response()->json(['success' => false, 'errors' => 'El código del contenido no se encuentra en el sistema ']);
			}

			$institutionContent = $content->institutions->where('codigo_institucion', session('institution')->codigo_institucion)->first();

			if(!$institutionContent instanceof ContenidoInstitucion) {
				return response()->json(['success' => false, 'errors' => 'El contenido no se encuentra en el sistema ']);
			}

			DB::beginTransaction();
            try {

				$codigo_estado = $institutionContent->codigo_estado;

				if($request->has('change_state') && !empty($request->change_state)){
					switch($request->get('action')){
						case 'aprobar':
							$codigo_estado = Estado::getState('active');
							break;
						case 'rechazar':
							$codigo_estado = Estado::getState('pending');
							break;
						case 'inactivar':
							$codigo_estado = Estado::getState('inactive');
							break;
						case 'activar':
							$codigo_estado = Estado::getState('active');
							break;
					}
				}else {
					$content->fill($data);
					$content->fecha_contenido = date('Y-m-d');
		            $content->hora_contenido = date('H:m:s');
					$content->save();
				}

				if(!$request->has('seo')){
					ContenidoInstitucion::where('codigo_institucion', $institutionContent->codigo_institucion)
					->where('codigo_contenido', $institutionContent->codigo_contenido)
					->update([
						'landing_contenido' => $request->landing_contenido,
						'codigo_tipo_cont_institucion' => $request->codigo_tipo_cont_institucion,
						'codigo_estado' => $codigo_estado
					]);
				}

				if($request->has('seo')){
					// Info seo
					$seo = Seo::where('id_recurso', $content->codigo_contenido)->where('codigo_modulo', Modulo::getModule('blog'))->first();
					if(!$seo instanceof Seo){
						$seo = new Seo;
					}

					$seo->fill($data);
					$seo->id_recurso = $content->codigo_contenido;
					$seo->codigo_modulo = Modulo::getModule('blog');
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
						$inputTags = explode(',', $request->tags_contenido);
						$tags = $content->tags()->get();
						foreach ($tags as $tag) {
							if(!in_array($tag->texto_tag, $inputTags)) {
								$content->tags()->detach($tag);
							}
						}
					}

					if($request->has('tags_contenido') && is_array($inputTags) && count($inputTags) > 0) {
						foreach($inputTags as $item) {
							if(!$tags->contains('texto_tag', $item)){
								$tag = Tag::firstOrCreate(['texto_tag' => $item]);
								if(!$tag instanceof Tag) {
									throw new \Exception('tag not found');
								}
								$content->tags()->attach($tag);
							}
						}
					}
				}

                // Commit Transaction
                DB::commit();
                return response()->json(['success' => true, 'message' => 'Contenido actualizado correctamente!!', 'codigo_contenido' => $content->codigo_contenido, 'codigo_estado' => $codigo_estado]);

			}catch(\Exception $e){
                DB::rollback();
                Log::error(sprintf('%s:%s', 'ContenidoController:update', $e->getMessage()));
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
			return response()->json(Contenido::getArticlesList( $request->all() ));
		}
		abort(403);
	}
}
