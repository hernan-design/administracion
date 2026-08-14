<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;

use App\Http\Requests;
use App\Http\Controllers\Controller;

use TuProyecto;
use DB, Lang, App, Log, Input, Auth;

use App\Models\Core\Discusion, App\Models\Core\Estado, App\Models\Core\ComentarioDiscusion, App\Models\Core\Comentario;
use App\Models\Seguridad\Usuario;

class ComentarioDiscusionController extends Controller
{
	/**
     * Instantiate a new Controller instance.
     */
    public function __construct()
    {
        $this->middleware('ability:admin|super-admin|administrativo|gestor-de-mentorias,view,marketplace/transacciones/acuerdo');
        $this->middleware('ability:admin|super-admin|administrativo,create,marketplace/transacciones/acuerdo', ['only' => ['create', 'store']]);
        $this->middleware('ability:admin|super-admin|administrativo,edit,marketplace/transacciones/acuerdo', ['only' => ['edit', 'update']]);
        $this->middleware('ability:admin|super-admin|administrativo,remove,marketplace/transacciones/acuerdo', ['only' => ['destroy']]);
    }

    /**
     * Display a listing of the resource.
     *
     * @return \Illuminate\Http\Response
     */
    public function index(Request $request)
    {
        if($request->ajax()){
            if(Input::has('codigo_discucion')) {
                list($codigo_discucion) = TuProyecto::decryptSrcParams($request->input('codigo_discucion'));
                if(!$codigo_discucion || !is_numeric($codigo_discucion)) {
                    App::abort(403);
                }
                $discussion = Discusion::find($codigo_discucion);
                if(!$discussion instanceof Discusion) {
                    abort(404);
                }
                return response()->json(ComentarioDiscusion::getComments($discussion->codigo_discucion));
            }
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
        if ($request->ajax()) {
            $data = $request->all();
            $commentDiscussion = new ComentarioDiscusion;
            if ($commentDiscussion->isValid($data)) {

                list($codigo_discucion) = TuProyecto::decryptSrcParams($request->input('codigo_discucion'));
                if(!$codigo_discucion || !is_numeric($codigo_discucion)) {
                    App::abort(403);
                }
                $discussion = Discusion::find($codigo_discucion);
                if(!$discussion instanceof Discusion) {
                    abort(404);
                }

                DB::beginTransaction();
                try {

                    // Discusion
                    $comentario = new Comentario;
                    $comentario->texto_comentario = $request->texto_comentario_discucion;
                    $comentario->codigo_usuario = Auth::user()->codigo_usuario;
                    $comentario->fecha_comentario = date('Y-m-d');
                    $comentario->hora_comentario = date('H:i:s');
                    $comentario->save();

                    // Comment discusion
                    $commentDiscussion->codigo_discucion = $discussion->codigo_discucion;
                    $commentDiscussion->codigo_comentario = $comentario->codigo_comentario;
                    $commentDiscussion->codigo_estado = Estado::getState('active');
                    $commentDiscussion->save();

                    // Prepare response
                    $response = ComentarioDiscusion::buildComment(ComentarioDiscusion::getComment($commentDiscussion->codigo_discucion, $commentDiscussion->codigo_comentario));
                    $response->success = true;

                    // Commit Transaction
                    DB::commit();
                    return response()->json($response);
                }catch(\Exception $e){
                    DB::rollback();
                    Log::error($e->getMessage());
                    return response()->json(['success' => false, 'errors' => Lang::get('app.exception')]);
                }
            }
            return response()->json(['success' => false, 'errors' => $commentDiscussion->errors]);
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
    public function destroy(Request $request, $id)
    {
        if ($request->ajax()) {

            // Decrypt id
            list($codigo_discucion) = TuProyecto::decryptSrcParams($request->input('codigo_discucion'));
            if(!$codigo_discucion || !is_numeric($codigo_discucion)) {
                abort(403);
            }

            list($codigo_comentario) = TuProyecto::decryptSrcParams($request->input('codigo_comentario'));
            if(!$codigo_comentario || !is_numeric($codigo_comentario)) {
                abort(403);
            }
            $discussion = Discusion::find($codigo_discucion);
            if(!$discussion instanceof Discusion) {
                abort(404);
            }

            $comment = Comentario::find($codigo_comentario);
            if(!$comment instanceof Comentario) {
                abort(404);
            }

            // Validate user update comment
            if(in_array(Auth::user()->codigo_usuario, [$comment->codigo_usuario])) {
                DB::beginTransaction();
                try {

                    // Update Comment Discussion
                    ComentarioDiscusion::where('codigo_discucion', $discussion->codigo_discucion )->where('codigo_comentario', $comment->codigo_comentario)
                        ->update(['codigo_estado' => Estado::getState('removed')]);

                    DB::commit();
                    return response()->json(['success' => true]);
                }catch(\Exception $e){
                    DB::rollback();
                    Log::error($e->getMessage());
                    return response()->json(['success' => false, 'errors' => Lang::get('app.exception')]);
                }
            }
            abort(403);
        }
        abort(404);
    }
}
