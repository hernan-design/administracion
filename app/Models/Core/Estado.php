<?php

namespace App\Models\Core;

use Illuminate\Database\Eloquent\Model;
use Validator, Input, Auth, DB, Lang, App, URL, Request, Cache;

class Estado extends Model
{
    protected $table = 'core.estado';

    protected $primaryKey = 'codigo_estado';

    public static function getState($state) {
    	switch ($state) :
		    case 'progress': return 1;
		    case 'review': return 2;
		    case 'cancelled': return 3;
		    case 'live': return 4;
		    case 'correction': return 5;
		    case 'confirm': return 6;
		    case 'confirmed': return 7;
		    case 'active': return 8;
		    case 'inactive': return 9;
		    case 'feedback': return 10;
		    case 'complete': return 11;
 		    case 'qualified': return 12;
 		    case 'removed': return 13;
 		    case 'pending': return 14;
 		    case 'evaluated': return 15;
 		    case 'closed': return 16;
 		    case 'locked': return 17;
        case 'pendinginstitution': return 18;
        case 'preinscription': return 19;
        case 'notified': return 20;
 		    case 'forward': return 21;
        case 'offer': return 22;
        case 'no_linked': return 23;
        case 'linked': return 24;
        case 'rejected': return 25;
        case 'returned': return 26;
        case 'replaced': return 27;
        case 'incomplete': return 28;
        case 'finalized': return 29;
        case 'to_finalize': return 30;
        case 'review_deliverables': return 32;
        case 'rejection_of_deliverables': return 33;
			  case 'pending_accept_session': return 34;

			  default: return 'Not state identified';
      endswitch;
    }

    public static function getNameState(int $state): string {
    	switch ($state) :
		    case 1: return 'Progreso';
		    case 2: return 'Revisión';
		    case 3: return 'Cancelado';
		    case 4: return 'live';
		    case 5: return 'corrección';
		    case 6: return 'Confirmar';
		    case 7: return 'Confirmado';
		    case 8: return 'Activo';
		    case 9: return 'Inactivo';
		    case 10: return 'feedback';
		    case 11: return 'Completo';
 		    case 12: return 'qualified';
 		    case 13: return 'removed';
 		    case 14: return 'Pendiente';
 		    case 15: return 'evaluated';
 		    case 16: return 'Cerrado';
 		    case 17: return 'Bloqueado';
            case 18: return 'pendinginstitution';
            case 19: return 'preinscription';
            case 20: return 'Notificado';
 		    case 21: return 'forward';
            case 22: return 'offer';
            case 23: return 'no_linked';
            case 24: return 'linked';
            case 25: return 'Rechazado';
            case 26: return 'Devuelto';
            case 27: return 'replaced';
            case 28: return 'Incompleto';
            case 29: return 'Finalizado';
            case 30: return 'Finalizar';
            case 32: return 'Revisión entregables';
            case 33: return 'Rechazo de los entregables';
            case 34: return 'Pendiente aceptar sesión';

			default: return 'Unidentified state';
        endswitch;
    }

    public static function getStatelist($filter)
    {
        $query = Estado::query();
        $query->select('codigo_estado', 'nombre_estado');

        switch ($filter) {
            case 'state_purchase':
                 $query->whereIn('codigo_estado', [self::getState('cancelled'),self::getState('confirm'), self::getState('confirmed'), self::getState('removed'), self::getState('pending'), self::getState('finalized'), self::getState('offer'), self::getState('rejected')]);
            break;

            case 'state_agreement':
                 $query->whereIn('codigo_estado', [self::getState('confirm'), self::getState('confirmed'), self::getState('active'), self::getState('finalized'), self::getState('progress'), self::getState('to_finalize'), self::getState('review_deliverables'), self::getState('rejection_of_deliverables')]);
            break;

            case 'state_product':
                 $query->whereIn('codigo_estado', [self::getState('review'), self::getState('active'), self::getState('pending')]);
            break;
        }
        $query->orderby('nombre_estado','asc');

        return $query->get();
    }

	public static function getStates(Array $denied = [], Array $allowed = [], $filter)
	{
		$query = Estado::query();
		$query->select('codigo_estado', 'nombre_estado');

		if(count($denied) > 0){
			$query->whereNotIn('codigo_estado', $denied);
		}

		if(count($allowed) > 0){
			$query->whereIn('codigo_estado', $allowed);
		}

		if($filter){
			$query->whereRaw("nombre_estado ilike '%".$filter."%'");
		}

		$query->orderby('nombre_estado', 'asc');

		return $query->get();
	}
}
