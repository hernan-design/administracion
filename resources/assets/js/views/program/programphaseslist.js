/**
* Class ProgramPhasesListView extends of Backbone View
* @author TuProyecto || Desarrollador : @dortegon
* @link http://TuProyecto.com
*/

//Global App Backbone
app || (app = {});

(function ($, window, document, undefined) {

    app.ProgramPhasesListView = Backbone.View.extend({
        templatePhase: _.template( ($('#item-phases-tpl').html() || '') ),
        templateConfirm: _.template( ($('#phase-active-tpl').html() || '') ),
        events: {
            'change .dd': 'onGetNestable',
            'click #add-program-phase': 'onModalProgramPhases',
            'click .attachment-accordion': 'onGetAttachmentPhase',
            'click .add-attachment-phase': 'onModalAttachmentPhase',
            'click .active-phase': 'onModalConfirm',
            'click .inactive-phase': 'onModalConfirm',
            'click .edit-phase' : 'onEditPhase',
        },
        parameters: {
            template: 'templatePhase',
            dataFilter: {}
        },

        /**
        * Constructor Method
        */
        initialize : function(opts) {

            // extends parameters
            if( opts.parameters !== undefined && _.isObject(opts.parameters) )
                this.parameters = $.extend({}, this.parameters, opts.parameters);

            this.configAlert = {
                'wrapper': '#main-wrap',
                'closeTime': 20000,
                'speedOpen': 500,
                'speedClose': 500
            };

            // Collection phases
            // this.collection.comparator = 'orden_fase';
            this.phaseAttachList = new app.AttachmentPhaseList();
            this.phaseAttachList.comparator = 'orden_adjunto_fase';
            this.ProgramPhasesList = new app.ProgramPhasesList();

            // Events Listener
            this.listenTo( this.model, 'change', this.render );
			this.listenTo( this.phaseAttachList, 'add', this.addPhase );
			this.listenTo( this.phaseAttachList, 'reset', this.addAll );
            this.listenTo( this.phaseAttachList, 'request', this.loadSpinner );
			this.listenTo( this.phaseAttachList, 'sync', this.responseServer);

            // Events Listener custome
            this.on( 'beforeRender', this.beforeRender );
            this.on( 'afterRender', this.afterRender );

        },

        /*
        * Render View Element
        */
        render: function() {
            // Reference to elements
            var attributes = this.model.toJSON();
            this.$el.html( this[this.parameters.template](attributes) );

            this.$wrapperPhasesAttachment = this.$('#nestable');

            return this;
        },

        /**
		* fires libraries js and functions
		*/
        ready: function () {
            if( typeof window.initComponent.configNestable == 'function' ){
                window.initComponent.configNestable();
            }

            if( typeof window.initComponent.validateForm == 'function' ){
                window.initComponent.validateForm();
            }

            if( typeof window.initComponent.configEllipsis == 'function' ){
                window.initComponent.configEllipsis();
            }
        },

        /**
		* Render view comment by model
		* @param Object phase Model instance
		*/
		addPhase: function (phase) {
            var view = new app.ProgramPhasesItemView({
				model: phase,
				parameters: _.extend(this.parameters, {
					template: 'templateAttachmentPhases',
                    data: {
					    codigo_adjunto: phase.get('codigo_adjunto'),
					    action: false
                    }
				})
			});

			this.$wrapperPhasesAttachment.find('ol').append( view.render().el );

		},

        /**
		* Render all view Course of the collection
		*/
		addAll: function () {
			this.$wrapperPhasesAttachment.find('ol').html('');
			this.phaseAttachList.forEach( this.addPhase, this );
		},

        /**
        * Load spinner on the request
        */
        loadSpinner: function () {
            window.Misc.setSpinner( this.$wrapperPhases );
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

        responseServerPhases: function (model, resp, opts) {
            //complete proccess
            window.Misc.removeSpinner({
                'wrap': this.$el,
                'direct': false
            });
        },

        /*
        * Change position
        */
        onGetNestable: function (e) {
            var _this = this,
                sortedItems = $(e.target).nestable('serialize');

            _.each(sortedItems, function (sortItem, indx) {
                sortItem.orden_adjunto = indx + 1;
            });

            $.post( window.Misc.urlFull( Route.route('convocatoria.fase.orderAdjuntoFase') ), {
                '_token': $('meta[name=csrf-token]').attr('content'),
                data: sortedItems
            }).success( function (resp){

                window.Misc.removeSpinner({
                    wrap: _this.$el,
                    direct: false
                });

                //complete proccess
                if( resp.success !== undefined ) {
                    if( resp.success ){
                        window.Misc.showAlertBox({
                            text: resp.message,
                            type: 'success',
                            closeTime: 5000
                        });

                        // Sort new collection
                        _this.phaseAttachList.set(sortedItems, {merge:true, add:false, remove:false});
                    }else {
                        window.Misc.showAlertBox({
                            text: resp.errors,
                            type: 'alert',
                            closeTime: 5000
                        });

                        // Sort initial collection
                        _this.addAll();
                    }
                }
            });
        },

        /**
        * Modal program phases
        */
        onModalProgramPhases: function (e) {
            e.preventDefault();

            //  Program phase model
            this.phaseModel = new app.PhaseModel();

            this.listenToOnce( this.phaseModel, 'change:codigo_convocatoria', function (model) {
                this.collection.add(model);
            });

            this.programPhasesWindow = new app.ProgramPhasesWindow({
                model: this.phaseModel,
                parameters: {
                    data:{
                        codigo_convocatoria: this.model.get('codigo_convocatoria'),
                        action: true    // TRUE: NEW PHASE / FALSE: UPDATE PHASE
                    }
                }
            });
            this.programPhasesWindow.render();
        },

        onGetAttachmentPhase: function(e) {
            e.preventDefault();
            var ariaExpanded = $(e.currentTarget).attr('aria-expanded');

            if( ariaExpanded == 'false' ){
                var phase =  $(e.currentTarget).attr('id');
                var codigoPhase = phase.split("g");
                this.phaseAttachList.fetch({
                    data: { program: this.model.get('codigo_convocatoria'), phase: codigoPhase[1]},
                    reset: true
                });
            }
        },

        onModalAttachmentPhase: function(e) {
            e.preventDefault();

            this.attachmentPhase = new app.AttachmentPhaseModel();
            this.listenToOnce( this.attachmentPhase, 'change:nombre_adjunto', function (model) {
                this.phaseAttachList.add(model);
            });

            this.programPhasesWindow = new app.ProgramPhasesWindow({
                model: this.attachmentPhase,
                parameters: {
                    template: 'templateAttachment',
                    data:{
                        codigo_fase: this.model.get('codigo_fase'),
                        codigo_convocatoria: this.model.get('codigo_convocatoria'),
                        action: true,
                    },
                }
            });

            this.programPhasesWindow.render();
        },

        onEditPhase: function(e) {
            e.preventDefault();
            e.stopPropagation();

            var _this = this;

            this.phaseModel = new app.PhaseModel();

            this.phaseModel.fetch({data: {phase: this.model.get('codigo_fase')}, success: function () {
                    var data_resource = _this.phaseModel.toJSON();

                    _this.programPhasesWindow = new app.ProgramPhasesWindow({
                        model: _this.phaseModel,
                        parameters: {
                            template: 'templateAddPhase',
                            data:{
                                codigo_fase: _this.model.get('codigo_fase'),
                                action: false,
                            },
                            data_resource: data_resource,
                        }
                    });

                    _this.programPhasesWindow.render();

                }
            });

        },

        /*
       * Modal Confirm
       */
        onModalConfirm: function (e) {
            e.preventDefault();
            e.stopPropagation();

            var _this = this,
                data = {
                    'change_state': true
                };


            if( this.acceptConfirm instanceof Backbone.View ) {
                this.acceptConfirm.stopListening();
                this.acceptConfirm.undelegateEvents();
            }

            this.parameters.template = 'templatePhase';

            this.acceptConfirm = new app.ConfirmWindow({
                model: this.model,
                parameters: {
                    template: this.templateConfirm,
                    titleConfirm: "Confirmación",
                    onConfirm: function () {
                        _this.model.save(data, {wait: true,
                            success: function (model, resp) {
                                // complete proccess
                                window.Misc.removeSpinner({
                                    'wrap': _this.$el,
                                    'direct': false
                                });

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

                                //complete proccess
                                if( resp.success !== undefined ){
                                    if( resp.success ){
                                        window.Misc.showAlertBox({
                                            text: resp.message,
                                            type: 'success',
                                            closeTime: 5000
                                        });
                                    }else{
                                        window.Misc.showAlertBox({
                                            text: resp.error,
                                            type: 'alert',
                                            closeTime: 5000
                                        });
                                    }
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
