/**
* Class MarketplaceAssignmentList extends of Backbone Collection
* @author TuProyecto || Desarrollador : @krobing
* @link http://TuProyecto.com
*/

//Global App Backbone
app || (app = {});

(function (window, document, undefined) {

    app.MarketplaceAssignmentList = Backbone.Collection.extend({

        url: function () {
            return window.Misc.urlFull( Route.route('marketplace.asignaciones.index') );
        },
        model: app.MarketplaceAssignmentModel,

        /**
        * Constructor Method
        */
        initialize : function() {
            //
        }
   });

})(window, document);
