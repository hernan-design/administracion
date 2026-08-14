/**
* Class SocialSeoView extends of Backbone View
* @author TuProyecto || Desarrollador : @dropecamargo
* @link http://TuProyecto.com
*/

//Global App Backbone
app || (app = {});

(function ($, window, document, undefined) {

    app.SocialSeoView = Backbone.View.extend({
		template: _.template( ($('#seo-social-tpl').html() || '') ),
        events: {
            'submit #form-social-seo': 'addSocialSeo'
        },        
        parameters: {
			data: {},
			social: '',
            fieldimage: null
        },

        /**
        * Constructor Method
        */
        initialize : function(opts) {			
            // extends parameters
            if( opts.parameters !== undefined && _.isObject(opts.parameters) ){
                this.parameters = $.extend({}, this.parameters, opts.parameters);
			}
			
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
			var attributes = this.parameters.data;
			attributes.social = this.parameters.social;

			this.$el.html( this.template(attributes) );

			this.$wrapperImgToCrop = this.$('#wrap-uploader-tocrop');
			   
			setTimeout(function(){
                this.setImgUploaderCrop();
            }.bind(this), 200);
		},

        /*
         * Add course seo
        */
	   	addSocialSeo: function (e) {
            e.preventDefault();

            var data = window.Misc.formToJson( e.target );
            data.seo = true;

            this.model.save( data, {patch: true, wait: true} );
        },

        // ===============================================================
		//              start of image uploader crop facebook
		// ===============================================================

		/**
		* set image uploader crop
		*/
		setImgUploaderCrop: function () {            
			var _this = this;

			this.$wrapperImgToCrop.imageUploaderCrop({
                formCrop: this.$('#form-social-seo'),
                imgCrop: this.$('#img-social-seo-tocrop'),
                previewImg: this.$('#social-seo-preview-tocrop'),
                uploaderImg: this.$('#uploader-social-seo-image'),
				templateImgUploader: 'qq-img-uploader-crop-template',
                btnToCrop: this.$('#btn-to-crop-social-seo'),
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
				extraDropzones: [this.$('#image-crop-social-seo')],
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
								_this.model.set(_this.parameters.fieldimage, resp.file_path);
                                _this.$('[name="'+_this.parameters.fieldimage+'"]').val(resp.file_path);
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

})(jQuery, this, this.document);