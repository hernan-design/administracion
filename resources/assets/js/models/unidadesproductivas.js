/**
* Class UnidadesProductivasModel of Backbone Model
* @author TuProyecto || Desarrollador : @dortegon
* @link http://TuProyecto.com
*/

//Global App Backbone
app || (app = {});

(function (window, document, undefined) {

     app.UnidadesProductivasModel = Backbone.Model.extend({

        urlRoot: function () {
            return window.Misc.urlFull( Route.route('diagnosticos.unidades.index') );
        },
        idAttribute: 'codigo_unidad_productiva',
        defaults: {
            'nombre_unidad_productiva': '',
            'descripcion_unidad_productiva': '',
            'porcentaje': '',
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
