<?php

namespace App\Models\Core;

use DB, Datatables;
use Illuminate\Database\Eloquent\Model;

class CategoriaProductoSesion extends Model
{

    protected $table = 'core.categoria_producto_sesion';

   	protected $primaryKey = 'codigo_categoria_producto_sesion';

}
