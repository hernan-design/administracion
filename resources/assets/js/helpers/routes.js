(function () {

    var laroute = (function () {

        var routes = {

            absolute: false,
            rootUrl: 'http://localhost',
            routes :
            [
                {
                    "host": null,
                    "methods": [
                        "GET",
                        "HEAD"
                    ],
                    "uri": "login",
                    "name": "login",
                    "action":"App\Http\Controllers\Auth\LoginController@showLoginForm"
                },
                {
                    "host": null,
                    "methods": [
                        "POST"
                    ],
                    "uri": "login",
                    "name": null,
                    "action":"App\Http\Controllers\Auth\LoginController@login"
                },
                {
                    "host": null,
                    "methods": [
                        "GET",
                        "HEAD"
                    ],
                    "uri": "logout",
                    "name": "auth.logout",
                    "action":"App\Http\Controllers\Auth\LoginController@logout"
                },
                {
                    "host": null,
                    "methods": [
                        "GET",
                        "HEAD"
                    ],
                    "uri": "\/",
                    "name": "dashboard",
                    "action":"App\Http\Controllers\HomeController@index"
                },
                {
                    "host": null,
                    "methods": [
                        "GET",
                        "HEAD"
                    ],
                    "uri": "valid",
                    "name": "usuario.valid.email",
                    "action":"App\Http\Controllers\UsuarioController@verifyEmail"
                },
                {
                    "host": null,
                    "methods": [
                        "GET",
                        "HEAD"
                    ],
                    "uri": "validIdent",
                    "name": "usuario.valid.identification",
                    "action":"App\Http\Controllers\UsuarioController@verifyIdentification"
                },
                {
                    "host": null,
                    "methods": [
                        "GET",
                        "HEAD"
                    ],
                    "uri": "usuario\/search",
                    "name": "usuario.search",
                    "action":"App\Http\Controllers\UsuarioController@search"
                },
                {
                    "host": null,
                    "methods": [
                        "GET",
                        "HEAD"
                    ],
                    "uri": "usuario\/searchEntities",
                    "name": "usuario.searchEntities",
                    "action":"App\Http\Controllers\UsuarioController@searchEntities"
                },
                {
                    "host": null,
                    "methods": [
                        "GET",
                        "HEAD"
                    ],
                    "uri": "usuario\/unlinkEntities",
                    "name": "usuario.unlinkEntities",
                    "action":"App\Http\Controllers\UsuarioController@unlinkEntities"
                },
                {
                    "host": null,
                    "methods": [
                        "GET",
                        "HEAD"
                    ],
                    "uri": "usuario\/exportar",
                    "name": "usuario.exportar",
                    "action":"App\Http\Controllers\UsuarioController@exportar"
                },
                {
                    "host": null,
                    "methods": [
                        "GET",
                        "HEAD"
                    ],
                    "uri": "usuario\/detail\/{user}",
                    "name": "usuario.detail",
                    "action":"App\Http\Controllers\UsuarioController@userDetail"
                },
                {
                    "host": null,
                    "methods": [
                        "GET",
                        "HEAD"
                    ],
                    "uri": "usuario\/profesion",
                    "name": "usuario.profesion.index",
                    "action":"App\Http\Controllers\ProfesionPersonaController@index"
                },
                {
                    "host": null,
                    "methods": [
                        "GET",
                        "HEAD"
                    ],
                    "uri": "usuario\/profesion\/create",
                    "name": "usuario.profesion.create",
                    "action":"App\Http\Controllers\ProfesionPersonaController@create"
                },
                {
                    "host": null,
                    "methods": [
                        "POST"
                    ],
                    "uri": "usuario\/profesion",
                    "name": "usuario.profesion.store",
                    "action":"App\Http\Controllers\ProfesionPersonaController@store"
                },
                {
                    "host": null,
                    "methods": [
                        "GET",
                        "HEAD"
                    ],
                    "uri": "usuario\/profesion\/{profesion}",
                    "name": "usuario.profesion.show",
                    "action":"App\Http\Controllers\ProfesionPersonaController@show"
                },
                {
                    "host": null,
                    "methods": [
                        "GET",
                        "HEAD"
                    ],
                    "uri": "usuario\/profesion\/{profesion}\/edit",
                    "name": "usuario.profesion.edit",
                    "action":"App\Http\Controllers\ProfesionPersonaController@edit"
                },
                {
                    "host": null,
                    "methods": [
                        "PUT",
                        "PATCH"
                    ],
                    "uri": "usuario\/profesion\/{profesion}",
                    "name": "usuario.profesion.update",
                    "action":"App\Http\Controllers\ProfesionPersonaController@update"
                },
                {
                    "host": null,
                    "methods": [
                        "GET",
                        "HEAD"
                    ],
                    "uri": "usuario\/ocupacion",
                    "name": "usuario.ocupacion.index",
                    "action":"App\Http\Controllers\OcupacionPersonaController@index"
                },
                {
                    "host": null,
                    "methods": [
                        "GET",
                        "HEAD"
                    ],
                    "uri": "usuario\/ocupacion\/create",
                    "name": "usuario.ocupacion.create",
                    "action":"App\Http\Controllers\OcupacionPersonaController@create"
                },
                {
                    "host": null,
                    "methods": [
                        "POST"
                    ],
                    "uri": "usuario\/ocupacion",
                    "name": "usuario.ocupacion.store",
                    "action":"App\Http\Controllers\OcupacionPersonaController@store"
                },
                {
                    "host": null,
                    "methods": [
                        "GET",
                        "HEAD"
                    ],
                    "uri": "usuario\/ocupacion\/{ocupacion}",
                    "name": "usuario.ocupacion.show",
                    "action":"App\Http\Controllers\OcupacionPersonaController@show"
                },
                {
                    "host": null,
                    "methods": [
                        "GET",
                        "HEAD"
                    ],
                    "uri": "usuario\/ocupacion\/{ocupacion}\/edit",
                    "name": "usuario.ocupacion.edit",
                    "action":"App\Http\Controllers\OcupacionPersonaController@edit"
                },
                {
                    "host": null,
                    "methods": [
                        "PUT",
                        "PATCH"
                    ],
                    "uri": "usuario\/ocupacion\/{ocupacion}",
                    "name": "usuario.ocupacion.update",
                    "action":"App\Http\Controllers\OcupacionPersonaController@update"
                },
                {
                    "host": null,
                    "methods": [
                        "GET",
                        "HEAD"
                    ],
                    "uri": "usuario\/rol",
                    "name": "usuario.rol.index",
                    "action":"App\Http\Controllers\RolUsuarioController@index"
                },
                {
                    "host": null,
                    "methods": [
                        "POST"
                    ],
                    "uri": "usuario\/rol",
                    "name": "usuario.rol.store",
                    "action":"App\Http\Controllers\RolUsuarioController@store"
                },
                {
                    "host": null,
                    "methods": [
                        "DELETE"
                    ],
                    "uri": "usuario\/rol\/{rol}",
                    "name": "usuario.rol.destroy",
                    "action":"App\Http\Controllers\RolUsuarioController@destroy"
                },
                {
                    "host": null,
                    "methods": [
                        "GET",
                        "HEAD"
                    ],
                    "uri": "usuario\/equipo",
                    "name": "usuario.equipo.index",
                    "action":"App\Http\Controllers\GrupoUsuarioController@index"
                },
                {
                    "host": null,
                    "methods": [
                        "GET",
                        "HEAD"
                    ],
                    "uri": "usuario\/equipo\/{equipo}",
                    "name": "usuario.equipo.show",
                    "action":"App\Http\Controllers\GrupoUsuarioController@show"
                },
                {
                    "host": null,
                    "methods": [
                        "GET",
                        "HEAD"
                    ],
                    "uri": "usuario\/mentoria\/sectoreconomico",
                    "name": "usuario.mentoria.sectoreconomico.index",
                    "action":"App\Http\Controllers\SectorEconomicoController@index"
                },
                {
                    "host": null,
                    "methods": [
                        "POST"
                    ],
                    "uri": "usuario\/mentoria\/sectoreconomico",
                    "name": "usuario.mentoria.sectoreconomico.store",
                    "action":"App\Http\Controllers\SectorEconomicoController@store"
                },
                {
                    "host": null,
                    "methods": [
                        "DELETE"
                    ],
                    "uri": "usuario\/mentoria\/sectoreconomico\/{sectoreconomico}",
                    "name": "usuario.mentoria.sectoreconomico.destroy",
                    "action":"App\Http\Controllers\SectorEconomicoController@destroy"
                },
                {
                    "host": null,
                    "methods": [
                        "GET",
                        "HEAD"
                    ],
                    "uri": "usuario\/mentoria\/especialidades",
                    "name": "usuario.mentoria.especialidades.index",
                    "action":"App\Http\Controllers\EspecialidadUsuarioController@index"
                },
                {
                    "host": null,
                    "methods": [
                        "POST"
                    ],
                    "uri": "usuario\/mentoria\/especialidades",
                    "name": "usuario.mentoria.especialidades.store",
                    "action":"App\Http\Controllers\EspecialidadUsuarioController@store"
                },
                {
                    "host": null,
                    "methods": [
                        "DELETE"
                    ],
                    "uri": "usuario\/mentoria\/especialidades\/{especialidade}",
                    "name": "usuario.mentoria.especialidades.destroy",
                    "action":"App\Http\Controllers\EspecialidadUsuarioController@destroy"
                },
                {
                    "host": null,
                    "methods": [
                        "GET",
                        "HEAD"
                    ],
                    "uri": "usuario",
                    "name": "usuario.index",
                    "action":"App\Http\Controllers\UsuarioController@index"
                },
                {
                    "host": null,
                    "methods": [
                        "GET",
                        "HEAD"
                    ],
                    "uri": "usuario\/create",
                    "name": "usuario.create",
                    "action":"App\Http\Controllers\UsuarioController@create"
                },
                {
                    "host": null,
                    "methods": [
                        "POST"
                    ],
                    "uri": "usuario",
                    "name": "usuario.store",
                    "action":"App\Http\Controllers\UsuarioController@store"
                },
                {
                    "host": null,
                    "methods": [
                        "GET",
                        "HEAD"
                    ],
                    "uri": "usuario\/{usuario}",
                    "name": "usuario.show",
                    "action":"App\Http\Controllers\UsuarioController@show"
                },
                {
                    "host": null,
                    "methods": [
                        "PUT",
                        "PATCH"
                    ],
                    "uri": "usuario\/{usuario}",
                    "name": "usuario.update",
                    "action":"App\Http\Controllers\UsuarioController@update"
                },
                {
                    "host": null,
                    "methods": [
                        "GET",
                        "HEAD"
                    ],
                    "uri": "rol\/usuario",
                    "name": "rol.usuario.index",
                    "action":"App\Http\Controllers\UsuarioRolController@index"
                },
                {
                    "host": null,
                    "methods": [
                        "POST"
                    ],
                    "uri": "rol\/usuario",
                    "name": "rol.usuario.store",
                    "action":"App\Http\Controllers\UsuarioRolController@store"
                },
                {
                    "host": null,
                    "methods": [
                        "DELETE"
                    ],
                    "uri": "rol\/usuario\/{usuario}",
                    "name": "rol.usuario.destroy",
                    "action":"App\Http\Controllers\UsuarioRolController@destroy"
                },
                {
                    "host": null,
                    "methods": [
                        "GET",
                        "HEAD"
                    ],
                    "uri": "rol\/permiso",
                    "name": "rol.permiso.index",
                    "action":"App\Http\Controllers\PermisoModuloController@index"
                },
                {
                    "host": null,
                    "methods": [
                        "POST"
                    ],
                    "uri": "rol\/permiso",
                    "name": "rol.permiso.store",
                    "action":"App\Http\Controllers\PermisoModuloController@store"
                },
                {
                    "host": null,
                    "methods": [
                        "DELETE"
                    ],
                    "uri": "rol\/permiso\/{permiso}",
                    "name": "rol.permiso.destroy",
                    "action":"App\Http\Controllers\PermisoModuloController@destroy"
                },
                {
                    "host": null,
                    "methods": [
                        "GET",
                        "HEAD"
                    ],
                    "uri": "rol",
                    "name": "rol.index",
                    "action":"App\Http\Controllers\RolController@index"
                },
                {
                    "host": null,
                    "methods": [
                        "GET",
                        "HEAD"
                    ],
                    "uri": "rol\/create",
                    "name": "rol.create",
                    "action":"App\Http\Controllers\RolController@create"
                },
                {
                    "host": null,
                    "methods": [
                        "POST"
                    ],
                    "uri": "rol",
                    "name": "rol.store",
                    "action":"App\Http\Controllers\RolController@store"
                },
                {
                    "host": null,
                    "methods": [
                        "GET",
                        "HEAD"
                    ],
                    "uri": "rol\/{rol}",
                    "name": "rol.show",
                    "action":"App\Http\Controllers\RolController@show"
                },
                {
                    "host": null,
                    "methods": [
                        "GET",
                        "HEAD"
                    ],
                    "uri": "rol\/{rol}\/edit",
                    "name": "rol.edit",
                    "action":"App\Http\Controllers\RolController@edit"
                },
                {
                    "host": null,
                    "methods": [
                        "PUT",
                        "PATCH"
                    ],
                    "uri": "rol\/{rol}",
                    "name": "rol.update",
                    "action":"App\Http\Controllers\RolController@update"
                },
                {
                    "host": null,
                    "methods": [
                        "GET",
                        "HEAD"
                    ],
                    "uri": "comunidad\/categoria\/categories",
                    "name": "comunidad.categoria.categories",
                    "action":"App\Http\Controllers\CategoriaCanalController@categories"
                },
                {
                    "host": null,
                    "methods": [
                        "GET",
                        "HEAD"
                    ],
                    "uri": "comunidad\/canal\/usuario",
                    "name": "comunidad.canal.usuario.index",
                    "action":"App\Http\Controllers\CanalUsuarioController@index"
                },
                {
                    "host": null,
                    "methods": [
                        "POST"
                    ],
                    "uri": "comunidad\/canal\/usuario",
                    "name": "comunidad.canal.usuario.store",
                    "action":"App\Http\Controllers\CanalUsuarioController@store"
                },
                {
                    "host": null,
                    "methods": [
                        "PUT",
                        "PATCH"
                    ],
                    "uri": "comunidad\/canal\/usuario\/{usuario}",
                    "name": "comunidad.canal.usuario.update",
                    "action":"App\Http\Controllers\CanalUsuarioController@update"
                },
                {
                    "host": null,
                    "methods": [
                        "DELETE"
                    ],
                    "uri": "comunidad\/canal\/usuario\/{usuario}",
                    "name": "comunidad.canal.usuario.destroy",
                    "action":"App\Http\Controllers\CanalUsuarioController@destroy"
                },
                {
                    "host": null,
                    "methods": [
                        "GET",
                        "HEAD"
                    ],
                    "uri": "comunidad\/canal\/profesion",
                    "name": "comunidad.canal.profesion.index",
                    "action":"App\Http\Controllers\VinculoCanalController@index"
                },
                {
                    "host": null,
                    "methods": [
                        "POST"
                    ],
                    "uri": "comunidad\/canal\/profesion",
                    "name": "comunidad.canal.profesion.store",
                    "action":"App\Http\Controllers\VinculoCanalController@store"
                },
                {
                    "host": null,
                    "methods": [
                        "DELETE"
                    ],
                    "uri": "comunidad\/canal\/profesion\/{profesion}",
                    "name": "comunidad.canal.profesion.destroy",
                    "action":"App\Http\Controllers\VinculoCanalController@destroy"
                },
                {
                    "host": null,
                    "methods": [
                        "GET",
                        "HEAD"
                    ],
                    "uri": "comunidad\/categoria",
                    "name": "comunidad.categoria.index",
                    "action":"App\Http\Controllers\CategoriaCanalController@index"
                },
                {
                    "host": null,
                    "methods": [
                        "GET",
                        "HEAD"
                    ],
                    "uri": "comunidad\/categoria\/create",
                    "name": "comunidad.categoria.create",
                    "action":"App\Http\Controllers\CategoriaCanalController@create"
                },
                {
                    "host": null,
                    "methods": [
                        "POST"
                    ],
                    "uri": "comunidad\/categoria",
                    "name": "comunidad.categoria.store",
                    "action":"App\Http\Controllers\CategoriaCanalController@store"
                },
                {
                    "host": null,
                    "methods": [
                        "GET",
                        "HEAD"
                    ],
                    "uri": "comunidad\/categoria\/{categorium}",
                    "name": "comunidad.categoria.show",
                    "action":"App\Http\Controllers\CategoriaCanalController@show"
                },
                {
                    "host": null,
                    "methods": [
                        "GET",
                        "HEAD"
                    ],
                    "uri": "comunidad\/categoria\/{categorium}\/edit",
                    "name": "comunidad.categoria.edit",
                    "action":"App\Http\Controllers\CategoriaCanalController@edit"
                },
                {
                    "host": null,
                    "methods": [
                        "PUT",
                        "PATCH"
                    ],
                    "uri": "comunidad\/categoria\/{categorium}",
                    "name": "comunidad.categoria.update",
                    "action":"App\Http\Controllers\CategoriaCanalController@update"
                },
                {
                    "host": null,
                    "methods": [
                        "GET",
                        "HEAD"
                    ],
                    "uri": "comunidad\/canal",
                    "name": "comunidad.canal.index",
                    "action":"App\Http\Controllers\CanalController@index"
                },
                {
                    "host": null,
                    "methods": [
                        "GET",
                        "HEAD"
                    ],
                    "uri": "comunidad\/canal\/create",
                    "name": "comunidad.canal.create",
                    "action":"App\Http\Controllers\CanalController@create"
                },
                {
                    "host": null,
                    "methods": [
                        "POST"
                    ],
                    "uri": "comunidad\/canal",
                    "name": "comunidad.canal.store",
                    "action":"App\Http\Controllers\CanalController@store"
                },
                {
                    "host": null,
                    "methods": [
                        "GET",
                        "HEAD"
                    ],
                    "uri": "comunidad\/canal\/{canal}",
                    "name": "comunidad.canal.show",
                    "action":"App\Http\Controllers\CanalController@show"
                },
                {
                    "host": null,
                    "methods": [
                        "GET",
                        "HEAD"
                    ],
                    "uri": "comunidad\/canal\/{canal}\/edit",
                    "name": "comunidad.canal.edit",
                    "action":"App\Http\Controllers\CanalController@edit"
                },
                {
                    "host": null,
                    "methods": [
                        "PUT",
                        "PATCH"
                    ],
                    "uri": "comunidad\/canal\/{canal}",
                    "name": "comunidad.canal.update",
                    "action":"App\Http\Controllers\CanalController@update"
                },
                {
                    "host": null,
                    "methods": [
                        "GET",
                        "HEAD"
                    ],
                    "uri": "comunidad\/publicacion",
                    "name": "comunidad.publicacion.index",
                    "action":"App\Http\Controllers\PublicacionCotroller@index"
                },
                {
                    "host": null,
                    "methods": [
                        "POST"
                    ],
                    "uri": "comunidad\/publicacion",
                    "name": "comunidad.publicacion.store",
                    "action":"App\Http\Controllers\PublicacionCotroller@store"
                },
                {
                    "host": null,
                    "methods": [
                        "GET",
                        "HEAD"
                    ],
                    "uri": "mentoria\/mentores",
                    "name": "mentoria.mentores.index",
                    "action":"App\Http\Controllers\MentorController@index"
                },
                {
                    "host": null,
                    "methods": [
                        "GET",
                        "HEAD"
                    ],
                    "uri": "mentoria\/mentores\/{mentore}",
                    "name": "mentoria.mentores.show",
                    "action":"App\Http\Controllers\MentorController@show"
                },
                {
                    "host": null,
                    "methods": [
                        "PUT",
                        "PATCH"
                    ],
                    "uri": "mentoria\/mentores\/{mentore}",
                    "name": "mentoria.mentores.update",
                    "action":"App\Http\Controllers\MentorController@update"
                },
                {
                    "host": null,
                    "methods": [
                        "GET",
                        "HEAD"
                    ],
                    "uri": "mentoria\/mentorprofile",
                    "name": "mentoria.mentorprofile.index",
                    "action":"App\Http\Controllers\ProfileMentorController@index"
                },
                {
                    "host": null,
                    "methods": [
                        "GET",
                        "HEAD"
                    ],
                    "uri": "mentoria\/mentorprofile\/{mentorprofile}",
                    "name": "mentoria.mentorprofile.show",
                    "action":"App\Http\Controllers\ProfileMentorController@show"
                },
                {
                    "host": null,
                    "methods": [
                        "PUT",
                        "PATCH"
                    ],
                    "uri": "mentoria\/mentorprofile\/{mentorprofile}",
                    "name": "mentoria.mentorprofile.update",
                    "action":"App\Http\Controllers\ProfileMentorController@update"
                },
                {
                    "host": null,
                    "methods": [
                        "GET",
                        "HEAD"
                    ],
                    "uri": "mentoria\/discusion",
                    "name": "mentoria.discusion.index",
                    "action":"App\Http\Controllers\DiscusionMentoriaController@index"
                },
                {
                    "host": null,
                    "methods": [
                        "GET",
                        "HEAD"
                    ],
                    "uri": "mentoria\/enviarterminosmentor\/{usuario}",
                    "name": "mentoria.terminos.enviar",
                    "action":"App\Http\Controllers\ProfileMentorController@sendMentorTerms"
                },
                {
                    "host": null,
                    "methods": [
                        "GET",
                        "HEAD"
                    ],
                    "uri": "mentoria",
                    "name": "mentoria.index",
                    "action":"App\Http\Controllers\AsesoriaController@index"
                },
                {
                    "host": null,
                    "methods": [
                        "POST"
                    ],
                    "uri": "mentoria",
                    "name": "mentoria.store",
                    "action":"App\Http\Controllers\AsesoriaController@store"
                },
                {
                    "host": null,
                    "methods": [
                        "GET",
                        "HEAD"
                    ],
                    "uri": "mentoria\/{mentorium}",
                    "name": "mentoria.show",
                    "action":"App\Http\Controllers\AsesoriaController@show"
                },
                {
                    "host": null,
                    "methods": [
                        "DELETE"
                    ],
                    "uri": "mentoria\/{mentorium}",
                    "name": "mentoria.destroy",
                    "action":"App\Http\Controllers\AsesoriaController@destroy"
                },
                {
                    "host": null,
                    "methods": [
                        "GET",
                        "HEAD"
                    ],
                    "uri": "mentoriasesiones",
                    "name": "mentoria.sesiones",
                    "action":"App\Http\Controllers\AsesoriaController@getSessionMentorings"
                },
                {
                    "host": null,
                    "methods": [
                        "GET",
                        "HEAD"
                    ],
                    "uri": "mentoria\/reporte",
                    "name": "mentoria.reporte.index",
                    "action":"App\Http\Controllers\Reportes\ReporteMentoriaController@index"
                },
                {
                    "host": null,
                    "methods": [
                        "GET",
                        "HEAD"
                    ],
                    "uri": "mentorias\/masivas",
                    "name": "mentorias.masivas.index",
                    "action":"App\Http\Controllers\MentoriaMasivaController@index"
                },
                {
                    "host": null,
                    "methods": [
                        "POST"
                    ],
                    "uri": "mentorias\/masivas",
                    "name": "mentorias.masivas.store",
                    "action":"App\Http\Controllers\MentoriaMasivaController@store"
                },
                {
                    "host": null,
                    "methods": [
                        "GET",
                        "HEAD"
                    ],
                    "uri": "mentorias\/masivas\/{mentoria}",
                    "name": "mentorias.masivas.show",
                    "action":"App\Http\Controllers\MentoriaMasivaController@show"
                },
                {
                    "host": null,
                    "methods": [
                        "GET",
                        "HEAD"
                    ],
                    "uri": "mentorias\/masivas\/show\/table\/{mentor}",
                    "name": "mentorias.masivas.show.table",
                    "action":"App\Http\Controllers\MentoriaMasivaController@show"
                },
                {
                    "host": null,
                    "methods": [
                        "GET",
                        "HEAD"
                    ],
                    "uri": "mentorias\/getMentor",
                    "name": "mentorias.getMentor",
                    "action":"App\Http\Controllers\MentoriaMasivaController@getMentor"
                },
                {
                    "host": null,
                    "methods": [
                        "GET",
                        "HEAD"
                    ],
                    "uri": "mentorias\/masivas\/lista",
                    "name": "mentorias.masivas.lista",
                    "action":"App\Http\Controllers\MentoriaMasivaController@getMassiveMentoring"
                },
                {
                    "host": null,
                    "methods": [
                        "POST"
                    ],
                    "uri": "mentorias\/sesiones",
                    "name": "mentorias.sesiones.store",
                    "action":"App\Http\Controllers\MentoriaMasivaController@storeSessions"
                },
                {
                    "host": null,
                    "methods": [
                        "GET",
                        "HEAD"
                    ],
                    "uri": "mentorias\/sesiones\/table\/{mentoriamasiva}",
                    "name": "mentorias.sesiones.table",
                    "action":"App\Http\Controllers\MentoriaMasivaController@getSessionsMass"
                },
                {
                    "host": null,
                    "methods": [
                        "GET",
                        "HEAD"
                    ],
                    "uri": "mentorias\/sesion\/table\/{mentoriamasiva}",
                    "name": "mentorias.sesion.table",
                    "action":"App\Http\Controllers\MentoriaMasivaController@getSingleSession"
                },
                {
                    "host": null,
                    "methods": [
                        "POST"
                    ],
                    "uri": "mentorias\/descripcion",
                    "name": "mentorias.descripcion",
                    "action":"App\Http\Controllers\MentoriaSesionesController@descriptionMentoring"
                },
                {
                    "host": null,
                    "methods": [
                        "POST"
                    ],
                    "uri": "mentorias\/tasks",
                    "name": "mentorias.tasks",
                    "action":"App\Http\Controllers\MentoriaSesionesController@TareaMentoria"
                },
                {
                    "host": null,
                    "methods": [
                        "POST"
                    ],
                    "uri": "mentorias\/TagSesion",
                    "name": "mentorias.TagSesion",
                    "action":"App\Http\Controllers\MentoriaSesionesController@TagSesionMentoria"
                },
                {
                    "host": null,
                    "methods": [
                        "POST"
                    ],
                    "uri": "mentorias\/generalObjetive",
                    "name": "mentorias.generalObjetive",
                    "action":"App\Http\Controllers\MentoriaSesionesController@generalObjetive"
                },
                {
                    "host": null,
                    "methods": [
                        "POST"
                    ],
                    "uri": "mentorias\/duracion",
                    "name": "mentorias.duracion",
                    "action":"App\Http\Controllers\MentoriaSesionesController@durationMentoring"
                },
                {
                    "host": null,
                    "methods": [
                        "POST"
                    ],
                    "uri": "mentorias\/phase",
                    "name": "mentorias.phase",
                    "action":"App\Http\Controllers\MentoriaSesionesController@phaseMentoring"
                },
                {
                    "host": null,
                    "methods": [
                        "POST"
                    ],
                    "uri": "mentorias\/diagnosis",
                    "name": "mentorias.diagnosis",
                    "action":"App\Http\Controllers\MentoriaSesionesController@diagnosisMentoring"
                },
                {
                    "host": null,
                    "methods": [
                        "POST"
                    ],
                    "uri": "mentorias\/recommends",
                    "name": "mentorias.recommends",
                    "action":"App\Http\Controllers\MentoriaSesionesController@recommendsMentoring"
                },
                {
                    "host": null,
                    "methods": [
                        "POST"
                    ],
                    "uri": "mentorias\/seguimientos",
                    "name": "mentorias.seguimientos",
                    "action":"App\Http\Controllers\MentoriaSesionesController@tracingMentoring"
                },
                {
                    "host": null,
                    "methods": [
                        "POST"
                    ],
                    "uri": "mentorias\/finalizar",
                    "name": "mentorias.finalizar",
                    "action":"App\Http\Controllers\MentoriaSesionesController@finishMentoring"
                },
                {
                    "host": null,
                    "methods": [
                        "POST"
                    ],
                    "uri": "mentorias\/cancelar",
                    "name": "mentorias.cancelar",
                    "action":"App\Http\Controllers\MentoriaSesionesController@cancelMentoring"
                },
                {
                    "host": null,
                    "methods": [
                        "POST"
                    ],
                    "uri": "convocatoria\/storementoria",
                    "name": "convocatoria.storementoria",
                    "action":"App\Http\Controllers\ConvocatoriaController@storeMentoria"
                },
                {
                    "host": null,
                    "methods": [
                        "GET",
                        "HEAD"
                    ],
                    "uri": "convocatoria\/getAssignments",
                    "name": "convocatoria.getAssignments",
                    "action":"App\Http\Controllers\ConvocatoriaController@getAssignments"
                },
                {
                    "host": null,
                    "methods": [
                        "POST"
                    ],
                    "uri": "convocatoria\/deleteMentor",
                    "name": "convocatoria.deleteMentor",
                    "action":"App\Http\Controllers\ConvocatoriaController@deleteMentor"
                },
                // rutas archivo excel para carga de usuarios
                {
                    "host": null,
                    "methods": [
                        "GET",
                        "HEAD"
                    ],
                    "uri": "adjunto\/uploadexcel",
                    "name": "adjunto.uploadexcel.index",
                    "action":"App\Http\Controllers\CargaExcelController@index"
                },
                {
                    "host": null,
                    "methods": [
                        "POST"
                    ],
                    "uri": "adjunto\/uploadexcel",
                    "name": "adjunto.uploadexcel.store",
                    "action":"App\Http\Controllers\CargaExcelController@store"
                },
                {
                    "host": null,
                    "methods": [
                        "PUT",
                        "PATCH"
                    ],
                    "uri": "adjunto\/uploadexcel\/{usuario}",
                    "name": "adjunto.uploadexcel.update",
                    "action":"App\Http\Controllers\CargaExcelController@update"
                },
                {
                    "host": null,
                    "methods": [
                        "DELETE"
                    ],
                    "uri": "adjunto\/uploadexcel\/{usuario}",
                    "name": "adjunto.uploadexcel.destroy",
                    "action":"App\Http\Controllers\CargaExcelController@destroy"
                },
                {
                    "host": null,
                    "methods": [
                        "GET",
                        "HEAD"
                    ],
                    "uri": "adjunto\/viewFileErrors",
                    "name": "adjunto.viewFileErrors",
                    "action":"App\Http\Controllers\CargaExcelController@viewFileErrors"
                },
                //rutas para carga excel de usuarios/entidades
                {
                    "host": null,
                    "methods": [
                        "GET",
                        "HEAD"
                    ],
                    "uri": "adjunto\/excelentidades",
                    "name": "adjunto.excelentidades.index",
                    "action":"App\Http\Controllers\CargaExcelEntidadesController@index"
                },
                {
                    "host": null,
                    "methods": [
                        "POST"
                    ],
                    "uri": "adjunto\/excelentidades",
                    "name": "adjunto.excelentidades.store",
                    "action":"App\Http\Controllers\CargaExcelEntidadesController@store"
                },
                {
                    "host": null,
                    "methods": [
                        "PUT",
                        "PATCH"
                    ],
                    "uri": "adjunto\/excelentidades\/{usuario}",
                    "name": "adjunto.excelentidades.update",
                    "action":"App\Http\Controllers\CargaExcelEntidadesController@update"
                },
                {
                    "host": null,
                    "methods": [
                        "DELETE"
                    ],
                    "uri": "adjunto\/excelentidades\/{usuario}",
                    "name": "adjunto.excelentidades.destroy",
                    "action":"App\Http\Controllers\CargaExcelEntidadesController@destroy"
                },
                {
                    "host": null,
                    "methods": [
                        "GET",
                        "HEAD"
                    ],
                    "uri": "adjunto\/viewFileErrorsEntities",
                    "name": "adjunto.viewFileErrorsEntities",
                    "action":"App\Http\Controllers\CargaExcelEntidadesController@viewFileErrorsEntities"
                },
                {
                    "host": null,
                    "methods": [
                        "GET",
                        "HEAD"
                    ],
                    "uri": "convocatoria\/fase\/voucher",
                    "name": "convocatoria.fase.voucher.index",
                    "action":"App\Http\Controllers\FaseVoucherController@index"
                },
                {
                    "host": null,
                    "methods": [
                        "GET",
                        "HEAD"
                    ],
                    "uri": "convocatoria\/fase\/voucher\/{codigo_registro_fase_usuario}",
                    "name": "convocatoria.fase.voucher.show",
                    "action":"App\Http\Controllers\FaseVoucherController@show"
                },
                {
                    "host": null,
                    "methods": [
                        "PUT",
                        "PATCH"
                    ],
                    "uri": "convocatoria\/fase\/voucher\/{codigo_registro_fase_usuario}",
                    "name": "convocatoria.fase.voucher.update",
                    "action":"App\Http\Controllers\FaseVoucherController@update"
                },
                {
                    "host": null,
                    "methods": [
                        "GET",
                        "HEAD"
                    ],
                    "uri": "convocatoria\/fase\/getMentorignsPhase",
                    "name": "convocatoria.fase.voucher.getMentorignsPhase",
                    "action":"App\Http\Controllers\FaseVoucherController@getMentorignsPhase"
                },
                {
                    "host": null,
                    "methods": [
                        "POST"
                    ],
                    "uri": "convocatoria\/fase\/storeComment",
                    "name": "convocatoria.fase.voucher.storeComment",
                    "action":"App\Http\Controllers\FaseVoucherController@storeComment"
                },
                {
                    "host": null,
                    "methods": [
                        "GET",
                        "HEAD"
                    ],
                    "uri": "convocatoria\/search",
                    "name": "convocatoria.search",
                    "action":"App\Http\Controllers\ConvocatoriaController@search"
                },
                {
                    "host": null,
                    "methods": [
                        "POST"
                    ],
                    "uri": "convocatoria\/cancel",
                    "name": "convocatoria.cancel",
                    "action":"App\Http\Controllers\ConvocatoriaController@cancelRegister"
                },
                {
                    "host": null,
                    "methods": [
                        "GET",
                        "HEAD"
                    ],
                    "uri": "convocatoria\/fase\/documentador\/exportar\/{register}",
                    "name": "convocatoria.fase.documentador.exportar",
                    "action":"App\Http\Controllers\DocumentadorController@exportar"
                },
                {
                    "host": null,
                    "methods": [
                        "GET",
                        "HEAD"
                    ],
                    "uri": "convocatoria\/fase\/documentador",
                    "name": "convocatoria.fase.documentador.index",
                    "action":"App\Http\Controllers\DocumentadorController@index"
                },
                {
                    "host": null,
                    "methods": [
                        "GET",
                        "HEAD"
                    ],
                    "uri": "convocatoria\/fase\/documentador\/{documentador}",
                    "name": "convocatoria.fase.documentador.show",
                    "action":"App\Http\Controllers\DocumentadorController@show"
                },
                {
                    "host": null,
                    "methods": [
                        "PUT",
                        "PATCH"
                    ],
                    "uri": "convocatoria\/fase\/documentador\/{documentador}",
                    "name": "convocatoria.fase.documentador.update",
                    "action":"App\Http\Controllers\DocumentadorController@update"
                },
                {
                    "host": null,
                    "methods": [
                        "GET",
                        "HEAD"
                    ],
                    "uri": "convocatoria\/fase\/compromiso",
                    "name": "convocatoria.fase.compromiso.index",
                    "action":"App\Http\Controllers\CartaCompromisoController@index"
                },
                {
                    "host": null,
                    "methods": [
                        "GET",
                        "HEAD"
                    ],
                    "uri": "convocatoria\/fase\/compromiso\/{compromiso}",
                    "name": "convocatoria.fase.compromiso.show",
                    "action":"App\Http\Controllers\CartaCompromisoController@show"
                },
                {
                    "host": null,
                    "methods": [
                        "PUT",
                        "PATCH"
                    ],
                    "uri": "convocatoria\/fase\/compromiso\/{compromiso}",
                    "name": "convocatoria.fase.compromiso.update",
                    "action":"App\Http\Controllers\CartaCompromisoController@update"
                },
                {
                    "host": null,
                    "methods": [
                        "POST"
                    ],
                    "uri": "convocatoria\/fase\/orden",
                    "name": "convocatoria.fase.orden",
                    "action":"App\Http\Controllers\FaseController@order"
                },
                {
                    "host": null,
                    "methods": [
                        "POST"
                    ],
                    "uri": "convocatoria\/fase\/orderAdjuntoFase",
                    "name": "convocatoria.fase.orderAdjuntoFase",
                    "action":"App\Http\Controllers\FaseController@orderAdjuntoFase"
                },
                {
                    "host": null,
                    "methods": [
                        "GET",
                        "HEAD"
                    ],
                    "uri": "convocatoria\/fase\/notificacion\/{usuario}",
                    "name": "convocatoria.fase.notificacion",
                    "action":"App\Http\Controllers\FaseUsuarioController@notification"
                },
                {
                    "host": null,
                    "methods": [
                        "GET",
                        "HEAD"
                    ],
                    "uri": "convocatoria\/fase\/recurso",
                    "name": "convocatoria.fase.recurso.index",
                    "action":"App\Http\Controllers\FaseRecursoController@index"
                },
                {
                    "host": null,
                    "methods": [
                        "POST"
                    ],
                    "uri": "convocatoria\/fase\/recurso",
                    "name": "convocatoria.fase.recurso.store",
                    "action":"App\Http\Controllers\FaseRecursoController@store"
                },
                {
                    "host": null,
                    "methods": [
                        "PUT",
                        "PATCH"
                    ],
                    "uri": "convocatoria\/fase\/recurso\/{recurso}",
                    "name": "convocatoria.fase.recurso.update",
                    "action":"App\Http\Controllers\FaseRecursoController@update"
                },
                {
                    "host": null,
                    "methods": [
                        "GET",
                        "HEAD"
                    ],
                    "uri": "convocatoria\/respuestas\/evaluacion",
                    "name": "convocatoria.respuestas.evaluacion.index",
                    "action":"App\Http\Controllers\RespuestaEvaluacionController@index"
                },
                {
                    "host": null,
                    "methods": [
                        "GET",
                        "HEAD"
                    ],
                    "uri": "convocatoria\/respuestas\/evaluacion\/{evaluacion}",
                    "name": "convocatoria.respuestas.evaluacion.show",
                    "action":"App\Http\Controllers\RespuestaEvaluacionController@show"
                },
                {
                    "host": null,
                    "methods": [
                        "PUT",
                        "PATCH"
                    ],
                    "uri": "convocatoria\/respuestas\/evaluacion\/{evaluacion}",
                    "name": "convocatoria.respuestas.evaluacion.update",
                    "action":"App\Http\Controllers\RespuestaEvaluacionController@update"
                },
                {
                    "host": null,
                    "methods": [
                        "GET",
                        "HEAD"
                    ],
                    "uri": "convocatoria\/carta\/llave\/{llave}",
                    "name": "convocatoria.carta.llave.show",
                    "action":"App\Http\Controllers\CartaLlaveController@show"
                },
                {
                    "host": null,
                    "methods": [
                        "GET",
                        "HEAD"
                    ],
                    "uri": "convocatoria\/fase",
                    "name": "convocatoria.fase.index",
                    "action":"App\Http\Controllers\FaseController@index"
                },
                {
                    "host": null,
                    "methods": [
                        "POST"
                    ],
                    "uri": "convocatoria\/fase",
                    "name": "convocatoria.fase.store",
                    "action":"App\Http\Controllers\FaseController@store"
                },
                {
                    "host": null,
                    "methods": [
                        "PUT",
                        "PATCH"
                    ],
                    "uri": "convocatoria\/fase\/{fase}",
                    "name": "convocatoria.fase.update",
                    "action":"App\Http\Controllers\FaseController@update"
                },
                {
                    "host": null,
                    "methods": [
                        "GET",
                        "HEAD"
                    ],
                    "uri": "convocatoria\/usuario",
                    "name": "convocatoria.usuario.index",
                    "action":"App\Http\Controllers\FaseUsuarioController@index"
                },
                {
                    "host": null,
                    "methods": [
                        "POST"
                    ],
                    "uri": "convocatoria\/usuario",
                    "name": "convocatoria.usuario.store",
                    "action":"App\Http\Controllers\FaseUsuarioController@store"
                },
                {
                    "host": null,
                    "methods": [
                        "PUT",
                        "PATCH"
                    ],
                    "uri": "convocatoria\/usuario\/{usuario}",
                    "name": "convocatoria.usuario.update",
                    "action":"App\Http\Controllers\FaseUsuarioController@update"
                },
                {
                    "host": null,
                    "methods": [
                        "GET",
                        "HEAD"
                    ],
                    "uri": "convocatoria\/reporte",
                    "name": "convocatoria.reporte.index",
                    "action":"App\Http\Controllers\Reportes\ConvocatoriaController@index"
                },
                {
                    "host": null,
                    "methods": [
                        "GET",
                        "HEAD"
                    ],
                    "uri": "convocatoria\/categoria",
                    "name": "convocatoria.categoria.index",
                    "action":"App\Http\Controllers\CategoriaProcesoController@index"
                },
                {
                    "host": null,
                    "methods": [
                        "GET",
                        "HEAD"
                    ],
                    "uri": "convocatoria\/categoria\/create",
                    "name": "convocatoria.categoria.create",
                    "action":"App\Http\Controllers\CategoriaProcesoController@create"
                },
                {
                    "host": null,
                    "methods": [
                        "POST"
                    ],
                    "uri": "convocatoria\/categoria",
                    "name": "convocatoria.categoria.store",
                    "action":"App\Http\Controllers\CategoriaProcesoController@store"
                },
                {
                    "host": null,
                    "methods": [
                        "GET",
                        "HEAD"
                    ],
                    "uri": "convocatoria\/categoria\/{categorium}",
                    "name": "convocatoria.categoria.show",
                    "action":"App\Http\Controllers\CategoriaProcesoController@show"
                },
                {
                    "host": null,
                    "methods": [
                        "GET",
                        "HEAD"
                    ],
                    "uri": "convocatoria\/categoria\/{categorium}\/edit",
                    "name": "convocatoria.categoria.edit",
                    "action":"App\Http\Controllers\CategoriaProcesoController@edit"
                },
                {
                    "host": null,
                    "methods": [
                        "PUT",
                        "PATCH"
                    ],
                    "uri": "convocatoria\/categoria\/{categorium}",
                    "name": "convocatoria.categoria.update",
                    "action":"App\Http\Controllers\CategoriaProcesoController@update"
                },
                {
                    "host": null,
                    "methods": [
                        "GET",
                        "HEAD"
                    ],
                    "uri": "convocatoria\/convocatoriacategoria",
                    "name": "convocatoria.convocatoriacategoria.index",
                    "action":"App\Http\Controllers\ConvocatoriaCategoriaController@index"
                },
                {
                    "host": null,
                    "methods": [
                        "POST"
                    ],
                    "uri": "convocatoria\/convocatoriacategoria",
                    "name": "convocatoria.convocatoriacategoria.store",
                    "action":"App\Http\Controllers\ConvocatoriaCategoriaController@store"
                },
                {
                    "host": null,
                    "methods": [
                        "DELETE"
                    ],
                    "uri": "convocatoria\/convocatoriacategoria\/{convocatoriacategorium}",
                    "name": "convocatoria.convocatoriacategoria.destroy",
                    "action":"App\Http\Controllers\ConvocatoriaCategoriaController@destroy"
                },
                {
                    "host": null,
                    "methods": [
                        "GET",
                        "HEAD"
                    ],
                    "uri": "convocatoria\/convocatoriaetapa",
                    "name": "convocatoria.convocatoriaetapa.index",
                    "action":"App\Http\Controllers\ConvocatoriaEtapaController@index"
                },
                {
                    "host": null,
                    "methods": [
                        "POST"
                    ],
                    "uri": "convocatoria\/convocatoriaetapa",
                    "name": "convocatoria.convocatoriaetapa.store",
                    "action":"App\Http\Controllers\ConvocatoriaEtapaController@store"
                },
                {
                    "host": null,
                    "methods": [
                        "DELETE"
                    ],
                    "uri": "convocatoria\/convocatoriaetapa\/{convocatoriaetapa}",
                    "name": "convocatoria.convocatoriaetapa.destroy",
                    "action":"App\Http\Controllers\ConvocatoriaEtapaController@destroy"
                },
                {
                    "host": null,
                    "methods": [
                        "GET",
                        "HEAD"
                    ],
                    "uri": "convocatoria\/registro\/usuario",
                    "name": "convocatoria.registro.usuario.index",
                    "action":"App\Http\Controllers\RegistroConvocatoriaController@index"
                },
                {
                    "host": null,
                    "methods": [
                        "POST"
                    ],
                    "uri": "convocatoria\/registro\/usuario",
                    "name": "convocatoria.registro.usuario.store",
                    "action":"App\Http\Controllers\RegistroConvocatoriaController@store"
                },
                {
                    "host": null,
                    "methods": [
                        "PUT",
                        "PATCH"
                    ],
                    "uri": "convocatoria\/registro\/usuario\/{usuario}",
                    "name": "convocatoria.registro.usuario.update",
                    "action":"App\Http\Controllers\RegistroConvocatoriaController@update"
                },
                {
                    "host": null,
                    "methods": [
                        "GET",
                        "HEAD"
                    ],
                    "uri": "convocatoria",
                    "name": "convocatoria.index",
                    "action":"App\Http\Controllers\ConvocatoriaController@index"
                },
                {
                    "host": null,
                    "methods": [
                        "GET",
                        "HEAD"
                    ],
                    "uri": "convocatoria\/create",
                    "name": "convocatoria.create",
                    "action":"App\Http\Controllers\ConvocatoriaController@create"
                },
                {
                    "host": null,
                    "methods": [
                        "POST"
                    ],
                    "uri": "convocatoria",
                    "name": "convocatoria.store",
                    "action":"App\Http\Controllers\ConvocatoriaController@store"
                },
                {
                    "host": null,
                    "methods": [
                        "GET",
                        "HEAD"
                    ],
                    "uri": "convocatoria\/{convocatorium}",
                    "name": "convocatoria.show",
                    "action":"App\Http\Controllers\ConvocatoriaController@show"
                },
                {
                    "host": null,
                    "methods": [
                        "GET",
                        "HEAD"
                    ],
                    "uri": "convocatoria\/{convocatorium}\/edit",
                    "name": "convocatoria.edit",
                    "action":"App\Http\Controllers\ConvocatoriaController@edit"
                },
                {
                    "host": null,
                    "methods": [
                        "PUT",
                        "PATCH"
                    ],
                    "uri": "convocatoria\/{convocatorium}",
                    "name": "convocatoria.update",
                    "action":"App\Http\Controllers\ConvocatoriaController@update"
                },
                {
                    "host": null,
                    "methods": [
                        "GET",
                        "HEAD"
                    ],
                    "uri": "categoriaproducto",
                    "name": "categoriaproducto.index",
                    "action":"App\Http\Controllers\CategoriaProductoController@index"
                },
                {
                    "host": null,
                    "methods": [
                        "GET",
                        "HEAD"
                    ],
                    "uri": "resultadoevaluacion",
                    "name": "resultadoevaluacion.index",
                    "action":"App\Http\Controllers\ResultadoEvaluacionController@index"
                },
                {
                    "host": null,
                    "methods": [
                        "POST"
                    ],
                    "uri": "institucion\/upload",
                    "name": "institucion.upload",
                    "action":"App\Http\Controllers\InstitucionController@upload"
                },
                {
                    "host": null,
                    "methods": [
                        "GET",
                        "HEAD"
                    ],
                    "uri": "institucion\/attachment",
                    "name": "institucion.attachment",
                    "action":"App\Http\Controllers\InstitucionController@viewAttachment"
                },
                {
                    "host": null,
                    "methods": [
                        "DELETE"
                    ],
                    "uri": "institucion\/deleteAttach\/{id}",
                    "name": "institucion.deleteAttach",
                    "action":"App\Http\Controllers\InstitucionController@deleteAttachment"
                },
                {
                    "host": null,
                    "methods": [
                        "GET",
                        "HEAD"
                    ],
                    "uri": "institucion",
                    "name": "institucion.index",
                    "action":"App\Http\Controllers\InstitucionController@index"
                },
                {
                    "host": null,
                    "methods": [
                        "GET",
                        "HEAD"
                    ],
                    "uri": "institucion\/create",
                    "name": "institucion.create",
                    "action":"App\Http\Controllers\InstitucionController@create"
                },
                {
                    "host": null,
                    "methods": [
                        "POST"
                    ],
                    "uri": "institucion",
                    "name": "institucion.store",
                    "action":"App\Http\Controllers\InstitucionController@store"
                },
                {
                    "host": null,
                    "methods": [
                        "GET",
                        "HEAD"
                    ],
                    "uri": "institucion\/{institucion}",
                    "name": "institucion.show",
                    "action":"App\Http\Controllers\InstitucionController@show"
                },
                {
                    "host": null,
                    "methods": [
                        "GET",
                        "HEAD"
                    ],
                    "uri": "institucion\/{institucion}\/edit",
                    "name": "institucion.edit",
                    "action":"App\Http\Controllers\InstitucionController@edit"
                },
                {
                    "host": null,
                    "methods": [
                        "PUT",
                        "PATCH"
                    ],
                    "uri": "institucion\/{institucion}",
                    "name": "institucion.update",
                    "action":"App\Http\Controllers\InstitucionController@update"
                },
                {
                    "host": null,
                    "methods": [
                        "GET",
                        "HEAD"
                    ],
                    "uri": "entidad\/search",
                    "name": "entidad.search",
                    "action":"App\Http\Controllers\EntidadController@search"
                },
                {
                    "host": null,
                    "methods": [
                        "GET",
                        "HEAD"
                    ],
                    "uri": "entidad\/searchDataTable",
                    "name": "entidad.searchDatatable",
                    "action":"App\Http\Controllers\EntidadController@searchDataTable"
                },
                {
                    "host": null,
                    "methods": [
                        "GET",
                        "HEAD"
                    ],
                    "uri": "entidad\/etapa",
                    "name": "entidad.etapa.index",
                    "action":"App\Http\Controllers\EtapaProyectoController@index"
                },
                {
                    "host": null,
                    "methods": [
                        "GET",
                        "HEAD"
                    ],
                    "uri": "entidad\/etapa\/create",
                    "name": "entidad.etapa.create",
                    "action":"App\Http\Controllers\EtapaProyectoController@create"
                },
                {
                    "host": null,
                    "methods": [
                        "POST"
                    ],
                    "uri": "entidad\/etapa",
                    "name": "entidad.etapa.store",
                    "action":"App\Http\Controllers\EtapaProyectoController@store"
                },
                {
                    "host": null,
                    "methods": [
                        "GET",
                        "HEAD"
                    ],
                    "uri": "entidad\/etapa\/{etapa}",
                    "name": "entidad.etapa.show",
                    "action":"App\Http\Controllers\EtapaProyectoController@show"
                },
                {
                    "host": null,
                    "methods": [
                        "GET",
                        "HEAD"
                    ],
                    "uri": "entidad\/etapa\/{etapa}\/edit",
                    "name": "entidad.etapa.edit",
                    "action":"App\Http\Controllers\EtapaProyectoController@edit"
                },
                {
                    "host": null,
                    "methods": [
                        "PUT",
                        "PATCH"
                    ],
                    "uri": "entidad\/etapa\/{etapa}",
                    "name": "entidad.etapa.update",
                    "action":"App\Http\Controllers\EtapaProyectoController@update"
                },
                {
                    "host": null,
                    "methods": [
                        "GET",
                        "HEAD"
                    ],
                    "uri": "entidad\/industria",
                    "name": "entidad.industria.index",
                    "action":"App\Http\Controllers\CategoriaProyectoController@index"
                },
                {
                    "host": null,
                    "methods": [
                        "GET",
                        "HEAD"
                    ],
                    "uri": "entidad\/industria\/create",
                    "name": "entidad.industria.create",
                    "action":"App\Http\Controllers\CategoriaProyectoController@create"
                },
                {
                    "host": null,
                    "methods": [
                        "POST"
                    ],
                    "uri": "entidad\/industria",
                    "name": "entidad.industria.store",
                    "action":"App\Http\Controllers\CategoriaProyectoController@store"
                },
                {
                    "host": null,
                    "methods": [
                        "GET",
                        "HEAD"
                    ],
                    "uri": "entidad\/industria\/{industrium}",
                    "name": "entidad.industria.show",
                    "action":"App\Http\Controllers\CategoriaProyectoController@show"
                },
                {
                    "host": null,
                    "methods": [
                        "GET",
                        "HEAD"
                    ],
                    "uri": "entidad\/industria\/{industrium}\/edit",
                    "name": "entidad.industria.edit",
                    "action":"App\Http\Controllers\CategoriaProyectoController@edit"
                },
                {
                    "host": null,
                    "methods": [
                        "PUT",
                        "PATCH"
                    ],
                    "uri": "entidad\/industria\/{industrium}",
                    "name": "entidad.industria.update",
                    "action":"App\Http\Controllers\CategoriaProyectoController@update"
                },
                {
                    "host": null,
                    "methods": [
                        "GET",
                        "HEAD"
                    ],
                    "uri": "entidad",
                    "name": "entidad.index",
                    "action":"App\Http\Controllers\EntidadController@index"
                },
                {
                    "host": null,
                    "methods": [
                        "GET",
                        "HEAD"
                    ],
                    "uri": "entidad\/reporte",
                    "name": "entidad.reporte.index",
                    "action":"App\Http\Controllers\Reportes\ReporteEntidadController@index"
                },
                {
                    "host": null,
                    "methods": [
                        "GET",
                        "HEAD"
                    ],
                    "uri": "entidad\/create",
                    "name": "entidad.create",
                    "action":"App\Http\Controllers\EntidadController@create"
                },
                {
                    "host": null,
                    "methods": [
                        "POST"
                    ],
                    "uri": "entidad",
                    "name": "entidad.store",
                    "action":"App\Http\Controllers\EntidadController@store"
                },
                {
                    "host": null,
                    "methods": [
                        "GET",
                        "HEAD"
                    ],
                    "uri": "entidad\/{entidad}",
                    "name": "entidad.show",
                    "action":"App\Http\Controllers\EntidadController@show"
                },
                {
                    "host": null,
                    "methods": [
                        "GET",
                        "HEAD"
                    ],
                    "uri": "entidad\/{entidad}\/edit",
                    "name": "entidad.edit",
                    "action":"App\Http\Controllers\EntidadController@edit"
                },
                {
                    "host": null,
                    "methods": [
                        "PUT",
                        "PATCH"
                    ],
                    "uri": "entidad\/{entidad}",
                    "name": "entidad.update",
                    "action":"App\Http\Controllers\EntidadController@update"
                },
                {
                    "host": null,
                    "methods": [
                        "GET",
                        "HEAD"
                    ],
                    "uri": "evaluacion\/emprendimiento",
                    "name": "evaluacion.emprendimiento",
                    "action":"App\Http\Controllers\EvaluacionEmprendimientoController@evaluations"
                },
                {
                    "host": null,
                    "methods": [
                        "GET",
                        "HEAD"
                    ],
                    "uri": "evaluacion\/exportar",
                    "name": "evaluacion.exportar",
                    "action":"App\Http\Controllers\EvaluacionEmprendimientoController@exportar"
                },
                {
                    "host": null,
                    "methods": [
                        "GET",
                        "HEAD"
                    ],
                    "uri": "evaluacion\/exportarevaluacion\/{purchase}",
                    "name": "evaluacion.exportarevaluacion",
                    "action":"App\Http\Controllers\EvaluacionEmprendimientoController@exportarEvaluacion"
                },
                {
                    "host": null,
                    "methods": [
                        "GET",
                        "HEAD"
                    ],
                    "uri": "evaluacion\/asignacion",
                    "name": "evaluacion.asignacion.index",
                    "action":"App\Http\Controllers\AsignacionJuradoController@index"
                },
                {
                    "host": null,
                    "methods": [
                        "GET",
                        "HEAD"
                    ],
                    "uri": "evaluacion\/asignacion\/{asignacion}",
                    "name": "evaluacion.asignacion.show",
                    "action":"App\Http\Controllers\AsignacionJuradoController@show"
                },
                {
                    "host": null,
                    "methods": [
                        "GET",
                        "HEAD"
                    ],
                    "uri": "evaluacion\/evaluacionescomentarios",
                    "name": "evaluacion.evaluacionescomentarios",
                    "action":"App\Http\Controllers\EvaluacionComentarioController@evaluations"
                },
                {
                    "host": null,
                    "methods": [
                        "GET",
                        "HEAD"
                    ],
                    "uri": "evaluacion\/comentario",
                    "name": "evaluacion.comentario.index",
                    "action":"App\Http\Controllers\EvaluacionComentarioController@index"
                },
                {
                    "host": null,
                    "methods": [
                        "GET",
                        "HEAD"
                    ],
                    "uri": "evaluacion\/comentario\/create",
                    "name": "evaluacion.comentario.create",
                    "action":"App\Http\Controllers\EvaluacionComentarioController@create"
                },
                {
                    "host": null,
                    "methods": [
                        "POST"
                    ],
                    "uri": "evaluacion\/comentario",
                    "name": "evaluacion.comentario.store",
                    "action":"App\Http\Controllers\EvaluacionComentarioController@store"
                },
                {
                    "host": null,
                    "methods": [
                        "GET",
                        "HEAD"
                    ],
                    "uri": "evaluacion\/comentario\/{comentario}",
                    "name": "evaluacion.comentario.show",
                    "action":"App\Http\Controllers\EvaluacionComentarioController@show"
                },
                {
                    "host": null,
                    "methods": [
                        "GET",
                        "HEAD"
                    ],
                    "uri": "evaluacion\/comentario\/{comentario}\/edit",
                    "name": "evaluacion.comentario.edit",
                    "action":"App\Http\Controllers\EvaluacionComentarioController@edit"
                },
                {
                    "host": null,
                    "methods": [
                        "PUT",
                        "PATCH"
                    ],
                    "uri": "evaluacion\/comentario\/{comentario}",
                    "name": "evaluacion.comentario.update",
                    "action":"App\Http\Controllers\EvaluacionComentarioController@update"
                },
                {
                    "host": null,
                    "methods": [
                        "DELETE"
                    ],
                    "uri": "evaluacion\/comentario\/{comentario}",
                    "name": "evaluacion.comentario.destroy",
                    "action":"App\Http\Controllers\EvaluacionComentarioController@destroy"
                },
                {
                    "host": null,
                    "methods": [
                        "GET",
                        "HEAD"
                    ],
                    "uri": "evaluacion",
                    "name": "evaluacion.index",
                    "action":"App\Http\Controllers\EvaluacionEmprendimientoController@index"
                },
                {
                    "host": null,
                    "methods": [
                        "GET",
                        "HEAD"
                    ],
                    "uri": "itemevaluacionemprendimiento",
                    "name": "itemevaluacionemprendimiento.index",
                    "action":"App\Http\Controllers\ItemEvaluacionEmprendimientoController@index"
                },
                {
                    "host": null,
                    "methods": [
                        "GET",
                        "HEAD"
                    ],
                    "uri": "evaluacion\/{evaluacion}",
                    "name": "evaluacion.show",
                    "action":"App\Http\Controllers\EvaluacionEmprendimientoController@show"
                },
                {
                    "host": null,
                    "methods": [
                        "GET",
                        "HEAD"
                    ],
                    "uri": "itemevaluacionemprendimiento\/{itemevaluacion}",
                    "name": "itemevaluacionemprendimiento.show",
                    "action":"App\Http\Controllers\ItemEvaluacionEmprendimientoController@show"
                },
                {
                    "host": null,
                    "methods": [
                        "PUT",
                        "PATCH"
                    ],
                    "uri": "evaluacion",
                    "name": "evaluacion.update",
                    "action":"App\Http\Controllers\EvaluacionEmprendimientoController@update"
                },
                {
                    "host": null,
                    "methods": [
                        "GET",
                        "HEAD"
                    ],
                    "uri": "curso\/search",
                    "name": "curso.search",
                    "action":"App\Http\Controllers\CursoController@search"
                },
                {
                    "host": null,
                    "methods": [
                        "POST"
                    ],
                    "uri": "curso\/tema\/contenido\/orden",
                    "name": "curso.tema.contenido.orden",
                    "action":"App\Http\Controllers\ContenidoCursoController@order"
                },
                {
                    "host": null,
                    "methods": [
                        "GET",
                        "HEAD"
                    ],
                    "uri": "curso\/tema\/contenido",
                    "name": "curso.tema.contenido.index",
                    "action":"App\Http\Controllers\ContenidoCursoController@index"
                },
                {
                    "host": null,
                    "methods": [
                        "POST"
                    ],
                    "uri": "curso\/tema\/contenido",
                    "name": "curso.tema.contenido.store",
                    "action":"App\Http\Controllers\ContenidoCursoController@store"
                },
                {
                    "host": null,
                    "methods": [
                        "PUT",
                        "PATCH"
                    ],
                    "uri": "curso\/tema\/contenido\/{contenido}",
                    "name": "curso.tema.contenido.update",
                    "action":"App\Http\Controllers\ContenidoCursoController@update"
                },
                {
                    "host": null,
                    "methods": [
                        "GET",
                        "HEAD"
                    ],
                    "uri": "curso\/tema",
                    "name": "curso.tema.index",
                    "action":"App\Http\Controllers\TemaCursoController@index"
                },
                {
                    "host": null,
                    "methods": [
                        "POST"
                    ],
                    "uri": "curso\/tema",
                    "name": "curso.tema.store",
                    "action":"App\Http\Controllers\TemaCursoController@store"
                },
                {
                    "host": null,
                    "methods": [
                        "PUT",
                        "PATCH"
                    ],
                    "uri": "curso\/tema\/{tema}",
                    "name": "curso.tema.update",
                    "action":"App\Http\Controllers\TemaCursoController@update"
                },
                {
                    "host": null,
                    "methods": [
                        "GET",
                        "HEAD"
                    ],
                    "uri": "curso\/evaluacion\/exportar",
                    "name": "curso.evaluacion.exportar.index",
                    "action":"App\Http\Controllers\Reportes\FormularioController@index"
                },
                {
                    "host": null,
                    "methods": [
                        "GET",
                        "HEAD"
                    ],
                    "uri": "curso\/evaluacion",
                    "name": "curso.evaluacion.index",
                    "action":"App\Http\Controllers\EvaluacionCursoController@index"
                },
                {
                    "host": null,
                    "methods": [
                        "GET",
                        "HEAD"
                    ],
                    "uri": "curso\/evaluacion\/{evaluacion}",
                    "name": "curso.evaluacion.show",
                    "action":"App\Http\Controllers\EvaluacionCursoController@show"
                },
                {
                    "host": null,
                    "methods": [
                        "GET",
                        "HEAD"
                    ],
                    "uri": "curso\/evaluacion\/{evaluacion}\/edit",
                    "name": "curso.evaluacion.edit",
                    "action":"App\Http\Controllers\EvaluacionCursoController@edit"
                },
                {
                    "host": null,
                    "methods": [
                        "PUT",
                        "PATCH"
                    ],
                    "uri": "curso\/evaluacion\/{evaluacion}",
                    "name": "curso.evaluacion.update",
                    "action":"App\Http\Controllers\EvaluacionCursoController@update"
                },
                {
                    "host": null,
                    "methods": [
                        "GET",
                        "HEAD"
                    ],
                    "uri": "curso",
                    "name": "curso.index",
                    "action":"App\Http\Controllers\CursoController@index"
                },
                {
                    "host": null,
                    "methods": [
                        "GET",
                        "HEAD"
                    ],
                    "uri": "curso\/create",
                    "name": "curso.create",
                    "action":"App\Http\Controllers\CursoController@create"
                },
                {
                    "host": null,
                    "methods": [
                        "POST"
                    ],
                    "uri": "curso",
                    "name": "curso.store",
                    "action":"App\Http\Controllers\CursoController@store"
                },
                {
                    "host": null,
                    "methods": [
                        "GET",
                        "HEAD"
                    ],
                    "uri": "curso\/{curso}",
                    "name": "curso.show",
                    "action":"App\Http\Controllers\CursoController@show"
                },
                {
                    "host": null,
                    "methods": [
                        "GET",
                        "HEAD"
                    ],
                    "uri": "curso\/{curso}\/edit",
                    "name": "curso.edit",
                    "action":"App\Http\Controllers\CursoController@edit"
                },
                {
                    "host": null,
                    "methods": [
                        "PUT",
                        "PATCH"
                    ],
                    "uri": "curso\/{curso}",
                    "name": "curso.update",
                    "action":"App\Http\Controllers\CursoController@update"
                },
                {
                    "host": null,
                    "methods": [
                        "GET",
                        "HEAD"
                    ],
                    "uri": "contenido\/search",
                    "name": "contenido.search",
                    "action":"App\Http\Controllers\ContenidoController@search"
                },
                {
                    "host": null,
                    "methods": [
                        "GET",
                        "HEAD"
                    ],
                    "uri": "contenido",
                    "name": "contenido.index",
                    "action":"App\Http\Controllers\ContenidoController@index"
                },
                {
                    "host": null,
                    "methods": [
                        "GET",
                        "HEAD"
                    ],
                    "uri": "contenido\/create",
                    "name": "contenido.create",
                    "action":"App\Http\Controllers\ContenidoController@create"
                },
                {
                    "host": null,
                    "methods": [
                        "POST"
                    ],
                    "uri": "contenido",
                    "name": "contenido.store",
                    "action":"App\Http\Controllers\ContenidoController@store"
                },
                {
                    "host": null,
                    "methods": [
                        "GET",
                        "HEAD"
                    ],
                    "uri": "contenido\/{contenido}",
                    "name": "contenido.show",
                    "action":"App\Http\Controllers\ContenidoController@show"
                },
                {
                    "host": null,
                    "methods": [
                        "GET",
                        "HEAD"
                    ],
                    "uri": "contenido\/{contenido}\/edit",
                    "name": "contenido.edit",
                    "action":"App\Http\Controllers\ContenidoController@edit"
                },
                {
                    "host": null,
                    "methods": [
                        "PUT",
                        "PATCH"
                    ],
                    "uri": "contenido\/{contenido}",
                    "name": "contenido.update",
                    "action":"App\Http\Controllers\ContenidoController@update"
                },
                {
                    "host": null,
                    "methods": [
                        "GET",
                        "HEAD"
                    ],
                    "uri": "contenidocanal",
                    "name": "contenidocanal.index",
                    "action":"App\Http\Controllers\ContenidoCanalController@index"
                },
                {
                    "host": null,
                    "methods": [
                        "POST"
                    ],
                    "uri": "contenidocanal",
                    "name": "contenidocanal.store",
                    "action":"App\Http\Controllers\ContenidoCanalController@store"
                },
                {
                    "host": null,
                    "methods": [
                        "DELETE"
                    ],
                    "uri": "contenidocanal\/{contenidocanal}",
                    "name": "contenidocanal.destroy",
                    "action":"App\Http\Controllers\ContenidoCanalController@destroy"
                },
                {
                    "host": null,
                    "methods": [
                        "POST"
                    ],
                    "uri": "file\/temp",
                    "name": "file.temp",
                    "action":"App\Http\Controllers\FileController@temp"
                },
                {
                    "host": null,
                    "methods": [
                        "POST"
                    ],
                    "uri": "file\/crop",
                    "name": "file.crop",
                    "action":"App\Http\Controllers\FileController@crop"
                },
                {
                    "host": null,
                    "methods": [
                        "POST"
                    ],
                    "uri": "file\/upload",
                    "name": "file.upload",
                    "action":"App\Http\Controllers\FileController@upload"
                },
                {
                    "host": null,
                    "methods": [
                        "POST"
                    ],
                    "uri": "file\/course\/upload",
                    "name": "file.course",
                    "action":"App\Http\Controllers\FileController@uploadCourseAttachment"
                },
                {
                    "host": null,
                    "methods": [
                        "GET",
                        "HEAD"
                    ],
                    "uri": "adjunto\/documentador",
                    "name": "adjunto.documentador.index",
                    "action":"App\Http\Controllers\AdjuntoDocumentadorController@index"
                },
                {
                    "host": null,
                    "methods": [
                        "POST"
                    ],
                    "uri": "adjunto\/documentador",
                    "name": "adjunto.documentador.store",
                    "action":"App\Http\Controllers\AdjuntoDocumentadorController@store"
                },
                {
                    "host": null,
                    "methods": [
                        "GET",
                        "HEAD"
                    ],
                    "uri": "adjunto\/usuario",
                    "name": "adjunto.usuario.index",
                    "action":"App\Http\Controllers\AdjuntoUsuarioController@index"
                },
                {
                    "host": null,
                    "methods": [
                        "POST"
                    ],
                    "uri": "adjunto\/usuario",
                    "name": "adjunto.usuario.store",
                    "action":"App\Http\Controllers\AdjuntoUsuarioController@store"
                },
                {
                    "host": null,
                    "methods": [
                        "PUT",
                        "PATCH"
                    ],
                    "uri": "adjunto\/usuario\/{usuario}",
                    "name": "adjunto.usuario.update",
                    "action":"App\Http\Controllers\AdjuntoUsuarioController@update"
                },
                {
                    "host": null,
                    "methods": [
                        "DELETE"
                    ],
                    "uri": "adjunto\/usuario\/{usuario}",
                    "name": "adjunto.usuario.destroy",
                    "action":"App\Http\Controllers\AdjuntoUsuarioController@destroy"
                },
                {
                    "host": null,
                    "methods": [
                        "GET",
                        "HEAD"
                    ],
                    "uri": "adjunto\/cursos",
                    "name": "adjunto.cursos.index",
                    "action":"App\Http\Controllers\AdjuntoCursosController@index"
                },
                {
                    "host": null,
                    "methods": [
                        "POST"
                    ],
                    "uri": "adjunto\/cursos",
                    "name": "adjunto.cursos.store",
                    "action":"App\Http\Controllers\AdjuntoCursosController@store"
                },
                {
                    "host": null,
                    "methods": [
                        "PUT",
                        "PATCH"
                    ],
                    "uri": "adjunto\/cursos\/{curso}",
                    "name": "adjunto.cursos.update",
                    "action":"App\Http\Controllers\AdjuntoCursosController@update"
                },
                {
                    "host": null,
                    "methods": [
                        "DELETE"
                    ],
                    "uri": "adjunto\/cursos\/{curso}",
                    "name": "adjunto.cursos.destroy",
                    "action":"App\Http\Controllers\AdjuntoCursosController@destroy"
                },
                {
                    "host": null,
                    "methods": [
                        "GET",
                        "HEAD"
                    ],
                    "uri": "adjuntos",
                    "name": "adjuntos.index",
                    "action":"App\Http\Controllers\AdjuntoConvocatoriaController@index"
                },
                {
                    "host": null,
                    "methods": [
                        "POST"
                    ],
                    "uri": "adjuntos",
                    "name": "adjuntos.store",
                    "action":"App\Http\Controllers\AdjuntoConvocatoriaController@store"
                },
                {
                    "host": null,
                    "methods": [
                        "PUT",
                        "PATCH"
                    ],
                    "uri": "adjuntos\/{adjunto}",
                    "name": "adjuntos.update",
                    "action":"App\Http\Controllers\AdjuntoConvocatoriaController@update"
                },
                {
                    "host": null,
                    "methods": [
                        "DELETE"
                    ],
                    "uri": "adjuntos\/{adjunto}",
                    "name": "adjuntos.destroy",
                    "action":"App\Http\Controllers\AdjuntoConvocatoriaController@destroy"
                },
                {
                    "host": null,
                    "methods": [
                        "GET",
                        "HEAD"
                    ],
                    "uri": "marketplace\/transacciones\/acuerdo\/exportar\/{acuerdo}",
                    "name": "marketplace.transacciones.acuerdo.exportar",
                    "action":"App\Http\Controllers\AcuerdoCompraController@exportar"
                },
                {
                    "host": null,
                    "methods": [
                        "GET",
                        "HEAD"
                    ],
                    "uri": "marketplace\/transacciones\/acuerdo\/actividad",
                    "name": "marketplace.transacciones.acuerdo.actividad.index",
                    "action":"App\Http\Controllers\ActividadAcuerdoController@index"
                },
                {
                    "host": null,
                    "methods": [
                        "POST"
                    ],
                    "uri": "marketplace\/transacciones\/acuerdo\/actividad",
                    "name": "marketplace.transacciones.acuerdo.actividad.store",
                    "action":"App\Http\Controllers\ActividadAcuerdoController@store"
                },
                {
                    "host": null,
                    "methods": [
                        "PUT",
                        "PATCH"
                    ],
                    "uri": "marketplace\/transacciones\/acuerdo\/actividad\/{actividad}",
                    "name": "marketplace.transacciones.acuerdo.actividad.update",
                    "action":"App\Http\Controllers\ActividadAcuerdoController@update"
                },
                {
                    "host": null,
                    "methods": [
                        "DELETE"
                    ],
                    "uri": "marketplace\/transacciones\/acuerdo\/actividad\/{actividad}",
                    "name": "marketplace.transacciones.acuerdo.actividad.destroy",
                    "action":"App\Http\Controllers\ActividadAcuerdoController@destroy"
                },
                {
                    "host": null,
                    "methods": [
                        "GET",
                        "HEAD"
                    ],
                    "uri": "marketplace\/transacciones\/acuerdo\/entregable",
                    "name": "marketplace.transacciones.acuerdo.entregable.index",
                    "action":"App\Http\Controllers\EntregableAcuerdoController@index"
                },
                {
                    "host": null,
                    "methods": [
                        "POST"
                    ],
                    "uri": "marketplace\/transacciones\/acuerdo\/entregable",
                    "name": "marketplace.transacciones.acuerdo.entregable.store",
                    "action":"App\Http\Controllers\EntregableAcuerdoController@store"
                },
                {
                    "host": null,
                    "methods": [
                        "PUT",
                        "PATCH"
                    ],
                    "uri": "marketplace\/transacciones\/acuerdo\/entregable\/{entregable}",
                    "name": "marketplace.transacciones.acuerdo.entregable.update",
                    "action":"App\Http\Controllers\EntregableAcuerdoController@update"
                },
                {
                    "host": null,
                    "methods": [
                        "POST"
                    ],
                    "uri": "marketplace\/transacciones\/entregable\/updatedeliverables",
                    "name": "marketplace.transacciones.entregable.updatedeliverables",
                    "action":"App\Http\Controllers\EntregableAcuerdoController@updateDeliverables"
                },
                {
                    "host": null,
                    "methods": [
                        "DELETE"
                    ],
                    "uri": "marketplace\/transacciones\/acuerdo\/entregable\/{entregable}",
                    "name": "marketplace.transacciones.acuerdo.entregable.destroy",
                    "action":"App\Http\Controllers\EntregableAcuerdoController@destroy"
                },
                {
                    "host": null,
                    "methods": [
                        "GET",
                        "HEAD"
                    ],
                    "uri": "marketplace\/transacciones\/acuerdo\/usuario",
                    "name": "marketplace.transacciones.acuerdo.usuario.index",
                    "action":"App\Http\Controllers\UsuarioAcuerdoController@index"
                },
                {
                    "host": null,
                    "methods": [
                        "POST"
                    ],
                    "uri": "marketplace\/transacciones\/acuerdo\/usuario",
                    "name": "marketplace.transacciones.acuerdo.usuario.store",
                    "action":"App\Http\Controllers\UsuarioAcuerdoController@store"
                },
                {
                    "host": null,
                    "methods": [
                        "DELETE"
                    ],
                    "uri": "marketplace\/transacciones\/acuerdo\/usuario\/{usuario}",
                    "name": "marketplace.transacciones.acuerdo.usuario.destroy",
                    "action":"App\Http\Controllers\UsuarioAcuerdoController@destroy"
                },
                {
                    "host": null,
                    "methods": [
                        "GET",
                        "HEAD"
                    ],
                    "uri": "marketplace\/transacciones\/acuerdo\/empresario",
                    "name": "marketplace.transacciones.acuerdo.empresario.index",
                    "action":"App\Http\Controllers\UsuarioEmpresarioController@index"
                },
                {
                    "host": null,
                    "methods": [
                        "POST"
                    ],
                    "uri": "marketplace\/transacciones\/acuerdo\/empresario",
                    "name": "marketplace.transacciones.acuerdo.empresario.store",
                    "action":"App\Http\Controllers\UsuarioEmpresarioController@store"
                },
                {
                    "host": null,
                    "methods": [
                        "PUT",
                        "PATCH"
                    ],
                    "uri": "marketplace\/transacciones\/acuerdo\/empresario\/{empresario}",
                    "name": "marketplace.transacciones.acuerdo.empresario.update",
                    "action":"App\Http\Controllers\UsuarioEmpresarioController@update"
                },
                {
                    "host": null,
                    "methods": [
                        "DELETE"
                    ],
                    "uri": "marketplace\/transacciones\/acuerdo\/empresario\/{empresario}",
                    "name": "marketplace.transacciones.acuerdo.empresario.destroy",
                    "action":"App\Http\Controllers\UsuarioEmpresarioController@destroy"
                },
                {
                    "host": null,
                    "methods": [
                        "GET",
                        "HEAD"
                    ],
                    "uri": "marketplace\/transacciones\/discusion",
                    "name": "marketplace.transacciones.discusion.index",
                    "action":"App\Http\Controllers\DiscusionAcuerdoController@index"
                },
                {
                    "host": null,
                    "methods": [
                        "POST"
                    ],
                    "uri": "marketplace\/transacciones\/discusion",
                    "name": "marketplace.transacciones.discusion.store",
                    "action":"App\Http\Controllers\DiscusionAcuerdoController@store"
                },
                {
                    "host": null,
                    "methods": [
                        "DELETE"
                    ],
                    "uri": "marketplace\/transacciones\/discusion\/{discusion}",
                    "name": "marketplace.transacciones.discusion.destroy",
                    "action":"App\Http\Controllers\DiscusionAcuerdoController@destroy"
                },
                {
                    "host": null,
                    "methods": [
                        "GET",
                        "HEAD"
                    ],
                    "uri": "marketplace\/transacciones\/acuerdo",
                    "name": "marketplace.transacciones.acuerdo.index",
                    "action":"App\Http\Controllers\AcuerdoCompraController@index"
                },
                {
                    "host": null,
                    "methods": [
                        "POST"
                    ],
                    "uri": "marketplace\/transacciones\/acuerdo",
                    "name": "marketplace.transacciones.acuerdo.store",
                    "action":"App\Http\Controllers\AcuerdoCompraController@store"
                },
                {
                    "host": null,
                    "methods": [
                        "GET",
                        "HEAD"
                    ],
                    "uri": "marketplace\/transacciones\/acuerdo\/{acuerdo}",
                    "name": "marketplace.transacciones.acuerdo.show",
                    "action":"App\Http\Controllers\AcuerdoCompraController@show"
                },
                {
                    "host": null,
                    "methods": [
                        "PUT",
                        "PATCH"
                    ],
                    "uri": "marketplace\/transacciones\/acuerdo\/{acuerdo}",
                    "name": "marketplace.transacciones.acuerdo.update",
                    "action":"App\Http\Controllers\AcuerdoCompraController@update"
                },
                {
                    "host": null,
                    "methods": [
                        "GET",
                        "HEAD"
                    ],
                    "uri": "marketplace\/transacciones\/evaluacion",
                    "name": "marketplace.transacciones.evaluacion.index",
                    "action":"App\Http\Controllers\EvaluacionCompraController@index"
                },
                {
                    "host": null,
                    "methods": [
                        "GET",
                        "HEAD"
                    ],
                    "uri": "marketplace\/transacciones\/evaluacion\/{evaluacion}",
                    "name": "marketplace.transacciones.evaluacion.show",
                    "action":"App\Http\Controllers\EvaluacionCompraController@show"
                },
                {
                    "host": null,
                    "methods": [
                        "PUT",
                        "PATCH"
                    ],
                    "uri": "marketplace\/transacciones\/evaluacion\/{evaluacion}",
                    "name": "marketplace.transacciones.evaluacion.update",
                    "action":"App\Http\Controllers\EvaluacionCompraController@update"
                },
                {
                    "host": null,
                    "methods": [
                        "DELETE"
                    ],
                    "uri": "marketplace\/transacciones\/evaluacion\/{evaluacion}",
                    "name": "marketplace.transacciones.evaluacion.destroy",
                    "action":"App\Http\Controllers\EvaluacionCompraController@destroy"
                },
                {
                    "host": null,
                    "methods": [
                        "GET",
                        "HEAD"
                    ],
                    "uri": "marketplace\/transacciones\/factura",
                    "name": "marketplace.transacciones.factura",
                    "action":"App\Http\Controllers\EvaluacionCompraController@bill"
                },
                {
                    "host": null,
                    "methods": [
                        "POST"
                    ],
                    "uri": "marketplace\/transacciones\/facturauploader",
                    "name": "marketplace.transacciones.facturauploader",
                    "action":"App\Http\Controllers\EvaluacionCompraController@billUploader"
                },
                {
                    "host": null,
                    "methods": [
                        "GET",
                        "HEAD"
                    ],
                    "uri": "marketplace\/transacciones\/exportar",
                    "name": "marketplace.transacciones.exportar",
                    "action":"App\Http\Controllers\CompraController@exportar"
                },
                {
                    "host": null,
                    "methods": [
                        "GET",
                        "HEAD"
                    ],
                    "uri": "marketplace\/transacciones",
                    "name": "marketplace.transacciones.index",
                    "action":"App\Http\Controllers\CompraController@index"
                },
                {
                    "host": null,
                    "methods": [
                        "GET",
                        "HEAD"
                    ],
                    "uri": "marketplace\/compra",
                    "name": "marketplace.compra.index",
                    "action":"App\Http\Controllers\CompraController@index"
                },
                {
                    "host": null,
                    "methods": [
                        "POST"
                    ],
                    "uri": "marketplace\/compra",
                    "name": "marketplace.compra.store",
                    "action":"App\Http\Controllers\CompraController@store"
                },
                {
                    "host": null,
                    "methods": [
                        "GET",
                        "HEAD"
                    ],
                    "uri": "marketplace\/compra\/{compra}",
                    "name": "marketplace.compra.show",
                    "action":"App\Http\Controllers\CompraController@show"
                },
                {
                    "host": null,
                    "methods": [
                        "PUT",
                        "PATCH"
                    ],
                    "uri": "marketplace\/compra\/{compra}",
                    "name": "marketplace.compra.update",
                    "action":"App\Http\Controllers\CompraController@update"
                },
                {
                    "host": null,
                    "methods": [
                        "GET",
                        "HEAD"
                    ],
                    "uri": "marketplace\/exportar",
                    "name": "marketplace.exportar",
                    "action":"App\Http\Controllers\ProductoController@exportar"
                },
                {
                    "host": null,
                    "methods": [
                        "GET",
                        "HEAD"
                    ],
                    "uri": "marketplace\/vouchers",
                    "name": "marketplace.vouchers.index",
                    "action":"App\Http\Controllers\DetalleVoucherController@index"
                },
                {
                    "host": null,
                    "methods": [
                        "GET",
                        "HEAD"
                    ],
                    "uri": "marketplace\/vouchers\/{voucher}",
                    "name": "marketplace.vouchers.show",
                    "action":"App\Http\Controllers\DetalleVoucherController@show"
                },
                {
                    "host": null,
                    "methods": [
                        "GET",
                        "HEAD"
                    ],
                    "uri": "marketplace\/asignaciones",
                    "name": "marketplace.asignaciones.index",
                    "action":"App\Http\Controllers\AsignacionMarketplaceController@index"
                },
                {
                    "host": null,
                    "methods": [
                        "POST"
                    ],
                    "uri": "marketplace\/asignaciones",
                    "name": "marketplace.asignaciones.store",
                    "action":"App\Http\Controllers\AsignacionMarketplaceController@store"
                },
                {
                    "host": null,
                    "methods": [
                        "GET",
                        "HEAD"
                    ],
                    "uri": "marketplace\/asignaciones\/{asignacione}",
                    "name": "marketplace.asignaciones.show",
                    "action":"App\Http\Controllers\AsignacionMarketplaceController@show"
                },
                {
                    "host": null,
                    "methods": [
                        "DELETE"
                    ],
                    "uri": "marketplace\/asignaciones\/{asignacione}",
                    "name": "marketplace.asignaciones.destroy",
                    "action":"App\Http\Controllers\AsignacionMarketplaceController@destroy"
                },
                {
                    "host": null,
                    "methods": [
                        "GET",
                        "HEAD"
                    ],
                    "uri": "marketplace\/promociones",
                    "name": "marketplace.promociones.index",
                    "action":"App\Http\Controllers\PromocionController@index"
                },
                {
                    "host": null,
                    "methods": [
                        "GET",
                        "HEAD"
                    ],
                    "uri": "marketplace\/promociones\/{promocione}",
                    "name": "marketplace.promociones.show",
                    "action":"App\Http\Controllers\PromocionController@show"
                },
                {
                    "host": null,
                    "methods": [
                        "PUT",
                        "PATCH"
                    ],
                    "uri": "marketplace\/promociones\/{promocione}",
                    "name": "marketplace.promociones.update",
                    "action":"App\Http\Controllers\PromocionController@update"
                },
                {
                    "host": null,
                    "methods": [
                        "GET",
                        "HEAD"
                    ],
                    "uri": "marketplace",
                    "name": "marketplace.index",
                    "action":"App\Http\Controllers\ProductoController@index"
                },
                {
                    "host": null,
                    "methods": [
                        "GET",
                        "HEAD"
                    ],
                    "uri": "marketplace\/{marketplace}",
                    "name": "marketplace.show",
                    "action":"App\Http\Controllers\ProductoController@show"
                },
                {
                    "host": null,
                    "methods": [
                        "PUT",
                        "PATCH"
                    ],
                    "uri": "marketplace\/{marketplace}",
                    "name": "marketplace.update",
                    "action":"App\Http\Controllers\ProductoController@update"
                },
                {
                    "host": null,
                    "methods": [
                        "GET",
                        "HEAD"
                    ],
                    "uri": "asignacion",
                    "name": "asignacion.index",
                    "action":"App\Http\Controllers\AsignacionController@index"
                },
                {
                    "host": null,
                    "methods": [
                        "POST"
                    ],
                    "uri": "asignacion",
                    "name": "asignacion.store",
                    "action":"App\Http\Controllers\AsignacionController@store"
                },
                {
                    "host": null,
                    "methods": [
                        "PUT",
                        "PATCH"
                    ],
                    "uri": "asignacion\/{asignacion}",
                    "name": "asignacion.update",
                    "action":"App\Http\Controllers\AsignacionController@update"
                },
                {
                    "host": null,
                    "methods": [
                        "DELETE"
                    ],
                    "uri": "asignacion\/{asignacion}",
                    "name": "asignacion.destroy",
                    "action":"App\Http\Controllers\AsignacionController@destroy"
                },
                {
                    "host": null,
                    "methods": [
                        "GET",
                        "HEAD"
                    ],
                    "uri": "asignacionautomatica",
                    "name": "asignacionautomatica.index",
                    "action":"App\Http\Controllers\AsignacionAutomaticaController@index"
                },
                {
                    "host": null,
                    "methods": [
                        "POST"
                    ],
                    "uri": "asignacionautomatica",
                    "name": "asignacionautomatica.store",
                    "action":"App\Http\Controllers\AsignacionAutomaticaController@store"
                },
                {
                    "host": null,
                    "methods": [
                        "PUT",
                        "PATCH"
                    ],
                    "uri": "asignacionautomatica\/{asignacion}",
                    "name": "asignacionautomatica.update",
                    "action":"App\Http\Controllers\AsignacionAutomaticaController@update"
                },
                {
                    "host": null,
                    "methods": [
                        "DELETE"
                    ],
                    "uri": "asignacionautomatica\/{asignacion}",
                    "name": "asignacionautomatica.destroy",
                    "action":"App\Http\Controllers\AsignacionAutomaticaController@destroy"
                },
                {
                    "host": null,
                    "methods": [
                        "GET",
                        "HEAD"
                    ],
                    "uri": "discusion\/comentario",
                    "name": "discusion.comentario.index",
                    "action":"App\Http\Controllers\ComentarioDiscusionController@index"
                },
                {
                    "host": null,
                    "methods": [
                        "POST"
                    ],
                    "uri": "discusion\/comentario",
                    "name": "discusion.comentario.store",
                    "action":"App\Http\Controllers\ComentarioDiscusionController@store"
                },
                {
                    "host": null,
                    "methods": [
                        "DELETE"
                    ],
                    "uri": "discusion\/comentario\/{comentario}",
                    "name": "discusion.comentario.destroy",
                    "action":"App\Http\Controllers\ComentarioDiscusionController@destroy"
                },
                {
                    "host": null,
                    "methods": [
                        "GET",
                        "HEAD"
                    ],
                    "uri": "voucher",
                    "name": "voucher.index",
                    "action":"App\Http\Controllers\VoucherController@index"
                },
                {
                    "host": null,
                    "methods": [
                        "POST"
                    ],
                    "uri": "voucher",
                    "name": "voucher.store",
                    "action":"App\Http\Controllers\VoucherController@store"
                },
                {
                    "host": null,
                    "methods": [
                        "PUT",
                        "PATCH"
                    ],
                    "uri": "voucher\/{voucher}",
                    "name": "voucher.update",
                    "action":"App\Http\Controllers\VoucherController@update"
                },
                {
                    "host": null,
                    "methods": [
                        "GET",
                        "HEAD"
                    ],
                    "uri": "reportes\/empodera",
                    "name": "reportes.empodera.index",
                    "action":"App\Http\Controllers\Reportes\Innpulsa\ReportEmpoderaController@index"
                },
                {
                    "host": null,
                    "methods": [
                        "POST"
                    ],
                    "uri": "reportes\/empodera",
                    "name": "reportes.empodera.store",
                    "action":"App\Http\Controllers\Reportes\Innpulsa\ReportEmpoderaController@store"
                },
                {
                    "host": null,
                    "methods": [
                        "GET",
                        "HEAD"
                    ],
                    "uri": "reportes\/monitoreo12",
                    "name": "reportes.monitoreo12.index",
                    "action":"App\Http\Controllers\Reportes\Monitoreo12Controller@index"
                },
                {
                    "host": null,
                    "methods": [
                        "POST"
                    ],
                    "uri": "reportes\/monitoreo12",
                    "name": "reportes.monitoreo12.store",
                    "action":"App\Http\Controllers\Reportes\Monitoreo12Controller@store"
                },
                {
                    "host": null,
                    "methods": [
                        "GET",
                        "HEAD"
                    ],
                    "uri": "reportes\/reto3",
                    "name": "reportes.reto3.index",
                    "action":"App\Http\Controllers\Reportes\Reto3Controller@index"
                },
                {
                    "host": null,
                    "methods": [
                        "POST"
                    ],
                    "uri": "reportes\/reto3",
                    "name": "reportes.reto3.store",
                    "action":"App\Http\Controllers\Reportes\Reto3Controller@store"
                },
                {
                    "host": null,
                    "methods": [
                        "GET",
                        "HEAD"
                    ],
                    "uri": "reportes\/reto4",
                    "name": "reportes.reto4.index",
                    "action":"App\Http\Controllers\Reportes\Reto4Controller@index"
                },
                {
                    "host": null,
                    "methods": [
                        "POST"
                    ],
                    "uri": "reportes\/reto4",
                    "name": "reportes.reto4.store",
                    "action":"App\Http\Controllers\Reportes\Reto4Controller@store"
                },
                {
                    "host": null,
                    "methods": [
                        "GET",
                        "HEAD"
                    ],
                    "uri": "reportes\/cursos",
                    "name": "reportes.cursos.index",
                    "action":"App\Http\Controllers\Reportes\CursoController@index"
                },
                {
                    "host": null,
                    "methods": [
                        "POST"
                    ],
                    "uri": "reportes\/cursos",
                    "name": "reportes.cursos.store",
                    "action":"App\Http\Controllers\Reportes\CursoController@store"
                },
                {
                    "host": null,
                    "methods": [
                        "POST"
                    ],
                    "uri": "reportes\/cursoReporteIndividual",
                    "name": "reportes.cursos.reporteIndividual",
                    "action":"App\Http\Controllers\Reportes\CursoController@store"
                },
                {
                    "host": null,
                    "methods": [
                        "GET",
                        "HEAD"
                    ],
                    "uri": "reportes\/unicoempo",
                    "name": "reportes.unicoempo.index",
                    "action":"App\Http\Controllers\Reportes\UnicoEmpoderaController@index"
                },
                {
                    "host": null,
                    "methods": [
                        "POST"
                    ],
                    "uri": "reportes\/unicoempo",
                    "name": "reportes.unicoempo.store",
                    "action":"App\Http\Controllers\Reportes\UnicoEmpoderaController@store"
                },
                {
                    "host": null,
                    "methods": [
                        "GET",
                        "HEAD"
                    ],
                    "uri": "reportes\/unicoterp",
                    "name": "reportes.unicoterp.index",
                    "action":"App\Http\Controllers\Reportes\UnicoTerpelController@index"
                },
                {
                    "host": null,
                    "methods": [
                        "POST"
                    ],
                    "uri": "reportes\/unicoterp",
                    "name": "reportes.unicoterp.store",
                    "action":"App\Http\Controllers\Reportes\UnicoTerpelController@store"
                },
                {
                    "host": null,
                    "methods": [
                        "GET",
                        "HEAD"
                    ],
                    "uri": "reportes\/monitoreo",
                    "name": "reportes.monitoreo.index",
                    "action":"App\Http\Controllers\Reportes\MonitoreoController@index"
                },
                {
                    "host": null,
                    "methods": [
                        "POST"
                    ],
                    "uri": "reportes\/monitoreo",
                    "name": "reportes.monitoreo.store",
                    "action":"App\Http\Controllers\Reportes\MonitoreoController@store"
                },
                {
                    "host": null,
                    "methods": [
                        "POST"
                    ],
                    "uri": "reportes\/rutan\/convocatorias",
                    "name": "reportes.rutan.convocatorias.store",
                    "action":"App\Http\Controllers\Reportes\Rutan\ReporteConvocatoriaController@store"
                },
                {
                    "host": null,
                    "methods": [
                        "GET",
                        "HEAD"
                    ],
                    "uri": "reportes\/evaluaciones",
                    "name": "reportes.evaluaciones.index",
                    "action":"App\Http\Controllers\Reportes\EvaluacionController@index"
                },
                {
                    "host": null,
                    "methods": [
                        "POST"
                    ],
                    "uri": "reportes\/evaluaciones",
                    "name": "reportes.evaluaciones.store",
                    "action":"App\Http\Controllers\Reportes\EvaluacionController@store"
                },
                {
                    "host": null,
                    "methods": [
                        "GET",
                        "HEAD"
                    ],
                    "uri": "reportes\/seguimiento",
                    "name": "reportes.seguimiento.index",
                    "action":"App\Http\Controllers\Reportes\SeguimientoConvocatoriaController@index"
                },
                {
                    "host": null,
                    "methods": [
                        "POST"
                    ],
                    "uri": "reportes\/seguimiento",
                    "name": "reportes.seguimiento.store",
                    "action":"App\Http\Controllers\Reportes\SeguimientoConvocatoriaController@store"
                },
                {
                    "host": null,
                    "methods": [
                        "GET",
                        "HEAD"
                    ],
                    "uri": "reportes\/votos",
                    "name": "reportes.votos.index",
                    "action":"App\Http\Controllers\Reportes\VotoController@index"
                },
                {
                    "host": null,
                    "methods": [
                        "POST"
                    ],
                    "uri": "reportes\/votos",
                    "name": "reportes.votos.store",
                    "action":"App\Http\Controllers\Reportes\VotoController@store"
                },
                {
                    "host": null,
                    "methods": [
                        "GET",
                        "HEAD"
                    ],
                    "uri": "reportes\/usuarios",
                    "name": "reportes.usuarios.index",
                    "action":"App\Http\Controllers\Reportes\ReporteUsuarioController@index"
                },
                {
                    "host": null,
                    "methods": [
                        "POST"
                    ],
                    "uri": "reportes\/usuarios",
                    "name": "reportes.usuarios.store",
                    "action":"App\Http\Controllers\Reportes\ReporteUsuarioController@store"
                },
                {
                    "host": null,
                    "methods": [
                        "GET",
                        "HEAD"
                    ],
                    "uri": "reportes\/gestion",
                    "name": "reportes.gestion.index",
                    "action":"App\Http\Controllers\Reportes\GestionController@index"
                },
                {
                    "host": null,
                    "methods": [
                        "DELETE"
                    ],
                    "uri": "reportes\/gestion\/{gestion}",
                    "name": "reportes.gestion.destroy",
                    "action":"App\Http\Controllers\Reportes\GestionController@destroy"
                },
                {
                    "host": null,
                    "methods": [
                        "GET",
                        "HEAD"
                    ],
                    "uri": "reportes\/eventos",
                    "name": "reportes.eventos.index",
                    "action":"App\Http\Controllers\Reportes\Compensar\ReporteGeneralEventos@index"
                },
                {
                    "host": null,
                    "methods": [
                        "POST"
                    ],
                    "uri": "reportes\/eventos",
                    "name": "reportes.eventos.store",
                    "action":"App\Http\Controllers\Reportes\Compensar\ReporteGeneralEventos@store"
                },
                {
                    "host": null,
                    "methods": [
                        "GET",
                        "HEAD"
                    ],
                    "uri": "reportes\/evaluationgeneral",
                    "name": "reportes.evaluationgeneral.index",
                    "action":"App\Http\Controllers\Reportes\Innpulsa\EvaluacionGeneralController@index"
                },
                {
                    "host": null,
                    "methods": [
                        "POST"
                    ],
                    "uri": "reportes\/evaluationgeneral",
                    "name": "reportes.evaluationgeneral.store",
                    "action":"App\Http\Controllers\Reportes\Innpulsa\EvaluacionGeneralController@store"
                },
                {
                    "host": null,
                    "methods": [
                        "GET",
                        "HEAD"
                    ],
                    "uri": "reportes\/mentorias",
                    "name": "reportes.mentorias.index",
                    "action":"App\Http\Controllers\Reportes\ReporteGeneralMentorias@index"
                },
                {
                    "host": null,
                    "methods": [
                        "POST"
                    ],
                    "uri": "reportes\/mentorias",
                    "name": "reportes.mentorias.store",
                    "action":"App\Http\Controllers\Reportes\ReporteGeneralMentorias@store"
                },
                {
                    "host": null,
                    "methods": [
                        "GET",
                        "HEAD"
                    ],
                    "uri": "reportes\/proyectos",
                    "name": "reportes.proyectos.index",
                    "action":"App\Http\Controllers\Reportes\ProyectoController@index"
                },
                {
                    "host": null,
                    "methods": [
                        "POST"
                    ],
                    "uri": "reportes\/proyectos",
                    "name": "reportes.proyectos.store",
                    "action":"App\Http\Controllers\Reportes\ProyectoController@store"
                },
                {
                    "host": null,
                    "methods": [
                        "GET",
                        "HEAD"
                    ],
                    "uri": "reportes\/dinamiza\/usuarios",
                    "name": "reportes.dinamiza.usuarios.index",
                    "action":"App\Http\Controllers\Reportes\Dinamiza\ReporteUsuarioController@index"
                },
                {
                    "host": null,
                    "methods": [
                        "POST"
                    ],
                    "uri": "reportes\/dinamiza\/usuarios",
                    "name": "reportes.dinamiza.usuarios.store",
                    "action":"App\Http\Controllers\Reportes\Dinamiza\ReporteUsuarioController@store"
                },
                {
                    "host": null,
                    "methods": [
                        "GET",
                        "HEAD"
                    ],
                    "uri": "reportes\/dinamiza\/programas",
                    "name": "reportes.dinamiza.programas.index",
                    "action":"App\Http\Controllers\Reportes\Dinamiza\ReporteProgramaController@index"
                },
                {
                    "host": null,
                    "methods": [
                        "POST"
                    ],
                    "uri": "reportes\/dinamiza\/programas",
                    "name": "reportes.dinamiza.programas.store",
                    "action":"App\Http\Controllers\Reportes\Dinamiza\ReporteProgramaController@store"
                },
                {
                    "host": null,
                    "methods": [
                        "GET",
                        "HEAD"
                    ],
                    "uri": "ciudades\/localidades\/barrios",
                    "name": "ciudades.localidades.barrios.index",
                    "action":"App\Http\Controllers\BarrioLocalidadController@index"
                },
                {
                    "host": null,
                    "methods": [
                        "GET",
                        "HEAD"
                    ],
                    "uri": "ciudades\/localidades",
                    "name": "ciudades.localidades.index",
                    "action":"App\Http\Controllers\LocalidadCiudadController@index"
                },
                {
                    "host": null,
                    "methods": [
                        "GET",
                        "HEAD"
                    ],
                    "uri": "ciudades",
                    "name": "ciudades.index",
                    "action":"App\Http\Controllers\CiudadController@index"
                },
                {
                    "host": null,
                    "methods": [
                        "GET",
                        "HEAD"
                    ],
                    "uri": "privacidad",
                    "name": "privacidad.index",
                    "action":"App\Http\Controllers\PrivacidadContenidoController@index"
                },
                {
                    "host": null,
                    "methods": [
                        "GET",
                        "HEAD"
                    ],
                    "uri": "formulario\/respuestas\/exportar\/{usersurvey}",
                    "name": "formulario.respuestas.exportar",
                    "action":"App\Http\Controllers\RespuestaController@exportar"
                },
                {
                    "host": null,
                    "methods": [
                        "GET",
                        "HEAD"
                    ],
                    "uri": "formulario\/respuestas\/pdf\/{register}",
                    "name": "formulario.respuestas.pdf",
                    "action":"App\Http\Controllers\RespuestaController@exportarPDF"
                },
                {
                    "host": null,
                    "methods": [
                        "GET",
                        "HEAD"
                    ],
                    "uri": "formulario\/diagnostico\/radar\/{usersurvey}",
                    "name": "formulario.diagnostico.radar",
                    "action":"App\Http\Controllers\DiagnosisController@exportRadarDiagram"
                },
                {
                    "host": null,
                    "methods": [
                        "GET",
                        "HEAD"
                    ],
                    "uri": "formulario\/respuestas\/exportarservices\/{usersurvey}",
                    "name": "formulario.respuestas.exportSpecializedService",
                    "action":"App\Http\Controllers\RespuestaController@exportSpecializedService"
                },
                {
                    "host": null,
                    "methods": [
                        "GET",
                        "HEAD"
                    ],
                    "uri": "formulario\/pregunta\/opcion",
                    "name": "formulario.pregunta.opcion.index",
                    "action":"App\Http\Controllers\OpcionPreguntaEncuestaController@index"
                },
                {
                    "host": null,
                    "methods": [
                        "POST"
                    ],
                    "uri": "formulario\/pregunta\/opcion",
                    "name": "formulario.pregunta.opcion.store",
                    "action":"App\Http\Controllers\OpcionPreguntaEncuestaController@store"
                },
                {
                    "host": null,
                    "methods": [
                        "PUT",
                        "PATCH"
                    ],
                    "uri": "formulario\/pregunta\/opcion\/{opcion}",
                    "name": "formulario.pregunta.opcion.update",
                    "action":"App\Http\Controllers\OpcionPreguntaEncuestaController@update"
                },
                {
                    "host": null,
                    "methods": [
                        "DELETE"
                    ],
                    "uri": "formulario\/pregunta\/opcion\/{opcion}",
                    "name": "formulario.pregunta.opcion.destroy",
                    "action":"App\Http\Controllers\OpcionPreguntaEncuestaController@destroy"
                },
                {
                    "host": null,
                    "methods": [
                        "GET",
                        "HEAD"
                    ],
                    "uri": "formulario\/evaluacion\/exportar",
                    "name": "formulario.evaluacion.exportar.index",
                    "action":"App\Http\Controllers\Reportes\FormularioController@index"
                },
                {
                    "host": null,
                    "methods": [
                        "GET",
                        "HEAD"
                    ],
                    "uri": "formulario\/version",
                    "name": "formulario.version.index",
                    "action":"App\Http\Controllers\VersionFormularioController@index"
                },
                {
                    "host": null,
                    "methods": [
                        "POST"
                    ],
                    "uri": "formulario\/version",
                    "name": "formulario.version.store",
                    "action":"App\Http\Controllers\VersionFormularioController@store"
                },
                {
                    "host": null,
                    "methods": [
                        "GET",
                        "HEAD"
                    ],
                    "uri": "formulario\/pregunta",
                    "name": "formulario.pregunta.index",
                    "action":"App\Http\Controllers\PreguntaFormularioController@index"
                },
                {
                    "host": null,
                    "methods": [
                        "GET",
                        "HEAD"
                    ],
                    "uri": "evaluacion\/pregunta",
                    "name": "evaluacion.pregunta.index",
                    "action":"App\Http\Controllers\PreguntaEvaluacionController@index"
                },
                {
                    "host": null,
                    "methods": [
                        "POST"
                    ],
                    "uri": "formulario\/pregunta",
                    "name": "formulario.pregunta.store",
                    "action":"App\Http\Controllers\PreguntaFormularioController@store"
                },
                {
                    "host": null,
                    "methods": [
                        "PUT",
                        "PATCH"
                    ],
                    "uri": "formulario\/pregunta\/{preguntum}",
                    "name": "formulario.pregunta.update",
                    "action":"App\Http\Controllers\PreguntaFormularioController@update"
                },
                {
                    "host": null,
                    "methods": [
                        "DELETE"
                    ],
                    "uri": "formulario\/pregunta\/{preguntum}",
                    "name": "formulario.pregunta.destroy",
                    "action":"App\Http\Controllers\PreguntaFormularioController@destroy"
                },
                {
                    "host": null,
                    "methods": [
                        "GET",
                        "HEAD"
                    ],
                    "uri": "formulario\/grupo",
                    "name": "formulario.grupo.index",
                    "action":"App\Http\Controllers\GrupoPreguntaEncuestaController@index"
                },
                {
                    "host": null,
                    "methods": [
                        "POST"
                    ],
                    "uri": "formulario\/grupo",
                    "name": "formulario.grupo.store",
                    "action":"App\Http\Controllers\GrupoPreguntaEncuestaController@store"
                },
                {
                    "host": null,
                    "methods": [
                        "GET",
                        "HEAD"
                    ],
                    "uri": "formulario\/grupo\/{grupo}",
                    "name": "formulario.grupo.show",
                    "action":"App\Http\Controllers\GrupoPreguntaEncuestaController@show"
                },
                {
                    "host": null,
                    "methods": [
                        "PUT",
                        "PATCH"
                    ],
                    "uri": "formulario\/grupo\/{grupo}",
                    "name": "formulario.grupo.update",
                    "action":"App\Http\Controllers\GrupoPreguntaEncuestaController@update"
                },
                {
                    "host": null,
                    "methods": [
                        "DELETE"
                    ],
                    "uri": "formulario\/grupo\/{grupo}",
                    "name": "formulario.grupo.destroy",
                    "action":"App\Http\Controllers\GrupoPreguntaEncuestaController@destroy"
                },
                {
                    "host": null,
                    "methods": [
                        "GET",
                        "HEAD"
                    ],
                    "uri": "formulario\/respuesta",
                    "name": "formulario.respuesta.index",
                    "action":"App\Http\Controllers\UsuarioEncuestaController@index"
                },
                {
                    "host": null,
                    "methods": [
                        "GET",
                        "HEAD"
                    ],
                    "uri": "formulario\/paginas",
                    "name": "formulario.paginas.index",
                    "action":"App\Http\Controllers\PaginasEncuestaController@index"
                },
                {
                    "host": null,
                    "methods": [
                        "POST"
                    ],
                    "uri": "formulario\/paginas",
                    "name": "formulario.paginas.store",
                    "action":"App\Http\Controllers\PaginasEncuestaController@store"
                },
                {
                    "host": null,
                    "methods": [
                        "PUT",
                        "PATCH"
                    ],
                    "uri": "formulario\/paginas\/{pagina}",
                    "name": "formulario.paginas.update",
                    "action":"App\Http\Controllers\PaginasEncuestaController@update"
                },
                {
                    "host": null,
                    "methods": [
                        "GET",
                        "HEAD"
                    ],
                    "uri": "formulario\/subpreguntas",
                    "name": "formulario.subpreguntas.index",
                    "action":"App\Http\Controllers\SubPreguntasController@index"
                },
                {
                    "host": null,
                    "methods": [
                        "POST"
                    ],
                    "uri": "formulario\/subpreguntas",
                    "name": "formulario.subpreguntas.store",
                    "action":"App\Http\Controllers\SubPreguntasController@store"
                },
                {
                    "host": null,
                    "methods": [
                        "PUT",
                        "PATCH"
                    ],
                    "uri": "formulario\/subpreguntas\/{subpregunta}",
                    "name": "formulario.subpreguntas.update",
                    "action":"App\Http\Controllers\SubPreguntasController@update"
                },
                {
                    "host": null,
                    "methods": [
                        "GET",
                        "HEAD"
                    ],
                    "uri": "formulario",
                    "name": "formulario.index",
                    "action":"App\Http\Controllers\FormularioController@index"
                },
                {
                    "host": null,
                    "methods": [
                        "GET",
                        "HEAD"
                    ],
                    "uri": "formulario\/create",
                    "name": "formulario.create",
                    "action":"App\Http\Controllers\FormularioController@create"
                },
                {
                    "host": null,
                    "methods": [
                        "POST"
                    ],
                    "uri": "formulario",
                    "name": "formulario.store",
                    "action":"App\Http\Controllers\FormularioController@store"
                },
                {
                    "host": null,
                    "methods": [
                        "GET",
                        "HEAD"
                    ],
                    "uri": "formulario\/{formulario}",
                    "name": "formulario.show",
                    "action":"App\Http\Controllers\FormularioController@show"
                },
                {
                    "host": null,
                    "methods": [
                        "GET",
                        "HEAD"
                    ],
                    "uri": "formulario\/{formulario}\/edit",
                    "name": "formulario.edit",
                    "action":"App\Http\Controllers\FormularioController@edit"
                },
                {
                    "host": null,
                    "methods": [
                        "GET",
                        "HEAD"
                    ],
                    "uri": "evaluacion\/{evaluacion}\/edit",
                    "name": "evaluacion.edit",
                    "action":"App\Http\Controllers\EvaluacionEmprendimientoController@edit"
                },
                {
                    "host": null,
                    "methods": [
                        "PUT",
                        "PATCH"
                    ],
                    "uri": "formulario\/{formulario}",
                    "name": "formulario.update",
                    "action":"App\Http\Controllers\FormularioController@update"
                },
                {
                    "host": null,
                    "methods": [
                        "GET",
                        "HEAD"
                    ],
                    "uri": "foro\/categoria",
                    "name": "foro.categoria.index",
                    "action":"App\Http\Controllers\CategoriaForoController@index"
                },
                {
                    "host": null,
                    "methods": [
                        "GET",
                        "HEAD"
                    ],
                    "uri": "foro\/categoria\/create",
                    "name": "foro.categoria.create",
                    "action":"App\Http\Controllers\CategoriaForoController@create"
                },
                {
                    "host": null,
                    "methods": [
                        "POST"
                    ],
                    "uri": "foro\/categoria",
                    "name": "foro.categoria.store",
                    "action":"App\Http\Controllers\CategoriaForoController@store"
                },
                {
                    "host": null,
                    "methods": [
                        "GET",
                        "HEAD"
                    ],
                    "uri": "foro\/categoria\/{categorium}",
                    "name": "foro.categoria.show",
                    "action":"App\Http\Controllers\CategoriaForoController@show"
                },
                {
                    "host": null,
                    "methods": [
                        "PUT",
                        "PATCH"
                    ],
                    "uri": "foro\/categoria\/{categorium}",
                    "name": "foro.categoria.update",
                    "action":"App\Http\Controllers\CategoriaForoController@update"
                },
                {
                    "host": null,
                    "methods": [
                        "DELETE"
                    ],
                    "uri": "foro\/categoria\/{categorium}",
                    "name": "foro.categoria.destroy",
                    "action":"App\Http\Controllers\CategoriaForoController@destroy"
                },
                {
                    "host": null,
                    "methods": [
                        "GET",
                        "HEAD"
                    ],
                    "uri": "foro",
                    "name": "foro.index",
                    "action":"App\Http\Controllers\ForoController@index"
                },
                {
                    "host": null,
                    "methods": [
                        "GET",
                        "HEAD"
                    ],
                    "uri": "foro\/{foro}",
                    "name": "foro.show",
                    "action":"App\Http\Controllers\ForoController@show"
                },
                {
                    "host": null,
                    "methods": [
                        "PUT",
                        "PATCH"
                    ],
                    "uri": "foro\/{foro}",
                    "name": "foro.update",
                    "action":"App\Http\Controllers\ForoController@update"
                },
                {
                    "host": null,
                    "methods": [
                        "GET",
                        "HEAD"
                    ],
                    "uri": "notificacion\/usuario",
                    "name": "notificacion.usuario.index",
                    "action":"App\Http\Controllers\DetalleNotificacionUsuarioController@index"
                },
                {
                    "host": null,
                    "methods": [
                        "POST"
                    ],
                    "uri": "notificacion\/usuario",
                    "name": "notificacion.usuario.store",
                    "action":"App\Http\Controllers\DetalleNotificacionUsuarioController@store"
                },
                {
                    "host": null,
                    "methods": [
                        "GET",
                        "HEAD"
                    ],
                    "uri": "notificacion\/output",
                    "name": "notificacion.output.index",
                    "action":"App\Http\Controllers\NotificacionOutputController@index"
                },
                {
                    "host": null,
                    "methods": [
                        "GET",
                        "HEAD"
                    ],
                    "uri": "notificacion\/output\/create",
                    "name": "notificacion.output.create",
                    "action":"App\Http\Controllers\NotificacionOutputController@create"
                },
                {
                    "host": null,
                    "methods": [
                        "POST"
                    ],
                    "uri": "notificacion\/output",
                    "name": "notificacion.output.store",
                    "action":"App\Http\Controllers\NotificacionOutputController@store"
                },
                {
                    "host": null,
                    "methods": [
                        "PUT",
                        "PATCH"
                    ],
                    "uri": "notificacion\/output\/{output}",
                    "name": "notificacion.output.update",
                    "action":"App\Http\Controllers\NotificacionOutputController@update"
                },
                {
                    "host": null,
                    "methods": [
                        "DELETE"
                    ],
                    "uri": "notificacion\/output\/{output}",
                    "name": "notificacion.output.destroy",
                    "action":"App\Http\Controllers\NotificacionOutputController@destroy"
                },
                {
                    "host": null,
                    "methods": [
                        "GET",
                        "HEAD"
                    ],
                    "uri": "evento\/respuestas\/exportar\/{evento}",
                    "name": "evento.respuestas.exportar",
                    "action":"App\Http\Controllers\EventoController@exportar"
                },
                {
                    "host": null,
                    "methods": [
                        "GET",
                        "HEAD"
                    ],
                    "uri": "evento\/organizadorev",
                    "name": "evento.organizerev",
                    "action":"App\Http\Controllers\OrganizadorEventoController@organizerEvent"
                },
                {
                    "host": null,
                    "methods": [
                        "GET",
                        "HEAD"
                    ],
                    "uri": "evento\/search",
                    "name": "evento.search",
                    "action":"App\Http\Controllers\EventoController@search"
                },
                {
                    "host": null,
                    "methods": [
                        "GET",
                        "HEAD"
                    ],
                    "uri": "evento\/eventtype",
                    "name": "evento.eventtype",
                    "action":"App\Http\Controllers\EventoController@getEventType"
                },
                {
                    "host": null,
                    "methods": [
                        "GET",
                        "HEAD"
                    ],
                    "uri": "evento\/organizador",
                    "name": "evento.organizador.index",
                    "action":"App\Http\Controllers\OrganizadorEventoController@index"
                },
                {
                    "host": null,
                    "methods": [
                        "POST"
                    ],
                    "uri": "evento\/organizador",
                    "name": "evento.organizador.store",
                    "action":"App\Http\Controllers\OrganizadorEventoController@store"
                },
                {
                    "host": null,
                    "methods": [
                        "DELETE"
                    ],
                    "uri": "evento\/organizador\/{organizador}",
                    "name": "evento.organizador.destroy",
                    "action":"App\Http\Controllers\OrganizadorEventoController@destroy"
                },
                {
                    "host": null,
                    "methods": [
                        "GET",
                        "HEAD"
                    ],
                    "uri": "evento\/participante",
                    "name": "evento.participante.index",
                    "action":"App\Http\Controllers\UsuarioEventoController@index"
                },
                {
                    "host": null,
                    "methods": [
                        "POST"
                    ],
                    "uri": "evento\/participante",
                    "name": "evento.participante.store",
                    "action":"App\Http\Controllers\UsuarioEventoController@store"
                },
                {
                    "host": null,
                    "methods": [
                        "DELETE"
                    ],
                    "uri": "evento\/participante\/{participante}",
                    "name": "evento.participante.destroy",
                    "action":"App\Http\Controllers\UsuarioEventoController@destroy"
                },
                {
                    "host": null,
                    "methods": [
                        "GET",
                        "HEAD"
                    ],
                    "uri": "evento\/asistencia",
                    "name": "evento.asistencia.index",
                    "action":"App\Http\Controllers\ControlAsistenciaController@index"
                },
                {
                    "host": null,
                    "methods": [
                        "POST"
                    ],
                    "uri": "evento\/asistencia",
                    "name": "evento.asistencia.store",
                    "action":"App\Http\Controllers\ControlAsistenciaController@store"
                },
                {
                    "host": null,
                    "methods": [
                        "DELETE"
                    ],
                    "uri": "evento\/asistencia\/{asistencium}",
                    "name": "evento.asistencia.destroy",
                    "action":"App\Http\Controllers\ControlAsistenciaController@destroy"
                },
                {
                    "host": null,
                    "methods": [
                        "GET",
                        "HEAD"
                    ],
                    "uri": "evento\/beneficio",
                    "name": "evento.beneficio.index",
                    "action":"App\Http\Controllers\ListaBeneficioEventoController@index"
                },
                {
                    "host": null,
                    "methods": [
                        "POST"
                    ],
                    "uri": "evento\/beneficio",
                    "name": "evento.beneficio.store",
                    "action":"App\Http\Controllers\ListaBeneficioEventoController@store"
                },
                {
                    "host": null,
                    "methods": [
                        "DELETE"
                    ],
                    "uri": "evento\/beneficio\/{beneficio}",
                    "name": "evento.beneficio.destroy",
                    "action":"App\Http\Controllers\ListaBeneficioEventoController@destroy"
                },
                {
                    "host": null,
                    "methods": [
                        "POST"
                    ],
                    "uri": "evento\/certificado",
                    "name": "evento.certificado",
                    "action":"App\Http\Controllers\EventoController@certificado"
                },
                {
                    "host": null,
                    "methods": [
                        "POST"
                    ],
                    "uri": "evento\/duplicar",
                    "name": "evento.duplicar",
                    "action":"App\Http\Controllers\EventoController@duplicar"
                },
                {
                    "host": null,
                    "methods": [
                        "GET",
                        "HEAD"
                    ],
                    "uri": "evento\/usuario\/invitacion",
                    "name": "evento.usuario.invitacion.index",
                    "action":"App\Http\Controllers\InvitacionEventoController@index"
                },
                {
                    "host": null,
                    "methods": [
                        "POST"
                    ],
                    "uri": "evento\/usuario\/invitacion",
                    "name": "evento.usuario.invitacion.store",
                    "action":"App\Http\Controllers\InvitacionEventoController@store"
                },
                {
                    "host": null,
                    "methods": [
                        "POST"
                    ],
                    "uri": "evento\/sesion\/orden",
                    "name": "evento.sesion.orden",
                    "action":"App\Http\Controllers\SesionEventoController@order"
                },
                {
                    "host": null,
                    "methods": [
                        "GET",
                        "HEAD"
                    ],
                    "uri": "evento\/sesion",
                    "name": "evento.sesion.index",
                    "action":"App\Http\Controllers\SesionEventoController@index"
                },
                {
                    "host": null,
                    "methods": [
                        "POST"
                    ],
                    "uri": "evento\/sesion",
                    "name": "evento.sesion.store",
                    "action":"App\Http\Controllers\SesionEventoController@store"
                },
                {
                    "host": null,
                    "methods": [
                        "PUT",
                        "PATCH"
                    ],
                    "uri": "evento\/sesion\/{sesion}",
                    "name": "evento.sesion.update",
                    "action":"App\Http\Controllers\SesionEventoController@update"
                },
                {
                    "host": null,
                    "methods": [
                        "GET",
                        "HEAD"
                    ],
                    "uri": "evento\/eventocategoria",
                    "name": "evento.eventocategoria.index",
                    "action":"App\Http\Controllers\EventoCategoriaController@index"
                },
                {
                    "host": null,
                    "methods": [
                        "POST"
                    ],
                    "uri": "evento\/eventocategoria",
                    "name": "evento.eventocategoria.store",
                    "action":"App\Http\Controllers\EventoCategoriaController@store"
                },
                {
                    "host": null,
                    "methods": [
                        "DELETE"
                    ],
                    "uri": "evento\/eventocategoria\/{eventocategorium}",
                    "name": "evento.eventocategoria.destroy",
                    "action":"App\Http\Controllers\EventoCategoriaController@destroy"
                },
                {
                    "host": null,
                    "methods": [
                        "GET",
                        "HEAD"
                    ],
                    "uri": "evento\/eventoetapa",
                    "name": "evento.eventoetapa.index",
                    "action":"App\Http\Controllers\EventoEtapaController@index"
                },
                {
                    "host": null,
                    "methods": [
                        "POST"
                    ],
                    "uri": "evento\/eventoetapa",
                    "name": "evento.eventoetapa.store",
                    "action":"App\Http\Controllers\EventoEtapaController@store"
                },
                {
                    "host": null,
                    "methods": [
                        "DELETE"
                    ],
                    "uri": "evento\/eventoetapa\/{eventoetapa}",
                    "name": "evento.eventoetapa.destroy",
                    "action":"App\Http\Controllers\EventoEtapaController@destroy"
                },
                {
                    "host": null,
                    "methods": [
                        "GET",
                        "HEAD"
                    ],
                    "uri": "evento",
                    "name": "evento.index",
                    "action":"App\Http\Controllers\EventoController@index"
                },
                {
                    "host": null,
                    "methods": [
                        "GET",
                        "HEAD"
                    ],
                    "uri": "evento\/create",
                    "name": "evento.create",
                    "action":"App\Http\Controllers\EventoController@create"
                },
                {
                    "host": null,
                    "methods": [
                        "POST"
                    ],
                    "uri": "evento",
                    "name": "evento.store",
                    "action":"App\Http\Controllers\EventoController@store"
                },
                {
                    "host": null,
                    "methods": [
                        "GET",
                        "HEAD"
                    ],
                    "uri": "evento\/{evento}",
                    "name": "evento.show",
                    "action":"App\Http\Controllers\EventoController@show"
                },
                {
                    "host": null,
                    "methods": [
                        "GET",
                        "HEAD"
                    ],
                    "uri": "evento\/{evento}\/edit",
                    "name": "evento.edit",
                    "action":"App\Http\Controllers\EventoController@edit"
                },
                {
                    "host": null,
                    "methods": [
                        "PUT",
                        "PATCH"
                    ],
                    "uri": "evento\/{evento}",
                    "name": "evento.update",
                    "action":"App\Http\Controllers\EventoController@update"
                },
                {
                    "host": null,
                    "methods": [
                        "GET",
                        "HEAD"
                    ],
                    "uri": "beneficio",
                    "name": "beneficio.index",
                    "action":"App\Http\Controllers\BeneficioEventoController@index"
                },
                {
                    "host": null,
                    "methods": [
                        "POST"
                    ],
                    "uri": "beneficio",
                    "name": "beneficio.store",
                    "action":"App\Http\Controllers\BeneficioEventoController@store"
                },
                {
                    "host": null,
                    "methods": [
                        "GET",
                        "HEAD"
                    ],
                    "uri": "patrocinador",
                    "name": "patrocinador.index",
                    "action":"App\Http\Controllers\PatrocinadorController@index"
                },
                {
                    "host": null,
                    "methods": [
                        "POST"
                    ],
                    "uri": "patrocinador",
                    "name": "patrocinador.store",
                    "action":"App\Http\Controllers\PatrocinadorController@store"
                },
                {
                    "host": null,
                    "methods": [
                        "GET",
                        "HEAD"
                    ],
                    "uri": "actividad",
                    "name": "actividad.index",
                    "action":"App\Http\Controllers\LogActividadController@index"
                },
                {
                    "host": null,
                    "methods": [
                        "GET",
                        "HEAD"
                    ],
                    "uri": "tag",
                    "name": "tag.index",
                    "action":"App\Http\Controllers\TagController@index"
                },
                {
                    "host": null,
                    "methods": [
                        "GET",
                        "HEAD"
                    ],
                    "uri": "patrocinadorCurso",
                    "name": "patrocinadorCurso.index",
                    "action":"App\Http\Controllers\PatrocinadorCursoController@index"
                },
                {
                    "host": null,
                    "methods": [
                        "POST"
                    ],
                    "uri": "patrocinadorCurso",
                    "name": "patrocinadorCurso.store",
                    "action":"App\Http\Controllers\PatrocinadorCursoController@store"
                },
                {
                    "host": null,
                    "methods": [
                        "GET",
                        "HEAD"
                    ],
                    "uri": "adjunto\/proyecto",
                    "name": "adjunto.proyecto.index",
                    "action":"App\Http\Controllers\AdjuntoProyectoController@index"
                },
                {
                    "host": null,
                    "methods": [
                        "POST"
                    ],
                    "uri": "adjunto\/proyecto",
                    "name": "adjunto.proyecto.store",
                    "action":"App\Http\Controllers\AdjuntoProyectoController@store"
                },
                {
                    "host": null,
                    "methods": [
                        "PUT",
                        "PATCH"
                    ],
                    "uri": "adjunto\/proyecto\/{usuario}",
                    "name": "adjunto.proyecto.update",
                    "action":"App\Http\Controllers\AdjuntoProyectoController@update"
                },
                {
                    "host": null,
                    "methods": [
                        "DELETE"
                    ],
                    "uri": "adjunto\/proyecto\/{usuario}",
                    "name": "adjunto.proyecto.destroy",
                    "action":"App\Http\Controllers\AdjuntoProyectoController@destroy"
                },
                {
                    "host": null,
                    "methods": [
                        "GET",
                        "HEAD"
                    ],
                    "uri": "proyecto",
                    "name": "proyecto.index",
                    "action":"App\Http\Controllers\ProyectoController@index"
                },
                {
                    "host": null,
                    "methods": [
                        "GET",
                        "HEAD"
                    ],
                    "uri": "adjunto\/evento",
                    "name": "adjunto.evento.index",
                    "action":"App\Http\Controllers\AdjuntoEventoController@index"
                },
                {
                    "host": null,
                    "methods": [
                        "POST"
                    ],
                    "uri": "adjunto\/evento",
                    "name": "adjunto.evento.store",
                    "action":"App\Http\Controllers\AdjuntoEventoController@store"
                },
                {
                    "host": null,
                    "methods": [
                        "PUT",
                        "PATCH"
                    ],
                    "uri": "adjunto\/evento\/{evento}",
                    "name": "adjunto.evento.update",
                    "action":"App\Http\Controllers\AdjuntoEventoController@update"
                },
                {
                    "host": null,
                    "methods": [
                        "DELETE"
                    ],
                    "uri": "adjunto\/evento\/{evento}",
                    "name": "adjunto.evento.destroy",
                    "action":"App\Http\Controllers\AdjuntoEventoController@destroy"
                },
                {
                    "host": null,
                    "methods": [
                        "GET"
                    ],
                    "uri": "eventos\/usuario",
                    "name": "eventos.usuario.index",
                    "action":"App\Http\Controllers\EventosUsuarioController@index"
                },
                {
                    "host": null,
                    "methods": [
                        "GET",
                        "HEAD"
                    ],
                    "uri": "eventos\/usuario\/{usuario}",
                    "name": "eventos.usuario.show",
                    "action":"App\Http\Controllers\EventosUsuarioController@show"
                },
                {
                    "host": null,
                    "methods": [
                        "GET",
                        "HEAD"
                    ],
                    "uri": "evaluacion\/create",
                    "name": "evaluacion.create",
                    "action":"App\Http\Controllers\EvaluacionEmprendimientoController@create"
                },
                {
                    "host": null,
                    "methods": [
                        "POST"
                    ],
                    "uri": "evaluacion",
                    "name": "evaluacion.store",
                    "action":"App\Http\Controllers\EvaluacionEmprendimientoController@store"
                },
                {
                    "host": null,
                    "methods": [
                        "PUT",
                        "PATCH"
                    ],
                    "uri": "evaluacion\/{evaluacion}",
                    "name": "evaluacion.update",
                    "action":"App\Http\Controllers\EvaluacionEmprendimientoController@update"
                },
                {
                    "host": null,
                    "methods": [
                        "GET",
                        "HEAD"
                    ],
                    "uri": "diagnosticos",
                    "name": "diagnosticos.index",
                    "action":"App\Http\Controllers\DiagnosticosController@index"
                },
                {
                    "host": null,
                    "methods": [
                        "GET",
                        "HEAD"
                    ],
                    "uri": "diagnosticos\/create",
                    "name": "diagnosticos.create",
                    "action":"App\Http\Controllers\DiagnosticosController@create"
                },
                {
                    "host": null,
                    "methods": [
                        "POST"
                    ],
                    "uri": "diagnosticos",
                    "name": "diagnosticos.store",
                    "action":"App\Http\Controllers\DiagnosticosController@store"
                },
                {
                    "host": null,
                    "methods": [
                        "GET",
                        "HEAD"
                    ],
                    "uri": "diagnosticos\/{diagnostico}\/edit",
                    "name": "diagnosticos.edit",
                    "action":"App\Http\Controllers\DiagnosticosController@edit"
                },
                {
                    "host": null,
                    "methods": [
                        "GET",
                        "HEAD"
                    ],
                    "uri": "diagnosticos\/madurez",
                    "name": "diagnosticos.madurez.index",
                    "action":"App\Http\Controllers\NivelMadurezController@index"
                },
                {
                    "host": null,
                    "methods": [
                        "POST"
                    ],
                    "uri": "diagnosticos\/madurez",
                    "name": "diagnosticos.madurez.store",
                    "action":"App\Http\Controllers\NivelMadurezController@store"
                },
                {
                    "host": null,
                    "methods": [
                        "DELETE"
                    ],
                    "uri": "diagnosticos\/madurez\/{nivel}",
                    "name": "diagnosticos.madurez.destroy",
                    "action":"App\Http\Controllers\NivelMadurezController@destroy"
                },
                {
                    "host": null,
                    "methods": [
                        "GET",
                        "HEAD"
                    ],
                    "uri": "diagnosticos\/madurez\/{nivel}\/edit",
                    "name": "diagnosticos.madurez.edit",
                    "action":"App\Http\Controllers\NivelMadurezController@edit"
                },
                {
                    "host": null,
                    "methods": [
                        "GET",
                        "HEAD"
                    ],
                    "uri": "diagnosticos\/madurez\/{diagnostico}\/show",
                    "name": "diagnosticos.madurez.show",
                    "action":"App\Http\Controllers\NivelMadurezController@show"
                },
                {
                    "host": null,
                    "methods": [
                        "GET",
                        "HEAD"
                    ],
                    "uri": "diagnosticos\/unidades",
                    "name": "diagnosticos.unidades.index",
                    "action":"App\Http\Controllers\UnidadesProductivasController@index"
                },
                {
                    "host": null,
                    "methods": [
                        "POST"
                    ],
                    "uri": "diagnosticos\/unidades",
                    "name": "diagnosticos.unidades.store",
                    "action":"App\Http\Controllers\UnidadesProductivasController@store"
                },
                {
                    "host": null,
                    "methods": [
                        "DELETE"
                    ],
                    "uri": "diagnosticos\/unidades\/{nivel}",
                    "name": "diagnosticos.unidades.destroy",
                    "action":"App\Http\Controllers\UnidadesProductivasController@destroy"
                },
                {
                    "host": null,
                    "methods": [
                        "GET",
                        "HEAD"
                    ],
                    "uri": "diagnosticos\/unidades\/{nivel}\/edit",
                    "name": "diagnosticos.unidades.edit",
                    "action":"App\Http\Controllers\UnidadesProductivasController@edit"
                },
                {
                    "host": null,
                    "methods": [
                        "GET",
                        "HEAD"
                    ],
                    "uri": "asignacion-diagnostico",
                    "name": "asignacion-diagnostico.index",
                    "action":"App\Http\Controllers\ModuloDiagnosticoController@index"
                },
                {
                    "host": null,
                    "methods": [
                        "GET",
                        "HEAD"
                    ],
                    "uri": "diagnosticos\/preguntasunidad",
                    "name": "diagnosticos.preguntasunidad.index",
                    "action":"App\Http\Controllers\PreguntasUnidadProductivaController@index"
                },
                {
                    "host": null,
                    "methods": [
                        "POST"
                    ],
                    "uri": "diagnosticos\/preguntasunidad",
                    "name": "diagnosticos.preguntasunidad.store",
                    "action":"App\Http\Controllers\PreguntasUnidadProductivaController@store"
                },
        ],
            prefix: '',

            route : function (name, parameters, route) {
                route = route || this.getByName(name);

                if ( ! route ) {
                    return undefined;
                }

                return this.toRoute(route, parameters);
            },

            url: function (url, parameters) {
                parameters = parameters || [];

                var uri = url + '/' + parameters.join('/');

                return this.getCorrectUrl(uri);
            },

            toRoute : function (route, parameters) {
                var uri = this.replaceNamedParameters(route.uri, parameters);
                var qs  = this.getRouteQueryString(parameters);

                if (this.absolute && this.isOtherHost(route)){
                    return "//" + route.host + "/" + uri + qs;
                }

                return this.getCorrectUrl(uri + qs);
            },

            isOtherHost: function (route){
                return route.host && route.host != window.location.hostname;
            },

            replaceNamedParameters : function (uri, parameters) {
                uri = uri.replace(/\{(.*?)\??\}/g, function(match, key) {
                    if (parameters.hasOwnProperty(key)) {
                        var value = parameters[key];
                        delete parameters[key];
                        return value;
                    } else {
                        return match;
                    }
                });

                // Strip out any optional parameters that were not given
                uri = uri.replace(/\/\{.*?\?\}/g, '');

                return uri;
            },

            getRouteQueryString : function (parameters) {
                var qs = [];
                for (var key in parameters) {
                    if (parameters.hasOwnProperty(key)) {
                        qs.push(key + '=' + parameters[key]);
                    }
                }

                if (qs.length < 1) {
                    return '';
                }

                return '?' + qs.join('&');
            },

            getByName : function (name) {
                for (var key in this.routes) {
                    if (this.routes.hasOwnProperty(key) && this.routes[key].name === name) {
                        return this.routes[key];
                    }
                }
            },

            getByAction : function(action) {
                for (var key in this.routes) {
                    if (this.routes.hasOwnProperty(key) && this.routes[key].action === action) {
                        return this.routes[key];
                    }
                }
            },

            getCorrectUrl: function (uri) {
                var url = this.prefix + '/' + uri.replace(/^\/?/, '');

                if ( ! this.absolute) {
                    return url;
                }

                return this.rootUrl.replace('/\/?$/', '') + url;
            }
        };

        var getLinkAttributes = function(attributes) {
            if ( ! attributes) {
                return '';
            }

            var attrs = [];
            for (var key in attributes) {
                if (attributes.hasOwnProperty(key)) {
                    attrs.push(key + '="' + attributes[key] + '"');
                }
            }

            return attrs.join(' ');
        };

        var getHtmlLink = function (url, title, attributes) {
            title      = title || url;
            attributes = getLinkAttributes(attributes);

            return '<a href="' + url + '" ' + attributes + '>' + title + '</a>';
        };

        return {
            // Generate a url for a given controller action.
            // Route.action('HomeController@getIndex', [params = {}])
            action : function (name, parameters) {
                parameters = parameters || {};

                return routes.route(name, parameters, routes.getByAction(name));
            },

            // Generate a url for a given named route.
            // Route.route('routeName', [params = {}])
            route : function (route, parameters) {
                parameters = parameters || {};

                return routes.route(route, parameters);
            },

            // Generate a fully qualified URL to the given path.
            // Route.route('url', [params = {}])
            url : function (route, parameters) {
                parameters = parameters || {};

                return routes.url(route, parameters);
            },

            // Generate a html link to the given url.
            // Route.link_to('foo/bar', [title = url], [attributes = {}])
            link_to : function (url, title, attributes) {
                url = this.url(url);

                return getHtmlLink(url, title, attributes);
            },

            // Generate a html link to the given route.
            // Route.link_to_route('route.name', [title=url], [parameters = {}], [attributes = {}])
            link_to_route : function (route, title, parameters, attributes) {
                var url = this.route(route, parameters);

                return getHtmlLink(url, title, attributes);
            },

            // Generate a html link to the given controller action.
            // Route.link_to_action('HomeController@getIndex', [title=url], [parameters = {}], [attributes = {}])
            link_to_action : function(action, title, parameters, attributes) {
                var url = this.action(action, parameters);

                return getHtmlLink(url, title, attributes);
            }

        };

    }).call(this);

    /**
     * Expose the class either via AMD, CommonJS or the global object
     */
    if (typeof define === 'function' && define.amd) {
        define(function () {
            return laroute;
        });
    }
    else if (typeof module === 'object' && module.exports){
        module.exports = laroute;
    }
    else {
        window.Route = laroute;
    }

}).call(this);

