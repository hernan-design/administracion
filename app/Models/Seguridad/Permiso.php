<?php

namespace App\Models\Seguridad;

use Laratrust\Models\Permission as LaratrustPermission;

class Permiso extends LaratrustPermission
{

	public static function getPermission($permission) {
    	switch ($permission) :
		    case 'login-admin': return 11;
			default: return 'Not permission identified';
		endswitch;
    }
}
