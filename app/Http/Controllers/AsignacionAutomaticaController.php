<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;

use App\Http\Requests;
use App\Http\Controllers\Controller;
use App\Models\Core\Convocatoria;

use TuProyecto;
use DB, Lang, App, Log, Auth, Queue, SendEmail;

use App\Models\Core\AsignacionJurado,
    App\Models\Core\Estado,
    App\Models\Core\RegistroFaseUsuario,
    App\Models\Core\AsignacionEvaluadores;
use App\Models\Seguridad\Usuario, Custom\Innpulsa;

class AsignacionAutomaticaController extends Controller
{
	/**
     * Instantiate a new Controller instance.
     */
    public function __construct()
    {
        $this->middleware('ability:admin|super-admin|administrativo,view,evaluacion/asignacion');
        $this->middleware('ability:admin|super-admin|administrativo,create,evaluacion/asignacion', ['only' => ['create', 'store']]);
        $this->middleware('ability:admin|super-admin|administrativo,edit,evaluacion/asignacion', ['only' => ['edit', 'update']]);
        $this->middleware('ability:admin|super-admin|administrativo,remove,evaluacion/asignacion', ['only' => ['destroy']]);
    }

    /**
     * Display a listing of the resource.
     *
     * @return \Illuminate\Http\Response
     */
    public function index(Request $request)
    {
        $evaluadores = AsignacionEvaluadores::where('codigo_convocatoria', $request->codigo_convocatoria)
                                        ->get();
        // dd($evaluadores);
        $evaluadores = $evaluadores->map->user;
// dd($evaluadores);
        return response()->json($evaluadores);
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
        if($request->ajax()) {
            if ($request->has('email') && isset($request->email) && !empty($request->email)) {
                $user = Usuario::getUserMail(['email' => $request->email]);

                if(!$user instanceof Usuario) {
                    return response()->json([
                                'success' => false,
                                'errors' =>
                                'No existe evaluador asociado a esta dirección de correo electrónico.'
                            ]);
                }
            }

            if(!$user->hasRole(['jurado'])) {
                return response()->json(['success' => false, 'errors' => 'El usuario no tiene asignado el rol de evaluador.']);
            }

            // Decrypt src-params
            $codigo_convocatoria = $request->codigo_convocatoria;
            if(!$codigo_convocatoria || !is_numeric($codigo_convocatoria)) {
                return response()->json(['success' => false, 'errors' => trans('app.exception')]);
            }

            DB::beginTransaction();
            try {

                $convocatoria = Convocatoria::find($codigo_convocatoria);

                $evaluador = AsignacionEvaluadores::where('codigo_usuario', $user->codigo_usuario);
                if ($evaluador->count() > 0) {
                    return response()->json(['success' => false, 'errors' => 'Evaluador ya fue asignado']);
                }
                $evaluador = new AsignacionEvaluadores;
                $evaluador->codigo_usuario = $user->codigo_usuario;
                $evaluador->codigo_convocatoria = $convocatoria->codigo_convocatoria;
                $evaluador->save();


                // Prepare response
                $response = new \stdClass();
                $response->success = true;
                $response->message = 'Asignación realizada con éxito.';

                activity()
                ->performedOn($convocatoria)
                ->withProperties([
                    'attributes' => [
                        'convocatoria' => $convocatoria->nombre_convocatoria
                    ]
                ])
                ->log('activitylog.assign.evaluator_automatic');


                // Commit Transaction
                DB::commit();
                return response()->json($response);

            }catch(\Exception $e){
                DB::rollback();
                Log::error($e->getMessage());
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
     * @param  \Illuminate\Http\Request  $request
     * @return \Illuminate\Http\Response
     */
    public function destroy(Request $request)
    {
        if($request->ajax()) {

            if ($request->has('email') && isset($request->email) && !empty($request->email)) {
                $user = Usuario::getUserMail(['email' => $request->email]);

                if(!$user instanceof Usuario) {
                    return response()->json([
                                'success' => false,
                                'errors' => 'No existe evaluador asociado a esta dirección de correo electrónico.'
                            ]);
                }
            }

            if(!$user->hasRole(['jurado'])) {
                return response()->json(['success' => false, 'errors' => 'El usuario no tiene asignado el rol de evaluador.']);
            }

            // Decrypt src-params
            $codigo_convocatoria = $request->codigo_convocatoria;
            if(!$codigo_convocatoria || !is_numeric($codigo_convocatoria)) {
                return response()->json(['success' => false, 'errors' => trans('app.exception')]);
            }

            DB::beginTransaction();
            try {

                $convocatoria = Convocatoria::find($codigo_convocatoria);

                $evaluador = AsignacionEvaluadores::where('codigo_convocatoria', $codigo_convocatoria)
                                                    ->where('codigo_usuario', $user->codigo_usuario)
                                                    ->first();

                // Prepare response
                $response = new \stdClass();
                $response->success = true;
                $response->message = 'Eliminación realizada con éxito';

                activity()
                ->performedOn($evaluador)
                ->withProperties([
                    'attributes' => [
                        'convocatoria' => $convocatoria->nombre_convocatoria
                    ]
                ])
                ->log('activitylog.assign.delete_evaluator_automatic');

                $evaluador->delete();

                // Commit Transaction
                DB::commit();
                return response()->json($response);

            }catch(\Exception $e){
                DB::rollback();
                Log::error($e->getMessage());
                return response()->json(['success' => false, 'errors' => trans('app.exception')]);
            }
        }
        abort(404);
    }
}
