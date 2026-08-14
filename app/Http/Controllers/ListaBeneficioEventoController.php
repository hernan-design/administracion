<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;

use DB, Log;

use App\Models\Core\BeneficioEvento, App\Models\Core\ListaBeneficioEvento, App\Models\Core\Estado;

class ListaBeneficioEventoController extends Controller
{
    /**
     * Display a listing of the resource.
     *
     * @return \Illuminate\Http\Response
     */
    public function index(Request $request)
    {
        if($request->ajax()){
            if($request->has('codigo_usuario_evento') && $request->has('codigo_sesion_evento')){
                return response()->json( ListaBeneficioEvento::listBenefitsEvent( $request->all() ) );
            }

            return response()->json( ListaBeneficioEvento::benefitsEvent( $request->all() ) );
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
            $listBenefitEvent = new ListaBeneficioEvento;
            
            DB::beginTransaction();
			try {

				$listBenefitEvent->codigo_beneficio_evento = $request->codigo_beneficio_evento;
				$listBenefitEvent->codigo_evento = $request->codigo_evento;
				$listBenefitEvent->codigo_estado = Estado::getState('active');
                $listBenefitEvent->save();

                $benefit = BeneficioEvento::find($request->codigo_beneficio_evento);

                $response = $listBenefitEvent;
                $response->success = true;
                $response->message = 'Se ha creado el beneficio correctamente.';
                $response->nombre_beneficio_evento = $benefit->nombre_beneficio_evento;

				// Commit Transaction
				DB::commit();
				return response()->json($response);

			}catch(\Exception $e){
				DB::rollback();
				Log::error(sprintf('%s:%s', 'BeneficioEventoController:store', $e->getMessage()));
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
}
