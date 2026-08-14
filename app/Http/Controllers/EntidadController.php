<?php

namespace App\Http\Controllers;

use App\Models\Core\ClaseEmprendimiento;
use App\Models\Core\ClaseEmprendimientoInstitucion;
use App\Models\Core\PrivacidadContenido;
use Illuminate\Http\Request;

use App\Http\Requests;
use App\Http\Controllers\Controller;

use DB, Log, TuProyecto, Response, Lang,  Config, Auth;

use App\Models\Core\Entidad,
    App\Models\Core\EtapaProyecto,
    App\Models\Seguridad\Usuario,
    App\Models\Seguridad\Rol,
    App\Models\Core\UsuarioInstitucion,
    App\Models\Core\DatoEntidad,
    App\Models\Core\Ciudad,
    App\Models\Core\Estado,
    App\Models\Core\TemaMentoria,
    App\Models\Core\TipoIdentificacion;

class EntidadController extends Controller
{
	/**
     * Instantiate a new EntidadController instance.
     */
    public function __construct()
    {
        $this->middleware('ability:admin|super-admin|administrativo,view,entidad', ['except' => ['search', 'update']]);
        $this->middleware('ability:admin|super-admin|administrativo,create,entidad', ['only' => ['create', 'store']]);
        $this->middleware('ability:admin|super-admin|administrativo,edit,entidad', ['only' => ['edit', 'update']]);
    }

    /**
     * Display a listing of the resource.
     *
     * @return \Illuminate\Http\Response
     */
    public function index(Request $request)
    {
        if($request->ajax()) {
          if($request->has('data') && !empty($request->data)){
            return Entidad::getEntities( $request->all() );
          }

          if($request->has('entity') && !empty($request->entity)){
            return Entidad::getEntityUrl($request->entity);
          }

			    return Entidad::entities( $request->all() );
        }

        $states = Estado::getStates([], [Estado::getState('active'), Estado::getState('inactive')], $filter = null);
        return view('project.index', compact('states'));
    }

    /**
     * Show the form for creating a new resource.
     *
     * @return \Illuminate\Http\Response
     */
    public function create()
    {
		  $identificationType = TipoIdentificacion::identificationType();
		  $cities = Ciudad::getCities();
      $entityTypes = ClaseEmprendimiento::getClasses();
      $stages = EtapaProyecto::getAllStages(session('institution')->codigo_institucion);
		  return view('project.create-edit', compact('identificationType','entityTypes','cities', 'stages'));
    }

