/**
* Class ForumListView extends of Backbone View
* @author TuProyecto || Desarrollador : @dortegon
* @link http://TuProyecto.com
*/

//Global App Backbone
app || (app = {});

(function ($, window, document, undefined) {

    app.ForumListView = Backbone.View.extend({

        el: '#forum-content',
        templateButtonsAction: _.template( ($('#buttons-action-tpl').html() || '') ),
        templateConfirm: _.template( ($('#confirm-forum-tpl').html() || '') ),
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
            this.forumModel = new app.ForumModel();
        },

        /*
         * Render View Element
         */
        render: function() {

            // Initialize attributes
            this.$forumListSearchTable = this.$('#forum-list-search-table');
            this.$searchForumCategory  = this.$('#codigo_categoria_filter_data');
            this.$filterEmailUser  = this.$('#correo_electronico_usuario_filter_data');
            this.$filterUser  = this.$('#nombre_usuario_filter_data');
            this.$searchState   = this.$('#codigo_estado_filter_data');
            this.$filterBeginDate  = this.$('#fecha_inicio_foro_filter_data');
            this.$filterEndDate  = this.$('#fecha_fin_foro_filter_data');

            // overwrite model id attribute
            this.referenceViews();
        },

        /**
         * reference to views
         */
        referenceViews: function () {

            var _this = this;
            this.forumListSearchTable = this.$forumListSearchTable.DataTable({
                dom:"<'row'<'col-sm-5'i><'col-sm-7'p>>" +
                "<'row'<'col-sm-12'tr>>" +
                "<'row'<'col-sm-5'i><'col-sm-7'p>>",
                processing: true,
                serverSide: true,
                language: window.Misc.dataTableES(),
                ajax: {
                    url: window.Misc.urlFull( Route.route('foro.index') ),
                    data: function( data ) {
                        data.codigo_categoria_foro = _this.$searchForumCategory.val();
                        data.correo_electronico_usuario = _this.$filterEmailUser.val();
                        data.codigo_estado = _this.$searchState.val();
                        data.fecha_inicio_foro = _this.$filterBeginDate.val();
                        data.fecha_fin_foro = _this.$filterEndDate.val();
                    },
                },
                columns: [
                    { data: 'titulo_foro', name: 'titulo_foro'},
                    { data: 'nombre_categoria_foro',        name: 'nombre_categoria_foro'},
                    { data: 'nombre_usuario',   name: 'nombre_usuario' },
                    { data: 'correo_electronico',   name: 'correo_electronico' },
                    { data: 'fecha_apertura_foro',       name: 'fecha_apertura_foro' },
                    { data: 'hora_apertura_foro',       name: 'hora_apertura_foro', orderable: false },
                    { data: 'nombre_estado',        name: 'nombre_estado' },
                ],
                order: [
                    [ 4, 'desc' ]
                ],
                columnDefs: [
                    {
                        targets: 0,
                        render: function ( data, type, full, row ) {
                            var ucFirst = full.titulo_foro.charAt(0).toUpperCase() + full.titulo_foro.slice(1);
                            return ucFirst;
                        }
                    },
                    {
                        targets: 7,
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
            window.Misc.setSpinner( this.$el );
        },

        search: function(e) {
            e.preventDefault();
            this.forumListSearchTable.ajax.reload();
        },

        clear: function(e) {
            e.preventDefault();

            this.$searchForumCategory.val('');
            this.$filterUser.val('');
            this.$filterEmailUser.val('');
            this.$searchState.val('');
            this.$filterBeginDate.val('');
            this.$filterEndDate.val('');
            this.forumListSearchTable.ajax.reload();
        },

         // Modal confirm
        onModalConfirm: function (e) {
            e.preventDefault();
            console.log('target',e.currentTarget);
            this.listenToOnce( this.forumModel, 'request', this.loadSpinner );

            var _this = this,
                target = $(e.currentTarget),
                data = {
                    'codigo_foro': target.data('id'),
                    'codigo_estado': target.data('state'),
                    'change_state': true
                };

            if( this.acceptConfirm instanceof Backbone.View ) {
                this.acceptConfirm.stopListening();
                this.acceptConfirm.undelegateEvents();
            }

            this.acceptConfirm = new app.ConfirmWindow({
                parameters: {
                    template: this.templateConfirm( {
                        forum: target.data('name'),
                        action: target.data('action')
                    } ),
                    titleConfirm: "Confirmación",
                    onConfirm: function () {
                        _this.forumModel.save(data, {wait: true, patch: true,
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
                                        _this.forumListSearchTable.ajax.reload();
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
