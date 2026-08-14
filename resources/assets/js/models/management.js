/**
* Class ManagementModel of Backbone Model
* @author TuProyecto || Desarrollador : @dortegon
* @link http://TuProyecto.com
*/

//Global App Backbone
app || (app = {});

(function (window, document, undefined) {

     app.ManagementModel = Backbone.Model.extend({

        urlRoot: function () {
            return window.Misc.urlFull( Route.route('reportes.gestion.index') );
        },
        idAttribute: 'key',
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
