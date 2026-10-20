/**
* Class ProgramCreateEditView extends of Backbone View
* @author TuProyecto || Desarrollador : @dortegon
* @link http://TuProyecto.com
*/

//Global App Backbone
app || (app = {});

(function ($, window, document, undefined) {

    app.ProgramCreateEditView = Backbone.View.extend({
        el: '#programs-content',
        templateSurvey: _.template( ($('#survey-program-tpl').html() || '') ),
        templateUrlExternal: _.template( ($('#url-external-program-tpl').html() || '') ),
        templateButtonsAction: _.template( ($('#buttons-action-entity-tpl').html() || '') ),
        events: {
            'submit #form-program': 'addProgram',
            'change #tipo_convocatoria': 'onFields',
            /* Boton encargado de realizar busquedas en la tabla entidad */
            // 'click .btn-search-user-entity': 'searchEntity',
            'click .btn-clear-user-entity': 'clearEntity',
            'click .btn-add-user-entity': 'addEntity',
            'click .btn-unlink-entities': 'unlinkEntity',
            'submit #program-add-results': 'addResults'
        },
        parameters: {
            'codigo_convocatoria': null,
            'data': []
        },

        /**
        * Constructor Method
        */
        initialize: function(opts){
            _.bindAll(this, 'beforeRender', 'render', 'afterRender', 'onReadyFile', 'onCompleteLoadFile', 'onCompleteFetchFiles', 'onDeleteFile');

            // extends parameters
            if( opts.parameters !== undefined && _.isObject(opts.parameters) ){
                this.parameters = $.extend({}, this.parameters, opts.parameters);
            }

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

            this.changes_in_form = [];

            this.stateUpload = "";

            // Model AttachmentModel
            this.AttachmentModel = new app.AttachmentModel;

            // init collection
            this.usersProgramList = new app.UsersProgramList;

            if(this.model.id){
                this.fieldsShow(this.$el.find('#tipo_convocatoria').val());
            }

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
            // references
            this.$wrapperProgramUsers = this.$('#wrapper-render-users');
            this.$wrapperProgramPhases = this.$('#wrapper-render-phases');
            this.$searchEntityId      = this.$('#id_entidad_filter_data');
            this.$searchEntityName = this.$('#entidad_filter_data');
            this.$userListSearchTable = this.$('#entity-search-table');
            this.$userAddEntity = this.$('.btn-add-user-entity');

            // Initialize attributes
            this.$wrapperImgToCrop = this.$('#wrap-program-uploader-tocrop');
            this.$formCreate = this.$('#form-program');

            this.ready();
            this.referenceViews();
            this.renderDatatableEntity();
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

            // render uploader and cropper of image
            this.setImgUploaderCrop();
        },

        /**
        * fires libraries js
        */
        ready: function () {
            // reload plugins
            if( typeof window.initComponent.select2Trigger == 'function' ){
                window.initComponent.select2Trigger();
            }

            if( typeof(window.initComponent.autosizeTrigger) == 'function')
                window.initComponent.autosizeTrigger();

            if( typeof window.initComponent.icheckTrigger == 'function' ){
                window.initComponent.icheckTrigger();
            }

            if(typeof window.initComponent.multiselectTrigger == 'function' ){
                window.initComponent.multiselectTrigger();
            }
            // input mask
            this.$(":input").inputmask();
        },

        renderDatatableEntity: function() {
            var _this = this;

            this.userListSearchTable = this.$userListSearchTable.DataTable({
                retrieve: true,
                paging: false,
                dom:"<'row'<'col-sm-5'i><'col-sm-7'p>>" +
                    "<'row'<'col-sm-12'tr>>" +
                    "<'row'<'col-sm-5'i><'col-sm-7'p>>",
                processing: true,
                serverSide: true,
                language: window.Misc.dataTableES(),
                ajax: {
                    url: window.Misc.urlFull( Route.route('usuario.searchEntities') ),
                    data: function( data ) {
                        data.identificacion_entidad = _this.$searchEntityId.val();
                        data.codigo_convocatoria = _this.model.get('codigo_convocatoria');
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
                    },
                    {
                        targets : 4,
                        orderable: false,
                        className: 'text-center',
                        render: function ( data, type, full, row ) {
                            return _this.templateButtonsAction({codigo_entidad: full.codigo_usuario, codigo_convocatoria: _this.model.get('codigo_convocatoria')});
                        }
                    }
                ],
                fnRowCallback: function( row, data ) {
                }
            });
        },

        // searchEntity: function(e) {
        //     e.preventDefault();
        //     this.userListSearchTable.ajax.reload();
        // },

        clearEntity: function(e) {
          e.preventDefault();

            this.$searchEntityId.val('');
            this.$searchEntityName.val('');

            this.userListSearchTable.ajax.reload();
        },

        addEntity: function(e) {
            e.preventDefault();
            var data = {};
            var _this = this;
            if (this.$searchEntityId.val() &&  this.$searchEntityName.val()) {
                this.stopListening(this.model, 'change');
                  data.codigo_convocatoria = this.model.get('codigo_convocatoria');
                  data.codigo_entidad = this.$searchEntityId.val();
                  this.model.save(data, { patch: true, success: function() {
                          _this.userListSearchTable.ajax.reload();
                          _this.listenTo( _this.model, 'change', _this.render );
                    }
                  });
            }
        },

        unlinkEntity: function(e) {
          e.preventDefault();
          var _this = this;
          var codigo_entidad = $('.' + e.target.className).data('entidad');
          var codigo_convocatoria = $('.' + e.target.className).data('convocatoria');
          var data = {};
          data.codigo_convocatoria = codigo_convocatoria;
          data.codigo_entidad = codigo_entidad;
          data.unlink_entity = true;
            this.stopListening(this.model, 'change');
          this.model.save(data, { wait: true, patch: true, success: function(){
                  _this.userListSearchTable.ajax.reload();
                  _this.listenTo( _this.model, 'change', _this.render );
              }
          })
        },

        // Uploader

        renderUploaderFile: function () {
            var _this = this;

            this.uploaderFile = $('#upload-program-editor');

            this.uploaderFile.fineUploader({
                debug: false,
                template: 'qq-attachments-template',
                session: {
                    endpoint: window.Misc.urlFull(Route.route('adjuntos.index')),
                    params: {
                        'codigo_convocatoria': _this.model.get('codigo_convocatoria'),
                    },
                    refreshOnRequest: false
                },
                request: {
                    inputName: 'file',
                    endpoint: window.Misc.urlFull(Route.route('file.upload')),
                    customHeaders: {
                        'X-CSRF-TOKEN': $('meta[name="csrf-token"]').attr('content'),
                        'X-XSRF-TOKEN': window.Misc.getCsrf().getCookie('XSRF-TOKEN')
                    },
                    params: {
                        '_token': $('meta[name="csrf-token"]').attr('content'),
                        'codigo_convocatoria': _this.model.get('codigo_convocatoria')
                    }
                },
                deleteFile: {
                    enabled: true,
                    endpoint: window.Misc.urlFull(Route.route('adjuntos.index')),
                    customHeaders: {
                        'X-CSRF-TOKEN': $('meta[name="csrf-token"]').attr('content'),
                        'X-XSRF-TOKEN': window.Misc.getCsrf().getCookie('XSRF-TOKEN')
                    }
                },
                thumbnails: {
                    placeholders: {
                        waitingPath: window.Misc.urlFull('img/waiting-generic.png'),
                        notAvailablePath: window.Misc.urlFull('img/not_available-generic.png')
                    }
                },
                autoUpload: false,
                multiple: true,
                validation: {
                    allowedExtensions: ['doc', 'pdf', 'xlsx', 'pptx'],
                    itemLimit: 0,
                    stopOnFirstInvalidFile: false,
                    sizeLimit: (8 * 1024) * 1024 // 8 MB = (8 * 1024) * 1024 bytes
                },
                // Events handlers
                callbacks: {
                    onSubmitted: _this.onReadyFile,
                    onComplete: _this.onCompleteLoadFile,
                    onSessionRequestComplete: _this.onCompleteFetchFiles,
                    onSubmitDelete: this.onDeleteFile,
                    onStatusChange: function (id, oldStatus, newStatus) {
                        if(newStatus == qq.status['UPLOAD_SUCCESSFUL']) {
                            window.setTimeout(function () {
                                var $itemFile = _this.uploaderFile.fineUploader('getItemByFileId', id);
                            }, 500);
                        }
                    }
                },
                dragAndDrop: {
                    extraDropzones: []
                },
                text: {
                    fileInputTitle: 'Carga un archivo',
                    failUpload: 'La carga falló',
                    paused: 'Pausado',
                    waitingForResponse: 'Procesando...',
                },
                messages: window._tp.messages,
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

        onReadyFile: function (id, name) {
            this.ready();
        },

        onCompleteLoadFile: function (id, name, resp) {
            var _this = this;
            this.uploaderFile = $('#upload-program-editor');
            var dataAttachment = {
                'direccion_adjunto': resp.file_path,
                'nombre_adjunto': name,
                'codigo_convocatoria': this.model.get('codigo_convocatoria')
            };
            var $itemFile = this.uploaderFile.fineUploader('getItemByFileId', id);
            this.itemWasLoaded($itemFile, dataAttachment);
            this.AttachmentModel.idAttribute = null;
            this.AttachmentModel.save(dataAttachment, { type: 'POST', wait: true, success: function(){
                _this.parameters.data.push({"codigo_adjunto" : _this.AttachmentModel.get('codigo_adjunto')});
            }});
        },

        onCompleteFetchFiles: function (resp, success) {
            this.uploaderFile = $('#upload-program-editor');
            if (Array.isArray(resp)) {
                this.parameters.data = resp;
            } else {
                return false
            }
            var _this = this,
                count = 0;
            if( success ) {
                _.each(resp, function(file) {

                    var $itemFile = this.uploaderFile.fineUploader('getItemByFileId', count);
                    this.itemWasLoaded($itemFile, file);

                    count++;
                }, this);
            }
        },

        itemWasLoaded: function ($itemFile, fileObj) {
            console.log(fileObj);
            $itemFile.find('.preview-link').attr('href', fileObj.direccion_adjunto);
            // reload plugins function
            this.ready();

            $itemFile.find('select').addClass('qq-hide')
                .next('.select2-container').addClass('qq-hide');
        },

        onDeleteFile: function(id) {
            var data = this.parameters.data[id];
            var parameters = {
                "codigo_convocatoria": this.model.get('codigo_convocatoria'),
                "codigo_adjunto": data.codigo_adjunto
            };
            this.uploaderFile.fineUploader('setDeleteFileParams', parameters, id);
        },

        /**
        * reference to views
        */
        referenceViews: function () {
            // Render view phases
            this.programPhasesListView = new app.ProgramPhaseView({
                el: this.$wrapperProgramPhases,
                model: this.model,
                collection: this.collection,
                parameters: {
                    data: {
                        codigo_convocatoria: this.parameters.codigo_convocatoria
                    }
                }
            });

            this.programPhasesListView.render();

            // Render multiselect channel content
            if( this.$('#section-channel-content').length > 0 ){
                this.channelContentView = new app.ChannelContentView({
                    el: this.$('#section-channel-content'),
                    parameters:{
                        id_contenido: this.model.get('codigo_convocatoria'),
                        tipo_contenido: window._tp.getModule['program']
                    }
                });
            }

            if(this.model.get('codigo_orientacion_convocatoria') == window._tp.orientationProgram['user']){
                
                // Render view users
                this.programUsersListView = new app.ProgramUsersListView({
                    el: this.$el,
                    model: this.model,
                    collection: this.usersProgramList,
                    parameters: {
                        data: {
                            program: this.model.get('codigo_convocatoria')
                        }
                    }
                }); 

                this.programUsersListView.trigger('getdata', {program: this.model.get('codigo_convocatoria')});
            }
        },

        /**
        * Load spinner on the request
        */
        loadSpinner: function () {
            window.Misc.setSpinner( this.$('#section-content') )
        },

        /**
        * Add new program
        */
        addProgram: function (e) {
            e.preventDefault();
            var _this = this;
            this.uploaderFile = $('#upload-program-editor');
            var data = window.Misc.formToJson(e.target);
            var changes = this.changesInForm(data);
            var getUploadsSubmitted = this.uploaderFile.fineUploader('getUploads', ({
                status: [qq.status.SUBMITTED]
            }));
            if (getUploadsSubmitted.length === 0) {
                this.model.save(data, {wait: true, patch: true});
            }

            if (getUploadsSubmitted.length > 0 && changes === false) {
                this.uploaderFile.fineUploader('uploadStoredFiles');
            }

            if (getUploadsSubmitted.length > 0 && changes === true) {
                this.model.save(data, { wait: true, patch: true, success: function () {
                        _this.uploaderFile.fineUploader('uploadStoredFiles');
                    }
                });
            }
        },

        changesInForm: function(obj) {
            var change = this.changes_in_form.push(obj);
            if (change === 1) {
                return true;
            } else {
                if (change > 1) {
                    if (this.changes_in_form[change -  1] === this.changes_in_form[change - 2]) {
                        return false;
                    } else {
                        return true
                    }
                }
            }
         },


        addResults: function(e) {
            e.preventDefault();
            var _this = this;
            var data = window.Misc.formToJson(e.target);
            data.codigo_convocatoria = this.model.get('codigo_convocatoria');
            this.stopListening(this.model, 'change');
            this.model.save(data, { wait: true, patch: true, success: function() {
                _this.listenTo( _this.model, 'change', _this.render );
            }});
        },


        // Event change field
        onFields: function (e) {
            e.preventDefault();
            this.fieldsShow($(e.target).val());
        },

        // Render section url external and survey
        fieldsShow: function (param) {
            var urlExternalProgram = this.$el.find('#url-external-program');
            var surveyProgram = this.$el.find('#survey-program');

            if( param == window._tp.typeProgram['external'] ){
                urlExternalProgram.html( this.templateUrlExternal );
                $('#survey-section').remove();
            }

            if( param == window._tp.typeProgram['internal'] ){
                surveyProgram.html( this.templateSurvey );
                this.ready();
                $('#url-external-section').remove();
            }
        },

        /**
        * response of the server
        */
        responseServer: function ( model, resp, opts ) {
            var _this = this;
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
                    window.setTimeout(function() {
                        window.Misc.showAlertBox(_this.configAlert);
                    }, 2000);
                    if(resp.redirect) {
                        window.setTimeout(function() {
                            window.Misc.redirect( app.AppRouter.navigate( Route.route('convocatoria.edit',{convocatorium: _this.model.get('codigo_convocatoria')}) ) )
                        }, 3000);
                    }
                }else {
                    window.Misc.showAlertBox(this.configAlert);
                    return false;
                }
            }
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
				formCrop: this.$('#form-program'),
				imgCrop: this.$('#img-program-tocrop'),
				previewImg: this.$('#program-preview-tocrop'),
				uploaderImg: this.$('#uploader-program-image'),
				templateImgUploader: 'qq-img-uploader-crop-template',
                btnToCrop: this.$('#btn-to-crop'),
				configImg: {
                    maxWidthImg : 590 * 4,
					maxHeightImg : 354 * 4,
					minWidthImg : 590,
					minHeightImg : 354
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
				cropping: {
					endpoint: document.url + Route.route('file.crop'),
					params: {}
				},
				extraDropzones: [this.$('#image-crop-program')],
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
                                _this.$('[name="imagen_convocatoria"]').val(resp.file_path);
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
                messages: window._tp.messages,
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
        }
    });
})(window.jQuery, window, document);
