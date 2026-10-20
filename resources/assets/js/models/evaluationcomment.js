/**
* Class EvaluationCommentModel of Backbone Model
* @author TuProyecto || Desarrollador : @xindykatalina
* @link http://TuProyecto.com
*/

//Global App Backbone
app || (app = {});

(function (window, document, undefined) {

     app.EvaluationCommentModel = Backbone.Model.extend({

        urlRoot: function () {
            return window.Misc.urlFull( Route.route('evaluacion.comentario.index') );
        },
        idAttribute: 'codigo_coment_eval_emprendi',
        defaults: {
            'name_coment_eval_emprendi': '',
            'texto_coment_eval_emprendi': '',
            'codigo_evaluacion_emprendimiento':null,
        },

        /**
        * Constructor Method
        * @param Object attrs, model attributes
        * @param Object opts, model Options
        */
        initialize: function(attrs, opts){

        },

        /**
        * validate attributes model in the change
        * @param Object. modified attributes model
        */
        validate: function (attrs) {

        }
    });

})(window, document);
