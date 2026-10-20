/**
* Class OptionsQuestionList extends of Backbone Collection
* @author TuProyecto || Desarrollador : @dortegon
* @link http://TuProyecto.com
*/

//Global App Backbone
app || (app = {});

(function (window, document, undefined) {

    app.OptionsQuestionList = Backbone.Collection.extend({
        mode: 'server',
        url: function () {
            return window.Misc.urlFull( Route.route('formulario.pregunta.opcion.index') );
        },
        model: app.OptionQuestionModel,
        state: {
            //
        },

        /**
        * Constructor Method
        */
        initialize : function(){
        }
   });

})(window, document);
