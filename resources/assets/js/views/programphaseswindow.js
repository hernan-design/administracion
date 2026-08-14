/**
* Class ProgramPhasesWindow extends of Backbone View
* @author TuProyecto || Desarrollador : @dortegon
* @link http://TuProyecto.com
*/

//Global App Backbone
app || (app = {});

(function ($, window, document, undefined) {

    app.ProgramPhasesWindow = Backbone.View.extend({

        el: '#modal-form',
        templateAttachment: _.template( ($('#form-modal-phase-attachment-tpl').html() || '') ),
        templateAddPhase: _.template( ($('#form-modal-phase-tpl').html() || '') ),
        events: {
            'submit #form-modal-all': 'onSubmit',
            'click .cancel-action': 'onModalDestroy',
            'ifChanged #edicion_formulario_registro': 'onFormEdition'
		},
        parameters: {
            template: 'templateAttachment',
            dataFilter: { },
            data: { },
        },

        /**
        * Constructor Method
        */
        initialize : function(opts) {
            _.bindAll(this, 'render');

            // extends attributtes
            if( opts !== undefined && _.isObject(opts.parameters) )
                this.parameters = _.extend({}, this.parameters, opts.parameters );

            // Init attributes
            this.$formModal = this.$('#form-modal-all');

            this.modelPhase = new app.PhaseModel;

            this.listenTo( this.model, 'sync', this.responseServer);
            this.listenTo( this.model, 'request', this.loadSpinner );

            // Init Attributes
            this.configAlert = {
                'wrapper': '#main-wrap',
                'closeTime': 6000,
                'speedOpen': 500,
                'speedClose': 500
            };
        },

        /*
        * Render View Element
        */
        render: function() {
            var _this = this;

            var attributes = {};

            // extend attributes confirm window
            if (this.model instanceof Backbone.Model) _.extend(attributes, this.model.toJSON());

            if (this.parameters.data.action === false) {

                var data_resource = this.parameters.data_resource[0] != undefined ? this.parameters.data_resource[0] : this.parameters.data_resource;
                attributes.codigo_fase = data_resource.codigo_fase;
                attributes.codigo_adjunto = data_resource.codigo_adjunto;
                attributes.direccion_adjunto = data_resource.direccion_adjunto;
                attributes.codigo_tipo_adjunto = data_resource.codigo_tipo_adjunto;
                attributes.nombre_adjunto = data_resource.nombre_adjunto;
                attributes.codigo_recurso_fase = data_resource.codigo_recurso_fase;
                attributes.tipo_recurso_phase = data_resource.codigo_tipo_recurso_fase;
                attributes.nombre_recurso_fase = data_resource.nombre_recurso_fase;
                attributes.nombre_fase = data_resource.nombre_fase;
                attributes.descripcion_adjunto_fase = data_resource.descripcion_adjunto_fase;
                attributes.fecha_inicio_fase = data_resource.fecha_inicio_fase;
                attributes.fecha_fin_fase = data_resource.fecha_fin_fase;
                attributes.codigo_privacidad_contenido = data_resource.codigo_privacidad_contenido;
                attributes.codigo_evaluacion_emprendimiento = data_resource.codigo_evaluacion_emprendimiento;
                attributes.rango_superior_evaluacion = data_resource.rango_superior_evaluacion;
                attributes.rango_inferior_evaluacion = data_resource.rango_inferior_evaluacion;
                attributes.numero_maximo_evaluadores = data_resource.numero_maximo_evaluadores;
                attributes.asignacion_especialidad = data_resource.asignacion_especialidad;
                attributes.fase_entidad_editable = data_resource.fase_entidad_editable;
                attributes.edicion_formulario_registro = data_resource.edicion_formulario_registro;
                attributes.resumen_fase = data_resource.resumen_fase;
                attributes.descripcion_fase = data_resource.descripcion_fase;
                attributes.fecha_edicion_formulario_registro = data_resource.fecha_edicion_formulario_registro;
            }

            // Change modal text and content
            this.$el.find('.modal-title').html('Agregar fase');

            this.$el.find('.content-modal').html( this[this.parameters.template]( attributes ) );

            this.$el.modal("show").removeAttr('tabindex');
            this.ready();

            if (this.parameters.template == 'templateAddPhase' && this.parameters.data.action == true) {

                // render editor
                ReactDOM.render(React.createElement(ProjectEditor, {
                    content: this.model,
                    formCreate: this.$formModal,
                    value: this.model.get('descripcion_fase'),
                    nameField: "descripcion_fase",
                    column: "col-md-12",
                    previewHide: true,
                    required: true
                }, null), document.getElementById('description-fase-editor'));

                if( !_.isUndefined(this.model.id) ){
                    this.$el.find('input[name=descripcion_fase]').val( this.model.get('descripcion_fase') );

                    if(this.model.get('edicion_formulario_registro')){
                        this.optionFormEditionDate( this.model.get('fecha_edicion_formulario_registro') );
                    }
                }
            } else if (this.parameters.template == 'templateAddPhase' && this.parameters.data.action == false) {
                // render editor
                ReactDOM.render(React.createElement(ProjectEditor, {
                    content: this.model,
                    formCreate: this.$formModal,
                    value: attributes.descripcion_fase,
                    nameField: "descripcion_fase",
                    column: "col-md-12",
                    previewHide: true,
                    required: true
                }, null), document.getElementById('description-fase-editor'));

                if( !_.isUndefined(attributes.codigo_fase) ){
                    this.$el.find('input[name=descripcion_fase]').val( 'Pureba' );

                    if(attributes.edicion_formulario_registro){
                        this.optionFormEditionDate( attributes.fecha_edicion_formulario_registro );
                    }
                }
            }

            return this;
        },

        /**
        * fires libraries js
        */
        ready: function () {
            // reload plugins
            if(typeof window.initComponent.select2Trigger == 'function' ){
                window.initComponent.select2Trigger();
            }

            if( typeof(window.initComponent.datepickerTrigger) == 'function'){
                window.initComponent.datepickerTrigger();
            }

            if( typeof window.initComponent.icheckTrigger == 'function' ){
                window.initComponent.icheckTrigger();
            }
        },

        /**
        * Display input date edition form create
        */
        onFormEdition: function (e) {

            if($(e.target).is(':checked')){
                this.optionFormEditionDate();
            }else{
                $('#form-edition-date').remove();
            }
        },

        /*
        * Display input date edition form edit
        */
        optionFormEditionDate: function (param) {
            var itemFormEditionDate = _.template( ($('#item-registry-form-edition-date-tpl').html() || '') );
            var wrapFormEditionDate = this.$el.find('#registry-form-edition-date');
            wrapFormEditionDate.html( itemFormEditionDate );
            this.ready();

            if(param){
                $('#form-edition-date').find('#fecha_edicion_formulario_registro').val(param);
            }
        },
        /*
        * Submit data
        */
        onSubmit: function (e) {
            e.preventDefault();

            var _this = this,
                data = window.Misc.formToJson( this.$formModal );
                data['codigo_adjunto'] = this.model.get('codigo_adjunto');
                data['codigo_convocatoria'] = this.model.get('codigo_convocatoria');
                _.extend(data, this.parameters.data);

            this.model.save(data, {wait: true, patch: true,
                success: function (model, resp) {
                    // complete proccess
                    window.Misc.removeSpinner({
                        'wrap': _this.$el,
                        'direct': false
                    });

                    _this.$el.modal().off();
                    _this.$el.modal('hide');

                    // response success or error
                    var type = resp.success ? 'success' : 'alert',
                        text = resp.success ?
                            (resp.message !== undefined ? resp.message : '...')
                            : resp.errors;

                    if( _.isObject( resp.errors ) ){

                        var listError = '<ul>';

                        $.each(resp.errors, function(field, item) {
                             listError += '<li>'+ item[0] +'</li>';
                        });
                        listError += '</ul>';

                        text = listError;
                    }

                    var config = {
                        'text': text,
                        'type': type
                    }
                    confCrop =  $.extend({}, _this.configAlert, config);

                    //complete proccess
                    if( resp.success !== undefined ){
                        if( resp.success ){
                            window.Misc.showAlertBox( confCrop );

                            if (resp.redirect) {
                                window.Misc.redirect( app.AppRouter.navigate( Route.route('convocatoria.edit',{convocatorium: data['codigo_convocatoria']}) ) )
                            }
                        }else{
                            window.Misc.showAlertBox( confCrop );

                        }
                    }
                }
            });
        },

        onModalDestroy: function (e){
            this.$el.modal().off();
        },

        loadSpinner: function () {
            window.Misc.setSpinner( this.$el );
        },

        responseServer: function(model, resp, opts) {
            //complete proccess
            window.Misc.removeSpinner({
                'wrap': this.$el,
                'direct': false
            });
        }

   });

})(jQuery, this, this.document);
