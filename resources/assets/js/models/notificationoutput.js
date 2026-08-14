/**
* Class NotificationOutputModel extend of Backbone Model
* @author TuProyecto || Desarrollador : @dortegon
* @link http://TuProyecto.com
*/

//Global App Backbone
app || (app = {});

(function (window, document, undefined) {

    app.NotificationOutputModel = Backbone.Model.extend({

        urlRoot: function () {
            return window.Misc.urlFull( Route.route('notificacion.output.index') );
        },
        idAttribute: 'codigo_notificacion',
        defaults: {
            //
        }
    });

})(this, this.document);
