/**
* Class ProjectCreateEditView extends of Backbone View
* @author TuProyecto || Desarrollador : @dortegon
* @link http://TuProyecto.com
*/

//Global App Backbone
app || (app = {});

(function ($, window, document, undefined) {

    app.ProjectCreateEditView = Backbone.View.extend({
        el: '#entity-content',
        templateTypeIdentification: _.template( ($('#tipo-identificacion-tpl').html() || '') ),
        templateNumberIdentification: _.template( ($('#numero-identificacion-tpl').html() || '') ),
        templateUserSearch: _.template( ($('#persona-encargada-tpl').html() || '') ),
        events: {
            'change #codigo_clase_empre_insti': 'onFields',
            'submit #form-project': 'addProject'
        },
        parameters: {
            'codigo_entidad': null,
            'entidad':null
        },

        /**
        * Constructor Method
        */
        initialize: function(opts){
            _.bindAll(this, 'beforeRender', 'render', 'afterRender');

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

            if(this.parameters.entidad){
                this.fieldsShow( $('option:selected', this.$el.find('#codigo_clase_empre_insti')).attr('data-clase_emprendimiento')   );
                this.userSearchShow(true);
            }
            
            var _this = this;
            this.render = _.wrap(this.render, function(render) {

                _this.trigger('beforeRender');

                render();

                _this.trigger('afterRender');

                return _this;
            });

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

            // Initialize attributes
            this.$wrapperImgToCrop = this.$('#wrap-project-uploader-tocrop');
            this.$formCreate = this.$('#form-project');

            this.ready();

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

            if( typeof window.initComponent.icheckTrigger == 'function' ){
                window.initComponent.icheckTrigger();
            }

            // input mask
            this.$(":input").inputmask();
        },

        /**
        * Load spinner on the request
        */
        loadSpinner: function () {
            window.Misc.setSpinner( this.$('#section-content') );
        },

        /**
        * Add new project
        */
        addProject: function (e) {
            e.preventDefault();

            var data = window.Misc.formToJson( e.target );
           this.model.save( data, {wait: true, patch:true} );
        },

        /**
         * Add user
         */
        addUser: function (e) {
            e.preventDefault();

        },

        // Event change field
        onFields: function (e) {
            e.preventDefault();
            this.fieldsShow($('option:selected', e.target).attr('data-clase_emprendimiento'));
        },

        // Render section identification type and number
        fieldsShow: function (param) {

            var typeIdentification= this.$el.find('#tipo-identificacion');
            var numberIdentification= this.$el.find('#numero-identificacion');

            if( param == window._tp.getClassEntrepreneurship['company'] ){
                typeIdentification.html( this.templateTypeIdentification );
                numberIdentification.html( this.templateNumberIdentification );

            }else{
                $('#tipo-identificacion-section').remove();
                $('#numero-identificacion-section').remove();
            }

        },

        // Render section person
        userSearchShow: function (param) {
            var userSearch= this.$el.find('#persona-encargada');
            if( param){
                userSearch.html( this.templateUserSearch);

            }else{
                $('#users-search-toggle').remove();
            }

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
                    _
                    if(resp.redirect){
                        window.Misc.redirect( app.AppRouter.navigate( Route.route('entidad.edit',{entidad: resp.url_entidad}) ) );
                    }
                }else {
                    window.Misc.showAlertBox(this.configAlert);
                    this.render();
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
				formCrop: this.$('#form-project'),
				imgCrop: this.$('#img-project-tocrop'),
				previewImg: this.$('#project-preview-tocrop'),
				uploaderImg: this.$('#uploader-project-image'),
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
				extraDropzones: [this.$('#image-crop-project')],
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
                                _this.$('[name="imagen_usuario"]').val(resp.file_path);
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
                        console.log(error);
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
