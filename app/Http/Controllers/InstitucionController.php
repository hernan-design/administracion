<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use App\Models\Param\Institucion, App\Models\Core\Estado, App\Models\Param\TipoInstitucion, App\Models\Param\RedSocial, App\Models\Param\Meta, App\Models\Core\Adjunto, App\Models\Param\ParametroInstitucion, App\Models\Param\MetaInstitucion, App\Models\Param\ParametroModulo, App\Models\Param\VisibilidadInstitucion, App\Models\Param\RedSocialInsti, App\Models\Param\TerminosCondiciones, App\Models\Param\VersionTerminos;
use Datatables, DB, Log, Lang;

class InstitucionController extends Controller
{
    /**
     * Display a listing of the resource.
     *
     * @return \Illuminate\Http\Response
     */
    public function index(Request $request)
    {
        if ($request->ajax()) {
            return Datatables::of( Institucion::getInstitutions( $request->all() ) )->make(true);
        }

        return view('institution.index');
    }

    /**
     * Show the form for creating a new resource.
     *
     * @return \Illuminate\Http\Response
     */
    public function create()
    {
        $institutionType = TipoInstitucion::all();    
        $institution = null;

        return view('institution.create-edit', compact('institutionType', 'institution'));
    }

    /**
     * Store a newly created resource in storage.
     *
     * @param  \Illuminate\Http\Request  $request
     * @return \Illuminate\Http\Response
     */
    public function store(Request $request)
    {
        if ($request->ajax()) 
        
        {
            DB::beginTransaction();
            try {
                $institution = new Institucion();
                $institution->codigo_tipo_institucion = $request->codigo_tipo_institucion;
                $institution->nombre_institucion = $request->nombre_institucion;
                $institution->slug_institucion = $request->slug_institucion;
                $institution->url_institucion = $request->url_institucion;
                $institution->descripcion_institucion = $request->descripcion_institucion;
                $institution->twitter_institucion = $request->twitter_institucion;
                $institution->widget_twitter = $request->widget_twitter_institucion;
                $institution->sigla_institucion = $request->sigla_institucion;
                $institution->privacidad_institucion = $request->privacidad_institucion == 0 ? true : false;
                $institution->facebook_api_key = $request->facebook_api_key_institucion;
                $institution->linkedin_api_key = $request->linkedin_api_key_institucion;
                $institution->twitter_api_key = $request->twitter_api_key_institucion;
                $institution->twitter_secret_key = $request->twitter_secret_key_institucion;
                $institution->google_analytics = $request->google_analytics_institucion;
                $institution->codigo_estado_inicial = $request->estado_inicial_institucion;
                $institution->validacion_correo = $request->validacion_identificacion_institucion == 0 ? true : false;
                $institution->contacto_institucion = $request->contacto_institucion;
                $institution->texto_registro_institucion = $request->texto_registro_institucion;
                $institution->contenido_registro_institucion = $request->contenido_registro_institucion;
                $institution->boton_registro_institucion = $request->boton_registro_institucion;
                $institution->social_login_institucion = $request->social_login_institucion == 0 ? true : false;
                $institution->tipo_registro_institucion = $request->tipo_registro_institucion == 0 ? true : false;
                $institution->home = $request->home_institucion;
                $institution->video_institucion = $request->video_institucion;
                $institution->correo_envio_notificaciones = $request->correo_notificaciones_institucion;
                $institution->carrousel_institucion = $request->carrousel_institucion == 0 ? true : false;
                $institution->revision_productos_marketplace = $request->revision_productos_marketplace_institucion == 0 ? true : false;
                $institution->identificacion_registro = $request->identificacion_registro_institucion == 0 ? true : false;
                $institution->modelo_negocio_institucion = $request->modelo_negocio_institucion == 0 ? true : false;
                $institution->url_admin_institucion = $request->url_admin_institucion;
                $institution->personalizacion_landing = $request->personalizacion_landing_institucion == 0 ? true : false;
                $institution->fuentes_personalizadas = $request->fuentes_personalizadas_institucion == 0 ? true : false;
                $institution->contador_articulos_pendientes = $request->contador_articulos_pendientes_institucion == 0 ? true : false;
                $institution->url_helpdesk_institucion = $request->url_helpdesk_institucion;
                $institution->clave_sitio_captcha = $request->clave_sitio_captcha_institucion;
                $institution->clave_secreta_captcha = $request->clave_secreta_captcha_institucion;
                $institution->codigo_userguiding = $request->codigo_userguiding_institucion;
                $institution->facebook_secret_key = $request->facebook_secret_key_institucion;
                $institution->linkedin_secret_key = $request->linkedin_secret_key_institucion;
                $institution->pasos_complementarios_registro = $request->pasos_complementarios_registro_institucion == 0 ? true : false;
                $institution->visibilidad_info_vendedor = $request->visibilidad_info_vendedor_institucion == 0 ? true : false;
                $institution->datos_complementarios = $request->datos_complementarios_institucion == 0 ? true : false;
                $institution->raiz_comunidad = $request->raiz_comunidad_institucion == 0 ? true : false;
                $institution->recomendaciones_marketplace = $request->recomendaciones_marketplace_institucion;
                $institution->pasos_compra = $request->pasos_compra_institucion == 0 ? true : false;
                $institution->contenido_usuario = $request->contenido_usuario_institucion == 0 ? true : false;
                $institution->solicitud_directa_mentoria = $request->solicitud_directa_mentoria_institucion == 0 ? true : false;
                $institution->certificado_evento = $request->certificado_evento_institucion == 0 ? true : false;
                $institution->validacion_identificacion = $request->validacion_identificacion_institucion;
                $institution->marketplace_entidad = $request->marketplace_entidad_institucion == 0 ? true : false;
                $institution->marketplace_servicios = $request->marketplace_servicios_institucion == 0 ? true : false;
                $institution->save();

                // Commit Transaction
				DB::commit();
				return response()->json(['success' => true, 'message' => 'La institucion ha sido creada correctamente!!', 'codigo_institucion' => $institution->codigo_institucion, 'codigo_estado' => $institution->codigo_estado, 'redirect' => true]);
            } catch (\Exception $e) {
                DB::rollback();
				Log::error(sprintf('%s:%s', 'InstitucionController:insert', $e->getMessage()));
				return response()->json(['success' => false, 'errors' => trans('app.exception')]);
            }
        }
    }

    /**
     * Display the specified resource.
     *
     * @param  int  $id
     * @return \Illuminate\Http\Response
     */
    public function show(Request $request, $id)
    {
        if ($request->ajax()) {
            $institution = Institucion::find($id);

            if (!$institution instanceof Institucion) {
                abort(404);
            }

            $typeParameters = Institucion::generalParametersInstitution('homepage', $institution->codigo_institucion);
            $typeParametersPlataform = Institucion::generalParametersInstitution('plataforma', $institution->codigo_institucion);

            $response = Institucion::buildGeneralDatesInstitution($institution, $typeParameters, $typeParametersPlataform);

            return response()->json($response);
        }
    }

    /**
     * Show the form for editing the specified resource.
     *
     * @param  int  $id
     * @return \Illuminate\Http\Response
     */
    public function edit($id)
    {
        if(!is_numeric($id)) {
            Log::error(sprintf('%s:%s', 'InstitucionController:edit', 'Error: not numeric ID'));
            abort(404,'Error: not numeric ID') ;
        }

        $institution = Institucion::find($id);
        $home = Institucion::getHomeInstitution($institution->home);
        $type_institution = TipoInstitucion::find($institution->codigo_tipo_institucion);
        $parametersInstitution = Institucion::generalParametersInstitution('homepage', $institution->codigo_institucion);
        $socialNetwork = RedSocial::all();
        $socialNetworkInsti = RedSocialInsti::where('codigo_institucion', '=', $institution->codigo_institucion)->get();
        $parametroInstitucion = ParametroInstitucion::join('param.parametro', 'parametro.codigo_parametro', '=', 'parametro_institucion.codigo_parametro')->where('codigo_institucion', '=', $institution->codigo_institucion)->orderBy('codigo_tipo_parametro', 'ASC')->get();
        $meta = Meta::all();
        $versionTerms = VersionTerminos::getValidationTerms($institution->codigo_institucion, TerminosCondiciones::getTypeTerms('terms'));
        $versionPrivacy = VersionTerminos::getValidationTerms($institution->codigo_institucion, TerminosCondiciones::getTypeTerms('privacy'));
        $versionVentas = VersionTerminos::getValidationTerms($institution->codigo_institucion, TerminosCondiciones::getTypeTerms('sales'));
        $versionMentoring = VersionTerminos::getValidationTerms($institution->codigo_institucion, TerminosCondiciones::getTypeTerms('mentoring'));
        $versionEvaluators = VersionTerminos::getValidationTerms($institution->codigo_institucion, TerminosCondiciones::getTypeTerms('evaluator'));

        return view('institution.create-edit', compact('versionTerms', 'versionPrivacy', 'versionVentas', 'versionMentoring', 'versionEvaluators', 'type_institution', 'institution', 'home', 'parametersInstitution', 'socialNetwork', 'meta', 'socialNetworkInsti', 'parametroInstitucion'));
    }

