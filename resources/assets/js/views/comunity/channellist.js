/**
* Class ChannelListView extends of Backbone View
* @author TuProyecto || Desarrollador : @dortegon
* @link http://TuProyecto.com
*/

//Global App Backbone
app || (app = {});

(function ($, window, document, undefined) {

    app.ChannelListView = Backbone.View.extend({

        el: '#channel-content',
        templateConfirm: _.template( ($('#confirm-channel-tpl').html() || '') ),
        templateButtonsAction: _.template( ($('#buttons-action-tpl').html() || '') ),
        events: {
            'click .btn-search': 'search',
            'click .btn-clear': 'clear',
            'click .success': 'onModalConfirm',
            'click .danger': 'onModalConfirm'
        },
        parameters: {
            dataFilter: {}
        },

        /**
         * Constructor Method
         */
        initialize : function(opts) {
            // extends parameters
            if( opts !== undefined && _.isObject(opts.parameters) ){
                this.parameters = $.extend({}, this.parameters, opts.parameters);
            }
            
            this.channelModel = new app.ChannelModel();
        },

        /*
         * Render View Element
         */
        render: function() {

            // Initialize attributes
            this.$channelListSearchTable    = this.$('#channel-list-search-table');
            this.$searchNombre              = this.$('#nombre_canal');
            this.$searchState               = this.$('#codigo_estado');

            // overwrite model id attribute
            this.referenceViews();
        },

        /**
         * reference to views
         */
        referenceViews: function () {

            var _this = this;
            this.channelListSearchTable = this.$channelListSearchTable.DataTable({
                dom:"<'row'<'col-sm-5'i><'col-sm-7'p>>" +
                "<'row'<'col-sm-12'tr>>" +
                "<'row'<'col-sm-5'i><'col-sm-7'p>>",
                processing: true,
                serverSide: true,
                language: window.Misc.dataTableES(),
                ajax: {
                    url: window.Misc.urlFull( Route.route('comunidad.canal.index') ),
                    data: function( data ) {
                        data.nombre_canal   = _this.$searchNombre.val();
                        data.codigo_estado  = _this.$searchState.val();
                    },
                },
                columns: [
                    { data: 'nombre_canal', name: 'nombre_canal'       },
                    { data: 'descripcion_canal',  name: 'descripcion_canal' },
                ],
                order: [
                    [ 0, 'asc' ]
                ],
                columnDefs: [
                    {
                        targets: 0,
                        render: function ( data, type, full, row ) {
                            var ucFirst = full.nombre_canal.charAt(0).toUpperCase() + full.nombre_canal.slice(1);
                            return '<a href="' + window.Misc.urlFull(Route.route('comunidad.canal.edit', {canal: full.codigo_canal}) ) + '" title="Ver canal" style="color:#3FC1A5">'+ucFirst+'</a>';
                        }
                    },
                    {
                        targets: 2,
                        width: '10%',
                        orderable: false,
                        className: 'text-center',
                        render: function ( data, type, full, row ) {
                            return _this.templateButtonsAction( {full: full} )
                        }
                    }
                ]
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
            this.channelListSearchTable.ajax.reload();
        },

        clear: function(e) {
            e.preventDefault();

            this.$searchState.val('');
            this.$searchNombre.val('');

            this.channelListSearchTable.ajax.reload();
        },

        // Modal confirm
        onModalConfirm: function (e) {
            e.preventDefault();

            this.listenToOnce( this.channelModel, 'request', this.loadSpinner );

            var _this = this,
                template,
                target = $(e.currentTarget);
                data = {
                    'codigo_canal': target.data('id'),
                    'change_state': true
                };

            if( this.acceptConfirm instanceof Backbone.View ) {
                this.acceptConfirm.stopListening();
                this.acceptConfirm.undelegateEvents();
            }

            this.acceptConfirm = new app.ConfirmWindow({
                parameters: {
                    template: this.templateConfirm( {
                        channel: target.data('name'),
                        action: target.data('action'),
                        count: target.data('inscribed'),
                        codigo_estado: target.data('state')
                    } ),
                    titleConfirm: "Confirmación",
                    onConfirm: function () {
                        _this.channelModel.save(data, {wait: true, patch: true,
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
                                        _this.channelListSearchTable.ajax.reload( null, false );
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
