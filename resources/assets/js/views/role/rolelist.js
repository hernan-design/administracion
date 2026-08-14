/**
* Class RoleListView extends of Backbone View
* @author TuProyecto || Desarrollador : @dortegon
* @link http://TuProyecto.com
*/

//Global App Backbone
app || (app = {});

(function ($, window, document, undefined) {

    app.RoleListView = Backbone.View.extend({

        el: '#role-content',
        template: _.template( ($('#role-list-tpl').html() || '') ),
        templateConfirm: _.template( ($('#confirm-role-tpl').html() || '') ),
        templateButtonsAction: _.template( ($('#buttons-action-tpl').html() || '') ),
        events: {
            'click .btn-search': 'search',
            'click .btn-clear': 'clear',
            'click .success': 'onModalConfirm',
            'click .danger': 'onModalConfirm',
            'click .data-export-rols': 'onRolsExport',
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
            this.$roleListSearchTable   = this.$('#role-list-search-table');
            this.$searchNombre          = this.$('#title');
            this.$searchState           = this.$('#codigo_estado');
            this.$formFilters           = this.$('#form-role-filter');

            // overwrite model id attribute
            this.referenceViews();
        },

        /**
         * reference to views
         */
        referenceViews: function () {

            var _this = this;
            this.roleListSearchTable = this.$roleListSearchTable.DataTable({
                dom:"<'row'<'col-sm-5'i><'col-sm-7'p>>" +
                "<'row'<'col-sm-12'tr>>" +
                "<'row'<'col-sm-5'i><'col-sm-7'p>>",
                processing: true,
                serverSide: true,
                language: window.Misc.dataTableES(),
                ajax: {
                    url: window.Misc.urlFull( Route.route('rol.index') ),
                    data: function( data ) {
                        data.title   = _this.$searchNombre.val();
                        data.codigo_estado  = _this.$searchState.val();
                    },
                },
                columns: [
                    { data: 'title', name: 'title' },
                    { data: 'type', name: 'type' },
                    { data: 'description', name: 'description' },
                ],
                order: [
                    [ 0, 'asc' ]
                ],
                columnDefs: [
                    {
                        targets: 0,
                        render: function ( data, type, full, row ) {
                            return '<a href="' + window.Misc.urlFull(Route.route('rol.edit', {rol: full.id}) ) + '" title="Ver rol" style="color:#3FC1A5">'+full.title+'</a>';
                        }
                    },
                    {
                        targets: 3,
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
            this.roleListSearchTable.ajax.reload();
        },

        clear: function(e) {
            e.preventDefault();

            this.$searchNombre.val('');
            this.$searchState.val('');

            this.roleListSearchTable.ajax.reload();
        },

        // Modal confirm
        onModalConfirm: function (e) {
            e.preventDefault();
            this.roleModel = new app.RoleModel();
            this.listenToOnce( this.roleModel, 'request', this.loadSpinner );

            var _this = this,
                action = $(e.currentTarget).attr('data-action'),
                role_name = $(e.currentTarget).attr('data-name'),
                data = {
                    'id': $(e.currentTarget).data('id'),
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
                        role_name: role_name
                    }),
                    titleConfirm: "Confirmación",
                    onConfirm: function () {
                        _this.roleModel.save(data, {wait: true, patch: true,
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
                                        _this.roleListSearchTable.ajax.reload( null, false );
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

        /**
         * on Rols export
         */
        onRolsExport: function (e) {
            e.preventDefault();
            data = window.Misc.formToJson(this.$formFilters);
            console.log(data);
            window.Misc.redirect( window.Misc.urlFull( Route.route( 'rol.show', data) ) );
        },
    });

})(jQuery, this, this.document);