    /**
     * Update the specified resource in storage.
     *
     * @param  \Illuminate\Http\Request  $request
     * @param  int  $id
     * @return \Illuminate\Http\Response
     */
    public function update(Request $request, $id)
    {
        if ($request->ajax()) {
            DB::beginTransaction();
            try {
                $institution = Institucion::find($request->codigo_institucion);

                if(!$institution instanceof Institucion) {
                    abort(404);
                }

                if ($request->has('update') && !$request->has('terms')) {
                    if ($request->has('tab')) {
                        switch ($request->tab) {
                            case 'home':
                                $institution->codigo_tipo_institucion = $request->codigo_tipo_institucion;
                                $institution->nombre_institucion = $request->nombre_institucion;
                                $institution->slug_institucion = $request->slug_institucion;
                                $institution->url_institucion = $request->url_institucion;
                                $institution->descripcion_institucion = $request->descripcion_institucion;
                                $institution->twitter_institucion = $request->twitter_institucion;
                                $institution->widget_twitter = $request->widget_twitter_institucion;
                                $institution->sigla_institucion = $request->sigla_institucion;
                                $institution->privacidad_institucion = $request->privacidad_institucion == 0 ? true : false;
                                $institution->facebook_api_key = $request->facebook_api_key_institucion;
                                $institution->linkedin_api_key = $request->linkedin_api_key_institucion;
                                $institution->twitter_api_key = $request->twitter_api_key_institucion;
                                $institution->twitter_secret_key = $request->twitter_secret_key_institucion;
                                $institution->google_analytics = $request->google_analytics_institucion;
                                $institution->codigo_estado_inicial = $request->estado_inicial_institucion;
                                $institution->validacion_correo = $request->validacion_identificacion_institucion == 0 ? true : false;
                                $institution->contacto_institucion = $request->contacto_institucion;
                                $institution->texto_registro_institucion = $request->texto_registro_institucion;
                                $institution->contenido_registro_institucion = $request->contenido_registro_institucion;
                                $institution->boton_registro_institucion = $request->boton_registro_institucion;
                                $institution->social_login_institucion = $request->social_login_institucion == 0 ? true : false;
                                $institution->tipo_registro_institucion = $request->tipo_registro_institucion == 0 ? true : false;
                                $institution->home = $request->home_institucion;
                                $institution->video_institucion = $request->video_institucion;
                                $institution->correo_envio_notificaciones = $request->correo_notificaciones_institucion;
                                $institution->carrousel_institucion = $request->carrousel_institucion == 0 ? true : false;
                                $institution->revision_productos_marketplace = $request->revision_productos_marketplace_institucion == 0 ? true : false;
                                $institution->identificacion_registro = $request->identificacion_registro_institucion == 0 ? true : false;
                                $institution->modelo_negocio_institucion = $request->modelo_negocio_institucion == 0 ? true : false;
                                $institution->url_admin_institucion = $request->url_admin_institucion;
                                $institution->personalizacion_landing = $request->personalizacion_landing_institucion == 0 ? true : false;
                                $institution->fuentes_personalizadas = $request->fuentes_personalizadas_institucion == 0 ? true : false;
                                $institution->contador_articulos_pendientes = $request->contador_articulos_pendientes_institucion == 0 ? true : false;
                                $institution->url_helpdesk_institucion = $request->url_helpdesk_institucion;
                                $institution->clave_sitio_captcha = $request->clave_sitio_captcha_institucion;
                                $institution->clave_secreta_captcha = $request->clave_secreta_captcha_institucion;
                                $institution->codigo_userguiding = $request->codigo_userguiding_institucion;
                                $institution->facebook_secret_key = $request->facebook_secret_key_institucion;
                                $institution->linkedin_secret_key = $request->linkedin_secret_key_institucion;
                                $institution->pasos_complementarios_registro = $request->pasos_complementarios_registro_institucion == 0 ? true : false;
                                $institution->visibilidad_info_vendedor = $request->visibilidad_info_vendedor_institucion == 0 ? true : false;
                                $institution->datos_complementarios = $request->datos_complementarios_institucion == 0 ? true : false;
                                $institution->raiz_comunidad = $request->raiz_comunidad_institucion == 0 ? true : false;
                                $institution->recomendaciones_marketplace = $request->recomendaciones_marketplace_institucion;
                                $institution->pasos_compra = $request->pasos_compra_institucion == 0 ? true : false;
                                $institution->contenido_usuario = $request->contenido_usuario_institucion == 0 ? true : false;
                                $institution->solicitud_directa_mentoria = $request->solicitud_directa_mentoria_institucion == 0 ? true : false;
                                $institution->certificado_evento = $request->certificado_evento_institucion == 0 ? true : false;
                                $institution->validacion_identificacion = $request->validacion_identificacion_institucion;
                                $institution->marketplace_entidad = $request->marketplace_entidad_institucion == 0 ? true : false;
                                $institution->marketplace_servicios = $request->marketplace_servicios_institucion == 0 ? true : false;
                                $institution->recomendaciones_marketplace = $request->recomendaciones_marketplace;
                                $institution->save();
                            break;
                            case 'homepage':
                                if ($request->has('parameters')) {
                                    foreach ($request->parameters as $item) {
                                        $explParameter = explode("-", $item);
                                        $explValue = explode(":", $item); 
                                        $parameter = $explParameter[1];

                                        if (isset($explValue[1]) && $explValue[1] != '') {
                                            $paramInstitution = ParametroInstitucion::where('codigo_institucion', '=', $request->codigo_institucion)->where('codigo_parametro', '=', $parameter)->first();
                                           
                                            if (!$paramInstitution instanceof ParametroInstitucion) {
                                                $newparamInstitution = new ParametroInstitucion();
                                                $newparamInstitution->codigo_institucion = $request->institucion;
                                                $newparamInstitution->codigo_parametro = $parameter;
                                                $newparamInstitution->codigo_estado = Estado::getState('active');
                                                $newparamInstitution->valor_parametro = $explValue[1];
                                                $newparamInstitution->save();
                                            } else {
                                                ParametroInstitucion::where('codigo_institucion', '=', $request->codigo_institucion)
                                                    ->where('codigo_parametro', '=', $parameter)
                                                    ->update(['valor_parametro' => $explValue[1], 'codigo_estado' => Estado::getState('active')]);
                                            }
                                        }
                                    }
                                }

                                if ($request->has('social')) {
                                    foreach ($request->social as $item) {
                                        $explCodeSocial = explode("-", $item);
                                        $explValueSocial = explode(":", $item);
                                        $codeSocial = $explCodeSocial[1];
                                        $valueSocial = $explValueSocial[1];
                                        
                                        if (isset($valueSocial) && $valueSocial != '') {
                                            $redSocialInsti = RedSocialInsti::where('codigo_institucion', '=', $request->codigo_institucion)->where('codigo_red_social', '=', $codeSocial)->first();

                                            if (!$redSocialInsti instanceof RedSocialInsti) {
                                                $newRedSocialInsti = new RedSocialInsti();
                                                $newRedSocialInsti->codigo_institucion = $request->codigo_institucion;
                                                $newRedSocialInsti->codigo_red_social = $codeSocial;
                                                $newRedSocialInsti->valor_red_social_institucion = $valueSocial;
                                                $newRedSocialInsti->save();
                                            } else {
                                                RedSocialInsti::where('codigo_institucion', '=', $request->codigo_institucion)
                                                ->where('codigo_red_social', '=', $codeSocial)
                                                ->update(['valor_red_social_institucion' => $valueSocial]);
                                            }
                                        }
                                        
                                    }
                                }
                            break;
                            case 'module':
                                if ($request->has('estado')) {  
                                    foreach ($request->estado as $item) {
                                        $explModulesEstado = explode("-", $item);
                                        $explValueEstado = explode(":", $item);
                                        $valueModuleEstado = $explValueEstado[1];
                                        $codeModuleEstado = $explModulesEstado[1];
                                        
                                        $valVisibilidad = $valueModuleEstado == Estado::getState('active') ? true : false;

                                        $visibiliadInstitucionModulos = VisibilidadInstitucion::where('codigo_institucion', '=', $request->codigo_institucion)->where('codigo_modulo', '=', $codeModuleEstado)->first();

                                        if ($visibiliadInstitucionModulos instanceof VisibilidadInstitucion) {
                                            VisibilidadInstitucion::where('codigo_institucion', '=', $request->codigo_institucion)
                                            ->where('codigo_modulo', '=', $codeModuleEstado)
                                            ->update(['codigo_estado' => $valueModuleEstado, 'valor_visibilidad_institucion' => $valVisibilidad]);
                                        } else {
                                            $newVisibiliadInstitucionModulos = new VisibilidadInstitucion();
                                            $newVisibiliadInstitucionModulos->codigo_institucion = $request->codigo_institucion;
                                            $newVisibiliadInstitucionModulos->codigo_modulo = $codeModuleEstado;
                                            $newVisibiliadInstitucionModulos->codigo_estado = $valueModuleEstado;
                                            $newVisibiliadInstitucionModulos->valor_visibilidad_institucion = $valVisibilidad;
                                            $newVisibiliadInstitucionModulos->save();
                                        }
                                    }
                                }
                                
                                if ($request->has('color_title')) {
                                    foreach ($request->color_title as $item) {
                                        $explModulesColorTitle = explode("-", $item);
                                        $explValueColorTitle = explode(":", $item);
                                        $valueModuleColorTitle = $explValueColorTitle[1];
                                        $codeModuleColorTitle = $explModulesColorTitle[1];
                                        $codeParameterColorTitle = $explModulesColorTitle[2];
                                        
                                        $visibiliadInstitucionModulos = VisibilidadInstitucion::where('codigo_institucion', '=', $request->codigo_institucion)->where('codigo_modulo', '=', $codeModuleColorTitle)->where('valor_visibilidad_institucion', '=', true)->first();
                                        
                                        if ($visibiliadInstitucionModulos instanceof VisibilidadInstitucion) {
                                            $parameterModuleColorTitle = ParametroModulo::where('codigo_institucion', '=', $request->codigo_institucion)->where('codigo_modulo', '=', $codeModuleColorTitle)->where('codigo_parametro', '=', $codeParameterColorTitle)->first();
                                            
                                            if (!$parameterModuleColorTitle instanceof ParametroModulo) {
                                                $newParameterModuleColorTitle = new ParametroModulo();
                                                $newParameterModuleColorTitle->codigo_institucion = $request->codigo_institucion;
                                                $newParameterModuleColorTitle->codigo_modulo = $codeModuleColorTitle;
                                                $newParameterModuleColorTitle->codigo_parametro = $codeParameterColorTitle;
                                                $newParameterModuleColorTitle->codigo_estado = Estado::getState('active');
                                                $newParameterModuleColorTitle->valor_parametro_modulo = $valueModuleColorTitle;
                                                $newParameterModuleColorTitle->save();
                                            } else {
                                                ParametroModulo::where('codigo_institucion', '=', $request->codigo_institucion)
                                                ->where('codigo_modulo', '=', $codeModuleColorTitle)
                                                ->where('codigo_parametro', '=', $codeParameterColorTitle)
                                                ->update(['valor_parametro_modulo' => $valueModuleColorTitle]);
                                            }
                                        }
                                    }
                                }

                                if ($request->has('background')) {
                                    foreach ($request->background as $item) {
                                        $explModulesBackground = explode("-", $item);
                                        $explValueBackground = explode(":", $item);
                                        $valueModuleBackground = $explValueBackground[1];
                                        $codeModuleBackground = $explModulesBackground[1];
                                        $codeParameterBackground = $explModulesBackground[2];

                                        $visibiliadInstitucionModulos = VisibilidadInstitucion::where('codigo_institucion', '=', $request->codigo_institucion)->where('codigo_modulo', '=', $codeModuleBackground)->where('valor_visibilidad_institucion', '=', true)->first();

                                        if ($visibiliadInstitucionModulos instanceof VisibilidadInstitucion) {
                                            $parameterModuleBackground = ParametroModulo::where('codigo_institucion', '=', $request->codigo_institucion)->where('codigo_modulo', '=', $codeModuleBackground)->where('codigo_parametro', '=', $codeParameterBackground)->first();
                                            
                                            if (!$parameterModuleBackground instanceof ParametroModulo) {
                                                $parameterModuleBackground = new ParametroModulo();
                                                $parameterModuleBackground->codigo_institucion = $request->codigo_institucion;
                                                $parameterModuleBackground->codigo_modulo = $codeModuleBackground;
                                                $parameterModuleBackground->codigo_parametro = $codeParameterBackground;
                                                $parameterModuleBackground->codigo_estado = Estado::getState('active');
                                                $parameterModuleBackground->valor_parametro_modulo = $valueModuleBackground;
                                                $parameterModuleBackground->save();
                                            } else {
                                                ParametroModulo::where('codigo_institucion', '=', $request->codigo_institucion)
                                                ->where('codigo_modulo', '=', $codeModuleBackground)
                                                ->where('codigo_parametro', '=', $codeParameterBackground)
                                                ->update(['valor_parametro_modulo' => $valueModuleBackground]);
                                            }
                                        }
                                    }
                                }

                                if ($request->has('color_hover')) {
                                    foreach ($request->color_hover as $item) {
                                        $explModulesColorHover = explode("-", $item);
                                        $explValueColorHover = explode(":", $item);
                                        $valueModuleColorHover = $explValueColorHover[1];
                                        $codeModuleColorHover = $explModulesColorHover[1];
                                        $codeParameterColorHover = $explModulesColorHover[2];

                                        $visibiliadInstitucionModulos = VisibilidadInstitucion::where('codigo_institucion', '=', $request->codigo_institucion)->where('codigo_modulo', '=', $codeModuleColorHover)->where('valor_visibilidad_institucion', '=', true)->first();

                                        if ($visibiliadInstitucionModulos instanceof VisibilidadInstitucion) {
                                            $parameterModuleColorHover = ParametroModulo::where('codigo_institucion', '=', $request->codigo_institucion)->where('codigo_modulo', '=', $codeModuleColorHover)->where('codigo_parametro', '=', $codeParameterColorHover)->first();

                                            if (!$parameterModuleColorHover instanceof ParametroModulo) {
                                                $parameterModuleColorHover = new ParametroModulo();
                                                $parameterModuleColorHover->codigo_institucion = $request->codigo_institucion;
                                                $parameterModuleColorHover->codigo_modulo = $codeModuleColorHover;
                                                $parameterModuleColorHover->codigo_parametro = $codeParameterColorHover;
                                                $parameterModuleColorHover->codigo_estado = Estado::getState('active');
                                                $parameterModuleColorHover->valor_parametro_modulo = $valueModuleColorHover;
                                                $parameterModuleColorHover->save();
                                            } else {
                                                ParametroModulo::where('codigo_institucion', '=', $request->codigo_institucion)
                                                ->where('codigo_modulo', '=', $codeModuleColorHover)
                                                ->where('codigo_parametro', '=', $codeParameterColorHover)
                                                ->update(['valor_parametro_modulo' => $valueModuleColorHover]);
                                            }
                                         }
                                    }
                                }

                                if ($request->has('color_icon')) {
                                    foreach ($request->color_icon as $item) {
                                        $explModulesColorIcon = explode("-", $item);
                                        $explValueColorIcon = explode(":", $item);
                                        $valueModuleColorIcon = $explValueColorIcon[1];
                                        $codeModuleColorIcon = $explModulesColorIcon[1];
                                        $codeParameterColorIcon = $explModulesColorIcon[2];

                                        $visibiliadInstitucionModulos = VisibilidadInstitucion::where('codigo_institucion', '=', $request->codigo_institucion)->where('codigo_modulo', '=', $codeModuleColorIcon)->where('valor_visibilidad_institucion', '=', true)->first();

                                        if ($visibiliadInstitucionModulos instanceof VisibilidadInstitucion) {
                                            
                                            $parameterModuleColorIcon = ParametroModulo::where('codigo_institucion', '=', $request->codigo_institucion)->where('codigo_modulo', '=', $codeModuleColorIcon)->where('codigo_parametro', '=', $codeParameterColorIcon)->first();

                                            if (!$parameterModuleColorIcon instanceof ParametroModulo) {
                                                $parameterModuleColorIcon = new ParametroModulo();
                                                $parameterModuleColorIcon->codigo_institucion = $request->codigo_institucion;
                                                $parameterModuleColorIcon->codigo_modulo = $codeModuleColorIcon;
                                                $parameterModuleColorIcon->codigo_parametro = $codeParameterColorIcon;
                                                $parameterModuleColorIcon->codigo_estado = Estado::getState('active');
                                                $parameterModuleColorIcon->valor_parametro_modulo = $valueModuleColorIcon;
                                                $parameterModuleColorIcon->save();
                                            } else {
                                                ParametroModulo::where('codigo_institucion', '=', $request->codigo_institucion)
                                                ->where('codigo_modulo', '=', $codeModuleColorIcon)
                                                ->where('codigo_parametro', '=', $codeParameterColorIcon)
                                                ->update(['valor_parametro_modulo' => $valueModuleColorIcon]);
                                            }
                                            
                                        }
                                    }
                                }

                                if ($request->has('background_hover')) {
                                    foreach ($request->background_hover as $item) {
                                        $explModulesBackgroundHover = explode("-", $item);
                                        $explValueBackgroundHover = explode(":", $item);
                                        $valueModuleBackgroundHover = $explValueBackgroundHover[1];
                                        $codeModuleBackgroundHover = $explModulesBackgroundHover[1];
                                        $codeParameterBackgroundHover = $explModulesBackgroundHover[2];

                                        $visibiliadInstitucionModulos = VisibilidadInstitucion::where('codigo_institucion', '=', $request->codigo_institucion)->where('codigo_modulo', '=', $codeModuleBackgroundHover)->where('valor_visibilidad_institucion', '=', true)->first();

                                        if ($visibiliadInstitucionModulos instanceof VisibilidadInstitucion) {
                                            $parameterModuleBackgroundHover = ParametroModulo::where('codigo_institucion', '=', $request->codigo_institucion)->where('codigo_modulo', '=', $codeModuleBackgroundHover)->where('codigo_parametro', '=', $codeParameterBackgroundHover)->first();

                                            if (!$parameterModuleBackgroundHover instanceof ParametroModulo) {
                                                $parameterModuleBackgroundHover = new ParametroModulo();
                                                $parameterModuleBackgroundHover->codigo_institucion = $request->codigo_institucion;
                                                $parameterModuleBackgroundHover->codigo_modulo = $codeModuleBackgroundHover;
                                                $parameterModuleBackgroundHover->codigo_parametro = $codeParameterBackgroundHover;
                                                $parameterModuleBackgroundHover->codigo_estado = Estado::getState('active');
                                                $parameterModuleBackgroundHover->valor_parametro_modulo = $valueModuleBackgroundHover;
                                                $parameterModuleBackgroundHover->save();
                                            } else {
                                                ParametroModulo::where('codigo_institucion', '=', $request->codigo_institucion)
                                                ->where('codigo_modulo', '=', $codeModuleBackgroundHover)
                                                ->where('codigo_parametro', '=', $codeParameterBackgroundHover)
                                                ->update(['valor_parametro_modulo' => $valueModuleBackgroundHover]);
                                            }
                                        }
                                    }
                                }

                                if ($request->has('color_before')) {
                                    foreach ($request->color_before as $item) {
                                        $explModulesColorBefore = explode("-", $item);
                                        $explValueColorBefore = explode(":", $item);
                                        $valueModuleColorBefore = $explValueColorBefore[1];
                                        $codeModuleColorBefore = $explModulesColorBefore[1];
                                        $codeParameterColorBefore = $explModulesColorBefore[2];

                                        $visibiliadInstitucionModulos = VisibilidadInstitucion::where('codigo_institucion', '=', $request->codigo_institucion)->where('codigo_modulo', '=', $codeModuleColorBefore)->where('valor_visibilidad_institucion', '=', true)->first();

                                        if ($visibiliadInstitucionModulos instanceof VisibilidadInstitucion) {
                                            $parameterModuleColorBefore = ParametroModulo::where('codigo_institucion', '=', $request->codigo_institucion)->where('codigo_modulo', '=', $codeModuleColorBefore)->where('codigo_parametro', '=', $codeParameterColorBefore)->first();

                                            if (!$parameterModuleColorBefore instanceof ParametroModulo) {
                                                $parameterModuleColorBefore = new ParametroModulo();
                                                $parameterModuleColorBefore->codigo_institucion = $request->codigo_institucion;
                                                $parameterModuleColorBefore->codigo_modulo = $codeModuleColorBefore;
                                                $parameterModuleColorBefore->codigo_parametro = $codeParameterColorBefore;
                                                $parameterModuleColorBefore->codigo_estado = Estado::getState('active');
                                                $parameterModuleColorBefore->valor_parametro_modulo = $valueModuleColorBefore;
                                                $parameterModuleColorBefore->save();
                                            } else {
                                                ParametroModulo::where('codigo_institucion', '=', $request->codigo_institucion)
                                                ->where('codigo_modulo', '=', $codeModuleColorBefore)
                                                ->where('codigo_parametro', '=', $codeParameterColorBefore)
                                                ->update(['valor_parametro_modulo' => $valueModuleColorBefore]);
                                            }
                                        }
                                    }
                                }

                                if ($request->has('background_icon')) {
                                    foreach ($request->background_icon as $item) {
                                        $explModulesBackgroundIcon = explode("-", $item);
                                        $explValueBackgroundIcon = explode(":", $item);
                                        $valueModuleBackgroundIcon = $explValueBackgroundIcon[1];
                                        $codeModuleBackgroundIcon = $explModulesBackgroundIcon[1];
                                        $codeParameterBackgroundIcon = $explModulesBackgroundIcon[2];

                                        $visibiliadInstitucionModulos = VisibilidadInstitucion::where('codigo_institucion', '=', $request->codigo_institucion)->where('codigo_modulo', '=', $codeModuleBackgroundIcon)->where('valor_visibilidad_institucion', '=', true)->first();

                                        if ($visibiliadInstitucionModulos instanceof VisibilidadInstitucion) {
                                            $parameterModuleBackgroundIcon = ParametroModulo::where('codigo_institucion', '=', $request->codigo_institucion)->where('codigo_modulo', '=', $codeModuleBackgroundIcon)->where('codigo_parametro', '=', $codeParameterBackgroundIcon)->first();

                                            if (!$parameterModuleBackgroundIcon instanceof ParametroModulo) {
                                                $parameterModuleBackgroundIcon = new ParametroModulo();
                                                $parameterModuleBackgroundIcon->codigo_institucion = $request->codigo_institucion;
                                                $parameterModuleBackgroundIcon->codigo_modulo = $codeModuleBackgroundIcon;
                                                $parameterModuleBackgroundIcon->codigo_parametro = $codeParameterBackgroundIcon;
                                                $parameterModuleBackgroundIcon->codigo_estado = Estado::getState('active');
                                                $parameterModuleBackgroundIcon->valor_parametro_modulo = $valueModuleBackgroundIcon;
                                                $parameterModuleBackgroundIcon->save();
                                            } else {
                                                ParametroModulo::where('codigo_institucion', '=', $request->codigo_institucion)
                                                ->where('codigo_modulo', '=', $codeModuleBackgroundIcon)
                                                ->where('codigo_parametro', '=', $codeParameterBackgroundIcon)
                                                ->update(['valor_parametro_modulo' => $valueModuleBackgroundIcon]);
                                            }
                                        }
                                    }
                                }

                                if ($request->has('color')) {
                                    foreach ($request->color as $item) {
                                        $explModulesColor = explode("-", $item);
                                        $explValueColor = explode(":", $item);
                                        $valueModuleColor = $explValueColor[1];
                                        $codeModuleColor = $explModulesColor[1];
                                        $codeParameterColor = $explModulesColor[2];

                                        $visibiliadInstitucionModulos = VisibilidadInstitucion::where('codigo_institucion', '=', $request->codigo_institucion)->where('codigo_modulo', '=', $codeModuleColor)->where('valor_visibilidad_institucion', '=', true)->first();

                                        if ($visibiliadInstitucionModulos instanceof VisibilidadInstitucion) {
                                            $parameterModuleColor = ParametroModulo::where('codigo_institucion', '=', $request->codigo_institucion)->where('codigo_modulo', '=', $codeModuleColor)->where('codigo_parametro', '=', $codeParameterColor)->first();

                                            if (!$parameterModuleColor instanceof ParametroModulo) {
                                                $parameterModuleColor = new ParametroModulo();
                                                $parameterModuleColor->codigo_institucion = $request->codigo_institucion;
                                                $parameterModuleColor->codigo_modulo = $codeModuleColor;
                                                $parameterModuleColor->codigo_parametro = $codeParameterColor;
                                                $parameterModuleColor->codigo_estado = Estado::getState('active');
                                                $parameterModuleColor->valor_parametro_modulo = $valueModuleColor;
                                                $parameterModuleColor->save();
                                            } else {
                                                ParametroModulo::where('codigo_institucion', '=', $request->codigo_institucion)
                                                ->where('codigo_modulo', '=', $codeModuleColor)
                                                ->where('codigo_parametro', '=', $codeParameterColor)
                                                ->update(['valor_parametro_modulo' => $valueModuleColor]);
                                            }
                                        }
                                    }
                                }

                                if ($request->has('fontsize')) {
                                    foreach ($request->fontsize as $item) {
                                        $explModulesFontSize = explode("-", $item);
                                        $explValueFontSize = explode(":", $item);
                                        $valueModuleFontSize = $explValueFontSize[1];
                                        $codeModuleFontSize = $explModulesFontSize[1];
                                        $codeParameterFontSize = $explModulesFontSize[2];

                                        $visibiliadInstitucionModulos = VisibilidadInstitucion::where('codigo_institucion', '=', $request->codigo_institucion)->where('codigo_modulo', '=', $codeModuleFontSize)->where('valor_visibilidad_institucion', '=', true)->first();

                                        if ($visibiliadInstitucionModulos instanceof VisibilidadInstitucion) {
                                           
                                                $parameterModuleFontSize = ParametroModulo::where('codigo_institucion', '=', $request->codigo_institucion)->where('codigo_modulo', '=', $codeModuleFontSize)->where('codigo_parametro', '=', $codeParameterFontSize)->first();

                                                if (!$parameterModuleFontSize instanceof ParametroModulo) {
                                                    $parameterModuleFontSize = new ParametroModulo();
                                                    $parameterModuleFontSize->codigo_institucion = $request->codigo_institucion;
                                                    $parameterModuleFontSize->codigo_modulo = $codeModuleFontSize;
                                                    $parameterModuleFontSize->codigo_parametro = $codeParameterFontSize;
                                                    $parameterModuleFontSize->codigo_estado = Estado::getState('active');
                                                    $parameterModuleFontSize->valor_parametro_modulo = $valueModuleFontSize;
                                                    $parameterModuleFontSize->save();
                                                } else {
                                                    ParametroModulo::where('codigo_institucion', '=', $request->codigo_institucion)
                                                    ->where('codigo_modulo', '=', $codeModuleFontSize)
                                                    ->where('codigo_parametro', '=', $codeParameterFontSize)
                                                    ->update(['valor_parametro_modulo' => $valueModuleFontSize]);
                                                }
                                            
                                        }
                                    }
                                }

                                if ($request->has('text')) {
                                    foreach ($request->text as $item) {
                                        $explModulesText = explode("-", $item);
                                        $explValueText = explode(":", $item);
                                        $valueModuleText = $explValueText[1];
                                        $codeModuleText = $explModulesText[1];
                                        $codeParameterText = $explModulesText[2];

                                        $visibiliadInstitucionModulos = VisibilidadInstitucion::where('codigo_institucion', '=', $request->codigo_institucion)->where('codigo_modulo', '=', $codeModuleText)->where('valor_visibilidad_institucion', '=', true)->first();

                                        if ($visibiliadInstitucionModulos instanceof VisibilidadInstitucion) {
                                            
                                                $parameterModuleText = ParametroModulo::where('codigo_institucion', '=', $request->codigo_institucion)->where('codigo_modulo', '=', $codeModuleText)->where('codigo_parametro', '=', $codeParameterText)->first();

                                                if (!$parameterModuleText instanceof ParametroModulo) {
                                                    $parameterModuleText = new ParametroModulo();
                                                    $parameterModuleText->codigo_institucion = $request->codigo_institucion;
                                                    $parameterModuleText->codigo_modulo = $codeModuleText;
                                                    $parameterModuleText->codigo_parametro = $codeParameterText;
                                                    $parameterModuleText->codigo_estado = Estado::getState('active');
                                                    $parameterModuleText->valor_parametro_modulo = $valueModuleText;
                                                    $parameterModuleText->save();
                                                } else {
                                                    ParametroModulo::where('codigo_institucion', '=', $request->codigo_institucion)
                                                    ->where('codigo_modulo', '=', $codeModuleText)
                                                    ->where('codigo_parametro', '=', $codeParameterText)
                                                    ->update(['valor_parametro_modulo' => $valueModuleText]);
                                                }
                                            
                                        }
                                    }
                                }

                                if ($request->has('font_size_title')) {
                                    foreach ($request->font_size_title as $item) {
                                        $explModulesFontSize = explode("-", $item);
                                        $explValueFontSize = explode(":", $item);
                                        $valueModuleFontSize = $explValueFontSize[1];
                                        $codeModuleFontSize = $explModulesFontSize[1];
                                        $codeParameterFontSize = $explModulesFontSize[2];

                                        $visibiliadInstitucionModulos = VisibilidadInstitucion::where('codigo_institucion', '=', $request->codigo_institucion)->where('codigo_modulo', '=', $codeModuleFontSize)->where('valor_visibilidad_institucion', '=', true)->first();

                                        if ($visibiliadInstitucionModulos instanceof VisibilidadInstitucion) {
                                            
                                                $parameterModuleFontSize = ParametroModulo::where('codigo_institucion', '=', $request->codigo_institucion)->where('codigo_modulo', '=', $codeModuleFontSize)->where('codigo_parametro', '=', $codeParameterFontSize)->first();

                                                if (!$parameterModuleFontSize instanceof ParametroModulo) {
                                                    $parameterModuleFontSize = new ParametroModulo();
                                                    $parameterModuleFontSize->codigo_institucion = $request->codigo_institucion;
                                                    $parameterModuleFontSize->codigo_modulo = $codeModuleFontSize;
                                                    $parameterModuleFontSize->codigo_parametro = $codeParameterFontSize;
                                                    $parameterModuleFontSize->codigo_estado = Estado::getState('active');
                                                    $parameterModuleFontSize->valor_parametro_modulo = $valueModuleFontSize;
                                                    $parameterModuleFontSize->save();
                                                } else {
                                                    ParametroModulo::where('codigo_institucion', '=', $request->codigo_institucion)
                                                    ->where('codigo_modulo', '=', $codeModuleFontSize)
                                                    ->where('codigo_parametro', '=', $codeParameterFontSize)
                                                    ->update(['valor_parametro_modulo' => $valueModuleFontSize]);
                                                }
                                            
                                        }
                                    }
                                }

                                if ($request->has('position')) {
                                    foreach ($request->position as $item) {
                                        $explModulesPosition = explode("-", $item);
                                        $explValuePosition = explode(":", $item);
                                        $valueModulePosition = $explValuePosition[1];
                                        $codeModulePosition = $explModulesPosition[1];
                                        $codeParameterPosition = $explModulesPosition[2];

                                        $visibiliadInstitucionModulos = VisibilidadInstitucion::where('codigo_institucion', '=', $request->codigo_institucion)->where('codigo_modulo', '=', $codeModulePosition)->where('valor_visibilidad_institucion', '=', true)->first();

                                        if ($visibiliadInstitucionModulos instanceof VisibilidadInstitucion) {
                                            
                                                $parameterModulePosition = ParametroModulo::where('codigo_institucion', '=', $request->codigo_institucion)->where('codigo_modulo', '=', $codeModulePosition)->where('codigo_parametro', '=', $codeParameterPosition)->first();

                                                if (!$parameterModulePosition instanceof ParametroModulo) {
                                                    $parameterModulePosition = new ParametroModulo();
                                                    $parameterModulePosition->codigo_institucion = $request->codigo_institucion;
                                                    $parameterModulePosition->codigo_modulo = $codeModulePosition;
                                                    $parameterModulePosition->codigo_parametro = $codeParameterPosition;
                                                    $parameterModulePosition->codigo_estado = Estado::getState('active');
                                                    $parameterModulePosition->valor_parametro_modulo = $valueModulePosition;
                                                    $parameterModulePosition->save();
                                                } else {
                                                    ParametroModulo::where('codigo_institucion', '=', $request->codigo_institucion)
                                                    ->where('codigo_modulo', '=', $codeModulePosition)
                                                    ->where('codigo_parametro', '=', $codeParameterPosition)
                                                    ->update(['valor_parametro_modulo' => $valueModulePosition]);
                                                }
                                            
                                        }
                                    }
                                }

                                if ($request->has('title')) {
                                    foreach ($request->title as $item) {
                                        $explModulesTitle = explode("-", $item);
                                        $explValueTitle = explode(":", $item);
                                        $valueModuleTitle = $explValueTitle[1];
                                        $codeModuleTitle = $explModulesTitle[1];
                                        $codeParameterTitle = $explModulesTitle[2];

                                        $visibiliadInstitucionModulos = VisibilidadInstitucion::where('codigo_institucion', '=', $request->codigo_institucion)->where('codigo_modulo', '=', $codeModuleTitle)->where('valor_visibilidad_institucion', '=', true)->first();

                                        if ($visibiliadInstitucionModulos instanceof VisibilidadInstitucion) {
                                            
                                                $parameterModuleTitle = ParametroModulo::where('codigo_institucion', '=', $request->codigo_institucion)->where('codigo_modulo', '=', $codeModuleTitle)->where('codigo_parametro', '=', $codeParameterTitle)->first();

                                                if (!$parameterModuleTitle instanceof ParametroModulo) {
                                                    $parameterModuleTitle = new ParametroModulo();
                                                    $parameterModuleTitle->codigo_institucion = $request->codigo_institucion;
                                                    $parameterModuleTitle->codigo_modulo = $codeModuleTitle;
                                                    $parameterModuleTitle->codigo_parametro = $codeParameterTitle;
                                                    $parameterModuleTitle->codigo_estado = Estado::getState('active');
                                                    $parameterModuleTitle->valor_parametro_modulo = $valueModuleTitle;
                                                    $parameterModuleTitle->save();
                                                } else {
                                                    ParametroModulo::where('codigo_institucion', '=', $request->codigo_institucion)
                                                    ->where('codigo_modulo', '=', $codeModuleTitle)
                                                    ->where('codigo_parametro', '=', $codeParameterTitle)
                                                    ->update(['valor_parametro_modulo' => $valueModuleTitle]);
                                                }
                                            
                                        }
                                    }
                                }
                            break;

                        }
                    }
                } else if ($request->has('terms') && !$request->has('update')) {
                    $termCondition = TerminosCondiciones::where('codigo_institucion', '=', $institution->codigo_institucion)->first();

                    if (!$termCondition instanceof TerminosCondiciones) {
                        $numTerms = TerminosCondiciones::getNumberOfTerms('all');

                        for ($i=1; $i <= $numTerms; $i++) { 
                           
                            switch ($i) {
                                case 1:
                                    $newTerms = new TerminosCondiciones();
                                    $newTerms->codigo_institucion = $institution->codigo_institucion;
                                    $newTerms->version_actual_terminos = 1;
                                    $newTerms->codigo_estado = Estado::getState('active');
                                    $newTerms->tipo_terminos = $i;
                                    $newTerms->titulo_terminos_condiciones = 'Términos y condiciones';
                                    $newTerms->save();
                                break;
                                case 2:
                                    $newTerms = new TerminosCondiciones();
                                    $newTerms->codigo_institucion = $institution->codigo_institucion;
                                    $newTerms->version_actual_terminos = 1;
                                    $newTerms->codigo_estado = Estado::getState('active');
                                    $newTerms->tipo_terminos = $i;
                                    $newTerms->titulo_terminos_condiciones = 'Políticas de privacidad';
                                    $newTerms->save();
                                break;
                                case 3:
                                    $newTerms = new TerminosCondiciones();
                                    $newTerms->codigo_institucion = $institution->codigo_institucion;
                                    $newTerms->version_actual_terminos = 1;
                                    $newTerms->codigo_estado = Estado::getState('active');
                                    $newTerms->tipo_terminos = $i;
                                    $newTerms->titulo_terminos_condiciones = 'Políticas de ventas';
                                    $newTerms->save();
                                break;
                                case 4:
                                    $newTerms = new TerminosCondiciones();
                                    $newTerms->codigo_institucion = $institution->codigo_institucion;
                                    $newTerms->version_actual_terminos = 1;
                                    $newTerms->codigo_estado = Estado::getState('active');
                                    $newTerms->tipo_terminos = $i;
                                    $newTerms->titulo_terminos_condiciones = 'Políticas Mentoria';
                                    $newTerms->save();
                                break;
                                case 5:
                                    $newTerms = new TerminosCondiciones();
                                    $newTerms->codigo_institucion = $institution->codigo_institucion;
                                    $newTerms->version_actual_terminos = 1;
                                    $newTerms->codigo_estado = Estado::getState('active');
                                    $newTerms->tipo_terminos = $i;
                                    $newTerms->titulo_terminos_condiciones = 'Políticas de evaluador';
                                    $newTerms->save();
                                break;
                            }
                        }

                        $terminosCondiciones = TerminosCondiciones::where('codigo_institucion', '=', $institution->codigo_institucion)->get();

                        if (count($terminosCondiciones) > 0) {
                            foreach ($terminosCondiciones as $key) {
                                switch ($key->tipo_terminos) {
                                    case 1:
                                        $newVersionTerminos = new VersionTerminos();
                                        $newVersionTerminos->codigo_terminos_condiciones = $key->codigo_terminos_condiciones;
                                        $newVersionTerminos->texto_version_terminos = $request->terminos_condiciones;
                                        $newVersionTerminos->numero_version_terminos = 1;
                                        $newVersionTerminos->fecha_inicio_terminos = date('Y-m-d');
                                        $newVersionTerminos->fecha_fin_terminos = $request->fecha_fin_termino_condiciones;
                                        $newVersionTerminos->save();
                                    break;

                                    case 2:
                                        $newVersionTerminos = new VersionTerminos();
                                        $newVersionTerminos->codigo_terminos_condiciones = $key->codigo_terminos_condiciones;
                                        $newVersionTerminos->texto_version_terminos = $request->politicas_privacidad;
                                        $newVersionTerminos->numero_version_terminos = 1;
                                        $newVersionTerminos->fecha_inicio_terminos = date('Y-m-d');
                                        $newVersionTerminos->fecha_fin_terminos = $request->fecha_fin_politicas_privacidad;
                                        $newVersionTerminos->save();
                                    break;

                                    case 3:
                                        $newVersionTerminos = new VersionTerminos();
                                        $newVersionTerminos->codigo_terminos_condiciones = $key->codigo_terminos_condiciones;
                                        $newVersionTerminos->texto_version_terminos = $request->politica_ventas;
                                        $newVersionTerminos->numero_version_terminos = 1;
                                        $newVersionTerminos->fecha_inicio_terminos = date('Y-m-d');
                                        $newVersionTerminos->fecha_fin_terminos = $request->fecha_fin_politica_ventas;
                                        $newVersionTerminos->save();
                                    break;

                                    case 4:
                                        $newVersionTerminos = new VersionTerminos();
                                        $newVersionTerminos->codigo_terminos_condiciones = $key->codigo_terminos_condiciones;
                                        $newVersionTerminos->texto_version_terminos = $request->politicas_mentoria;
                                        $newVersionTerminos->numero_version_terminos = 1;
                                        $newVersionTerminos->fecha_inicio_terminos = date('Y-m-d');
                                        $newVersionTerminos->fecha_fin_terminos = $request->fecha_fin_politica_mentoria;
                                        $newVersionTerminos->save();
                                    break;

                                    case 5:
                                        $newVersionTerminos = new VersionTerminos();
                                        $newVersionTerminos->codigo_terminos_condiciones = $key->codigo_terminos_condiciones;
                                        $newVersionTerminos->texto_version_terminos = $request->politicas_evaluadores;
                                        $newVersionTerminos->numero_version_terminos = 1;
                                        $newVersionTerminos->fecha_inicio_terminos = date('Y-m-d');
                                        $newVersionTerminos->fecha_fin_terminos = $request->fecha_fin_politica_evaluadores;
                                        $newVersionTerminos->save();
                                    break;
                                }
                            }
                        }

                    } else {
                        $terminosCondiciones = TerminosCondiciones::where('codigo_institucion', '=', $institution->codigo_institucion)->get();
                        if (count($terminosCondiciones) > 0) {
                            foreach ($terminosCondiciones as $key) {
                                $versionTerminos = VersionTerminos::getTerms($institution->codigo_institucion, $key->codigo_terminos_condiciones);

                                if (!$versionTerminos instanceof VersionTerminos) { 
                                    switch ($key->tipo_terminos) {
                                        case 1:
                                            $newVersionTerminos = new VersionTerminos();
                                            $newVersionTerminos->codigo_terminos_condiciones = $key->codigo_terminos_condiciones;
                                            $newVersionTerminos->texto_version_terminos = $request->terminos_condiciones;
                                            $newVersionTerminos->numero_version_terminos = 1;
                                            $newVersionTerminos->fecha_inicio_terminos = date('Y-m-d');
                                            $newVersionTerminos->fecha_fin_terminos = $request->fecha_fin_termino_condiciones;
                                            $newVersionTerminos->save();
                                        break;

                                        case 2:
                                            $newVersionTerminos = new VersionTerminos();
                                            $newVersionTerminos->codigo_terminos_condiciones = $key->codigo_terminos_condiciones;
                                            $newVersionTerminos->texto_version_terminos = $request->politicas_privacidad;
                                            $newVersionTerminos->numero_version_terminos = 1;
                                            $newVersionTerminos->fecha_inicio_terminos = date('Y-m-d');
                                            $newVersionTerminos->fecha_fin_terminos = $request->fecha_fin_politicas_privacidad;
                                            $newVersionTerminos->save();
                                        break;

                                        case 3:
                                            $newVersionTerminos = new VersionTerminos();
                                            $newVersionTerminos->codigo_terminos_condiciones = $key->codigo_terminos_condiciones;
                                            $newVersionTerminos->texto_version_terminos = $request->politica_ventas;
                                            $newVersionTerminos->numero_version_terminos = 1;
                                            $newVersionTerminos->fecha_inicio_terminos = date('Y-m-d');
                                            $newVersionTerminos->fecha_fin_terminos = $request->fecha_fin_politica_ventas;
                                            $newVersionTerminos->save();
                                        break;

                                        case 4:
                                            $newVersionTerminos = new VersionTerminos();
                                            $newVersionTerminos->codigo_terminos_condiciones = $key->codigo_terminos_condiciones;
                                            $newVersionTerminos->texto_version_terminos = $request->politicas_mentoria;
                                            $newVersionTerminos->numero_version_terminos = 1;
                                            $newVersionTerminos->fecha_inicio_terminos = date('Y-m-d');
                                            $newVersionTerminos->fecha_fin_terminos = $request->fecha_fin_politica_mentoria;
                                            $newVersionTerminos->save();
                                        break;

                                        case 5:
                                            $newVersionTerminos = new VersionTerminos();
                                            $newVersionTerminos->codigo_terminos_condiciones = $key->codigo_terminos_condiciones;
                                            $newVersionTerminos->texto_version_terminos = $request->politicas_evaluadores;
                                            $newVersionTerminos->numero_version_terminos = 1;
                                            $newVersionTerminos->fecha_inicio_terminos = date('Y-m-d');
                                            $newVersionTerminos->fecha_fin_terminos = $request->fecha_fin_politica_evaluadores;
                                            $newVersionTerminos->save();
                                        break;
                                    }

                                } else {
                                    switch ($key->tipo_terminos) {
                                        case 1:
                                            VersionTerminos::where('codigo_terminos_condiciones', '=', $key->codigo_terminos_condiciones)
                                                ->update(['texto_version_terminos' => $request->terminos_condiciones, 'fecha_fin_terminos' => $request->fecha_fin_termino_condiciones]);
                                        break;

                                        case 2:
                                            VersionTerminos::where('codigo_terminos_condiciones', '=', $key->codigo_terminos_condiciones)
                                                ->update(['texto_version_terminos' => $request->politicas_privacidad, 'fecha_fin_terminos' => $request->fecha_fin_politicas_privacidad]);
                                        break;

                                        case 3:
                                            VersionTerminos::where('codigo_terminos_condiciones', '=', $key->codigo_terminos_condiciones)
                                                ->update(['texto_version_terminos' => $request->politica_ventas, 'fecha_fin_terminos' => $request->fecha_fin_politica_ventas]);
                                        break;

                                        case 4:
                                            VersionTerminos::where('codigo_terminos_condiciones', '=', $key->codigo_terminos_condiciones)
                                                ->update(['texto_version_terminos' => $request->politicas_mentoria, 'fecha_fin_terminos' => $request->fecha_fin_politica_mentoria]);
                                        break;

                                        case 5:
                                            VersionTerminos::where('codigo_terminos_condiciones', '=', $key->codigo_terminos_condiciones)
                                                ->update(['texto_version_terminos' => $request->politicas_evaluadores, 'fecha_fin_terminos' => $request->fecha_fin_politica_evaluadores]);
                                        break;
                                    }
                                    
                                }  
                            }
                        }
                    }
                    
                }

                // Commit Transaction
				DB::commit();
				return response()->json(['success' => true, 'message' => 'La institucion ha sido editada correctamente!!', 'codigo_institucion' => $institution->codigo_institucion, 'codigo_estado' => $institution->codigo_estado]);
            } catch(\Exception $e) {
                DB::rollback();
				Log::error(sprintf('%s:%s', 'InstitucionController:update', $e->getMessage()));
				return response()->json(['success' => false, 'errors' => trans('app.exception')]);
            }
        }
    }

