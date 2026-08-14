<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;

use App\Http\Requests;
use App\Http\Controllers\Controller;

use TuProyecto;

use App\Models\Core\ResultadoEvaluation, App\Models\Core\Convocatoria, App\Models\Core\RegistroFaseUsuario, App\Models\Core\DetalleVoucher;

class ResultadoEvaluacionController extends Controller
{
	/**
     * Instantiate a new Controller instance.
     */
    public function __construct()
    {
        $this->middleware('ability:admin|super-admin|administrativo,view,convocatoria/fase/documentador');
        $this->middleware('ability:admin|super-admin|administrativo,create,convocatoria/fase/documentador', ['only' => ['create', 'store']]);
        $this->middleware('ability:admin|super-admin|administrativo,edit,convocatoria/fase/documentador', ['only' => ['edit', 'update']]);
        $this->middleware('ability:admin|super-admin|administrativo,remove,convocatoria/fase/documentador', ['only' => ['destroy']]);
    }

    /**
     * Display a listing of the resource.
     *
     * @return \Illuminate\Http\Response
     */
    public function index(Request $request)
    {
        if($request->ajax()) {
            $register = RegistroFaseUsuario::find($request->register);
            if(!$register instanceof RegistroFaseUsuario) {
                abort(404);
            }

            if($request->has('codigo_convocatoria')) {
                switch ($request->codigo_convocatoria) {
                    case Convocatoria::getProgram('aldea__20'):
                        return response()->json(ResultadoEvaluation::buildResultEvaluationProgram($register));
                    break;
                }
            } else {
                return response()->json(ResultadoEvaluation::getResultEvaluations($register));
            }

        }
        return view('program.detail');
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
        //
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
