/**
* Class patrocinadorCurso of Backbone Model
* @author TuProyecto || Desarrollador : @ayriel
* @link http://TuProyecto.com
*/

//Global App Backbone
app || (app = {});

(function (window, document, undefined) {

    app.OrganizerCourseModel = Backbone.Model.extend({

        urlRoot: function () {
            return window.Misc.urlFull( Route.route('patrocinadorCurso.index') );
        },
        idAttribute: 'id',
        defaults: {
           // 'codigo_patrocinador': ''
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
        validate: function (attrs, options) {
            console.log(attrs);
          //  if (attrs.nombre_patrocinador == "" || attrs.imagen_patrocinador == "") {
          //      return "Ambos campos son obligatorios";
          //  }
        }
    });

})(window, document);
