<?php

namespace App\Http\Controllers;

use App\Models\Core\Diagnostico;
use App\Models\Core\EvaluacionEmprendimiento;
use App\Models\Core\Formulario;
use App\Models\Core\ModuloDiagnostico;
use App\Models\Core\PreguntaEncuesta;
use App\Models\Core\PreguntasUnidadProductiva;
use App\Models\Core\UnidadesProductivas;
use App\Models\Param\MenuInstitucion;
use App\Models\Seguridad\Usuario;
use Illuminate\Http\Request;

use App\Http\Requests;
use App\Http\Controllers\Controller;

use Illuminate\Support\Facades\Storage;

use Auth, AWS, Datatables, DB, TuProyecto, Log;

class DiagnosticosController extends Controller
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
          return response()->json(Diagnostico::getDiagnosticsList( $request->all() ));
          break;
        default:
          return Datatables::of( Diagnostico::getDiagnostics( $request->all() ) )->make(true);
      }
  }

    return view('diagnostic.general.index');
  }

  /**
     * Show the form for creating a new resource.
     *
     * @return \Illuminate\Http\Response
     */
    public function create()
    {

      $formulario = Formulario::getFormsAlls();
      return view('diagnostic.general.create-edit', compact('formulario'));
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
      $data = $request->all();

      $diagnostic = new Diagnostico;
      if ($diagnostic->isValid($data)) {
        DB::beginTransaction();
        try {
          $diagnostic->fill($data);
          $diagnostic->nombre_diagnostico = $request->nombre_diagnostico;
          $diagnostic->descripcion_diagnostico = $request->descripcion_diagnostico;
          $diagnostic->codigo_institucion = session('institution')->codigo_institucion;
          $diagnostic->codigo_encuesta = $request->codigo_encuesta;
          $diagnostic->save();
          // Commit Transaction
          DB::commit();
          return response()->json(['success' => true, 'message' => 'El diagnóstico ha sido creado correctamente!!', 'codigo_diagnostico' => $diagnostic->codigo_diagnostico, 'redirect' => true ]);

        } catch(\Exception $e){
          DB::rollback();
          Log::error(sprintf('%s:%s', 'DiagnosticosController:store', $e->getMessage()));
          return response()->json(['success' => false, 'errors' => trans('app.exception')]);
        }
      }
    }

    abort(404);
  }

  /**
     * Show the form for editing the specified resource.
     *
     * @param  int  $id
     * @return \Illuminate\Http\Response
     */
    public function edit($id)
    {
      if(!is_numeric($id)){
        abort(404);
      }

      $diagnostic = Diagnostico::getDiagnostic($id);
      if(!$diagnostic instanceof Diagnostico){
        abort(404);
      }

      $diagnostic = Diagnostico::buidDiagnostic( $diagnostic );
      $formulario = Formulario::getFormsAlls();
      $menuInstitucion = MenuInstitucion::where("codigo_institucion", '=', session('institution')->codigo_institucion)->where("codigo_menu", '=', 2)->get();
      //Se traen los modulos que están asociados al diagnostico
      $modulosDiagnostico = ModuloDiagnostico::where("codigo_diagnostico", "=", $id);
      //Se traen los modulos que no están asociados al diagnostico para remover los módulos que ya están
      $modulosDiagnosticoRemove = ModuloDiagnostico::where("codigo_diagnostico", "!=", $id);

      $filteredMenuInstitucion = $menuInstitucion->reject(function ($menu) use ($modulosDiagnosticoRemove) {
        return in_array($menu->codigo_menu_institucion, $modulosDiagnosticoRemove->pluck('codigo_menu_institucion')->toArray());
    });

    //Se traen las unidades productivas que están creadas para mostrarlas en preguntas unidad productiva
    $unidadesProductivas = UnidadesProductivas::where("codigo_diagnostico", "=", $id)->get();

    // Obtener los códigos de unidades productivas
    $unidadesProductivasArray = $unidadesProductivas->pluck('codigo_unidad_productiva')->toArray();

    // Ahora puedes usar $unidadesProductivasArray en una consulta con IN
    $preguntasUnidadProductiva = PreguntasUnidadProductiva::whereIn('codigo_unidad_productiva', $unidadesProductivasArray)->get();

    //Se toma el id del formulario asociado al diagnostico
    $formulary_id = $diagnostic->codigo_encuesta;

    //Se traen las preguntas del formulario que está asociado a el diagnostico
    $preguntasFormulario = PreguntaEncuesta::where('codigo_encuesta', $formulary_id)
        ->where(function ($query) {
            $query->where('tipo_campo_pregunta', 15)
                  ->orWhere('tipo_pregunta_encuesta', 2)
                  ->orWhere('tipo_pregunta_encuesta', 3);
        })
        ->get();

    return view('diagnostic.general.create-edit', compact(
        'diagnostic',
        'formulario',
        'filteredMenuInstitucion',
        'modulosDiagnostico',
        'unidadesProductivas',
        'preguntasFormulario',
        'preguntasUnidadProductiva'
    ));
    }

    /**
   * Store a newly created resource in storage.
   *
   * @param  \Illuminate\Http\Request  $request
   * @return \Illuminate\Http\Response
   */
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
}
