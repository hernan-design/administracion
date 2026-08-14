/**
* Class ProgramCreateEditView extends of Backbone View
* @author TuProyecto || Desarrollador : @dortegon
* @link http://TuProyecto.com
*/

//Global App Backbone
app || (app = {});

(function ($, window, document, undefined) {

    app.InstitutionCreateEdit = Backbone.View.extend({
        el: '#institution-content',
        templateParametersIntitution: _.template( ($('#template-parameters-institution-tpl').html() || '') ),
        templateMetaIntitution: _.template( ( $('#template-meta-institution-tpl').html() || '' ) ),
        templateModulesInstitution: _.template( ( $('#template-accordion-models-institution-tpl').html() || '' ) ),
        events: {
            'submit #form-create-institution': 'addInstitution',
            'submit #form-create-homepage-institution': 'addHomePage',
            'submit #form-create-modules-institution': 'addModules',
            'submit #form-create-terms-institution': 'addTerms',
        },
        parameters: {
            'codigo_convocatoria': null,
            'data': []
        },

        /**
        * Constructor Method
        */
        initialize: function(opts){
            _.bindAll(this, 'beforeRender', 'render', 'afterRender', 'onCompleteFetchLogoFiles', 'onCompleteFetchLogoMailFiles', 'onCompleteFetchImageAdminFiles', 'onCompleteFetchImageReportFiles', 'onCompleteFetchLogoHeaderFiles', 'onCompleteFetchFaviconFiles', 'onCompleteFetchContentNetworkSocialsFiles', 'onCompleteFetchImageModuleFiles');

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
            this.$formCreateInstitution = this.$('#form-create-institution');
            this.$parametersInstitution = this.$('#render-parameters-institution-tpl');
            this.$metaInstitution = this.$('#render-meta-institution-tpl');
            this.$accordionModules = this.$('#render-accordion-modules-institution-tpl');

            this.ready();
            this.referenceViews();
        },
      
        referenceViews: function(){
            var attributes = this.model.toJSON(), _this = this;
            
            this.$parametersInstitution.html(this.templateParametersIntitution( { parameters: attributes.parametros } ));
            this.$metaInstitution.html( this.templateMetaIntitution ( { meta: attributes.meta } ) );
            this.$accordionModules.html( this.templateModulesInstitution( { modulos: attributes.modulo, 'parametros': attributes.parametrosPlataforma, 'valueParameters': attributes.valorParametros } ) );
            this.$uploaderLogoHeaderFile = this.$('#logo_header');
            this.$uploaderFavIconFile = this.$('#favicon_meta_institucion');
            this.$uploaderSocialImageFile = this.$('#social_image_meta_institucion');

            if (this.$uploaderLogoHeaderFile.length > 0) {
                // render uploader plugin
                this.renderImageLogoHeaderUploaderFile();
            }

            if (this.$uploaderFavIconFile.length > 0) {
                
                // Render Uploader
                this.renderImageFaviconUploaderFile();
            }

            if (this.$uploaderSocialImageFile.length > 0) {
                
                // Render Uploader
                this.renderSocialImageUploaderFile();
            }

            if (!_.isEmpty(attributes.modulo)) {
                attributes.modulo.forEach(function(element) {
                    var code_module = element.codigo_modulo;

                    _this.$uploaderImageModule = _this.$('#imagen_modulos_institucion_' + element.codigo_modulo);
                    
                    _this.renderImageModuleUploaderFile(code_module);
                });
            }
        },

        addInstitution: function(e) {
            e.preventDefault();
            var data = window.Misc.formToJson(e.target), attributes = this.model.toJSON(), _this = this;

            this.stopListening(this.model, 'change');

            this.model.clear().set(this.model.defaults);

            this.model.set({'codigo_institucion': this.parameters.institution})

            if (attributes.codigo_institucion) {
                data.update = true;
                data.tab = 'home';
            }

            this.model.save(data, { wait: true, success: function() {
                _this.listenTo(this.model, 'change');
            } });
        },

        addHomePage: function(e) {
            e.preventDefault();
            var data = window.Misc.formToJson(e.target);

            this.stopListening(this.model, 'change');

            this.model.clear().set(this.model.defaults);
            this.model.set({'codigo_institucion': this.parameters.institution})
            
            

            this.sendDatatoUpdateHomePage(data);
        },

        addModules: function(e) {
            e.preventDefault();
            var data = window.Misc.formToJson(e.target);

            this.stopListening(this.model, 'change');

            this.model.clear().set(this.model.defaults);
            this.model.set({'codigo_institucion': this.parameters.institution})
            
            this.senDatatoModule(data);
        },

        addTerms: function(e) {
            e.preventDefault();
            var data = window.Misc.formToJson(e.target), _this = this;

            this.stopListening(this.model, 'change');

            this.model.clear().set(this.model.defaults);
            
            this.model.set({'codigo_institucion': this.parameters.institution});

            data.terms = true;

            this.model.save(data, { wait: true, success: function() {
                _this.listenTo(this.model, 'change');
            } });
        },

        senDatatoModule: function(data) {
            var _this = this, dataFormed = [], dataSending = [], termColorTitle = 'color_title-', termBackground = 'background-', termColorHover = 'color_hover-', termColorIcon = 'color_icon-',
            termBackgroundHover = 'background_hover-', termColorBefore = 'color_before-', backgroundIcon = 'background_icon-', color = 'color-', termFontSize = 'fontsize-', termText = 'text-',
            termFontSizeTitle = 'fontsize_title-', termPosition = 'position-', termTitle = 'title-', termEstado = '_estado-';

            $.each( data, function( key, value ) {
                dataFormed.push(key + '-:' + value);
            });
            
            
            var dataColorTitle = _.filter(dataFormed, function(item){ return item.toLowerCase().indexOf(termColorTitle) > -1 }),
            dataBackground = _.filter(dataFormed, function(item){ return item.toLowerCase().indexOf(termBackground) > -1 }),
            dataColorHover =  _.filter(dataFormed, function(item){ return item.toLowerCase().indexOf(termColorHover) > -1 }),
            dataColorIcon = _.filter(dataFormed, function(item){ return item.toLowerCase().indexOf(termColorIcon) > -1 }),
            dataBackgroundHover = _.filter(dataFormed, function(item){ return item.toLowerCase().indexOf(termBackgroundHover) > -1 }),
            dataColorBefore = _.filter(dataFormed, function(item){ return item.toLowerCase().indexOf(termColorBefore) > -1 }),
            dataBackgroundIcon = _.filter(dataFormed, function(item){ return item.toLowerCase().indexOf(backgroundIcon) > -1 }),
            dataColor = _.filter(dataFormed, function(item){ return item.toLowerCase().indexOf(color) > -1 }),
            dataFontSize =  _.filter(dataFormed, function(item){ return item.toLowerCase().indexOf(termFontSize) > -1 }),
            dataText = _.filter(dataFormed, function(item){ return item.toLowerCase().indexOf(termText) > -1 }),
            dataFontSizeTitle = _.filter(dataFormed, function(item){ return item.toLowerCase().indexOf(termFontSizeTitle) > -1 }),
            dataPosition = _.filter(dataFormed, function(item){ return item.toLowerCase().indexOf(termPosition) > -1 }),
            dataTitle = _.filter(dataFormed, function(item){ return item.toLowerCase().indexOf(termTitle) > -1 }),
            dataEstado = _.filter(dataFormed, function(item){ return item.toLowerCase().indexOf(termEstado) > -1 });     

            dataSending['color_title'] = dataColorTitle;
            dataSending['background'] = dataBackground;
            dataSending['color_hover'] = dataColorHover;
            dataSending['color_icon'] = dataColorIcon;
            dataSending['background_hover'] = dataBackgroundHover;
            dataSending['color_before'] = dataColorBefore;
            dataSending['background_icon'] = dataBackgroundIcon;
            dataSending['color'] = dataColor;
            dataSending['fontsize'] = dataFontSize;
            dataSending['text'] = dataText;
            dataSending['font_size_title'] = dataFontSizeTitle;
            dataSending['position'] = dataPosition;
            dataSending['title'] = dataTitle;
            dataSending['estado'] = dataEstado;
 
            dataSending.update = true;
            dataSending.tab = 'module'

            this.model.save(dataSending, { wait: true, success: function() {
                _this.listenTo(this.model, 'change');
            } });

            this.ready();
        },

        sendDatatoUpdateHomePage: function(data) {
            var _this = this, dataFormed = [], dataSending = [], termParameters = 'parameters_', termSocial = 'social_';

            $.each( data, function( key, value ) {
                dataFormed.push(key + '-:' + value);
            });
            
            
            var dataParameters = _.filter(dataFormed, function(item){ return item.toLowerCase().indexOf(termParameters) > -1 });
            var dataSocial = _.filter(dataFormed, function(item){ return item.toLowerCase().indexOf(termSocial) > -1 });
            
            dataSending['parameters'] = dataParameters;
            dataSending['social'] = dataSocial;
            

            dataSending.update = true;
            dataSending.tab = 'homepage'

            this.model.save(dataSending, { wait: true, success: function() {
                _this.listenTo(this.model, 'change');
            } });

            this.ready();

        },

		/**
		* render files uploader
		*/
		renderImageLogoHeaderUploaderFile: function () {
            var _this = this, parametro = this.$uploaderLogoHeaderFile.data('parametro');
            this.uploaderFileLogoReport = $('#logo_header');

			this.$uploaderLogoHeaderFile.fineUploader({
				debug: false,
                template: 'qq-attachments-template',
                session: {
                    endpoint: window.Misc.urlFull(Route.route('institucion.attachment')),
                    params: {
                        'codigo_institucion': _this.model.get('codigo_institucion'),
                        'type': 'imageLogoHeader',
                        'codigo_parametro': parametro,
                    },
                    refreshOnRequest: false
                },
				request: {
                    inputName: 'file',
                    endpoint: window.Misc.urlFull(Route.route('institucion.upload')),
                    customHeaders: {
                        'X-CSRF-TOKEN': $('meta[name="csrf-token"]').attr('content'),
                        'X-XSRF-TOKEN': window.Misc.getCsrf().getCookie('XSRF-TOKEN')
                    },
                    params: {
                        '_token': $('meta[name="csrf-token"]').attr('content'),
                        'codigo_institucion': _this.model.get('codigo_institucion'),
                        'codigo_parametro': parametro,
                        'type': 'imageLogoHeader'
                    }
                },
                // deleteFile: {
                //     enabled: true,
                //     endpoint: window.Misc.urlFull(Route.route('institucion.deleteAttach')),
                //     customHeaders: {
                //         'X-CSRF-TOKEN': $('meta[name="csrf-token"]').attr('content'),
                //         'X-XSRF-TOKEN': window.Misc.getCsrf().getCookie('XSRF-TOKEN')
                //     },
                //     params: {
                //         '_token': $('meta[name="csrf-token"]').attr('content'),
                //         'codigo_institucion': _this.model.get('codigo_institucion'),
                //         'type': 'imageLogoHeader',
                //         'codigo_parametro': parametro,
                //     }
                // },
				thumbnails: {
					placeholders: {
						waitingPath: window.Misc.urlFull('img/waiting-generic.png'),
						notAvailablePath: window.Misc.urlFull('img/not_available-generic.png')
					}
				},
				autoUpload: true,
				multiple: false,
				validation: {
					allowedExtensions: window._tp.inDocs,
					itemLimit: 0,
					stopOnFirstInvalidFile: false,
					sizeLimit: (8 * 1024) * 1024 // 8 MB = (8 * 1024) * 1024 bytes
				},
				// Events handlers
				callbacks: {
                    onSessionRequestComplete: _this.onCompleteFetchLogoHeaderFiles,
                    onStatusChange: function (id, oldStatus, newStatus) {

                        if(newStatus == qq.status['UPLOAD_SUCCESSFUL']) {

                            window.setTimeout(function () {
                                var $itemFile = _this.uploaderFileLogoReport.fineUploader('getItemByFileId', id);
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
                    waitingForResponse: 'Procesando...'
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

        renderSocialImageUploaderFile: function () {
            var _this = this, meta = this.$uploaderSocialImageFile.data('meta');
            this.uploaderSocialImageFile = $('#social_image_meta_institucion');

			this.$uploaderSocialImageFile.fineUploader({
				debug: false,
                template: 'qq-attachments-template',
                session: {
                    endpoint: window.Misc.urlFull(Route.route('institucion.attachment')),
                    params: {
                        'codigo_institucion': _this.model.get('codigo_institucion'),
                        'type': 'imageMeta',
                        'codigo_meta': meta,
                    },
                    refreshOnRequest: false
                },
				request: {
                    inputName: 'file',
                    endpoint: window.Misc.urlFull(Route.route('institucion.upload')),
                    customHeaders: {
                        'X-CSRF-TOKEN': $('meta[name="csrf-token"]').attr('content'),
                        'X-XSRF-TOKEN': window.Misc.getCsrf().getCookie('XSRF-TOKEN')
                    },
                    params: {
                        '_token': $('meta[name="csrf-token"]').attr('content'),
                        'codigo_institucion': _this.model.get('codigo_institucion'),
                        'codigo_meta': meta,
                        'type': 'imageMeta'
                    }
                },
                // deleteFile: {
                //     enabled: true,
                //     endpoint: window.Misc.urlFull(Route.route('institucion.deleteAttach')),
                //     customHeaders: {
                //         'X-CSRF-TOKEN': $('meta[name="csrf-token"]').attr('content'),
                //         'X-XSRF-TOKEN': window.Misc.getCsrf().getCookie('XSRF-TOKEN')
                //     },
                //     params: {
                //         '_token': $('meta[name="csrf-token"]').attr('content'),
                //         'codigo_institucion': _this.model.get('codigo_institucion'),
                //         'type': 'imageMeta',
                //         'codigo_meta': meta,
                //     }
                // },
				thumbnails: {
					placeholders: {
						waitingPath: window.Misc.urlFull('img/waiting-generic.png'),
						notAvailablePath: window.Misc.urlFull('img/not_available-generic.png')
					}
				},
				autoUpload: true,
				multiple: false,
				validation: {
					allowedExtensions: ['jpg', 'png'],
					itemLimit: 0,
					stopOnFirstInvalidFile: false,
                    sizeLimit: (8 * 1024) * 1024, // 8 MB = (8 * 1024) * 1024 bytes
                    image: {
                        maxHeight: 870,
                        maxWidth: 1850,
                    }
				},
				// Events handlers
				callbacks: {
                    onSessionRequestComplete: _this.onCompleteFetchContentNetworkSocialsFiles,
                    onStatusChange: function (id, oldStatus, newStatus) {

                        if(newStatus == qq.status['UPLOAD_SUCCESSFUL']) {

                            window.setTimeout(function () {
                                var $itemFile = _this.uploaderSocialImageFile.fineUploader('getItemByFileId', id);
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
                    waitingForResponse: 'Procesando...'
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

        renderImageModuleUploaderFile: function () {
            var _this = this, modulo = this.$uploaderImageModule.data('modulo'), parametro = this.$uploaderImageModule.data('parametro');
            this.uploaderImageModule = $('#imagen_modulos_institucion_' + modulo);
        
			this.$uploaderImageModule.fineUploader({
				debug: false,
                template: 'qq-attachments-template',
                session: {
                    endpoint: window.Misc.urlFull(Route.route('institucion.attachment')),
                    params: {
                        'codigo_institucion': _this.model.get('codigo_institucion'),
                        'type': 'imageModule',
                        'codigo_modulo': modulo,
                        'codigo_parametro': parametro,
                    },
                    refreshOnRequest: false
                },
				request: {
                    inputName: 'file',
                    endpoint: window.Misc.urlFull(Route.route('institucion.upload')),
                    customHeaders: {
                        'X-CSRF-TOKEN': $('meta[name="csrf-token"]').attr('content'),
                        'X-XSRF-TOKEN': window.Misc.getCsrf().getCookie('XSRF-TOKEN')
                    },
                    params: {
                        '_token': $('meta[name="csrf-token"]').attr('content'),
                        'codigo_institucion': _this.model.get('codigo_institucion'),
                        'codigo_modulo': modulo,
                        'codigo_parametro': parametro,
                        'type': 'imageModule'
                    }
                },
                // deleteFile: {
                //     enabled: true,
                //     endpoint: window.Misc.urlFull(Route.route('institucion.deleteAttach')),
                //     customHeaders: {
                //         'X-CSRF-TOKEN': $('meta[name="csrf-token"]').attr('content'),
                //         'X-XSRF-TOKEN': window.Misc.getCsrf().getCookie('XSRF-TOKEN')
                //     },
                //     params: {
                //         '_token': $('meta[name="csrf-token"]').attr('content'),
                //         'codigo_institucion': _this.model.get('codigo_institucion'),
                //         'type': 'imageMeta',
                //         'codigo_meta': meta,
                //     }
                // },
				thumbnails: {
					placeholders: {
						waitingPath: window.Misc.urlFull('img/waiting-generic.png'),
						notAvailablePath: window.Misc.urlFull('img/not_available-generic.png')
					}
				},
				autoUpload: true,
				multiple: false,
				validation: {
					allowedExtensions: ['jpg', 'png'],
					itemLimit: 0,
					stopOnFirstInvalidFile: false,
                    sizeLimit: (8 * 1024) * 1024, // 8 MB = (8 * 1024) * 1024 bytes
                    image: {
                        maxHeight: 355,
                        maxWidth: 590,
                    }
				},
				// Events handlers
				callbacks: {
                    onSessionRequestComplete: _this.onCompleteFetchImageModuleFiles,
                    onStatusChange: function (id, oldStatus, newStatus) {

                        if(newStatus == qq.status['UPLOAD_SUCCESSFUL']) {

                            window.setTimeout(function () {
                                var $itemFile = _this.uploaderImageModule.fineUploader('getItemByFileId', id);
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
                    waitingForResponse: 'Procesando...'
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
        
        renderImageFaviconUploaderFile: function () {
            var _this = this, meta = this.$uploaderFavIconFile.data('meta');
            this.uploaderFavIconFile = $('#favicon_meta_institucion');
            
			this.$uploaderFavIconFile.fineUploader({
				debug: false,
                template: 'qq-attachments-template',
                session: {
                    endpoint: window.Misc.urlFull(Route.route('institucion.attachment')),
                    params: {
                        'codigo_institucion': _this.model.get('codigo_institucion'),
                        'type': 'imageMeta',
                        'codigo_meta': meta,
                    },
                    refreshOnRequest: false
                },
				request: {
                    inputName: 'file',
                    endpoint: window.Misc.urlFull(Route.route('institucion.upload')),
                    customHeaders: {
                        'X-CSRF-TOKEN': $('meta[name="csrf-token"]').attr('content'),
                        'X-XSRF-TOKEN': window.Misc.getCsrf().getCookie('XSRF-TOKEN')
                    },
                    params: {
                        '_token': $('meta[name="csrf-token"]').attr('content'),
                        'codigo_institucion': _this.model.get('codigo_institucion'),
                        'codigo_meta': meta,
                        'type': 'imageMeta'
                    }
                },
                // deleteFile: {
                //     enabled: true,
                //     endpoint: window.Misc.urlFull(Route.route('institucion.deleteAttach')),
                //     customHeaders: {
                //         'X-CSRF-TOKEN': $('meta[name="csrf-token"]').attr('content'),
                //         'X-XSRF-TOKEN': window.Misc.getCsrf().getCookie('XSRF-TOKEN')
                //     },
                //     params: {
                //         '_token': $('meta[name="csrf-token"]').attr('content'),
                //         'codigo_institucion': _this.model.get('codigo_institucion'),
                //         'type': 'imageMeta',
                //         'codigo_meta': meta,
                //     }
                // },
				thumbnails: {
					placeholders: {
						waitingPath: window.Misc.urlFull('img/waiting-generic.png'),
						notAvailablePath: window.Misc.urlFull('img/not_available-generic.png')
					}
				},
				autoUpload: true,
				multiple: false,
				validation: {
					allowedExtensions: ['png'],
					itemLimit: 0,
					stopOnFirstInvalidFile: false,
                    sizeLimit: (8 * 1024) * 1024, // 8 MB = (8 * 1024) * 1024 bytes
                    image: {
                        maxHeight: 16,
                        maxWidth: 16,
                    }
				},
				// Events handlers
				callbacks: {
                    onSessionRequestComplete: _this.onCompleteFetchFaviconFiles,
                    onStatusChange: function (id, oldStatus, newStatus) {

                        if(newStatus == qq.status['UPLOAD_SUCCESSFUL']) {

                            window.setTimeout(function () {
                                var $itemFile = _this.uploaderFavIconFile.fineUploader('getItemByFileId', id);
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
                    waitingForResponse: 'Procesando...'
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

        renderUploaderFileLogo: function () {
            var _this = this;

            this.uploaderFileLogo = $('#upload_logo_institucion');

            this.uploaderFileLogo.fineUploader({
                debug: false,
                template: 'qq-attachments-template',
                session: {
                    endpoint: window.Misc.urlFull(Route.route('institucion.attachment')),
                    params: {
                        'codigo_institucion': _this.model.get('codigo_institucion'),
                        'type': 'logo'
                    },
                    refreshOnRequest: false
                },
                request: {
                    inputName: 'file',
                    endpoint: window.Misc.urlFull(Route.route('institucion.upload')),
                    customHeaders: {
                        'X-CSRF-TOKEN': $('meta[name="csrf-token"]').attr('content'),
                        'X-XSRF-TOKEN': window.Misc.getCsrf().getCookie('XSRF-TOKEN')
                    },
                    params: {
                        '_token': $('meta[name="csrf-token"]').attr('content'),
                        'codigo_institucion': _this.model.get('codigo_institucion'),
                        'type': 'logo'
                    }
                },
                // deleteFile: {
                //     enabled: true,
                //     endpoint: window.Misc.urlFull(Route.route('institucion.deleteAttach')),
                //     customHeaders: {
                //         'X-CSRF-TOKEN': $('meta[name="csrf-token"]').attr('content'),
                //         'X-XSRF-TOKEN': window.Misc.getCsrf().getCookie('XSRF-TOKEN')
                //     },
                //     params: {
                //         '_token': $('meta[name="csrf-token"]').attr('content'),
                //         'id': _this.model.get('codigo_institucion'),
                //         'type': 'logo'
                //     }
                // },
                thumbnails: {
                    placeholders: {
                        waitingPath: window.Misc.urlFull('img/waiting-generic.png'),
                        notAvailablePath: window.Misc.urlFull('img/not_available-generic.png')
                    }
                },
                autoUpload: true,
                multiple: true,
                validation: {
                    allowedExtensions: ['jpg', 'png'],
                    itemLimit: 0,
                    stopOnFirstInvalidFile: false,
                    sizeLimit: (8 * 1024) * 1024, // 8 MB = (8 * 1024) * 1024 bytes
                    image: {
                        maxHeight: 80,
                        maxWidth: 270,
                    }
                },
                // Events handlers
                callbacks: {
                    onSessionRequestComplete: _this.onCompleteFetchLogoFiles,
                    onStatusChange: function (id, oldStatus, newStatus) {
                        if(newStatus == qq.status['UPLOAD_SUCCESSFUL']) {
                            window.setTimeout(function () {
                                var $itemFile = _this.uploaderFileLogo.fineUploader('getItemByFileId', id);
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

        renderUploaderFileImageMail: function () {
            var _this = this;

            this.uploaderFileLogoImageMail = $('#upload_imagen_mail_institucion');

            this.uploaderFileLogoImageMail.fineUploader({
                debug: false,
                template: 'qq-attachments-template',
                session: {
                    endpoint: window.Misc.urlFull(Route.route('institucion.attachment')),
                    params: {
                        'codigo_institucion': _this.model.get('codigo_institucion'),
                        'type': 'logoMail'
                    },
                    refreshOnRequest: false
                },
                request: {
                    inputName: 'file',
                    endpoint: window.Misc.urlFull(Route.route('institucion.upload')),
                    customHeaders: {
                        'X-CSRF-TOKEN': $('meta[name="csrf-token"]').attr('content'),
                        'X-XSRF-TOKEN': window.Misc.getCsrf().getCookie('XSRF-TOKEN')
                    },
                    params: {
                        '_token': $('meta[name="csrf-token"]').attr('content'),
                        'codigo_institucion': _this.model.get('codigo_institucion'),
                        'type': 'logoMail'
                    }
                },
                // deleteFile: {
                //     enabled: true,
                //     endpoint: window.Misc.urlFull(Route.route('institucion.deleteAttach')),
                //     customHeaders: {
                //         'X-CSRF-TOKEN': $('meta[name="csrf-token"]').attr('content'),
                //         'X-XSRF-TOKEN': window.Misc.getCsrf().getCookie('XSRF-TOKEN')
                //     },
                //     params: {
                //         '_token': $('meta[name="csrf-token"]').attr('content'),
                //         'codigo_institucion': _this.model.get('codigo_institucion'),
                //         'type': 'logoMail'
                //     }
                // },
                thumbnails: {
                    placeholders: {
                        waitingPath: window.Misc.urlFull('img/waiting-generic.png'),
                        notAvailablePath: window.Misc.urlFull('img/not_available-generic.png')
                    }
                },
                autoUpload: true,
                multiple: false,
                validation: {
                    allowedExtensions: ['jpg', 'png'],
                    itemLimit: 0,
                    stopOnFirstInvalidFile: false,
                    sizeLimit: (8 * 1024) * 1024,
                    image: {
                        maxHeight: 80,
                        maxWidth: 270,
                    } // 8 MB = (8 * 1024) * 1024 bytes
                },
                // Events handlers
                callbacks: {
                    onSessionRequestComplete: _this.onCompleteFetchLogoMailFiles,
                    onStatusChange: function (id, oldStatus, newStatus) {
                        if(newStatus == qq.status['UPLOAD_SUCCESSFUL']) {
                            window.setTimeout(function () {
                                var $itemFile = _this.uploaderFileLogoImageMail.fineUploader('getItemByFileId', id);
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
        
        renderUploaderFileImageAdmin: function () {
            var _this = this;

            this.uploaderFileLogoAdmin = $('#upload_imagen_administrador_institucion');

            this.uploaderFileLogoAdmin.fineUploader({
                debug: false,
                template: 'qq-attachments-template',
                session: {
                    endpoint: window.Misc.urlFull(Route.route('institucion.attachment')),
                    params: {
                        'codigo_institucion': _this.model.get('codigo_institucion'),
                        'type': 'imageAdmin'
                    },
                    refreshOnRequest: false
                },
                request: {
                    inputName: 'file',
                    endpoint: window.Misc.urlFull(Route.route('institucion.upload')),
                    customHeaders: {
                        'X-CSRF-TOKEN': $('meta[name="csrf-token"]').attr('content'),
                        'X-XSRF-TOKEN': window.Misc.getCsrf().getCookie('XSRF-TOKEN')
                    },
                    params: {
                        '_token': $('meta[name="csrf-token"]').attr('content'),
                        'codigo_institucion': _this.model.get('codigo_institucion'),
                        'type': 'imageAdmin'
                    }
                },
                // deleteFile: {
                //     enabled: true,
                //     endpoint: window.Misc.urlFull(Route.route('institucion.deleteAttach')),
                //     customHeaders: {
                //         'X-CSRF-TOKEN': $('meta[name="csrf-token"]').attr('content'),
                //         'X-XSRF-TOKEN': window.Misc.getCsrf().getCookie('XSRF-TOKEN')
                //     },
                //     params: {
                //         '_token': $('meta[name="csrf-token"]').attr('content'),
                //         'codigo_institucion': _this.model.get('codigo_institucion'),
                //         'type': 'imageAdmin'
                //     }
                // },
                thumbnails: {
                    placeholders: {
                        waitingPath: window.Misc.urlFull('img/waiting-generic.png'),
                        notAvailablePath: window.Misc.urlFull('img/not_available-generic.png')
                    }
                },
                autoUpload: true,
                multiple: false,
                validation: {
                    allowedExtensions: ['jpg', 'png'],
                    itemLimit: 0,
                    stopOnFirstInvalidFile: false,
                    sizeLimit: (8 * 1024) * 1024, // 8 MB = (8 * 1024) * 1024 bytes
                    image: {
                        maxHeight: 30,
                        maxWidth: 50,
                    } 
                },
                // Events handlers
                callbacks: {
                    onSessionRequestComplete: _this.onCompleteFetchImageAdminFiles,
                    onStatusChange: function (id, oldStatus, newStatus) {
                        if(newStatus == qq.status['UPLOAD_SUCCESSFUL']) {
                            window.setTimeout(function () {
                                var $itemFile = _this.uploaderFileLogoAdmin.fineUploader('getItemByFileId', id);
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

        renderUploaderFileImageReport: function () {
            var _this = this;

            this.uploaderFileLogoReport = $('#upload_imagen_reporte_institucion');

            this.uploaderFileLogoReport.fineUploader({
                debug: false,
                template: 'qq-attachments-template',
                session: {
                    endpoint: window.Misc.urlFull(Route.route('institucion.attachment')),
                    params: {
                        'codigo_institucion': _this.model.get('codigo_institucion'),
                        'type': 'imageReport'
                    },
                    refreshOnRequest: false
                },
                request: {
                    inputName: 'file',
                    endpoint: window.Misc.urlFull(Route.route('institucion.upload')),
                    customHeaders: {
                        'X-CSRF-TOKEN': $('meta[name="csrf-token"]').attr('content'),
                        'X-XSRF-TOKEN': window.Misc.getCsrf().getCookie('XSRF-TOKEN')
                    },
                    params: {
                        '_token': $('meta[name="csrf-token"]').attr('content'),
                        'codigo_institucion': _this.model.get('codigo_institucion'),
                        'type': 'imageReport'
                    }
                },
                // deleteFile: {
                //     enabled: true,
                //     endpoint: window.Misc.urlFull(Route.route('institucion.deleteAttach')),
                //     customHeaders: {
                //         'X-CSRF-TOKEN': $('meta[name="csrf-token"]').attr('content'),
                //         'X-XSRF-TOKEN': window.Misc.getCsrf().getCookie('XSRF-TOKEN')
                //     },
                //     params: {
                //         '_token': $('meta[name="csrf-token"]').attr('content'),
                //         'codigo_institucion': _this.model.get('codigo_institucion'),
                //         'type': 'imageReport'
                //     }
                // },
                thumbnails: {
                    placeholders: {
                        waitingPath: window.Misc.urlFull('img/waiting-generic.png'),
                        notAvailablePath: window.Misc.urlFull('img/not_available-generic.png')
                    }
                },
                autoUpload: true,
                multiple: false,
                validation: {
                    allowedExtensions: ['jpg', 'png'],
                    itemLimit: 0,
                    stopOnFirstInvalidFile: false,
                    sizeLimit: (8 * 1024) * 1024, // 8 MB = (8 * 1024) * 1024 bytes
                    image: {
                        maxWidth: 600,
                    } 
                },
                // Events handlers
                callbacks: {
                    onSessionRequestComplete: _this.onCompleteFetchImageReportFiles,
                    onStatusChange: function (id, oldStatus, newStatus) {
                        if(newStatus == qq.status['UPLOAD_SUCCESSFUL']) {
                            window.setTimeout(function () {
                                var $itemFile = _this.uploaderFileLogoReport.fineUploader('getItemByFileId', id);
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

        onCompleteFetchLogoFiles: function (resp, success) {
            this.uploaderFileLogo = $('#upload_logo_institucion');
            if (Array.isArray(resp)) {
                this.parameters.data = resp;
            } else {
                return false
            }
            var _this = this,
                count = 0;
            if( success ) {
                _.each(resp, function(file) {

                    var $itemFile = this.uploaderFileLogo.fineUploader('getItemByFileId', count);
                    this.itemLogoWasLoaded($itemFile, file);

                    count++;
                }, this);
            }
        },

        itemLogoWasLoaded: function ($itemFile, fileObj) {
            $itemFile.find('.preview-link').attr('href', fileObj.direccion_adjunto);
            // reload plugins function
            this.ready();

            $itemFile.find('select').addClass('qq-hide')
                .next('.select2-container').addClass('qq-hide');
        },

        onCompleteFetchLogoMailFiles: function (resp, success) {
            this.uploaderFileLogoImageMail = $('#upload_imagen_mail_institucion');
            if (Array.isArray(resp)) {
                this.parameters.data = resp;
            } else {
                return false
            }
            var _this = this,
                count = 0;
            if( success ) {
                _.each(resp, function(file) {

                    var $itemFile = this.uploaderFileLogoImageMail.fineUploader('getItemByFileId', count);
                    this.itemLogoMailWasLoaded($itemFile, file);

                    count++;
                }, this);
            }
        },

        itemLogoMailWasLoaded: function ($itemFile, fileObj) {
            $itemFile.find('.preview-link').attr('href', fileObj.direccion_adjunto);
            // reload plugins function
            this.ready();

            $itemFile.find('select').addClass('qq-hide')
                .next('.select2-container').addClass('qq-hide');
        },

        onCompleteFetchImageAdminFiles: function (resp, success) {
            this.uploaderFileLogoAdmin = $('#upload_imagen_administrador_institucion');
            if (Array.isArray(resp)) {
                this.parameters.data = resp;
            } else {
                return false
            }
            var _this = this,
                count = 0;
            if( success ) {
                _.each(resp, function(file) {

                    var $itemFile = this.uploaderFileLogoAdmin.fineUploader('getItemByFileId', count);
                    this.itemImageAdminWasLoaded($itemFile, file);

                    count++;
                }, this);
            }
        },

        itemImageAdminWasLoaded: function ($itemFile, fileObj) {
            $itemFile.find('.preview-link').attr('href', fileObj.direccion_adjunto);
            // reload plugins function
            this.ready();

            $itemFile.find('select').addClass('qq-hide')
                .next('.select2-container').addClass('qq-hide');
        },

        onCompleteFetchImageReportFiles: function (resp, success) {
            this.uploaderFileLogoReport = $('#upload_imagen_reporte_institucion');
            if (Array.isArray(resp)) {
                this.parameters.data = resp;
            } else {
                return false
            }
            var _this = this,
                count = 0;
            if( success ) {
                _.each(resp, function(file) {

                    var $itemFile = this.uploaderFileLogoReport.fineUploader('getItemByFileId', count);
                    this.itemImageReportWasLoaded($itemFile, file);

                    count++;
                }, this);
            }
        },

        itemImageReportWasLoaded: function ($itemFile, fileObj) {
            $itemFile.find('.preview-link').attr('href', fileObj.direccion_adjunto);
            // reload plugins function
            this.ready();

            $itemFile.find('select').addClass('qq-hide')
                .next('.select2-container').addClass('qq-hide');
        },

        onCompleteFetchLogoHeaderFiles: function (resp, success) {
            this.uploaderLogoHeaderFile = $('#logo_header');
            if (Array.isArray(resp)) {
                this.parameters.data = resp;
            } else {
                return false
            }
            var _this = this,
                count = 0;
            if( success ) {
                _.each(resp, function(file) {

                    var $itemFile = this.uploaderLogoHeaderFile.fineUploader('getItemByFileId', count);
                    this.itemLogoHeaderWasLoaded($itemFile, file);

                    count++;
                }, this);
            }
        },

        itemLogoHeaderWasLoaded: function ($itemFile, fileObj) {
            $itemFile.find('.preview-link').attr('href', fileObj.direccion_adjunto);
            // reload plugins function
            this.ready();

            $itemFile.find('select').addClass('qq-hide')
                .next('.select2-container').addClass('qq-hide');
        },

        onCompleteFetchFaviconFiles: function (resp, success) {
            this.uploaderFavIconFile = $('#favicon_meta_institucion');
            if (Array.isArray(resp)) {
                this.parameters.data = resp;
            } else {
                return false
            }
            var _this = this,
                count = 0;
            if( success ) {
                _.each(resp, function(file) {

                    var $itemFile = this.uploaderFavIconFile.fineUploader('getItemByFileId', count);
                    this.itemFaviconWasLoaded($itemFile, file);

                    count++;
                }, this);
            }
        },

        itemFaviconWasLoaded: function ($itemFile, fileObj) {
            $itemFile.find('.preview-link').attr('href', fileObj.direccion_adjunto);
            // reload plugins function
            this.ready();

            $itemFile.find('select').addClass('qq-hide')
                .next('.select2-container').addClass('qq-hide');
        },

        onCompleteFetchContentNetworkSocialsFiles: function (resp, success) {
            this.uploaderSocialImageFile = $('#social_image_meta_institucion');
            if (Array.isArray(resp)) {
                this.parameters.data = resp;
            } else {
                return false
            }
            var _this = this,
                count = 0;
            if( success ) {
                _.each(resp, function(file) {

                    var $itemFile = this.uploaderSocialImageFile.fineUploader('getItemByFileId', count);
                    this.itemNetworkSocialFilesWasLoaded($itemFile, file);

                    count++;
                }, this);
            }
        },


        onCompleteFetchImageModuleFiles: function (resp, success) {
            if (!_.isEmpty(resp)) {
                this.uploaderImageModule = $('#imagen_modulos_institucion_' + resp[0].codigo_modulo);
                if (Array.isArray(resp)) {
                    this.parameters.data = resp;
                } else {
                    return false
                }
                var _this = this,
                    count = 0;
                if( success ) {
                    _.each(resp, function(file) {

                        var $itemFile = this.uploaderImageModule.fineUploader('getItemByFileId', count);
                        this.itemImageModuleWasLoaded($itemFile, file);

                        count++;
                    }, this);
                }
            }
        },

        itemImageModuleWasLoaded: function ($itemFile, fileObj) {
            $itemFile.find('.preview-link').attr('href', fileObj.direccion_adjunto);
            // reload plugins function
            this.ready();

            $itemFile.find('select').addClass('qq-hide')
                .next('.select2-container').addClass('qq-hide');
        },


        itemNetworkSocialFilesWasLoaded: function ($itemFile, fileObj) {
            $itemFile.find('.preview-link').attr('href', fileObj.direccion_adjunto);
            // reload plugins function
            this.ready();

            $itemFile.find('select').addClass('qq-hide')
                .next('.select2-container').addClass('qq-hide');
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

            if( typeof window.initComponent.colorpickerTrigger == 'function' ){
                window.initComponent.colorpickerTrigger();
            }
            // input mask
            this.$(":input").inputmask();
        },

        /**
        * Load spinner on the request
        */
        loadSpinner: function () {
            window.Misc.setSpinner( this.$('#section-content') )
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

        /**
        * Load spinner on the request
        */
        loadSpinner: function () {
            window.Misc.setSpinner( this.$('#section-content') )
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
                    window.Misc.showAlertBox(_this.configAlert);
                    if(resp.redirect) {
                        window.setTimeout(function() {
                            window.Misc.redirect( app.AppRouter.navigate( Route.route('institucion.edit',{institucion: _this.model.get('codigo_institucion')}) ) )
                        }, 3000);
                    }
                }else {
                    window.Misc.showAlertBox(this.configAlert);
                    return false;
                }
            }
        },
    });
})(jQuery, this, this.document);
