/**
* Class MenuModel of Backbone Model
* @author TuProyecto || Desarrollador : @xindykatalina
* @link http://TuProyecto.com
*/

//Global App Backbone
app || (app = {});

(function (window, document, undefined) {

     app.MenuModel = Backbone.Model.extend({

        urlRoot: function () {
            return window.Misc.urlFull( Route.route('menu.index') );
        },
        idAttribute: 'codigo_menu_institucion',
        defaults: {
            'codigo_menu': 0,
            'nombre_item_menu': '',
            'tipo_enlace_menu_institucion': '',
            'menu_padre': '',
            'icono_menu_item': '',
            'ruta_menu_institucion': '',
            'descripcion_menu_item': '',
            'tipo_menu_institucion': 0 
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