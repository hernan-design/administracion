/**
* Class ProfessionsListView extends of Backbone View
* @author TuProyecto || Desarrollador : @dortegon
* @link http://TuProyecto.com
*/

//Global App Backbone
app || (app = {});

(function ($, window, document, undefined) {

    app.ProfessionsListView = Backbone.View.extend({

        el: '#profession-content',
        template: _.template( ($('#profession-list-tpl').html() || '') ),
        templateConfirm: _.template( ($('#confirm-profession-tpl').html() || '') ),
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
            this.$searchState   = this.$('#codigo_estado');
            this.$formFilters   = this.$('#form-profession-filter');
            this.$searchNombre  = this.$('#nombnre_profesion_persona');
            this.$professionListSearchTable = this.$('#profession-list-search-table');

            // overwrite model id attribute
            this.referenceViews();
        },

        /**
         * reference to views
         */
        referenceViews: function () {
            var _this = this;
            this.professionListSearchTable = this.$professionListSearchTable.DataTable({
                dom:"<'row'<'col-sm-5'i><'col-sm-7'p>>" +
                "<'row'<'col-sm-12'tr>>" +
                "<'row'<'col-sm-5'i><'col-sm-7'p>>",
                processing: true,
                serverSide: true,
                language: window.Misc.dataTableES(),
                ajax: {
                    url: window.Misc.urlFull( Route.route('usuario.profesion.index') ),
                    data: function( data ) {
                        data.nombnre_profesion_persona   = _this.$searchNombre.val();
                        data.codigo_estado  = _this.$searchState.val();
                    },
                },
                columns: [
                    { data: 'nombnre_profesion_persona', name: 'nombnre_profesion_persona' }
                ],
                order: [
                    [ 0, 'asc' ]
                ],
                columnDefs: [
                    {
                        targets: 0,
                        render: function ( data, type, full, row ) {
                            return '<a href="' + window.Misc.urlFull(Route.route('usuario.profesion.edit', {profesion: full.codigo_profesion_persona}) ) + '" title="Ver profesión" style="color:#3FC1A5">'+full.nombnre_profesion_persona+'</a>';
                        }
                    },
                    {
                        targets: 1,
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
            this.professionListSearchTable.ajax.reload();
        },

        clear: function(e) {
            e.preventDefault();

            this.$searchNombre.val('');
            this.$searchState.val('');

            this.professionListSearchTable.ajax.reload();
        },

        // Modal confirm
        onModalConfirm: function (e) {
            e.preventDefault();
            this.professionModel = new app.ProfessionModel();
            this.listenToOnce( this.professionModel, 'request', this.loadSpinner );

            var _this = this,
                action = $(e.currentTarget).attr('data-action'),
                profession_name = $(e.currentTarget).attr('data-name'),
                data = {
                    'codigo_profesion_persona': $(e.currentTarget).data('id'),
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
                        profession_name: profession_name
                    }),
                    titleConfirm: "Confirmación",
                    onConfirm: function () {
                        _this.professionModel.save(data, {wait: true, patch: true,
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
                                        _this.professionListSearchTable.ajax.reload( null, false );
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

})(jQuery, this, this.document);
