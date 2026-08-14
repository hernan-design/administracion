/**
* Class EvaluationModel of Backbone Model
* @author TuProyecto || Desarrollador : @xindykatalina
* @link http://TuProyecto.com
*/

//Global App Backbone
app || (app = {});

(function (window, document, undefined) {

     app.EvaluationModel = Backbone.Model.extend({

        urlRoot: function () {
            return window.Misc.urlFull( Route.route('evaluacion.index') );
        },
        idAttribute: 'codigo_evaluacion_emprendimiento',
        defaults: {
            'nombre_evaluacion_emprendimiento': '',
            'descripcion_evaluacion_emprendimiento': ''
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
