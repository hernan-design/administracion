/**
* Class CourseContentModel of Backbone Model
* @author TuProyecto || Desarrollador : @dortegon
* @link http://TuProyecto.com
*/

//Global App Backbone
app || (app = {});

(function (window, document, undefined) {

     app.CourseContentModel = Backbone.Model.extend({

        urlRoot: function () {
            return window.Misc.urlFull( Route.route('curso.tema.contenido.index') );
        },
        idAttribute: 'codigo_contenido_curso',
        defaults: {
        	'titulo_contenido_curso': '',
        	'direccion_adjunto': '',
        	'tipo_contenido_curso': '',
        	'codigo_tipo_adjunto': ''
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
