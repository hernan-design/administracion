/**
* Class ProgramModel of Backbone Model
* @author TuProyecto || Desarrollador : @xindykatalina
* @link http://TuProyecto.com
*/

//Global App Backbone
app || (app = {});

(function (window, document, undefined) {

     app.ProgramModel = Backbone.Model.extend({

        urlRoot: function () {
            return window.Misc.urlFull( Route.route('convocatoria.index') );
        },
        idAttribute: 'codigo_convocatoria',
        defaults: {
            'codigo_usuario': '',
            'url_convocatoria': '',
            'nombre_convocatoria' : '',
            'imagen_convocatoria' : '',
            'banner_convocatoria' : '',
            'contador_inscritos' : 0,
            'descripcion_convocatoria' : '',
            'resumen_convocatoria' : '',
            'tipo_convocatoria': '',
            'url_externa_convocatoria': '',
            'terminos_convocatoria' : '',
            'politicas_convocatoria' : '',
            'codigo_orientacion_convocatoria': 0,
            'fecha_cierre_registro': '',
            'fecha_cierre_convocatoria': '',
            'hora_cierre_convocatoria': '',
            'texto_boton': '',
            'permanente_convocatoria': '',
            'visualizacion_usuario': '',
            'evaluacion_convocatoria': '',
            'fases' : [],
            'documentos' : [],
            'registered': false,
            'registered_entity': false,
            'opened': false,
            'codigo_estado': 0,
            'contador_like': 0,
            'srcparams': '',
            'userentitys': [],
            'type_user': 0,
            'url_encuesta': '',
            'codigo_etapa_proyecto': '',
            'seg_codigo_usuario': '',
        },

        /**
        * Constructor Method
        * @param Object attrs, model attributes
        * @param Object opts, model Options
        */
        initialize: function(attrs, opts){
            //
        },

        /**
        * validate attributes model in the change
        * @param Object. modified attributes model
        */
        validate: function (attrs) {

        }
    });

})(this, this.document);
