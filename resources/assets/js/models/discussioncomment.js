/**
* Class DiscussionCommentModel of Backbone Model
* @author TuProyecto || Desarrollador : @xindykatalina
* @link http://TuProyecto.com
*/

//Global App Backbone
app || (app = {});

(function (window, document, undefined) {

    app.DiscussionCommentModel = Backbone.Model.extend({

        urlRoot: function () {
            return window.Misc.urlFull( Route.route('discusion.comentario.index') );
        },
        idAttribute: 'codigo_comentario',
        defaults: {
            'codigo_discucion': 0,
            'texto_comentario': '',
            'imagen_usuario': '',
            'nickname_usuario': '',
            'contador_like': 0,            
            'fecha_comentario': '',
            'hora_comentario': '',
            'srcparams': '',
            'nombre_usuario': '',
            'owner': false,
            'id_comentario': 0,
            'url_profile': ''
        },

        /**
        * Constructor Method
        */
        initialize: function(attrs, opts){

        },
    });

})(window, document);
