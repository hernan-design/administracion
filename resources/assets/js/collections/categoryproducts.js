
//Global App Backbone
app || (app = {});

(function (window, document, undefined) {

    app.CategoryProductsList = Backbone.Collection.extend({

        url: function () {
            return window.Misc.urlFull( Route.route('categoriaproducto.index') );
        },
        model: app.CategoryProductModel,

        /**
        * Constructor Method
        */
        initialize : function(){
        }
   });

})(this, this.document);
