/**
 * Class FormQuestionGroupModel of Backbone Model
 * Class FormQuestionGroupModel of Backbone Model
 * @author TuProyecto || Desarrollador : @brahian0701
 * @link http://TuProyecto.com
 */

//Global App Backbone
app || (app = {});

(function (window, document, undefined) {

    app.FormSubquestionModel = Backbone.Model.extend({

        urlRoot: function () {
            return window.Misc.urlFull( Route.route('formulario.subpreguntas.index') );
        },
        idAttribute: 'codigo_subpregunta_encuesta',
        defaults: {
            codigo_pregunta_encuesta: null,
            cor_codigo_pregunta_encuesta: null,
            codigo_opcion_encuesta: null,
            codigo_version_encuesta: null,
            codigo_pagina_pregunta_encuesta: null
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
