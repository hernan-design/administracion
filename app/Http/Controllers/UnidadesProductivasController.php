<?php

namespace App\Http\Controllers;

use App\Models\Core\NivelMadurez;
use App\Models\Core\UnidadesProductivas;
use Illuminate\Http\Request;

use DB, Log, Datatables;

use App\Models\Core\BeneficioEvento, App\Models\Core\ListaBeneficioEvento, App\Models\Core\Estado;

class UnidadesProductivasController extends Controller
{
    /**
     * Display a listing of the resource.
     *
     * @return \Illuminate\Http\Response
     */
    public function index(Request $request)
    {
      if($request->ajax()){
        switch ($request->data) {
          case 'list':
            return response()->json(UnidadesProductivas::getDiagnosticsList( $request->all() ));
            break;
          default:
            return Datatables::of( UnidadesProductivas::getDiagnostics( $request->all() ) )->make(true);
        }
    }
        abort(404);
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
          if(!isset($request->currentMadurez)){
            $unidadProductiva = new UnidadesProductivas;
            try {

              $unidadProductiva->nombre_unidad_productiva = $request->nombre_unidad_productiva;
              $unidadProductiva->descripcion_unidad_productiva = $request->descripcion_unidad_productiva;
              $unidadProductiva->porcentaje = $request->porcentaje;
              $unidadProductiva->codigo_diagnostico = $request->codigo_diagnostico;
              $unidadProductiva->estado = true;
              $unidadProductiva->save();

              $response = $unidadProductiva;
              $response->success = true;
              $response->message = 'La unidad productiva ha sido creada correctamente';
              $response->nombre_unidad_productiva = $request->nombre_unidad_productiva;

              // Commit Transaction
              DB::commit();
              return response()->json($response);

            }catch(\Exception $e){
              DB::rollback();
              Log::error(sprintf('%s:%s', 'UnidadesProductivasController:store', $e->getMessage()));
              return response()->json(['success' => false, 'errors' => trans('app.exception')]);
            }
          } else {
            //Editar la unidad productiva existente
            $unidadProductiva = UnidadesProductivas::findOrFail($request->currentMadurez['codigo_unidad_productiva']);
            try {

              if ($request->nombre_unidad_productiva != "" && $request->nombre_unidad_productiva != null){
                $unidadProductiva->nombre_unidad_productiva = $request->nombre_unidad_productiva;
              }
              if ($request->descripcion_unidad_productiva != "" && $request->descripcion_unidad_productiva != null){
                $unidadProductiva->descripcion_unidad_productiva = $request->descripcion_unidad_productiva;
              }
              if ($request->porcentaje != "" && $request->porcentaje != null){
                $unidadProductiva->porcentaje = $request->porcentaje;
              }
              $unidadProductiva->estado = true;

                $unidadProductiva->save();
                $response = $unidadProductiva;
                $response->success = true;
                $response->message = 'La unidad productiva ha sido editada correctamente';
                $response->nombre_beneficio_evento = $request->nombre_unidad_productiva;

                // Commit Transaction
                DB::commit();
                return response()->json($response);
            }catch(\Exception $e){
              DB::rollback();
              Log::error(sprintf('%s:%s', 'UnidadesProductivasController:store', $e->getMessage()));
              return response()->json(['success' => false, 'errors' => trans('app.exception')]);
            }
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
    public function destroy(Request $request, $id)
    {
        if($request->ajax()){
			if(!is_numeric($id)){
				return response()->json(['success' => false, 'errors' => 'El dato seleccionado no corresponde con la base de datos, verifique.']);
			}

			$listBenefitEvent = ListaBeneficioEvento::find($id);
			if(!$listBenefitEvent instanceof ListaBeneficioEvento){
				return response()->json(['success' => false, 'errors' => 'El beneficio no se encuentra asignado, verifique.']);
			}

			DB::beginTransaction();
			try {
				$listBenefitEvent->delete();

				// Commit Transaction
				DB::commit();
				return response()->json(['success' => true, 'message' => 'El beneficio ha sido eliminado correctamente.']);

			}catch(\Exception $e){
				DB::rollback();
				Log::error(sprintf('%s:%s', 'BeneficioEventoController:destroy', $e->getMessage()));
				return response()->json(['success' => false, 'errors' => trans('app.exception')]);
			}
		}
    }

     /**
     * Show the form for editing the specified resource.
     *
     * @param  int  $id
     * @return \Illuminate\Http\Response
     */
    public function edit($id)
    {
      if($id != "" && $id != null){
        return Datatables::of( UnidadesProductivas::getDiagnostic( $id) )->make(true);
      }
        abort(404);
    }
}
