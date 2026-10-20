/**
* Class FormVersionsList extends of Backbone Collection
* @author TuProyecto || Desarrollador : @dortegon
* @link http://TuProyecto.com
*/

//Global App Backbone
app || (app = {});

(function (window, document, undefined) {

    app.FormVersionsList = Backbone.Collection.extend({
        url: function () {
            return window.Misc.urlFull( Route.route('formulario.version.index') );
        },
        model: app.FormVersionModel
   });

})(window, document);
