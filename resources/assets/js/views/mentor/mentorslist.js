/**
* Class MentorsListView extends of Backbone View
* @author TuProyecto || Desarrollador : @dortegon
* @link http://TuProyecto.com
*/

//Global App Backbone
app || (app = {});

(function ($, window, document, undefined) {

    app.MentorsListView = Backbone.View.extend({

        el: '#advisory-content',
        template: _.template( ($('#advisory-list-tpl').html() || '') ),
        templateConfirm: _.template( ($('#confirm-advisory-tpl').html() || '') ),
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
            this.advisoryModel = new app.AdvisoryModel();

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
            this.$advisoryListSearchTable   = this.$('#advisory-list-search-table');
            this.$searchNombre              = this.$('#nombre_usuario');
            this.$searchNickname            = this.$('#nickname_usuario');
            this.$searchIdentification      = this.$('#cedula_usuario');
            this.$searchEmail               = this.$('#correo_electronico');
            this.$searchCity                = this.$('#codigo_ciudad');
            this.$searchState               = this.$('#codigo_estado');
            this.$formFilters               = this.$('#form-user-filter');

            // overwrite model id attribute
            this.referenceViews();
        },

        /**
         * reference to views
         */
        referenceViews: function () {

            var _this = this;
            this.advisoryListSearchTable = this.$advisoryListSearchTable.DataTable({
                dom:"<'row'<'col-sm-5'i><'col-sm-7'p>>" +
                "<'row'<'col-sm-12'tr>>" +
                "<'row'<'col-sm-5'i><'col-sm-7'p>>",
                processing: true,
                serverSide: true,
                language: window.Misc.dataTableES(),
                ajax: {
                    url: window.Misc.urlFull( Route.route('mentoria.mentores.index', {role: window._tp.getRole['mentor']}) ),
                    data: function( data ) {
                        data.nombre_usuario     = _this.$searchNombre.val();
                        data.nickname_usuario   = _this.$searchNickname.val();
                        data.cedula_usuario     = _this.$searchIdentification.val();
                        data.correo_electronico = _this.$searchEmail.val();
                        data.nombre_ciudad      = _this.$searchCity.val();
                        data.codigo_estado      = _this.$searchState.val();
                    }
                },
                columns: [
                    { data: 'nombre_usuario',       name: 'nombre_usuario'      },
                    { data: 'nickname_usuario',     name: 'nickname_usuario'    },
                    { data: 'correo_electronico',   name: 'correo_electronico'  },
                    { data: 'ciudad',               name: 'ciudad'              },
                    { data: 'promedio_evaluacion_mentor',   name: 'promedio_evaluacion_mentor'              },
                    { data: 'fecha_aceptacion_terminos',   name: 'fecha_aceptacion_terminos' },
                ],
                order: [
                    [ 0, 'desc' ]
                ],
                columnDefs: [
                    {
                        targets: 0,
                        className: 'text-capitalize',
                        render: function ( data, type, full, row ) {
                            return '<a href="' + window.Misc.urlFull( Route.route('mentoria.mentores.show', {mentore: full.codigo_usuario})) + '" title="Ver mentor" style="color:#3FC1A5">' + full.nombre_usuario + '</a>';
                        }
                    },
                    {
                        targets: 4,
                        className: 'text-center',
                    },
                    {
                        targets: 6,
                        width: '10%',
                        orderable: false,
                        className: 'text-center',
                        render: function ( data, type, full, row ) {
                            return _this.templateButtonsAction( {full: full} )
                        }
                    }
                ],
                fnRowCallback: function( row, data ) {
                    if ( data.codigo_estado == window._tp.getState['review'] ) {
                        $(row).css( {"color":"#C9302C"} );
                    }
                }
            });
        },

        search: function(e) {
            e.preventDefault();
            this.advisoryListSearchTable.ajax.reload();
        },

        clear: function(e) {
            e.preventDefault();

            this.$searchState.val('')
            this.$searchEmail.val('');
            this.$searchNombre.val('');
            this.$searchNickname.val('');
            this.$searchCity.select2('val', '');

            this.advisoryListSearchTable.ajax.reload();
        },

        /**
         * on filter export
         */
        onFilterExport: function (e) {
            e.preventDefault();
            data = window.Misc.formToJson(this.$formFilters);
            window.Misc.redirect( window.Misc.urlFull( Route.route( 'usuario.exportar', data) ) );
        },

        // Modal confirm
        onModalConfirm: function (e) {
            e.preventDefault();

            this.listenToOnce( this.advisoryModel, 'request', this.loadSpinner );

            var _this = this,
                target = $(e.currentTarget),
                data = {
                    'role': window._tp.getRole['mentor'],
                    'codigo_usuario': target.data('id'),
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
                        advisory: target.data('name'),
                        action: target.data('action')
                    } ),
                    titleConfirm: "Confirmación",
                    onConfirm: function () {
                        _this.advisoryModel.save(data, {wait: true, patch: true,
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
                                        _this.advisoryListSearchTable.ajax.reload( null, false );
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
