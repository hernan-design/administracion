/**
* Class CourseCreateEditView extends of Backbone View
* @author TuProyecto || Desarrollador : @dortegon
* @link http://TuProyecto.com
*/

//Global App Backbone
app || (app = {});

(function ($, window, document, undefined) {

    app.CourseCreateEditView = Backbone.View.extend({
        el: '#course-content',
        templateUrlExternal: _.template( ($('#url-external-course-tpl').html() || '') ),
        events: {
            'submit #form-course': 'addCourse',
            'change #codigo_orientacion_curso': 'onFields',
            'click #twitter-tab': 'onRenderViewTabTwitter',
            'click #facebook-tab': 'onRenderViewTabFacebook',
            'apply.daterangepicker #fecha_apertura_curso': 'onDate'
        },
        parameters: {
            codigo_curso: null
        },

        /**
        * Constructor Method
        */
        initialize : function(opts) {
            _.bindAll(this, 'beforeRender', 'render', 'afterRender', 'onReadyFile', 'onDeleteFile', 'onCompleteLoadFile', 'onCompleteFetchFiles');
            console.log(opts);
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

            if(this.model.id){
                this.fieldsShow(this.$el.find('#codigo_orientacion_curso').val());
            }
            
            // Events Listener
            this.listenTo( this.model, 'request', this.loadSpinner );
            this.listenTo( this.model, 'sync', this.responseServer );

            // Events listener custom
            this.on( 'beforeRender', this.beforeRender );
            this.on( 'afterRender', this.afterRender );

            //variable para saber codigo curso en vista organizadores
            app.CodigoCurso = this.parameters;
          
        },

        /*
        * Render View Element
        */
        render: function() {
            // references
            this.$wrapperCourseThemes = this.$('#wrapper-render-themes');

            // Initialize attributes
            this.$formCreate = this.$('#form-course');
            this.$formAddThemes = this.$('#form-add-themes-course');
            this.$wrapperImgToCrop = this.$('#wrap-course-uploader-tocrop');

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
            // render uploader and cropper of image
            this.setImgUploaderCrop();
            this.ready();
        },

        /**
        * fires libraries js
        */
        ready: function () {
            var _this = this;

            if( typeof window.initComponent.datepickerTrigger == 'function' ){
                window.initComponent.datepickerTrigger();
            }

            // input mask
            this.$(":input").inputmask();
        },

        /**
        * reference to views
        */
        referenceViews: function () {
            console.log(this.parameters);

            this.courseThemeView = new app.CourseThemeView({
                el: this.$wrapperCourseThemes,
                model: this.model,
                collection: this.collection
            });

            this.courseThemeView.render();

            if( this.$('#section-channel-content').length > 0 ){
                // Render multiselect channel content
                this.channelContentView = new app.ChannelContentView({
                    el: this.$('#section-channel-content'),
                    parameters:{
                        id_contenido: this.model.get('codigo_curso'),
                        tipo_contenido: window._tp.getModule['course']
                    }
                });
            }

            if(this.model.id){
                this.courseSeoView = new app.CourseSeoView({
                    model: this.model
                });
                this.courseSeoView.render();
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

        // Event change field
        onFields: function (e) {
            e.preventDefault();
            this.fieldsShow($(e.target).val());
        },

        // Render section url external and survey
        fieldsShow: function (param) {
            var urlExternalProgram = this.$el.find('#url-external-course');

            if( param == window._tp.courseOrientation['external'] ){
                urlExternalProgram.html( this.templateUrlExternal );
            }

            if( param == window._tp.courseOrientation['internal'] ){
                $('#url-external-section').remove();
            }
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
                wrap: this.$el,
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
                        window.Misc.redirect( app.AppRouter.navigate( Route.route('curso.edit',{curso: this.model.get('codigo_curso')}) ) );
                    }
                }else {
                    window.Misc.showAlertBox(this.configAlert);
                    return false;
                }
            }
        },

        /**
        * Add new course
        */
        addCourse: function (e) {
            e.preventDefault();

            var data = window.Misc.formToJson( e.target )
            this.model.save( data, {wait: true, patch: true} );
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
				formCrop: this.$('#form-course'),
				imgCrop: this.$('#img-course-tocrop'),
				previewImg: this.$('#course-preview-tocrop'),
				uploaderImg: this.$('#uploader-course-image'),
				templateImgUploader: 'qq-img-uploader-crop-template',
                btnToCrop: this.$('#btn-to-crop'),
				configImg: {
					maxWidthImg : 770 * 4,
					maxHeightImg : 500 * 4,
					minWidthImg : 770,
                    minHeightImg : 500
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
				extraDropzones: [this.$('#image-crop-course')],
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
                                _this.$('[name="imagen_curso"]').val(resp.file_path);
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
		},

        onDate: function (e) {
            e.preventDefault();

            var initDate = $(e.target).val();

            $('#fecha_cierre_curso').daterangepicker({
                startDate: initDate,
                minDate: initDate,
                singleDatePicker: true,
                locale: {
                    format: 'YYYY-MM-DD'
                }
            });
        },

        // Uploader

        renderUploaderFile: function () {
            var _this = this;

            this.uploaderFile = $('#upload-attach-course');

            this.uploaderFile.fineUploader({
                debug: false,
                template: 'qq-attachments-template',
                session: {
                    endpoint: window.Misc.urlFull(Route.route('adjunto.cursos.index')),
                    params: {
                        'codigo_curso': _this.model.get('codigo_curso'),
                    },
                    refreshOnRequest: false
                },
                request: {
                    inputName: 'file',
                    endpoint: window.Misc.urlFull(Route.route('adjunto.cursos.index')),
                    customHeaders: {
                        'X-CSRF-TOKEN': $('meta[name="csrf-token"]').attr('content'),
                        'X-XSRF-TOKEN': window.Misc.getCsrf().getCookie('XSRF-TOKEN')
                    },
                    params: {
                        '_token': $('meta[name="csrf-token"]').attr('content'),
                        'codigo_curso': _this.model.get('codigo_curso')
                    }
                },
                deleteFile: {
                    enabled: true,
                    endpoint: window.Misc.urlFull(Route.route('adjunto.cursos.index')),
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
                autoUpload: true,
                multiple: true,
                validation: {
                    allowedExtensions: ['doc', 'pdf'],
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
            this.uploaderFile = $('#upload-attach-course');
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

        onDeleteFile: function(id) {
            var data = this.parameters.data[id];
            var parameters = {
                "codigo_curso": this.model.get('codigo_curso'),
                "codigo_adjunto": data.codigo_adjunto
            };
            this.uploaderFile.fineUploader('setDeleteFileParams', parameters, id);
        },

        onCompleteFetchFiles: function (resp, success) {
            this.uploaderFile = $('#upload-attach-course');        
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
            // console.log(fileObj);
            $itemFile.find('.preview-link').attr('href', fileObj.direccion_adjunto);
            // reload plugins function
            this.ready();

            $itemFile.find('select').addClass('qq-hide')
                .next('.select2-container').addClass('qq-hide');
        },
   });

})(window.jQuery, window, document);