    /**
     * Store a newly created resource in storage.
     *
     * @param  \Illuminate\Http\Request  $request
     * @return \Illuminate\Http\Response
     */
	public function store(Request $request)
	{
		if ($request->ajax()) {
      $data = $request->all();
      $entity = new Entidad;

      if (isset($data['change_state'])) {
        DB::beginTransaction();
        try {
          Usuario::where('codigo_usuario', $data['codigo_usuario'])
            ->update([ 'codigo_estado' => $data['state'] ]);

          UsuarioInstitucion::where('codigo_usuario', $data['codigo_usuario'])
            ->where('codigo_institucion', session('institution')->codigo_institucion)
            ->update([ 'codigo_estado' => $data['state'] ]);

          $user = Usuario::find($data['codigo_usuario']);

					DB::commit();
          return response()->json(['success' => true, 'message' => 'El estado cambio correctamente', 'codigo_estado' => $user->codigo_estado, 'entidad' => $user->nombre_usuario]);
        } catch (\Exception $e) {
          DB::rollback();
					Log::error(sprintf('%s:%s', 'Entidad:store rollback change state fail', $e->getMessage()));
					return Response::json(array('success' => false, 'errors' => Lang::get('app.exception')));
        }
      }

			if ($entity->isValid($data)) {
        DB::beginTransaction();
				try {
					//User
					$user = new Usuario;
          $user->fill($data);
					$user->codigo_estado = Estado::getState('active');
					$user->tipo_usuario = Usuario::typeUser('entity');
					$username = TuProyecto::unwantedCharacters($data['nombre_usuario']);
					// Validate unique nickname
					$usernickname= Usuario::validateNinckname(str_slug(str_limit($username, 16), ""));
					$user->nickname_usuario = $usernickname;
          $user->save();

					//user Institution
					$userInstitution = new UsuarioInstitucion;
					$userInstitution->codigo_usuario = $user->codigo_usuario;
					$userInstitution->codigo_institucion =  session('institution')->codigo_institucion;
					$userInstitution->codigo_estado = Estado::getState('active');
					$userInstitution->fecha_registro_usuario = date('Y-m-d');
					$userInstitution->hora_registro_usuario = date('H:i:s');
					$userInstitution->codigo_rol = Rol:: getRol('user');
					$userInstitution->save();

					// Entity
          $entity->fill($data);
					$entity->codigo_usuario = $user->codigo_usuario;
					$entity->seg_codigo_usuario = Auth::user()->codigo_usuario;
					$entity->save();

					//Data Entity
					$dataEntity = new DatoEntidad;
					$dataEntity->codigo_usuario_institucion = $userInstitution->codigo_usuario_institucion;
					$dataEntity->codigo_privacidad_contenido = PrivacidadContenido::getPrivacy('public');
          $dataEntity->codigo_estado = Estado::getState('active');
          $dataEntity->codigo_etapa_proyecto = $data['etapa_usuario'];
					if($request->has('codigo_categoria_proyecto') && $data['codigo_categoria_proyecto']!=0){
						$dataEntity->codigo_categoria_proyecto = $data['codigo_categoria_proyecto']; /// Tipo proyecto
					}

					$classEntrepreneurship = ClaseEmprendimientoInstitucion::find($entity->codigo_clase_empre_insti);
					if(!$classEntrepreneurship instanceof ClaseEmprendimientoInstitucion) {
                        Log::error(sprintf('%s:%s', 'Entidad:store' ,'ClaseEmprendimientoInstitucion not found'));
						abort(404, 'ClaseEmprendimientoInstitucion not found');
					}

					switch ($classEntrepreneurship->codigo_clase_emprendimiento) {
						case ClaseEmprendimiento::getClass('entrepreneurship'):
							if($request->has('codigo_etapa_proyecto')){
								$dataEntity->codigo_etapa_proyecto = $data['codigo_etapa_proyecto'];
							}
							break;

						case ClaseEmprendimiento::getClass('company'):
							if($request->has('codigo_etapa_proyecto')){
								$dataEntity->codigo_etapa_proyecto = $data['codigo_etapa_proyecto'];
							}
							break;
					}
					$dataEntity->save();

					// Commit Transaction
					DB::commit();
          return response()->json(['success' => true, 'message' => 'La entidad ha sido creada correctamente!!', 'url_entidad'=> $user->nickname_usuario  , 'redirect' => true]);

				}catch(\Exception $e){
					DB::rollback();
					Log::error(sprintf('%s:%s', 'Entidad:store rollback', $e->getMessage()));
					return Response::json(array('success' => false, 'errors' => Lang::get('app.exception')));
				}
			}

            Log::error(sprintf('%s:%s', 'Entidad:store rollback', $entity->errors));
			return Response::json(array('success' => false, 'errors' => $entity->errors));
		}
        Log::error(sprintf('%s:%s', 'Entidad:store Error 400 BadRequest: must be ajax'));
		abort(400, 'Request is not ajax');
	}


	/**
     * Display the specified resource.
     *
     * @param  int  $id
     * @return \Illuminate\Http\Response
     */
    public function show(Request $request, $url)
    {
		  $entity =  Entidad::getEntityUrl($url);

		  if(!$entity instanceof Entidad) {
            abort(404, 'entity not founded');
		  }
      $entityBuild = Entidad::buildEntity($entity);
		  $mentorThemes = TemaMentoria::getThemesList();

      if ($request->ajax()) {
        return response()->json($entityBuild);
      }

      return view('project.detail', compact('entityBuild', 'mentorThemes'));
    }

