<?php

namespace App\Http\Controllers;

use App\Models\Core\Especialidad;
use Illuminate\Http\Request;
use DB, Log;
use App\Http\Requests;
use App\Http\Controllers\Controller;
use App\Models\Seguridad\Usuario;


class EspecialidadUsuarioController extends Controller
{
    /**
     * Display a listing of the resource.
     *
     * @return \Illuminate\Http\Response
     */
    public function index(Request $request)
    {
      if($request->ajax()){

        return Especialidad::getSpecialtiesList();
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


      $especialidad = Especialidad::getSpecialtyById($request->data['option_id']);
      if(!$especialidad instanceof Especialidad){
        throw new \Exception("Especialidad no encontrada");
      }

      $user = Usuario::find($request->data['data_id']);
      if(!$user instanceof Usuario){
        throw new \Exception("Usuario no encontrado");
      }

       DB::beginTransaction();
      try {

       $user->attachSpeciality($especialidad);

        activity()
          ->performedOn($user)
          ->withProperties([
            'attributes' => [
              'title' => $especialidad->nombre_especialidad,
              'user' => $user->nombre_usuario,
              'user_id' => $user->codigo_usuario
            ] ])
          ->log('activitylog.user.speciality.created');

        // Commit Transaction
        DB::commit();
        return response()->json(['success' => true, 'message' => 'Se ha asignado las especialidad correctamente!!']);

      }catch(\Exception $e){
        DB::rollback();
        Log::error(sprintf('%s:%s', 'Especialidad:store', $e->getMessage()));
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
		//
    }

    /**
     * Remove the specified resource from storage.
     *
     * @param  int  $id
     * @return \Illuminate\Http\Response
     */
    public function destroy(Request $request)
    {
      if($request->ajax()){

        $especialidad = Especialidad::getSpecialtyById($request['especialidad_id']);
        if(!$especialidad instanceof Especialidad){
          throw new \Exception("Especialidad no encontrada");
        }

        $user = Usuario::find($request->data['data_id']);
        if(!$user instanceof Usuario){
          throw new \Exception("Usuario no encontrado");
        }

        DB::beginTransaction();
        try {

         $user->detachSpeciality($especialidad);

          activity()
            ->performedOn($user)
            ->withProperties([
              'attributes' => [
                'title' => $especialidad->nombre_especialidad,
                'user' => $user->nombre_usuario,
                'user_id' => $user->codigo_usuario
              ] ])
            ->log('activitylog.user.speciality.deleted');

          // Commit Transaction
          DB::commit();
          return response()->json(['success' => true, 'message' => 'Se ha eliminado la especialidad correctamente!!']);

        }catch(\Exception $e){
          DB::rollback();
          Log::error(sprintf('%s:%s', 'Especialidad:destroy', $e->getMessage()));
          return response()->json(['success' => false, 'errors' => trans('app.exception')]);
        }
      }
      abort(404);
    }
}
