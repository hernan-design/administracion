<?php

namespace App\Http\Controllers\Reportes;

use Illuminate\Http\Request;

use App\Http\Requests;
use App\Http\Controllers\Controller;
use App\Exports\ReportsExport;

use App\Models\Core\Estado, App\Models\Core\Ciudad, App\Models\Core\RegistroFaseUsuario, App\Models\Seguridad\Usuario;

use DB, Excel, View, Auth;

class Monitoreo12Controller extends Controller
{
	/**
	 * Display a listing of the resource.
	 *
	 * @return \Illuminate\Http\Response
	 */
	public function index(Request $request)
	{
		return view('reports.program.innpulsa.monitoreo12.index');
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
		$user = Usuario::find(Auth::user()->codigo_usuario);
		if(!$user instanceof Usuario) {
			throw new \Exception("Error recuperando usuario en sesión.");
		}

		// Data
		$query = RegistroFaseUsuario::query();
		$query->select('numero_registro_convocatoria', 'usuario.nombre_usuario', 'usuario.identificacion_usuario', DB::raw("(fecha_registro_convocatoria || ' ' || hora_registro_convocatoria) as fecha_registro_convocatoria"), 'puntaje_total_eval_emprendi', 'evaluador.nombre_usuario as nombre_evaluador', 'notificacion_cambio_registro_fase', 'nombre_categoria_proyecto', 'puntaje_total_encuesta as puntaje_reto1',
			DB::raw("(SELECT texto_respuesta_encuesta FROM core.respuesta_encuesta
				WHERE codigo_pregunta_encuesta = 1223 AND respuesta_encuesta.codigo_usuario_encuesta = registro_convocatoria.codigo_usuario_encuesta) as cedula_usuario"),
			DB::raw("(SELECT texto_respuesta_encuesta FROM core.respuesta_encuesta
				WHERE codigo_pregunta_encuesta = 1228 AND respuesta_encuesta.codigo_usuario_encuesta = registro_convocatoria.codigo_usuario_encuesta) as ciudad_usuario"),
			DB::raw("(CASE WHEN (puntaje_total_eval_emprendi >= 60 AND puntaje_total_eval_emprendi <= 85) THEN 'SI' ELSE 'NO' END) as aprobado_reto12"),
			DB::raw("
			(
				SELECT ROUND(SUM( ( t.evaluacion + t.scoring ) / 2 ), 2)
					FROM (
						SELECT codigo_grupo_item_eval,
						SUM( COALESCE(puntaje_resul_eval_empren, 0) ) AS evaluacion,
						(
							SELECT SUM(puntaje_respuesta_encuesta)
							FROM core.respuesta_encuesta
							INNER JOIN core.pregunta_encuesta ON respuesta_encuesta.codigo_pregunta_encuesta = pregunta_encuesta.codigo_pregunta_encuesta
							WHERE pregunta_encuesta.codigo_encuesta = 49
							AND respuesta_encuesta.codigo_usuario_encuesta = registro_convocatoria.codigo_usuario_encuesta
							AND pregunta_encuesta.codigo_pregunta_encuesta in (1245, 1249, 1246, 1247)
							AND respuesta_encuesta.codigo_estado = 8
						) AS scoring

						FROM core.item_evaluacion_emprendimiento
						LEFT JOIN core.evaluacion_item_emprendimiento ON evaluacion_item_emprendimiento.codigo_item_evaluacion = item_evaluacion_emprendimiento.codigo_item_evaluacion
						LEFT JOIN core.resultado_evaluacion_emprendimiento ON resultado_evaluacion_emprendimiento.codigo_resultado_evaluacion = evaluacion_item_emprendimiento.codigo_resultado_evaluacion
                        LEFT JOIN core.asignacion_jurado ON resultado_evaluacion_emprendimiento.codigo_asignacion_jurado = asignacion_jurado.codigo_asignacion_jurado

						WHERE item_evaluacion_emprendimiento.codigo_evaluacion_emprendimiento = 9 AND codigo_grupo_item_eval = 1
						AND (asignacion_jurado.codigo_registro_fase_usuario = registro_fase_usuario.codigo_registro_fase_usuario OR asignacion_jurado.codigo_registro_fase_usuario IS NULL)

						GROUP BY codigo_grupo_item_eval
						ORDER BY codigo_grupo_item_eval
					) as t
				) as average_differential"
			),

			DB::raw("
			(
				SELECT ROUND(SUM( ( t.evaluacion + t.scoring ) / 2 ), 2)
					FROM (
						SELECT codigo_grupo_item_eval,
						SUM( COALESCE(puntaje_resul_eval_empren, 0) ) AS evaluacion,
						(
							( SELECT
								SUM((CASE pregunta_encuesta.codigo_pregunta_encuesta
									WHEN 1395
									THEN (
										(CASE WHEN (pregunta_encuesta.codigo_pregunta_encuesta = 1395 and respuesta_encuesta.codigo_opcion_encuesta = 1851)
											THEN ( CASE
												WHEN (
													(SELECT COALESCE(r1260.texto_respuesta_encuesta::double precision, 0)
														FROM core.respuesta_encuesta as r1260
														WHERE r1260.codigo_usuario_encuesta = respuesta_encuesta.codigo_usuario_encuesta
														AND r1260.codigo_pregunta_encuesta = 1260
													) >= 300000
												)
											THEN 7.5 ELSE -7.5 END)
										ELSE 0 END)
									)
									ELSE puntaje_respuesta_encuesta
								END))
							FROM core.respuesta_encuesta
							INNER JOIN core.pregunta_encuesta ON respuesta_encuesta.codigo_pregunta_encuesta = pregunta_encuesta.codigo_pregunta_encuesta
							WHERE pregunta_encuesta.codigo_encuesta = 49 AND respuesta_encuesta.codigo_estado = 8
							AND respuesta_encuesta.codigo_usuario_encuesta = registro_convocatoria.codigo_usuario_encuesta
							AND pregunta_encuesta.codigo_pregunta_encuesta IN (1395, 1252, 1253, 1248) )

						) AS scoring

						FROM core.item_evaluacion_emprendimiento
						LEFT JOIN core.evaluacion_item_emprendimiento ON evaluacion_item_emprendimiento.codigo_item_evaluacion = item_evaluacion_emprendimiento.codigo_item_evaluacion
						LEFT JOIN core.resultado_evaluacion_emprendimiento ON resultado_evaluacion_emprendimiento.codigo_resultado_evaluacion = evaluacion_item_emprendimiento.codigo_resultado_evaluacion
                        LEFT JOIN core.asignacion_jurado ON resultado_evaluacion_emprendimiento.codigo_asignacion_jurado = asignacion_jurado.codigo_asignacion_jurado

						WHERE
						item_evaluacion_emprendimiento.codigo_evaluacion_emprendimiento = 9 AND codigo_grupo_item_eval = 2
						AND (asignacion_jurado.codigo_registro_fase_usuario = registro_fase_usuario.codigo_registro_fase_usuario OR asignacion_jurado.codigo_registro_fase_usuario IS NULL)

						GROUP BY codigo_grupo_item_eval
						ORDER BY codigo_grupo_item_eval
					) as t
				) as average_commercial"
			),

			DB::raw("( SELECT SUM(puntaje_respuesta_encuesta)
					FROM core.respuesta_encuesta
					INNER JOIN core.pregunta_encuesta ON respuesta_encuesta.codigo_pregunta_encuesta = pregunta_encuesta.codigo_pregunta_encuesta
					WHERE pregunta_encuesta.codigo_encuesta = 49 AND respuesta_encuesta.codigo_estado = 8
					AND respuesta_encuesta.codigo_usuario_encuesta = registro_convocatoria.codigo_usuario_encuesta
					AND pregunta_encuesta.codigo_pregunta_encuesta IN (1254, 1395, 1256) ) as validation_commercial"),

			DB::raw("( SELECT SUM(puntaje_respuesta_encuesta)
					FROM core.respuesta_encuesta
					INNER JOIN core.pregunta_encuesta ON respuesta_encuesta.codigo_pregunta_encuesta = pregunta_encuesta.codigo_pregunta_encuesta
					WHERE pregunta_encuesta.codigo_encuesta = 49 AND respuesta_encuesta.codigo_estado = 8
					AND respuesta_encuesta.codigo_usuario_encuesta = registro_convocatoria.codigo_usuario_encuesta
					AND pregunta_encuesta.codigo_pregunta_encuesta IN (1250, 1251) ) as average_work_team")
		);
		$query->join('core.registro_convocatoria', 'registro_fase_usuario.codigo_registro_convocatoria', '=', 'registro_convocatoria.codigo_registro_convocatoria');
		$query->join('core.usuario_encuesta', 'usuario_encuesta.codigo_usuario_encuesta', '=', 'registro_convocatoria.codigo_usuario_encuesta');
		$query->join('seguridad.usuario', 'registro_fase_usuario.codigo_usuario', '=', 'usuario.codigo_usuario');
		$query->join('core.entidad', 'usuario.codigo_usuario', '=', 'entidad.codigo_usuario');
		$query->join('core.usuario_institucion', function($join) {
			$join->on('usuario_institucion.codigo_usuario', '=', 'entidad.codigo_usuario')
				->where('usuario_institucion.codigo_institucion', '=', 51);
		});
		$query->join('core.dato_entidad', 'usuario_institucion.codigo_usuario_institucion', '=', 'dato_entidad.codigo_usuario_institucion');
		$query->join('core.categoria_proyecto', 'dato_entidad.codigo_categoria_proyecto', '=', 'categoria_proyecto.codigo_categoria_proyecto');
		$query->join('core.asignacion_jurado', function($join) {
			$join->on('registro_fase_usuario.codigo_registro_fase_usuario', '=', 'asignacion_jurado.codigo_registro_fase_usuario')
				->where('asignacion_jurado.codigo_estado', '!=', Estado::getState('removed'));
		});
		$query->join('core.resultado_evaluacion_emprendimiento', 'asignacion_jurado.codigo_asignacion_jurado', '=', 'resultado_evaluacion_emprendimiento.codigo_asignacion_jurado');
		$query->join('seguridad.usuario as evaluador', 'asignacion_jurado.codigo_usuario', '=', 'evaluador.codigo_usuario');

		if($request->has('notified') && !empty($request->notified)) {
			$query->where('registro_fase_usuario.visualizacion_usuario', $request->notified);
		}

		if($request->has('number_register') && !empty($request->number_register)){
			$query->where('registro_convocatoria.numero_registro_convocatoria', '=', $request->number_register);
		}

		$query->where('registro_fase_usuario.codigo_estado', '!=', Estado::getState('inactive'));
		$query->where('registro_fase_usuario.codigo_fase', 150);
		$query->orderBy('fecha_registro_convocatoria','asc');
		$query->orderBy('hora_registro_convocatoria','asc');
		$data = $query->get()->toArray();

		// Cities
		$cities = Ciudad::whereIn('codigo_ciudad', $query->pluck('ciudad_usuario')->all())->pluck('nombre_ciudad', 'codigo_ciudad')->toArray();

		$filename = sprintf('%s_%s_%s', 'reporte_monitoreo_1_2', date('Y_m_d'), date('H_m_s'));

    $headings = ['No.', 'Entidad', 'Nit', 'Cédula', 'Sector o Industria', 'Ciudad/Departamento', 'Puntaje Inicial', 'Criterio 1 Producto con Diferencial', 'Criterio 2 Proyección Comercial', 'Criterio 3 Validación Comercial', 'Criterio 4 Equipo de Trabajo', 'Puntaje Final', 'Aprobado Reto 2', 'Fecha de registro', 'Fecha de Notificación', 'Evaluador'];

    $viewPath= "reports.program.innpulsa.monitoreo12.report";

    return Excel::download(new ReportsExport($headings, $data, $cities, $viewPath), 'report.xlsx');

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
