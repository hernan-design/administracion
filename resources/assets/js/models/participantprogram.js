/**
* Class ParticipantProgramModel extend of Backbone Model
* @author TuProyecto || Desarrollador : @xindykatalina
* @link http://TuProyecto.com
*/

//Global App Backbone
app || (app = {});

(function (window, document, undefined) {

    app.ParticipantProgramModel = Backbone.Model.extend({

        urlRoot: function () {
            return window.Misc.urlFull( Route.route('convocatoria.usuario.index') );
        },
        idAttribute: 'codigo_registro_fase_usuario',
        defaults: {
            'nombre_usuario': '',
            'imagen_usuario': '',
            'correo_electronico': '',
            'nombre_entidad': '',
            'codigo_orientacion_convocatoria': 0,
            'visualizacion_usuario': false,
            'codigo_convocatoria': 0,
            'codigo_registro_fase_usuario': 0,
            'total_evaluation': 0,
            'codigo_usuario': 0,
            'approved': false,
            'codigo_estado': 0
        },

        /**
        * Constructor Method
        */
        initialize: function(attrs, opts){
            
        }
    });

})(this, this.document);
