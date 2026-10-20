/**
* Class ProjectsListView extends of Backbone View
* @author TuProyecto || Desarrollador : @dortegon
* @link http://TuProyecto.com
*/

//Global App Backbone
app || (app = {});

(function ($, window, document, undefined) {

    app.ProjectsListView = Backbone.View.extend({

        el: '#entity-content',
        template: _.template( ($('#projects-list-tpl').html() || '') ),
        templateConfirm: _.template( ($('#confirm-project-tpl').html() || '') ),
        templateConfirmExportData: _.template( ($('#confirm-export-data-tpl').html() || '') ),
        templateButtonsAction: _.template( ($('#buttons-action-tpl').html() || '') ),
        events: {
            'click .btn-search': 'search',
            'click .btn-clear': 'clear',
            'click .btn-export': 'export_report',
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

            this.configAlert = {
                'wrapper': '#mentoring-wrapper',
                'closeTime': 4000,
                'speedOpen': 500,
                'speedClose': 500
            };

        },

        /*
         * Render View Element
         */
        render: function() {

            var attributes = {};
            this.$el.html( this.template(attributes) );

            // Initialize attributes
            this.$searchState   = this.$('#codigo_estado');
            this.$searchIdentificacion  = this.$('#numero_identificacion');
            this.$searchNombre  = this.$('#nombre_proyecto');
            this.$formFilters   = this.$('#form-projects-filter');
            this.$searchStageProyect  = this.$('#codigo_etapa_proyecto');
            this.$searchCategoryProyect  = this.$('#codigo_categoria_proyecto');
            this.$searchCategoryProyect  = this.$('#codigo_categoria_proyecto');
            this.$projectsListSearchTable = this.$('#projects-list-search-table');
            this.$searchEntrepreneur  = this.$('#correo_electronico_emprendedor_filter_data');

            // overwrite model id attribute
            this.referenceViews();
        },

        /**
         * reference to views
         */
        referenceViews: function () {
            var _this = this;
            this.projectsListSearchTable = this.$projectsListSearchTable.DataTable({
                dom:"<'row'<'col-sm-5'i><'col-sm-7'p>>" +
                "<'row'<'col-sm-12'tr>>" +
                "<'row'<'col-sm-5'i><'col-sm-7'p>>",
                processing: true,
                serverSide: true,
                language: window.Misc.dataTableES(),
                ajax: {
                    url: window.Misc.urlFull( Route.route('entidad.index') ),
                    data: function( data ) {
                        console.log(data);
                        data.identificacion_usuario   = _this.$searchIdentificacion.val();
                        data.nombre_usuario   = _this.$searchNombre.val();
                        data.codigo_etapa_proyecto  = _this.$searchStageProyect.val();
                        data.codigo_categoria_proyecto  = _this.$searchCategoryProyect.val();
                        data.correo_electronico_emprendedor = _this.$searchEntrepreneur.val();
                    },
                },
                columns: [
                    { data: 'identificacion_usuario', name: 'identificacion_usuario' },
                    { data: 'nombre_usuario', name: 'nombre_usuario' },
                    { data: 'nombre_founder', name: 'nombre_founder' },
                    { data: 'nombre_etapa_proyecto', name: 'nombre_etapa_proyecto' },
                    { data: 'nombre_categoria_proyecto', name: 'nombre_categoria_proyecto' }
                ],
                order: [
                    [ 0, 'asc' ]
                ],
                columnDefs: [
                    {
                        targets: 1,
                        render: function ( data, type, full, row ) {
                            return '<a href="' + window.Misc.urlFull( Route.route('entidad.show', {entidad: full.nickname_usuario})) + '" title="Ver proyecto">'+full.nombre_usuario+'</a>';
                        }
                    },
                    {
                        targets: 5,
                       // width: '10%',
                        orderable: false,
                        className: 'text-center',
                        render: function ( data, type, full, row ) {
                            // console.log('Full', full)
                            return _this.templateButtonsAction( {full: full} )
                        }
                    }
                ]
            });

            //para carga de excel, usuario & entidades
            this.uploadEntitiesDocuments = new app.UploadEntitiesDocuments({
				el: this.$('#wrap-documents'),
				//model: this.modelo,
				parameters: {
					//
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
            this.projectsListSearchTable.ajax.reload();
        },

        clear: function(e) {
            e.preventDefault();
            this.$searchIdentificacion.val('');
            this.$searchNombre.val('');
            this.$searchEntrepreneur.val('');
            this.$searchStageProyect.select2('val', '');
            this.$searchCategoryProyect.select2('val', '');

            this.projectsListSearchTable.ajax.reload();
        },

        export_report: function(e) {
            e.preventDefault();
            window.Misc.setSpinner( '.well' );

            var _this = this;

            if( this.acceptConfirm instanceof Backbone.View ) {
                this.acceptConfirm.stopListening();
                this.acceptConfirm.undelegateEvents();
            }

            this.acceptConfirm = new app.ConfirmWindow({
                parameters: {
                    template: this.templateConfirmExportData(),
                    titleConfirm: "Confirmación",
                    onConfirm: function () {
                        console.log('on confirm');
                        $.ajax({
                          //  data: data,
                            url: window.Misc.urlFull( Route.route('entidad.reporte.index')),
                            type: 'get',
                            success: function (data, textStatus, jqXHR ) {
                                // complete proccess
                                window.Misc.removeSpinner({
                                    'wrap': '.well',
                                    'direct': false
                                });
                                
                                const text = data.success ? data.message : data.errors;
                                const type = data.success ? 'success' : 'alert';
                        
                                var config = {
                                    'text': text,
                                    'type': type
                                };
                        
                                _.extend( _this.configAlert, config );
                        
                                window.Misc.showAlertBox( _this.configAlert );
                 
                            },
                            error: function ( XMLHttpRequest, textStatus, errorThrown ) {
        
                                const text = 'Contacte con el administrador';
                                const type = 'alert';
                        
                                var config = {
                                    'text': text,
                                    'type': type
                                };
                        
                                _.extend( _this.configAlert, config );
                        
                                window.Misc.showAlertBox( _this.configAlert );
                            }
        
                        });
                    }
                }
            });

            this.acceptConfirm.render();
        },

        // Modal confirm
        onModalConfirm: function (e) {
            e.preventDefault();
            this.projectModel = new app.ProjectModel();
            this.listenToOnce( this.projectModel, 'request', this.loadSpinner );

            var _this = this,
                action = $(e.currentTarget).attr('data-action'),
                project_name = $(e.currentTarget).attr('data-name'),
                data = {
                    'codigo_usuario': $(e.currentTarget).attr('data-id'),
                    'state': $(e.currentTarget).attr('data-state') == window._tp.getState['active'] ? window._tp.getState['inactive'] : window._tp.getState['active'],
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
                        project_name: project_name
                    }),
                    titleConfirm: "Confirmación",
                    onConfirm: function () {
                        _this.projectModel.save(data, {wait: true, patch: true,
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
                                        _this.projectsListSearchTable.ajax.reload( null, false );
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
