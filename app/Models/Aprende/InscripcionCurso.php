<?php

namespace App\Models\Aprende;

use Illuminate\Database\Eloquent\Model;

use App\Models,
    App\Models\Core\Estado;

use Validator, TuProyecto, DB, Input, Auth;

class InscripcionCurso extends Model {
	/**
	 * The database table used by the model.
	 *
	 * @var string
	 */
	protected $table = 'aprende.inscripcion_curso';

   	protected $primaryKey = 'codigo_inscripcion_curso';

	public $timestamps = false;

	protected $fillable = array(
								'codigo_usuario',
								'codigo_curso',
								'codigo_estado',
								'fecha_inscripcion_curso',
								'hora_inscripcion_curso',
								'porcentaje_progreso',
								'codigo_institucion',
								'fecha_envio_certificado'
							);

    /**
     * usuarios registrados a la convocatoria
     * registrados a cursos
     * total usuario q' completaron cursos
     */
    public static function  getCursesCompleted(array $codigosCursos, int $codigoConvocatoria)
    {
        return $curseCompleted = InscripcionCurso::select(
            DB::raw('count(u.correo_electronico)'),
            'u.correo_electronico', 'u.codigo_usuario',
            'u.nombre_usuario','u.nickname_usuario',
            DB::raw('max(inscripcion_curso.fecha_inscripcion_curso) AS fecha_inscripcion_curso'),
            DB::raw('max(inscripcion_curso.codigo_inscripcion_curso) AS codigo_inscripcion_curso'),
            DB::raw('count(inscripcion_curso.fecha_envio_certificado) AS envio_certificado'),
            'pe.primer_apellido_persona'
            )
            ->join('aprende.curso_institucion as ci', 'inscripcion_curso.codigo_curso', '=', 'ci.codigo_curso')
            ->join('seguridad.usuario as u', 'inscripcion_curso.codigo_usuario', '=', 'u.codigo_usuario')
            ->join('core.persona as pe', 'inscripcion_curso.codigo_usuario', '=', 'pe.codigo_usuario')
            ->join('core.registro_convocatoria as rc', 'rc.codigo_usuario', '=', 'inscripcion_curso.codigo_usuario')
            ->where('ci.codigo_institucion', 75 )
            ->where('ci.codigo_estado', Estado::getState('active'))
            ->whereIn('ci.codigo_curso', $codigosCursos)
            ->where('inscripcion_curso.porcentaje_progreso', 100)
            ->where('rc.codigo_convocatoria', $codigoConvocatoria)
            ->where('inscripcion_curso.codigo_estado', Estado::getState('active'))
            ->groupBy('u.correo_electronico', 'u.codigo_usuario', 'u.nombre_usuario','u.nickname_usuario', 
                'pe.primer_apellido_persona')
            ->get();
    }

    /**
     * Defining Relations
    */
    public function usuario()
    {
        return $this->hasOne('App\Usuario','codigo_usuario','codigo_usuario');
    }


}
