/**
* Class EvaluationPurchaseModel of Backbone Model
* @author TuProyecto || Desarrollador : @danielortegon
* @link http://TuProyecto.com
*/

//Global App Backbone
app || (app = {});

(function (window, document, undefined) {

     app.EvaluationPurchaseModel = Backbone.Model.extend({

        urlRoot: function () {
            return window.Misc.urlFull( Route.route('marketplace.transacciones.evaluacion.index') );
        },
        idAttribute: 'codigo_evaluacion_compra',
        defaults: {
            'codigo_compra' : '',
            'cometario_evaluacion_compra': '',
            'itemsevaluacion': [],
            'answers': [],
            'entregables': []
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
