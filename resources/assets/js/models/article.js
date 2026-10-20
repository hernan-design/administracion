/**
* Class ArticleModel of Backbone Model
* @author TuProyecto || Desarrollador : @dortegon
* @link http://TuProyecto.com
*/

//Global App Backbone
app || (app = {});

(function (window, document, undefined) {

     app.ArticleModel = Backbone.Model.extend({

        urlRoot: function () {
            return window.Misc.urlFull( Route.route('contenido.index') );
        },
        idAttribute: 'codigo_contenido',
        defaults: {
            'nombre_contenido': '',
            'imagen_contenido': '',
            'texto_contenido' : '',
            'resumen_contenido': '',
            'url_contenido': '',
            'imagen_contenido': '',
            'fuente_contenido': '',
            'landing_contenido': '',
            'privacidad_contenido': '',
            'titulo_facebook_contenido': '',
            'descripcion_facebook_contenido': '',
            'imagen_facebook_contenido': '',
            'titulo_twitter_contenido': '',
            'descripcion_twitter_contenido': '',
            'imagen_twitter_contenido': '',
            'tags': ''
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
