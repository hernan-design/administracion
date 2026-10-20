/**
* Class ProgramCommitmentView extends of Backbone View
* @author TuProyecto || Desarrolladora : @xindykatalina
* @link http://TuProyecto.com
*/

//Global App Backbone
app || (app = {});

(function ($, window, document, undefined) {

    app.ProgramCommitmentView = Backbone.View.extend({

        el: '#programs-content',
        template: _.template( ($('#program-commitment-tpl').html() || '') ),
        templateConfirm: _.template( ($('#commitment-confirm-tpl').html() || '') ),
        templateCommitmentChangeConfirm: _.template( ($('#program-comitment-change-confirm-tpl').html() || '') ),
        events: {
            'submit #form-program-commitment': 'onStore',
            'click .closed-commitment': 'onConfirm',
            'change #codigo_clase_empre_insti': 'onProgramClass'
        },
        parameters: {
            dataFilter: {}
        },

        /**
        * Constructor Method
        */
        initialize : function(opts) {
            _.bindAll(this, 'beforeRender', 'render', 'afterRender');

            // extends parameters
            if( opts.parameters !== undefined && _.isObject(opts.parameters) )
                this.parameters = $.extend({}, this.parameters, opts.parameters);

            var _this = this;
            this.render = _.wrap(this.render, function(render) {

                _this.trigger('beforeRender');

                render();

                _this.trigger('afterRender');

                return _this;
            });

            // Events Listener
            this.listenTo( this.model, 'change', this.render );
            this.listenTo(this.model, 'request', this.loadSpinner);
            this.listenTo( this.model, 'sync', this.responseServer );

            // Events Listener custome
            this.on( 'beforeRender', this.beforeRender );
            this.on( 'afterRender', this.afterRender );

        },

        /*
        * Render View Element
        */
        render: function() {

            var attributes = this.model.toJSON();
            attributes.dataFilter = this.parameters['dataFilter'];

            console.log(attributes);

            this.$el.html( this.template(attributes) );

            console.log(attributes);

            // Initialize attributes
         	this.$formCommitment = this.$('#form-program-commitment');

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
        * Load spinner on the request
        */
        loadSpinner: function ( target, xhr, opts ) {

            if( target instanceof Backbone.Model )
                window.Misc.setSpinner( this.$formCommitment );
        },

        /**
        * fires libraries js
        */
        ready: function () {

            var _this = this;

            // reload plugins
            if( typeof window.initComponent.select2Trigger == 'function' && _.has($.fn, 'select2') )
                window.initComponent.select2Trigger();

            if( typeof(window.initComponent.datepickerTrigger) == 'function')
                window.initComponent.datepickerTrigger();

            if( typeof(window.initComponent.autosizeTrigger) == 'function')
                window.initComponent.autosizeTrigger();

            if( typeof(window.initComponent.icheckTrigger) == 'function')
                window.initComponent.icheckTrigger();
        },

        /**
        * reference to views
        */
        referenceViews: function () {

        },

        /**
        * response of the server
        */
        responseServer: function ( model, resp, opts ) {

            window.Misc.removeSpinner({
                'wrap': this.$formCommitment,
                'direct': true
            });

            this.$formCommitment.find(':submit').removeClass('disabled');
        },

        /**
        * Store
        */
        onStore: function (e) {
            e.preventDefault();
            this.$formCommitment.find(':submit').addClass('disabled');
            var data = window.Misc.formToJson( e.target );
            data.codigo_registro_fase_usuario = this.model.get('codigo_registro_fase_usuario');
            this.model.save( data, {patch: true, wait: true,
                success: function (model, resp) {

                    // response success or error
                    var type = resp.success ? 'success' : 'alert',
                        txt = resp.success ?
                            (resp.message !== undefined ? resp.message : '...')
                            : resp.errors;

                    if( _.isObject( resp.errors ) ){

                        var listError = '<ul>';

                        $.each(resp.errors, function(field, item) {
                             listError += '<li>'+ item[0] +'</li>';
                        });
                        listError += '</ul>';

                        txt = listError;
                    }
                    //complete proccess
                    if( resp.success !== undefined ){
                        if( resp.success ){
                            new PNotify({
                                title: 'Estado...',
                                text:  txt,
                                type: 'success',
                                addclass: "stack-custom2"

                            });
                        }else{
                           new PNotify({
                                title: 'Error!',
                                text: txt
                            });
                        }
                    }
                }
            });
        },

        /**
        * Notification Close Confirm
        */
        onConfirm: function (e) {
			e.preventDefault();

            var  data = window.Misc.formToJson(this.$formCommitment);
            data.codigo_registro_fase_usuario = this.model.get('codigo_registro_fase_usuario');
            data.action = 'close';

            _this = this;

            if( this.acceptConfirm instanceof Backbone.View ) {
                this.acceptConfirm.stopListening();
                this.acceptConfirm.undelegateEvents();
            }

            this.acceptConfirm = new app.ConfirmWindow({
                model: this.model,
                parameters: {
                    template: this.templateConfirm(this.model.toJSON()) ,
                    titleConfirm: "Confirmación",
                    onConfirm: function () {
                        _this.model.save( data, {patch: true, wait:true,
                            success: function (model, resp) {

                                // complete proccess
                                window.Misc.removeSpinner({
                                    'wrap': _this.$el,
                                    'direct': false
                                });

                                // response success or error
                                var type = resp.success ? 'success' : 'alert',
                                    txt = resp.success ?
                                        (resp.message !== undefined ? resp.message : '...')
                                        : resp.errors;

                                if( _.isObject( resp.errors ) ){

                                    var listError = '<ul>';

                                    $.each(resp.errors, function(field, item) {
                                         listError += '<li>'+ item[0] +'</li>';
                                    });
                                    listError += '</ul>';

                                    txt = listError;
                                }
                                //complete proccess
                                if( resp.success !== undefined ){
                                    if( resp.success ){
                                        new PNotify({
                                            title: 'Estado...',
                                            text:  txt,
                                            type: 'success',
                                            addclass: "stack-custom2"

                                        });
                                    }else{
                                       new PNotify({
                                            title: 'Error!',
                                            text: txt
                                        });
                                    }
                                }
                            }
                        });
                    }
                }
            });

            this.acceptConfirm.render();
        },

        /*
        *   Confirm change program class
        */
        onProgramClass: function (e) {
            e.preventDefault();

            var  data = {},
                _this = this,
                text = '¿Realmente desea clasificar a empresa?';

            data.codigo_clase_empre_insti = $(e.target).val();

            if(this.$('#codigo_clase_empre_insti').val() == window._tp.getProgramClass['project']){
                text = '¿Realmente desea clasificar a proyecto?';
            }

            if( this.acceptConfirm instanceof Backbone.View ) {
                this.acceptConfirm.stopListening();
                this.acceptConfirm.undelegateEvents();
            }

            this.acceptConfirm = new app.ConfirmWindow({
                model: this.model,
                parameters: {
                    template: this.templateCommitmentChangeConfirm({text: text}),
                    titleConfirm: "Confirmación",
                    onConfirm: function () {
                        window.Misc.setSpinner( _this.$formCommitment );
                        $.ajax({
                            type: 'PUT',
                            url: window.Misc.urlFull(Route.route('entidad.update', {entidad: _this.model.get('codigo_entidad')})),
                            data: data
                        }).done(function(resp) {

                            //complete proccess
                            if( resp.success !== undefined ) {
                                if( resp.success ){
                                    // complete proccess
                                    window.Misc.removeSpinner({
                                        'wrap': _this.$el,
                                        'direct': false
                                    });

                                    location.reload();
                                }else {
                                    window.Misc.showAlertBox({
                                        text: resp.errors !== undefined ? resp.errors : '...',
                                        type: 'success'
                                    });
                                    return false;
                                }
                            }
                        });
                    }
                }
            });
            this.acceptConfirm.render();
        }
   });

})(window.jQuery, window, document);
