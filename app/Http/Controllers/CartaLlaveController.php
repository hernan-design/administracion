<?php

namespace App\Http\Controllers;

use TuProyecto, App;
use Custom\Innpulsa;
use App\Models\Core\RegistroFaseUsuario, App\Models\Core\ComentDefDocument, App\Models\Core\Fase, App\Models\Core\PuntajeRegistroFase, App\Models\Core\AsignacionJurado, App\Models\Core\CategoriaProducto;
use Illuminate\Http\Request;

class CartaLlaveController extends Controller
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
        list($codigo_registro_fase_usuario) = TuProyecto::decryptSrcParams($id);
        $register = RegistroFaseUsuario::select('codigo_registro_convocatoria', 'valor_asignacion_rfu')
            ->where('codigo_registro_fase_usuario', $codigo_registro_fase_usuario)->first();

        if(!$register instanceof RegistroFaseUsuario) {
            abort(404);
        }

        $registerPhase__Challenge_2 = RegistroFaseUsuario::select('codigo_registro_fase_usuario', 'valor_asignacion_rfu')
            ->where('codigo_fase', Fase::getIdPhase('aldea__20_second__challenge'))
            ->where('codigo_registro_convocatoria', $register->codigo_registro_convocatoria)->first();

        if(!$registerPhase__Challenge_2 instanceof RegistroFaseUsuario) {
            abort(404);
        }

        $score__Challenge_2 = PuntajeRegistroFase::select('valor_puntaje_registro_fase')
            ->where('codigo_registro_fase_usuario', $registerPhase__Challenge_2->codigo_registro_fase_usuario)->first();

        if(!$score__Challenge_2 instanceof PuntajeRegistroFase) {
            abort(404);
        }

        $dataDocument = Innpulsa::getDataKeysAccessAprove($codigo_registro_fase_usuario, $register->codigo_registro_convocatoria);
        $comments = ComentDefDocument::select('texto_coment_eval_emprendi')
            ->join('core.coment_eval_emprendi', 'coment_eval_emprendi.codigo_coment_eval_emprendi', '=', 'coment_def_document.codigo_coment_eval_emprendi')
            ->where('codigo_registro_fase_usuario', $codigo_registro_fase_usuario)
            ->get();
		$pdf = App::make('dompdf.wrapper');

        // Validate Keys
        if($registerPhase__Challenge_2->valor_asignacion_rfu > 0) {
            $recomendations = AsignacionJurado::select('resultado_evaluacion_emprendimiento.comentario_resultado_evaluacion')
                ->join('core.resultado_evaluacion_emprendimiento', 'resultado_evaluacion_emprendimiento.codigo_asignacion_jurado', '=','asignacion_jurado.codigo_asignacion_jurado')
                ->where('asignacion_jurado.codigo_registro_fase_usuario', $registerPhase__Challenge_2->codigo_registro_fase_usuario)->get();

            $categories = CategoriaProducto::getCategories($registerPhase__Challenge_2->codigo_registro_fase_usuario);

            $data = [];
            $data['recomendations'] = $recomendations;
            $data['_comments'] = $comments;
            $data['categories'] = $categories;
            $data['data'] = $dataDocument;
            $data['score__challenge_Two'] = $score__Challenge_2->valor_puntaje_registro_fase;
            $data['keys'] = money_format("%i", $registerPhase__Challenge_2->valor_asignacion_rfu);
            $pdf->loadHTML(view('reports.program.innpulsa.letter_keys', $data)->render());
        } else {
            $recomendations = AsignacionJurado::select('resultado_evaluacion_emprendimiento.comentario_resultado_evaluacion')
                ->join('core.resultado_evaluacion_emprendimiento', 'resultado_evaluacion_emprendimiento.codigo_asignacion_jurado', '=','asignacion_jurado.codigo_asignacion_jurado')
                ->where('asignacion_jurado.codigo_registro_fase_usuario', $registerPhase__Challenge_2->codigo_registro_fase_usuario)->get();

            $data = [];
            $data['recomendations'] = $recomendations;
            $data['_comments'] = $comments;
            $data['data'] = $dataDocument;
            $data['score__challenge_Two'] = $score__Challenge_2->valor_puntaje_registro_fase;
            $data['keys'] = money_format("%i", $registerPhase__Challenge_2->valor_asignacion_rfu);
            $pdf->loadHTML(view('reports.program.innpulsa.letter_keys_refuse', $data)->render());
        }

        return $pdf->stream(sprintf('%s.pdf', 'documentador'));
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
