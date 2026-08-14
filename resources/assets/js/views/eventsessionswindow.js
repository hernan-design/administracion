/**
* Class EventSessionsWindow extends of Backbone View
* @author TuProyecto || Desarrollador : @dortegon
* @link http://TuProyecto.com
*/

//Global App Backbone
app || (app = {});

(function ($, window, document, undefined) {

    app.EventSessionsWindow = Backbone.View.extend({

        el: '#modal-form',
        events: {
            'submit #form-modal-all': 'onSubmit',
            'click .cancel-action': 'onModalDestroy',
            'apply.daterangepicker #fecha_inicio_sesion': 'onDate',
		},
        parameters: {
            template: _.template( ($('#form-modal-session-event-tpl').html() || '') ),
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

            // Init Attributes
            this.configAlert = {
                'wrapper': '#main-wrap',
                'closeTime': 6000,
                'speedOpen': 500,
                'speedClose': 500
            };

            this.listenTo( this.model, 'request', this.loadSpinner );
            this.listenTo( this.model, 'sync', this.responseServer);
        },

        /*
        * Render View Element
        */
        render: function() {

            var _this = this;

            var attributes = {};

            var fecha_evento = {};

            // extend attributes confirm window
            if( this.model instanceof Backbone.Model ) _.extend(attributes, this.model.toJSON());

            // Change modal text and content
            this.$el.find('.modal-title').html('Agregar sesión');
            this.$el.find('.content-modal').html( this.parameters.template(attributes) );

            this.$el.modal("show");
            this.ready();

            fecha_evento['fecha_evento'] = this.model.get('fecha_evento');
            fecha_evento['fecha_fin_evento'] = this.model.get('fecha_fin_evento');
            _.extend(fecha_evento, this.parameters.data);

            $('#fecha_inicio_sesion').daterangepicker({
                startDate: fecha_evento['fecha_evento'],
                minDate: fecha_evento['fecha_evento'],
                maxDate: fecha_evento['fecha_fin_evento'],
                singleDatePicker: true,
                locale: {
                    format: 'YYYY-MM-DD'
                }
            });

            $('.dateTimeInitHour').on('dp.change', function(e){
                var hours = e.date._d.getHours();
                var minutes = e.date._d.getMinutes();
                _this.onHour(hours, minutes);
            });

            return this;
        },

        /**
        * fires libraries js
        */
        ready: function () {
            // input mask
            this.$(":input").inputmask();

            // reload plugins
            if( typeof(window.initComponent.datepickerTrigger) == 'function'){
                window.initComponent.datepickerTrigger();
            }

            if( typeof(window.initComponent.datetimepickerTrigger) == 'function'){
                window.initComponent.datetimepickerTrigger();
            }
        },

        loadSpinner: function () {
            window.Misc.setSpinner( this.$el );
        },

        /**
         * response of the server
         */
        responseServer: function ( model, resp, opts ) {

            //complete proccess
            window.Misc.removeSpinner({
                'wrap': this.$el,
                'direct': false
            });
        },

        /*
            Funcion encargada de validacion de Fechas
            @backend01
         */

        onDate: function (e) {
            e.preventDefault();
            _.extend(fecha_evento, this.parameters.data);
            var initDate = $(e.target).val();
            $('#fecha_fin_sesion').daterangepicker({
                startDate: initDate,
                minDate: initDate,
                maxDate: fecha_evento['fecha_fin_evento'],
                singleDatePicker: true,
                locale: {
                    format: 'YYYY-MM-DD'
                }
            });
        },

        onHour: function(hour, minutes) {
            $('.dateTimeEndHour').data('DateTimePicker').minDate(hour + ':' + minutes);
            $('.dateTimeEndHour').data('DateTimePicker').date(hour + ':' + minutes);
        },


        /*
        * Submit data
        */
        onSubmit: function (e) {
            e.preventDefault();
            var _this = this,
                data = window.Misc.formToJson( this.$formModal );
                data['codigo_evento'] = this.model.get('codigo_evento');
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
                        }else{
                            window.Misc.showAlertBox( confCrop );

                        }
                    }
                }
            });
        },

        onModalDestroy: function (e){
            this.$el.modal().off();
        }
   });

})(jQuery, this, this.document);
