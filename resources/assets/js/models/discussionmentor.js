/**
* Class DiscussionMentorModel of Backbone Model
* @author TuProyecto || Desarrollador : @xindykatalina
* @link http://TuProyecto.com
*/

//Global App Backbone
app || (app = {});

(function (window, document, undefined) {

    app.DiscussionMentorModel = Backbone.Model.extend({

        urlRoot: function () {
            return window.Misc.urlFull( Route.route('mentoria.discusion.index') );
        },
        idAttribute: 'codigo_discucion',
        defaults: {
            codigo_usuario: 0,
            contador_comentarios_discucion: 0,
            contador_like: 0,
            fecha_discusion: '',
            imagen_usuario: '',
            nickname_usuario: '',
            nombre_usuario: '',
            texto_discucion: ''
        },

        /**
        * Constructor Method
        */
        initialize: function(attrs, opts){

        },
    });

})(window, document);
