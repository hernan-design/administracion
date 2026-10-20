//Global App Backbone
app || (app = {});

(function (window, document, undefined) {

    app.ResultEvaluationList = Backbone.Collection.extend({

        url: function () {
            return window.Misc.urlFull( Route.route('resultadoevaluacion.index') );
        },
        model: app.ResultEvaluationModel,

        /**
        * Constructor Method
        */
        initialize : function(){
        }
   });

})(window, document);
