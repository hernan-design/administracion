/**
* Class ActivityLogModel of Backbone Model
* @author TuProyecto || Desarrollador : @dortegon
* @link http://TuProyecto.com
*/

//Global App Backbone
app || (app = {});

(function (window, document, undefined) {

     app.ActivityLogModel = Backbone.Model.extend({

        urlRoot: function () {
            return window.Misc.urlFull( Route.route('actividad.index') );
        },
        idAttribute: 'id',
        defaults: {
            'description': '',
            'properties': '',
            'causer_type' : '',
            'log_name': '',
            'subject_type': '',
            'subject_id': '',
            'causer_id': '',
            'updated_at': '',
            'created_at': ''
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
