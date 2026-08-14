<?php

namespace App\Models\Seguridad;

use Greatsami\Entrust\EntrustPermission;

class Permiso extends EntrustPermission
{

	public static function getPermission($permission) {
    	switch ($permission) :
		    case 'login-admin': return 11;
			default: return 'Not permission identified';
		endswitch;
    }
}
