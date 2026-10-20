/**
* Class SessionModel of Backbone Model
* @author TuProyecto || Desarrollador : @dortegon
* @link http://TuProyecto.com
*/

//Global App Backbone
app || (app = {});

(function (window, document, undefined) {

     app.SessionModel = Backbone.Model.extend({

        urlRoot: function () {
            return window.Misc.urlFull( Route.route('evento.sesion.index') );
        },
        idAttribute: 'codigo_sesion_evento',
        defaults: {
            'codigo_evento': '',
            'codigo_estado': '',
            'fecha_inicio_sesion': '',
            'fecha_fin_sesion': '',
            'hora_inicio_sesion': '',
            'hora_fin_sesion': '',
            'numero_sesion': ''
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
