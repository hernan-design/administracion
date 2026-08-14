/**
* Class FormQuestionGroupModel of Backbone Model
* Class FormQuestionGroupModel of Backbone Model
* @author TuProyecto || Desarrollador : @dortegon
* @link http://TuProyecto.com
*/

//Global App Backbone
app || (app = {});

(function (window, document, undefined) {

    app.FormQuestionGroupModel = Backbone.Model.extend({

        urlRoot: function () {
            return window.Misc.urlFull( Route.route('formulario.grupo.index') );
        },
        idAttribute: 'codigo_grupo_pregunta_encuesta',
        defaults: {
            codigo_encuesta: null,
            nombre_grupo_pregunta_encuesta: '',
            desc_grupo_pregunta_encuesta: '',
            pagina_grupo_pregunta: null
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
