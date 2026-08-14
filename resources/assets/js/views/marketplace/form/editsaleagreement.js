/**
* Class EditSaleAgreementView extends of Backbone View
* @author TuProyecto || Desarrollador : @danielortegon
* @link http://TuProyecto.com
*/

//Global App Backbone
app || (app = {});

(function ($, window, document, undefined) {

	app.EditSaleAgreementView = Backbone.View.extend({

		el: '#wrapper-render-agreement',
		template: _.template( ($('#marketplace-agreement-edit-tpl').html() || '') ),
		templateActionsBtn: _.template( ($('#btn-actions-agreement-purchase-tpl').html() || '') ),
		events: {
			'click #add-agreement-activity': 'onAddActivity',
            'click #add-agreement-deliverable': 'onAddDeliverable',
			'click #add-agreement-adviser': 'onAddAdviser',
			'click #add-agreement-businessman': 'onAddMember',
			'click #enviar-revision': 'onSendReviewAgreement',
			'click #confirmar': 'onConfirmAgreement',
            'change #descripcionagreement': 'onChangeField',
            'change #resultadosagreement': 'onChangeField'
        },
        parameters: {
           sale: null,
           tab: ''
        },

        /**
        * Constructor Method
        */
        initialize : function(opts) {
        	_.bindAll(this, 'beforeRender', 'render', 'afterRender');

            // extend parameters
            if(!_.isUndefined(opts) && _.isObject(opts.parameters)){
                this.parameters = $.extend({}, this.parameters, opts.parameters);
            }

            //Init attributes
            this.activitiesList = new ( Backbone.Collection.extend({
                url : window.Misc.urlFull( Route.route('marketplace.transacciones.acuerdo.actividad.index')),
            	model: Backbone.Model.extend({ idAttribute: 'codigo_actividad_acuerdo', urlRoot: window.Misc.urlFull( Route.route('marketplace.transacciones.acuerdo.actividad.index')) })
            }) );

            this.deliverablesList = new ( Backbone.Collection.extend({
                url : window.Misc.urlFull( Route.route('marketplace.transacciones.acuerdo.entregable.index')),
            	model: Backbone.Model.extend({ idAttribute: 'codigo_entregable_acuerdo', urlRoot: window.Misc.urlFull( Route.route('marketplace.transacciones.acuerdo.entregable.index'))})
            }) );

            this.advisorsList = new ( Backbone.Collection.extend({
                url : window.Misc.urlFull( Route.route('marketplace.transacciones.acuerdo.usuario.index')),
                model: Backbone.Model.extend({ idAttribute: 'codigo_usuario_acuerdo', urlRoot: window.Misc.urlFull( Route.route('marketplace.transacciones.acuerdo.usuario.index'))})
            }) );

            this.businessmansList = new ( Backbone.Collection.extend({
                url : window.Misc.urlFull( Route.route('marketplace.transacciones.acuerdo.businessman.index')),
                model: Backbone.Model.extend({ idAttribute: 'codigo_usuario_empresario', urlRoot: window.Misc.urlFull( Route.route('marketplace.transacciones.acuerdo.empresario.index'))})
            }) );

            this.configAlert = {
                'wrapper': '#main-wrap',
                'closeTime': 20000,
                'speedOpen': 500,
                'speedClose': 500
            };

            //Init Attribute
            this.discussionList = new app.DiscussionSaleAgreementList();

            var _this = this;
			this.render = _.wrap(this.render, function(render) {

				_this.trigger('beforeRender');

				render();

				_this.trigger('afterRender');

				return _this;
			});

            // Events Listener
            this.listenTo( this.model, 'change:codigo_acuerdo_compra', this.render );
            this.listenTo( this.model, 'request', this.loadSpinner );
            this.listenTo( this.model, 'sync', this.responseServer );
            this.listenTo( this.activitiesList, 'add', this.addOneActivity );
            this.listenTo( this.deliverablesList, 'add', this.addOneDeliverable );
            this.listenTo( this.advisorsList, 'add', this.addOneAdviser );
            this.listenTo( this.businessmansList, 'add', this.addOneMember );

            // Events customize
            this.on( 'beforeRender', this.beforeRender );
			this.on( 'afterRender', this.afterRender );
        },

        /**
		* view render
        */
        render: function () {
            // render
        	var attributes = this.model.toJSON();
            this.$el.html( this.template(attributes) );

        	// Reference to elements
        	this.$formCreate = this.$('#form-edit-sale-agreement');
        	this.$wrapperActivities = this.$('#sale-agreement-activities');
        	this.$wrapperDeliverables = this.$('#sale-agreement-deliverables');
            this.$wrapperAdvisors = this.$('#sale-agreement-advisors');
            this.$wrapperBusinessman = this.$('#sale-agreement-businessman');
            this.$actionsBtnWrap = this.$('#btn-actions-agreement-purchase');

            // render actions buttons
            this.renderActionsBtn();

        	// fill carts of sale agreement
        	if( this.model.hasChanged('actividad_acuerdo') || this.model.hasChanged('asesor_acuerdo') || this.model.hasChanged('entregable_acuerdo') || this.model.hasChanged('empresario_acuerdo') ){
                this.fillCarts();
            }

        	return this;
        },

        /**
        * render actions buttons
        */
        renderActionsBtn: function() {
            var attributes = this.model.toJSON();
        	attributes['tab'] = this.parameters.tab;

            this.$actionsBtnWrap.html( this.templateActionsBtn(attributes) );
        },

        /**
		* fill sale agreement carts
        */
        fillCarts: function () {
        	this.activitiesList.reset({silent:true});
        	this.deliverablesList.reset({silent:true});
            this.advisorsList.reset({silent:true});
            this.businessmansList.reset({silent:true});

        	this.activitiesList.set( this.model.get('actividad_acuerdo') );
        	this.deliverablesList.set( this.model.get('entregable_acuerdo') );
            this.advisorsList.set( this.model.get('asesor_acuerdo') );
            this.businessmansList.set( this.model.get('empresario_acuerdo') );
        },

        /**
		* Fires before of run render function
		*/
		beforeRender: function() {
			//
		},

		/**
		* Fires after of run render function
		*/
		afterRender: function() {
			this.ready();
		},

		/**
		* fires libraries js
		*/
		ready: function () {

			var _this = this;

			// reload plugins
			if( typeof window.initComponent.datepickerTrigger == 'function' && _.has($.fn, 'daterangepicker') )
				window.initComponent.datepickerTrigger();

            this.$(':input[data-inputmask]').inputmask();
		},

		/**
        * Load spinner on the request
        */
        loadSpinner: function () {
            window.Misc.setSpinner( this.$formCreate );
        },

        /**
        * response of the server
        */
        responseServer: function ( model, resp ) {
            // response success or error
            var type = resp.success ? 'success' : 'alert',
                text = resp.success ?
							(resp.message !== undefined ? resp.message : '')
							: resp.errors;

            if( _.isObject( resp.errors ) ) {

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
            };
            _.extend( this.configAlert, config );

            if(resp.success !== undefined) {
                if( resp.success ) {
                    if(resp.message) {
                        window.Misc.showAlertBox( this.configAlert );
                    }

                    this.fillCarts();
                }else {
                    window.Misc.showAlertBox( this.configAlert );
                }
            }

            //complete proccess
            window.Misc.removeSpinner({
                'wrap': this.$formCreate,
                'direct': false
            });
        },

		/**
		* add one activity
		* @param Object activity model
		*/
		addOneActivity: function (activity) {
            var parameters = _.extend({ parentview: this}, this.parameters);
			var view = new app.SaleAgreementCarView({
				model: activity,
				parameters: parameters
			});

			this.$wrapperActivities.append( view.render().el );
		},

		/**
		* add new development activity
		*/
		onAddActivity: function (e) {
			e.preventDefault();

			var data = window.Misc.formToJson(this.$formCreate),
				newData = _.pick(data, 'desc_actividad_acuerdo', 'sesiones_presencial', 'sesiones_virtual', 'resp_actividad_acuerdo', 'fecha_inicial_actividad_acuerdo', 'fecha_final_actividad_acuerdo', 'sesiones_presencial_horas', 'sesiones_virtual_horas');

			// add responsible
			newData[data['resp_actividad_acuerdo']] = true;
			newData['codigo_acuerdo_compra'] = this.model.get('codigo_acuerdo_compra');

            if(newData.sesiones_presencial == 0 && newData.sesiones_virtual == 0){
                var config = {
                    'text': 'Debes seleccionar al menos una sesión virtual o una sesión presencial',
                    'type': 'alert'
                };
                _.extend( this.configAlert, config );
                window.Misc.showAlertBox( config );
                return ;
            }else {

                if(newData.sesiones_presencial > 0 && newData.sesiones_presencial_horas == 0 ){
                    var config = {
                        'text': 'Debes seleccionar al menos una hora para la sesión presencial',
                        'type': 'alert'
                    };
                    _.extend( this.configAlert, config );
                    window.Misc.showAlertBox( config );
                    return ;
                }

                if(newData.sesiones_virtual > 0 && newData.sesiones_virtual_horas == 0 ){
                    var config = {
                        'text': 'Debes seleccionar al menos una hora para la sesión virtual',
                        'type': 'alert'
                    };
                    _.extend( this.configAlert, config );
                    window.Misc.showAlertBox( config );
                    return ;
                }
            }

			// validate fieldset inputs
			if( this.validateFields($('#fields_agreement_activities').data('parsley-group')) ) {

                this.activitiesList.create(newData, {wait:true, success: function(model, resp, opts){
                    var type = resp.success ? 'success' : 'alert',
                    text = resp.success ?
                            (resp.message !== undefined ? resp.message : '')
                            : resp.errors;

                    if( _.isObject( resp.errors ) ) {
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
                    };
                    _.extend( this.configAlert, config );

                    if(resp.success) {
                        // reset fieldset inputs
                        this.resetFields( this.$formCreate.find('[name="fields_agreement_activities"]') );

                        // Render actions buttons
                        this.model.set('codigo_estado', resp.codigo_estado_acuerdo, {silent: true});
                        this.renderActionsBtn();
                    }

                    window.Misc.showAlertBox( this.configAlert );
                }.bind(this)});
            }
		},

		/**
		* add one deliverable
		* @param Object deliverable model
		*/
		addOneDeliverable: function (deliverable) {

            var parameters = _.extend({ template: 'templateDeliverable' }, this.parameters);
            parameters.parentview = this;

			var view = new app.SaleAgreementCarView({
				model: deliverable,
				parameters: parameters
			});

			this.$wrapperDeliverables.append( view.render().el );
        },

		/**
		* add new development deliverable
		*/
		onAddDeliverable: function (e) {
            e.preventDefault();

			var data = _.pick(window.Misc.formToJson(this.$formCreate), 'descripcion_entregable');
            data.codigo_acuerdo_compra = this.model.get('codigo_acuerdo_compra');

			// validate fieldset inputs
			if( this.validateFields($('#fields_agreement_deliverables').data('parsley-group')) ) {
                this.deliverablesList.create(data, {wait:true, success: function(model, resp, opts){
                    var type = resp.success ? 'success' : 'alert',
                    text = resp.success ?
                            (resp.message !== undefined ? resp.message : '')
                            : resp.errors;

                    if( _.isObject( resp.errors ) ) {
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
                    };
                    _.extend( this.configAlert, config );

                    if(resp.success) {
                        // reset fieldset inputs
                        this.resetFields( this.$formCreate.find('[name="fields_agreement_deliverables"]') );

                        // Render actions buttons
                        this.model.set('codigo_estado', resp.codigo_estado_acuerdo, {silent: true});
                        this.renderActionsBtn();
                    }

                    window.Misc.showAlertBox( this.configAlert );
                }.bind(this)});
            }
		},

        /**
        * add one adviser
        * @param Object advider model
        */
        addOneAdviser: function (adviser) {
            var parameters = _.extend({ template: 'templateAdviser' }, this.parameters);
            parameters.parentview = this;
            parameters.codigo_usuario_entidad = this.model.get('codigo_usuario_entidad');

            var view = new app.SaleAgreementCarView({
                model: adviser,
                parameters: parameters
            });

            this.$wrapperAdvisors.append( view.render().el );
        },

        /**
        * add new development adviser
        */
        onAddAdviser: function (e) {
            e.preventDefault();

            var data = window.Misc.formToJson(this.$formCreate);
            data.codigo_acuerdo_compra = this.model.get('codigo_acuerdo_compra');

            // validate fieldset inputs
            if( this.validateFields($('#fields_agreement_advisors').data('parsley-group')) ) {
                this.advisorsList.create(data, {wait:true, success: function(model, resp, opts){
                    var type = resp.success ? 'success' : 'alert',
                    text = resp.success ?
                            (resp.message !== undefined ? resp.message : '')
                            : resp.errors;

                    if( _.isObject( resp.errors ) ) {
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
                    };
                    _.extend( this.configAlert, config );

                    if(resp.success) {
                        // Render actions buttons
                        this.model.set('codigo_estado', resp.codigo_estado_acuerdo, {silent: true});
                        this.renderActionsBtn();
                    }

                    window.Misc.showAlertBox( this.configAlert );
                }.bind(this)});
            }
        },

        /**
        * add one businessman
        * @param Object businessman model
        */
        addOneMember: function (adviser) {
            var parameters = _.extend({ template: 'templateMember' }, this.parameters);
            parameters.parentview = this;

            var view = new app.SaleAgreementCarView({
                model: adviser,
                parameters: parameters
            });

            this.$wrapperBusinessman.append( view.render().el );
        },

        /**
        * add new development businessman
        */
        onAddMember: function (e) {
            e.preventDefault();

            var data = _.pick(window.Misc.formToJson(this.$formCreate), 'nombre_usuario_empresario', 'cargo_usuario_empresario', 'correo_usuario_empresario');
            data.codigo_acuerdo_compra = this.model.get('codigo_acuerdo_compra');

            // validate fieldset inputs
            if( this.validateFields($('#fields_agreement_businessman').data('parsley-group')) ) {
                this.businessmansList.create(data, {wait:true, success: function(model, resp, opts){
                    var type = resp.success ? 'success' : 'alert',
                    text = resp.success ?
                            (resp.message !== undefined ? resp.message : '')
                            : resp.errors;

                    if( _.isObject( resp.errors ) ) {
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
                    };
                    _.extend( this.configAlert, config );

                    if(resp.success) {
                        // reset fieldset inputs
                        this.resetFields( this.$formCreate.find('[name="fields_agreement_businessman"]') );

                        // Render actions buttons
                        this.model.set('codigo_estado', resp.codigo_estado_acuerdo, {silent: true});
                        this.renderActionsBtn();
                    }

                    window.Misc.showAlertBox( this.configAlert );
                }.bind(this)});
            }
        },

		/**
		* reset form fields
		* @param parentForm Element
		*/
		resetFields: function (parentForm) {
			// reset inputs
            $(':input', parentForm).not(':button, :submit, :reset, :hidden, :checkbox, :radio').val('');

			this.ready();
		},

		/**
		* @param fieldSet String|Element
		* @param e Event|Object with property type
		*/
		validateFields: function (groupValid) {
            this.$formCreate.parsley().validate({group: groupValid, force: true });
			return this.$formCreate.parsley().isValid({group: groupValid, force: true });
		},

        /**
        * confirm agreement
        */
        onConfirmAgreement: function (e) {
            e.preventDefault();
            var _this = this;

            this.acceptConfirm = new app.ConfirmWindow({
                model: this.model,
                parameters: {
                    template: _.template($("#confirm-agreement-confirm-tpl").html() || ""),
                    titleConfirm: "Confirmar acuerdo de negociación",
                    onConfirm: function () {
                        var data = window.Misc.formToJson(_this.$formCreate);
                        data.action = 'confirmed';

                        _this.model.save(data, {patch: true, wait:true});
                    }
                }
            });

            this.acceptConfirm.render();
        },

		/**
		* send review agreement
		*/
		onSendReviewAgreement: function (e) {
			e.preventDefault();
            var _this = this;

            this.acceptConfirm = new app.ConfirmWindow({
                model: this.model,
                parameters: {
                    template: _.template($("#send-review-agreement-confirm-tpl").html() || ""),
                    titleConfirm: "Enviar acuerdo de negociación a revisión",
                    onConfirm: function () {

                        var data = window.Misc.formToJson(_this.$formCreate),
            				storeData = _.pick(data, '_token');

                        storeData.action = 'return-to-review';
                        _this.model.save(storeData, {patch: true, wait:true });
                    }
                }
            });

            this.acceptConfirm.render();
		},

        /**
        * change states cars
        */
        changeStateCars: function(model, statePurchase, opts) {

            if( model.collection === this.activitiesList ) {
                this.model.set({
                    'codigo_estado': statePurchase,
                    'actividad_acuerdo': model.collection.toJSON()
                });
            }
            if( model.collection === this.deliverablesList ) {
                this.model.set({
                    'codigo_estado': statePurchase,
                    'entregable_acuerdo': model.collection.toJSON()
                });
            }
            if( model.collection === this.advisorsList ) {
                this.model.set({
                    'codigo_estado': statePurchase,
                    'asesor_acuerdo': model.collection.toJSON()
                });
            }
            if( model.collection === this.businessmansList ) {
                this.model.set({
                    'codigo_estado': statePurchase,
                    'empresario_acuerdo': model.collection.toJSON()
                });
            }
        },

        /**
        * change text fields
        */
        onChangeField: function(e) {
            e.preventDefault();
            var attributes = this.model.toJSON();
            attributes['tab'] = this.parameters.tab;

            var data = window.Misc.formToJson(this.$formCreate), storeData = _.pick(data, 'alcance_acuerdo_compra', 'resultados_acuerdo_compra', '_token');

            if( $.trim(data['alcance_acuerdo_compra']) === this.model.get('alcance_acuerdo_compra') && $.trim(data['resultados_acuerdo_compra']) === this.model.get('resultados_acuerdo_compra') ) {
                return;
            }

            storeData.action = 'required-return-to-review';
            this.model.save(storeData, {patch: true, wait:true});
        },

        /**
        * submit new discussion
        */
        onSubmitDiscussion: function(e){
            e.preventDefault();

            var data = window.Misc.formToJson(this.$formDiscussion);
        }
	});

})(jQuery, this, this.document)
