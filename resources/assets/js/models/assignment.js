/**
* Class AssignmentModel of Backbone Model
* @author TuProyecto || Desarrollador : @xindykatalina
* @link http://TuProyecto.com
*/

//Global App Backbone
app || (app = {});

(function (window, document, undefined) {

     app.AssignmentModel = Backbone.Model.extend({

        urlRoot: function () {
            return window.Misc.urlFull( Route.route('asignacion.index') );
        },
        idAttribute: 'codigo_asignacion_jurado',
        defaults: {
            'codigo_registro_fase_usuario': 0,
            'codigo_convocatoria': 0,
            'nombre_usuario': '',
            'fecha_asignacion_jurado': '',
            'nombre_estado': '',
            'correo_electronico': '',
            'fecha_evaluacion_jurado': '',
            'lugar_evaluacion_jurado': ''
        },

        /**
        * Constructor Method
        * @param Object attrs, model attributes
        * @param Object opts, model Options
        */
        initialize: function(attrs, opts){
            //
        }
    });

})(window, document);