    /**
     * Show the form for editing the specified resource.
     *
     * @param  int  $url
     * @return \Illuminate\Http\Response
     */
    public function edit($url)
    {
      $entity =  Entidad::getEntityUrl($url);
      if(!$entity instanceof Entidad){
        Log::error(sprintf('%s:%s %s', 'Entidad:store' ,'No se encuentra la entidad con id:', $url));
        abort(404, 'Entidad not founded');
      }
      $person = Usuario::find($entity->seg_codigo_usuario);
      if(!$person instanceof Usuario){
        Log::error(sprintf('%s:%s %s', 'Entidad:store' ,'No se encuentra el encargado con id:', $entity->seg_codigo_usuario));
        abort(404, 'User not founded');
      }

      $person->fullname = $person->getName();
      $project = Entidad::buildEntity($entity);
      $identificationType = TipoIdentificacion::identificationType();
      $cities = Ciudad::getCities();
      $entityTypes = ClaseEmprendimiento::getClasses();
      $stages = EtapaProyecto::getAllStages(session('institution')->codigo_institucion);
      $stage = EtapaProyecto::getStage($entity->codigo_etapa_proyecto);

      return view('project.create-edit', compact('project','identificationType','entityTypes','cities','person', 'stages', 'stage'));

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
      $data = $request->all();

      if($request->ajax()){
			// Decrypt src-param
			DB::beginTransaction();
      try {

        $entity =  Entidad::getEntityUrl($id);
        if(!$entity instanceof Entidad) {
          list($codigo_entidad) = TuProyecto::decryptSrcParams($id);
          if(!$codigo_entidad || !is_numeric($codigo_entidad)) {
            Log::error(sprintf('%s:%s %s', 'Entidad:store' ,'No se encuentra la entidad con id:', $codigo_entidad));
            abort('404','No se encuentra la entidad con id:', $codigo_entidad);
            }

            $entity = Entidad::find($codigo_entidad);
        }

        if(!$entity instanceof Entidad) {
          Log::error(sprintf('%s:%s %s', 'Entidad:update' ,'No se encuentra la entidad con id:', $id));
            throw new \Exception("Entidad no encontrada {$id}");
        }

        $user = Usuario::find($entity->codigo_usuario);
        if(!$user instanceof Usuario) {
          Log::error(sprintf('%s:%s %s', 'Entidad:update' ,'No se encuentra el usuario con id:', $entity->codigo_usuario));
          throw new \Exception("Entidad no encontrada {$id}");
        }

        $dateEntity = DatoEntidad::getDataEntity($entity->codigo_usuario);
        if(!$dateEntity instanceof DatoEntidad) {
          Log::error(sprintf('%s:%s %s', 'Entidad:update' ,'No se encuentra el dato entidad con el usuario:', $entity->codigo_usuario));
          throw new \Exception("Entidad no encontrada {$id}");
        }


        $entity->fill($data);
        if(  isset($data['nombre_encargado'] ) && isset($data['correo_electronico_encargado']) && $data['nombre_encargado'] && $data['correo_electronico_encargado'])
        {
          $userValid = Usuario::validateMail( ['correo_electronico'=>$data['correo_electronico_encargado']] );
          if($userValid instanceof Usuario){
            $entity->seg_codigo_usuario =$userValid->codigo_usuario;
          }

        }
        $entity->save();

        if (isset($data['etapa_usuario'])) {
          $dateEntity->codigo_etapa_proyecto = $data['etapa_usuario'];
          $dateEntity->save();
        }

        $user->fill($data);
        $user->save();

                // Commit Transaction
                DB::commit();
                return response()->json(['success' => true, 'message' => 'Se ha editado la entidad correctamente!!', 'url_entidad'=> $user->nickname_usuario  , 'redirect' => true]);

            }catch(\Exception $e){
                DB::rollback();
				Log::error(sprintf('%s:%s', 'EntidadController:update', $e->getMessage()));
				return response()->json(['success' => false, 'errors' => trans('app.exception')]);
            }
        }
        Log::error(sprintf('%s:%s', 'EntidadController:update','recurso no encontrado'));
        abort(404);
        return response()->json(['success' => false, 'message' => 'recurso no encontrado'] ) ;

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
        if($request->ajax()) {
            return Entidad::getEntities($request->all());
        }
        abort(403);
    }
}
