<?php
/*
|--------------------------------------------------------------------------
| Web Routes
|--------------------------------------------------------------------------
|
| Here is where you can register web routes for your application. These
| routes are loaded by the RouteServiceProvider within a group which
| contains the "web" middleware group. Now create something great!
|
*/
Route::get('login', ['as' => 'login', 'uses' => 'Auth\LoginController@showLoginForm']);
Route::post('login', ['uses' => 'Auth\LoginController@login']);
Route::get('logout', ['as' => 'auth.logout', 'uses' => 'Auth\LoginController@logout']);
/*
|--------------------------------------------------------------------------
| Secure Routes Application
|--------------------------------------------------------------------------
*/
Route::group(['middleware' => 'auth'], function () {
  Route::get('/', ['as' => 'dashboard', 'uses' => 'HomeController@index']);
  Route::get('valid', ['as' => 'usuario.valid.email', 'uses' => 'UsuarioController@verifyEmail']);
  Route::get('validIdent', ['as' => 'usuario.valid.identification', 'uses' => 'UsuarioController@verifyIdentification']);

  /*
   | Prefix Usuarios
   */
  Route::group(['prefix' => 'usuario', 'as' => 'usuario.'], function () {
    Route::get('search', ['as' => 'search', 'uses' => 'UsuarioController@search']);
    Route::get('searchEntities', ['as' => 'searchEntities', 'uses' => 'UsuarioController@searchEntities']);
    Route::get('unlinkEntities', ['as' => 'unlinkEntities', 'uses' => 'UsuarioController@unlinkEntities']);
    Route::get('exportar', ['as' => 'exportar', 'uses' => 'UsuarioController@exportar']);
    Route::get('detail/{user}', ['as' => 'detail', 'uses' => 'UsuarioController@userDetail']);
    Route::resource('profesion', 'ProfesionPersonaController', ['except' => ['destroy']]);
    Route::resource('ocupacion', 'OcupacionPersonaController', ['except' => ['destroy']]);
    Route::resource('rol', 'RolUsuarioController', ['only' => ['index', 'store', 'destroy']]);
    Route::resource('equipo', 'GrupoUsuarioController', ['only' => ['index', 'show']]);
    Route::group(['prefix' => 'mentoria', 'as' => 'mentoria.'], function () {
      Route::resource('sectoreconomico', 'SectorEconomicoController', ['only' => ['index', 'store', 'destroy']]);
      Route::resource('especialidades', 'EspecialidadUsuarioController', ['only' => ['index', 'store', 'destroy']]);
    });
  });

  Route::resource('usuario', 'UsuarioController', ['only' => ['index', 'create', 'update', 'store', 'show']]);

  /*
   | Prefix Roles
   */
  Route::group(['prefix' => 'rol', 'as' => 'rol.'], function () {
    Route::resource('usuario', 'UsuarioRolController', ['only' => ['index', 'store', 'destroy']]);
    Route::resource('permiso', 'PermisoModuloController', ['only' => ['index', 'store', 'destroy']]);
  });
  Route::resource('rol', 'RolController', ['except' => ['destroy']]);

  /*
   | Prefix Comunidad
   */
  Route::group(['prefix' => 'comunidad', 'as' => 'comunidad.'], function () {
    Route::group(['prefix' => 'categoria', 'as' => 'categoria.'], function () {
      Route::get('categories', ['as' => 'categories', 'uses' => 'CategoriaCanalController@categories']);
    });
    Route::group(['prefix' => 'canal', 'as' => 'canal.'], function () {
      Route::resource('usuario', 'CanalUsuarioController', ['only' => ['index', 'store', 'destroy', 'update']]);
      Route::resource('profesion', 'VinculoCanalController', ['only' => ['index', 'store', 'destroy']]);
    });
    Route::resource('categoria', 'CategoriaCanalController', ['except' => ['destroy']]);
    Route::resource('canal', 'CanalController', ['except' => ['destroy']]);
    Route::resource('publicacion', 'PublicacionCotroller', ['only' => ['index', 'store']]);
  });

  /*
   | Prefix mentoria
   */
  Route::group(['prefix' => 'mentoria', 'as' => 'mentoria.'], function () {
    Route::resource('mentores', 'MentorController', ['only' => ['index', 'update', 'show']]);
    Route::resource('mentorprofile', 'ProfileMentorController', ['only' => ['index', 'update', 'show']]);
    Route::resource('discusion', 'DiscusionMentoriaController', ['only' => ['index']]);
    Route::get('enviarterminosmentor/{usuario}', ['as' => 'terminos.enviar', 'uses' => 'ProfileMentorController@sendMentorTerms']);
    Route::resource('reporte', 'Reportes\ReporteMentoriaController', ['only' => ['index']]);
  });
  Route::resource('mentoria', 'AsesoriaController', ['only' => ['index', 'store', 'show', 'destroy', 'update']]);
  Route::get('mentoriasesiones', ['as' => 'mentoriasesiones', 'uses' => 'AsesoriaController@getSessionMentorings']);

  Route::group(['prefix' => 'mentorias', 'as' => 'mentorias.'], function () {
    Route::get('masivas/lista', 'MentoriaMasivaController@getMassiveMentoring')->name('masivas.lista');
    Route::resource('masivas', 'MentoriaMasivaController');
    Route::get('getMentor', ['as' => 'mentorias/getMentor', 'uses' => 'MentoriaMasivaController@getMentor']);
    Route::get('masivas/show/table/{mentor}', ['as' => 'mentorias/masivas/show/table', 'uses' => 'MentoriaMasivaController@show']);
    Route::post('sesiones', 'MentoriaMasivaController@storeSessions');
    Route::get('getTemasSesionList', 'MentoriaMasivaController@getTemasSesionList')->name('gettemassesionlist');
    Route::get('sesiones/table/{mentoriamasiva}', 'MentoriaMasivaController@getSessionsMass');
    Route::get('sesion/table/{mentoriamasiva}', 'MentoriaMasivaController@getSingleSession');

    Route::post('descripcion', 'MentoriaSesionesController@descriptionMentoring');
    Route::post('tasks', 'MentoriaSesionesController@TareaMentoria');
    Route::post('TagSesion', 'MentoriaSesionesController@TagSesionMentoria');
    Route::post('generalObjetive', 'MentoriaSesionesController@generalObjetive');
    Route::post('phase', 'MentoriaSesionesController@phaseMentoring');
    Route::post('diagnosis', 'MentoriaSesionesController@diagnosisMentoring');
    Route::post('recommends', 'MentoriaSesionesController@recommendsMentoring');
    Route::post('seguimientos', 'MentoriaSesionesController@tracingMentoring');
    Route::post('duracion', 'MentoriaSesionesController@durationMentoring');
    Route::post('finalizar', 'MentoriaSesionesController@finishMentoring');
    Route::post('cancelar', 'MentoriaSesionesController@cancelMentoring');
  });



  /*
     | Prefix convocatoria
   */
  Route::group(['prefix' => 'convocatoria', 'as' => 'convocatoria.'], function () {
    Route::get('search', ['as' => 'search', 'uses' => 'ConvocatoriaController@search']);
    Route::post('cancel', ['as' => 'cancel', 'uses' => 'ConvocatoriaController@cancelRegister']);
    Route::group(['prefix' => 'fase', 'as' => 'fase.'], function () {
      Route::group(['prefix' => 'documentador', 'as' => 'documentador.'], function () {
        Route::get('exportar/{register}', ['as' => 'exportar', 'uses' => 'DocumentadorController@exportar']);
      });
      Route::resource('documentador', 'DocumentadorController', ['only' => ['index', 'show', 'update']]);
      Route::resource('compromiso', 'CartaCompromisoController', ['only' => ['index', 'show', 'update']]);
      Route::post('orden', ['as' => 'orden', 'uses' => 'FaseController@order']);
      Route::post('orderAdjuntoFase', ['as' => 'orderAdjuntoFase', 'uses' => 'FaseController@orderAdjuntoFase']);
      Route::get('notificacion/{usuario}', ['as' => 'notificacion', 'uses' => 'FaseUsuarioController@notification']);
      Route::resource('recurso', 'FaseRecursoController', ['only' => ['index', 'store', 'update']]);
      Route::resource('voucher', 'FaseVoucherController', ['only' => ['index', 'show', 'store', 'update']]);
      Route::get('getMentorignsPhase', 'FaseVoucherController@getMentorignsPhase');
      Route::post('storeComment', 'FaseVoucherController@storeComment');
    });
    Route::group(['prefix' => 'respuestas', 'as' => 'respuestas.'], function () {
      Route::resource('evaluacion', 'RespuestaEvaluacionController', ['only' => ['index', 'show', 'update']]);
    });
    Route::group(['prefix' => 'carta', 'as' => 'carta.'], function () {
      Route::resource('llave', 'CartaLlaveController', ['only' => ['show']]);
    });

    Route::resource('fase', 'FaseController', ['only' => ['index', 'store', 'update']]);
    Route::resource('usuario', 'FaseUsuarioController', ['only' => ['index', 'store', 'update']]);
    Route::resource('reporte', 'Reportes\ConvocatoriaController', ['only' => ['index']]);
    Route::resource('categoria', 'CategoriaProcesoController', ['except' => ['destroy']]);
    Route::resource('convocatoriacategoria', 'ConvocatoriaCategoriaController', ['only' => ['index', 'store', 'destroy']]);
    Route::resource('convocatoriaetapa', 'ConvocatoriaEtapaController', ['only' => ['index', 'store', 'destroy']]);


    Route::group(['prefix' => 'registro', 'as' => 'registro.'], function () {
      Route::resource('usuario', 'RegistroConvocatoriaController', ['only' => ['index', 'store', 'update']]);
    });

    Route::get('{id}/asignacionmentor/{nickname}', 'ConvocatoriaController@assignmentMentorView');
    Route::get('getAssignments', 'ConvocatoriaController@getAssignments');
    Route::post('storementoria', 'ConvocatoriaController@storeMentoria');
    Route::post('deleteMentor', 'ConvocatoriaController@deleteMentor');

    Route::prefix('faqs')->group(function () {
      Route::get('{id}', 'PreguntasFrecuentesController@index')->name('faqs.index');

      Route::post('{program}', 'PreguntasFrecuentesController@store')->name('faqs.store');
      Route::patch('{program}/{id}', 'PreguntasFrecuentesController@update')->name('faqs.update');
      Route::delete('{program}/{id}', 'PreguntasFrecuentesController@destroy')->name('faqs.status');
    });
  });

  Route::resource('convocatoria', 'ConvocatoriaController', ['except' => ['destroy']]);
  Route::resource('categoriaproducto', 'CategoriaProductoController', ['only' => ['index']]);
  Route::resource('resultadoevaluacion', 'ResultadoEvaluacionController', ['only' => ['index']]);

  /*
   | Prefix Institution
   */

  Route::group(['prefix' => 'institucion', 'as' => 'institucion.'], function () {
    Route::post('upload', ['as' => 'upload', 'uses' => 'InstitucionController@upload']);
    Route::get('attachment', ['as' => 'attachment', 'uses' => 'InstitucionController@viewAttachment']);
    Route::delete('deleteAttach/{id}', ['as' => 'deleteAttach', 'uses' => 'InstitucionController@deleteAttachment']);
  });

  Route::resource('institucion', 'InstitucionController', ['except' => ['destroy']]);

  /*
   | Prefix entidad
   */
  Route::group(['prefix' => 'entidad', 'as' => 'entidad.'], function () {
    Route::get('search', ['as' => 'search', 'uses' => 'EntidadController@search']);
    Route::get('searchDataTable', ['as' => 'searchDatatable', 'uses' => 'EntidadController@searchDataTable']);
    Route::resource('etapa', 'EtapaProyectoController', ['except' => ['destroy']]);
    Route::resource('industria', 'CategoriaProyectoController', ['except' => ['destroy']]);
    Route::resource('reporte', 'Reportes\ReporteEntidadController', ['only' => ['index']]);
  });
  Route::resource('entidad', 'EntidadController', ['except' => ['destroy']]);
  //Route::resource('proyecto', 'ProyectoController', ['only' => ['index']]);

  /*
   | Prefix evaluacion
   */
  Route::group(['prefix' => 'evaluacion', 'as' => 'evaluacion.'], function () {
    Route::get('emprendimiento', ['as' => 'emprendimiento', 'uses' => 'EvaluacionEmprendimientoController@evaluations']);
    Route::get('exportar', ['as' => 'exportar', 'uses' => 'EvaluacionEmprendimientoController@exportar']);
    Route::get('exportarevaluacion/{purchase}', ['as' => 'exportarevaluacion', 'uses' => 'EvaluacionEmprendimientoController@exportarEvaluacion']);
    Route::resource('asignacion', 'AsignacionJuradoController', ['only' => ['index', 'show']]);
    Route::get('evaluacionescomentarios', ['as' => 'evaluacionescomentarios', 'uses' => 'EvaluacionComentarioController@evaluations']);
    Route::resource('comentario', 'EvaluacionComentarioController', ['except' => ['delete']]);
    Route::resource('pregunta', 'PreguntaEvaluacionController', ['only' => ['index', 'store', 'update', 'destroy']]);

  });
  Route::resource('evaluacion', 'EvaluacionEmprendimientoController', ['only' => ['index', 'show', 'create', 'store', 'edit', 'update']]);

   /*
   | Prefix itemevaluacionemprendimiento
   */
  Route::group(['prefix' => 'itemevaluacionemprendimiento', 'as' => 'itemevaluacionemprendimiento.'], function () {

  });
  Route::resource('itemevaluacionemprendimiento', 'ItemEvaluacionEmprendimientoController', ['only' => ['index', 'show', 'create', 'store', 'edit']]);

  /*
   | Prefix course
   */
  Route::group(['prefix' => 'curso', 'as' => 'curso.'], function () {
    Route::get('search', ['as' => 'search', 'uses' => 'CursoController@search']);
    Route::group(['prefix' => 'tema', 'as' => 'tema.'], function () {
      Route::group(['prefix' => 'contenido', 'as' => 'contenido.'], function () {
        Route::post('orden', ['as' => 'orden', 'uses' => 'ContenidoCursoController@order']);
      });
      Route::resource('contenido', 'ContenidoCursoController', ['only' => ['index', 'store', 'update']]);
    });
    Route::resource('tema', 'TemaCursoController', ['only' => ['index', 'store', 'update']]);

    Route::group(['prefix' => 'evaluacion', 'as' => 'evaluacion.'], function () {
      Route::resource('exportar', 'Reportes\FormularioController', ['only' => ['index']]);
    });
    Route::resource('evaluacion', 'EvaluacionCursoController', ['except' => ['destroy', 'create', 'store']]);

  });
  Route::resource('curso', 'CursoController', ['except' => ['destroy']]);

  /*
   | Prefix blog
   */
  Route::group(['prefix' => 'contenido', 'as' => 'contenido.'], function () {
    Route::get('search', ['as' => 'search', 'uses' => 'ContenidoController@search']);
  });
  Route::resource('contenido', 'ContenidoController', ['except' => ['destroy']]);

  /*
   | Prefix contenido canal
   */
  Route::resource('contenidocanal', 'ContenidoCanalController', ['only' => ['index', 'store', 'destroy']]);

  /*
   | Prefix file
   */
  Route::group(['prefix' => 'file'], function () {

    Route::post('temp', ['as' => 'file.temp', 'uses' => 'FileController@temp']);
    Route::post('crop', ['as' => 'file.crop', 'uses' => 'FileController@crop']);
    Route::post('upload', ['as' => 'file.upload', 'uses' => 'FileController@upload']);
    Route::post('course/upload', ['as' => 'file.course', 'uses' => 'FileController@uploadCourseAttachment']);
  });

  /*
   | Prefix Attached
   */
  Route::group(['prefix' => 'adjunto', 'as' => 'adjunto.'], function () {
    Route::resource('documentador', 'AdjuntoDocumentadorController', ['only' => ['index', 'store']]);
    Route::resource('usuario', 'AdjuntoUsuarioController', ['only' => ['index', 'store', 'update', 'destroy']]);
    Route::resource('proyecto', 'AdjuntoProyectoController', ['only' => ['index', 'store', 'update', 'destroy']]);
    Route::resource('evento', 'AdjuntoEventoController', ['only' => ['index', 'store', 'update', 'destroy']]);
    Route::resource('cursos', 'AdjuntoCursosController', ['only' => ['index', 'store', 'update', 'destroy']]);
    Route::resource('uploadexcel', 'CargaExcelController', ['only' => ['index', 'store', 'update', 'destroy']]);
    Route::get('viewFileErrors', 'CargaExcelController@viewFileErrors');
    Route::resource('excelentidades', 'CargaExcelEntidadesController', ['only' => ['index', 'store', 'update', 'destroy']]);
    Route::get('viewFileErrorsEntities', 'CargaExcelEntidadesController@viewFileErrorsEntities');
  });
  Route::resource('adjuntos', 'AdjuntoConvocatoriaController', ['only' => ['index', 'store', 'update', 'destroy']]);

  /*
   | Prefix marketplace
   */
  Route::group(['prefix' => 'marketplace', 'as' => 'marketplace.'], function () {
    Route::group(['prefix' => 'transacciones', 'as' => 'transacciones.'], function () {
      /*
         | Prefix Agreement
         */
      Route::group(['prefix' => 'acuerdo', 'as' => 'acuerdo.'], function () {
        Route::get('exportar/{acuerdo}', ['as' => 'exportar', 'uses' => 'AcuerdoCompraController@exportar']);
        Route::resource('actividad', 'ActividadAcuerdoController', ['only' => ['index', 'store', 'update', 'destroy']]);
        Route::resource('entregable', 'EntregableAcuerdoController', ['only' => ['index', 'store', 'update', 'destroy']]);
        Route::resource('usuario', 'UsuarioAcuerdoController', ['only' => ['index', 'store', 'destroy']]);
        Route::resource('empresario', 'UsuarioEmpresarioController', ['only' => ['index', 'store', 'update', 'destroy']]);
      });
      Route::post('entregable/updatedeliverables', 'EntregableAcuerdoController@updateDeliverables');
      Route::resource('discusion', 'DiscusionAcuerdoController', ['only' => ['index', 'store', 'destroy']]);
      Route::resource('acuerdo', 'AcuerdoCompraController', ['only' => ['index', 'show', 'store', 'update']]);
      Route::resource('evaluacion', 'EvaluacionCompraController', ['only' => ['index', 'update', 'show', 'destroy']]);
      Route::get('factura', ['as' => 'factura', 'uses' => 'EvaluacionCompraController@bill']);
      Route::post('facturauploader', ['as' => 'facturauploader', 'uses' => 'EvaluacionCompraController@billUploader']);
      Route::get('exportar', ['as' => 'exportar', 'uses' => 'CompraController@exportar']);
    });
    Route::resource('transacciones', 'CompraController', ['only' => ['index']]);
    Route::resource('compra', 'CompraController', ['only' => ['index', 'show', 'store', 'update']]);
    Route::get('exportar', ['as' => 'exportar', 'uses' => 'ProductoController@exportar']);
    Route::resource('vouchers', 'DetalleVoucherController', ['only' => ['index', 'show']]);
    Route::resource('asignaciones', 'AsignacionMarketplaceController', ['only' => ['index', 'show', 'store', 'destroy']]);
    Route::resource('promociones', 'PromocionController', ['only' => ['index', 'show', 'update']]);
  });
  Route::resource('marketplace', 'ProductoController', ['only' => ['index', 'show', 'update']]);

  /*
   | Prefix asignacion
   */
  Route::resource('asignacion', 'AsignacionController', ['only' => ['index', 'store', 'update', 'destroy']]);
  Route::resource('asignacionautomatica', 'AsignacionAutomaticaController', ['only' => ['index', 'store', 'update', 'destroy']]);


  /*
   | Prefix discusion
   */
  Route::group(['prefix' => 'discusion', 'as' => 'discusion.'], function () {
    Route::resource('comentario', 'ComentarioDiscusionController', ['only' => ['index', 'store', 'destroy']]);
  });

  /*
   | Prefix voucher
   */
  Route::resource('voucher', 'VoucherController', ['only' => ['index', 'store', 'update']]);

  /*
|-------------------------
| Reports Routes
|-------------------------
*/
  Route::group(['prefix' => 'reportes', 'as' => 'reportes.'], function () {
    Route::resource('empodera', 'Reportes\Innpulsa\ReportEmpoderaController', ['only' => ['index', 'store']]);
    Route::resource('monitoreo12', 'Reportes\Monitoreo12Controller', ['only' => ['index', 'store']]);
    Route::resource('reto3', 'Reportes\Reto3Controller', ['only' => ['index', 'store']]);
    Route::resource('reto4', 'Reportes\Reto4Controller', ['only' => ['index', 'store']]);
    Route::resource('cursos', 'Reportes\CursoController', ['only' => ['index', 'store']]);
    Route::resource('cursoReporteIndividual', 'Reportes\CursoIndividualController');
    Route::resource('unicoempo', 'Reportes\UnicoEmpoderaController', ['only' => ['index', 'store']]);
    Route::resource('unicoterp', 'Reportes\UnicoTerpelController', ['only' => ['index', 'store']]);
    Route::resource('monitoreo', 'Reportes\MonitoreoController', ['only' => ['index', 'store']]);
    Route::resource('evaluaciones', 'Reportes\EvaluacionController', ['only' => ['index', 'store']]);
    Route::resource('seguimiento', 'Reportes\SeguimientoConvocatoriaController', ['only' => ['index', 'store']]);
    Route::resource('votos', 'Reportes\VotoController', ['only' => ['index', 'store']]);
    Route::resource('usuarios', 'Reportes\ReporteUsuarioController', ['only' => ['index', 'store']]);
    Route::resource('gestion', 'Reportes\GestionController', ['only' => ['index', 'destroy']]);
    Route::resource('eventos', 'Reportes\Compensar\ReporteGeneralEventos', ['only' => ['index', 'store']]);
    Route::resource('evaluationgeneral', 'Reportes\Innpulsa\EvaluacionGeneralController', ['only' => ['index', 'store']]);
    Route::resource('mentorias', 'Reportes\ReporteGeneralMentorias', ['only' => ['index', 'store']]);
    Route::resource('sesiones', 'Reportes\ReporteGeneralSesiones', ['only' => ['index', 'store']]);
    Route::resource('proyectos', 'Reportes\ProyectoController', ['only' => ['index', 'store']]);
    Route::group(['prefix' => 'dinamiza', 'as' => 'dinamiza.'], function () {
      Route::resource('usuarios', 'Reportes\Dinamiza\ReporteUsuarioController', ['only' => ['index', 'store']]);
      Route::resource('programas', 'Reportes\Dinamiza\ReporteProgramaController', ['only' => ['index', 'store']]);
    });

    Route::group(['prefix' => 'rutan', 'as' => 'rutan.'], function () {
      Route::resource('convocatorias', 'Reportes\Rutan\ReporteConvocatoriaController', ['only' => ['index', 'store']]);
    });
  });


  /*
   * Prefix Ciudades
   */
  Route::group(['prefix' => 'ciudades', 'as' => 'ciudades.'], function () {
    Route::group(['prefix' => 'localidades', 'as' => 'localidades.'], function () {
      Route::resource('barrios', 'BarrioLocalidadController', ['only' => ['index']]);
    });
    Route::resource('localidades', 'LocalidadCiudadController', ['only' => ['index']]);
  });
  Route::resource('ciudades', 'CiudadController', ['only' => ['index']]);

  /*
   * Privacidad contenido
   */
  Route::resource('privacidad', 'PrivacidadContenidoController', ['only' => ['index']]);

  /*
   * Prefix formulario
   */
  Route::group(['prefix' => 'formulario', 'as' => 'formulario.'], function () {
    Route::group(['prefix' => 'respuestas', 'as' => 'respuestas.'], function () {
      Route::get('exportar/{usersurvey}', ['as' => 'exportar', 'uses' => 'RespuestaController@exportar']);
      Route::get('pdf/{register}', ['as' => 'pdf', 'uses' => 'RespuestaController@exportarPDF']);
      Route::get('exportarservices/{usersurvey}', ['as' => 'exportSpecializedService', 'uses' => 'RespuestaController@exportSpecializedService']);
    });
    Route::group(['prefix' => 'pregunta', 'as' => 'pregunta.'], function () {
      Route::resource('opcion', 'OpcionPreguntaEncuestaController', ['only' => ['index', 'store', 'update', 'destroy']]);
    });

    Route::group(['prefix' => 'evaluacion', 'as' => 'evaluacion.'], function () {
      Route::resource('exportar', 'Reportes\FormularioController', ['only' => ['index']]);
    });

    Route::group(['prefix' => 'diagnostico', 'as' => 'diagnostico.'], function () {
      Route::get('radar/{usersurvey}', ['as' => 'radar', 'uses' => 'DiagnosisController@exportRadarDiagram']);
    });

    Route::resource('version', 'VersionFormularioController', ['only' => ['index', 'store']]);
    Route::resource('pregunta', 'PreguntaFormularioController', ['only' => ['index', 'store', 'update', 'destroy']]);
    Route::resource('grupo', 'GrupoPreguntaEncuestaController', ['only' => ['index', 'store', 'update', 'destroy', 'show']]);
    Route::resource('respuesta', 'UsuarioEncuestaController', ['only' => ['index']]);
    Route::resource('paginas', 'PaginasEncuestaController', ['only' => ['index', 'store', 'update']]);
    Route::resource('subpreguntas', 'SubPreguntasController', ['only' => ['index', 'store', 'update']]);
  });
  Route::resource('formulario', 'FormularioController', ['except' => ['destroy']]);

  /*
   * Prefix foro
   */
  Route::group(['prefix' => 'foro', 'as' => 'foro.'], function () {
    Route::resource('categoria', 'CategoriaForoController', ['except' => ['edit']]);
  });

  Route::resource('foro', 'ForoController', ['only' => ['index', 'update', 'show']]);


  /*
   * Prefix Notificaciones
   */
  Route::group(['prefix' => 'notificacion', 'as' => 'notificacion.'], function () {
    Route::resource('usuario', 'DetalleNotificacionUsuarioController', ['only' => ['index', 'store']]);
    Route::resource('output', 'NotificacionOutputController', ['only' => ['index', 'create', 'store', 'update', 'destroy']]);
  });

  /*
   | Prefix Evento
   */
  Route::group(['prefix' => 'evento', 'as' => 'evento.'], function () {
    Route::group(['prefix' => 'respuestas', 'as' => 'respuestas.'], function () {
      Route::get('exportar/{evento}', ['as' => 'exportar', 'uses' => 'EventoController@exportar']);
    });
    Route::get('organizadorev', ['as' => 'organizerev', 'uses' => 'OrganizadorEventoController@organizerEvent']);
    Route::get('search', ['as' => 'search', 'uses' => 'EventoController@search']);
    Route::get('eventtype', ['as' => 'eventtype', 'uses' => 'EventoController@getEventType']);
    Route::resource('organizador', 'OrganizadorEventoController', ['only' => ['index', 'store', 'destroy']]);
    Route::resource('participante', 'UsuarioEventoController', ['only' => ['index', 'store', 'destroy']]);
    Route::resource('asistencia', 'ControlAsistenciaController', ['only' => ['index', 'store', 'destroy']]);
    Route::resource('beneficio', 'ListaBeneficioEventoController', ['only' => ['index', 'store', 'destroy']]);
    Route::post('certificado', ['as' => 'certificado', 'uses' => 'EventoController@certificado']);
    Route::post('duplicar', ['as' => 'duplicar', 'uses' => 'EventoController@duplicar']);
    Route::group(['prefix' => 'usuario', 'as' => 'usuario.'], function () {
      Route::resource('invitacion', 'InvitacionEventoController', ['only' => ['index', 'store']]);
    });

    Route::group(['prefix' => 'sesion', 'as' => 'sesion.'], function () {
      Route::post('orden', ['as' => 'orden', 'uses' => 'SesionEventoController@order']);
    });
    Route::resource('sesion', 'SesionEventoController', ['only' => ['index', 'store', 'update']]);
    Route::resource('eventocategoria', 'EventoCategoriaController', ['only' => ['index', 'store', 'destroy']]);
    Route::resource('eventoetapa', 'EventoEtapaController', ['only' => ['index', 'store', 'destroy']]);
  });
  Route::resource('evento', 'EventoController', ['except' => ['destroy']]);
  Route::resource('eventos/usuario', 'EventosUsuarioController', [
    'names' => [
      'index' => 'eventos.usuario.index',
      'show' => 'eventos.usuario.show',
      // etc...
    ]
  ]);

  /**
   * Beneficio
   */
  Route::resource('beneficio', 'BeneficioEventoController', ['only' => ['index', 'store']]);

  /*
   | Patrocinador
   */
  Route::resource('patrocinador', 'PatrocinadorController');

  /*
   | patrocinador curso
   */
  Route::resource('patrocinadorCurso', 'PatrocinadorCursoController');

  /*
   | Log actividad
   */
  Route::resource('actividad', 'LogActividadController', ['only' => 'index']);

  /*
   | Prefix Tag
   */
  Route::resource('tag', 'TagController', ['only' => ['index']]);

  /* Rutas para modulo de reportes*/
  Route::group(['prefix' => 'reportes', 'as' => 'reportes.'], function () {

  });
  Route::resource('reportes', 'ReporteController');

  /* Rutas para modulo de Dianogsticos*/
  Route::group(['prefix' => 'diagnosticos', 'as' => 'diagnosticos.'], function () {
    Route::resource('madurez', 'NivelMadurezController', ['only' => ['index', 'store', 'edit', 'update', 'destroy', 'show']]);
    Route::resource('unidades', 'UnidadesProductivasController', ['only' => ['index', 'store', 'edit', 'update', 'destroy']]);
    Route::resource('preguntasunidad', 'PreguntasUnidadProductivaController', ['only' => ['index', 'store', 'edit', 'update', 'destroy']]);
  });
  Route::resource('diagnosticos', 'DiagnosticosController');

  Route::group(['prefix' => 'asignacion-diagnostico', 'as' => 'asignacion-diagnostico.'], function () {
  });
  Route::resource('asignacion-diagnostico', 'ModuloDiagnosticoController');
});
