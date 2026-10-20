/**
* Class ProjectModel of Backbone Model
* @author TuProyecto || Desarrollador : @dortegon
* @link http://TuProyecto.com
*/

//Global App Backbone
app || (app = {});

(function (window, document, undefined) {

     app.ProjectModel = Backbone.Model.extend({

        urlRoot: function () {
            return window.Misc.urlFull( Route.route('entidad.index') );
        },
        idAttribute: 'id',
        defaults: {
            'biografia_usuario': '',
            'imagen_usuario': ''
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

})(window, document);
