/**
* Class ResultEvaluationModel of Backbone Model
* @author TuProyecto || Desarrollador : @xindykatalina
* @link http://TuProyecto.com
*/

//Global App Backbone
app || (app = {});

(function (window, document, undefined) {

     app.ResultEvaluationModel = Backbone.Model.extend({

        urlRoot: function () {
            return window.Misc.urlFull( Route.route('resultadoevaluacion.index') );
        },
        idAttribute: 'codigo_resultado_evaluacion',
        defaults: {
            'comentario_resultado_evaluacion': ''
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
