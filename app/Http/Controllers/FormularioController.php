<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;

use App\Http\Requests;
use App\Http\Controllers\Controller;

use Datatables, Log, DB, Auth;
use App\Models\Core\Formulario, App\Models\Core\EncuestaInstitucion, App\Models\Core\Estado, App\Models\Core\TipoFormulario, App\Models\Core\OrientacionFormulario, App\Models\Core\PrivacidadContenido, App\Models\Core\VersionFormulario, App\Models\Core\FormularioInstitucion, App\Models\Seguridad\Usuario;

class FormularioController extends Controller
{
    /**
     * Display a listing of the resource.
     *
     * @return \Illuminate\Http\Response
     */
    public function index(Request $request)
    {
        if($request->ajax()){
          if($request->has('id') && !empty($request->id)){
            return response()->json( Formulario::select('codigo_encuesta as id', 'nombre_encuesta as text')->where('codigo_encuesta', $request->id)->get() );
          }

          if($request->has('data')){
            return response()->json( Formulario::getForms( $request->all() ) );
          }

          return Datatables::of( Formulario::getFormsAlls( $request->all() ) )->make(true);
        }

		    $states = Estado::getStates([], [Estado::getState('active'), Estado::getState('inactive')], $filter = null);
        return view('form.index', compact('states'));
    }

    /**
     * Show the form for creating a new resource.
     *
     * @return \Illuminate\Http\Response
     */
    public function create()
    {
		$paginationType = Formulario::$pagination;
		$formType = TipoFormulario::getSurveyType();
		$privacyContent = PrivacidadContenido::getContentPrivacy();
		$formOrientation = OrientacionFormulario::getSurveyOrientation();
        return view('form.create-edit', compact('formType', 'formOrientation', 'privacyContent', 'paginationType'));
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

			$form = new Formulario;
			if ($form->isValid($data)) {
				DB::beginTransaction();
      try {
          $form->fill($data);
          $form->codigo_estado = Estado::getState('active');
          $form->codigo_usuario = Auth::user()->codigo_usuario;
          $form->fecha_encuesta = date('Y-m-d');
          $form->hora_encuesta = date('H:m:s');
          $form->codigo_institucion = session('institution')->codigo_institucion;
          $form->url_encuesta = slugtp($request->nombre_encuesta, $form->getTable(), 'url_encuesta');
          $form->editar_encuesta = $request->editar_encuesta ? TRUE : FALSE;
          $form->save();

					$formVersion = new VersionFormulario;
					$formVersion->codigo_encuesta = $form->codigo_encuesta;
					$formVersion->codigo_estado = Estado::getState('active');
					$formVersion->fecha_version_encuesta = date('Y-m-d');
                    $formVersion->comentario_version_encuesta = 'Default';
                    $formVersion->version_encuesta_editable = TRUE;
					$formVersion->save();

					$form->codigo_version_encuesta = $formVersion->codigo_version_encuesta;
					$form->save();

					$institutionForm = new FormularioInstitucion;
					$institutionForm->codigo_institucion = session('institution')->codigo_institucion;
					$institutionForm->codigo_encuesta = $form->codigo_encuesta;
					$institutionForm->codigo_estado = Estado::getState('active');
					$institutionForm->fecha_encuesta_institucion = date('Y-m-d H:m:s');
					$institutionForm->save();

					// Commit Transaction
                    DB::commit();
                    return response()->json(['success' => true, 'message' => 'El formulario ha sido creado correctamente.', 'codigo_encuesta' => $form->codigo_encuesta, 'codigo_version_encuesta' => $form->codigo_version_encuesta, 'redirect' => true]);

				}catch(\Exception $e){
	                DB::rollback();
	                Log::error(sprintf('%s:%s', 'FormularioController:store', $e->getMessage()));
	                return response()->json(['success' => false, 'errors' => trans('app.exception')]);
	            }
			}
			return response()->json(['success' => false, 'errors' => $form->errors]);
		}
		abort(404);
    }

    /**
     * Display the specified resource.
     *
     * @param  int  $id
     * @return \Illuminate\Http\Response
     */
    public function show(Request $request, $id)
    {
		if(!is_numeric($id)){
			abort(404);
		}

		$form = Formulario::getForm($id);
		if(!$form instanceof Formulario) {
			abort(404);
		}

		$form = Formulario::buildForm($form);

		if ($request->ajax()) {
			return response()->json($form);
		}

		return view('form.detail');
    }

    /**
     * Show the form for editing the specified resource.
     *
     * @param  int  $id
     * @return \Illuminate\Http\Response
     */
    public function edit($id)
    {
		if(!is_numeric($id)) {
            abort(404);
        }

		$form = Formulario::getForm($id);
		if(!$form instanceof Formulario){
			abort(404);
		}

		$paginationType = Formulario::$pagination;
		$formType = TipoFormulario::getSurveyType();
		$privacyContent = PrivacidadContenido::getContentPrivacy();
		$formOrientation = OrientacionFormulario::getSurveyOrientation();
        return view('form.create-edit', compact('form', 'formType', 'formOrientation', 'privacyContent', 'paginationType'));
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

			$form = Formulario::find($id);
			if(!$form instanceof Formulario) {
				return response()->json(['success' => false, 'errors' => 'El código del formulario no se encuentra en el sistema ']);
			}

			DB::beginTransaction();
			try {
				if($request->has('change_version') && !empty($request->change_version)){
					$form->codigo_version_encuesta = $request->codigo_version_encuesta;
					$form->save();

					$form = Formulario::buildForm($form);

					$message = 'Se ha cambiado la versión del formulario correctamente.';
				}else{
          if($request->has('change_state') && !empty($request->change_state)){
            $form->codigo_estado = $form->codigo_estado == Estado::getState('active') ? Estado::getState('inactive') : Estado::getState('active');
            EncuestaInstitucion::where('codigo_institucion', session('institution')->codigo_institucion)
						->where('codigo_encuesta', $form->codigo_encuesta)
						->update([
							'codigo_estado' => $form->codigo_estado
						]);
          }else {
            $form->fill($data);
            $form->editar_encuesta = $request->editar_encuesta ? TRUE : FALSE;
          }

					$form->save();

					$message = 'El formulario ha sido editado correctamente.';
				}

				$response = $form;
				$response->success = true;
				$response->message = $message;

                // Commit Transaction
                DB::commit();
                return response()->json($response);

			}catch(\Exception $e){
				DB::rollback();
				Log::error(sprintf('%s:%s', 'FormularioController:update', $e->getMessage()));
				return response()->json(['success' => false, 'errors' => trans('app.exception')]);
			}
		}
		abort(404);
    }

    /**
	 * Export pdf the specified resource.
	 *
	 * @param  int  $id
	 * @return \Illuminate\Http\Response
	 */
	public function exportar(Request $request)
	{
    $data = $request->all();

		if($request->has('evaluation') && !empty($request->evaluation))
		{
			$user = Usuario::find(Auth::user()->codigo_usuario);
			if(!$user instanceof Usuario) {
				throw new \Exception("Error recuperando usuario en sesión.");
			}

		}
		abort(500);
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
