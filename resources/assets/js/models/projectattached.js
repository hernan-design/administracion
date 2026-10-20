/**
* Class ProjectModel of Backbone Model
* @author TuProyecto || Desarrollador : @dortegon
* @link http://TuProyecto.com
*/

//Global App Backbone
app || (app = {});

(function (window, document, undefined) {

     app.ProjectAttachedModel = Backbone.Model.extend({

        urlRoot: function () {
            return window.Misc.urlFull( Route.route('proyecto.index') );
        },
        idAttribute: 'codigo_entidad',
        defaults: {
			'probando_modelo': true
			/*'id_entidad': 0,
			'url_nickname': '',
			'nickname_usuario': '',
			'nombre_usuario': '',
			'biografia_usuario': '',
			'descripcion_entidad': null,
			'codigo_etapa_proyecto': 0,
			'codigo_categoria_proyecto': 0,
			'cor_codigo_categoria_proyecto': null,
			'codigo_clase_emprendimiento': 0,
			'codigo_ciudad': '',
			'nombre_ciudad': '',
			'nombre_pais': '',
			'codigo_privacidad_contenido': 0,
			'contador_like': 0,
			'srcparams': '',
			'updated_at': '',
			'nombre_clase_emprendimiento': '',
			'identificacion_usuario': '',
			'codigo_categoria_clase_espacio': 0,
			'owner': false,
			'creator': false,
			'codigo_usuario': 0,
			'nombre_privacidad_contenido': '',
            'stage': '',
			'descripcion_adicional_usuario': '',
            'modelo_negocio_institucion': false,
            'cargo_entidad': 0,*/
		},

        /**
        * Constructor Method
        * @param Object attrs, model attributes
        * @param Object opts, model Options
        */
        initialize: function(attrs, opts){
            //
            console.log('en modelo ProjectAttachedModel');
        },

        /**
        * validate attributes model in the change
        * @param Object. modified attributes model
        */
        validate: function (attrs) {

        }
    });

})(window, document);
