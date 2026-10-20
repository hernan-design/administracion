/**
* Class NotificationListView extends of Backbone View
* @author TuProyecto || Desarrollador : @dortegon
* @link http://TuProyecto.com
*/

//Global App Backbone
app || (app = {});

(function ($, window, document, undefined) {

    app.NotificationListView = Backbone.View.extend({
        el: '#output-content',
        templateButtonsAction: _.template( ($('#buttons-action-tpl').html() || '') ),
        templateConfirmNoti: _.template( ($('#confirm-notification-tpl').html() || '') ),
        events: {
            'click .btn-clear': 'clear',
            'click .btn-search': 'search',
            'click .danger': 'onModalConfirm',
            'click .success': 'onReturnNotification'
        },
        parameters: {
            //
        },

        /**
         * Constructor Method
         */
        initialize : function(opts) {
            
            // extends parameters
            if( opts !== undefined && _.isObject(opts.parameters) ){
                this.parameters = $.extend({}, this.parameters, opts.parameters);
            }

            this.notificationOutputModel = new app.NotificationOutputModel();

            //Init Attribute
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
            // Initialize attributes
            this.$searchType    = this.$('#tipo_notificacion');
            this.$searchDate    = this.$('#fecha_notificacion');
            this.$formFilters   = this.$('#notification-output-filter');
            this.$notificationListSearchTable   = this.$('#notification-list-search-table');

            // overwrite model id attribute
            this.referenceViews();      
        },

        /**
         * reference to views
         */
        referenceViews: function () {

            var _this = this;
            this.notificationListSearchTable = this.$notificationListSearchTable.DataTable({
                dom:"<'row'<'col-sm-5'i><'col-sm-7'p>>" +
                "<'row'<'col-sm-12'tr>>" +
                "<'row'<'col-sm-5'i><'col-sm-7'p>>",
                processing: true,
                serverSide: true,
                language: window.Misc.dataTableES(),
                ajax: {
                    url: window.Misc.urlFull( Route.route('notificacion.output.index') ),
                    data: function( data ) {
                        data.tipo_notificacion  = _this.$searchType.val();
                        data.fecha_notificacion = _this.$searchDate.val();
                    }
                },
                columns: [
                    { data: 'titulo_notificacion',  name: 'titulo_notificacion' },
                    { data: 'texto_notificacion',   name: 'texto_notificacion'  },
                ],
                order: [
                    [ 0, 'desc' ]
                ],
                columnDefs: [
                    {
                        targets: 2,
                        width: '10%',
                        orderable: false,
                        className: 'text-center',
                        render: function ( data, type, full, row ) {
                            return _this.templateButtonsAction( {full: full} );
                        }
                    }
                ],
                fnRowCallback: function( row, data ) {
                }
            });
        },

        search: function(e) {
            e.preventDefault();
            this.notificationListSearchTable.ajax.reload();
        },

        clear: function(e) {
            e.preventDefault();

            this.$searchDate.val('');
            this.$searchType.val('');

            this.notificationListSearchTable.ajax.reload();
        },

        // Modal confirm
        onModalConfirm: function (e) {
            e.preventDefault();
            
            var _this = this,
                action = $(e.currentTarget).attr('data-action'),
                noti_name = $(e.currentTarget).attr('data-name'),
                data = {
                    'codigo_notificacion': $(e.currentTarget).data('id')
                };

            // init model
            this.notificationOutputModel.set(data);

            if( this.acceptConfirm instanceof Backbone.View ) {
                this.acceptConfirm.stopListening();
                this.acceptConfirm.undelegateEvents();
            }

            this.acceptConfirm = new app.ConfirmWindow({
                parameters: {
                    template: this.templateConfirmNoti({
                        action : action,
                        noti_name: noti_name
                    }),
                    titleConfirm: "Confirmación",
                    onConfirm: function () {
                        _this.notificationOutputModel.destroy({
                            success: function(model, resp){
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
                                        _this.notificationListSearchTable.ajax.reload( null, false );
                                    }else{
                                        window.Misc.showAlertBox( confCrop );
                                    }
                                }
                            }
                        });
                    }
                }
            });

            this.acceptConfirm.render();
        },

        // Confirm return notification 
        onReturnNotification: function(e) {
            e.preventDefault();

            var _this = this,
                action = $(e.currentTarget).attr('data-action'),
                noti_name = $(e.currentTarget).attr('data-name'),
                data = {
                    codigo_notificacion: $(e.currentTarget).data('id')
                };

            // init model
            this.notificationOutputModel.set(data);

            if( this.acceptConfirm instanceof Backbone.View ) {
                this.acceptConfirm.stopListening();
                this.acceptConfirm.undelegateEvents();
            }
    
            this.acceptConfirm = new app.ConfirmWindow({
                parameters: {
                    template: this.templateConfirmNoti({
                        action : action,
                        noti_name: noti_name
                    }),
                    titleConfirm: "Confirmación",
                    onConfirm: function () {
                        _this.notificationOutputModel.save(data, {wait: true, patch: true,
                            success: function(model, resp, options){
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
                                        _this.notificationListSearchTable.ajax.reload( null, false );
                                    }else{
                                        window.Misc.showAlertBox( confCrop );
                                    }
                                }
                            }
                        });
                    }
                }
            });
    
            this.acceptConfirm.render();
        }

    });

})(window.jQuery, window, document);
