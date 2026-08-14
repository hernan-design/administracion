/**
* Class CategoryProductModel of Backbone Model
* @author TuProyecto || Desarrollador : @xindykatalina
* @link http://TuProyecto.com
*/

//Global App Backbone
app || (app = {});

(function (window, document, undefined) {

     app.CategoryProductModel = Backbone.Model.extend({

        urlRoot: function () {
            return window.Misc.urlFull( Route.route('categoriaproducto.index') );
        },
        idAttribute: 'codigo_categoria_prod_insti',
        defaults: {
            'codigo_categoria_producto': 0,
            'nombre_categoria_producto': ''
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
