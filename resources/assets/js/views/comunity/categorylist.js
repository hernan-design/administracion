/**
* Class CategoyListView extends of Backbone View
* @author TuProyecto || Desarrollador : @dortegon
* @link http://TuProyecto.com
*/

//Global App Backbone
app || (app = {});

(function ($, window, document, undefined) {

    app.CategoyListView = Backbone.View.extend({

        el: '#category-content',
        templateConfirm: _.template( ($('#confirm-category-tpl').html() || '') ),
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
            this.channelCategoryModel = new app.ChannelCategoryModel();
        },

        /*
         * Render View Element
         */
        render: function() {

            // Initialize attributes
            this.$searchState       = this.$('#codigo_estado');
            this.$searchNombre      = this.$('#nombre_categoria_canal');
            this.$categoryListSearchTable    = this.$('#category-list-search-table');

            // overwrite model id attribute
            this.referenceViews();
        },

        /**
         * reference to views
         */
        referenceViews: function () {

            var _this = this;
            this.categoryListSearchTable = this.$categoryListSearchTable.DataTable({
                dom:"<'row'<'col-sm-5'i><'col-sm-7'p>>" +
                "<'row'<'col-sm-12'tr>>" +
                "<'row'<'col-sm-5'i><'col-sm-7'p>>",
                processing: true,
                serverSide: true,
                language: window.Misc.dataTableES(),
                ajax: {
                    url: window.Misc.urlFull( Route.route('comunidad.categoria.index') ),
                    data: function( data ) {
                        data.nombre_categoria_canal   = _this.$searchNombre.val();
                        data.codigo_estado  = _this.$searchState.val();
                    },
                },
                columns: [
                    { data: 'nombre_categoria_canal', name: 'nombre_categoria_canal'       },
                ],
                order: [
                    [ 0, 'asc' ]
                ],
                columnDefs: [
                    {
                        targets: 0,
                        render: function ( data, type, full, row ) {
                            var ucFirst = full.nombre_categoria_canal.charAt(0).toUpperCase() + full.nombre_categoria_canal.slice(1)

                            return '<a href="' + window.Misc.urlFull(Route.route('comunidad.categoria.edit', {categorium: full.codigo_categoria_canal}) ) + '" title="Ver categoría" style="color:#3FC1A5">'+ucFirst+'</a>';
                        }
                    },
                    {
                        targets: 1,
                        width: '10%',
                        orderable: false,
                        render: function ( data, type, full, row ) {
                            return '<a href="'+ window.Misc.urlFull(Route.route('comunidad.categoria.edit', {categorium: full.codigo_categoria_canal}) ) +'" title="Editar categoría" class="btn btn-default btn-xs primary"><i class="glyphicon glyphicon-pencil blue"></i></a>'+
                                '<a title="Inactivar categoría" data-id="'+ full.codigo_categoria_canal +'" class="btn btn-default btn-xs success"><i class="glyphicon glyphicon-ok green"></i></a>';
                        }
                    }
                ],
                fnRowCallback: function( row, data ) {
                    if ( data.codigo_estado == window._tp.getState['inactive'] ) {
                        $(row).find('.green').attr( "class", "glyphicon glyphicon-remove red" );
                        $(row).find('.success').attr( "title", "Activar categoría" );
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
            this.categoryListSearchTable.ajax.reload();
        },

        clear: function(e) {
            e.preventDefault();

            this.$searchState.val('');
            this.$searchNombre.val('');

            this.categoryListSearchTable.ajax.reload();
        },

        // Modal confirm
        onModalConfirm: function (e) {
            e.preventDefault();

            this.listenToOnce( this.channelCategoryModel, 'request', this.loadSpinner );

            var _this = this,
                data = {
                    'codigo_categoria_canal': $(e.currentTarget).data('id'),
                    'change_state': true
                };

            if( this.acceptConfirm instanceof Backbone.View ) {
                this.acceptConfirm.stopListening();
                this.acceptConfirm.undelegateEvents();
            }

            this.acceptConfirm = new app.ConfirmWindow({
                parameters: {
                    template: this.templateConfirm,
                    titleConfirm: "Confirmación",
                    onConfirm: function () {
                        _this.channelCategoryModel.save(data, {wait: true, patch: true,
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
                                        _this.categoryListSearchTable.ajax.reload( null, false );
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
