<?php

namespace App\Http\Controllers;

use App\Models\Core\NivelMadurez;
use App\Models\Core\PreguntasUnidadProductiva;
use App\Models\Core\UnidadesProductivas;
use Illuminate\Http\Request;

use DB, Log, Datatables;

use App\Models\Core\BeneficioEvento, App\Models\Core\ListaBeneficioEvento, App\Models\Core\Estado;

class PreguntasUnidadProductivaController extends Controller
{
    /**
     * Display a listing of the resource.
     *
     * @return \Illuminate\Http\Response
     */
    public function index(Request $request)
    {
      if($request->ajax()){
        return Datatables::of( UnidadesProductivas::getDiagnostics( $request->all() ) )->make(true);
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
        $data = $request->except(['codigo_unidad_productiva', 'codigo_pregunta_encuesta']); // Obtener todos los datos del request excepto los campos no numéricos

        // Iniciar transacción
        DB::beginTransaction();

        try {
            // Se recorre $data
            foreach ($data as $key => $item) {
                // Filtrar solo claves numéricas
                if (is_numeric($key)) {
                    // Eliminar los registros existentes para la unidad productiva
                    PreguntasUnidadProductiva::where('codigo_unidad_productiva', (int)$key)->delete();

                    foreach ($item as $preguntasUp) {
                        $preguntaUnidadProductiva = new PreguntasUnidadProductiva();
                        // Asignar valores
                        $preguntaUnidadProductiva->codigo_unidad_productiva = (int)$key;
                        $preguntaUnidadProductiva->codigo_pregunta_encuesta = $preguntasUp;
                        $preguntaUnidadProductiva->save();
                    }
                }
            }

            // Commit Transaction
            DB::commit();

            $response = new \stdClass();
            $response->success = true;
            $response->message = 'Las preguntas se han asignado correctamente';

            return response()->json($response);

        } catch (\Exception $e) {
            // Rollback Transaction
            DB::rollback();
            Log::error(sprintf('%s:%s', 'PreguntasUnidadProductivaController:store', $e->getMessage()));
            return response()->json(['success' => false, 'errors' => trans('app.exception')]);
        }
    }

    abort(404);
}




    public function update(Request $request, $id)
  {

    if($request->ajax()){
      $data = $request->all();

      if(!isset($data[0])){
        return response()->json(['success' => false, 'errors' => "Debe seleccionar mínimo un módulo"]);
      }
      // Obtener los índices numéricos del array que contienen los módulos seleccionados
      $modulesIndexes = array_filter(array_keys($data), 'is_numeric');

      $totalIndexes = count($modulesIndexes);
      $counter = 1;

      //Se eliminan los registros que tienen el diagnostico  asignado
      ModuloDiagnostico::where('codigo_diagnostico', $data["codigo_diagnostico"])->delete();

      foreach ($modulesIndexes as $index) {
        $moduleDiagnostic = new ModuloDiagnostico;
        $modulo = $data[$index];
        DB::beginTransaction();

        try {
          $moduleDiagnostic->codigo_diagnostico = $data["codigo_diagnostico"];
          $moduleDiagnostic->codigo_menu_institucion = $modulo;
          $moduleDiagnostic->save();
          // Commit Transaction
          DB::commit();
          $counter ++;

        } catch(\Exception $e){
          DB::rollback();
          Log::error(sprintf('%s:%s', 'DiagnosticosController:update', $e->getMessage()));
          return response()->json(['success' => false, 'errors' => trans('app.exception')]);
        }

      }

      if($counter > $totalIndexes){
        return response()->json(['success' => true, 'message' => 'El diagnóstico ha sido asignado correctamente!!', 'codigo_diagnostico' => $moduleDiagnostic->codigo_diagnostico, 'redirect' => true ]);
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
