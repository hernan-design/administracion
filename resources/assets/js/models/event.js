/**
* Class EventModel of Backbone Model
* @author TuProyecto || Desarrollador : @dortegon
* @link http://TuProyecto.com
*/

//Global App Backbone
app || (app = {});

(function (window, document, undefined) {

     app.EventModel = Backbone.Model.extend({

        urlRoot: function () {
            return window.Misc.urlFull( Route.route('evento.index') );
        },
        idAttribute: 'codigo_evento',
        defaults: {
            'nombre_evento': '',
            'descripcion_evento': '',
            'fecha_evento': '',
            'lugar_evento': '',
            'hora_inicio_evento' : '',
            'hora_fin_evento': '',
            'codigo_calendario': '',
            'fecha_fin_evento': '',
            'codigo_ciudad': '',
            'url_evento_organizador': '',
            'codigo_privacidad_contenido': '',
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
