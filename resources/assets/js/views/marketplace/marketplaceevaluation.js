/**
* Class AgreementView extends of Backbone View
* @author TuProyecto || Desarrolladora : @xindykatalina
* @link http://TuProyecto.com
*/

//Global App Backbone
app || (app = {});

(function ($, window, document, undefined) {

    app.MarketplaceEvaluationView = Backbone.View.extend({

        el: '#marketplaces-evaluation-content',
        template: _.template( ($('#marketplace-evaluation-tpl').html() || '') ),
        templateButtons: _.template( ($('#button-action-deliverables-tpl').html() || '') ),
        events: {
            'submit #form-create-eval-purchase': 'onStore',
            'submit #form-bill': 'onStoreBill',
            'click .btn-approve': 'onApprove',
            'click .btn-refused': 'onRefused',
            'submit #form-entregables': 'onDeliverables',
        },

        /**
        * Constructor Method
        */
        initialize : function(opts) {
            _.bindAll(this, 'render', 'onReadyFile', 'onCompleteFetchFiles', 'onDeleteFile');

            //Init Attribute
            this.configAlert = {
                'wrapper': '#main-wrap',
                'closeTime': 6000,
                'speedOpen': 500,
                'speedClose': 500
            };

            // Init Attribute
            this.$wrapperForm = this.$('#wrapper-form-evaluation');
            this.$wrapperAction = this.$('#wrapper-action-deliverables');
            this.$wrapperSaleAgreementDeliverables = this.$('#wrapper-sale-agreement-deliverables');
            this.$commentaryRefuse = this.$('#comentario_rechazo');
            this.$inputBill = this.$('#bill_req');
            this.deliverablesModel = new app.PurchaseModel();
            this.deliverablesModel.set({'codigo_compra': this.model.get('codigo_compra')}, {'silent': true});
            this.deliverablesModel.fetch();

            // Events Listener
            this.listenTo( this.model, 'change:itemsevaluacion', this.render );
            this.listenTo( this.model, 'request', this.loadSpinner );
            this.listenTo( this.model, 'sync', this.responseServer );
            this.listenTo( this.deliverablesModel, 'change:codigo_estado', this.deliverablesButtons );
        },

        /*
        * Render View Element
        */
        render: function() {
            var attributes = this.model.toJSON();

            this.$wrapperForm.html( this.template(attributes) );
            this.deliverablesButtons();
        },

        ready: function () {
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
            // input mask
            this.$(":input").inputmask();
        },



        /**
         * Uploader Bill @backend01
         */

        renderUploaderFileBill: function () {
            var _this = this;

            this.uploaderFile = $('#wrapper-uploader-agreement');

            this.uploaderFile.fineUploader({
                debug: false,
                template: 'qq-attachments-template',
                session: {
                    endpoint: window.Misc.urlFull(Route.route('marketplace.transacciones.factura')),
                    params: {
                        'codigo_compra': _this.model.get('codigo_compra'),
                    },
                    refreshOnRequest: false
                },
                request: {
                    inputName: 'file',
                    endpoint: window.Misc.urlFull(Route.route('marketplace.transacciones.facturauploader')),
                    customHeaders: {
                        'X-CSRF-TOKEN': $('meta[name="csrf-token"]').attr('content'),
                        'X-XSRF-TOKEN': window.Misc.getCsrf().getCookie('XSRF-TOKEN')
                    },
                    params: {
                        '_token': $('meta[name="csrf-token"]').attr('content'),
                        'codigo_compra': _this.model.get('codigo_compra')
                    }
                },
                deleteFile: {
                    enabled: true,
                    endpoint: window.Misc.urlFull(Route.route('marketplace.transacciones.evaluacion.index')),
                    customHeaders: {
                        'X-CSRF-TOKEN': $('meta[name="csrf-token"]').attr('content'),
                        'X-XSRF-TOKEN': window.Misc.getCsrf().getCookie('XSRF-TOKEN')
                    },
                    params: {
                        '_token': $('meta[name="csrf-token"]').attr('content'),
                        'codigo_compra': _this.model.get('codigo_compra'),
                        'delete_bill' : true
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

            this.$inputBill.val(name);
        },

        onCompleteFetchFiles: function (resp, success) {
            this.uploaderFile = $('#uploader-bill');

            var _this = this,
                count = 0;
            if( success ) {
                _.each(resp, function(file) {
                    var $itemFile = this.uploaderFile.fineUploader('getItemByFileId', count);
                    this.itemWasLoaded($itemFile, file);
                    this.$inputBill.val(file.name);

                    count++;
                }, this);
            }
        },

        onDeleteFile: function(id) {
            this.$inputBill.val('');
        },

        itemWasLoaded: function ($itemFile, fileObj) {
            $itemFile.find('.preview-link').attr('href', fileObj.direccion_adjunto);
            // reload plugins function
            this.ready();

            $itemFile.find('select').addClass('qq-hide')
                .next('.select2-container').addClass('qq-hide');
        },

        /**
         * Load spinner on the request
        */
        loadSpinner: function () {
             window.Misc.setSpinner( this.$wrapperForm );
        },

        /**
        * response of the server
        */
        responseServer: function ( model, resp, opts ) {
            window.Misc.removeSpinner({
                'wrap': this.$wrapperForm,
                'direct': true
            });

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
            if( !_.isUndefined(resp.success) ){
                if( resp.success ){
                    new PNotify({
                        title: 'La evaluación ha sido modificada exitosamente!!!',
                        text:  txt,
                        type: 'success',
                        addclass: "stack-custom2"
                    });
                }else{
                   new PNotify({
                        title: 'Error',
                        text: txt
                    });
                }
            }
        },

        /**
        * onStore
        */
        onStore: function (e) {
            e.preventDefault();
            var data = window.Misc.formToJson( e.target );
            this.model.save( data, {wait: true} );
        },

        onStoreBill: function(e) {
            e.preventDefault();
            var _this = this,
            data = window.Misc.formToJson( e.target );
            data.codigo_acuerdo_compra = this.model.get('codigo_compra');

            this.deliverablesModel.save(data, {wait: true, success:function() {
                    _this.listenTo( _this.deliverablesModel, 'request', _this.loadSpinner );
                    _this.listenTo( _this.deliverablesModel, 'sync', _this.responseServer );
                }
            });
            this.stopListening(this.deliverablesModel, 'request');
            this.stopListening(this.deliverablesModel, 'sync');
        },

        /**
        *   Reject deliverables
        */
        onRefused: function (e) {
            e.preventDefault();

            this.deliverablesModal = new app.DeliverablesWindow({
                model: this.deliverablesModel,
                parameters: {
                    data:{
                        codigo_compra: this.model.get('codigo_compra'),
                        module: window._tp.getModule['agreements'],
                        refused_deliverables: true
                    }
                }
            });

            this.deliverablesModal.render();
        },

        /**
        *   Approve deliverables
        */
        onApprove: function (e) {
            e.preventDefault();

            var isChecked = this.$wrapperSaleAgreementDeliverables.find('input:checkbox:checked');
            var numCheck = this.$wrapperSaleAgreementDeliverables.find('input:checkbox');

            if (isChecked.length === numCheck.length ) {
                this.deliverablesModal = new app.DeliverablesWindow({
                    model: this.deliverablesModel,
                    parameters: {
                        data:{
                            codigo_compra: this.model.get('codigo_compra'),
                            approve_deliverables: true
                        }
                    }
                });
                this.deliverablesModal.render();
            } else {
                var conf = {
                    'text': 'Debes seleccionar todos los entregables',
                    'type': 'alert'
                };
                _.extend(this.configAlert, conf);

                window.Misc.showAlertBox(this.configAlert);
            }

        },

        /*
        * Render buttons deliverables
        */
        deliverablesButtons: function () {
            if(this.deliverablesModel.get('codigo_estado')){
                if (this.deliverablesModel.get('codigo_estado') === window._tp.getState['finalized']) {
                    var checkBox = this.$wrapperSaleAgreementDeliverables.find('input:checkbox');
                    checkBox.attr("disabled", true);
                } else {
                    var checkBox = this.$wrapperSaleAgreementDeliverables.find('input:checkbox');
                    checkBox.attr("disabled", false);
                }
                this.$wrapperAction.html( this.templateButtons( {codigo_estado: this.deliverablesModel.get('codigo_estado'), state_agreement: this.deliverablesModel.get('state_agreement'), codigo_compra_encrypt: this.deliverablesModel.get('codigo_compra_encrypt'), codigo_acuerdo_compra: this.deliverablesModel.get('codigo_acuerdo_compra')} ) );
            }
        },

        /**
        * guarda aceptacion entregables
        * por parte de admin
        */
        onDeliverables: function (e) {
            e.preventDefault();
            var data = window.Misc.formToJson(e.target);

            _this = this;
            
            console.log(data, Array.isArray(data.entregables));

            if (!Array.isArray(data.entregables)) {
                data.entregables = new Array(data.entregables);
            }

            window.Misc.setSpinner( e.target );

            if( this.acceptConfirm instanceof Backbone.View ) {
                this.acceptConfirm.stopListening();
                this.acceptConfirm.undelegateEvents();
            }

            this.acceptConfirm = new app.ConfirmWindow({
                parameters: {
                    template: '<p>¿Desea enviar aprobación?</p>',
                    titleConfirm: "Confirmación",
                    onConfirm: function () {
                        $.ajax({
                            url: document.url + Route.route('marketplace.transacciones.entregable.updatedeliverables'),
                            type: 'POST',
                            data: data,
                        })
                        .success (function (response, textStatus, jqXHR ) {
                            const text = response.success ? response.message : response.errors;
                            const type = response.success ? 'success' : 'alert';
            
                            // e.target.reset();

                            $('#form-entregables-submit').addClass('disabled');
            
                            window.Misc.removeSpinner({
                                'wrap': e.target,
                                'direct': true
                            });
            
                            var config = {
                                'text': text,
                                'type': type
                            };
            
                            _.extend( _this.configAlert, config );
            
                            window.Misc.showAlertBox( _this.configAlert );
            
                        })
                        .error (function ( XMLHttpRequest, textStatus, errorThrown ) {
            
                            const text = 'Contacte con el administrador';
                            const type = 'alert';
            
                            var config = {
                                'text': text,
                                'type': type
                            };
            
                            _.extend( _this.configAlert, config );
            
                            window.Misc.showAlertBox( _this.configAlert );
                        });
                    }
                }
            });

            this.acceptConfirm.render();

        }
   });

})(jQuery, this, this.document);
