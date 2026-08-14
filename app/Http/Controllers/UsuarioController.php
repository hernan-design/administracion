<?php

namespace App\Http\Controllers;

use App\Models\Bita\LogActivity;
use Illuminate\Http\Request;

use App\Http\Requests;
use App\Http\Controllers\Controller;

use Illuminate\Auth\Passwords\PasswordBroker;

use DB, Lang, App, Log, Hash, Input, Excel, SendEmail, Datatables, TuProyecto, Carbon\Carbon;

use App\Models\Core\Persona, App\Models\Seguridad\Usuario, App\Models\Core\UsuarioInstitucion, App\Models\Seguridad\Rol, App\Models\Core\Estado;

class UsuarioController extends Controller
{
  /**
   * Instantiate a new Controller instance.
   */
  public function __construct()
  {
    $this->middleware('ability:admin|super-admin|administrativo,view', ['except' => ['verifyEmail', 'search']]);
    $this->middleware('ability:admin|super-admin|administrativo,export,usuario', ['only' => ['exportar']]);
    $this->middleware('ability:admin|super-admin|administrativo,create,usuario', ['only' => ['create', 'store']]);
  }

  /**
   * Display a listing of the resource.
   *
   * @return \Illuminate\Http\Response
   */
  public function index(Request $request)
  {
    if ($request->ajax()) {
      return Datatables::of(Usuario::getUsers($request->all()))->make(true);
    }

    return view('users.main');
  }


