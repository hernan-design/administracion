/**
* Class CourseContentWindow extends of Backbone View
* @author TuProyecto || Desarrollador : @dortegon
* @link http://TuProyecto.com
*/

//Global App Backbone
app || (app = {});

(function ($, window, document, undefined) {

    app.CourseContentWindow = Backbone.View.extend({

        el: '#modal-form',
        events: {
            'submit #form-modal-all': 'onSubmit',
            'click .cancel-action': 'onModalDestroy',
            'change .data-option': 'onChangeTypeCourseContent',
            'change .type-attachment': 'onChangeTypeAttachment'
		},
        parameters: {
            template: _.template( ($('#form-modal-tpl').html() || '') ),
            dataFilter: { },
            data: { },
        },

        /**
        * Constructor Method
        */
        initialize : function(opts) {
            _.bindAll(this, 'render', 'onReadyFile', 'onCompleteLoadFile');

            // extends attributtes
            if( opts !== undefined && _.isObject(opts.parameters) )
                this.parameters = _.extend({}, this.parameters, opts.parameters );


            var _this = this;
            this.render = _.wrap(this.render, function(render) {

                _this.trigger('beforeRender');

                render();

                _this.trigger('afterRender');

                return _this;
            });

            // Init attributes
            this.$formModal = this.$('#form-modal-all');

            // Init Attributes
            this.configAlert = {
                'wrapper': '#main-wrap',
                'closeTime': 6000,
                'speedOpen': 500,
                'speedClose': 500
            };
        },

        /*
        * Render View Element
        */
        render: function() {
            this.setElement(this.el);

            var attributes = {};

            // extend attributes confirm window
            if( this.model instanceof Backbone.Model ) _.extend(attributes, this.model);

            // Change modal text and content
            this.$el.find('.content-modal').html(this.parameters.template(this.model.toJSON()));

            if( !_.isUndefined(this.model.id) ){
                this.optionTypeCourseContent( this.model.get('tipo_contenido_curso') );
                this.optionTypeAttachment( this.model.get('codigo_tipo_adjunto') );
            }

            this.$el.modal("show");

            return this;
        },

        /**
        * fires libraries js
        */
        ready: function () {
            // reload plugins
            if( typeof(window.initComponent.autosizeTrigger) == 'function')
                window.initComponent.autosizeTrigger();

            if( typeof window.initComponent.select2Trigger == 'function' ){
                window.initComponent.select2Trigger();
            }
            
            if( typeof window.initComponent.icheckTrigger == 'function' ){
                window.initComponent.icheckTrigger();
            }

            if(typeof window.initComponent.multiselectTrigger == 'function' ){
                window.initComponent.multiselectTrigger();
            }
        },

        /*
        * Value selected data option
        */
        optionTypeCourseContent: function (param) {

            var dataOption = this.$el.find('#course-content-type');

            if(param == window._tp.getTypeCourseContent['attachment']){
                var templateAttachment = _.template( ($('#attachment-type-tpl').html() || '') );
                dataOption.html( templateAttachment(this.model.toJSON()) );
            }

            if(param == window._tp.getTypeCourseContent['evaluation']){
                var templateEvaluation = _.template( ($('#evaluation-tpl').html() || '') );
                dataOption.html( templateEvaluation(this.model.toJSON()) );
            }

            if(param == window._tp.getTypeCourseContent['homework']){
                var templateHomework = _.template( ($('#homework-tpl').html() || '') );
                dataOption.html( templateAttachmentFile(this.model.toJSON()) );
            }

            if( !_.isUndefined(this.model.id) ){
                this.$el.find('#tipo_contenido_curso').attr("disabled", true);
                this.optionTypeAttachment( this.model.get('codigo_tipo_adjunto') );
                this.$el.find('input[name=descripcion_contenido_curso]').val( this.model.get('descripcion_contenido_curso') );
            }
        },

        /*
        * Value selected data option event
        */
        onChangeTypeCourseContent: function (e) {
            e.preventDefault();
            var optionChange = $(e.target).find('option:selected').val();
            this.optionTypeCourseContent(optionChange);
        },

        /*
        * Value selected attachment type
        */
        optionTypeAttachment: function (param) {
            var attachmentType = this.$el.find('#attachment-type');
      
            if(param == window._tp.getTypeAttachment['youtube'] || param == window._tp.getTypeAttachment['vimeo']){
                var templateAttachmentVideo = _.template( ($('#attachment-type-video-tpl').html() || '') );
                attachmentType.html( templateAttachmentVideo(this.model.toJSON()) );
            }

            if(param == window._tp.getTypeAttachment['link'] || param == window._tp.getTypeAttachment['html']){
                var templateAttachmentVideo = _.template( ($('#attachment-type-article-tpl').html() || '') );
                attachmentType.html( templateAttachmentVideo(this.model.toJSON()) );
            }

            if(param == window._tp.getTypeAttachment['survey']){
                var templateAttachmentVideo = _.template( ($('#attachment-type-survey-tpl').html() || '') );
                attachmentType.html( templateAttachmentVideo(this.model.toJSON()) );
                this.ready();
            }

            if(param == window._tp.getTypeAttachment['pdf']){
                var templateAttachmentFile = _.template( ($('#attachment-type-file-tpl').html() || '') );
                attachmentType.html( templateAttachmentFile(this.model.toJSON()) );
                this.$uploaderFile = this.$('#attachment-course-themes');

                // render uploader plugin
                this.renderUploaderFile();
            }
        },

        /*
        * Value selected attachment type event
        */
        onChangeTypeAttachment: function (e) {
            e.preventDefault();

            var optionChange = $(e.target).find('option:selected').val();
            this.optionTypeAttachment(optionChange);
        },


        // ==============================================
		//           start attachment uploader
		// ==============================================
		/**
		* render files uploader
		*/
		renderUploaderFile: function () {
			var _this = this;

			this.$uploaderFile.fineUploader({
				debug: false,
				template: 'qq-attachments-template',
				request: {
					inputName: 'file',
					endpoint: window.Misc.urlFull(Route.route('file.course')),
					customHeaders: {
						'X-CSRF-TOKEN': $('meta[name="csrf-token"]').attr('content'),
						'X-XSRF-TOKEN': window.Misc.getCsrf().getCookie('XSRF-TOKEN')
					},
					params: {
						'_token': $('meta[name="csrf-token"]').attr('content'),
                        'codigo_curso':this.parameters.data.codigo_curso,
                        'codigo_tema': this.parameters.data.codigo_tema_curso,
                        'parameters':  this.parameters
                    }
				},
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
					onSubmitted: _this.onReadyFile,
					onComplete: this.onCompleteLoadFile,
                    onStatusChange: function (id, oldStatus, newStatus) {

                        if(newStatus == qq.status['UPLOAD_SUCCESSFUL']) {

                            window.setTimeout(function () {
                                var $itemFile = _this.$uploaderFile.fineUploader('getItemByFileId', id);
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

        /**
        * when the file is put over loader
        * @param Number id
        * @param String name
        */
        onReadyFile: function (id, name) {
        	this.ready();
        },

        /**
		* complete upload of file
		* @param Number id
		* @param Strinf name
		* @param Object resp
		*/
		onCompleteLoadFile: function (id, name, resp) {
            $('#direccion_adjunto').val(resp.file_path);
            this.$('[name="direccion_adjunto"]').val(resp.file_path);
		},

        /**
		* when item was loaded
        * @param $itemFile Element
        * @param fileObj Object
		*/
		itemWasLoaded: function ($itemFile, fileObj) {

			$itemFile.find('.preview-link').attr('href', fileObj.direccion_adjunto);
		},

        /*
        * Submit data
        */
        onSubmit: function (e) {
            e.preventDefault();

            var _this = this,
                data = window.Misc.formToJson( this.$formModal );
                 _.extend(data, this.parameters.data);
            this.model.save(data, {wait: true,
                success: function (model, resp) {
                    // complete proccess
                    window.Misc.removeSpinner({
                        'wrap': _this.$el,
                        'direct': false
                    });

                    _this.$el.modal().off();
                    _this.$el.modal('hide');

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

                    var config = {
                        'text': text,
                        'type': type
                    }
                    confCrop =  $.extend({}, _this.configAlert, config);

                    //complete proccess
                    if( resp.success !== undefined ){
                        if( resp.success ){
                            window.Misc.showAlertBox( confCrop );

                        }else{
                            window.Misc.showAlertBox( confCrop );
                        }
                    }
                }
            });
        },

        onModalDestroy: function (e){
            this.$el.modal().off();
        }
   });

})(jQuery, this, this.document);
