/**
* Class FormModel of Backbone Model
* @author TuProyecto || Desarrollador : @danielo
* @link http://TuProyecto.com
*/

//Global App Backbone
app || (app = {});

(function (window, document, undefined) {

     app.FormModel = Backbone.Model.extend({

        urlRoot: function () {
            return window.Misc.urlFull( Route.route('formulario.index') );
        },
        idAttribute: 'codigo_encuesta',
        defaults: {
            'nombre_encuesta': '',
            'descripcion_encuesta': '',
            'imagen_encuesta': '',
            'label_boton_finalizar_encuesta' : '',
            'color_fondo_encuesta': '',
            'color_encuesta': '',
            'respuesta_encuesta': '',
            'tipo_paginacion_encuesta': '',
            'codigo_version_encuesta': '',
            'paginacion_encuesta': 1,
            'versions': []
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
