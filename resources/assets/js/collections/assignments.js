//Global App Backbone
app || (app = {});

(function (window, document, undefined) {

    app.AssignmentList = Backbone.Collection.extend({

        url: function () {
            return window.Misc.urlFull( Route.route('asignacion.index') );
        },
        model: app.AssignmentModel,

        /**
        * Constructor Method
        */
        initialize : function(){
        }
   });

})(window, document);
