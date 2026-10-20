/**
* Class ProfessionChannelModel of Backbone Model
* @author TuProyecto || Desarrollador : @dortegon
* @link http://TuProyecto.com
*/

//Global App Backbone
app || (app = {});

(function (window, document, undefined) {

     app.ProfessionChannelModel = Backbone.Model.extend({

        urlRoot: function () {
            return window.Misc.urlFull( Route.route('comunidad.canal.profesion.index') );
        },
        idAttribute: 'codigo_vinculo_canal',
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

})(window, document);
