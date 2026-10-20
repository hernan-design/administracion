/**
* Class PromotionModel of Backbone Model
* @author TuProyecto || Desarrollador : @dortegon
* @link http://TuProyecto.com
*/

//Global App Backbone
app || (app = {});

(function (window, document, undefined) {

    app.PromotionModel = Backbone.Model.extend({

        urlRoot: function () {
            return window.Misc.urlFull( Route.route('marketplace.promociones.index') );
        },
        idAttribute: 'codigo_promocion',
        defaults: {
            fecha_inicio_promocion: '',
            fecha_fin_promocion: '',
            imagen_promocion: '',
            tipo_promocion: 0,
            descripcion_promocion: '',
            codigo_usuario_institucion: 0
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
