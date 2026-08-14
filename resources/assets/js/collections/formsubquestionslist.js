/**
 * Class FormQuestionGroupsList extends of Backbone Collection
 * @author TuProyecto || Desarrollador : @brahian0701
 * @link http://TuProyecto.com
 */

//Global App Backbone
app || (app = {});

(function (window, document, undefined) {

    app.FormSubquestionsList = Backbone.Collection.extend({
        mode: 'server',
        url: function () {
            return window.Misc.urlFull( Route.route('formulario.subpreguntas.index') );
        },
        model: app.FormSubquestionModel,
        state: {
            //
        },

        /**
         * Constructor Method
         */
        initialize : function(){
        }
    });

})(this, this.document);
