<?php

namespace App\Http\Controllers;

use App\Models\Core\SectorEconomico;
use Illuminate\Http\Request;
use DB, Log;
use App\Http\Requests;
use App\Http\Controllers\Controller;
use App\Models\Seguridad\Usuario;

class SectorEconomicoController extends Controller
{
    /**
     * Display a listing of the resource.
     *
     * @return \Illuminate\Http\Response
     */
    public function index(Request $request)
    {
      if($request->ajax()){

        return SectorEconomico::getSectors();
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


      $sector = SectorEconomico::getIndustry($request->data['option_id']);
      if(!$sector instanceof SectorEconomico){
        throw new \Exception("Sector Economico no encontrado");
      }

      $user = Usuario::find($request->data['data_id']);
      if(!$user instanceof Usuario){
        throw new \Exception("Usuario no encontrado");
      }

       DB::beginTransaction();
      try {

        $user->persona->attachEconomicSector($sector);

        activity()
          ->performedOn($user)
          ->withProperties([
            'attributes' => [
              'title' => $sector->nombre_sector_economico,
              'user' => $user->nombre_usuario,
              'user_id' => $user->codigo_usuario
            ] ])
          ->log('activitylog.user.sector.created');

        // Commit Transaction
        DB::commit();
        return response()->json(['success' => true, 'message' => 'Se ha asignado el sector correctamente!!']);

      }catch(\Exception $e){
        DB::rollback();
        Log::error(sprintf('%s:%s', 'SectorEconomico:store', $e->getMessage()));
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

        $sector = SectorEconomico::getIndustry($request['sector_id']);
        if(!$sector instanceof SectorEconomico){
          throw new \Exception("Sector Economico no encontrado");
        }

        $user = Usuario::find($request->data['data_id']);
        if(!$user instanceof Usuario){
          throw new \Exception("Usuario no encontrado");
        }

        DB::beginTransaction();
        try {

          $user->persona->detachEconomicSector($sector);

          activity()
            ->performedOn($user)
            ->withProperties([
              'attributes' => [
                'title' => $sector->nombre_sector_economico,
                'user' => $user->nombre_usuario,
                'user_id' => $user->codigo_usuario
              ] ])
            ->log('activitylog.user.sector.deleted');

          // Commit Transaction
          DB::commit();
          return response()->json(['success' => true, 'message' => 'Se ha eliminado el sector economico correctamente!!']);

        }catch(\Exception $e){
          DB::rollback();
          Log::error(sprintf('%s:%s', 'SectorEconomico:destroy', $e->getMessage()));
          return response()->json(['success' => false, 'errors' => trans('app.exception')]);
        }
      }
      abort(404);
    }
}
