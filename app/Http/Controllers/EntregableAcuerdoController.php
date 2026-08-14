<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;

use App\Http\Requests;
use DB, Log, TuProyecto;
use App\Http\Controllers\Controller;
use App\Models\Core\EntregableAcuerdo,
    App\Models\Core\AcuerdoCompra,
    App\Models\Core\Estado,
    App\Models\Core\Compra,
    App\Models\Core\RegistroFaseUsuario,
    App\Models\Aprende\Mentoria,
    App\Models\Core\CategoriaProductoSesion;

class EntregableAcuerdoController extends Controller
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
    public function index()
    {
        //
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
		if($request->ajax()){
			$data = $request->all();

			if(!$request->has('codigo_acuerdo_compra')) {
				return response()->json(['success' => false, 'errors' => 'El código de acuerdo compra es requerido.']);
			}

            // Decrypt src-params
            list($codigo_acuerdo_compra) = TuProyecto::decryptSrcParams($request->codigo_acuerdo_compra);
            if(!$codigo_acuerdo_compra || !is_numeric($codigo_acuerdo_compra)) {
                DB::rollback();
                return response()->json(['success' => false, 'errors' => 'El código de acuerdo compra no es un número.']);
            }

            $purchaseAgreement = AcuerdoCompra::find($codigo_acuerdo_compra);
            if(!$purchaseAgreement instanceof AcuerdoCompra) {
                DB::rollback();
                return response()->json(['success' => false, 'errors' => 'El código del acuerdo compra no se encuentra en el sistema.']);
            }

			DB::beginTransaction();
            try {
				// Insert Deliverable Agreement
				$deliverableAgreement = new EntregableAcuerdo();
				$deliverableAgreement->codigo_acuerdo_compra = $purchaseAgreement->codigo_acuerdo_compra;
				$deliverableAgreement->codigo_estado = Estado::getState('active');
				$deliverableAgreement->fill($data);
				$deliverableAgreement->save();

				// update state agreement
				$purchaseAgreement->codigo_estado = Estado::getState('correction');
				$purchaseAgreement->save();

                // Commit Transaction
                DB::commit();
                return response()->json(['success' => true, 'message' => 'Se ha creado satisfactoriamente el entregable', 'codigo_estado_acuerdo' => $purchaseAgreement->codigo_estado]);
            }catch(\Exception $e){
                DB::rollback();
                Log::error(sprintf('%s:%s', 'Nueva actividad acuerdo', $e->getMessage()));
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
		if($request->ajax()){
			$data = $request->all();

			if(!$request->has('codigo_acuerdo_compra') || !is_numeric($request->codigo_acuerdo_compra)) {
				return response()->json(['success' => false, 'errors' => 'El código de acuerdo compra no es un número ']);
			}

			$purchaseAgreement = AcuerdoCompra::find($request->codigo_acuerdo_compra);
			if(!$purchaseAgreement instanceof AcuerdoCompra) {
				return response()->json(['success' => false, 'errors' => 'El código del acuerdo compra no se encuentra en el sistema ']);
			}

			$entregablecuerdo = EntregableAcuerdo::find($id);
			if(!$entregablecuerdo instanceof EntregableAcuerdo){
				return response()->json(['success' => false, 'errors' => 'El código del acuerdo compra no se encuentra en el sistema ']);
			}

			if ($entregablecuerdo->isValid($data)) {
				DB::beginTransaction();
	            try {
					$entregablecuerdo->fill($data);
					$entregablecuerdo->save();

					// update state agreement
					$purchaseAgreement->codigo_estado = Estado::getState('correction');
					$purchaseAgreement->save();

					// Commit Transaction
					DB::commit();
					return response()->json(['success' => true, 'message' => 'Entregable editado correctamente.', 'codigo_estado_acuerdo' => $purchaseAgreement->codigo_estado]);

				}catch(\Exception $e){
					DB::rollback();
					Log::error(sprintf('%s:%s', 'Edición cambio de entregable acuerdo', $e->getMessage()));
					return response()->json(['success' => false, 'errors' => trans('app.exception')]);
				}
			}
			return response()->json(['success' => false, 'errors' => $actividadacuerdo->errors]);
		}
		abort(404);
    }

	/**
     * Remove the specified resource from storage.
     *
	 * @param  \Illuminate\Http\Request  $request
     * @param  int  $id
     * @return \Illuminate\Http\Response
     */
    public function destroy(Request $request, $id)
    {
		$entregablecuerdo = EntregableAcuerdo::find($id);
		if(!$entregablecuerdo instanceof EntregableAcuerdo){
			return response()->json(['success' => false, 'errors' => 'El código del entregable no se encuentra.']);
		}

		$purchaseAgreement = AcuerdoCompra::find($entregablecuerdo->codigo_acuerdo_compra);
		if(!$purchaseAgreement instanceof AcuerdoCompra) {
			return response()->json(['success' => false, 'errors' => 'El código del acuerdo compra no se encuentra.']);
		}

		DB::beginTransaction();
        try {

			// Update state entregable
			$entregablecuerdo->codigo_estado = Estado::getState('removed');
			$entregablecuerdo->save();

			// Update state agreement
			$purchaseAgreement->codigo_estado = Estado::getState('correction');
			$purchaseAgreement->save();

			// Commit Transaction
			DB::commit();
			return response()->json(['success' => true, 'message' => 'Entregable eliminado satisfactoriamente.', 'codigo_estado_acuerdo' => $purchaseAgreement->codigo_estado]);

		}catch(\Exception $e){
			DB::rollback();
			Log::error(sprintf('%s:%s', 'Elimiando item entregable acuerdo', $e->getMessage()));
			return response()->json(['success' => false, 'errors' => trans('app.exception')]);
		}
    }


	/**
	 * Update the specified resource in storage.
	 * aprobar entregables, Rutan
	 * @param  \Illuminate\Http\Request  $request
	 * @param  int  $id
	 * @return \Illuminate\Http\Response
	 */
	public function updateDeliverables(Request $request)
    {
        if ($request->ajax()) {
            DB::beginTransaction();
            try {
                /**
                 * cambiamos el estado del entregable para indiar que fue aprobado
                 */
                if (isset($request->entregables)) {

                    foreach ($request->entregables as $entregable) {

                        $acuerdo_entregable = EntregableAcuerdo::find($entregable);
                        $acuerdo_entregable->codigo_estado =  Estado::getState('confirmed');
                        $acuerdo_entregable->save();
                    }
                }


                //permite llevar fecha creacion entregable, aceptación/aprobacion entregables
			    $purchaseAgreement = AcuerdoCompra::find($request->codigo_acuerdo_compra);
                if (!$purchaseAgreement instanceof AcuerdoCompra) {
                    DB::rollback();
                    return response()->json(['success' => false, 'errors' => 'El código del entregable no se encuentra']);

                }

                $purchaseAgreement->codigo_estado = Estado::getState('confirmed');
                $purchaseAgreement->fecha_acuerdo_aprobacion_compra = date('Y-m-d');
                $purchaseAgreement->seg_codigo_usuario = \Auth::user()->codigo_usuario;
                $purchaseAgreement->save();

                $message = 'Entregables aprobados correctamente';

                $mentoria = $this->getMentorConvocatory($purchaseAgreement->codigo_compra);
                $mentoria = Mentoria::find($mentoria->codigo_mentoria);
                $nombre_mentor = $mentoria->mentor->nombre_usuario." ".$mentoria->mentor->persona->primer_apellido_persona;

                CategoriaProductoSesion::where('codigo_mentoria', $mentoria->codigo_mentoria)
                                        ->update(['codigo_estado' => 9]);
                // Send mail to mentor
                $data = [];
                $data['_template'] = 'emails.marketplace.aprobacionentregables';
                $data['_subject'] = 'Aprobación de entregables';
                $data['_to'] = $mentoria->mentor->correo_electronico;
                $data['_to_name'] = ucwords(strtolower($nombre_mentor));
                $data['_inst'] = session('institution')->codigo_institucion;
                $data['_admin'] = \Auth::user()->nombre_usuario." ". \Auth::user()->persona->primer_apellido_persona;
                $data['_codigo_compra'] = $purchaseAgreement->codigo_compra;
                \SendEmail::notification($data);

                // Commit Transaction
                DB::commit();
                return response()->json(['success' => true, 'message' => $message, 'codigo_estado' => $purchaseAgreement->codigo_estado]);
            }catch(\Exception $e){
                DB::rollback();
                Log::error(sprintf('%s:%s', 'EntregableAcuerdoController:updateDeliverables', $e->getMessage()));
                return response()->json(['success' => false, 'errors' => trans('app.exception')]);
            }
        }
        abort(404);
    }

    private function getMentorConvocatory(int $codigo_compra)
    {
        $mentor = RegistroFaseUsuario::query();
        $mentor->select('mentoria.*');
        $mentor->join('core.fase', 'fase.codigo_fase', 'registro_fase_usuario.codigo_fase');
        $mentor->join('core.entidad', 'entidad.codigo_usuario', '=', 'registro_fase_usuario.codigo_usuario');
        $mentor->join('core.categoria_producto_sesion as cps', function($join) {
            $join->on('cps.codigo_registro_fase_usuario', '=', 'registro_fase_usuario.codigo_registro_fase_usuario')
                ->where('cps.codigo_estado', '=', 8);
        });
        $mentor->join('core.categoria_producto_insti as cpi', function ($join) {
            $join->on('cpi.codigo_categoria_producto', '=', 'cps.codigo_categoria_producto')
                ->where('cpi.codigo_institucion', '=', 61)
                ->where('cpi.codigo_estado', '=', 8);
        });
        $mentor->join('core.convocatoria_mentoria as cm', 'cm.codigo_fase', '=', 'fase.codigo_fase');

        $mentor->join('aprende.mentoria', function ($join) {
            $join->on('mentoria.codigo_mentoria', '=', 'cps.codigo_mentoria')
                ->where('mentoria.codigo_asesoria', '=', DB::raw('cm.codigo_asesoria::INTEGER'));
        });
        $mentor->join('core.compra', 'compra.codigo_usuario', '=', 'entidad.seg_codigo_usuario');

        $mentor->where('compra.codigo_compra', '=', $codigo_compra);

        return $mentor = $mentor->first();
    }

}
