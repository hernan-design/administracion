<?php
/**
 * Modelo OpcionEncuesta
 * @Annotation permite asociar las opciones de respuesta por cada pregunta encuesta creada.
 * @author Harold Rodrígurz <hartuproyecto@gmail.com>
 */
namespace App\Models\Core;

use Illuminate\Database\Eloquent\Model;

class OpcionEncuesta extends Model
{
    /**
	 * The database table used by the model.
	 *
	 * @var string
	 */
    protected $table = 'core.opcion_encuesta';

    protected $primaryKey = 'codigo_opcion_encuesta';

    public $timestamps = false;

}