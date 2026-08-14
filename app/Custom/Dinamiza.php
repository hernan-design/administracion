<?php

namespace Custom;

use SendEmail;

use App\Models\Core\Fase, App\Models\Seguridad\Usuario, App\Models\Seguridad\Rol, App\Models\Core\RegistroFaseUsuario, App\Models\Core\Estado;

class Dinamiza
{
    public static function registerToPhase(Fase $phase, Usuario $user, RegistroFaseUsuario $registerPhase)
    {
        switch ($phase->codigo_fase) {
            case  Fase::getIdPhase('register'):
                if($registerPhase->visualizacion_usuario){
                    $registerPhase->codigo_estado = Estado::getState('linked');
                    $registerPhase->save();

                    $role = Rol::getRol('seller');
                    $user->attachRole($role);

                    // Send mail
                    $data = [];
                    $data['_template'] = 'emails.program.dinamiza.selectionprovider';
                    $data['_subject'] = 'Selección proveedor';
                    $data['_to'] = $user->correo_electronico;
                    $data['_to_name'] = ucwords(strtolower($user->nombre_usuario));
                    $data['_inst'] = session('institution')->codigo_institucion;
                    $data['_url_action'] = '/marketplace';
                    SendEmail::notification($data);
                }else{
                    $role = Rol::getRol('seller');
                    $user->detachRole($role);

                    $registerPhase->codigo_estado = Estado::getState('active');
                    $registerPhase->save();
                }
            break;

            case Fase::getIdPhase('reg_mentores_provedores'):
              if($registerPhase->visualizacion_usuario){
                $role = Rol::getRol('mentor');
                $user->attachRole($role);

                // Send mail
                $data = [];
                $data['_template'] = 'emails.program.dinamiza.approvementoreprovedor';
                $data['_subject'] = 'Solicitud de Mentor';
                $data['_to'] = $user->correo_electronico;
                $data['_name'] = ucfirst($user->nombre_usuario);
                $data['_to_name'] = ucwords(strtolower($user->nombre_usuario));
                $data['_inst'] = session('institution')->codigo_institucion;
                $data['_url_action'] = '/marketplace';
                $data['_url_action_mentor'] = '/mentoria/mentores/create';
                SendEmail::notification($data);

              } else {
                $role = Rol::getRol('mentor');
                $user->detachRole($role);
              }
            break;

            case Fase::getIdPhase('reg_entidades_provedores'):
              $userEntityApply = Usuario::find($user->owner_id);
              if($registerPhase->visualizacion_usuario){
                $role = Rol::getRol('seller');
                $userEntityApply->attachRole($role);

                // Send mail
                $data = [];
                $data['_template'] = 'emails.program.dinamiza.approveentityprovedor';
                $data['_subject'] = 'Solicitud proveedor de servicios';
                $data['_to'] = $userEntityApply->correo_electronico;
                $data['_name'] = ucfirst($userEntityApply->nombre_usuario);
                $data['_to_name'] = ucwords(strtolower($user->nombre_usuario));
                $data['_inst'] = session('institution')->codigo_institucion;
                $data['_url_action'] = '/marketplace';
                SendEmail::notification($data);

              } else {
                $role = Rol::getRol('seller');
                $userEntityApply->detachRole($role);
              }
            break;
        }
    }
}