    /**
     * Remove the specified resource from storage.
     *
     * @param  int  $id
     * @return \Illuminate\Http\Response
     */
    public function destroy($id)
    {
        //
    }

    public function upload(Request $request) {
        if ($request->ajax()) {
            if ($request->hasFile('file')) {
                try {
                    $file = $request->file('file');
                    $request->has('codigo_institucion') ? $institucion_code = $request->codigo_institucion : $institucion_code = null;

                    if (isset($institucion_code)) {
                        
                        $institucion = Institucion::find($institucion_code);
                        if (!$institucion instanceof Institucion) {
                            abort(404);
                        }

                        $response = Adjunto::uploadInstitutionAttachment($file->getRealPath(), $file->getClientOriginalName(), $institucion_code, $institucion->nombre_institucion, 'images'); 


                        if ($request->has('type')) {
                            switch ($request->type) {
                                case 'logo':
                                    $institucion->logo_institucion = $response->file_path;
                                break;
                                case 'logoMail':
                                    $institucion->imagen_notificacion_mail = $response->file_path;
                                default;
                                case 'imageAdmin':
                                    $institucion->logo_admin_institucion = $response->file_path;
                                break;
                                case 'imageReport':
                                    $institucion->logo_reporte_institucion = $response->file_path;
                                break;
                                case 'imageLogoHeader':
                                    if (!$request->has('codigo_parametro')) {
                                        Log::error('No existe el codigo del parametro');
                                        return response()->json(['success' => false, 'errors' => Lang::get('app.exception')]);
                                    }

                                    $parametrosInstitucion = ParametroInstitucion::where('codigo_institucion', '=', $institucion_code)->where('codigo_parametro', '=', $request->codigo_parametro)->first();
                                    
                                    if ($parametrosInstitucion instanceof ParametroInstitucion) {
                                        $parametrosInstitucion->codigo_estado = Estado::getState('active');
                                        $parametrosInstitucion->valor_parametro = $response->file_path;
                                        $parametrosInstitucion->save();
                                    } else {
                                        $newparametrosInstitucion = new ParametroInstitucion();
                                        $newparametrosInstitucion->codigo_institucion = $institucion->codigo_institucion;
                                        $newparametrosInstitucion->codigo_parametro = $request->codigo_parametro;
                                        $newparametrosInstitucion->valor_parametro = $response->file_path;
                                        $newparametrosInstitucion->codigo_estado = Estado::getState('active');

                                        $newparametrosInstitucion->save();
                                    }

                                break;
                                case 'imageMeta':
                                    if (!$request->has('codigo_meta')) {
                                        Log::error('No existe el codigo de Meta');
                                        return response()->json(['success' => false, 'errors' => Lang::get('app.exception')]);
                                    }

                                    $metaInstitucion = MetaInstitucion::where('codigo_institucion', '=', $institucion_code)->where('codigo_meta', '=', $request->codigo_meta)->first();

                                    if ($metaInstitucion instanceof MetaInstitucion) {
                                        $metaInstitucion->codigo_estado = Estado::getState('active');
                                        $metaInstitucion->valor_meta_institucion = $response->file_path;
                                        $metaInstitucion->save();
                                    } else {
                                        $newMetaInstitucion = new MetaInstitucion();
                                        $newMetaInstitucion->codigo_institucion = $institucion->codigo_institucion;
                                        $newMetaInstitucion->codigo_meta = $request->codigo_meta;
                                        $newMetaInstitucion->codigo_estado = Estado::getState('active');
                                        $newMetaInstitucion->valor_meta_institucion = $response->file_path;

                                        $newMetaInstitucion->save();
                                    }

                                break;
                                case 'imageModule':
                                    if (!$request->has('codigo_modulo')) {
                                        Log::error('No existe el codigo del Modulo');
                                        return response()->json(['success' => false, 'errors' => Lang::get('app.exception')]);
                                    }
                                    
                                    if (!$request->has('codigo_parametro')) {
                                        Log::error('No existe el codigo del Parametro');
                                        return response()->json(['success' => false, 'errors' => Lang::get('app.exception')]);
                                    }

                                    $parametroModuloInstitucion = ParametroModulo::where('codigo_institucion', '=', $institucion_code)->where('codigo_modulo', '=', $request->codigo_modulo)->first();
                                    if ($parametroModuloInstitucion instanceof ParametroModulo) {
                                        ParametroModulo::where('codigo_institucion', '=', $institucion_code)
                                        ->where('codigo_modulo', '=', $request->codigo_modulo)
                                        ->update(['valor_parametro_modulo' => $response->file_path]);
                                    } else {

                                        $visibiliadInstitucionModulos = VisibilidadInstitucion::where('codigo_institucion', '=', $request->codigo_institucion)->where('codigo_modulo', '=', $request->codigo_modulo)->first();

                                        if (!$visibiliadInstitucionModulos instanceof VisibilidadInstitucion) {
                                            $visibiliadInstitucion = new VisibilidadInstitucion();
                                            $visibiliadInstitucion->codigo_institucion = $institucion_code;
                                            $visibiliadInstitucion->codigo_modulo = $request->codigo_modulo;
                                            $visibiliadInstitucion->codigo_estado = Estado::getState('active');
                                            $visibiliadInstitucion->valor_visibilidad_institucion = TRUE;
                                            $visibiliadInstitucion->save();
                                        }

                                        $newParametroModuloInstitucion = new ParametroModulo();
                                        $newParametroModuloInstitucion->codigo_institucion = $institucion_code;
                                        $newParametroModuloInstitucion->codigo_modulo = $request->codigo_modulo;
                                        $newParametroModuloInstitucion->codigo_parametro = $request->codigo_parametro;
                                        $newParametroModuloInstitucion->codigo_estado = Estado::getState('active');
                                        $newParametroModuloInstitucion->valor_parametro_modulo = $response->file_path;

                                        $newParametroModuloInstitucion->save();
                                    }
                                break;
                                case 'homepage':
                                    dd($request->all());
                                break;
                            }
                        }
                       
                        $institucion->save();

                        return response()->json(['success' => true, 'message' => 'Institucion editada correctamente', 'file_path' => $response->file_path]);
                    } else {
                        abort(404);
                    }
                    
                } catch (\Exception $e) {
                    Log::error($e->getMessage());
                    return response()->json(['success' => false, 'errors' => Lang::get('app.exception')]);
                }
            }
        }
    }

