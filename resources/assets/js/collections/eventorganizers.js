/**
* Class EventOrganizersList extends of Backbone Collection
* @author TuProyecto || Desarrollador : @dortegon
* @link http://TuProyecto.com
*/

//Global App Backbone
app || (app = {});

(function (window, document, undefined) {

    app.EventOrganizersList = Backbone.Collection.extend({
        url: function () {
            return window.Misc.urlFull( Route.route('evento.organizador.index') );
        },
        model: app.EventOrganizerModel
   });

})(this, this.document);
