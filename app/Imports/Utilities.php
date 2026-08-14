<?php

namespace App\Imports;

require("ocupations.php");
require("professions.php");
require("industries.php");
require("actieconomic.php");

use App\Models\Seguridad\Usuario,
    App\Models\Core\UsuarioInstitucion,
    App\Models\Core\Persona;

class Utilities
{
    /**
     * return null or int
     */
    public static function  getOcupation($ocupation = "")
    {
        $ocupation = mb_strtolower(trim($ocupation));

        $ocupations = OCUPATIONS; //of ocupations.php
        if (array_key_exists($ocupation, $ocupations ))
            return $ocupations[$ocupation];

        return null;
    }

    /**
     * return null or int
     */
    public static function  getProfession($profession = "")
    {
        $profession = mb_strtolower(trim($profession));

        $professions = PROFESSIONS; //of professions.php
        if (array_key_exists($profession, $professions))
            return $professions[$profession];

        return null;
    }

    /**
     * return null or int
     */
    public static function  getIndustry($industry = "")
    {
        $industry = mb_strtolower(trim($industry));

        $industries = INDUSTRIES; //of industries.php
        if (array_key_exists($industry, $industries))
            return $industries[$industry];

        return null;
    }

    /**
     * return null or int
     */
    public static function  getPhase($phase = "")
    {
        $phase = mb_strtolower(trim($phase));

        $phases = [
            'crecimiento'=>146,
            'pre semilla'=>143,
            'semilla'=>144,
            'etapa temprana'=>145,
            'consolidación'=>142,
            'idea' => 70,
            'en marcha' => 71
        ];
        if (array_key_exists($phase, $phases))
            return $phases[$phase];

        return null;
    }

    /**
     * return null or int
     */
    public static function  getSectorInteres($sector = "")
    {
        $sector = mb_strtolower(trim($sector));

        $sectors = ACTI_ECONOMIC; //of actieconomic.php

        if (array_key_exists($sector, $sectors))
            return $sectors[$sector];

        return null;
    }

    /**
     * return null or int
     */
    public static function  getEntityCoverage($coverage = "")
    {
        $coverage = mb_strtolower(trim($coverage));

        $coverages = [
            'local'=>1,
            'regional'=>2,
            'nacional'=>3,
            'internacional'=>4,
        ];

        if (array_key_exists($coverage, $coverages))
            return $coverages[$coverage];

        return null;
    }

    /**
     * return null or int
     */
    public static function  getTypeSociety($society_company = "")
    {
        $society_company = mb_strtolower(trim($society_company));

        $arr_society_companys = [
            'sociedad por acciones simplificadas(s.a.s)'=>1,
            'sociedad anónima(s.a)'=>2,
            'ltda'=>3,
            'persona natural'=>4,
            'persona jurídica'=>5,
            'cooperativa'=>6,
            'cooperativa de trabajo asociado(c.t.a)'=>7,
            'fundación'=>8,
            'empresa unipersonal(e.u)'=>9,
            'persona natural con establecimiento de comercio'=>10,
            'sociedad anónima simplificada '=>11,
            'régimen simplificado'=>12,
            'otra'=>13,
            'sin registro'=>14,
            'sociedad comercial'=>15,
            'entidad sin ánimo de lucro'=>16,
            'persona natural con establecimiento comercial'=>17,
            'economía solidaria'=>18,
            'persona natural'=>19,
        ];

        if (array_key_exists($society_company, $arr_society_companys))
            return $arr_society_companys[$society_company];

        return null;
    }


    /**
     * obtiene tipo de identificacion
     * $tipo_identificacion string
     */
    public static function getOptionIdentification($tipo_identificacion)
    {

        if (is_null($tipo_identificacion)) return null ;

        $tipo_identificacion = mb_strtolower(trim($tipo_identificacion));
        $codigo_tipo_identificacion = [
            'cédula de ciudadanía' => 1,
            'permiso especial de permanencia' => 9,
            'cédula de extranjería' => 2,
            'cédula de otro país' => 11,
            'pasaporte' => 5,
            'tarjeta de identidad' => 4,
            'documento nacional de identificación' => 12,
            'nit' => 3,
            'registro civil' => 6,
        ];


        if (array_key_exists($tipo_identificacion, $codigo_tipo_identificacion))
            return $codigo_tipo_identificacion[$tipo_identificacion];

        return null;
    }

    /**
     * return null or int
     */
    public static function getGender($genero = "")
    {
        $genero = mb_strtolower(trim($genero));

        $codigo_genero = [
            'masculino' => 1,
            'femenino' => 2,
            'prefiero no decirlo' => 3,
        ];

        if (array_key_exists($genero, $codigo_genero))
            return $codigo_genero[$genero];

        return null;
    }

    /**
     * return null or int
     */
    public static function getLevelAcademic($nivel_academico = "")
    {
        $nivel_academico = mb_strtolower(trim($nivel_academico));

        $codigo_nivel_academico = [
            'bachiller' => 8,
            'básica secundaria' => 14,
            'doctorado' => 5,
            'especialización' => 4,
            'maestría' => 11,
            'primaria' => 13,
            'profesional' => 3,
            'sin título' => 12,
            'técnico laboral' => 1,
            'técnico profesional' => 15,
            'tecnológica' => 9,
        ];

        if (array_key_exists($nivel_academico, $codigo_nivel_academico))
            return $codigo_nivel_academico[$nivel_academico];

        return null;
    }

    public static function validateDate($date, $format = 'Y-m-d')
    {
        $d = \DateTime::createFromFormat($format, $date);
        return $d && $d->format($format) == $date;
    }

    public static function getUser($email, $numero_identificacion)
    {
        return Usuario::where('correo_electronico', '=',  $email)
                    ->orWhere('identificacion_usuario', $numero_identificacion)
                    ->first();
    }

    
    public static function getUsuarioInstitucion(int $codigo_usuario, int $codigo_institucion)
    {
		return UsuarioInstitucion::where('codigo_usuario', '=', $codigo_usuario)
                                ->where('codigo_institucion', '=', $codigo_institucion)
                                ->first();
    }

    
    public static function getIdentificationUser(string $identification, int $codigo_institucion)
    {
        $query = Usuario::query();
        $query->select();
        $query->join('core.usuario_institucion', 'usuario_institucion.codigo_usuario', '=', 'usuario.codigo_usuario');
        $query->where('usuario_institucion.codigo_institucion', '=', $codigo_institucion);
        $query->where('usuario.identificacion_usuario', '=', $identification);
        $data = $query->first();
        return $data;
    }

    /**
     * Obtiene datos del modelo Persona mediante el codigo_usuario.
     *
     * @access public
     * @static function
     * @param int id_user
     * @return object $person
     */
    public static function getPerson($id_user)
    {
        $query = Persona::query();
        $query->where('codigo_usuario', $id_user);
        $person = $query->first();
        return $person;
    }
}
