/**
* Class MarketplaceAssignmentModel extends of Backbone Model
* @author TuProyecto || Desarrollador : @krobing
* @link http://TuProyecto.com
*/

//Global App Backbone
app || (app = {});

(function (window, document, undefined) {

     app.MarketplaceAssignmentModel = Backbone.Model.extend({

        urlRoot: function () {
            return window.Misc.urlFull( Route.route('marketplace.asignaciones.index') );
        },
        idAttribute: 'codigo_asignacion_mp',
        defaults: {
            'codigo_registro_fase_usuario' : 0,
            'fecha_asignacion_mp' : '',
            'nombre_usuario' : '',
            'correo_electronico' : '',
            'codigo_estado' : null,
            'nombre_estado' : 0,
            'codigo_registro_fase_usuario' : 0
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
        * Validate attributes
        * @param Object attrs
        * @param Object opts
        */
        validate: function(attrs, opts) {
            // if (!attrs.codigo_asignacion_mp) {
            //     return "El codigo de la asignación no esta asignado";
            // }
        }
    });

})(this, this.document);
