/**
* Class EvaluationQuestionModel of Backbone Model
* @author TuProyecto || Desarrollador : @danielo
* @link http://TuProyecto.com
*/

//Global App Backbone
app || (app = {});

(function (window, document, undefined) {

     app.EvaluationQuestionModel = Backbone.Model.extend({

        urlRoot: function () {
            return window.Misc.urlFull( Route.route('evaluation.pregunta.index') );
        },
        idAttribute: 'codigo_pregunta_encuesta',
        defaults: {
            codigo_pregunta_encuesta: null,
            codigo_grupo_pregunta_encuesta: null,
            codigo_opcion_encuesta: null,
            numero_pregunta_encuesta: null,
            opciones: [],
            options: [],
            pregunta_padre: null,
            texto_pregunta_encuesta: '',
            texto_opcional_pregunta: '',
            tipo_campo_pregunta: null,
            tipo_pregunta_encuesta: null,
            clase_pregunta_encuesta: null
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
