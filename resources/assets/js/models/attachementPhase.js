/**
 * Class CourseContentModel of Backbone Model
 * @author TuProyecto || Desarrollador : @brahian0701
 * @link http://TuProyecto.com
 */

//Global App Backbone
app || (app = {});

(function (window, document, undefined) {

    app.AttachmentPhaseModel = Backbone.Model.extend({

        urlRoot: function () {
            return window.Misc.urlFull( Route.route('convocatoria.fase.recurso.index') );
        },
        idAttribute: 'codigo_adjunto',
        defaults: {
            'direccion_adjunto': '',
            'nombre_adjunto': '',
            'codigo_adjunto_fase': '',
            'descripcion_adjunto_fase': ''
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
