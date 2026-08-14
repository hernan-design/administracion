/**
* Class EvaluationAssignmentModel extend of Backbone Model
* @author TuProyecto || Desarrollador : @xindykatalina
* @link http://TuProyecto.com
*/

//Global App Backbone
app || (app = {});

(function (window, document, undefined) {

    app.EvaluationAssignmentModel = Backbone.Model.extend({

        urlRoot: function () {
            return window.Misc.urlFull( Route.route('evaluacion.asignacion.index') );
        },
        idAttribute: 'codigo_registro_fase_usuario',
        defaults: {
            // 
        },

        /**
        * Constructor Method
        */
        initialize: function(attrs, opts){
            
        }
    });

})(this, this.document);
