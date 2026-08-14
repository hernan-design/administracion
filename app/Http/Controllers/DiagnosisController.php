<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;

use App\Models\Core\UsuarioEncuesta,
    App\Models\Core\Encuesta;

use App\Custom\Siinova;

use App, DB, TuProyecto,Redirect;

class DiagnosisController extends Controller
{
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

    /**
     * Export diagnosis in radar diagram to pdf
     *
     * @param  int  $id
     * @return \Illuminate\Http\Response
     */

    public function exportRadarDiagram($id)
	{
        // Decrypt src-params
		list($id_user_survey) = TuProyecto::decryptSrcParams($id);
		if(!$id_user_survey || !is_numeric($id_user_survey)) {
			abort(403);
        }
		$user_survey = UsuarioEncuesta::find($id_user_survey);
		if(!$user_survey instanceof UsuarioEncuesta){
            abort(404);
		}

        $survey = Encuesta::find($user_survey->codigo_encuesta);
        if(!$survey instanceof Encuesta){
            abort(404);
        }

        $slug = session('institution')->slug_institucion;

        switch ($slug) {
            case 'siinova':
                $data_pdf = Siinova::buildAnswersPdf($user_survey);
                break;
        }

        // Prepare PDF
		$pdf = App::make('dompdf.wrapper');

        if ( !is_bool($data_pdf) ) {

            switch ($slug) {
                case 'siinova':
                    $pdf->loadHTML(view('reports.diagnosis.siinovapdf.layout_1', $data_pdf)->render());
                    break;
            }

            return $pdf->stream(sprintf('%s_%s.pdf', 'diagnosis', date('YmdHms')));
        }else {
            return Redirect::back()->withErrors(['msg' => '¡Lo sentimos! Usuario pendiente de diagnóstico.']);
        }

    }
}

