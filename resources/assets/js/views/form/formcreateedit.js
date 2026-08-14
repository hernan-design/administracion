/**
* Class FormCreateEditView extends of Backbone View
* @author TuProyecto || Desarrollador : @dortegon
* @link http://TuProyecto.com
*/

//Global App Backbone
app || (app = {});

(function ($, window, document, undefined) {

    app.FormCreateEditView = Backbone.View.extend({
        el: '#form-content',
        formVersionsTpl: _.template( $('#form-versions-tpl').html() || '' ),
        formPaginationTpl: _.template( $('#form-pagination-tpl').html() || '' ),
        templateActions: _.template( ( $('#actions-question-tpl').html()  || '' ) ),
        formPaginationTypeTpl: _.template( $('#form-questions-edit-tpl').html() || '' ),
        templatePage: _.template( $('#container-pages-tpl').html() || ''),
        events: {
            'click .round-button': 'onModalNewVersion',
            'submit #form-survey': 'addSurvey',
            'select2:select #codigo_version_encuesta': 'onChangeFormVersion',
            'click .add-group': 'onCreateGroup',
            'click .add-question' : 'onCreateQuestion',
            'click .add-page': 'onAddPages',
            'click .edit-group': 'onEditGroup',
            'click .remove-group': 'onDeleteGroup',
        },
        parameters: {
            pages: [],
            action: false,
        },

        /**
        * Constructor Method
        */
        initialize: function(opts){
            _.bindAll(this, 'beforeRender', 'render', 'afterRender');

            // extends parameters
            if( opts.parameters !== undefined && _.isObject(opts.parameters) ) {
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

            // reference to elements
            this.$contentx = this.$('.content-x');
            this.$formCreate = this.$('#form-survey');
            this.$pagination = this.$('#pagination');
            this.$paginationForm = this.$('#pagination-form');
            this.$versionContent = this.$('#form-versions-content');
            this.$wrapperImgToCrop = this.$('#wrap-article-uploader-tocrop');

            // Intance new model
            this.formVersionModel = "";
            this.formQuestionGroupModel = new app.FormQuestionGroupModel();
            this.PageModel = Backbone.Model.extend({ idAttribute: 'pagina_grupo_pregunta' });
            this.formQuestion = new app.FormQuestionModel();
            this.formPages = new app.PagesModel();

            //Instance new collection
            this.formQuestionsList = new app.FormQuestionsList();
            this.formQuestionGroup = new app.FormQuestionGroupsList();
            this.pageList = new app.PageList();
            this.formQuestionList = new app.FormQuestionsList();

            // Events Listener
            this.listenTo( this.model, 'change:codigo_encuesta', this.referenceViews );
            this.listenTo( this.model, 'request', this.loadSpinner );
            this.listenTo( this.model, 'sync', this.responseServer );
            this.listenTo( this.formVersionModel, "change", this.onChangeVersion );
            // this.listenTo( this.formQuestionGroup, "sync", this.referenceViews );
            // this.listenTo( this.formQuestionsList, 'change', this.onRenderListGroup );

            this.on( 'edit', this.onViewModal );
            this.on( 'create', this.onAddGroup );

            // Events listener custom
            this.on( 'beforeRender', this.beforeRender );
            this.on( 'afterRender', this.afterRender );
        },

        /*
        * Render View Element
        */
        render: function() {
            //
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
            var _this = this;

            if( typeof window.initComponent.colorpickerTrigger == 'function' ){
                window.initComponent.colorpickerTrigger();
            }

            if( typeof window.initComponent.icheckTrigger == 'function' ){
                window.initComponent.icheckTrigger();
            }

            if(typeof window.initComponent.select2Trigger == 'function' ){
                window.initComponent.select2Trigger();
            }

            // input mask
            this.$(":input").inputmask();
        },

        referenceElements: function () {
            this.$formQuestions = this.$('#form-questions');
            this.$wrapperPages = this.$('#content-pages');
            this.$wrapperQuestions = this.$('#content-questions');
        },

        // Render new version
        onChangeVersion: function (version) {
            var data = {
                change_version: true,
                formulario: version.get('codigo_encuesta'),
                codigo_version_encuesta: version.get('codigo_version_encuesta')
            };

            this.formQuestionsList.remove(this.model.get('preguntas'));
            this.model.save(data, {wait: true, patch: true});
        },

        /**
        * reference to views
        */
        referenceViews: function (model) {
            var attributes = model.toJSON();

            this.$versionContent.html( this.formVersionsTpl(attributes) );

            // Pagination

            if (this.parameters.action == false) {
                this.onPagination(model);
            } else {
                this.onPaginationCreate(model);
            }

            this.ready();
        },

        // On Pagination without Create

        onPagination: function(model) {
            var attributes = model.toJSON();

            this.formQuestionGroup.set(this.model.get('agrupaciones'));
            this.formQuestionsList.set(this.model.get('preguntas'));

            var view = new app.FormPagesList({
              el: '#pagination',
              model: model,
              collection: this.pageList,
              parameters: {
                data: attributes
              }
            });

            this.pageList.reset(attributes);

            view.render();
        },

        // On Pagination Create

        onPaginationCreate: function(model) {
            var attributes = model.toJSON();

            var view = new app.FormPagesList({
                el: '#pagination',
                model: model,
                collection: this.pageList,
                parameters: {
                    data: attributes
                }
            });

            view.render();
        },

        // Select version
        onChangeFormVersion: function (e) {
            e.preventDefault();

            this.onModalChangeVersion({
                change_version: true,
                formulario: this.model.get('codigo_encuesta'),
                codigo_version_encuesta: $(e.target).find('option:selected').val()
            });
        },


        /**
        * Load spinner on the request
        */
        loadSpinner: function () {
            window.Misc.setSpinner( this.$contentx );
        },

        /**
        * response of the server
        */
        responseServer: function ( model, resp, opts ) {

            window.Misc.removeSpinner({
                wrap: this.$contentx,
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
                    this.referenceViews(model);
                    if(resp.redirect){
                       window.Misc.redirect( app.AppRouter.navigate( Route.route('formulario.edit',{formulario: this.model.get('codigo_encuesta')}) ) );
                    }
                }else {
                    window.Misc.showAlertBox(this.configAlert);
                    return false;
                }
            }
        },

        /*
         * Add survey
        */
        addSurvey: function (e) {
            e.preventDefault();

            var data = window.Misc.formToJson( e.target );
            this.parameters.action = true;

            this.model.save( data, {wait: true} );
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
				formCrop: this.$('#form-survey'),
				imgCrop: this.$('#img-form-tocrop'),
				previewImg: this.$('#form-preview-tocrop'),
				uploaderImg: this.$('#uploader-form-image'),
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
				cropping: {
					endpoint: document.url + Route.route('file.crop'),
					params: {}
				},
				extraDropzones: [this.$('#image-crop-form')],
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
								_this.model.set('imagen_encuesta', resp.file_path);
                                _this.$('[name="imagen_encuesta"]').val(resp.file_path);
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

        // Open modal new version
        onModalNewVersion: function (e) {
            e.preventDefault();
            e.stopPropagation();

            this.modalFormVersion = new app.FormSampleWindow({
                model: this.formVersionModel,
                parameters: {
                    title: 'Agregar versión',
                    codigo_encuesta: this.model.get('codigo_encuesta'),
                    template: _.template( ($('#form-modal-tpl').html() || '') )
                }
            });

            this.modalFormVersion.render();
        },

        // Open modal change version
        onModalChangeVersion: function (data) {
            this.modalChangeVersion = new app.ConfirmWindow({
                model: this.model,
                parameters: {
                    titleConfirm: "Confirmación",
                    codigo_encuesta: this.model.get('codigo_encuesta'),
                    template: _.template( ($('#form-modal-change-version-tpl').html() || '') ),
                    onConfirm: function () {
                        this.formQuestionsList.remove(this.model.get('preguntas'));
                        this.model.save(data, {wait: true, patch: true});
                    }.bind(this)
                }
            });

            this.modalChangeVersion.render();
        },

        // onAddGroupNew: function (e) {
        //     e.preventDefault();
        //
        //     this.onAddGroup(this.formQuestionGroupModel);
        // },

        // Open modal view add group
        // onAddGroup: function (model, collection, data) {
        onAddGroup: function (e) {
            e.preventDefault();
            // this.formQuestionGroup.add(model);

            this.modalFormGroup = new app.FormWindow({
                model: this.formQuestionGroupModel,
                collection: this.formQuestionGroup,
                parameters: {
                    title: 'Agregar grupo',
                    template: _.template( ($('#form-modal-group-tpl').html() || '') ),
                    data: {
                        codigo_encuesta: this.model.get('codigo_encuesta'),
                        pagina_grupo_pregunta: $(e.currentTarget).attr('data-page'),
                        codigo_version_encuesta: $('#codigo_version_encuesta').find('option:selected').val()
                    },
                }
            });

            this.modalFormGroup.render();
        },

        // Add question
        onAddQuestion: function (e) {
            e.preventDefault();
            e.stopPropagation();

            // Intance new model
            this.formQuestionModel = new app.FormQuestionModel();
            this.onViewModal(this.formQuestionModel, {codigo_pregunta_encuesta: $(e.currentTarget).attr('data-question'), codigo_grupo_pregunta_encuesta: $(e.currentTarget).attr('data-group')}, this.parameters.view);
        },

        // View modal by questions
        onViewModal: function (model, data) {
            var codigo_pregunta_encuesta = '',
                codigo_grupo_pregunta_encuesta = model.get('codigo_grupo_pregunta_encuesta');

            if(!_.isUndefined(data)){
                codigo_pregunta_encuesta = data.codigo_pregunta_encuesta;
                codigo_grupo_pregunta_encuesta = data.codigo_grupo_pregunta_encuesta;
            }

            if ( this.modalQuestionForm instanceof Backbone.View ){
                this.modalQuestionForm.stopListening();
                this.modalQuestionForm.undelegateEvents();
            }

            this.modalQuestionForm = new app.QuestionFormWindow({
                model: model,
                collection: this.formQuestionsList,
                parameters: {
                    data: {
                        codigo_encuesta: this.model.get('codigo_encuesta'),
                        codigo_pregunta_encuesta: codigo_pregunta_encuesta,
                        codigo_grupo_pregunta_encuesta: codigo_grupo_pregunta_encuesta,
                        codigo_version_encuesta: $('#codigo_version_encuesta').find('option:selected').val(),
                    },
                    view: this,
                }
            });
            console.log('aqwi' + this.modalQuestionForm);
            this.modalQuestionForm.render();
        },

        onCreateGroup: function (e) {
            e.preventDefault();

            if ( this.modalFormGroup instanceof Backbone.View ){
                this.modalFormGroup.stopListening();
                this.modalFormGroup.undelegateEvents();
            }

            this.formQuestionGroupModel.clear().set(this.formQuestionGroupModel.defaults);
            this.modalFormGroup = new app.FormWindow({
                model: this.formQuestionGroupModel,
                collection: this.formQuestionGroup,
                parameters: {
                    title: 'Agregar grupo',
                    template: _.template( ($('#form-modal-group-tpl').html() || '') ),
                    data: {
                        codigo_encuesta: this.model.get('codigo_encuesta'),
                        pagina_grupo_pregunta: $(e.currentTarget).attr('data-page'),
                        num_page: $(e.currentTarget).attr('data-numpage'),
                        codigo_version_encuesta: $('#codigo_version_encuesta').find('option:selected').val(),
                        update: false,
                    },
                    page: $(e.target).data('page'),
                }
            });

            this.modalFormGroup.render();
        },

        onCreateQuestion: function(e) {
            e.preventDefault();

            if ( this.modalQuestionForm instanceof Backbone.View ){
                this.modalQuestionForm.stopListening();
                this.modalQuestionForm.undelegateEvents();
              }

            this.modalQuestionForm = new app.QuestionFormWindow({
                model: this.formQuestion,
                collection: this.formQuestionsList,
                parameters: {
                    group: $(e.currentTarget).data('group'),
                    page: $(e.currentTarget).data('page'),
                    version: $('#codigo_version_encuesta').find('option:selected').val(),
                    encuesta: this.model.get('codigo_encuesta'),
                },
           });
           console.log(this.modalQuestionForm);
            this.modalQuestionForm.render();
        },

        onAddPages: function(e) {
            e.preventDefault();

            var attributes = this.model.toJSON(),
                _this = this;

            var page = 0;
            if (!this.parameters.pages.length) {
                if (attributes.paginas.length) {
                    for (var i = 1; i <= attributes.paginas.length; i++) {
                        _this.parameters.pages.push(1);
                    }
                    this.parameters.pages.push(1);
                } else {
                    this.parameters.pages.push(1);
                }
                page = this.parameters.pages.length;
            } else {
                this.parameters.pages.push(1);
                page = this.parameters.pages.length;
            }

            this.formPages.clear().set(this.formPages.defaults);
            this.formPages.save( {codigo_encuesta: attributes.codigo_encuesta, page: page}, {wait: true, success: function(model) {
                var view = new app.FormPagesList({
                    el: '#pagination',
                    model: model,
                    parameters: {
                        page: page,
                        data: {
                            codigo_encuesta: attributes.codigo_encuesta,
                        }
                    }
                });

                view.stopListening();
                view.onAddPages();
             }
            });
        },

        onEditGroup: function(e) {
            e.preventDefault();

            var group = $(e.currentTarget).data('group');

            this.formQuestionGroup.fetch({ data: { grupo: group }, success: function(model) {

                   var modelJSON = model.models[0].toJSON();

                    if ( this.modalFormGroup instanceof Backbone.View ){
                        this.modalFormGroup.stopListening();
                        this.modalFormGroup.undelegateEvents();
                    }

                    this.modalFormGroup = new app.FormWindow({
                        model: model.models[0],
                        parameters: {
                            title: 'Actualizar grupo',
                            template: _.template( ($('#form-modal-group-tpl').html() || '') ),
                            data: {
                                codigo_version_encuesta: $('#codigo_version_encuesta').find('option:selected').val(),
                                group: group,
                                codigo_encuesta: modelJSON.codigo_encuesta,
                                update: true,
                            },
                        }
                    });

                    this.modalFormGroup.render();
                }
            });
        },

        onDeleteGroup: function(e) {
		     e.preventDefault();

		     var _this = this, group = $(e.currentTarget).data('group');

            if ( this.modalFormGroup instanceof Backbone.View ){
                this.modalFormGroup.stopListening();
                this.modalFormGroup.undelegateEvents();
            }

            this.modalFormGroup = new app.FormWindow({
                model: this.formQuestionGroupModel,
                parameters: {
                    title: 'Eliminar grupo',
                    template: _.template( ($('#confirmation-remove-group-tpl').html() || '') ),
                    data: {
                        codigo_version_encuesta: $('#codigo_version_encuesta').find('option:selected').val(),
                        group: group,
                        codigo_encuesta: _this.model.get('codigo_encuesta'),
                        remove: true,
                    },
                }
            });

            this.modalFormGroup.render();
        },
    });
})(jQuery, this, this.document);
