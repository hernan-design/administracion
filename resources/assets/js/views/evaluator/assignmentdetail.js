/**
* Class AssignmentDetailView extends of Backbone View
* @author TuProyecto || Desarrolladora : @xindykatalina
* @link http://TuProyecto.com
*/

//Global App Backbone
app || (app = {});

(function ($, window, document, undefined) {

    app.AssignmentDetailView = Backbone.View.extend({

        el: '#evaluations-content',
        template: _.template( ($('#assigment-detail-tpl').html() || '') ),
        events: {
            'submit #form-assignment-list-jury': 'onAddJury',
            'focusout #email': 'onValidateEmail',
            'keyup #email': 'onKeyUpEmail',
            'keyup #identificacion_evaluador': 'onKeyUpIdentification',
            'focusout #identificacion_evaluador': 'onValidateIdentification',
            'change #tipo_evaluacion': 'onValidateTypeConference'
        },
        parameters: {
            dataFilter: {},
            tab: 'general'
        },

        /**
        * Constructor Method
        */
        initialize : function(opts) {
            _.bindAll(this, 'beforeRender', 'render', 'afterRender');

            // extends parameters
            if( opts.parameters !== undefined && _.isObject(opts.parameters) )
                this.parameters = $.extend({}, this.parameters, opts.parameters);

            //Init Attribute
            this.emailOld = '';
            this.assignmentList = new app.AssignmentList();
            this.configAlert = {
                'wrapper': '#main-wrap',
                'closeTime': 15000,
                'speedOpen': 500,
                'speedClose': 500
            };

            var _this = this;
            this.render = _.wrap(this.render, function(render) {

                _this.trigger('beforeRender');

                render();

                _this.trigger('afterRender');

                return _this;
            });

            // Events Listener
            this.listenTo( this.model, 'change', this.render );
            this.listenTo( this.model, 'request', this.loadSpinner );
            this.listenTo( this.model, 'sync', this.responseServer );

            // Events listener custom
            this.on( 'beforeRender', this.beforeRender );
            this.on( 'afterRender', this.afterRender );
        },

        /*
        * Render View Element
        */
        render: function() {
            var attributes = this.model.toJSON();
            attributes.dataFilter = this.parameters['dataFilter'];
            console.log('Attributes', attributes);
            this.$el.html( this.template(attributes) );

            this.$("#error-email").css('display', 'none');
            this.$("#error-identification").css('display', 'none');
            this.$("#error-type-conference").css('display', 'none');

            // Initialize attributes
            this.$wrapperJury = this.$('#wrapper-assignment-jury');
            this.$formJury = this.$('#form-assignment-list-jury');
            this.$fldEmail = this.$('#email');

            // overwrite model id attribute
            if( this.model.changed.codigo_usuario !== undefined ) {
                // Overwrite id attribute of the model
                this.model.idAttribute = 'codigo_usuario';
                this.model.set({'codigo_usuario': this.model.get('codigo_usuario')},{'silent':true});
            }
            this.referenceViews();
        },

        /**
        * Fires before of run render function
        */
        beforeRender: function () {
            //
        },

        /**
        * Fires before of run render function
        */
        afterRender: function () {

            this.ready();
        },

        /**
        * fires libraries js
        */
        ready: function () {
            var _this = this;

            // reload plugins
            if(typeof window.initComponent.select2Trigger == 'function' )
                window.initComponent.select2Trigger();

            if( typeof(window.initComponent.datepickerTrigger) == 'function')
                window.initComponent.datepickerTrigger();

            if( typeof(window.initComponent.autosizeTrigger) == 'function')
                window.initComponent.autosizeTrigger();

            if( typeof window.initComponent.icheckTrigger == 'function' ){
                window.initComponent.icheckTrigger();
            }

            if(typeof window.initComponent.multiselectTrigger == 'function' ){
                window.initComponent.multiselectTrigger();
            }

            if( typeof(window.initComponent.datetimepickerTrigger) == 'function'){
                window.initComponent.datetimepickerTrigger();
            }

            // input mask
            this.$(":input").inputmask();
        },

        /**
        * reference to views
        */
        referenceViews: function () {
            // instance of jury assignment view
            this.assignmentJuryListView = new app.AssignmentJuryListView({
                el: this.$wrapperJury,
                collection: this.assignmentList,
                parameters: {
                    asignacion_especialidad: this.model.get('asignacion_especialidad'),
                    codigo_registro_fase_usuario: this.model.get('codigo_registro_fase_usuario')
                }
            });

            this.assignmentJuryListView.render();
            this.assignmentList.fetch({ data: {'register':this.model.get('codigo_registro_fase_usuario')}, reset:true });
        },

        /**
        * Load spinner on the request
        */
        loadSpinner: function () {
             window.Misc.setSpinner( this.$el );
        },

        /**
        * response of the server
        */
        responseServer: function ( model, resp, opts ) {

            window.Misc.removeSpinner({
                'wrap': this.$el,
                'direct': true
            });
        },

        /**
        * add jury to the assignment evaluation
        */
        onAddJury: function (e) {
            e.preventDefault();
            var data = window.Misc.formToJson(e.target);


            // evaluate the form using generic validaing
            if( !validator.checkAll($(e.target)) )
                return false;
            
            if (data.email) {

                // validate email exits
                $.when(this.onValidateEmail()).done(function (resp) {

                    if (resp.success) this.assignmentList.trigger('store', data, $(e.target));
                }.bind(this));

            } else if (data.identificacion_evaluador) {

                // validate email exits
                $.when(this.onValidateIdentification()).done(function (resp) {

                    if (resp.success) this.assignmentList.trigger('store', data, $(e.target));
                }.bind(this));

            }
        },

        /**
         * Validate identification
         */

        onValidateIdentification: function(e) {
            var _this = this,
                $elField = e !== undefined ? $(e.currentTarget) : this.$('#identificacion_evaluador'),
                identification = $elField.val(),
                sendData = {
                    'identification': identification
                };

            return this.validateIdentification(sendData, function (respState, respAlert, resp) {

                window.Misc.removeSpinner({
                    'wrap': _this.$formJury,
                    'direct': true
                });

                if( !respState && identification !== "" ){
                    validator.mark($elField, 'La identificación ingresada no existe');
                }else{
                    validator.unmark($elField);
                }

                // this.emailOld = email;
            });

        },

        validateIdentification: function(sendData, callback) {
            var _this = this,
                respReturn = true,
                configAlert = this.configAlert,
                _data = {
                    'identification': ''
                };

            if( _.isObject(sendData) )
                _.extend(_data, sendData);

            window.Misc.setSpinner(_this.$formJury);
            return $.get(document.url + Route.route('usuario.valid.identification'), _data, function (resp) {
                if( resp.success !== undefined ) {

                    var type = resp.success ? 'success' : 'alert',
                        text = resp.success ?
                            (resp.message !== undefined ? resp.message : '')
                            : resp.errors;

                    if( _.isObject(resp.errors) ){

                        var listError = '<ul>';

                        $.each(resp.errors, function(field, item) {
                            listError += '<li>'+ item[0] +'</li>';
                        });
                        listError += '</ul>';

                        text = listError;
                    }

                    var conf = {
                        'text': text,
                        'type': type
                    };

                    _.extend(configAlert, conf);

                    if (!resp.success) {

                        respReturn = false;
                    }
                }

                if( typeof callback == 'function' ){
                    callback.call(null, respReturn, configAlert, resp);
                }
            });

        },

        onKeyUpEmail: function(e) {

          this.$(e.currentTarget).prop("required", true);
          this.$('#identificacion_evaluador').removeAttr("required");
          this.$('#identificacion_evaluador').removeClass('parsley-error');
          this.$('.parsley-errors-list').remove();

          validator.unmark(this.$('#identificacion_evaluador'));
        },

        onKeyUpIdentification: function(e) {

            this.$(e.currentTarget).prop("required", true);
            this.$('#email').removeAttr("required");
            this.$('#email').removeClass('parsley-error');
            this.$('.parsley-errors-list').remove();

            validator.unmark(this.$('#email'));
        },

        /**
        * validate email
        */
        onValidateEmail: function (e) {

            var _this = this,
                $elField = e !== undefined ? $(e.currentTarget) : this.$('#email'),
                email = $elField.val(),
                sendData = {
                    'email': email
                };

            return this.validateEmail(sendData, function (respState, respAlert, resp) {

                window.Misc.removeSpinner({
                    'wrap': _this.$formJury,
                    'direct': true
                });

                if( !respState && email !== "" ){
                    validator.mark($elField, 'El email ingresado no existe');
                }else if (respState) {
                    validator.unmark($elField);
                }

                this.emailOld = email;
            });
        },

        /**
        * validate E-mail of the team project
        * @param String email
        * @return Array | [Boolean, Object]
        */
        validateEmail: function (sendData, callback) {

            var _this = this,
                respReturn = true,
                configAlert = this.configAlert,
                _data = {
                    'email': ''
                };

            if( _.isObject(sendData) )
                _.extend(_data, sendData);

            if( window.Misc.isEmail(_data['email']) ) {

                window.Misc.setSpinner(_this.$formJury);
                return $.get(document.url + Route.route('usuario.valid.email'), _data, function (resp) {

                    if( resp.success !== undefined ) {

                        var type = resp.success ? 'success' : 'alert',
                            text = resp.success ?
                                   (resp.message !== undefined ? resp.message : '')
                                   : resp.errors;

                        if( _.isObject(resp.errors) ){

                            var listError = '<ul>';

                            $.each(resp.errors, function(field, item) {
                                 listError += '<li>'+ item[0] +'</li>';
                            });
                            listError += '</ul>';

                            text = listError;
                        }

                        var conf = {
                            'text': text,
                            'type': type
                        };

                        _.extend(configAlert, conf);

                        if (!resp.success) {

                            respReturn = false;
                        }
                    }

                    if( typeof callback == 'function' ){
                        callback.call(null, respReturn, configAlert, resp);
                    }
                });
            }
        },

        onValidateTypeConference: function(e) {
            e.preventDefault();

            var value = $(e.currentTarget).val();
            console.log(value);
            if (value === '0') {
                $('#lugar_evaluacion_jurado').prop('disabled', false);
            } else {
                $('#lugar_evaluacion_jurado').prop('disabled', true);
            }
        },

        /*
        * clean fields View Element
        */
        cleanfields: function(){

            this.$('#email').val('');
            this.$('#codigo_usuario').val('');
        }
   });

})(window.jQuery, window, document);
