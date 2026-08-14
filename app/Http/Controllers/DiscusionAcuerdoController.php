<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;

use App\Http\Requests;
use App\Http\Controllers\Controller;

use TuProyecto;

use DB, Log, Auth;

use App\Models\Core\DiscusionAcuerdoCompra, App\Models\Core\Discusion, App\Models\Core\AcuerdoCompra, App\Models\Core\Estado;
use App\Models\Seguridad\Usuario, App\Models\Seguridad\Modulo;
use App\Models\Param\DiscusionInstitucion;

class DiscusionAcuerdoController extends Controller
{
	/**
     * Instantiate a new Controller instance.
     */
    public function __construct()
    {
        $this->middleware('ability:admin|super-admin|administrativo,view,marketplace/transacciones/acuerdo');
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
        if($request->ajax()) {
            if($request->has('codigo_acuerdo_compra')) {
                // Decrypt src-params
                list($codigo_acuerdo) = TuProyecto::decryptSrcParams($request->input('codigo_acuerdo_compra'));
                if(!$codigo_acuerdo || !is_numeric($codigo_acuerdo)) {
                    abort(403);
                }
                return response()->json(DiscusionAcuerdoCompra::getAgreementDiscussions($codigo_acuerdo));
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
            $discussion = new Discusion;
            if ($discussion->isValid($data)) {
                // Decrypt src-params
                list($codigo_acuerdo) = TuProyecto::decryptSrcParams($request->input('codigo_acuerdo_compra'));
                if(!$codigo_acuerdo || !is_numeric($codigo_acuerdo)) {
                    abort(403);
                }

                $acuerdo = AcuerdoCompra::find($codigo_acuerdo);
                if(!$acuerdo instanceof AcuerdoCompra) {
                    abort(403);
                }

                DB::beginTransaction();
                try {
                    // Discussion
                    $discussion->fill($data);
                    $discussion->codigo_estado = Estado::getState('active');
                    $discussion->codigo_usuario = Auth::user()->codigo_usuario;
                    $discussion->codigo_modulo = Modulo::getModule('negotiationplatform');
                    $discussion->save();

                    // Discussion Agreement
                    $acuerdoDiscussion = new DiscusionAcuerdoCompra;
                    $acuerdoDiscussion->codigo_discucion = $discussion->codigo_discucion;
                    $acuerdoDiscussion->codigo_acuerdo_compra = $acuerdo->codigo_acuerdo_compra;
                    $acuerdoDiscussion->codigo_estado = Estado::getState('active');
                    $acuerdoDiscussion->fecha_discusion = date('Y-m-d H:i:s');
                    $acuerdoDiscussion->save();

                    $discussionInstitution = new DiscusionInstitucion;
                    $discussionInstitution->codigo_discucion = $discussion->codigo_discucion;
                    $discussionInstitution->codigo_institucion = session('institution')->codigo_institucion;
                    $discussionInstitution->codigo_estado = Estado::getState('active');
                    $discussionInstitution->save();

                    // Prepare response
                    $response = DiscusionAcuerdoCompra::buildDiscussion(DiscusionAcuerdoCompra::getDataDiscussion($discussion->codigo_discucion));
                    $response->success = true;

                    // Commit Transaction
                    DB::commit();
                    return response()->json($response);
                }catch(\Exception $e){
                    DB::rollback();
                    Log::error($e->getMessage());
                    return response()->json(['success' => false, 'errors' => trans('app.exception')]);
                }
            }
            return response()->json(['success' => false, 'errors' => $discussion->errors]);
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
    public function destroy($id)
    {
        //
    }
}
