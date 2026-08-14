<?php

namespace App\Models\Core;

use Illuminate\Database\Eloquent\Model;

use Validator;
class Discusion extends Model
{
    /**
	 * The database table used by the model.
	 *
	 * @var string
	 */
	protected $table = 'core.discucion';

	protected $primaryKey = 'codigo_discucion';

	public $timestamps = false;

    protected $fillable = ['texto_discucion'];

    public function isValid($data)
    {
        $rules = array(
            'texto_discucion' => 'required|string'
        );

        $validator = Validator::make($data, $rules);
        if ($validator->passes()) {
            return true;
        }
        $this->errors = $validator->errors();
        return false;
    }



	/**
	 * Defining Relations
	*/
	public function usuario()
    {
        return $this->hasOne('App\Models\Seguridad\Usuario','codigo_usuario','codigo_usuario');
    }

    public function estado()
    {
        return $this->hasOne('App\Models\Core\Estado','codigo_estado','codigo_estado');
    }

   	public function comentarios()
    {
        return $this->hasMany('App\Models\Core\ComentarioDiscusion','codigo_discucion','codigo_discucion');
    }
}
