/**
 * Class MentorModel of Backbone Model
 * @author TuProyecto || Desarrollador : @xindykatalina
 * @link http://TuProyecto.com
 */

//Global App Backbone
app || (app = {});

(function (window, document, undefined) {

    app.MentorModel = Backbone.Model.extend({

        urlRoot: function () {
            return window.Misc.urlFull( Route.route('mentoria.mentorprofile.index') );
        },
        idAttribute: 'codigo_usuario',
        defaults: {
            'nombre_usuario': '',
            'id_usuario': 0,
            'codigo_persona': 0,
            'experiencia_usuario': '',
            'forma_ayuda_mentoria': '',
            'nombre_especialidad': '',
            'codigo_especialidad': 0,
            'codigo_especialidad_destaca':0,
            'imagen_usuario': '',
            'nickname_usuario': '',
            'url_profile': '',
            'nombre_ciudad': '',
            'nombre_pais': '',
            'institucion': '',
            'owner': false,
            'specialties': []
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
