/**
* Class PhaseModel of Backbone Model
* @author TuProyecto || Desarrollador : @dortegon
* @link http://TuProyecto.com
*/

//Global App Backbone
app || (app = {});

(function (window, document, undefined) {

     app.PhaseModel = Backbone.Model.extend({

        urlRoot: function () {
            return window.Misc.urlFull( Route.route('convocatoria.fase.index') );
        },
        idAttribute: 'codigo_fase',
        defaults: {
            'nombre_fase': '',
            'codigo_evaluacion_emprendimiento': '',
            'codigo_privacidad_contenido': '',
            'descripcion_fase': '',
            'fase_cancelacion': '',
            'fase_entidad_editable': '',
            'fase_registro': '',
            'fecha_fin_fase': '',
            'fecha_inicio_fase': '',
            'rango_inferior_evaluacion': '',
            'rango_superior_evaluacion': '',
            'resumen_fase': '',
            'url_fase': '',
            'asignacion_especialidad': '',
            'numero_maximo_evaluadores': '',
            'edicion_formulario_registro': '',
            'fecha_edicion_formulario_registro': '',
            'codigo_adjunto': '',
            'codigo_tipo_adjunto': '',
            'direccion_adjunto': '',
            'nombre_adjunto': '',
            'codigo_recurso_fase': '',
            'nombre_recurso_fase': '',
            'descripcion_adjunto_fase': '',
            'codigo_estado': '',
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
