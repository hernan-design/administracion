/**
* Class UnidadesProductivasModel of Backbone Model
* @author TuProyecto || Desarrollador : @dortegon
* @link http://TuProyecto.com
*/

//Global App Backbone
app || (app = {});

(function (window, document, undefined) {

     app.PreguntasUnidadProductivaModel = Backbone.Model.extend({

        urlRoot: function () {
            return window.Misc.urlFull( Route.route('diagnosticos.preguntasunidad.index') );
        },
        idAttribute: 'codigo_pregunta_u_p',
        defaults: {
            'codigo_unidad_productiva': '',
            'codigo_pregunta_encuesta': ''
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
