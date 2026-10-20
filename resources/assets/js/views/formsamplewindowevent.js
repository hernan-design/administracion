/**
* Class FormSampleWindowEvent extends of Backbone View
* @author TuProyecto || Desarrollador : @dortegon
* @link http://TuProyecto.com
*/

//Global App Backbone
app || (app = {});

(function ($, window, document, undefined) {

    app.FormSampleWindowEvent = Backbone.View.extend({
        el: '#modal-form',
        events: {
            'submit #form-modal-all': 'onSubmit',
            'click .cancel-action': 'onModalDestroy',
            'change #imagen_patrocinador': 'onImageChange'
		},
        parameters: {
            data: {},
            title: '',
            edit: false,
            template: '',
            dataFilter: {}
        },

        /**
        * Constructor Method
        */
        initialize : function(opts) {

            // extends attributtes
            if( opts !== undefined && _.isObject(opts.parameters) ){
                this.parameters = _.extend({}, this.parameters, opts.parameters );
            }

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
            var attributes = this.model.toJSON();

            this.$el.find('.modal-title').html(this.parameters.title);
            this.$el.find('.content-modal').html(this.parameters.template( attributes ));
            this.$el.modal("show").removeAttr('tabindex');

            return this;
        },

        /**
        * fires libraries js
        */
        ready: function () {
            // reload plugins
            if( typeof(window.initComponent.autosizeTrigger) == 'function'){
                window.initComponent.autosizeTrigger();
            }
        },

        onImageChange: function (e){
            e.preventDefault();
                var fileInput = e.target;
                var file = fileInput.files[0];
                if (file) {
                    var fileType = file.type;
                    var acceptedTypes = ["image/png", "image/jpeg"];
                    if (!acceptedTypes.includes(fileType)) {
                        alert("El formato de la imagen debe ser png o jpg");
                        fileInput.value = "";

                    }
                }
        },

        /*
        * Submit data
        */
        onSubmit: function (e) {
            e.preventDefault();
            window.Misc.setSpinner( this.$el );

            var _this = this,
                data = window.Misc.formToJson( this.$formModal );

            _.extend(data, this.parameters);

            var fileInput = $('#imagen_patrocinador')[0];
            var imagenPatrocinador = fileInput.files[0];

            var formData = new FormData();
                formData.append('nombre_patrocinador', $('#nombre_patrocinador').val());
                formData.append('descripcion_patrocinador', $('#descripcion_patrocinador').val());
                formData.append('imagen_patrocinador', imagenPatrocinador);
                formData.append('codigo_curso', $('#codigo_curso').val());

            this.model.save(null, {
                data: formData,
                processData: false,
                contentType: false,
                wait: true,
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

})(window.jQuery, window, document);
