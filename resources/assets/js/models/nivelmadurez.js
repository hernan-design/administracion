/**
* Class NivelMadurezModel of Backbone Model
* @author TuProyecto || Desarrollador : @dortegon
* @link http://TuProyecto.com
*/

//Global App Backbone
app || (app = {});

(function (window, document, undefined) {

     app.NivelMadurezModel = Backbone.Model.extend({

        urlRoot: function () {
            return window.Misc.urlFull( Route.route('diagnosticos.madurez.index') );
        },
        idAttribute: 'codigo_nivel_madurez',
        defaults: {
            'nombre_nivel_madurez': '',
            'descripcion_nivel_madurez': '',
            'rango_minimo': '',
            'rango_maximo': '',
            'codigo_diagnostico': '',
            'estado': ''
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
