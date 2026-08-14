/**
* Class ReportConvocatoriaView extends of Backbone View
* @author TuProyecto || Desarrollador : @AyrielNoriega
* @link http://TuProyecto.com
*/

//Global App Backbone
app || (app = {});

(function ($, window, document, undefined) {

    app.ReportConvocatoriaView = Backbone.View.extend({

        el: '#report-convocatoria-rutan-wrapper',
        //template: _.template( ($('#projects-list-tpl').html() || '') ),
        //templateConfirm: _.template( ($('#confirm-project-tpl').html() || '') ),
        templateConfirmExportData: _.template( ($('#confirm-export-data-tpl').html() || '') ),
        //templateButtonsAction: _.template( ($('#buttons-action-tpl').html() || '') ),
        events: {
            'submit #form-report-rutan-209': 'export_report',
            'submit #form-report-rutan-200': 'export_report',
            'submit #form-general_convocatoria_ruta_emprendimiento' : 'export_report',
            'submit #form-report-mentorias' : 'export_report',
            'submit #form-report-sessions-rutan' : 'export_report',


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
            //this.$el.html( this.template(attributes) );

            // Initialize attributes
            /*this.$searchState   = this.$('#codigo_estado');
            this.$searchIdentificacion  = this.$('#numero_identificacion');
            this.$searchNombre  = this.$('#nombre_proyecto');
            this.$formFilters   = this.$('#form-projects-filter');
            this.$searchStageProyect  = this.$('#codigo_etapa_proyecto');
            this.$searchCategoryProyect  = this.$('#codigo_categoria_proyecto');
            this.$searchCategoryProyect  = this.$('#codigo_categoria_proyecto');
            this.$projectsListSearchTable = this.$('#projects-list-search-table');
            this.$searchEntrepreneur  = this.$('#correo_electronico_emprendedor_filter_data');
            */
            // overwrite model id attribute
            this.referenceViews();
        },

        /**
         * reference to views
         */
        referenceViews: function () {
            var _this = this;
          
        },

        /**
        * Load spinner on the request
        */
        loadSpinner: function () {
           // window.Misc.setSpinner( this.$gridSearchTable );
        },

        search: function(e) {
            e.preventDefault();
           // this.projectsListSearchTable.ajax.reload();
        },

        clear: function(e) {
            e.preventDefault();
           /* this.$searchIdentificacion.val('');
            this.$searchNombre.val('');
            this.$searchEntrepreneur.val('');
            this.$searchStageProyect.select2('val', '');
            this.$searchCategoryProyect.select2('val', '');

            this.projectsListSearchTable.ajax.reload();*/
        },

        export_report: function(e) {
            console.log('Backbone metodo export_report');

            e.preventDefault();
            var current_id = e.target.id;
            window.Misc.setSpinner( '#'+current_id );

            var _this = this;

            if( this.acceptConfirm instanceof Backbone.View ) {
                this.acceptConfirm.stopListening();
                this.acceptConfirm.undelegateEvents();
                console.log('Backbone if export_report');
            }
            var data = window.Misc.formToJson( e.target )

            this.acceptConfirm = new app.ConfirmWindow({
                parameters: {
                    template: this.templateConfirmExportData({id_report: data.id_report}),
                    titleConfirm: "Confirmación",
                    onConfirm: function () {
                        $.ajax({
                            data: data,
                            url: window.Misc.urlFull( Route.route('reportes.rutan.convocatorias.store')),
                            type: 'post',
                            success: function (data, textStatus, jqXHR ) {
                                // complete proccess
                                window.Misc.removeSpinner({
                                    'wrap': '#'+current_id,
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
                                console.log('Backbone success');
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
                                console.log('Backbone error');
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

})(jQuery, this, this.document);
