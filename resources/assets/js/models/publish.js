/**
* Class PublishModel extend of Backbone Model
* @author TuProyecto || Desarrollador : @dortegon
* @link http://TuProyecto.com
*/

//Global App Backbone
app || (app = {});

(function (window, document, undefined) {

    app.PublishModel = Backbone.Model.extend({

        urlRoot: function () {
            return window.Misc.urlFull( Route.route('comunidad.publicacion.index') );
        },
        idAttribute: 'codigo_muro',
        defaults: {
            //
        }
    });

})(window, document);
