<?php

namespace App\Http\Controllers;

use App\Models\Core\Diagnostico;
use App\Models\Core\EvaluacionEmprendimiento;
use App\Models\Core\Formulario;
use App\Models\Core\ModuloDiagnostico;
use App\Models\Param\MenuInstitucion;
use Illuminate\Http\Request;

use App\Http\Requests;
use App\Http\Controllers\Controller;

use Illuminate\Support\Facades\Storage;

use Auth, AWS, Datatables, DB, TuProyecto, Log;

class ModuloDiagnosticoController extends Controller
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
          return response()->json(ModuloDiagnostico::getDiagnosticsList( $request->all() ));
          break;
        default:
          return Datatables::of(ModuloDiagnostico::getDiagnostics( $request->all() ) )->make(true);
      }
  }

  $menuInstitucion = MenuInstitucion::where("codigo_institucion", '=', session('institution')->codigo_institucion)->where("codigo_menu", '=', 2)->get();

  $diagnosticosInsti = Diagnostico::where("codigo_institucion", '=', session('institution')->codigo_institucion)->get();

    return view('modulodiagnostico.index', compact('menuInstitucion', 'diagnosticosInsti'));
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
      return view('diagnostic.general.create-edit',compact('diagnostic', 'formulario'));
    }
}
