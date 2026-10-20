/**
* Class FormQuestionGroupsList extends of Backbone Collection
* @author TuProyecto || Desarrollador : @dortegon
* @link http://TuProyecto.com
*/

//Global App Backbone
app || (app = {});

(function (window, document, undefined) {

    app.FormQuestionGroupsList = Backbone.Collection.extend({
        mode: 'server',
        url: function () {
            return window.Misc.urlFull( Route.route('formulario.grupo.index') );
        },
        model: app.FormQuestionGroupModel,
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
