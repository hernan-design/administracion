/**
* Class OrganizerModel of Backbone Model
* @author TuProyecto || Desarrollador : @dortegon
* @link http://TuProyecto.com
*/

//Global App Backbone
app || (app = {});

(function (window, document, undefined) {

    app.OrganizerModel = Backbone.Model.extend({

        urlRoot: function () {
            return window.Misc.urlFull( Route.route('patrocinador.index') );
        },
        idAttribute: 'codigo_patrocinador',
        defaults: {
            'nombre_evento': '',
            'imagen_patrocinador': '',
            'descripcion_patrocinador': ''
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
        validate: function (attrs, options) {
          //  console.log(attrs);
            // if (attrs.nombre_patrocinador == "" || attrs.imagen_patrocinador == "" || attrs.imagen_patrocinador == "") {
            //     return "Todos los campos son obligatorios";
            // }
        }
    });

})(window, document);
