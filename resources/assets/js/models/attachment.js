/**
* Class AttachmentModel of Backbone Model
* @author TuProyecto || Desarrollador : @dortegon
* @link http://TuProyecto.com
*/

//Global App Backbone
app || (app = {});

(function (window, document, undefined) {

     app.AttachmentModel = Backbone.Model.extend({

         urlRoot: function () {
             return window.Misc.urlFull( Route.route('adjuntos.index') );
         },
         idAttribute: 'codigo_adjunto',
         defaults: {
        	'codigo_tipo_adjunto': 1,
            'direccion_adjunto': '',
            'privacidad_adjunto': '',
            'nombre_adjunto': '',
            'codigo_privacidad_contenido': '',
            'fecha_adjunto': '',
             'codigo_convocatoria': ''
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
