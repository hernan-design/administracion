/**
* Class ArticleCreateEditView extends of Backbone View
* @author TuProyecto || Desarrollador : @dortegon
* @link http://TuProyecto.com
*/

//Global App Backbone
app || (app = {});

(function ($, window, document, undefined) {

    app.ArticleCreateEditView = Backbone.View.extend({
        el: '#articles-content',
        events: {
            'submit #form-article': 'addArticle',
            'click #twitter-tab': 'onRenderViewTabTwitter',
            'click #facebook-tab': 'onRenderViewTabFacebook'
        },
        parameters: {
            codigo_contenido: null
        },

        /**
        * Constructor Method
        */
        initialize : function(opts) {
            _.bindAll(this, 'beforeRender', 'render', 'afterRender');

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

            // Initialize attributes
            this.$formCreate = this.$('#form-article');
            this.$wrapperImgToCrop = this.$('#wrap-article-uploader-tocrop');

            if(this.model.id){
                this.referenceViews();
            }
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

            // input mask
            this.$(":input").inputmask();
        },

        /**
        * reference to views
        */
        referenceViews: function () {
            if( this.$('#section-channel-content').length > 0 ){
                // Render multiselect channel content
                this.channelContentView = new app.ChannelContentView({
                    el: this.$('#section-channel-content'),
                    parameters:{
                        id_contenido: this.model.get('codigo_contenido'),
                        tipo_contenido: window._tp.getModule['blog']
                    }
                });
            }

            if(this.model.id){
                this.articleSeoView = new app.ArticleSeoView({
                    model: this.model
                });
                this.articleSeoView.render();
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
                        window.Misc.redirect( app.AppRouter.navigate( Route.route('contenido.edit', {contenido: this.model.get('codigo_contenido')}) ) );
                    }
                }else {
                    window.Misc.showAlertBox(this.configAlert);
                }
            }
        },

        /*
         * Add article
        */
        addArticle: function (e) {
            e.preventDefault();

            var data = window.Misc.formToJson( e.target )
            this.model.save( data, {patch: true, wait: true} );
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
				formCrop: this.$('#form-article'),
				imgCrop: this.$('#img-article-tocrop'),
				previewImg: this.$('#article-preview-tocrop'),
				uploaderImg: this.$('#uploader-article-image'),
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
				extraDropzones: [this.$('#image-crop-article')],
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
								_this.model.set('imagen_contenido', resp.file_path);
                                _this.$('[name="imagen_contenido"]').val(resp.file_path);
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
		}
   });

})(window.jQuery, window, document);
