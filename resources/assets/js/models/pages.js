/**
 * Class PagesModel of Backbone Model
 * @author TuProyecto || Desarrollador : @backend01
 * @link http://TuProyecto.com
 */

//Global App Backbone
app || (app = {});

(function (window, document, undefined) {
    app.PagesModel = Backbone.Model.extend({
        urlRoot: function () {
            return window.Misc.urlFull( Route.route('formulario.paginas.index') );
        },
        idAttribute: 'codigo_pagina_encuesta',
        defaults: {
            'codigo_estado': 1,
            'codigo_encuesta': '',
            'pagina_grupo_encuesta': '',
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
    })
})(this, this.document);
