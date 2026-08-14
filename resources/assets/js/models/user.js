/**
* Class UserModel of Backbone Model
* @author TuProyecto || Desarrollador : @dortegon
* @link http://TuProyecto.com
*/

//Global App Backbone
app || (app = {});

(function (window, document, undefined) {

     app.UserModel = Backbone.Model.extend({

        urlRoot: function () {
            return window.Misc.urlFull( Route.route('usuario.index') );
        },
        idAttribute: 'codigo_usuario',
        defaults: {
            'nombre_usuario': '',
            'codigo_ciudad': '',
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
