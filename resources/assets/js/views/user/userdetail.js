/**
* Class UserDetailView extends of Backbone View
* @author TuProyecto || Desarrollador : @dortegon
* @link http://TuProyecto.com
*/

//Global App Backbone
app || (app = {});

(function ($, window, document, undefined) {

    app.UserDetailView = Backbone.View.extend({

        el: '#user-content',
        templateMultiSelect: _.template( ($('#multiselect-tpl').html() || '') ),
        templateMentoring: _.template( ($('#mentor-detail-tpl').html() || '') ),
        templateUpdateUser: _.template( ($('#update-user-tpl').html() || '') ),
        templateNavTabs: _.template( ($('#user-detail-nav-tab-tpl').html() || '') ),
        events: {
            'submit #form-mentor-profile': 'submitMentor',
            'submit #form-update-user': 'onUpdateUser',
            'change #my-select-rol':'switchMentorTab',
            'click #btn-mentor-terms':'sendMentorTerms',
            'change #forma_ayuda_mentoria':'onChangeField',
            'change #experiencia_usuario':'onChangeField',
            'change [id^=codigo_etapa_proyecto_]':'onChangeField',
            'change #codigo_especialidad_destaca':'onChangeField',
        },
        parameters: {
            userCode: ''
        },

        /**
        * Constructor Method
        */
        initialize : function(opts) {
            _.bindAll(this, 'beforeRender', 'render', 'afterRender', 'onReadyMentorFile', 'onCompleteLoadMentorFile', 'onCompleteFetchMentorFiles', 'onMentorFileChange');

            if( _.isObject(opts) && opts.parameters ) {
                this.parameters = _.extend({}, this.parameters, opts.parameters);
            }

            // Init Attributes
            this.configAlert = {
                'wrapper': this.$el,
                'closeTime': 6000,
                'speedOpen': 500,
                'speedClose': 500
            };

            this.mentorModel = new app.MentorModel();
            this.mentorModel.idAttribute = 'codigo_usuario';
            this.mentorModel.set( {codigo_usuario:this.parameters['userCode']}, {'silent': true} );
            this.mentorModel.fetch({'codigo_usuario':this.parameters['userCode']});

            // User Model

            this.userModel = new app.UserModel();

            this.changes_in_form = [];

            var _this = this;
            this.render = _.wrap(this.render, function(render) {

                _this.trigger('beforeRender');

                render();

                _this.trigger('afterRender');

                return _this;
            });

            // Events Listener
            this.listenTo( this.collection, 'reset', this.render );
            this.listenTo( this.mentorModel, 'sync', this.render );
            this.listenTo( this.mentorModel, 'request', this.loadSpinner );
            this.listenTo( this.mentorModel, 'sync', this.responseServer );
            this.listenTo( this.userModel, 'request', this.loadSpinnerUpdateUser );
            this.listenTo( this.userModel, 'sync', this.responseServer );

            this.on( 'beforeRender', this.beforeRender );
            this.on( 'afterRender', this.afterRender );
        },

        /*
        * Render View Element
        */
        render: function() {
            var attributes = {};
            attributes.codigo_usuario = this.parameters['userCode'];
            attributes.roles = this.collection.toJSON();
            attributes.owner = true;


            attributes.mentor = this.mentorModel.toJSON() ;


            this.$('#user-detail-nav-tab').html( this.templateNavTabs( attributes ) );

            this.$('#multiple-select').html( this.templateMultiSelect( attributes ) );

            this.$('#update-user').html( this.templateUpdateUser() );

            this.$('#mentoring-content').html( this.templateMentoring( attributes) );
            this.$('#mentoring-tab-list-item').hide();
            this.switchMentorTab();
            this.onShowActivitiesLog();
            this.$formCreate = this.$('#form-mentor-profile');

            this.ready();
        },

        /**
         * Fires before of run render function
         */
        beforeRender: function () {
            // body...
        },

        /**
         * Fires after of run render function
         */
        afterRender: function () {

            //trigger functions

            this.ready();
            this.renderUploaderMentorFile();
        },

        /**
         * Update users Data
         */

        onUpdateUser: function(e) {
            e.preventDefault();
            var errorcellPhone = document.getElementById('error__cellPhone');
            var errorPhone = document.getElementById('error__Phone');

            errorcellPhone.style.display = 'none';
            errorPhone.style.display = 'none';

            // this.loadSpinnerUpdateUser();

            this.userModel.set({ codigo_usuario: this.parameters['userCode'] });

            var cellphoneInput = document.getElementById('inp__Cellphone');
            var cellphoneValue = cellphoneInput.value;
            var errorCell = false;

            // Validar la longitud y formato del número de celular
            var cellphoneRegex = /^\d{10}$/;
            if (cellphoneValue != "" && !cellphoneRegex.test(cellphoneValue)) {
                errorcellPhone.innerText = "El número de celular debe tener 10 caracteres.";
                errorcellPhone.style.display = 'block';
                errorCell = true;
            }

            var phoneInput = document.getElementById('inp__Phone');
            var phoneValue = phoneInput.value;
            var errorPhones = false;

            // Validar la longitud del número de teléfono
            if (phoneValue != "" && (phoneValue.length < 7 || phoneValue.length > 10)) {
                errorPhone.innerText = "El número de teléfono debe tener entre 7 y 10 caracteres.";
                errorPhone.style.display = 'block';
                errorPhones = true;
            }

            if(errorCell || errorPhones){
                return;
            }


            var data = window.Misc.formToJson(e.target);

            // var response = this.userModel.save(data);

            this.userModel.save(data, {wait: true, patch: true,
                success: function (model, resp) {
                    if(resp.success){
                        setTimeout(function() {
                            location.reload();
                        }, 1000);
                    }
                }
            });


        },

        /**
         * Add new mentor
         */
        submitMentor: function (e) {
            e.preventDefault();
            var _this = this;
            this.loadSpinner()

            var data = window.Misc.formToJson(e.target);

             this.mentorModel.save(data, {wait: true, patch: true});

        },

        /**
         * Add new mentor
         */
        switchMentorTab: function (e) {

            var selection = $("#my-select-rol").val();

            if(_.contains(selection, '7') ){

                if (typeof e !== 'undefined') {
                    window.Misc.redirect(  window.Misc.urlFull( Route.route('usuario.show', {usuario: this.parameters['userCode'] }) ) );
                }

                this.$('#mentoring-tab-list-item').show();
                this.ready();

            }else{
                this.$('#mentoring-tab-list-item').hide();
                this.ready();
            }

        },

        /**
         * Add new mentor
         */
        sendMentorTerms: function (e) {
            this.loadSpinner();
            _this = this;

            var data = {};
            data['codigo_especialidad_destaca'] = this.mentorModel.get('codigo_especialidad_destaca');
            data['experiencia_usuario'] =  typeof this.mentorModel.get('experiencia_usuario') === 'string' ? this.mentorModel.get('experiencia_usuario').trim() : '';
            data['forma_ayuda_mentoria'] = typeof this.mentorModel.get('forma_ayuda_mentoria') === 'string' ? this.mentorModel.get('forma_ayuda_mentoria').trim() : '';
            data['stage_mentoring'] = this.mentorModel.get('stage_mentoring');

            //Make Ajax Call
            Backbone.ajax({
                dataType: "json",
                method:"GET",
                url:   window.Misc.urlFull(Route.route( 'mentoria.terminos.enviar',{usuario: this.parameters['userCode'] }) ),
                data: data,
                success: function(resp){
                    //code after success
                    if(resp.success !== undefined )
                    {
                        // response success or error
                        var text = resp.success ? resp.message : resp.errors2,
                            type = resp.success ? 'success' : 'alert';

                        if( _.isObject( resp.errors2 ) ){
                            var listError = '<ul>';
                            $.each(resp.errors2, function(field, item) {
                                listError += '<li>'+ item[0] +'</li>';
                            });
                            listError += '</ul>';
                            text = listError;
                        }

                        var config = {
                            'text': text,
                            'type': type
                        };
                        _.extend( _this.configAlert, config );


                        //complete proccess
                        window.Misc.removeSpinner({
                            'wrap': _this.$el,
                            'direct': false,
                            'callback': (function (config, resp) {
                                return function () {

                                    if(!_.isUndefined(resp.success)) {
                                        window.Misc.showAlertBox( _this.configAlert );

                                        if(resp.success){
                                            $('#btn-mentor-terms')
                                                .prop("disabled", true)
                                                .removeClass("btn-danger")
                                                .addClass("btn-primary");
                                        }
                                    }
                                }
                            })(_this.configAlert, resp)
                        });
                    }
                },
                complete:function(resp){

                    if(_.isUndefined(resp.success) ) {
                        window.Misc.removeSpinner({
                            'wrap': _this.$el,
                            'direct': true
                        });
                    }
                }

            });
        },

        /**
        * change text fields
        */
       onChangeField: function(e) {
        e.preventDefault();
        var attributes = this.mentorModel.toJSON();
        var val = this.mentorModel.has(e.target.name) ? this.mentorModel.get(e.target.name) : e.target.value;
       if( (typeof e.target.value !== 'undefined' && $.trim(val) !==  $.trim(e.target.value) ) || e.target.type === 'checkbox' ||  e.target.type === 'select-one' ){

            $('#btn-mentor-terms')
                .prop("disabled", true)
                .removeClass("btn-danger")
                .addClass("btn-primary")
                .prop('title', 'Debes guardar primero los cambios realizados');
       }

       return;
    },

        ready: function () {

            // trigger functions
            if( _.has($.fn, 'foundation') ){
                this.$el.foundation('abide');
            }

            // reload plugins
            if( _.has($.fn, 'chosen') && typeof window.initComponent.chosenTrigger == 'function' )
                window.initComponent.chosenTrigger();

            if( _.has($.fn, 'perfectScrollbar') && typeof window.initComponent.configScrollbar == 'function' )
                window.initComponent.configScrollbar();

            if( typeof window.initComponent.initConfigForm == 'function' )
                window.initComponent.initConfigForm();

            if(typeof window.initComponent.multiselectTrigger == 'function' ){
                window.initComponent.multiselectTrigger();
            }
        },


        /*
        * Remove View Element
        */
        remove: function(){

            var _this = this;
            this.$el.fadeOut('normal', function (){
                _this.$el.remove();
            });
        },

        /**
         * Load spinner on the request
         */
        loadSpinner: function ( target, xhr, opts ) {
            window.Misc.setSpinner( this.$el );
        },

        /**
         * Load spiner on the request update user
         */

        loadSpinnerUpdateUser: function( target, xhr, opts ) {
            window.Misc.setSpinner( this.$('#update-user') );
        },

        /**
         * response of the server
         */
        responseServer: function ( model, resp, opts ) {
            // response success or error
            var text = resp.success ? resp.message : resp.errors,
                type = resp.success ? 'success' : 'alert';

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
            };
            _.extend( this.configAlert, config );

            _this = this;
            //complete proccess
            window.Misc.removeSpinner({
                'wrap': this.$el,
                'direct': false,
                'callback': (function (conf, resp) {
                    return function () {
                        if(!_.isUndefined(resp.success)) {
                                window.Misc.showAlertBox( _this.configAlert );
                        }
                    }
                })(this.configAlert, resp)
            });
        },


        /**
         * render files uploader
         */
        renderUploaderMentorFile: function () {

            var _this = this;
            this.$uploaderMentorFile = $('#fnuploader-attach-file');
            this.$uploaderMentorFile.fineUploader({
                debug: false,
                template: 'qq-attachments-template',
                session: {
                    endpoint: window.Misc.urlFull(Route.route('adjunto.usuario.index')),
                    params: {
                        'codigo_usuario': _this.mentorModel.get('codigo_usuario')
                    },
                    refreshOnRequest: false
                },
                request: {
                    inputName: 'file',
                    endpoint: window.Misc.urlFull(Route.route('adjunto.usuario.index')),
                    customHeaders: {
                        'X-CSRF-TOKEN': $('meta[name="csrf-token"]').attr('content'),
                        'X-XSRF-TOKEN': window.Misc.getCsrf().getCookie('XSRF-TOKEN')
                    },
                    params: {
                        '_token': $('meta[name="csrf-token"]').attr('content'),
                        'codigo_usuario': _this.mentorModel.get('codigo_usuario'),
                        'codigo_documento': window._tp.docAttached['curriculum'],
                        'codigo_privacidad_contenido': window._tp.typePrivacy['private']
                    }
                },
                deleteFile: {
                    enabled: true,
                    endpoint: window.Misc.urlFull(Route.route('adjunto.usuario.index')),
                    customHeaders: {
                        'X-CSRF-TOKEN': $('meta[name="csrf-token"]').attr('content'),
                        'X-XSRF-TOKEN': window.Misc.getCsrf().getCookie('XSRF-TOKEN')
                    },
                    params: {
                        '_token': $('meta[name="csrf-token"]').attr('content'),
                        'codigo_usuario': _this.mentorModel.get('codigo_usuario')
                    },
                    forceConfirm: true,
                    confirmMessage: window._tp.confirmMessage
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
                    allowedExtensions: ['pdf', 'txt', 'doc', 'docx', 'xls', 'xlsx', 'pps', 'ppt', 'pptx', 'png', 'jpg', 'jpeg', 'zip'],
                    itemLimit: 0,
                    stopOnFirstInvalidFile: false,
                    sizeLimit: (12 * 1024) * 1024 // 50 kB = 50 * 1024 bytes
                },
                // Events handlers
                callbacks: {
                    onSubmitted: this.onReadyMentorFile,
                    onComplete: this.onCompleteLoadMentorFile,
                    onSessionRequestComplete: this.onCompleteFetchMentorFiles

                },
                dragAndDrop: {
                    extraDropzones: []
                },
                text: window._tp.text,
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
        onReadyMentorFile: function (id, name) {
            this.ready();
        },

        /**
         * complete upload of file
         * @param Number id
         * @param Strinf name
         * @param Object resp
         */
        onCompleteLoadMentorFile: function (id, name, resp) {
            var _this = this;

            var $itemFile = this.$uploaderMentorFile.fineUploader('getItemByFileId', id);
            this.$uploaderMentorFile.fineUploader('setUuid', id, resp.uuid);
            this.itemWasLoaded($itemFile, resp);
        },

        /**
         * on complete fetch files
         * @param Array resp | object array
         * @param Boolean succcess
         */
        onCompleteFetchMentorFiles: function (resp, success) {
            count = 0;
            if( _.has($.fn, 'chosen') && typeof window.initComponent.chosenTrigger == 'function' )

                window.initComponent.chosenTrigger();

            if( success ) {
                _.each(resp, function(file) {

                    var $itemFile = this.$uploaderMentorFile.fineUploader('getItemByFileId', count);

                    this.itemWasLoaded($itemFile, file);
                    count++;
                }, this);
            }
        },

        onMentorFileChange: function (id, oldStatus, newStatus) {

            if(newStatus == qq.status['UPLOAD_SUCCESSFUL']) {
                var _this = this;
                window.setTimeout(function () {
                    var $itemFile = _this.$uploaderFile.fineUploader('getItemByFileId', id);
                }, 500);
            }
        },

        /**
         *
         */
        itemWasLoaded: function ($itemFile, fileObj) {

            $itemFile.find('.preview-link').attr('href', fileObj.direccion_adjunto);
            // reload plugins function
            this.ready();

            $itemFile.find('select').addClass('qq-hide')
                .next('.select2-container').addClass('qq-hide');
        },

        // show activities log
        onShowActivitiesLog: function () {
            this.viewActivityLog = new app.ActivityLogView({
                parameters: {
                    'causer': this.parameters['userCode'],
                    'type': 'full'
                }
            });
            this.viewActivityLog.render();
        }
   });

})(jQuery, this, this.document);
