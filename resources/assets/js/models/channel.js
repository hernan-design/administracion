/**
* Class ChannelModel of Backbone Model
* @author TuProyecto || Desarrollador : @dortegon
* @link http://TuProyecto.com
*/

//Global App Backbone
app || (app = {});

(function (window, document, undefined) {

     app.ChannelModel = Backbone.Model.extend({

        urlRoot: function () {
            return window.Misc.urlFull( Route.route('comunidad.canal.index') );
        },
        idAttribute: 'codigo_canal',
        defaults: {
            'codigo_estado': '',
            'codigo_institucion': '',
            'nombre_canal': '',
            'descripcion_canal': '',
            'canal_abierto': '',
            'canal_defecto': '',
            'tipo_canal': '',
            'codigo_categoria_canal': ''
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

})(this, this.document);