    public function viewAttachment(Request $request) {
        if ($request->ajax()) {
            
            $institucion = Institucion::find($request->codigo_institucion);
            $request->has('codigo_parametro') ? $parametro = $request->codigo_parametro : $parametro = null;
            $request->has('codigo_meta') ? $meta = $request->codigo_meta : $meta = null;
            $request->has('codigo_modulo') ? $modulo = $request->codigo_modulo : $modulo = null;
            $request->has('codigo_parametro') ? $parametro = $request->codigo_parametro : $parametro = null;

            if (!$institucion instanceof Institucion) {
                abort(404);
            }

            return response()->json(Adjunto::buildAttachmentInstitution($institucion, $request->type, $parametro, $meta, $modulo, $parametro));
        }
    }

    public function deleteAttachment(Request $request) {
        if ($request->ajax()) {
            dd('Entra');
            if ($request->has('codigo_institucion')) {
                $institucion = Institucion::find($request->codigo_institucion);

                if (!$institucion instanceof Institucion) {
                    abort(404);
                }

                if ($request->has('type')) {
                    switch ($request->type) {
                        case 'logo':
                            $institucion->logo_institucion = null;
                        break;

                        case 'logoMail':
                            $institucion->imagen_notificacion_mail = null;
                        break;
                        case 'imageAdmin':
                            $institucion->logo_admin_institucion = null;
                        break;
                        case 'imageReport':
                            $institucion->logo_reporte_institucion = null;
                        break;
                        case 'imageLogoHeader':
                            $parametrosInstitucion = ParametroInstitucion::where('codigo_institucion', '=', $request->codigo_institucion)->where('codigo_parametro', '=', $request->codigo_parametro)->where('codigo_estado', '=', Estado::getState('active'))->first();

                            if ($parametrosInstitucion instanceof ParametroInstitucion) {
                                $parametrosInstitucion->codigo_estado = Estado::getState('inactive');
                                $parametrosInstitucion->save();
                            }
                        break;
                        case 'imageMeta':
                            MetaInstitucion::where('codigo_institucion', '=', $request->codigo_institucion)
                                            ->where('codigo_meta', '=', $request->codigo_meta)
                                            ->update(['codigo_estado' => Estado::getState('inactive')]);
                        break;
                    }

                    $institucion->save();

                    return response()->json(['success' => true, 'message' => 'Institucion editada correctamente', 'codigo_institucion' => $institucion->codigo_institucion]);
                }
            }
        }
    }
}
