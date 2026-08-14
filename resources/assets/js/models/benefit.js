/**
* Class BenefitEventModel of Backbone Model
* @author TuProyecto || Desarrollador : @dortegon
* @link http://TuProyecto.com
*/

//Global App Backbone
app || (app = {});

(function (window, document, undefined) {

     app.BenefitEventModel = Backbone.Model.extend({

        urlRoot: function () {
            return window.Misc.urlFull( Route.route('beneficio.index') );
        },
        idAttribute: 'codigo_beneficio_evento',
        defaults: {
            'nombre_beneficio_evento': '',
            'codigo_estado': ''
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
