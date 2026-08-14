/**
* Class CommitmentModel of Backbone Model
* @author TuProyecto || Desarrollador : @xindykatalina
* @link http://TuProyecto.com
*/

//Global App Backbone
app || (app = {});

(function (window, document, undefined) {

     app.CommitmentModel = Backbone.Model.extend({

        urlRoot: function () {
            return window.Misc.urlFull( Route.route('convocatoria.fase.compromiso.index') );
        },
        idAttribute: 'codigo_registro_fase_usuario',
        defaults: {
            'codigo_carta_compormiso_compra': 0,
            'codigo_usuario': 0,
            'codigo_estado': 0,
            'fecha_carta_compormiso_compra': '',
            'nombre_empresa': '',
            'nombre_representante_legal': '',
            'apellidos_representante_legal': '',
            'identificacion_rep_legal': 0,
            'ciudad_expedicion_id_rep_legal': '',
            'ciudad_empresa': '',
        	'nit_empresa': 0,
            'tipo_usuario': ''
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
