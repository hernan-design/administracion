/**
* Class PurchasesList extends of Backbone Collection
* @author TuProyecto || Desarrollador : @dortegon
* @link http://TuProyecto.com
*/

//Global App Backbone
app || (app = {});

(function (window, document, undefined) {

    app.PurchasesList = Backbone.Collection.extend({

        url: function () {
            return window.Misc.urlFull( Route.route('marketplace.compra.index') );
        },
        model: app.PurchaseModel,

        /**
        * Constructor Method
        */
        initialize : function() {
            //
        }
   });

})(this, this.document);
