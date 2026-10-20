/**
* Class EventListView extends of Backbone View
* @author TuProyecto || Desarrollador : @dortegon
* @link http://TuProyecto.com
*/

//Global App Backbone
app || (app = {});

(function ($, window, document, undefined) {

    app.EventListView = Backbone.View.extend({

        el: '#events-content',
        template: _.template( ($('#event-list-tpl').html() || '') ),
        templateConfirm: _.template( ($('#confirm-event-tpl').html() || '') ),
        templateButtonsAction: _.template( ($('#buttons-action-tpl').html() || '') ),
        events: {
            'click .btn-search': 'search',
            'click .btn-clear': 'clear',
            'click .success': 'onModalConfirm',
            'click .danger': 'onModalConfirm',
            'click #clone-event': 'cloneEvent'
        },
        parameters: {
            dataFilter: {}
        },

        /**
         * Constructor Method
         */
        initialize : function(opts) {
            _.bindAll(this, 'render', 'cloneEvent');
        },

        /*
         * Render View Element
         */
        render: function() {

            var attributes = {};
            this.$el.html( this.template(attributes) );

            // Initialize attributes
            this.$eventListSearchTable  = this.$('#event-list-search-table');
            this.$searchNombre          = this.$('#nombre_evento');
            this.$searchState           = this.$('#codigo_estado');
            this.$formFilters           = this.$('#form-event-filter');

            // overwrite model id attribute
            this.referenceViews();
        },

        /**
         * reference to views
         */
        referenceViews: function () {

            var _this = this;
            this.eventListSearchTable = this.$eventListSearchTable.DataTable({
                dom:"<'row'<'col-sm-5'i><'col-sm-7'p>>" +
                "<'row'<'col-sm-12'tr>>" +
                "<'row'<'col-sm-5'i><'col-sm-7'p>>",
                processing: true,
                serverSide: true,
                language: window.Misc.dataTableES(),
                ajax: {
                    url: window.Misc.urlFull( Route.route('evento.index') ),
                    data: function( data ) {
                        data.nombre_evento   = _this.$searchNombre.val();
                        data.codigo_estado  = _this.$searchState.val();
                    },
                },
                columns: [
                    { data: 'codigo_evento',    name: 'codigo_evento'  },
                    { data: 'nombre_evento',    name: 'nombre_evento'  },
                    { data: 'fecha_evento',    name: 'fecha_evento'  }
                ],
                order: [
                    [ 0, 'desc' ]
                ],
                columnDefs: [
                    {
                        targets: 1,
                        render: function ( data, type, full, row ) {
                            return '<a href="' + window.Misc.urlFull(Route.route('evento.edit', {evento: full.codigo_evento}) ) + '" title="Ver evento" style="color:#3FC1A5">'+full.nombre_evento+'</a>';
                        }
                    },
                    {
                        targets: 3,
                        width: '10%',
                        orderable: false,
                        className: 'text-center',
                        render: function ( data, type, full, row ) {
                            return _this.templateButtonsAction( {full: full} );
                        }
                    }
                ],
                fnRowCallback: function( row, data ) {
                    if ( data.codigo_estado == window._tp.getState['inactive'] ) {
                        $(row).find('.green').attr( "class", "glyphicon glyphicon-remove red" );
                        $(row).find('.success').attr( "title", "Activar evento" );
                    }if(data.codigo_estado == window._tp.getState['active']){
                        $(row).find('.red').attr( "class", "glyphicon glyphicon-ok green" );
                    }
                }
            });
        },

        /**
        * Load spinner on the request
        */
        loadSpinner: function () {
             window.Misc.setSpinner( this.$gridSearchTable );
        },

        search: function(e) {
            e.preventDefault();
            this.eventListSearchTable.ajax.reload();
        },

        clear: function(e) {
            e.preventDefault();

            this.$searchState.val('');
            this.$searchNombre.val('');

            this.eventListSearchTable.ajax.reload();
        },

        // Clone Event

        cloneEvent: function(e) {
            e.preventDefault();

            var _this = this, attributes = { evento:  $(e.currentTarget).attr('data-event') };

            window.Misc.setSpinner( this.$eventListSearchTable );

            $.ajax({
                method: 'POST',
                url: window.Misc.urlFull( Route.route( 'evento.duplicar') ),
                data: attributes
            }).done(function(resp) {
                var _this = this;

                window.Misc.removeSpinner({
                    'wrap': _this.$eventListSearchTable,
                    'direct': false
                });

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

                //complete proccess
                if( resp.success !== undefined ) {
                    if( resp.success ){
                        _this.eventListSearchTable.ajax.reload();
                        window.Misc.showAlertBox({
                            text: resp.message,
                            type: 'success',
                            closeTime: 5000
                        });
                    }else {
                        window.Misc.showAlertBox({
                            text: resp.error,
                            type: 'alert',
                            closeTime: 5000
                        });
                    }
                }
            }.bind(this));
        },

        // Modal confirm
        onModalConfirm: function (e) {
            e.preventDefault();
            this.eventModel = new app.EventModel();
            this.listenToOnce( this.eventModel, 'request', this.loadSpinner );

            var _this = this,
                action = $(e.currentTarget).attr('data-action'),
                event_name = $(e.currentTarget).attr('data-name'),
                data = {
                    'codigo_evento': $(e.currentTarget).data('id'),
                    'change_state': true
                };

            if( this.acceptConfirm instanceof Backbone.View ) {
                this.acceptConfirm.stopListening();
                this.acceptConfirm.undelegateEvents();
            }

            this.acceptConfirm = new app.ConfirmWindow({
                parameters: {
                    template: this.templateConfirm({
                        action : action,
                        event_name: event_name
                    }),
                    titleConfirm: "Confirmación",
                    onConfirm: function () {
                        _this.eventModel.save(data, {wait: true, patch: true,
                            success: function (model, resp) {
                                // complete proccess
                                window.Misc.removeSpinner({
                                    'wrap': _this.$el,
                                    'direct': false
                                });

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
                                        _this.eventListSearchTable.ajax.reload( null, false );
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
