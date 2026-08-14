<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;

use App\Http\Requests;
use App\Http\Controllers\Controller;

use Datatables, Log, DB, SendEmail, Auth;

use App\Models\Seguridad\Usuario, App\Models\Seguridad\RolUsuario, App\Models\Core\Estado;

class MentorController extends Controller
{
    /**
     * Instantiate a new Controller instance.
     */
    public function __construct()
    {
        $this->middleware('ability:admin|super-admin|administrativo,view,mentoria/mentores');
        $this->middleware('ability:admin|super-admin|administrativo,edit,mentoria/mentores', ['only' => ['update']]);
    }

    /**
     * Display a listing of the resource.
     *
     * @return \Illuminate\Http\Response
     */
    public function index(Request $request)
    {
      if($request->ajax()){
        return Datatables::of( Usuario::getMentors( $request->all() ))->make(true);
      }

		  $states = Estado::getStates([], [Estado::getState('active'), Estado::getState('inactive'), Estado::getState('review')], $filter = null);
      return view('mentor.advisory.index', compact('states'));
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

    if ($request->ajax()) {
      $data = $request->all();
      $user = Usuario::find(Auth::user()->codigo_usuario);
      if(!$user instanceof Usuario) {
        abort(404);
      }

      if ($user->isValidMentor($data)) {

        DB::beginTransaction();
        try {

          $user->experiencia_usuario = $request->input("experiencia_usuario");
          $user->imagen_usuario = $request->input("imagen_usuario");
          $user->linkedin_usuario = $request->input("linkedin_usuario");
          $user->save();

          $person = Persona::where('codigo_usuario', '=', $user->codigo_usuario)->first();
          if(!$person instanceof Persona) {
            abort(404);
          }

          $person->codigo_especialidad = $request->input("codigo_especialidad_destaca");
          $person->forma_ayuda_mentoria = $request->input("forma_ayuda_mentoria");
          $person->save();

          // Stage Mentoring
          $items = EtapaProyecto::getStages();

          foreach ($items as $item) {
            if($request->has("codigo_etapa_proyecto_$item->codigo_etapa_proyecto")) {
              //Stage mentoring for user
              $stageMentorinUser = EtapaMentoria::where('codigo_usuario', '=', $user->codigo_usuario)->where('codigo_etapa_proyecto', $item->codigo_etapa_proyecto)->first();
              if(!$stageMentorinUser instanceof EtapaMentoria){
                $stageMentoring = new EtapaMentoria();
                $stageMentoring->codigo_usuario = $user->codigo_usuario;
                $stageMentoring->codigo_etapa_proyecto = $request->input("codigo_etapa_proyecto_$item->codigo_etapa_proyecto");
                $stageMentoring->codigo_estado = Estado::getState('active');
                $stageMentoring->save();
              }
            }
          }

          // Get user institution
          $userInstitution = UsuarioInstitucion::getUserInstitution($user->codigo_usuario);

          // Terms and sales policies
          $terms = TerminosCondiciones::termsAndConditions(TerminosCondiciones::getTypeTerms('mentoring'));
          if($terms instanceof TerminosCondiciones) {
            $acceptanceTerms = AceptacionTerminos::where('codigo_usuario_institucion', $userInstitution->codigo_usuario_institucion)->where('codigo_version_terminos', $terms->codigo_version_terminos)->first();
            if(!$acceptanceTerms instanceof AceptacionTerminos) {
              $acceptanceTerms = new AceptacionTerminos();
              $acceptanceTerms->codigo_usuario_institucion = $userInstitution->codigo_usuario_institucion;
              $acceptanceTerms->codigo_version_terminos = $terms->codigo_version_terminos;
              $acceptanceTerms->codigo_estado = Estado::getState('active');
              $acceptanceTerms->fecha_aceptacion_terminos = date('Y-m-d H:i:s');
              $acceptanceTerms->save();
            }

            $user->sync_algolia = 1;
            $user->save();
            $user->syncIndex();
          }

          // Role User
          $user->attachRole(Rol::find( Rol::getRole('mentor') ), Estado::getState('review'));

          // Commit Transaction
          DB::commit();
          return response()->json(['success' => true, 'message' => 'Hemos recibido tu solicitud para registrarte como Mentor, te estaremos notificando la respuesta posterior a nuestro proceso de valoración. Gracias']);
        }catch(\Exception $e){
          DB::rollback();
          Log::error($e->getMessage());
          return response()->json(['success' => false, 'errors' => Lang::get('app.exception')]);
        }
      }
      return response()->json(['success' => false, 'errors' => $user->errors]);
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

		$user = Usuario::getMentor($id);
		if(!$user instanceof Usuario){
			abort(404);
		}

		$advisory = Usuario::buildUser($user);

      if ($request->ajax()) {
        return response()->json($advisory);
      }

		return view('mentor.advisory.detail', compact('advisory'));
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


			$message = 'activado';
			$codigo_estado = '';

			$user = Usuario::find($id);
			if(!$user instanceof Usuario){
				throw new \Exception("Mentor no encontrado {$id}");
			}

			$userRole = RolUsuario::getRoleUser( $request->all() );
			if(!$userRole instanceof RolUsuario){
				throw new \Exception("Rol usuario no encontrado");
			}

			DB::beginTransaction();
			try {

				if($request->codigo_estado != Estado::getState('rejected')){
					$codigo_estado = $userRole->codigo_estado == Estado::getState('inactive') || $userRole->codigo_estado == Estado::getState('review') ? Estado::getState('active') : Estado::getState('inactive');

					RolUsuario::where('user_id', $request->codigo_usuario)
                    ->where('role_id', $request->role)
                    ->where('codigo_institucion', session('institution')->codigo_institucion)
                                ->update([
                                    'codigo_estado' => $codigo_estado
						]);

					if($codigo_estado == Estado::getState('inactive')){
                        $message = 'inactivado';
                        $user->sync_algolia = 1;
                        $user->save();
                        $user->syncIndex();
                    }elseif($codigo_estado == Estado::getState('active')){
                        $message = 'activado';
                        $user->sync_algolia = 1;
                        $user->save();
                        $user->syncIndex();
                    }

					if($request->codigo_estado == Estado::getState('review')){
						// Send notification
						$data = [];
						$data['_template'] = 'emails.mentoring.mentorconfirm';
						$data['_inst'] = session('institution')->codigo_institucion;
						$data['_subject'] = 'Aceptación mentor';
						$data['_url_action']  = sprintf("/%s/%s", 'mentoria/mentores', $user->nickname_usuario);
						$data['_to'] = $user->correo_electronico;
						$data['_to_name'] = ucwords(strtolower($user->nombre_usuario));
						SendEmail::notification($data);
					}
				}else{
					// Delete role user
					$user->detachRole($userRole->role_id);
                    $user->sync_algolia = 1;
                    $user->save();
                    $user->syncIndex();

					// Send notification
					$data = [];
					$data['_template'] = 'emails.mentoring.rejectapplicationadvice';
					$data['_inst'] = session('institution')->codigo_institucion;
					$data['_subject'] = 'Solicitud a mentor';
					$data['_to'] = $user->correo_electronico;
					$data['_to_name'] = ucwords(strtolower($user->nombre_usuario));
					SendEmail::notification($data);
					$message = 'rechazado';
				}

                activity()
                    ->performedOn($user)
                    ->withProperties([
                        'attributes' => [
                            'mentor' => $user->nombre_usuario,
                            //'mentor_id' => $user->codigo_usuario,
                            // 'report' => 'general de mentorias',
                            'message' => $message,
                            // 'user' => Auth::user()->nombre_usuario,
                        ]
                    ])
                    ->log('activitylog.user.log');

				// Commit Transaction
				DB::commit();
				return response()->json(['success' => true, 'message' => "El mentor {$user->nombre_usuario} ha sido {$message} correctamente!!", 'codigo_usuario' => $user->codigo_usuario, 'codigo_estado' => $codigo_estado]);

			}catch(\Exception $e){
				DB::rollback();
				Log::error(sprintf('%s:%s', 'MentorController:update', $e->getMessage()));
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
}