  /**
   * Show the form for creating a new resource.
   *
   * @return \Illuminate\Http\Response
   */
  public function create()
  {
    return view('users.create-edit');
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
      $trim_if_string = function ($var) {
        return is_string($var) ? strtolower(trim($var)) : $var;
      };
      $req = $request->merge(array_map($trim_if_string, $request->all()));
      $data = $req->all();
      $userValid = Usuario::validateMail($request->all());
      if ($userValid instanceof Usuario) {
        return response()->json(['success' => false, 'errors' => "Ya existe el usuario con correo $request->correo_electronico, verifique."]);
      }

      $userExist = Usuario::validateUser($request->all());
      $user_name = Usuario::validateNinckname(str_slug(str_limit($request->nombre_usuario, 16), ""));
      $user = new Usuario;

      if ($user->isValid($data)) {
        DB::beginTransaction();
        try {
          $password = str_random(8);

          if ($userExist instanceof Usuario) {
            $userInstitution = new UsuarioInstitucion;
            $userInstitution->codigo_usuario = $userExist->codigo_usuario;
            $userInstitution->codigo_estado = Estado::getState('active');
            $userInstitution->codigo_institucion = session('institution')->codigo_institucion;
            $userInstitution->codigo_rol = Rol::getRol('user');
            $userInstitution->fecha_registro_usuario = date('Y-m-d');
            $userInstitution->hora_registro_usuario = date('H:i:s');
            $userInstitution->save();


            // register activity
            activity()
              ->performedOn($userExist)
              ->withProperties([
                'attributes' => [
                  'user' => $userExist->nombre_usuario,
                  'user_id' => $userExist->codigo_usuario
                ]
              ])
              ->log('activitylog.user.created');

            $email = TuProyecto::encryptSrcParams([$userExist->correo_electronico]);

            // Send notification
            $data = [];
            $data['_inst'] = session('institution')->codigo_institucion;
            $data['_to'] = $userExist->correo_electronico;
            $data['_password'] = $password;
            $data['_to_name'] = ucwords(strtolower($userExist->nombre_usuario));
            $data['_subject'] = "Confirmación de registro plataforma";
            $data['_template'] = 'emails.user.createaccount';
            $data['_change_password'] = "/password/email";
            $data['_activate_account'] = "/usuario/confirmacion/{$email}";

            SendEmail::notification($data);

            $response = Usuario::getUserMail(['email' => $userExist->correo_electronico]);
            $response->success = true;
            $response->add_register = true;
            $response->message = 'El usuario ha sido creado correctamente.';

          } else {
            if (session('institution')->validacion_identificacion) {
              $userValidIdentification = Usuario::validateuserIdentification($data['identificacion_usuario']);
              if ($userValidIdentification instanceof Usuario) {
                return response()->json(['success' => false, 'errors' => "Ya existe el usuario con el numero de identificación $request->identificacion_usuario, verifique."]);
              }
            }

            $user->fill($data);
            $user->password = Hash::make($password);
            $user->codigo_estado = Estado::getState('active');
            $user->tipo_usuario = Usuario::typeUser('person');
            $user->codigo_institucion = session('institution')->codigo_institucion;
            $user->nickname_usuario = $user_name;
            $user->save();

            $person = new Persona;
            $person->fill($data);
            $person->codigo_usuario = $user->codigo_usuario;
            $person->save();

            $userInstitution = new UsuarioInstitucion;
            $userInstitution->codigo_usuario = $user->codigo_usuario;
            $userInstitution->codigo_estado = Estado::getState('active');
            $userInstitution->codigo_institucion = session('institution')->codigo_institucion;
            $userInstitution->codigo_rol = Rol::getRol('user');
            $userInstitution->fecha_registro_usuario = date('Y-m-d');
            $userInstitution->hora_registro_usuario = date('H:i:s');
            $userInstitution->save();

            // register activity
            activity()
              ->performedOn($user)
              ->withProperties([
                'attributes' => [
                  'user' => $user->nombre_usuario,
                  'user_id' => $user->codigo_usuario
                ]
              ])
              ->log('activitylog.user.created');

            $email = TuProyecto::encryptSrcParams([$user->correo_electronico]);

            // Send notification
            $data = [];
            $data['_inst'] = session('institution')->codigo_institucion;
            $data['_to'] = $user->correo_electronico;
            $data['_password'] = $password;
            $data['_to_name'] = ucwords(strtolower($user->nombre_usuario));
            $data['_subject'] = "Confirmación de registro plataforma";
            $data['_template'] = 'emails.user.createaccount';
            $data['_change_password'] = "/password/email";
            $data['_activate_account'] = "/usuario/confirmacion/{$email}";

            SendEmail::notification($data);

            $response = Usuario::getUserMail(['email' => $user->correo_electronico]);
            $response->success = true;
            $response->add_register = true;
            $response->message = 'El usuario ha sido creado correctamente.';

          }

          // Commit Transaction
          DB::commit();
          return response()->json($response);

        } catch (\Exception $e) {
          DB::rollback();
          Log::error(sprintf('%s:%s', 'UsuarioController:store', $e->getMessage()));
          return response()->json(['success' => false, 'errors' => trans('app.exception')]);
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
  public function show($id)
  {
    $user = Usuario::getPersonByUser($id);
    if (!$user instanceof Usuario) {
      abort(404);
    }

    if (isset($user->ultimo_ingreso_usuario)) {
      $user->ultimo_ingreso_usuario = Carbon::createFromFormat('Y-m-d H:i:s', $user->ultimo_ingreso_usuario)->format('l jS \\of F Y h:i:s A');
    } else {
      $user->ultimo_ingreso_usuario = '---';
    }

    // dd($user);

    return view('users.detail', compact('user'));
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
    DB::beginTransaction();
    try {

      $user = Usuario::where('correo_electronico', $request->inp__Mail)->first();

      $userIdentification = Usuario::select('usuario.*')
        ->leftJoin('core.usuario_institucion', 'usuario.codigo_usuario', '=', 'core.usuario_institucion.codigo_usuario' )
        ->where('usuario.identificacion_usuario', $request->inp__Number)
        ->where('usuario.tipo_usuario', '!=', 2)
        ->where('core.usuario_institucion.codigo_institucion', session('institution')->codigo_institucion)
        ->first();


      if ($user instanceof Usuario && $request->inp__Mail != "") {
        return response()->json(['success' => false, 'errors' => 'Ya existe un usuario con este correo electronico']);
      } else if ($userIdentification != null && $request->inp__Number != ""){
        return response()->json(['success' => false, 'errors' => 'Ya existe un usuario con este número de identificación']);
      }else {
        $updateDataUsuario = [];
        $updateDataPersona = [];

        if ($request->inp__Mail == "" && $request->inp__Name == "" && $request->inp__Number == "" && $request->inp__BirthDay == "" && $request->inp__Cellphone == "" && $request->inp__Phone == "" && $request->inp__Address == "" && $request->inp__FirstLastName == "" && $request->inp__SecondLastName == "") {
          return response()->json(['success' => false, 'errors' => 'Debe diligenciar mínimo un campo']);
        }

        // Verificar y agregar valores al array updateDataUsuario
        if (!empty($request->inp__Mail)) {
          if ($request->inp__Mail == $request->inp__CurrentEmail) {
            return response()->json(['success' => false, 'errors' => 'El correo debe ser distinto al actual']);
          } else {
            $updateDataUsuario['correo_electronico'] = $request->inp__Mail;
          }
        }
        if (!empty($request->inp__Name)) {
          $updateDataUsuario['nombre_usuario'] = $request->inp__Name;
        }
        if (!empty($request->inp__Number)) {
          $updateDataUsuario['identificacion_usuario'] = $request->inp__Number;
        }
        if (!empty($request->inp__BirthDay)) {
          $updateDataUsuario['fecha_nacimiento_usuario'] = $request->inp__BirthDay;
        }
        if (!empty($request->inp__Cellphone)) {
          $updateDataUsuario['movil_usuario'] = $request->inp__Cellphone;
        }
        if (!empty($request->inp__Phone)) {
          $updateDataUsuario['telefono_usuario'] = $request->inp__Phone;
        }
        if (!empty($request->inp__Address)) {
          $updateDataUsuario['direccion_usuario'] = $request->inp__Address;
        }

        // Verificar y agregar valores al array updateDataPersona
        if (!empty($request->inp__FirstLastName)) {
          $updateDataPersona['primer_apellido_persona'] = $request->inp__FirstLastName;
        }
        if (!empty($request->inp__SecondLastName)) {
          $updateDataPersona['segundo_apellido_persona'] = $request->inp__SecondLastName;
        }

        if (count($updateDataUsuario) > 0) {
          Usuario::where('codigo_usuario', $id)->update($updateDataUsuario);
        }

        if (!empty($request->inp__FirstLastName) || !empty($request->inp__FirstLastName)) {
          Persona::where('codigo_usuario', $id)->update($updateDataPersona);
        }


        if (!empty($request->inp__SecondLastName) || !empty($request->inp__SecondLastName)) {
          Persona::where('codigo_usuario', $id)->update($updateDataPersona);
        }

        // Commit Transaction
        DB::commit();
        return response()->json(['success' => true, 'message' => 'Se actualizo correctamente el usuario']);
      }
    } catch (\Exception $e) {
      DB::rollback();
      Log::error(sprintf('%s:%s', 'Error al actualizar usuario', $e->getMessage()));
      return response()->json(['success' => false, 'errors' => 'Ocurrio un error al intentar actualizar el usuario']);
    }
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
   * Verify Email
   *
   * @return Response
   */
  public function verifyEmail(Request $request)
  {
    if ($request->ajax()) {


      try {
        if ($request->has('email') && !empty($request->email)) {
          $user = Usuario::getUserMail($request->all());

          if ($user instanceof Usuario) {
            $lastname = '';
            if ($user->tipo_usuario == Usuario::typeUser('person')) {
              $person = Persona::where('codigo_usuario', $user->codigo_usuario)->first();
              $lastname = $person->primer_apellido_persona;
            }

            return response()->json(['success' => true, 'labelname' => $user->nombre_usuario, 'labellastname' => $lastname, 'codigo_usuario' => $user->codigo_usuario, 'correo_electronico' => $user->correo_electronico]);
          }
          return response()->json(['success' => false, 'errors' => 'El correo ingresado no es válido']);
        }
        return response()->json(['success' => false, 'errors' => 'email no válido']);
      } catch (\Exception $e) {
        Log::error($e->getMessage());
        return response()->json(['success' => false, 'errors' => 'Ocurrió un error']);
      }
    }
    abort(404);
  }

  /**
   * Validate identification
   */

  public function verifyIdentification(Request $request)
  {
    if ($request->ajax()) {
      try {
        if ($request->has('identification') && !empty($request->identification)) {
          $user = Usuario::getUsersEvaluators($request->identification);
          if ($user instanceof Usuario) {
            $lastname = '';
            if ($user->tipo_usuario == Usuario::typeUser('person')) {
              $person = Persona::where('codigo_usuario', $user->codigo_usuario)->first();
              $lastname = $person->primer_apellido_persona;
            }

            return response()->json(['success' => true, 'labelname' => $user->nombre_usuario, 'labellastname' => $lastname, 'codigo_usuario' => $user->codigo_usuario, 'correo_electronico' => $user->correo_electronico]);
          }
          return response()->json(['success' => false, 'errors' => 'La identificación ingresada no existe']);
        }

        return response()->json(['success' => false, 'errors' => 'Identificación no válida']);
      } catch (\Exception $e) {
        Log::error($e->getMessage());
        return response()->json(['success' => false, 'errors' => 'Ocurrió un error']);
      }
    }
  }

  /*
   * Export data to excel
   *
   * @return Response
   */

  public function exportar(Request $request)
  {
    \Artisan::queue('reports:usersmodule', [
      'user' => \Auth::user()->codigo_usuario,
      'institution' => session('institution')->codigo_institucion,
      'filters' => $request->all()
    ]);

    $user = Usuario::find(\Auth::user()->codigo_usuario);
    if (!$user instanceof Usuario) {
      throw new \Exception("Error recuperando reporte módulo usuarios " . \Auth::user()->codigo_usuario);
    }

    activity()
      ->performedOn($user)
      ->withProperties([
        'attributes' => [
          'user' => $user->nombre_usuario,
          'user_id' => $user->codigo_usuario,
          'report' => 'módulo usuarios'
        ]
      ])
      ->log('activitylog.reports');

    return redirect()->route('usuario.index')->with('message', trans('app.reports.success', ['email' => \Auth::user()->correo_electronico]));
  }

  public function search(Request $request)
  {
    if ($request->ajax()) {
      return Datatables::of(Usuario::getUsers($request->all()))->make(true);
    }
    abort(403);
  }

  public function searchEntities(Request $request)
  {
    if ($request->ajax()) {
      if ($request->has('codigo_convocatoria')) {
        return Datatables::of(Usuario::userEntityForAnnouncement($request->all()))->make(true);
      } else {
        return Datatables::of(Usuario::userEntityAll($request->all()))->make(true);
      }
    }
    abort(403);
  }

  public function unlinkEntities(Request $request)
  {
    dd($request->all());
  }

  public function userDetail($id)
  {
    $activity_events = LogActivity::getActivityEvents($id)->items;

    //  dd($activity_events);

    $activity_call = LogActivity::getActivityCall($id)->items;
    $activity_curses = LogActivity::getActivityCurses($id)->items;
    $activity_asing_mentor_project = LogActivity::getActivityAsignMentorProject($id)->items;
    $activity_asing_judge_project = LogActivity::getActivityAsignJudgeProject($id)->items;
    $codigo_usuario = $id;
    return view(
      'users.detail.user-detail',
      compact(
        'activity_events',
        'activity_call',
        'activity_curses',
        'activity_asing_mentor_project',
        'activity_asing_judge_project',
        'codigo_usuario'
      )
    );
  }
}
