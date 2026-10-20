/**
* Class ProgramDocumenterView extends of Backbone View
* @author TuProyecto || Desarrolladores : @xindykatalina | @krobing
* @link http://TuProyecto.com
*/

//Global App Backbone
app || (app = {});

(function ($, window, document, undefined) {

    app.ProgramDocumenterView = Backbone.View.extend({

        el: '#programs-content',
        template: _.template( ($('#program-documenter-tpl').html() || '') ),
        events: {
            'submit #form-documenter': 'onStore'
        },
        parameters: {
            dataFilter: {}
        },

        /**
        * Constructor Method
        */
        initialize : function(opts) {
            _.bindAll(this, 'beforeRender', 'render', 'afterRender', 'onReadyFile', 'onCompleteLoadFile');

            // extends parameters
            if( opts.parameters !== undefined && _.isObject(opts.parameters) )
                this.parameters = $.extend({}, this.parameters, opts.parameters);

            //Init Attribute
            this.categoryProductsList = new app.CategoryProductsList();
            this.resultEvaluationList = new app.ResultEvaluationList();
            this.resultEvaluationProgramList = new app.ResultEvaluationProgramList();

            var _this = this;
            this.render = _.wrap(this.render, function(render) {

                _this.trigger('beforeRender');

                render();

                _this.trigger('afterRender');

                return _this;
            });

            this.changes_in_form = [];

            // Events Listener
            this.listenTo( this.model, 'change:codigo_registro_fase_usuario', this.render );
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
            console.log('attributes', attributes);
            attributes.dataFilter = this.parameters['dataFilter'];
            this.$el.html( this.template(attributes) );

            // Initialize attributes
            this.$wrapperCategory = this.$('#categories-list-product');
            this.$wrapperResult = this.$('#result-list-evaluation');
            this.$wrapperResultProgram__504 = this.$('#result_list_program__504');
            this.$uploaderFile = this.$('#attach-file-program-documenter');

            // overwrite model id attribute
            if( this.model.changed.codigo_registro_fase_usuario !== undefined ) {
                // Overwrite id attribute of the model
                this.model.idAttribute = 'codigo_registro_fase_usuario';
                this.model.set({'codigo_registro_fase_usuario': this.model.get('codigo_registro_fase_usuario')}, {'silent':true});
            }

            // Initialize attributes
         	this.$formDocumenter = this.$('#form-documenter');

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
            this.renderUploaderFile();
        },

        /**
        * fires libraries js
        */
        ready: function () {

            var _this = this;

            // reload plugins
            if(typeof window.initComponent.select2Trigger == 'function' )
                window.initComponent.select2Trigger();

            if( typeof(window.initComponent.datepickerTrigger) == 'function')
                window.initComponent.datepickerTrigger();

            if( typeof(window.initComponent.autosizeTrigger) == 'function')
                window.initComponent.autosizeTrigger();
        },

        /**
        * reference to views
        */
        referenceViews: function () {

            // instance of Category product view
            this.categoryProductListView = new app.CategoryProductListView({
                el: this.$wrapperCategory,
                collection: this.categoryProductsList,
                parameters: {
                    //
                }
            });

            this.categoryProductListView.render();
            this.categoryProductsList.fetch({ data:{'register':this.model.get('codigo_registro_fase_usuario')}, reset:true });

            // instance of participant program view
            this.resultEvaluationListView = new app.ResultEvaluationListView({
                el: this.$wrapperResult,
                collection: this.resultEvaluationList,
                parameters: {
                    //
                }
            });

            this.resultEvaluationListView.render();
            this.resultEvaluationList.fetch({ data:{'register':this.model.get('codigo_registro_fase_usuario')}, reset:true });

            switch (this.model.get('codigo_convocatoria')) {
                case window._tp.getProgram['aldea__20']:
                    var attributes = {
                        codigo_convocatoria: this.model.get('codigo_convocatoria'),
                        register: this.model.get('codigo_registro_fase_usuario')
                    }

                    this.resultEvaluationProgramView = new app.ResultEvaluationProgramListView({
                        el: this.$wrapperResultProgram__504,
                        collection: this.resultEvaluationProgramList,
                        parameters: {
                            //
                        }
                    });

                    this.resultEvaluationProgramView.render();
                    this.resultEvaluationProgramList.fetch({ data: attributes, reset: true });

                break;
            }
        },

        /**
        * Load spinner on the request
        */
        loadSpinner: function ( target, xhr, opts ) {
            if( target instanceof Backbone.Model )
                window.Misc.setSpinner( this.$formDocumenter );
        },

        /**
        * response of the server
        */
        responseServer: function ( model, resp, opts ) {

            window.Misc.removeSpinner({
                'wrap': this.$formDocumenter,
                'direct': true
            });

            this.$formDocumenter.find(':submit').removeClass('disabled');
        },

        /**
        * Store
        */
        onStore: function (e) {
            e.preventDefault();
            this.$formDocumenter.find(':submit').addClass('disabled');
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
					endpoint: window.Misc.urlFull(Route.route('adjunto.documentador.index')),
					customHeaders: {
						'X-CSRF-TOKEN': $('meta[name="csrf-token"]').attr('content'),
						'X-XSRF-TOKEN': window.Misc.getCsrf().getCookie('XSRF-TOKEN')
					},
					params: {
						'_token': $('meta[name="csrf-token"]').attr('content'),
                        'codigo_registro_fase_usuario': this.model.get('codigo_registro_fase_usuario')
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
					allowedExtensions: ['pdf'],
					itemLimit: 0,
					stopOnFirstInvalidFile: false,
					sizeLimit: (8 * 1024) * 1024 // 8 MB = (8 * 1024) * 1024 bytes
				},
				// Events handlers
				callbacks: {
					onSubmitted: _this.onReadyFile,
					onComplete: _this.onCompleteLoadFile,
                    onStatusChange: function (id, oldStatus, newStatus) {

                        if(newStatus == qq.status['UPLOAD_SUCCESSFUL']) {

                            // $element.find('.qq-attachment-required').val(dataDeliverable.direccion_adjunto);
                            window.setTimeout(function () {
                                var $itemFile = _this.$uploaderFile.fineUploader('getItemByFileId', id);
                                _this.itemWasLoaded( $itemFile, {'direccion_adjunto': _this.model.get('adjunto_carta_notificacion').direccion_adjunto} );
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

            // add initial file
			if( !_.isEmpty(this.model.get('adjunto_carta_notificacion')) ) {
				this.$uploaderFile.fineUploader('addInitialFiles', [this.model.get('adjunto_carta_notificacion')]);
			}
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

			var $itemFile = this.$uploaderFile.fineUploader('getItemByFileId', id);
			this.$uploaderFile.fineUploader('setUuid', id, resp.uuid);
			this.itemWasLoaded($itemFile, resp);
		},

        /**
		* when item was loaded
        * @param $itemFile Element
        * @param fileObj Object
		*/
		itemWasLoaded: function ($itemFile, fileObj) {

			$itemFile.find('.preview-link').attr('href', fileObj.direccion_adjunto);
		}
   });

})(window.jQuery, window, document);
