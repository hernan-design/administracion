<?php
namespace App\Http\Controllers;

use Illuminate\Http\Request;

use App\Http\Requests;
use App\Http\Controllers\Controller;


use App\Models\Seguridad\Usuario, App\Models\Seguridad\Rol,  App\Models\Seguridad\RolUsuario, App\Models\Core\Estado,
App\Models\Core\Persona, App\Models\Core\EtapaMentoria, App\Models\Core\EtapaProyecto,
App\Models\Core\UsuarioInstitucion;


use Datatables, Log, DB, SendEmail, Auth, Lang, Input, TuProyecto;


class ProfileMentorController extends Controller
{
  /**
   * Instantiate a new MentorController instance.
   */
  public function __construct()
  {
    $this->middleware('auth', ['except' => ['show']]);

    $this->middleware('ability:super-admin,assign-mentor,mentoria', ['only' => ['create', 'store']]);
  }

  /**
   * Display a listing of the resource.
   *
   * @return \Illuminate\Http\Response
   */
  public function index(Request $request)
  {
    if($request->ajax()) {
      return response()->json(Persona::getMentorsList());
    }
    return view('core/mentoring/main');
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
   * Update the specified resource in storage.
   *
   * @param  \Illuminate\Http\Request  $request
   * @param  int  $id
   * @return \Illuminate\Http\Response
   */
  public function update(Request $request, $id)
  {



    if($request->ajax()) {
      // Decrypt src-params
      if(is_numeric($id)){
          $codigo_usuario = $id;
      }else{
          list($codigo_usuario) = TuProyecto::decryptSrcParams($id);
      }

      if(!$codigo_usuario || !is_numeric($codigo_usuario)) {
        abort(403);
      }
      $user = Usuario::find($codigo_usuario);
      if(!$user instanceof Usuario) {
        abort(404);
      }
      $person = Persona::find($user->persona->codigo_persona);
      if(!$person instanceof Persona) {
        abort(404);
      }

      // Validate users update mentor
        $data = $request->all();
        DB::beginTransaction();
        try{
          $person->fill($data);
          if($request->has('codigo_especialidad_destaca')){
            $person->codigo_especialidad = $request->input("codigo_especialidad_destaca");
          }

          if($request->has('forma_ayuda_mentoria')){
            $person->forma_ayuda_mentoria = $request->input("forma_ayuda_mentoria");
          }


          $user->fill($data);

          if($request->has('experiencia_usuario')){
            $user->experiencia_usuario = $request->input("experiencia_usuario");
          }

          // Stage Mentoring
          $items = EtapaProyecto::getStages();

          $user->etapamentoria()->detach();

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

          $person->save();
          $user->save();


          // Prepare response
          $mentor = Usuario::getMentor($user->codigo_usuario);



          if(!$mentor instanceof Usuario){
            DB::rollback();
            abort(404);
          }

          $response = Usuario::buildUser($mentor);

          $response->success = true;
          $response->message = "Se ha actualizado la información del mentor";

          DB::commit();

          return response()->json($response);
        }catch(\Exception $e){
          DB::rollback();
          Log::error($e->getMessage());
          return response()->json(['success' => false, 'errors' => Lang::get('app.exception')]);
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
  public function sendMentorTerms(Request $request, $id)
  {
    if($request->ajax()) {
      // Decrypt src-params
      if (is_numeric($id)) {
        $codigo_usuario = $id;
      } else {
        list($codigo_usuario) = TuProyecto::decryptSrcParams($id);
      }

      if (!$codigo_usuario || !is_numeric($codigo_usuario)) {
        abort(403);
      }

      $user = Usuario::find($codigo_usuario);
      if (!$user instanceof Usuario) {
        abort(404);
      }

      $mentor = Usuario::getMentor($id);
      if(!$mentor instanceof Usuario){
        abort(404);
      }

      
      $data = $request->all();
      try{  

          if($mentor->isValidMentor($data)){
       
            // Send notification
            $data = [];
            $data['_template'] = 'emails.mentoring.mentorterms';
            $data['_inst'] = session('institution')->codigo_institucion;
            $data['_subject'] = 'Aceptación de términos y políticas de mentor';
            $data['_url_action']  = '/mentoria/mentores/';
            $data['_url_link']  = trim(strtolower($user->nickname_usuario));
            $data['_to'] = $user->correo_electronico;
            $data['_to_name'] = ucwords(strtolower($user->nombre_usuario));
            SendEmail::notification($data);
        }else{  
          Log::error(sprintf('%s:%s', 'ProfileMentorController::sendMentorTerms', $mentor->errors));
          return response()->json(['success' => false, 'errors2' => $mentor->errors]);
        }
      }catch(\Exception $e){
        Log::error(sprintf('%s:%s', 'ProfileMentorController::sendMentorTerms', $e->getMessage()));
        return response()->json(['success' => false, 'errors2' => Lang::get('app.exception')]);
    }  
      $response = new \stdClass();
      $response->success = true;
      $response->message = "Se ha enviado el correo de aceptación de los términos y políticas para mentores";
      return response()->json($response);
    }
    abort(404);
  }
}
