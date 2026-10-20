/**
 * Created by root on 14/03/17.
 */

//Global App Backbone
app || (app = {});

(function ($, window, document, undefined) {

    app.UserListView = Backbone.View.extend({

        el: '#user-content',
        template: _.template( ($('#user-list-tpl').html() || '') ),
        templateButtonsAction: _.template( ($('#buttons-action-activity-detail').html() || '') ),
        events: {
            'click .btn-search': 'search',
            'click .btn-clear': 'clear',
            'click .data-export': 'onFilterExport',
            'click .active-product': 'onConfirm',
            'click .detail-activity': 'onDetailActivity',
        },
        parameters: {
            dataFilter: {}
        },

        /**
         * Constructor Method
         */
        initialize : function(opts) {

            // Events Listener custome
            this.on( 'beforeRender', this.beforeRender );
            this.on( 'afterRender', this.afterRender );

        },

        /*
         * Render View Element
         */
        render: function() {

            var attributes = {};
            this.$el.html( this.template(attributes) );

            // Initialize attributes
            this.$userListSearchTable   = this.$('#user-list-search-table');
            this.$searchNombre          = this.$('#nombre_usuario');
            this.$searchNickname        = this.$('#nickname_usuario');
            this.$searchEmail           = this.$('#correo_electronico');
            this.$searchCity            = this.$('#codigo_ciudad');
            this.$searchDocumentUser    = this.$('#identificacion_usuario');
            this.$searchTypeUser        = this.$('#tipo_usuario');
            this.$formFilters           = this.$('#form-user-filter');

            // overwrite model id attribute
            this.referenceViews();
        },

        /**
         * reference to views
         */
        referenceViews: function () {

            var _this = this;
            this.userListSearchTable = this.$userListSearchTable.DataTable({
                dom:"<'row'<'col-sm-5'i><'col-sm-7'p>>" +
                    "<'row'<'col-sm-12'tr>>" +
                    "<'row'<'col-sm-5'i><'col-sm-7'p>>",
                processing: true,
                serverSide: true,
                language: window.Misc.dataTableES(),
                ajax: {
                    url: window.Misc.urlFull( Route.route('usuario.index') ),
                    data: function( data ) {
                        data.nombre_usuario   = _this.$searchNombre.val();
                        data.nickname_usuario   = _this.$searchNickname.val();
                        data.correo_electronico = _this.$searchEmail.val();
                        data.nombre_ciudad = _this.$searchCity.val();
                        data.identificacion_usuario = _this.$searchDocumentUser.val();
                        data.tipo_usuario = _this.$searchTypeUser.val();
                    }
                },
                columns: [
                    { data: 'nombre_usuario',           name: 'nombre_usuario'           },
                    { data: 'nickname_usuario',         name: 'nickname_usuario'         },
                    { data: 'correo_electronico',       name: 'correo_electronico'       },
                    { data: 'fecha_nacimiento_usuario', name: 'fecha_nacimiento_usuario' },
                    { data: 'ciudad',                   name: 'ciudad'                   },
                    { data: 'fecha_aceptacion_terminos',name: 'fecha_aceptacion_terminos'},
                ],
                order: [
                    [ 0, 'desc' ]
                ],
                columnDefs: [
                    {
                        targets: 0,
                        className: 'text-capitalize',
                        render: function ( data, type, full, row ) {
                            return '<a href="' + window.Misc.urlFull( Route.route('usuario.show', {usuario: full.codigo_usuario})) + '" title="Ver usuario" style="color:#3FC1A5">' + full.nombre_usuario + '</a>';
                        }
                    },
                    {
                        targets: 2,
                        render: function ( data, type, full, row ) {
                            return '<a href="' + window.Misc.urlFull( Route.route('usuario.show', {usuario: full.codigo_usuario})) + '" title="Ver usuario" style="color:#3FC1A5">' + full.correo_electronico + '</a>';
                        }
                    },
                    {
                        targets: 6,
                        className: 'text-center',
                        render: function ( data, type, full, row ) {
                            return _this.templateButtonsAction( { full: full } );
                        }
                    }
                ],
                fnRowCallback: function( row, data ) {
                }
            });

            // Instance of Documents para carga de usuarios
			this.uploadUsersDocuments = new app.UploadUsersDocuments({
				el: this.$('#wrap-documents'),
				//model: this.modelo,
				parameters: {
					//
				}
			});
        },

        search: function(e) {
            e.preventDefault();
            this.userListSearchTable.ajax.reload();
        },

        clear: function(e) {
            e.preventDefault();

            this.$searchNombre.val('');
            this.$searchEmail.val('');
            this.$searchNickname.val('');
            this.$searchCity.select2('val', '');
            this.$searchDocumentUser.val('');

            this.userListSearchTable.ajax.reload();
        },

        /**
         * on filter export
         */
        onFilterExport: function (e) {
            e.preventDefault();
            data = window.Misc.formToJson(this.$formFilters);
            window.Misc.redirect( window.Misc.urlFull( Route.route( 'usuario.exportar', data) ) );
        },

        onDetailActivity: function(e) {
            e.preventDefault();

            var code_user = $(e.currentTarget).data('user');

            window.Misc.redirect( app.AppRouter.navigate( Route.route('usuario.detail',{user: code_user}) ) );
        }

    });

})(window.jQuery, window, document);
