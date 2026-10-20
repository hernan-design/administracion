/**
* Class CourseModel of Backbone Model
* @author TuProyecto || Desarrollador : @dortegon
* @link http://TuProyecto.com
*/

//Global App Backbone
app || (app = {});

(function (window, document, undefined) {

     app.CourseModel = Backbone.Model.extend({

        urlRoot: function () {
            return window.Misc.urlFull( Route.route('curso.index') );
        },
        idAttribute: 'codigo_curso',
        defaults: {
            'nombre_curso': '',
            'descripcion_curso': '',
            'fecha_apertura_curso': '',
            'fecha_cierre_curso': '',
            'codigo_categoria_curso': '',
            'imagen_curso' : '',
            'codigo_orientacion_curso': '',
            'codigo_privacidad_contenido': '',
            'tipo_curso': '',
            'url_curso': '',
            'log_curso': false,
            'log_biofacial': false
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

})(window, document);
