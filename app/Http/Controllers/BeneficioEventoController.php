<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;

use DB, Log;

use App\Models\Core\BeneficioEvento, App\Models\Core\Estado;

class BeneficioEventoController extends Controller
{
    /**
     * Display a listing of the resource.
     *
     * @return \Illuminate\Http\Response
     */
    public function index(Request $request)
    {
        if($request->ajax()){
            return response()->json( BeneficioEvento::benefitsEventList() );
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
            $benefit = new BeneficioEvento;
            
            DB::beginTransaction();
			try {

				$benefit->nombre_beneficio_evento = $request->nombre_beneficio_evento;
				$benefit->codigo_estado = Estado::getState('active');
				$benefit->codigo_institucion = session('institution')->codigo_institucion;
                $benefit->save();

				// Commit Transaction
				DB::commit();
				return response()->json(['success' => true, 'message' => 'Se ha creado el beneficio correctamente.']);

			}catch(\Exception $e){
				DB::rollback();
				Log::error(sprintf('%s:%s', 'BeneficioEventoController:store', $e->getMessage()));
				return response()->json(['success' => false, 'errors' => trans('app.exception')]);
			}
		}
		abort(404);
    }
}
