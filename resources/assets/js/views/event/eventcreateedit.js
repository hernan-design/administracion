/**
* Class EventCreateEditView extends of Backbone View
* @author TuProyecto || Desarrollador : @dortegon
* @link http://TuProyecto.com
*/

//Global App Backbone
app || (app = {});

(function ($, window, document, undefined) {

    app.EventCreateEditView = Backbone.View.extend({
        el: '#events-content',
        templateGuest: _.template( ($('#guest-event-grid-tpl').html() || '') ),
        template: _.template( ($('#participants-event-grid-tpl').html() || '') ),
        templateButtonsAction: _.template( ($('#buttons-action-tpl').html() || '') ),
        templateButtonsActionCertificate: _.template( ($('#button-actions-certificate').html() || '') ),
        templateConfirmRemove: _.template( ($('#confirm-remove-assist-tpl').html() || '') ),
        templateConfirmRemoveAllAssist: _.template( ($('#confirm-remove-all-assist-tpl').html() || '') ),
        templateConfirm: _.template( ($('#confirm-assist-tpl').html() || '') ),
        templateConfirmAllAssist: _.template( ($('#confirm-all-assist-tpl').html() || '') ),
        templateCreateBenefit: _.template( ($('#create-benefit-event-tpl').html() || '') ),
        templateAssistControl: _.template( ($('#assist-control-event-grid-tpl').html() || '') ),
        templateConfirmInvitation: _.template( ($('#confirm-invitation-event-tpl').html() || '') ),
        templateRemoveInvitation: _.template( ($('#confirm-remove-invitation-tpl').html() || '') ),
        templateConfirmSendUsersInvitationAll: _.template( ($('#confirm-send-users-invitation-all-tpl').html() || '') ),
        events: {
            'click .btn-clear': 'clear',
            'click .btn-search': 'search',
            'submit #form-event': 'addEvent',
            'click .invitation': 'onInvitationRemove',
            'click .round-button': 'onAddOrganizer',
            'click .confirm-turn': 'onConfirmAssist',
            'click .confirm-all-turn': 'onConfirmAllAssist',
            'click .confirm-assist': 'onConfirmInvitation',
            'change #numero_sesion': 'searchAssistControl',
            'click #twitter-tab': 'onRenderViewTabTwitter',
            'apply.daterangepicker #fecha_evento': 'onDate',
            'click #facebook-tab': 'onRenderViewTabFacebook',
			'click .btn-create-benefit': 'onModalBenefitCreate',
            'click .btn-clear-assist-control': 'clearAssistControl',
            'click .btn-clear-user-invitation': 'clearUserInvitation',
            'click .btn-clear-user-responsable' : 'clearUserResponsable',
            'click .btn-add-user-responsable' : 'onAddUserResponsable',
            'click .btn-search-user-invitation': 'searchUserInvitation',
            'click .btn-add-user-organizer': 'onAddPrincipalOrganizer',
            'click  .btn-clear-user-organizer': 'onClearPrincipalOrganizer',
            'click .btn-certificate' : 'onCertificate',
            'change .slc-calendar': 'onChangeCalendar',
            'submit #form-guest-event': 'sendUserInvitationAll',
            'change .select-program': 'onProgram',
            'change #inpMeet': 'selectUrlMeeting',

        },
        parameters: {
            haveBenefits: 0
        },

        /**
        * Constructor Method
        */
        initialize: function(opts){
            _.bindAll(this, 'beforeRender', 'render', 'afterRender', 'onCertificate');

            // extends parameters
            if( opts.parameters !== undefined && _.isObject(opts.parameters) )
                this.parameters = $.extend({}, this.parameters, opts.parameters);

            //Init Attribute
            this.configAlert = {
                'wrapper': '#main-wrap',
                'closeTime': 6000,
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

            if(this.model.id){
                // Init Models
                this.benfit = new app.BenefitEventModel();
                this.organizer = new app.OrganizerModel();

                // Init Collections
                this.eventBenfitsList = new app.EventBenefitsList();
                this.eventSessionsList = new app.EventSessionsList();
                this.eventSessionsList.fetch( {data: {event: this.model.get('codigo_evento')}} );
            }

            // Events Listener
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
            // Reference to elements
            this.$formCreate = this.$('#form-event');
            this.$modalComponent = $('#modal-base-create');
            this.$wrapperOrganizers = this.$('#wrapper-render-organizers');
            this.$wrapperImgToCrop = this.$('#wrap-event-uploader-tocrop');
            this.$wrapperEventSessions = this.$('#wrapper-render-sessions');
            this.$wrapperEventBenefits = this.$('#wrapper-render-benefits');
            this.$searchOrganizerId = this.$('#id_organizador_filter_data');
            this.$searchOrganizerName = this.$('#organizador_filter_data');
            this.$gridSearchTableOrganizer = this.$('#organizer-search-table');
            this.$btnCertificate = this.$('.btn-certificate');

            this.referenceViews();

            if (attributes.codigo_evento !== undefined) {
                this.renderTableOrganizer();
                this.renderTableUserResponsable();
            }
           // this.readyButtonAssist();
            this.ready();
        },

        /**
        * fires libraries js
        */
        ready: function () {
            // reload plugins
            // if( typeof window.initComponent.select2Trigger == 'function' ){
            //     window.initComponent.select2Trigger();
            // }

            // if( typeof(window.initComponent.autosizeTrigger) == 'function')
            //     window.initComponent.autosizeTrigger();

            // if( typeof window.initComponent.icheckTrigger == 'function' ){
            //     window.initComponent.icheckTrigger();
            // }

            // if(typeof window.initComponent.multiselectTrigger == 'function' ){
            //     window.initComponent.multiselectTrigger();
            // }

            // input mask
            this.$(":input").inputmask();
        },

        readyButtonAssist: function () {
            var button;
            var _this = this;
            $.ajax({
                url: window.Misc.urlFull( Route.route('evento.participante.index') ),
                data: {
                  //  turn: $(e.currentTarget).attr('data-turn'),
                    codigo_evento: _this.model.get('codigo_evento'),
                    numero_sesion: _this.$searchSessionNumber.val()
                },
                type: 'get',
                success: function (data) {
                    var assist_to_event;
                    var codigo_asistencia_sesion = [];
                    for (var index = 0; index < data.data.length; index++) {
                        assist_to_event = data.data[index].assist_to_event;
                        if (!assist_to_event)
                            break;
                    }

                    for (var index = 0; index < data.data.length; index++) {

                        if (data.data[index].codigo_asistencia_sesion != null)
                            codigo_asistencia_sesion.push(data.data[index].codigo_asistencia_sesion);

                    }
                    if (assist_to_event) {
                        button = '<label for="">Desmarcar asistencia de todos</label> <a href="" title="Desmarcar asistencia" class="btn btn-default btn-xs confirm-all-turn" data-turn="false" data-identifier="'+codigo_asistencia_sesion+'"> <i class="fa fa-toggle-on"></i></a>';
                    } else {
                        button = '<label for="">Marcar asistencia de todos</label> <a href="" title="Marcar asisencia" class="btn btn-default btn-xs confirm-all-turn" data-turn="true"> <i class="fa fa-toggle-off"></i></a>';
                    }

                    $('#buttonAllAssist').html(button);

                }
            });
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

            // render uploader and cropper of image
            this.setImgUploaderCrop();
        },

        /**
        * reference to views
        */
        referenceViews: function () {

            if(this.model.id){
                // Render view Organizers event
                this.eventOrganizerView = new app.EventOrganizerView({
                    el: this.$wrapperOrganizers,
                    model: this.model,
                    collection: this.collection
                });

                this.eventOrganizerView.render();

                // Render view Channels
                if( this.$('#section-channel-content').length > 0 ){
                    // Render multiselect channel content
                    this.channelContentView = new app.ChannelContentView({
                        el: this.$('#section-channel-content'),
                        parameters:{
                            id_contenido: this.model.get('codigo_evento'),
                            tipo_contenido: window._tp.getModule['calendar']
                        }
                    });
                }

                // Render view Participants event
                this.$('#participants-event-grid').html( this.template() );
                this.$searchEmail = this.$('#correo_electronico');
                this.$searchIdentification = this.$('#identificacion_usuario');
                this.$gridSearchTable = this.$('#participants-event-search-table');
                this.renderParticipants();

                this.eventSeoView = new app.EventSeoView({
                    model: this.model
                });
                this.eventSeoView.render();

                // Render view Assist control
                this.$('#assist-control-event-grid').html( this.templateAssistControl( {codigo_evento: this.model.get('codigo_evento')} ) );
                this.$searchSessionNumber = this.$('#numero_sesion');
                this.$gridSearchTableAssistControl = this.$('#assist-control-event-search-table');
                this.renderAssistControl();

                // Render view Sessions
                this.eventSessionsListView = new app.EventSessionsListView({
                    el: this.$wrapperEventSessions,
                    model: this.model,
                    collection: this.eventSessionsList
                });

                this.eventSessionsListView.render();

                // Render view Benefits event
                this.eventBenefitsListView = new app.EventBenefitsListView({
                    el: this.$wrapperEventBenefits,
                    model: this.model,
                    collection: this.eventBenfitsList
                });

                this.eventBenefitsListView.render();
                this.eventBenfitsList.fetch( {data: {event: this.model.get('codigo_evento')}} );

                // Render view Guest
                this.$('#guest-event-grid').html( this.templateGuest( {codigo_evento: this.model.get('codigo_evento')} ) );
                this.$searchProgram = this.$('#codigo_convocatoria');
                this.$searchUserResponsableName = this.$('#usuario_responsable_filter_data');
                this.$searchIdentificationUserResponsable = this.$('#id_usuario_responsable_filter_data');
                this.$searchEntityInvitation = this.$('#entidad_filter_data_invitation');
                this.$searchIdentificationEntityInvitation = this.$('#id_entidad_filter_data_invitation');
                this.$searchBirthdate = this.$('#fecha_nacimiento_usuario');
                this.$searchLevelEducation = this.$('#codigo_nivel_educacion');
                this.$searchEmployment = this.$('#codigo_lista_ocupacion');
                this.$searchCity = this.$('#codigo_ciudad_usuario');
                this.$searchLocations = this.$('#codigo_localidad_ciudad');
                this.$searchZone = this.$('#codigo_barrio_localidad');
                this.$searchStratum = this.$('#codigo_estrato');
                this.$searchHousingType = this.$('#codigo_tipo_vivienda');
                this.$searchHeadOfFamily = this.$('#jefe_hogar');
                this.$searchWorkSector = this.$('#codigo_sector_trabajo');
                this.$searchEnterprise = this.$('#enterprise');
                this.$searchSelfDenomination = this.$('#codigo_autodenominacion');
                this.$searchPopulationGroup = this.$('#codigo_grupo_poblacional');
                this.$searchCommunityGroup = this.$('#codigo_grupo_comunitario');
                this.$searchSocialGroup = this.$('#codigo_grupo_social');
                this.$searchDisability = this.$('#disability');
                this.$phase = this.$('#phase');
                this.$gridSearchTableGuest = this.$('#guest-event-search-table');
                this.$gridSearchTableUserResponsable = this.$('#user-responsable-search-table');
                this.$sendInvitationAll = this.$('.btn-send-user-invitation-all');
                this.$formGuestEvent = this.$('#form-guest-event');

                this.$sendInvitationAll.hide();

                this.renderGuests();

                //renderizar carga de archivo evidencias
                this.$wrapperDocuments = this.$('#wrap-documents');
                this.EventAttachedDocuments = new app.EventAttachedDocuments({
                    el: this.$wrapperDocuments,
                    parameters: {
                        //
                    }
                });
                //end renderizar carga de archivo evidencias

                this.readyButtonAssist();
            }
        },

        renderTableUserResponsable: function() {
            var _this = this;

            this.gridSearchTableUserResponsable = this.$gridSearchTableUserResponsable.DataTable({
                dom:"<'row'<'col-sm-5'i><'col-sm-7'p>>" +
                    "<'row'<'col-sm-12'tr>>" +
                    "<'row'<'col-sm-5'i><'col-sm-7'p>>",
                processing: true,
                serverSide: true,
                language: window.Misc.dataTableES(),
                ajax: {
                    url: window.Misc.urlFull( Route.route('evento.organizerev') ),
                    data: function( data ) {
                        data.codigo_evento     = _this.model.get('codigo_evento');
                        data.responsable = true;
                    }
                },
                columns: [
                    { data: 'nombre_usuario',       name: 'nombre_usuario'      },
                    { data: 'nickname_usuario',     name: 'nickname_usuario'    },
                    { data: 'ciudad',               name: 'ciudad'              },
                    { data: 'codigo_usuario',       name: 'codigo_usuario'      },
                ],
                order: [
                    [ 0, 'desc' ]
                ],
                columnDefs: [
                    {
                        targets : 3 ,
                        visible : false
                    }
                ],
                fnRowCallback: function( row, data ) {
                }
            });
        },

        renderTableOrganizer: function() {
          var _this = this;

            this.gridSearchTableOrganizer = this.$gridSearchTableOrganizer.DataTable({
                retrieve: true,
                paging: false,
                dom:"<'row'<'col-sm-5'i><'col-sm-7'p>>" +
                    "<'row'<'col-sm-12'tr>>" +
                    "<'row'<'col-sm-5'i><'col-sm-7'p>>",
                processing: true,
                serverSide: true,
                language: window.Misc.dataTableES(),
                ajax: {
                    url: window.Misc.urlFull( Route.route('evento.organizerev') ),
                    data: function( data ) {
                        data.codigo_evento = _this.model.get('codigo_evento');
                    }
                },
                columns: [
                    { data: 'nombre_usuario',       name: 'nombre_usuario'      },
                    { data: 'nickname_usuario',     name: 'nickname_usuario'    },
                    { data: 'ciudad',               name: 'ciudad'              },
                ],
                order: [
                    [ 0, 'desc' ]
                ],
            });
        },

        onAddPrincipalOrganizer: function(e) {
            e.preventDefault();

            var data = {};
            var _this = this;

            if (this.$searchOrganizerId.val() &&  this.$searchOrganizerName.val()) {
                data.codigo_evento = this.model.get('codigo_evento');
                data.codigo_organizador = this.$searchOrganizerId.val();
                this.model.save(data, { patch: true, success: function() {
                        _this.gridSearchTableOrganizer.ajax.reload();
                        _this.listenToOnce( _this.model, 'change', _this.render );
                    }
                });
            }
        },

        onClearPrincipalOrganizer: function(e) {
          e.preventDefault();

          this.$searchOrganizerId.val('');
          this.$searchOrganizerName.val('');

          this.gridSearchTableOrganizer.ajax.reload();
        },

        /**
         * Change Calendar
         */

        onChangeCalendar: function(e) {
            e.preventDefault();

            if($(e.currentTarget).val() != null){
                $.ajax({
                    data: { calendar: $(e.currentTarget).val() },
                    url: window.Misc.urlFull( Route.route('evento.eventtype')),
                    type: 'get',
                    success: function (data) {
                        $('#codigo_tipo_evento_calendario').html('');
                        $('#codigo_tipo_evento_calendario').append($('<option></option>').text('Seleccione...').val(0));
                        //recorremos con el metodo each el objeto
                        $.each(data, function(i) {
                            //Con los parametros que recibimos en nuestro objeto convocatoria creamos las opciones
                            $('#codigo_tipo_evento_calendario').append("<option value=\""+data[i].codigo_tipo_evento_calendario+"\">"+data[i].nombre_tipo_evento+"<\/option>");
                        });
                    }
                });
            }
        },


        /**
        * Render view tap facebook
        */
        onRenderViewTabFacebook: function () {
            // Tab facebook
            if ( this.socialSeoView instanceof Backbone.View ){
                this.socialSeoView.stopListening();
                this.socialSeoView.undelegateEvents();
            }

            this.socialSeoView = new app.SocialSeoView({
                el: '#tab_facebook',
                model: this.model,
                parameters: {
                    social: 'facebook',
                    fieldimage: 'imagen_facebook',
                    data: {
                        nombre: this.model.get('nombre_facebook'),
                        imagen: this.model.get('imagen_facebook'),
                        descripcion: this.model.get('descripcion_facebook')
                    }
                }
            });
            this.socialSeoView.render();
        },

        /**
        * Render view tap twitter
        */
        onRenderViewTabTwitter: function () {
            if ( this.socialSeoView instanceof Backbone.View ){
                this.socialSeoView.stopListening();
                this.socialSeoView.undelegateEvents();
            }

            // Tab twitter
            this.socialSeoView = new app.SocialSeoView({
                el: '#tab_twitter',
                model: this.model,
                parameters: {
                    social: 'twitter',
                    fieldimage: 'imagen_twitter',
                    data: {
                        nombre: this.model.get('nombre_twitter'),
                        imagen: this.model.get('imagen_twitter'),
                        descripcion: this.model.get('descripcion_twitter')
                    }
                }
            });
            this.socialSeoView.render();
        },

        /**
        * Load spinner on the request
        */
        loadSpinner: function () {
            window.Misc.setSpinner( this.$('#section-content') );
        },

        /**
        * response of the server
        */
        responseServer: function ( model, resp, opts ) {

            window.Misc.removeSpinner({
                wrap: this.$('#section-content'),
                direct: false
            });

            // response success or error
            var type = resp.success ? 'success' : 'alert',
                text = resp.success ?
                        (resp.message !== undefined ? resp.message : '...')
                            : resp.errors;

            if( _.isObject( resp.errors ) ) {

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
            _.extend(this.configAlert, conf);

            //complete proccess
            if( resp.success !== undefined ) {
                if( resp.success ){
                    window.Misc.showAlertBox(this.configAlert);

                    if(resp.redirect){
                        window.Misc.redirect( app.AppRouter.navigate( Route.route('evento.edit',{evento: this.model.get('codigo_evento')}) ) );
                    }
                }else {
                    window.Misc.showAlertBox(this.configAlert);
                    return false;
                }
            }



        },

        /*
         * Add event
        */
        addEvent: function (e) {
            e.preventDefault();
            var data = window.Misc.formToJson( e.target );
            if (data.pago_evento == null) {
                data.pago_evento = false;
            }

            if (data.participacion_evento == null) {
                data.participacion_evento = false;
            }

            if (data.activar_recordatorio == null) {
                data.activar_recordatorio = false;
            } else {
                data.activar_recordatorio = true;
            }


            this.model.save( data, { wait: true });
        },

        // ===============================================================
		//              start of image uploader crop
		// ===============================================================

		/**
		* set image uploader crop
		*/
		setImgUploaderCrop: function () {
			var _this = this;

			this.$wrapperImgToCrop.imageUploaderCrop({
				formCrop: this.$('#form-event'),
				imgCrop: this.$('#img-event-tocrop'),
				previewImg: this.$('#event-preview-tocrop'),
				uploaderImg: this.$('#uploader-event-image'),
				templateImgUploader: 'qq-img-uploader-crop-template',
                btnToCrop: this.$('#btn-to-crop'),
				configImg: {
					maxWidthImg : 780 * 4,
					maxHeightImg : 520 * 4,
					minWidthImg : 780,
					minHeightImg : 520
				},
				uploadding: {
					inputName: 'file',
					endpoint: document.url + Route.route('file.temp'),
					customHeaders: {
						'X-CSRF-TOKEN': $('meta[name="csrf-token"]').attr('content'),
						'X-XSRF-TOKEN': window.Misc.getCsrf().getCookie('XSRF-TOKEN')
					},
					params: {
						'_token': $('meta[name="csrf-token"]').attr('content')
					}
				},
                messages: window._tp.messages,
				cropping: {
					endpoint: document.url + Route.route('file.crop'),
					params: {}
				},
				extraDropzones: [this.$('#image-crop-event')],
				// Events handlers
				callbacks: {
					onLoadImg: function ($uploaderImg, id, name) {

						window.Misc.setSpinner( $uploaderImg );
					},
					onCompleteLoad: function ($uploaderImg, id, name, res) {
						$uploaderImg.find('.qq-uploader').css( {'border': 'none'} );

						window.Misc.removeSpinner({
							'wrap': $uploaderImg,
							'direct': false
						});

                        if(res.success){
                            window.Misc.showAlertBox({
                                text: 'La imagen ha sido cargada correctamente, por favor recortela antes de enviar el formulario!!',
                                type: 'info'
                            });
                        }
					},
					onCropping: function (jcrop_img) {
						window.Misc.setSpinner( this.$element.find('.iuc__content-image-to-crop') );
					},
					onCropComplete: function (jcrop_img, resp, jqXHR) {
						// response success or error
						var confCrop = {},
							type = resp.success ? 'success' : 'alert',
							text = resp.success ?
								(resp.message !== undefined ? resp.message : '')
								: resp.errors;

						if( _.isObject( resp.errors ) ){

							var listError = '<ul>';

							$.each(resp.errors, function(field, item) {
								 listError += '<li>'+ item[0] +'</li>';
							});
							listError += '</ul>';

							text = listError;
						}

						var conf = {
							wrapper: this.$element.find('.iuc__content-image-to-crop'),
							text: text,
							type: type
						};
						confCrop = $.extend({}, _this.configAlert, conf);

						if( resp.success !== undefined ) {
							if( resp.success ){
								_this.model.set('imagen_evento', resp.file_path);
                                _this.$('[name="imagen_evento"]').val(resp.file_path);
                                window.Misc.showAlertBox( confCrop );
							}else{
								window.Misc.showAlertBox( confCrop );
							}
						}

						window.Misc.removeSpinner({
							'wrap': this.$element.find('.iuc__content-image-to-crop'),
							'direct': false
						});
					},
					onCropError: function (jcrop_img, jqXHR, error) {
						var confCrop = {},
							conf = {
								wrapper: this.$element.find('.iuc__content-image-to-crop'),
								text: 'La imagen no puede ser ajustada.',
								type: 'alert',
								close: true
							};

						confCrop = $.extend({}, _this.configAlert, conf);

						window.Misc.removeSpinner({
							'wrap': this.$element.find('.iuc__content-image-to-crop'),
							'direct': false
						});
						window.Misc.showAlertBox( confCrop );
					}
				},
                showMessage: function (message) {
                    return window.Misc.alertDialog({
                        text: message
                    });
				},
				showConfirm: function (message) {
					var promise = new qq.Promise();

                    window.Misc.confirmDialog({
                        text: message,
                        onConfirm: function (notice, result) {
                            return promise.success(result);
                        },
                        onCancel: function () {
                            return promise.failure();
                        }
                    });
					return promise;
				},
				showPrompt: function (message, defaultValue) {
					var promise = new qq.Promise();

                    window.Misc.promptDialog({
                        text: message,
                        prompt_default: defaultValue,
                        onConfirm: function (notice, val) {
                            return promise.success(val);
                        },
                        onCancel: function () {
                            return promise.failure('');
                        }
                    });
					return promise;
				}
			});
		},

        /*
        * New organizer
        */
        onAddOrganizer: function (e) {
            e.preventDefault();
            e.stopPropagation();

            this.modalChannelCategory = new app.FormSampleWindowEvent({
                model: this.organizer,
                parameters: {
                    template: _.template( ($('#form-modal-tpl').html() || '') ),
                    title: 'Agregar organizador'
                }
            });

            this.modalChannelCategory.render();
        },

        onDate: function (e) {
            e.preventDefault();

            var initDate = $(e.target).val();

            $('#fecha_fin_evento').daterangepicker({
                startDate: initDate,
                minDate: initDate,
                singleDatePicker: true,
                locale: {
                    format: 'YYYY-MM-DD'
                }
            });
        },

        // Render participants
        renderParticipants: function () {
            var _this = this;

            this.gridSearchTable = this.$gridSearchTable.DataTable({
                dom:"<'row'<'col-sm-5'i><'col-sm-7'p>>" +
                    "<'row'<'col-sm-12'tr>>" +
                    "<'row'<'col-sm-5'i><'col-sm-7'p>>",
                processing: true,
                serverSide: true,
                language: window.Misc.dataTableES(),
                ajax: {
                    url: window.Misc.urlFull( Route.route('evento.participante.index') ),
                    data: function( data ) {
                        data.codigo_evento = _this.model.get('codigo_evento');
                        data.correo_electronico = _this.$searchEmail.val();
                        data.identificacion_usuario = _this.$searchIdentification.val();
                    }
                },
                columns: [
                    { data: 'nombre_usuario', name: 'nombre_usuario' },
                    { data: 'correo_electronico', name: 'correo_electronico' },
                    { data: 'identificacion_usuario', name: 'identificacion_usuario' },
                    { data: 'movil_usuario', name: 'movil_usuario' }
                ],
                order: [
                    [ 0, 'asc' ]
                ],
                columnDefs: [
                    {
                        targets: 0,
                        className: 'text-capitalize',
                        render: function ( data, type, full, row ) {
                            return '<a href="' + window.Misc.urlFull( Route.route('usuario.show', {usuario: full.codigo_usuario})) + '" title="Ver usuario" style="color:#3FC1A5">' + full.nombre_usuario + '</a>';
                        }
                    },
                    {
                        targets: 4,
                        orderable: false,
                        className: 'text-center',
                        render: function ( data, type, full, row ) {
                            return _this.templateButtonsActionCertificate({ codigo_usuario: full.codigo_usuario, codigo_usuario_evento: full.codigo_usuario_evento });
                        }
                    }
                ]
            });
        },

        // Render assist control
        renderAssistControl: function () {
            var _this = this;
            this.gridSearchTableAssistControl = this.$gridSearchTableAssistControl.DataTable({
                dom:"<'row'<'col-sm-5'i><'col-sm-7'p>>" +
                    "<'row'<'col-sm-12'tr>>" +
                    "<'row'<'col-sm-5'i><'col-sm-7'p>>",
                processing: true,
                serverSide: true,
                language: window.Misc.dataTableES(),
                ajax: {
                    url: window.Misc.urlFull( Route.route('evento.participante.index') ),
                    data: function( data ) {
                        data.codigo_evento = _this.model.get('codigo_evento');
                        data.numero_sesion = _this.$searchSessionNumber.val();
                    }
                },
                columns: [
                    { data: 'nombre_usuario', name: 'nombre_usuario' },
                    { data: 'correo_electronico', name: 'correo_electronico' },
                    { data: 'identificacion_usuario', name: 'identificacion_usuario' },
                    { data: 'movil_usuario', name: 'movil_usuario' },
                    { data: 'asistencia_evento', name: 'asistencia_evento' }
                ],
                order: [
                    [ 0, 'asc' ]
                ],
                columnDefs: [
                    {
                        targets: 0,
                        className: 'text-capitalize',
                        render: function ( data, type, full, row ) {
                            return '<a href="' + window.Misc.urlFull( Route.route('usuario.show', {usuario: full.codigo_usuario})) + '" title="Ver usuario" style="color:#3FC1A5">' + full.nombre_usuario + '</a>';
                        }
                    },
                    {
                        targets: 4,
                        orderable: false,
                        className: 'text-center',
                        render: function ( data, type, full, row ) {
                            if( full.assist_to_event ) {
                                return '<a href="" title="Desmarcar asistencia" class="btn btn-default btn-xs confirm-turn" data-turn="false" data-name="'+full.nombre_usuario+'" data-register="'+full.codigo_usuario_evento+'" data-identifier="'+full.codigo_asistencia_sesion+'"> <i class="fa fa-toggle-on"></i></a>';
                            }else {
                                return '<a href="" title="Marcar asisencia" class="btn btn-default btn-xs confirm-turn" data-turn="true" data-name="'+full.nombre_usuario+'" data-register="'+full.codigo_usuario_evento+'"> <i class="fa fa-toggle-off"></i></a>';
                            }
                        }
                    }
                ]
            });

        },

        // Render guest
        renderGuests: function () {
            var _this = this;

            this.gridSearchTableGuest = this.$gridSearchTableGuest.DataTable({
                dom:"<'row'<'col-sm-5'i><'col-sm-7'p>>" +
                    "<'row'<'col-sm-12'tr>>" +
                    "<'row'<'col-sm-5'i><'col-sm-7'p>>",
                processing: true,
                serverSide: true,
                language: window.Misc.dataTableES(),
                ajax: {
                    url: window.Misc.urlFull( Route.route('evento.usuario.invitacion.index') ),
                    data: function( data ) {
                        data.codigo_evento = _this.model.get('codigo_evento');
                        data.codigo_convocatoria = _this.$searchProgram.val();
                        data.identificacion_usuario_search =_this.$searchIdentificationEntityInvitation.val();
                        data.fecha_nacimiento_usuario = _this.$searchBirthdate.val();
                        data.codigo_nivel_educacion = _this.$searchLevelEducation.val();
                        data.codigo_lista_ocupacion = _this.$searchEmployment.val();
                        data.codigo_ciudad = _this.$searchCity.val();
                        data.codigo_localidad_ciudad = _this.$searchLocations.val();
                        data.codigo_barrio_localidad = _this.$searchZone.val();
                        data.codigo_estrato = _this.$searchStratum.val();
                        data.codigo_tipo_vivienda = _this.$searchHousingType.val();
                        data.jefe_hogar = _this.$searchHeadOfFamily.val();
                        data.codigo_sector_trabajo = _this.$searchWorkSector.val();
                        data.enterprise = _this.$searchEnterprise.val();
                        data.codigo_autodenominacion = _this.$searchSelfDenomination.val();
                        data.codigo_grupo_poblacional = _this.$searchPopulationGroup.val();
                        data.codigo_grupo_comunitario = _this.$searchCommunityGroup.val();
                        data.codigo_grupo_social = _this.$searchSocialGroup.val();
                        data.disability = _this.$searchDisability.val();
                        data.phase = _this.$phase.val();
                    }
                },
                columns: [
                    { data: 'nombre_usuario', name: 'nombre_usuario' },
                    { data: 'correo_electronico', name: 'correo_electronico' },
                    { data: 'identificacion_usuario', name: 'identificacion_usuario' },
                    { data: 'movil_usuario', name: 'movil_usuario' },
                    { data: 'asistencia_evento', name: 'asistencia_evento' },
                    { data: 'codigo_usuario', name: 'codigo_usuario' }
                ],
                order: [
                    [ 0, 'asc' ]
                ],
                columnDefs: [
                    {
                        targets: 0,
                        className: 'text-capitalize',
                        render: function ( data, type, full, row ) {
                            return '<a href="' + window.Misc.urlFull( Route.route('usuario.show', {usuario: full.codigo_usuario})) + '" title="Ver usuario" style="color:#3FC1A5">' + full.nombre_usuario + '</a>';
                        }
                    },
                    {
                      targets: 4,
                      orderable: false,
                      className: 'text-center',
                      render: function(data, type, full, row) {
                          if( full.invitation_event ) {
                              return '<a href="#" title="Desactivar invitación" class="btn btn-default btn-xs confirm-assist" data-invitation="true" data-turn="true" data-user="'+full.codigo_usuario+'" data-name="'+full.nombre_usuario+'"> <i class="fa fa-toggle-on"></i></a>';
                          }else {
                              return '<a href="#" title="Activar invitación" class="btn btn-default btn-xs confirm-assist" data-invitation="true" data-turn="false" data-user="'+full.codigo_usuario+'" data-name="'+full.nombre_usuario+'"> <i class="fa fa-toggle-off"></i></a>';
                          }
                      }
                    },
                    {
                        targets: 5,
                        visible: false
                    }
                ]
            });
        },

        /**
        * confirm assist
        */
        onConfirmAssist: function (e) {
            e.preventDefault();

            this.eventBenefitsAssitList = new app.EventBenefitsList();
            this.eventAssistControlModel = new app.EventAssistControlModel();

            var _this = this,
                data = {
                    turn: $(e.currentTarget).attr('data-turn'),
                    codigo_evento: this.model.get('codigo_evento'),
                    nombre_usuario: $(e.currentTarget).attr('data-name'),
                    codigo_usuario_evento: $(e.currentTarget).attr('data-register')
                },
                sessionNumber = $(e.currentTarget).parents().find('#numero_sesion').val();

            if(_.isEmpty(sessionNumber)){
                var config = {
                    'text': 'Debes seleccionar una sesión',
                    'type': 'alert'
                };
                _.extend( _this.configAlert, config );
                window.Misc.showAlertBox(_this.configAlert);

                return;
            }

            if(data.turn == 'true'){
                data['codigo_sesion_evento'] = sessionNumber;

                this.acceptConfirm = new app.ConfirmWindow({
                    model: this.eventAssistControlModel.set(data),
                    parameters: {
                        template: this.templateConfirm( data ),
                        titleConfirm: "Confirmación",
                        onConfirm: function () {
                            _this.eventAssistControlModel.save(data, {
                                success: function (model, resp) {

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
                                            _this.gridSearchTableAssistControl.ajax.reload();
                                            _this.readyButtonAssist();
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

            if(data.turn == 'false'){
                if( this.acceptConfirm instanceof Backbone.View ) {
                    this.acceptConfirm.stopListening();
                    this.acceptConfirm.undelegateEvents();
                }

                data['codigo_asistencia_sesion'] = $(e.currentTarget).attr('data-identifier');

                this.acceptConfirm = new app.ConfirmWindow({
                    model: this.eventAssistControlModel.set(data),
                    parameters: {
                        template: this.templateConfirmRemove( data ),
                        titleConfirm: "Confirmación",
                        onConfirm: function () {
                            _this.eventAssistControlModel.destroy({
                                success: function (model, resp) {

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
                                            _this.gridSearchTableAssistControl.ajax.reload();
                                            _this.readyButtonAssist();
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

        },

        /**
         * confirm all assist
         * @param {*} e
         */
        onConfirmAllAssist: function (e){
            e.preventDefault();
            this.eventAssistControlModel = new app.EventAssistControlModel();
            var dataAssist = [];
            var _this = this;
            $.ajax({
                dataAssist: dataAssist,
                url: window.Misc.urlFull( Route.route('evento.participante.index') ),
                data: {
                    turn: $(e.currentTarget).attr('data-turn'),
                    codigo_evento: _this.model.get('codigo_evento'),
                    numero_sesion: _this.$searchSessionNumber.val()
                },
                type: 'get',
                success: function (data) {
                    for (var index = 0; index < data.data.length; index++) {
                        dataAssist.push( data.data[index].codigo_usuario_evento )
                    }
                }
            });
            data = {
                turn: $(e.currentTarget).attr('data-turn'),
                codigo_evento: this.model.get('codigo_evento'),
                //   nombre_usuario: $(e.currentTarget).attr('data-name'),
                dataAssist: dataAssist,
                codigo_usuario_evento: $(e.currentTarget).attr('data-register')
            },
            sessionNumber = $(e.currentTarget).parents().find('#numero_sesion').val();

            if(_.isEmpty(sessionNumber)){
                var config = {
                    'text': 'Debes seleccionar una sesión',
                    'type': 'alert'
                };
                _.extend( _this.configAlert, config );
                window.Misc.showAlertBox(_this.configAlert);

                return;
            }

            if(data.turn == 'true'){
                data['codigo_sesion_evento'] = sessionNumber;

                this.acceptConfirm = new app.ConfirmWindow({
                    model: this.eventAssistControlModel.set(data),
                    parameters: {
                        template: this.templateConfirmAllAssist(),
                        titleConfirm: "Confirmación",
                        onConfirm: function () {
                            _this.eventAssistControlModel.save(data, {
                                success: function (model, resp) {

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
                                            _this.gridSearchTableAssistControl.ajax.reload();
                                            _this.readyButtonAssist();
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

            if(data.turn == 'false'){
                if( this.acceptConfirm instanceof Backbone.View ) {
                    this.acceptConfirm.stopListening();
                    this.acceptConfirm.undelegateEvents();
                }

                data['codigo_asistencia_sesion'] = $(e.currentTarget).attr('data-identifier');
                data['dataAssist'] = $(e.currentTarget).attr('data-identifier');

                this.acceptConfirm = new app.ConfirmWindow({
                    model: this.eventAssistControlModel.set(data),
                    parameters: {
                        template: this.templateConfirmRemoveAllAssist,
                        titleConfirm: "Confirmación",
                        onConfirm: function () {
                            _this.eventAssistControlModel.destroy({
                                success: function (data, resp, jqXHR) {
                                    // response success or error
                                    var text = resp.success
                                            ? (resp.message !== undefined ? resp.message : '...')
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
                                            _this.gridSearchTableAssistControl.ajax.reload();
                                            _this.readyButtonAssist();
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
        },

        onConfirmInvitation: function(e) {
            e.preventDefault();

            this.eventParticipantModel = new app.EventParticipantModel();

            var _this = this,
                data = {
                    turn: $(e.currentTarget).data('turn'),
                    codigo_usuario: $(e.currentTarget).data('user'),
                    nombre_usuario: $(e.currentTarget).data('name'),
                    invitation: $(e.currentTarget).data('invitation') ? true : false,
                    codigo_evento: this.model.get('codigo_evento')
                };


            this.acceptConfirm = new app.ConfirmWindow({
                model:this.eventParticipantModel.set(data),
                parameters: {
                    template: data.turn && data.invitation ? this.templateRemoveInvitation : data.turn ? this.templateConfirm( data ) : this.templateConfirmSendUsersInvitationAll( data ),
                    titleConfirm: "Confirmación",
                    onConfirm: function () {
                        _this.eventParticipantModel.save(data, {
                            success: function (model, resp) {

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
                                        _this.gridSearchTableGuest.ajax.reload();
                                        window.Misc.showAlertBox({
                                            text: resp.message,
                                            type: 'success',
                                            closeTime: 5000
                                        });
                                    }else{
                                        window.Misc.showAlertBox({
                                            text: resp.errors,
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
        },

        /**
        * confirm invitation remove
        */
        onInvitationRemove: function (e) {
            e.preventDefault();

            this.eventParticipantModel = new app.EventParticipantModel();

            var _this = this,
                data = {
                    turn: $(e.currentTarget).attr('data-turn'),
                    action: $(e.currentTarget).attr('data-action'),
                    codigo_evento: this.model.get('codigo_evento'),
                    user_name: $(e.currentTarget).attr('data-name'),
                    codigo_usuario: $(e.currentTarget).attr('data-register')
                };

            if(data.turn == 'false'){
                data.codigo_usuario_evento = $(e.currentTarget).attr('data-remove');

                if( this.removeConfirmInvitation instanceof Backbone.View ) {
                    this.removeConfirmInvitation.stopListening();
                    this.removeConfirmInvitation.undelegateEvents();
                }

                this.removeConfirmInvitation = new app.ConfirmWindow({
                    model: _this.eventParticipantModel.set( data ),
                    parameters: {
                        template: this.templateConfirmInvitation( data ),
                        titleConfirm: "Confirmación",
                        onConfirm: function () {

                            _this.eventParticipantModel.destroy({
                                success: function (model, resp) {

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
                                            _this.gridSearchTableGuest.ajax.reload();
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
                this.removeConfirmInvitation.render();
            }
        },

        /**
         *
         * Modal cre benefit
         */
        onModalBenefitCreate: function(e) {
            e.preventDefault();
            e.stopPropagation();

            this.modalCreateBenefit = new app.FormSampleWindow({
                model: this.benfit,
                parameters: {
                    template: _.template( ($('#create-benefit-event-tpl').html() || '') ),
                    title: 'Crear beneficio'
                }
            });

            this.modalCreateBenefit.render();
		},

        /**
         * Search participants
         * @param {*} e
         */
        search: function(e) {
            e.preventDefault();
            this.gridSearchTable.ajax.reload();
        },

        /**
         * Clear param of search from participants
         * @param {*} e
         */
        clear: function(e) {
            e.preventDefault();

            this.$searchEmail.val('');
            this.$searchIdentification.val('');

            this.gridSearchTable.ajax.reload();
        },

        /**
         * Search assist control
         * @param {*} e
         */
        searchAssistControl: function (e) {
            e.preventDefault();
            this.readyButtonAssist();
            this.gridSearchTableAssistControl.ajax.reload();
        },

        /**
         * Clear param of search from assist control
         * @param {*} e
         */
        clearAssistControl: function (e) {
            e.preventDefault();

            this.$searchSessionNumber.select2('val', '');
            this.readyButtonAssist();
            this.gridSearchTableAssistControl.ajax.reload();
        },

        /**
         * Clear param of search from user
         * @param {*} e
         */
        clearUserInvitation: function (e) {
            e.preventDefault();

            this.$searchProgram.select2('val', '');
            this.$searchEntityInvitation.val('');
            this.$searchIdentificationEntityInvitation.val('');
            this.$phase.val('');
            // this.$searchBirthdate.val('');
            // this.$searchLevelEducation.val('');
            // this.$searchEmployment.val('');
            // this.$searchCity.select2('val', '');
            // this.$searchLocations.val('');
            // this.$searchZone.val('');
            // this.$searchStratum.val('');
            // this.$searchHousingType.val('');
            // this.$searchHeadOfFamily.val('');
            // this.$searchWorkSector.val('');
            // this.$searchEnterprise.val('');
            // this.$searchSelfDenomination.val('');
            // this.$searchPopulationGroup.val('');
            // this.$searchCommunityGroup.val('');
            // this.$searchSocialGroup.val('');
            // this.$searchDisability.val('');
            this.gridSearchTableGuest.ajax.reload();
        },

        clearUserResponsable: function(e) {
            this.$searchUserResponsableName.val('');
            this.$searchIdentificationUserResponsable.val('');

            this.gridSearchTableUserResponsable.ajax.reload();
        },

        /**
         * Search user
         * @param {*} e
         */
        searchUserInvitation: function (e) {
            e.preventDefault();
            if ((this.$searchProgram.val() != null ) || (this.$searchIdentificationEntityInvitation.val() != '')) {
                this.$sendInvitationAll.show();
            } else if (this.$searchProgram.val() == '' || this.$searchIdentificationEntityInvitation.val() == '') {
                this.$sendInvitationAll.hide();
            }

            this.gridSearchTableGuest.ajax.reload();
        },

        onAddUserResponsable: function(e) {
          e.preventDefault();

          var _this = this,
              attributes = {};
          attributes.codigo_evento = this.model.get('codigo_evento');
          attributes.responsable = true;
          attributes.identification_responsable = this.$searchIdentificationUserResponsable.val();

          this.model.save(attributes, { patch: true, success: function () {
                  _this.gridSearchTableUserResponsable.ajax.reload();
              }
          });
        },

        onCertificate: function(e) {
          e.preventDefault(e);

          var _this = this,
              attributes = {};

          attributes.codigo_usuario = $(e.currentTarget).data('user');
          attributes.codigo_usuario_evento = $(e.currentTarget).data('user-event');
          attributes.codigo_evento = this.model.get('codigo_evento');
          attributes.certificate = true;

          window.Misc.setSpinner( this.$('#section-content') );

            $.ajax({
                method: 'POST',
                url: window.Misc.urlFull( Route.route( 'evento.certificado') ),
                data: attributes
            }).done(function(resp) {
                var _this = this;


                window.Misc.removeSpinner({
                    wrap: $('#section-content'),
                    direct: false
                });

                // response success or error
                var type = resp.success ? 'success' : 'alert',
                    text = resp.success ?
                            (resp.message !== undefined ? resp.message : '...')
                                : resp.errors;

                if( _.isObject( resp.errors ) ) {

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
                _.extend(_this.configAlert, conf);

                //complete proccess
                if( resp.success !== undefined ) {
                    if( resp.success ){
                        window.Misc.showAlertBox(_this.configAlert);

                        if(resp.redirect){
                            window.Misc.redirect( app.AppRouter.navigate( Route.route('evento.edit',{evento: _this.model.get('codigo_evento')}) ) );
                        }
                    }else {
                        window.Misc.showAlertBox(_this.configAlert);
                        return false;
                    }
                }
            }.bind(this));

        },

        /**
         * Send user invitation all
         */
        sendUserInvitationAll: function (e) {
            e.preventDefault();

            this.eventParticipantModel = new app.EventParticipantModel();

            var _this = this,
                data_table = this.gridSearchTableGuest.rows().data(),
                data = [],
                dataUsers = [],
                userCode = "",
                codigo_evento = this.$('.btn-send-user-invitation-all').attr('data-event');

            data['codigo_evento'] = codigo_evento;


            if (data_table.length > 1) {
                $.map(data_table, function(item) {
                    dataUsers.push(item.codigo_usuario);
                });

                data['codigos_usuarios'] = dataUsers;
            } else {
                $.map(data_table, function(item) {
                    userCode = item.codigo_usuario
                });

                data['codigo_usuario'] = userCode;
            }


            if( this.sendUsersInvitationAll instanceof Backbone.View ) {
                this.sendUsersInvitationAll.stopListening();
                this.sendUsersInvitationAll.undelegateEvents();
            }

            this.sendUsersInvitationAll = new app.ConfirmWindow({
                model: this.eventParticipantModel.set( data ),
                parameters: {
                    template: this.templateConfirmSendUsersInvitationAll( data ),
                    titleConfirm: "Confirmación",
                    onConfirm: function () {

                        _this.eventParticipantModel.save(data, {
                            success: function (model, resp) {

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
                                        _this.gridSearchTableGuest.ajax.reload();
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
            this.sendUsersInvitationAll.render();
        },

        onProgram: function(e) {
            e.preventDefault();

            if($(e.currentTarget).val() != null){
                $.ajax({
                    data:{program: $(e.currentTarget).val() },
                    url: window.Misc.urlFull( Route.route('convocatoria.fase.index')),
                    type: 'get',
                    success: function (data) {
                        $('#phase').html('');
                        $('#phase').append($('<option></option>').text('Seleccione una fase').val(0));
                        //recorremos con el metodo each el objeto
                        $.each(data, function(i) {
                            //Con los parametros que recibimos en nuestro objeto convocatoria creamos las opciones
                            $('#phase').append("<option value=\""+data[i].codigo_fase+"\">"+data[i].nombre_fase+"<\/option>");
                        });
                    }
                });
            }
        },

        selectUrlMeeting: function (e) {
            e.preventDefault();

            if ($(e.currentTarget).val() == 0) {
                $( ".others_urls_meeting" ).show( "slow");
            } else {
                $( ".others_urls_meeting" ).hide( "slow");
            }
        }
    });
})(window.jQuery, window, document);
