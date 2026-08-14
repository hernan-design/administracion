/**
* Class FormVersionModel of Backbone Model
* @author TuProyecto || Desarrollador : @danielo
* @link http://TuProyecto.com
*/

//Global App Backbone
app || (app = {});

(function (window, document, undefined) {

     app.FormVersionModel = Backbone.Model.extend({

        urlRoot: function () {
            return window.Misc.urlFull( Route.route('formulario.version.index') );
        },
        idAttribute: 'codigo_version_encuesta',
        defaults: {
            //
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
