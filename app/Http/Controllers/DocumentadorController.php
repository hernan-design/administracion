<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;

use App\Http\Requests;
use App\Http\Controllers\Controller;

use TuProyecto;
use DB, Lang, App, Log, View;
use App\Models\Core\RegistroFaseUsuario, App\Models\Core\Estado, App\Models\Core\ComentDefDocument, App\Models\Core\CategoriaProducto, App\Models\Core\RecomProductoEval, App\Models\Core\ResultadoEvaluation, App\Models\Core\GrupoItemEval, App\Models\Core\ComenDocumentador;

class DocumentadorController extends Controller
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
        $this->middleware('ability:admin|super-admin|administrativo,export,convocatoria/fase/documentador', ['only' => ['exportar']]);
    }

	/**
	 * Display a listing of the resource.
	 *
	 * @return \Illuminate\Http\Response
	 */
	public function index()
	{
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
	public function show(Request $request, $id)
	{
		$register = RegistroFaseUsuario::getRegisterPhase($id);
		if(!$register instanceof RegistroFaseUsuario) {
			abort(404);
		}

		if ($request->ajax()) {
			$response = ResultadoEvaluation::buildResultEvaluation($register);
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
			DB::beginTransaction();
			try {

				$registerPhase = RegistroFaseUsuario::find($request->codigo_registro_fase_usuario);
				if(!$registerPhase instanceof RegistroFaseUsuario) {
					abort(404);
				}

				$registerPhase->observaciones_registro_fase_usuario = $request->observaciones_registro_fase_usuario;
				if($request->comentario_general_aldea_20 != 0) {
					$comentay_general = ComentDefDocument::where('codigo_registro_fase_usuario', $request->codigo_registro_fase_usuario)
					->where('tipo_coment_def_doc', ComentDefDocument::getTypeComment('general'))
					->first();

					if($comentay_general instanceof ComentDefDocument) {
						$comentay_general->codigo_coment_eval_emprendi = $request->comentario_general_aldea_20;
						$comentay_general->save();
						$codigo_comentario_general = $comentay_general->codigo_coment_def_document;
					} else {
						$new_comentay_general = new ComentDefDocument();
						$new_comentay_general->codigo_estado = Estado::getState('active');
						$new_comentay_general->codigo_registro_fase_usuario = $request->codigo_registro_fase_usuario;
						$new_comentay_general->codigo_coment_eval_emprendi = $request->comentario_general_aldea_20;
						$new_comentay_general->tipo_coment_def_doc = ComentDefDocument::getTypeComment('general');
						$new_comentay_general->save();
						$codigo_comentario_general = $new_comentay_general->codigo_coment_def_document;
					}

					$registerPhase->codigo_coment_def_document = $codigo_comentario_general;
				}

				if ($request->comentario_secundario_aldea_20 != 0) {
					$comentay_secondary = ComentDefDocument::where('codigo_registro_fase_usuario', $request->codigo_registro_fase_usuario)
					->where('tipo_coment_def_doc', ComentDefDocument::getTypeComment('secondary'))
					->first();

					if($comentay_secondary instanceof ComentDefDocument) {
						$comentay_secondary->codigo_coment_eval_emprendi = $request->comentario_secundario_aldea_20;
						$comentay_secondary->save();
						$codigo_comentario_secundario = $comentay_secondary->codigo_coment_def_document;
					} else {
						$new_comentay_secondary = new ComentDefDocument();
						$new_comentay_secondary->codigo_estado = Estado::getState('active');
						$new_comentay_secondary->codigo_registro_fase_usuario = $request->codigo_registro_fase_usuario;
						$new_comentay_secondary->codigo_coment_eval_emprendi = $request->comentario_secundario_aldea_20;
						$new_comentay_secondary->tipo_coment_def_doc = ComentDefDocument::getTypeComment('secondary');
						$new_comentay_secondary->save();
						$codigo_comentario_secundario = $new_comentay_secondary->codigo_coment_def_document;
					}
				}

				$registerPhase->save();

				$groups = GrupoItemEval::select('nombre_grupo_item_eval', 'codigo_grupo_item_eval')->get();

				foreach ($groups as $group) {
					if($request->has("texto_comentario_documentador_$group->codigo_grupo_item_eval")) {

						$comentDoc = ComenDocumentador::where('codigo_registro_fase_usuario', $registerPhase->codigo_registro_fase_usuario)->where('codigo_grupo_item_eval',$group->codigo_grupo_item_eval)->first();

						if($comentDoc instanceof ComenDocumentador) {
							// Update Coment Document
							ComenDocumentador::where('codigo_registro_fase_usuario', $registerPhase->codigo_registro_fase_usuario)->where('codigo_grupo_item_eval', $group->codigo_grupo_item_eval)
							 ->update(['texto_comentario_documentador' => $request->input('texto_comentario_documentador_'.$group->codigo_grupo_item_eval), 'fecha_comentario_documentador' => date('Y-m-d')]);
						}else{
							// Coment Documenter
							$comentDoc = new ComenDocumentador();
							$comentDoc->codigo_registro_fase_usuario = $registerPhase->codigo_registro_fase_usuario;
							$comentDoc->codigo_grupo_item_eval = $group->codigo_grupo_item_eval;
							$comentDoc->codigo_estado = Estado::getState('active');
							$comentDoc->fecha_comentario_documentador = date('Y-m-d');
							$comentDoc->texto_comentario_documentador = $request->input('texto_comentario_documentador_'.$group->codigo_grupo_item_eval);
							$comentDoc->save();
						}

					}
				}

				$items = CategoriaProducto::getCategories();
				RecomProductoEval::where('codigo_registro_fase_usuario', $registerPhase->codigo_registro_fase_usuario)->delete();
				foreach ($items as $item) {
					if($request->has("codigo_categoria_producto_$item->codigo_categoria_producto")) {
					$cat_product = CategoriaProducto::getCategorieForFather($item->codigo_categoria_producto);
						foreach ($cat_product as $cat_item) {

							// Recom Product Eval
							$recom = new RecomProductoEval;
							$recom->codigo_registro_fase_usuario = $registerPhase->codigo_registro_fase_usuario;
							$recom->codigo_estado = Estado::getState('active');
							$recom->codigo_categoria_producto = $cat_item->categoria_producto;
							$recom->save();
						}
					}
				}

				// Prepare response
				$response = ResultadoEvaluation::buildResultEvaluation(RegistroFaseUsuario::getRegisterPhase($registerPhase->codigo_registro_fase_usuario));
				$response->success = true;
                $response->message = 'Documentador actualizado con éxito.';

				// Commit Transaction
				DB::commit();
				return response()->json($response);

			}catch(\Exception $e){
				DB::rollback();
				Log::error(sprintf('%s:%s', 'Actualizacion Documentador', $e->getMessage()));
				return response()->json(['success' => false, 'errors' => 'Ocurrió un error al momento de al actualizar la observación']);
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

	/**
	 * Export pdf the specified resource.
	 *
	 * @param  int  $id
	 * @return \Illuminate\Http\Response
	 */
	public function exportar($id)
	{
		$register = RegistroFaseUsuario::getRegisterPhase($id);
		if(!$register instanceof RegistroFaseUsuario) {
			abort(404);
		}

		$dataEval = ResultadoEvaluation::buildResultEvaluation($register);
		$data = [];
		$data['data'] = $dataEval;
		$data['categories'] = CategoriaProducto::getCategoriesSelected($id);
		$data['title'] = $dataEval->nombre_usuario;
		$data['recommendation'] = ResultadoEvaluation::getResultEvaluations($register);

		// Export pdf
		$pdf = App::make('dompdf.wrapper');
		$pdf->loadHTML(view('reports.program.innpulsa.registrofase', $data)->render());

		return $pdf->stream(sprintf('%s.pdf', 'documentador'));
	}
}
