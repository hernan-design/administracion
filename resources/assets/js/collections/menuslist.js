//Global App Backbone
app || (app = {});

(function (window, document, undefined) {

    app.MenuList = Backbone.Collection.extend({

        url: function () {
        	return window.Misc.urlFull( Route.route('menu.index') );
        },
        model: app.MenuModel,

        /**
        * Constructor Method
        */
        initialize : function(){
        }
   });

})(window, document);
