<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;

use App\Http\Requests;
use App\Http\Controllers\Controller;

use TuProyecto;

use DB, Log;

use App\Models\Core\RegistroFaseUsuario, App\Models\Core\Adjunto, App\Models\Core\TipoAdjunto, App\Models\Core\AdjuntoRegistroFaseUsuario, App\Models\Core\Estado;

class AdjuntoDocumentadorController extends Controller
{
    /**
     * Instantiate a new Controller instance.
     */
    public function __construct()
    {
        $this->middleware('ability:admin|super-admin|administrativo,view,convocatoria/fase/documentador');
        $this->middleware('ability:admin|super-admin|administrativo,edit,convocatoria/fase/documentador', ['only' => ['edit', 'update']]);
    }

    /**
     * Display a listing of the resource.
     *
     * @return \Illuminate\Http\Response
     */
    public function index()
    {
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
		if ($request->ajax())
		{
			$register = RegistroFaseUsuario::find($request->codigo_registro_fase_usuario);
			if(!$register instanceof RegistroFaseUsuario) {
				abort(404);
			}

			DB::beginTransaction();
			try {
				if ($request->hasFile('file'))
				{
					$file = $request->file('file');
					$result = Adjunto::upload($file->getRealPath(), $file->getClientOriginalName(), 'document', '');

					if(is_object($result) && $result->success === true) {
						// Adjunto
						$attached = new Adjunto();
						$attached->codigo_tipo_adjunto = TipoAdjunto::getTypeAttached('document');
						$attached->direccion_adjunto = $result->file_path;
						$attached->nombre_adjunto = $file->getClientOriginalName();
						$attached->save();

						// Adjunto registro fase
						$attachedrfu = AdjuntoRegistroFaseUsuario::where('codigo_registro_fase_usuario', $register->codigo_registro_fase_usuario)->first();
						if(!$attachedrfu instanceof AdjuntoRegistroFaseUsuario) {
							$attachedrfu = new AdjuntoRegistroFaseUsuario;
							$attachedrfu->codigo_estado = Estado::getState('active');
							$attachedrfu->codigo_registro_fase_usuario = $register->codigo_registro_fase_usuario;
							$attachedrfu->fecha_adjunto_rfu = date('Y-m-d');
						}

						$attachedrfu->codigo_adjunto = $attached->codigo_adjunto;
						$attachedrfu->save();

						// Prepare response
						$response = new \stdClass();
						$response->uuid = TuProyecto::encryptSrcParams([$attached->codigo_adjunto]);
						$response->codigo_tipo_adjunto = $attached->codigo_tipo_adjunto;
						$response->direccion_adjunto = $attached->direccion_adjunto;
						$response->name = $attached->nombre_adjunto;
						$response->success = true;

						// Commit Transaction
						DB::commit();
						return response()->json($response);
					}
				}
				DB::rollback();
				return response()->json(['success' => false, 'errors' => trans('app.exception')]);
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
     * @param  int  $id
     * @return \Illuminate\Http\Response
     */
    public function destroy($id)
    {
        //
    }
}
