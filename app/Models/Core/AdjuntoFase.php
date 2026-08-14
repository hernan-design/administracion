<?php


namespace App\Models\Core;

use Illuminate\Database\Eloquent\Model;

class AdjuntoFase extends Model
{
	protected $table = 'core.adjunto_fase';

	protected $primaryKey = 'codigo_adjunto_fase';

	public $timestamps = false;

	public $incrementing = false;

	protected $fillable = ['codigo_estado', 'codigo_adjunto', 'codigo_fase'];
}
