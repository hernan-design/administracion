/**
* Class MarketplaceAssignmentDetailView extends of Backbone View
* @author TuProyecto || Desarrollador : @krobing
* @link http://TuProyecto.com
*/

//Global App Backbone
app || (app = {});

(function ($, window, document, undefined) {

    app.MarketplaceAssignmentDetailView = Backbone.View.extend({
        el: '#marketplace-assignment-content',
        events: {
            'submit #form-assignment-marketplace-member': 'onAddAssignment',
            'focusout #email': 'onValidateEmail'
        },
        parameters: {
            codigo_registro_fase: null
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
            this.assignmentList = new app.MarketplaceAssignmentList();
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

            // Events listener custom
            this.on( 'beforeRender', this.beforeRender );
            this.on( 'afterRender', this.afterRender );
        },

        /*
        * Render View Element
        */
        render: function() {

            // Initialize attributes
            this.$wrapperAssignments = this.$('#wrapper-assignment-members');
            this.$formAssignment = this.$('#form-assignment-marketplace-member');
            this.$fldEmail = this.$('#email');

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

            // input mask
            this.$(":input").inputmask();
        },

        /**
        * reference to views
        */
        referenceViews: function () {
            // instance of member assignment view
            this.assignmentListView = new app.MarketplaceAssignmentListView({
                el: this.$wrapperAssignments,
                collection: this.assignmentList,
                parameters: {
                    codigo_registro_fase: this.parameters['codigo_registro_fase']
                }
            });
            this.assignmentListView.render();
            this.assignmentList.fetch({ data: {'codigo_registro_fase': this.parameters['codigo_registro_fase']}, reset:true });
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
        * add member to the assignment marketplace
        */
        onAddAssignment: function (e) {
            e.preventDefault();

            // evaluate the form using generic validaing
            if( !validator.checkAll($(e.target)) )
                return false;

            // validate email exits
            $.when( this.onValidateEmail() ).done(function (resp) {

                var data = window.Misc.formToJson(e.target);
                data['codigo_usuario'] = resp.codigo_usuario;

                if(resp.success) this.assignmentList.trigger('store', data, $(e.target));
            }.bind(this));
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
                    'wrap': _this.$formAssignment,
                    'direct': true
                });

                if( !respState ){
                    validator.mark($elField, 'El email ingresado no se encuentra registrado');
                }else{
                    validator.unmark($elField);
                }

                this.emailOld = email;
            });
        },

        /**
        * validate E-mail of the team project
        * @param sendData Object, email
        * @param callback Function
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

                window.Misc.setSpinner(_this.$formAssignment);
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

        /*
        * clean fields View Element
        */
        cleanfields: function(){

            this.$('#email').val('');
            this.$('#codigo_usuario').val('');
        }
   });

})(window.jQuery, window, document);
