<?php

namespace App\Http\Controllers;

use App\Models\Core\NivelMadurez;
use Illuminate\Http\Request;

use DB, Log, Datatables;

use App\Models\Core\BeneficioEvento, App\Models\Core\ListaBeneficioEvento, App\Models\Core\Estado;

class NivelMadurezController extends Controller
{
    /**
     * Display a listing of the resource.
     *
     * @return \Illuminate\Http\Response
     */
    public function index(Request $request)
    {
      dd($request->data);
      if($request->ajax()){
        switch ($request->data) {
          case 'list':
            return response()->json(NivelMadurez::getDiagnosticsList( $request->all() ));
            break;
          default:
            return Datatables::of( NivelMadurez::getDiagnostics( $request->all() ) )->make(true);
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
            $nivelMadurez = new NivelMadurez;
            try {

              $nivelMadurez->nombre_nivel_madurez = $request->nombre_nivel_madurez;
              $nivelMadurez->descripcion_nivel_madurez = $request->descripcion_nivel_madurez;
              $nivelMadurez->rango_minimo = $request->rango_minimo;
              $nivelMadurez->rango_maximo = $request->rango_maximo;
              $nivelMadurez->codigo_diagnostico = $request->codigo_diagnostico;
              $nivelMadurez->estado = true;
              $nivelMadurez->save();

              $response = $nivelMadurez;
              $response->success = true;
              $response->message = 'El nivel de madurez ha sido creado correctamente';
              $response->nombre_beneficio_evento = $request->nombre_nivel_madurez;
              $response->redirect = true;

              // Commit Transaction
              DB::commit();
              return response()->json($response);

            }catch(\Exception $e){
              DB::rollback();
              Log::error(sprintf('%s:%s', 'NivelMadurezController:store', $e->getMessage()));
              return response()->json(['success' => false, 'errors' => trans('app.exception')]);
            }

          } else {
            // Editar un NivelMadurez existente
            $nivelMadurez = NivelMadurez::findOrFail($request->currentMadurez['codigo_nivel_madurez']);
            try {

              if ($request->nombre_nivel_madurez != "" && $request->nombre_nivel_madurez != null){
                $nivelMadurez->nombre_nivel_madurez = $request->nombre_nivel_madurez;
              }

              if ($request->descripcion_nivel_madurez != "" && $request->descripcion_nivel_madurez != null){
                $nivelMadurez->descripcion_nivel_madurez = $request->descripcion_nivel_madurez;
              }

              if ($request->rango_minimo != "" && $request->rango_minimo != null){
                $nivelMadurez->rango_minimo = $request->rango_minimo;
              }

              if ($request->rango_maximo != "" && $request->rango_maximo != null){
                $nivelMadurez->rango_maximo = $request->rango_maximo;
              }

              $nivelMadurez->estado = true;
              $nivelMadurez->save();

              $response = $nivelMadurez;
              $response->success = true;
              $response->message = 'El nivel de madurez ha sido editado correctamente';
              $response->nombre_beneficio_evento = $request->nombre_nivel_madurez;
              $response->redirect = true;

              // Commit Transaction
              DB::commit();
              return response()->json($response);

            }catch(\Exception $e){
              DB::rollback();
              Log::error(sprintf('%s:%s', 'NivelMadurezController:store', $e->getMessage()));
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
    public function show($id)
    {
      dd('hola');
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
        return Datatables::of( NivelMadurez::getDiagnostic( $id) )->make(true);
      }
        abort(404);
    }
}
