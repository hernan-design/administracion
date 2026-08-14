<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;

use App\Http\Requests;
use App\Http\Controllers\Controller;

use TuProyecto;

use DB, Lang, App, Log, View, Auth, Input, SendEmail;

use App\Models\Seguridad\Usuario, App\Models\Core\RegistroFaseUsuario, App\Models\Core\Fase, App\Models\Core\Estado, App\Models\Core\CartaCompromisoCompra, App\Models\Core\CartaInstitucionDocumento, App\Models\Param\Institucion, Custom\Innpulsa;

class CartaCompromisoController extends Controller
{
    /**
     * Display a listing of the resource.
     *
     * @return \Illuminate\Http\Response
     */
    public function index(Request $request)
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
        //
    }

    /**
     * Display the specified resource.
     *
     * @param  int  $id
     * @return \Illuminate\Http\Response
     */
    public function show(Request $request, $id)
    {
        // Decrypt src-params
        list($codigo_registro_fase_usuario) = TuProyecto::decryptSrcParams($id);
        if(!$codigo_registro_fase_usuario || !is_numeric($codigo_registro_fase_usuario)) {
            abort(403);
        }

        $register = CartaCompromisoCompra::getRegisterCommitment($codigo_registro_fase_usuario);
        if(!$register instanceof RegistroFaseUsuario) {
            abort(404);
        }

        if($request->ajax()) {
            $response = CartaCompromisoCompra::buildCommitmentPurchase($register);
            return response()->json($response);
        }
        return view('program.detail');
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
        if ($request->ajax()) {
            // Decrypt src-params
            list($codigo_registro_fase_usuario) = TuProyecto::decryptSrcParams($request->codigo_registro_fase_usuario);
            if(!$codigo_registro_fase_usuario || !is_numeric($codigo_registro_fase_usuario)) {
                abort(403);
            }

            $registerPhase = RegistroFaseUsuario::find($codigo_registro_fase_usuario);
            if(!$registerPhase instanceof RegistroFaseUsuario) {
                abort(404);
            }

			$user = Usuario::getUserByEntity($registerPhase->codigo_usuario);
			if(!$user instanceof Usuario) {
				abort(404);
			}

            DB::beginTransaction();
            try {
                // Valid documents
                $documents = CartaCompromisoCompra::getDocuments($registerPhase->codigo_usuario);
            	if($request->has('action') && $request->action == 'close') {
            		foreach ($documents as $document) {
			            if(!$request->has("document_{$document->codigo_documento}")) {
				            DB::rollback();
		                	return response()->json(['success' => false, 'errors' => 'Para cerrar la carta de compromiso es necesaria la aprobación de todos los documentos en la lista.']);
	                	}
            		}
            	}

            	// Carta compromiso
                $commitmentPurchase = CartaCompromisoCompra::where('codigo_registro_fase_usuario', $registerPhase->codigo_registro_fase_usuario)->first();
                if(!$commitmentPurchase instanceof CartaCompromisoCompra) {
	                $commitmentPurchase = new CartaCompromisoCompra();
                }
                $commitmentPurchase->codigo_registro_fase_usuario = $registerPhase->codigo_registro_fase_usuario;
                $commitmentPurchase->codigo_usuario = Auth::user()->codigo_usuario;
                $commitmentPurchase->codigo_estado = ($request->has('action') && $request->action == 'close') ? Estado::getState('closed') : Estado::getState('active');
                $commitmentPurchase->fecha_carta_compormiso_compra = date('Y-m-d');
                $commitmentPurchase->nombre_empresa = $request->nombre_empresa;
                $commitmentPurchase->nombre_representante_legal = $request->nombre_representante_legal;
                $commitmentPurchase->apellidos_representante_legal = $request->apellidos_representante_legal;
                $commitmentPurchase->identificacion_rep_legal = $request->identificacion_rep_legal;
                $commitmentPurchase->codigo_ciudad = $request->ciudad_expedicion_id_rep_legal;
                $commitmentPurchase->cor_codigo_ciudad = $request->ciudad_empresa;
                $commitmentPurchase->nit_empresa = $request->nit_empresa;
                $commitmentPurchase->fecha_cierre_carta_compormiso_compra = date('Y-m-d');
                $commitmentPurchase->save();

                // Documents
                $documents = CartaCompromisoCompra::getDocuments($registerPhase->codigo_usuario);
                foreach ($documents as $document)
                {
                	$attachedletter = CartaInstitucionDocumento::where('codigo_documento', $document->codigo_documento)->where('codigo_carta_compormiso_compra', $commitmentPurchase->codigo_carta_compormiso_compra)->where('codigo_institucion', session('institution')->codigo_institucion)->first();
                	if(!$attachedletter instanceof CartaInstitucionDocumento){
                		$attachedletter = new CartaInstitucionDocumento;
						$attachedletter->codigo_carta_compormiso_compra = $commitmentPurchase->codigo_carta_compormiso_compra;
						$attachedletter->codigo_institucion = session('institution')->codigo_institucion;
						$attachedletter->codigo_documento = $document->codigo_documento;

                	}

					$attachedletter->codigo_estado = $request->has("document_{$document->codigo_documento}") ? Estado::getState('active') : Estado::getState('inactive');
                	$attachedletter->save();
                }

                // Prepare response
                $response = CartaCompromisoCompra::buildCommitmentPurchase(CartaCompromisoCompra::getRegisterCommitment($registerPhase->codigo_registro_fase_usuario));
                $response->success = true;
                $response->message = 'Carta de compromiso actualizada con exito.';

                // Notify
                if(in_array($commitmentPurchase->codigo_estado, [Estado::getState('closed')]))
                {
                    switch ($registerPhase->codigo_fase) {
                        case Fase::getIdPhase('three_challenge'):
                            Log::info('three_challenge');
                            $data = [];
                            $data['_template'] = 'emails.program.innpulsa.cartacompromiso';
                            $data['_inst'] = session('institution')->codigo_institucion;
                            $data['_subject'] = 'Bienvenido al Reto 3 de Aldea ¡Conéctate con tu asesor! - ALDEA';
                            $data['_to'] = $user->correo_electronico;
                            $data['_to_name'] = ucwords(strtolower($user->user_name));
                            $data['_url_action'] = '/marketplace';
                            SendEmail::notification($data);

                            Innpulsa::notificationLetterEngagement($registerPhase->codigo_registro_fase_usuario, $user);
                        break;
                        case Fase::getIdPhase('empodera__2020_intermediate__phase'):
                            $data = [];
                            $data['_template'] = 'emails.program.innpulsa.cartacompromiso_empodera';
                            $data['_inst'] = session('institution')->codigo_institucion;
                            $data['_subject'] = 'Finaliza tu vinculación a Empodera';
                            $data['_to'] = $user->correo_electronico;
                            $data['_to_name'] = ucwords(strtolower($user->user_name));
                            $data['_url_action'] = '/comunidad';
                            SendEmail::notification($data);

                            $registerPhase->codigo_estado = Estado::getState('no_linked');
                            $registerPhase->save();
                        break;
                        case Fase::getIdPhase('aldea__20_three__challenge'):
                            //Se apaga correo de aceptación reto 3.0 aldea 2.0
                            Log::info('Se apaga correo emails.program.innpulsa.cartacompromiso');
                            /* $data = [];
                            $data['_template'] = 'emails.program.innpulsa.cartacompromiso';
                            $data['_inst'] = session('institution')->codigo_institucion;
                            $data['_subject'] = 'Bienvenido al Reto 3 de Aldea ¡Conéctate con tu asesor! - ALDEA';
                            $data['_to'] = $user->correo_electronico;
                            $data['_to_name'] = ucwords(strtolower($user->user_name));
                            $data['_url_action'] = '/marketplace';
                            SendEmail::notification($data); */

                            Innpulsa::notificationLetterEngagement($registerPhase->codigo_registro_fase_usuario, $user);

                            // $registerPhase->codigo_estado = Estado::getState('linked');
                            $registerPhase->save();
                        break;
                    }

					// Other actions institution
					switch (session('institution')->codigo_institucion) {
						case Institucion::institution('innpulsa'):
							// Innpulsa::notificationLetterEngagement($registerPhase->codigo_registro_fase_usuario, $user);
						break;
					}
                }

                // Commit Transaction
                DB::commit();
                return response()->json($response);

            }catch(\Exception $e){
                DB::rollback();
                Log::error(sprintf('%s:%s', 'CartaCompromisoController:update', $e->getMessage()));
                return response()->json(['success' => false, 'errors' => 'Ocurrio un error al momento de al actualizar la carta compromiso compra']);
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
    public function destroy($id)
    {
        //
    }
}
