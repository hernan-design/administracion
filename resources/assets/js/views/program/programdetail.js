/**
 * Class ProgramDetailView extends of Backbone View
 * @author TuProyecto || Desarrolladora : @xindykatalina
 * @link http://TuProyecto.com
 */

//Global App Backbone
app || (app = {});

(function ($, window, document, undefined) {

    app.ProgramDetailView = Backbone.View.extend({
        el: '#programs-content',
        template: _.template( ($('#program-detail-tpl').html() || '') ),
        templateAldea: _.template( ($('#program-participants-tpl').html() || '') ),
        templateAldeaEmpodera: _.template( ($('#program-participants-empodera-tpl').html() || '') ),
        templateSpecializedService: _.template( ($('#program-user-specialized-service-advisors-tpl').html() || '') ),
        templateProgramUser: _.template( ($('#program-user-tpl').html() || '') ),
        templateCoursesPropace: _.template( ($('#program-course-propace-tpl').html() || '' ) ),
        templateProgramMentoresProvedoresUser: _.template( ($('#program-mentores-provedores-user-tpl').html() || '') ),
        templateProgramEntidadProvedoresUser: _.template( ($('#program-entity-provedores-tpl').html() || '') ),
        templateProgramEntity: _.template( ($('#program-entity-tpl').html() || '') ),
        templateTerpel20: _.template( ($('#program-terpel-20-tpl').html() || '') ),
        templateProgramEmpodera2020: _.template( ($('#program-empodera-2020-tpl').html() || '') ),
        templateProgramAldea20: _.template( ($('#program-aldea-20-tpl').html() || '') ),
        templateProgramAppsCo: _.template( ($('#program-apps-co-tpl').html() || '') ),
        templateProgramRegister: _.template( ($('#program-register-tpl').html() || '') ),
        templateConfirm: _.template( ($('#turn-confirm-tpl').html() || '') ),
        templateError: _.template( ($('#turn-error-tpl').html() || '') ),
        templateButtonsAction: _.template( ($('#buttons-action-tpl').html() || '') ),
        templateButtonsActionDefault: _.template( ($('#buttons-action-default-tpl').html() || '') ),
        templateButtonsActionRutan: _.template( ($('#buttons-action-rutan-tpl').html() || '') ),
        templateButtonsActionSiinova: _.template( ($('#buttons-action-siinova-tpl').html() || '') ),
        templateButtonsActionAldea20: _.template( ($('#buttons-action-aldea20-tpl').html() || '') ),
        templateButtonsActionProgramAppsCo: _.template( ($('#buttons-action-program-apps-co-tpl').html() || '') ),
        templateButtonsActionEmpodera20: _.template( ($('#buttons-action-empodera20-tpl').html() || '') ),
        templateButtonsActionEntidadProvedor: _.template( ($('#buttons-action-entidad-provedor-tpl').html() || '') ),
        templateButtonsActionDinamiza: _.template( ($('#buttons-action-dinamiza-tpl').html() || '') ),
        templateButtonsSpecializedService: _.template( ($('#buttons-action-specialized-service-tpl').html() || '') ),
        templateConfirmForwardFourChallenge: _.template( ($('#forward_email_four_challenge-confirm-tpl').html() || '') ),
        templateConfirmCancel: _.template( ($('#cancel-confirm-tpl').html() || '') ),
        events: {
            'click .btn-search': 'search',
            'click .btn-clear': 'clear',
            'click .btn-clear-appsco': 'clearAppsCo',
            'click .data-export': 'onFilterExport',
            'click .data-export-no-complete': 'onFilterExportNoComplete',
            'click .confirm-turn': 'onConfirm',
            'click #history-back': 'onBack',
            'click .btn-search-empodera': 'searchEmpodera',
            'click .btn-clear-empodera': 'clearEmpodera',
            'click .btn-clear-aldea20': 'clearAldea20',
            'click .forward_email_four_challenge': 'onForwardFourChallenge',
            'click .slc': 'onSelectAllItems',
            'click .btn-search-aldea' : 'searchAldea',
            'click .btn-search-aldea_20': 'searchAldea20',
            'click .btn-notify' : 'onNotifyMultiple',
            'click .btn-notify-empodera-2020': 'onNotifyEmpodera2020',
            'click .review__response__evaluation': 'onReviewReponse',
            'click .cancel': 'onCancel',
        },
        parameters: {
            dataFilter: {},
            tab: 'inscritos'
        },

        /**
         * Constructor Method
         */
        initialize : function(opts) {
            _.bindAll(this, 'beforeRender', 'render', 'afterRender');

            // extends parameters
            if(!_.isUndefined(opts) && _.isObject(opts.parameters))
                this.parameters = $.extend({}, this.parameters, opts.parameters);

            //Init Attribute

            var _this = this;
            this.render = _.wrap(this.render, function(render) {

                _this.trigger('beforeRender');

                render();

                _this.trigger('afterRender');

                return _this;
            });

            //Init Attribute
            this.configAlert = {
                'wrapper': '#main-wrap',
                'closeTime': 6000,
                'speedOpen': 500,
                'speedClose': 500
            };

            // Events Listener
            this.listenTo( this.model, 'change', this.render );
            this.listenTo( this.model, 'sync', this.responseServer );

            // Events Listener custome
            this.on( 'beforeRender', this.beforeRender );
            this.on( 'afterRender', this.afterRender );

        },

        /*
        * Render View Element
        */
        render: function() {

            var attributes = this.model.toJSON();
            attributes.dataFilter = this.parameters['dataFilter'];
            this.$el.html( this.template(attributes) );

            // overwrite model id attribute
            if( this.model.changed.codigo_convocatoria !== undefined ) {
                // Overwrite id attribute of the model
                this.model.idAttribute = 'codigo_convocatoria';
                this.model.set( {'codigo_convocatoria': this.model.get('codigo_convocatoria')}, {'silent':true} );
            }

            if (this.model.get('codigo_orientacion_convocatoria') == window._tp.orientationProgram['user']) {
                switch(this.model.get('codigo_convocatoria')) {
                    case window._tp.getProgram['register']:
                        this.$('#grid-container').html( this.templateProgramRegister( this.model.toJSON()) );
                        this.$searchordenCity = this.$('#city');
                        this.$searchordenPhase = this.$('#phase');
                        this.$searchordenEmail = this.$('#email');
                        this.$searchordenNumber = this.$('#number_register');
                        this.$searchordenEntrepreneurship = this.$('#emprendimiento');
                        this.renderProgramRegister();
                        break;

                    case window._tp.getProgram['specialized_service_advisors']:
                        this.$('#grid-container').html( this.templateSpecializedService( this.model.toJSON()) );
                        this.$searchordenEmail = this.$('#email');
                        this.$searchordenPhase = this.$('#phase');
                        this.$searchordenNumber = this.$('#number_register');
                        this.$searchordenCity = this.$('#city');
                        this.renderSpecializedService();
                        break;

                    case window._tp.getProgram['reg_mentores_provedores']:
                        this.$('#grid-container').html( this.templateProgramMentoresProvedoresUser( this.model.toJSON()) );
                        this.$searchordenEmail = this.$('#email');
                        this.$searchordenPhase = this.$('#phase');
                        this.$searchordenNumber = this.$('#number_register');
                        this.$searchordenCity = this.$('#city');
                        this.renderProgramMentoresProvedores();
                        break;

                    // Program Courses

                    case window._tp.getProgram['courses_propace']:
                        this.$('#grid-container').html( this.templateCoursesPropace( this.model.toJSON()) );
                        this.$searchordenEmail = this.$('#email');
                        this.$searchordenPhase = this.$('#phase');
                        this.$searchordenNumber = this.$('#number_register');
                        this.$searchordenCity = this.$('#city');
                        this.renderProgramCoursePropace();
                    break;

                    case window._tp.getProgram['courses_propace_two']:
                        this.$('#grid-container').html( this.templateCoursesPropace( this.model.toJSON()) );
                        this.$searchordenEmail = this.$('#email');
                        this.$searchordenPhase = this.$('#phase');
                        this.$searchordenNumber = this.$('#number_register');
                        this.$searchordenCity = this.$('#city');
                        this.renderProgramCoursePropace();
                    break;

                    case window._tp.getProgram['program_apps_co']:
                        this.$('#grid-container').html( this.templateProgramAppsCo( this.model.toJSON()) );
                        this.$searchordenEmail = this.$('#email');
                        this.$searchordenPhase = this.$('#phase');
                        this.$searchordenNumber = this.$('#number_register');
                        this.$searchordenCity = this.$('#city');
                        this.renderProgramAppsCO();
                    break;

                    default:
                        this.$('#grid-container').html( this.templateProgramUser( this.model.toJSON()) );
                        this.$searchordenEmail = this.$('#email');
                        this.$searchordenPhase = this.$('#phase');
                        this.$searchordenNumber = this.$('#number_register');
                        this.$searchordenCity = this.$('#city');
                        this.$searchIdentificacion = this.$('#identificacion');
                        this.renderProgramUser();
                        break;
                }
            }else {
                switch(this.model.get('codigo_convocatoria')) {
                    case window._tp.getProgram['invitation_entrepreneurs']:
                        this.$('#grid-container').html( this.templateAldea( this.model.toJSON()) );
                        this.$searchordenEmail = this.$('#email');
                        this.$searchordenPhase = this.$('#phase');
                        this.$searchordenNumber = this.$('#number_register');
                        this.$searchordenEntrepreneurship = this.$('#emprendimiento');
                        this.$searchordenNotified = this.$('#visualizacion');
                        this.$searchordenSortby = this.$('#sortby');
                        this.$searchordenScoring = this.$('#scoring');
                        this.$searchordenSortby_2 = this.$('#sortby_2');
                        this.$searchordenScoring_2 = this.$('#scoring_2');
                        this.renderAldea();
                        break;
                    case window._tp.getProgram['empodera']:
                        this.$('#grid-container').html( this.templateAldeaEmpodera( this.model.toJSON()) );
                        this.$searchordenEmail = this.$('#email');
                        this.$searchordenPhase = this.$('#phase');
                        this.$searchordenNumber = this.$('#number_register');
                        this.$searchordenEntrepreneurship = this.$('#emprendimiento');
                        this.$searchordenNotified = this.$('#visualizacion');
                        this.$searchordenSortby = this.$('#sortby');
                        this.$searchordenScoring = this.$('#scoring');
                        this.$searchordenSortby_2 = this.$('#sortby_2');
                        this.$searchordenScoring_2 = this.$('#scoring_2');
                        this.renderAldeaEmpodera();
                        break;

                    case window._tp.getProgram['empodera__2020']:
                        this.$('#grid-container').html( this.templateProgramEmpodera2020( this.model.toJSON()) );
                        this.$searchordenNumber = this.$('#number_register');
                        this.$searchordenEmail = this.$('#email');
                        this.$searchordenEntrepreneurship = this.$('#emprendimiento');
                        this.$searchordenCity = this.$('#city');
                        this.$searchordenPhase = this.$('#phase');
                        this.$searchordenState = this.$('#state');
                        this.renderEmpodera2020();
                    break;
                    case window._tp.getProgram['reg_entidades_provedores']:
                        this.$('#grid-container').html( this.templateProgramEntidadProvedoresUser( this.model.toJSON()) );
                        this.$searchordenNumber = this.$('#number_register');
                        this.$searchordenEmail = this.$('#email');
                        this.$searchordenEntrepreneurship = this.$('#emprendimiento');
                        this.$searchordenCity = this.$('#city');
                        this.renderProgramEntidadesProvedores();
                        break;
                    case window._tp.getProgram['aldea__20']:
                        this.$('#grid-container').html( this.templateProgramAldea20( this.model.toJSON()) );
                        this.$searchordenEmail = this.$('#email');
                        this.$searchordenPhase = this.$('#phase');
                        this.$searchordenNumber = this.$('#number_register');
                        this.$searchordenEntrepreneurship = this.$('#emprendimiento');
                        this.$searchordenNotified = this.$('#visualizacion');
                        this.$searchordenSortby = this.$('#sortby');
                        this.$searchordenScoring = this.$('#scoring');
                        this.$searchordenSortby_2 = this.$('#sortby_2');
                        this.$searchordenScoring_2 = this.$('#scoring_2');
                        this.renderAldea20();
                        break;
                    case window._tp.getProgram['terpel__20']:
                        this.$('#grid-container').html( this.templateTerpel20( this.model.toJSON()) );
                        this.$searchordenNumber = this.$('#number_register');
                        this.$searchordenEmail = this.$('#email');
                        this.$searchordenEntrepreneurship = this.$('#emprendimiento');
                        this.$searchordenCity = this.$('#city');
                        this.$searchordenPhase = this.$('#phase');
                        this.renderProgramTerpel20();
                    break;
                    case window._tp.getProgram['ruta_emprendimiento']:
                        this.$('#grid-container').html( this.templateProgramEntity( this.model.toJSON()) );
                        this.$searchordenNumber = this.$('#number_register');
                        this.$searchordenEmail = this.$('#email');
                        this.$searchordenEntrepreneurship = this.$('#emprendimiento');
                        this.$searchordenPhase = this.$('#phase');
                        this.renderProgramEntityRutan();
                        break;
                    case window._tp.getProgram['diagnostico_siinova']:
                        this.$('#grid-container').html( this.templateProgramEntity( this.model.toJSON()) );
                        this.$searchordenNumber = this.$('#number_register');
                        this.$searchordenEmail = this.$('#email');
                        this.$searchordenEntrepreneurship = this.$('#emprendimiento');
                        this.$searchordenPhase = this.$('#phase');
                        this.renderDiagnosisSiinova();
                        break;
                    default:
                        this.$('#grid-container').html( this.templateProgramEntity( this.model.toJSON()) );
                        this.$searchordenNumber = this.$('#number_register');
                        this.$searchordenEmail = this.$('#email');
                        this.$searchordenEntrepreneurship = this.$('#emprendimiento');
                        this.$searchordenPhase = this.$('#phase');
                        this.$searchIdentificacion = this.$('#identificacion');
                        this.renderProgramEntity();
                        break;
                }
            }
        },

        /**
         * Fires before of run render function
         */
        beforeRender: function () {
            //
        },

        /**
         * Fires before of run render function
         */
        afterRender: function () {

            this.ready();
        },

        /**
         * fires libraries js
         */
        ready: function () {

            var _this = this;

            // reload plugins
            if(typeof window.initComponent.select2Trigger == 'function' )
                window.initComponent.select2Trigger();

            if( typeof(window.initComponent.datepickerTrigger) == 'function')
                window.initComponent.datepickerTrigger();
        },

        /**
         * reference to views
         */
        renderAldea: function () {
            var _this = this;
            var selector = 'td:last-child';
            this.$gridSearchTable = this.$('#participants-search-table');
            this.gridSearchTable = this.$gridSearchTable.DataTable({
                // "initComplete": function(e, dt, button, config) {
                //     dt.ajax.reload()
                // },
                dom: "<'row'<'col-sm-5'l><'col-sm-7'p>>" +
                "<'row'<'col-sm-12'tr>>" + "<'row'<'col-sm-11'i><'col-sm-1'>>",
                "lengthMenu": [[15, 25, 50, 100], [15, 25, 50, 100]],
                processing: true,
                serverSide: true,
                language: window.Misc.dataTableES(),
                searching: true,
                ajax: {
                    url: window.Misc.urlFull( Route.route('convocatoria.usuario.index') ),
                    data: function( data ) {
                        data.program = _this.model.get('codigo_convocatoria');
                        data.emprendimiento = _this.$searchordenEntrepreneurship.val();
                        data.email = _this.$searchordenEmail.val();
                        data.phase = _this.$searchordenPhase.val();
                        data.visualizacion = _this.$searchordenNotified.val();
                        data.sortby = _this.$searchordenSortby.val();
                        data.scoring = _this.$searchordenScoring.val();
                        data.number_register = _this.$searchordenNumber.val();
                        data.sortby_2 = _this.$searchordenSortby_2.val();
                        data.scoring_2 = _this.$searchordenScoring_2.val();
                    }
                },
                columns: [
                    { data: 'numero_registro_convocatoria', name: 'numero_registro_convocatoria' },
                    { data: 'nombre_entidad', name: 'nombre_entidad' },
                    { data: 'nombre_usuario', name: 'nombre_usuario' },
                    { data: 'fecha_registro_fase_usuario', name: 'fecha_registro_fase_usuario' },
                    { data: 'nombre_fase', name: 'nombre_fase' },
                    { data: 'nombre_estado', name: 'nombre_estado' },
                    { data: 'total_evaluation', name: 'total_evaluation' },
                    { data: 'codigo_registro_fase_usuario', name: 'codigo_registro_fase_usuario' },
                    { data: 'seleccion', name: 'seleccion' }
                ],
                order: [
                    [ 0, 'asc' ]
                ],
                columnDefs: [
                    {
                        targets: 0,
                        width: '10%'
                    },
                    {
                        targets: 2,
                        render: function ( data, type, full, row ) {
                            return data + ' <br>' + full.correo_electronico;
                        }
                    },
                    {
                        targets: 7,
                        orderable: false,
                        className: 'text-center',
                        render: function ( data, type, full, row ) {
                            if( full.visualizacion_usuario ) {
                                return '<a href="#" title="Desactivar notificación" class="btn btn-default btn-xs confirm-turn" data-turn="false" data-register="'+full.codigo_registro_fase_usuario+'" data-scoring="'+full.total_evaluation+'" > <i class="fa fa-toggle-on"></i></a>';
                            }else {
                                return '<a href="#" title="Activar notificación" class="btn btn-default btn-xs confirm-turn" data-turn="true" data-register="'+full.codigo_registro_fase_usuario+'" data-scoring="'+full.total_evaluation+'"> <i class="fa fa-toggle-off"></i></a>';
                            }
                        }
                    },
                    {
                        targets: 8,
                        orderable: false,
                        className: 'text-center',
                        render: function ( data, type, full, row ) {
                            return _this.templateButtonsAction( {full: full} );
                        }
                    },
                    {
                        targets: 9,
                        orderable: false,
                        "visible": false,
                        className: 'select-checkbox',
                        render: function ( data, type, full, row ) {
                            return '';
                        }
                    }
                ]
            });
        },

        onSelectAllItems: function (e) {
            if($(e.target).is(':checked')){
                this.gridSearchTable.rows().select();
                this.dataSelection = this.gridSearchTable.rows().data();
            } else {
                this.gridSearchTable.rows().deselect();
                this.dataSelection = this.gridSearchTable.rows().data();
            }
        },

        renderEmpodera2020: function () {
            var _this = this;
            this.$gridSearchTable = this.$('#participants-search-table-empodera-2020');
            this.gridSearchTable = this.$gridSearchTable.DataTable({
                dom: "<'row'<'col-sm-5'i><'col-sm-7'p>>" +
                    "<'row'<'col-sm-12'tr>>" +
                    "<'row'<'col-sm-5'i><'col-sm-7'p>>",
                processing: true,
                serverSide: true,
                language: window.Misc.dataTableES(),
                searching: false,
                ajax: {
                    url: window.Misc.urlFull( Route.route('convocatoria.usuario.index') ),
                    data: function( data ) {
                        data.program = _this.model.get('codigo_convocatoria');
                        data.number_register = _this.$searchordenNumber.val();
                        data.email = _this.$searchordenEmail.val();
                        data.emprendimiento = _this.$searchordenEntrepreneurship.val();
                        data.city = _this.$searchordenCity.val();
                        data.phase = _this.$searchordenPhase.val();
                        data.state = _this.$searchordenState.val();
                    }
                },
                columns: [
                    { data: 'numero_registro_convocatoria', name: 'numero_registro_convocatoria' },
                    { data: 'nombre_entidad', name: 'nombre_entidad' },
                    { data: 'nombre_usuario', name: 'nombre_usuario' },
                    { data: 'correo_electronico', name: 'correo_electronico' },
                    { data: 'nombre_ciudad', name: 'nombre_ciudad' },
                    { data: 'nombre_estado', name: 'nombre_estado' },
                    { data: 'nombre_fase', name: 'nombre_fase' },
                    { data: 'fecha_registro_fase_usuario', name: 'fecha_registro_fase_usuario' },
                    { data: 'puntaje', name: 'puntaje'},
                    { data: 'seleccion', name: 'seleccion' }
                ],
                order: [
                    [ 0, 'asc' ]
                ],
                columnDefs: [
                    {
                        targets: 0,
                        width: '10%'
                    },
                    {
                        targets: 9,
                        orderable: false,
                        className: 'text-center',
                        render: function ( data, type, full, row ) {

                            if( full.visualizacion_usuario ) {
                                return '<a href="#" title="Desactivar notificación" class="btn btn-default btn-xs confirm-turn" data-turn="false" data-register="'+full.codigo_registro_fase_usuario+'" data-scoring="'+full.total_evaluation+'" > <i class="fa fa-toggle-on"></i></a>';
                            }else {
                                return '<a href="#" title="Activar notificación" class="btn btn-default btn-xs confirm-turn" data-turn="true" data-register="'+full.codigo_registro_fase_usuario+'" data-scoring="'+full.total_evaluation+'"> <i class="fa fa-toggle-off"></i></a>';
                            }
                        }
                    },
                    {
                        targets: 10,
                        orderable: false,
                        render: function ( data, type, full, row ) {
                            console.log('Full Data', full);
                            return _this.templateButtonsActionEmpodera20( {full: full} );
                        }
                    },
                    {
                        targets: 11,
                        orderable: false,
                        "visible": true,
                        className: 'select-checkbox',
                        render: function ( data, type, full, row ) {
                            return '';
                        }
                    }
                ]
            });
        },

        renderAldea20: function () {
            var _this = this;
            this.$gridSearchTable = this.$('#participants-search-table-aldea-20');
            this.gridSearchTable = this.$gridSearchTable.DataTable({
                dom: "<'row'<'col-sm-5'l><'col-sm-7'p>>" +
                "<'row'<'col-sm-12'tr>>" + "<'row'<'col-sm-11'i><'col-sm-1'>>",
                "lengthMenu": [[15, 25, 50, 100], [15, 25, 50, 100]],
                processing: true,
                serverSide: true,
                language: window.Misc.dataTableES(),
                searching: false,
                ajax: {
                    url: window.Misc.urlFull( Route.route('convocatoria.usuario.index') ),
                    data: function( data ) {
                        data.program = _this.model.get('codigo_convocatoria');
                        data.emprendimiento = _this.$searchordenEntrepreneurship.val();
                        data.email = _this.$searchordenEmail.val();
                        data.phase = _this.$searchordenPhase.val();
                        data.visualizacion = _this.$searchordenNotified.val();
                        data.sortby = _this.$searchordenSortby.val();
                        data.scoring = _this.$searchordenScoring.val();
                        data.number_register = _this.$searchordenNumber.val();
                        data.sortby_2 = _this.$searchordenSortby_2.val();
                        data.scoring_2 = _this.$searchordenScoring_2.val();
                    }
                },
                columns: [
                    { data: 'numero_registro_convocatoria', name: 'numero_registro_convocatoria' },
                    { data: 'nombre_entidad', name: 'nombre_entidad' },
                    { data: 'nombre_usuario', name: 'nombre_usuario' },
                    { data: 'correo_electronico', name: 'correo_electronico' },
                    { data: 'fecha_registro_fase_usuario', name: 'fecha_registro_fase_usuario' },
                    { data: 'nombre_fase', name: 'nombre_fase' },
                    { data: 'nombre_estado', name: 'nombre_estado' },
                    { data: 'puntaje', name: 'puntaje'},
                    { data: 'codigo_registro_fase_usuario', name: 'codigo_registro_fase_usuario' },
                    { data: 'seleccion', name: 'seleccion' }
                ],
                order: [
                    [ 0, 'asc' ]
                ],
                columnDefs: [
                    {
                        targets: 0,
                        width: '10%'
                    },
                    {
                        targets: 8,
                        orderable: false,
                        className: 'text-center',
                        render: function ( data, type, full, row ) {
                            if( full.visualizacion_usuario ) {
                                return '<a href="#" title="Desactivar notificación" class="btn btn-default btn-xs confirm-turn" data-turn="false" data-register="'+full.codigo_registro_fase_usuario+'" data-scoring="'+full.total_evaluation+'" > <i class="fa fa-toggle-on"></i></a>';
                            }else {
                                return '<a href="#" title="Activar notificación" class="btn btn-default btn-xs confirm-turn" data-turn="true" data-register="'+full.codigo_registro_fase_usuario+'" data-scoring="'+full.total_evaluation+'"> <i class="fa fa-toggle-off"></i></a>';
                            }
                        }
                    },
                    {
                        targets: 9,
                        orderable: false,
                        className: 'text-center',
                        render: function ( data, type, full, row ) {
                            console.log('Full Data', full);
                            return _this.templateButtonsActionAldea20( {full: full} );
                        }
                    },
                    {
                        targets: 10,
                        orderable: false,
                        "visible": false,
                        className: 'select-checkbox',
                        render: function ( data, type, full, row ) {
                            return '';
                        }
                    }
                ]
            });
        },

        renderProgramAppsCO: function () {
            var _this = this;
            this.$gridSearchTable = this.$('#participants-search-table-program-apps-co');
            this.gridSearchTable = this.$gridSearchTable.DataTable({
                dom: "<'row'<'col-sm-5'l><'col-sm-7'p>>" +
                "<'row'<'col-sm-12'tr>>" + "<'row'<'col-sm-11'i><'col-sm-1'>>",
                "lengthMenu": [[15, 25, 50, 100], [15, 25, 50, 100]],
                processing: true,
                serverSide: true,
                language: window.Misc.dataTableES(),
                searching: false,
                ajax: {
                    url: window.Misc.urlFull( Route.route('convocatoria.usuario.index') ),
                    data: function( data ) {
                        data.program = _this.model.get('codigo_convocatoria');
                        data.email = _this.$searchordenEmail.val();
                        data.phase = _this.$searchordenPhase.val();
                        data.number_register = _this.$searchordenNumber.val();
                        data.city = _this.$searchordenCity.val();
                    }
                },
                columns: [
                    { data: 'numero_registro_convocatoria', name: 'numero_registro_convocatoria' },
                    { data: 'nombre_usuario', name: 'nombre_usuario' },
                    { data: 'correo_electronico', name: 'correo_electronico' },
                    { data: 'nombre_ciudad', name: 'nombre_ciudad' },
                    { data: 'fecha_registro_fase_usuario', name: 'fecha_registro_fase_usuario' },
                    { data: 'nombre_fase', name: 'nombre_fase' },
                    { data: 'puntaje', name: 'puntaje'},
                    { data: 'seleccion', name: 'seleccion' }
                ],
                order: [
                    [ 0, 'asc' ]
                ],
                columnDefs: [
                    {
                        targets: 0,
                        width: '10%'
                    },
                    {
                        targets: 7,
                        orderable: false,
                        className: 'text-center',
                        render: function ( data, type, full, row ) {
                            //console.log('Full Data', full);
                            return _this.templateButtonsActionProgramAppsCo( {full: full} );
                        }
                    }
                ]
            });
        },

        onReviewReponse: function(e) {
            e.preventDefault();
            var code_user = $(e.currentTarget).data('user'),
                code_register = $(e.currentTarget).data('register'),
                program = this.model.toJSON().codigo_convocatoria;
            window.Misc.redirect( app.AppRouter.navigate( Route.route('convocatoria.respuestas.evaluacion.show', {'evaluacion': code_user, 'program': program, 'register': code_register}) ) );
        },


        renderAldeaEmpodera: function () {
            var _this = this;
            this.$gridSearchTableEmpodera = this.$('#participants-empodera-search-table');
            this.gridSearchTableEmpodera = this.$gridSearchTableEmpodera.DataTable({
                dom:"<'row'<'col-sm-5'i><'col-sm-7'p>>" +
                    "<'row'<'col-sm-12'tr>>" +
                    "<'row'<'col-sm-5'i><'col-sm-7'p>>",
                processing: true,
                serverSide: true,
                language: window.Misc.dataTableES(),
                searching: false,
                ajax: {
                    url: window.Misc.urlFull( Route.route('convocatoria.usuario.index') ),
                    data: function( data ) {
                        data.program = _this.model.get('codigo_convocatoria');
                        data.emprendimiento = _this.$searchordenEntrepreneurship.val();
                        data.email = _this.$searchordenEmail.val();
                        data.phase = _this.$searchordenPhase.val();
                        data.visualizacion = _this.$searchordenNotified.val();
                        data.sortby = _this.$searchordenSortby.val();
                        data.scoring = _this.$searchordenScoring.val();
                        data.number_register = _this.$searchordenNumber.val();
                        data.sortby_2 = _this.$searchordenSortby_2.val();
                        data.scoring_2 = _this.$searchordenScoring_2.val();
                    }
                },
                columns: [
                    { data: 'numero_registro_convocatoria', name: 'numero_registro_convocatoria' },
                    { data: 'nombre_entidad', name: 'nombre_entidad' },
                    { data: 'nombre_usuario', name: 'nombre_usuario' },
                    { data: 'correo_electronico', name: 'correo_electronico' },
                    { data: 'fecha_registro_fase_usuario', name: 'fecha_registro_fase_usuario' },
                    { data: 'nombre_fase', name: 'nombre_fase' },
                    { data: 'nombre_estado', name: 'nombre_estado' },
                    { data: 'total_evaluation', name: 'total_evaluation' },
                    { data: 'codigo_registro_fase_usuario', name: 'codigo_registro_fase_usuario' }
                ],
                order: [
                    [ 0, 'asc' ]
                ],
                columnDefs: [
                    {
                        targets: 0,
                        width: '10%'
                    },
                    {
                        targets: 8,
                        orderable: false,
                        className: 'text-center',
                        render: function ( data, type, full, row ) {

                            if( full.visualizacion_usuario ) {
                                return '<a href="#" title="Desactivar notificación" class="btn btn-default btn-xs confirm-turn" data-turn="false" data-register="'+full.codigo_registro_fase_usuario+'" data-scoring="'+full.total_evaluation+'" > <i class="fa fa-toggle-on"></i></a>';
                            }else {
                                return '<a href="#" title="Activar notificación" class="btn btn-default btn-xs confirm-turn" data-turn="true" data-register="'+full.codigo_registro_fase_usuario+'" data-scoring="'+full.total_evaluation+'"> <i class="fa fa-toggle-off"></i></a>';
                            }
                        }
                    },
                    {
                        targets: 9,
                        orderable: false,
                        className: 'text-center',
                        render: function ( data, type, full, row ) {
                            return _this.templateButtonsAction( {full: full} );
                        }
                    }
                ]
            });
        },

        /**
         * render program user
         */
        renderProgramUser: function (data) {
            //console.log('renderProgramUser')
            var _this = this;

            this.$gridSearchTable = this.$('#program-user-search-table');
            this.gridSearchTable = this.$gridSearchTable.DataTable({
                dom:"<'row'<'col-sm-5'i><'col-sm-7'p>>" +
                    "<'row'<'col-sm-12'tr>>" +
                    "<'row'<'col-sm-5'i><'col-sm-7'p>>",
                processing: true,
                serverSide: true,
                language: window.Misc.dataTableES(),
                searching: false,
                ajax: {
                    url: window.Misc.urlFull( Route.route('convocatoria.usuario.index') ),
                    data: function( data ) {
                        data.program = _this.model.get('codigo_convocatoria');
                        data.email = _this.$searchordenEmail.val();
                        data.phase = _this.$searchordenPhase.val();
                        data.number_register = _this.$searchordenNumber.val();
                        data.city = _this.$searchordenCity.val();
                        data.identificacion = _this.$searchIdentificacion.val();
                    }
                },
                columns: [
                    { data: 'numero_registro_convocatoria', name: 'numero_registro_convocatoria' },
                    { data: 'identificacion_usuario', name: 'identificacion_usuario' },
                    { data: 'nombre_usuario', name: 'nombre_usuario' },
                    { data: 'correo_electronico', name: 'correo_electronico' },
                    { data: 'nombre_ciudad', name: 'nombre_ciudad' },
                    { data: 'fecha_registro_fase_usuario', name: 'fecha_registro_fase_usuario' },
                    { data: 'nombre_fase', name: 'nombre_fase' }
                ],
                order: [
                    [ 0, 'asc' ]
                ],
                columnDefs: [
                    {
                        targets: 0,
                        width: '10%'
                    },
                    {
                        targets: 7,
                        orderable: false,
                        className: 'text-center',
                        render: function ( data, type, full, row ) {
                            //console.log('Full Data renderProgramUser: ', full);
                            if(full.nombre_fase != 'Formación'){
                                if( full.visualizacion_usuario ) {
                                    return '<a href="#" title="Desactivar notificación" class="btn btn-default btn-xs confirm-turn" data-turn="false" data-register="'+full.codigo_registro_fase_usuario+'" data-scoring="'+full.total_evaluation+'" > <i class="fa fa-toggle-on"></i></a>';
                                }else {
                                    return '<a href="#" title="Activar notificación" class="btn btn-default btn-xs confirm-turn" data-turn="true" data-register="'+full.codigo_registro_fase_usuario+'" data-scoring="'+full.total_evaluation+'"> <i class="fa fa-toggle-off"></i></a>';
                                }
                            }else {
                                return '<a title="Activar notificación" class="btn btn-default btn-xs" disabled data-turn="true" data-register="'+full.codigo_registro_fase_usuario+'" data-scoring="'+full.total_evaluation+'"> <i class="fa fa-toggle-off"></i></a>';
                            }

                        }
                    },
                ]
            });
        },

        /**
         * render program user
         */
        renderProgramCoursePropace: function (data) {
            var _this = this;
            //console.log('renderProgramCoursePropace')
            this.$gridSearchTable = this.$('#program-user-search-table');
            this.gridSearchTable = this.$gridSearchTable.DataTable({
                dom:"<'row'<'col-sm-5'i><'col-sm-7'p>>" +
                    "<'row'<'col-sm-12'tr>>" +
                    "<'row'<'col-sm-5'i><'col-sm-7'p>>",
                processing: true,
                serverSide: true,
                language: window.Misc.dataTableES(),
                searching: false,
                ajax: {
                    url: window.Misc.urlFull( Route.route('convocatoria.usuario.index') ),
                    data: function( data ) {
                        data.program = _this.model.get('codigo_convocatoria');
                        data.email = _this.$searchordenEmail.val();
                        data.phase = _this.$searchordenPhase.val();
                        data.number_register = _this.$searchordenNumber.val();
                        data.city = _this.$searchordenCity.val();
                    }
                },
                columns: [
                    { data: 'numero_registro_convocatoria', name: 'numero_registro_convocatoria' },
                    { data: 'identificacion_usuario', name: 'identificacion_usuario' },
                    { data: 'nombre_usuario', name: 'nombre_usuario' },
                    { data: 'correo_electronico', name: 'correo_electronico' },
                    { data: 'nombre_ciudad', name: 'nombre_ciudad' },
                    { data: 'fecha_registro_fase_usuario', name: 'fecha_registro_fase_usuario' },
                    { data: 'nombre_fase', name: 'nombre_fase' }
                ],
                order: [
                    [ 0, 'asc' ]
                ],
                columnDefs: [
                    {
                        targets: 0,
                        width: '10%'
                    },
                    {
                        targets: 7,
                        orderable: false,
                        className: 'text-center',
                        render: function ( data, type, full, row ) {
                            /*console.log('Full Data renderProgramCoursePropace', full);
                            console.log('data: ', data);*/
                            if( full.visualizacion_usuario ) {
                                return '<a href="#" title="Desactivar notificación" class="btn btn-default btn-xs confirm-turn" data-turn="false" data-register="'+full.codigo_registro_fase_usuario+'" data-scoring="'+full.total_evaluation+'" > <i class="fa fa-toggle-on"></i></a>';
                            }else {
                                return '<a href="#" title="Activar notificación" class="btn btn-default btn-xs confirm-turn" data-turn="true" data-register="'+full.codigo_registro_fase_usuario+'" data-scoring="'+full.total_evaluation+'"> <i class="fa fa-toggle-off"></i></a>';
                            }
                        }
                    },
                ]
            });
        },

        /**
         * Render Mentores - Provedores
         */

        renderProgramMentoresProvedores: function (data) {
            var _this = this;
            this.$gridSearchTable = this.$('#program-user-mentores-provedores-search-table');
            this.gridSearchTable = this.$gridSearchTable.DataTable({
                dom:"<'row'<'col-sm-5'i><'col-sm-7'p>>" +
                    "<'row'<'col-sm-12'tr>>" +
                    "<'row'<'col-sm-5'i><'col-sm-7'p>>",
                processing: true,
                serverSide: true,
                language: window.Misc.dataTableES(),
                searching: false,
                ajax: {
                    url: window.Misc.urlFull( Route.route('convocatoria.usuario.index') ),
                    data: function( data ) {
                        data.program = _this.model.get('codigo_convocatoria');
                        data.email = _this.$searchordenEmail.val();
                        data.phase = _this.$searchordenPhase.val();
                        data.number_register = _this.$searchordenNumber.val();
                        data.city = _this.$searchordenCity.val();
                        data.codigo_usuario = _this.model.get('codigo_usuario');
                    }
                },
                columns: [
                    { data: 'numero_registro_convocatoria', name: 'numero_registro_convocatoria' },
                    { data: 'nombre_usuario', name: 'nombre_usuario' },
                    { data: 'correo_electronico', name: 'correo_electronico' },
                    { data: 'nombre_ciudad', name: 'nombre_ciudad' },
                    { data: 'fecha_registro_fase_usuario', name: 'fecha_registro_fase_usuario' },
                    { data: 'nombre_fase', name: 'nombre_fase' }
                ],
                order: [
                    [ 0, 'asc' ]
                ],
                columnDefs: [
                    {
                        targets: 0,
                        width: '10%'
                    },
                    {
                        targets: 6,
                        orderable: false,
                        className: 'text-center',
                        render: function ( data, type, full, row ) {
                            if( full.mentor ) {
                                return '<a href="#" title="Quitar Rol" class="btn btn-default btn-xs confirm-turn" data-turn="false" data-register="'+full.codigo_registro_fase_usuario+'" data-scoring="'+full.total_evaluation+'" > <i class="fa fa-toggle-on"></i></a>';
                            }else {
                                return '<a href="#" title="Añadir Rol" class="btn btn-default btn-xs confirm-turn" data-turn="true" data-register="'+full.codigo_registro_fase_usuario+'" data-scoring="'+full.total_evaluation+'"> <i class="fa fa-toggle-off"></i></a>';
                            }
                        }
                    },
                ]
            });
        },

        /**
         * render program SpecializedService
         */
        renderSpecializedService: function (data) {
            var _this = this;

            this.$gridSearchTable = this.$('#program-user-specialized-service-search-table');
            this.gridSearchTable = this.$gridSearchTable.DataTable({
                dom:"<'row'<'col-sm-5'i><'col-sm-7'p>>" +
                    "<'row'<'col-sm-12'tr>>" +
                    "<'row'<'col-sm-5'i><'col-sm-7'p>>",
                processing: true,
                serverSide: true,
                language: window.Misc.dataTableES(),
                searching: false,
                ajax: {
                    url: window.Misc.urlFull( Route.route('convocatoria.usuario.index') ),
                    data: function( data ) {
                        data.program = _this.model.get('codigo_convocatoria');
                        data.email = _this.$searchordenEmail.val();
                        data.phase = _this.$searchordenPhase.val();
                        data.number_register = _this.$searchordenNumber.val();
                        data.city = _this.$searchordenCity.val();
                    }
                },
                columns: [
                    { data: 'numero_registro_convocatoria', name: 'numero_registro_convocatoria' },
                    { data: 'nombre_usuario', name: 'nombre_usuario' },
                    { data: 'correo_electronico', name: 'correo_electronico' },
                    { data: 'nombre_ciudad', name: 'nombre_ciudad' },
                    { data: 'fecha_registro_fase_usuario', name: 'fecha_registro_fase_usuario' },
                    { data: 'nombre_fase', name: 'nombre_fase' }
                ],
                order: [
                    [ 0, 'asc' ]
                ],
                columnDefs: [
                    {
                        targets: 0,
                        width: '10%'
                    },
                    {
                        targets: 6,
                        orderable: false,
                        className: 'text-center',
                        render: function ( data, type, full, row ) {
                            return _this.templateButtonsSpecializedService( {full: full} );
                        }
                    }
                ]
            });
        },


        /**
         * render program register
         */
        renderProgramRegister: function (data) {
            var _this = this;
            this.$gridSearchTable = this.$('#program-register-search-table');
            this.gridSearchTable = this.$gridSearchTable.DataTable({
                dom:"<'row'<'col-sm-5'i><'col-sm-7'p>>" +
                    "<'row'<'col-sm-12'tr>>" +
                    "<'row'<'col-sm-5'i><'col-sm-7'p>>",
                processing: true,
                serverSide: true,
                language: window.Misc.dataTableES(),
                searching: false,
                ajax: {
                    url: window.Misc.urlFull( Route.route('convocatoria.usuario.index') ),
                    data: function( data ) {
                        data.program = _this.model.get('codigo_convocatoria');
                        data.email = _this.$searchordenEmail.val();
                        data.phase = _this.$searchordenPhase.val();
                        data.number_register = _this.$searchordenNumber.val();
                        data.city = _this.$searchordenCity.val();
                    }
                },
                columns: [
                    { data: 'numero_registro_convocatoria', name: 'numero_registro_convocatoria' },
                    { data: 'nombre_usuario', name: 'nombre_usuario' },
                    { data: 'correo_electronico', name: 'correo_electronico' },
                    { data: 'nombre_ciudad', name: 'nombre_ciudad' },
                    { data: 'fecha_registro_fase_usuario', name: 'fecha_registro_fase_usuario' },
                    { data: 'nombre_fase', name: 'nombre_fase' }
                ],
                order: [
                    [ 0, 'asc' ]
                ],
                columnDefs: [
                    {
                        targets: 0,
                        width: '10%'
                    },
                    {
                        targets: 6,
                        orderable: false,
                        className: 'text-center',
                        render: function ( data, type, full, row ) {
                            
                            if( full.visualizacion_usuario ) {
                                return '<a href="#" title="Desactivar notificación" class="btn btn-default btn-xs confirm-turn" data-turn="false" data-register="'+full.codigo_registro_fase_usuario+'" data-scoring="'+full.total_evaluation+'" > <i class="fa fa-toggle-on"></i></a>';
                            }else {
                                return '<a href="#" title="Activar notificación" class="btn btn-default btn-xs confirm-turn" data-turn="true" data-register="'+full.codigo_registro_fase_usuario+'" data-scoring="'+full.total_evaluation+'"> <i class="fa fa-toggle-off"></i></a>';
                            }
                        }
                    },
                    {
                        targets: 7,
                        orderable: false,
                        className: 'text-center',
                        render: function ( data, type, full, row ) {
                            return _this.templateButtonsActionDinamiza( {full: full} );
                        }
                    }
                ]
            });
        },

        /**
         * render program entity
         */
        renderProgramEntity: function (data) {
            var _this = this;
            this.$gridSearchTable = this.$('#participants-search-table');
            this.gridSearchTable = this.$gridSearchTable.DataTable({
                dom: "<'row'<'col-sm-5'i><'col-sm-7'p>>" +
                    "<'row'<'col-sm-12'tr>>" +
                    "<'row'<'col-sm-5'i><'col-sm-7'p>>",
                processing: true,
                serverSide: true,
                language: window.Misc.dataTableES(),
                searching: false,
                ajax: {
                    url: window.Misc.urlFull( Route.route('convocatoria.usuario.index') ),
                    data: function( data ) {
                        data.program = _this.model.get('codigo_convocatoria');
                        data.number_register = _this.$searchordenNumber.val();
                        data.email = _this.$searchordenEmail.val();
                        data.phase = _this.$searchordenPhase.val();
                        data.emprendimiento = _this.$searchordenEntrepreneurship.val();
                        data.identificacion = _this.$searchIdentificacion.val();
                    }
                },
                columns: [
                    { data: 'numero_registro_convocatoria', name: 'numero_registro_convocatoria' },
                    { data: 'nombre_entidad', name: 'nombre_entidad' },
                    { data: 'nombre_usuario', name: 'nombre_usuario' },
                    { data: 'correo_electronico', name: 'correo_electronico' },
                    { data: 'identificacion_usuario', name: 'identificacion_usuario' },
                    { data: 'nombre_ciudad', name: 'nombre_ciudad' },
                    { data: 'fecha_registro_fase_usuario', name: 'fecha_registro_fase_usuario' },
                    { data: 'nombre_fase', name: 'nombre_fase' },
                    { data: 'estado', name: 'estado' },
                    { data: 'puntaje', name: 'puntaje'}
                ],
                order: [
                    [ 0, 'asc' ]
                ],
                columnDefs: [
                    {
                        targets: 0,
                        width: '10%'
                    },
                    {
                        targets: 10,
                        orderable: false,
                        className: 'text-center',
                        render: function ( data, type, full, row ) {
                            //console.log('Full', full);
                            if( full.visualizacion_usuario ) {
                                return '<a href="#" title="Desactivar notificación" class="btn btn-default btn-xs confirm-turn" data-turn="false" data-register="'+full.codigo_registro_fase_usuario+'" data-scoring="'+full.total_evaluation+'" > <i class="fa fa-toggle-on"></i></a>';
                            }else {
                                return '<a href="#" title="Activar notificación" class="btn btn-default btn-xs confirm-turn" data-turn="true" data-register="'+full.codigo_registro_fase_usuario+'" data-scoring="'+full.total_evaluation+'"> <i class="fa fa-toggle-off"></i></a>';
                            }
                        }
                    },
                    {
                        targets: 11,
                        orderable: false,
                        render: function ( data, type, full, row ) {
                            return '<a href="' + window.Misc.urlPlatform('proyecto/' + full.nickname_usuario) + '" title="Ver emprendimiento" class="btn btn-default btn-xs" ><i class="fa fa-file-text"></i></a>';
                        }
                    },
                    {
                        targets: 12,
                        orderable: false,
                        className: 'text-center',
                        render: function ( data, type, full, row ) {
                            return _this.templateButtonsActionDefault( {full: full} );
                        }
                    },
                    {
                        targets: 13,
                        orderable: false,
                        "visible": true,
                        className: 'select-checkbox',
                        render: function ( data, type, full, row ) {
                            return '';
                        }
                    }
                ]
            });
        },

        /**
         * render program entity
         */
        renderProgramEntityRutan: function (data) {
            var _this = this;
            this.$gridSearchTable = this.$('#participants-search-table');
            this.gridSearchTable = this.$gridSearchTable.DataTable({
                dom: "<'row'<'col-sm-5'i><'col-sm-7'p>>" +
                    "<'row'<'col-sm-12'tr>>" +
                    "<'row'<'col-sm-5'i><'col-sm-7'p>>",
                processing: true,
                serverSide: true,
                language: window.Misc.dataTableES(),
                searching: false,
                ajax: {
                    url: window.Misc.urlFull( Route.route('convocatoria.usuario.index') ),
                    data: function( data ) {
                        data.program = _this.model.get('codigo_convocatoria');
                        data.number_register = _this.$searchordenNumber.val();
                        data.email = _this.$searchordenEmail.val();
                        data.phase = _this.$searchordenPhase.val();
                        data.emprendimiento = _this.$searchordenEntrepreneurship.val();
                    }
                },
                columns: [
                    { data: 'numero_registro_convocatoria', name: 'numero_registro_convocatoria' },
                    { data: 'nombre_entidad', name: 'nombre_entidad' },
                    { data: 'nombre_usuario', name: 'nombre_usuario' },
                    { data: 'nombre_ciudad', name: 'nombre_ciudad' },
                    { data: 'fecha_registro_fase_usuario', name: 'fecha_registro_fase_usuario' },
                    { data: 'nombre_fase', name: 'nombre_fase' },
                    { data: 'estado', name: 'estado' },
                    { data: 'puntaje', name: 'puntaje'}
                ],
                order: [
                    [ 0, 'asc' ]
                ],
                columnDefs: [
                    {
                        targets: 0,
                        width: '10%'
                    },
                    {
                        targets: 2,
                        render: function ( data, type, full, row ) {
                            return data + ' <br>' + full.correo_electronico;
                        }
                    },
                    {
                        targets: 8,
                        orderable: false,
                        className: 'text-center',
                        render: function ( data, type, full, row ) {
                            //console.log('Full', full);
                            if( full.visualizacion_usuario ) {
                                return '<a href="#" title="Desactivar notificación" class="btn btn-default btn-xs confirm-turn" data-turn="false" data-register="'+full.codigo_registro_fase_usuario+'" data-scoring="'+full.total_evaluation+'" > <i class="fa fa-toggle-on"></i></a>';
                            }else {
                                return '<a href="#" title="Activar notificación" class="btn btn-default btn-xs confirm-turn" data-turn="true" data-register="'+full.codigo_registro_fase_usuario+'" data-scoring="'+full.total_evaluation+'"> <i class="fa fa-toggle-off"></i></a>';
                            }
                        }
                    },
                    {
                        targets: 9,
                        orderable: false,
                        render: function ( data, type, full, row ) {
                            return '<a href="' + window.Misc.urlPlatform('proyecto/' + full.nickname_usuario) + '" title="Ver emprendimiento" class="btn btn-default btn-xs" ><i class="fa fa-file-text"></i></a>';
                        }
                    },
                    {
                        targets: 10,
                        orderable: false,
                        className: 'text-center',
                        render: function ( data, type, full, row ) {
                            return _this.templateButtonsActionRutan( {full: full} );
                        }
                    },
                    {
                        targets: 11,
                        orderable: false,
                        "visible": true,
                        className: 'select-checkbox',
                        render: function ( data, type, full, row ) {
                            return '';
                        }
                    }
                ]
            });
        },

        /**
         * render diagnosis entity
         */
        renderDiagnosisSiinova: function (data) {
            var _this = this;
            this.$gridSearchTable = this.$('#participants-search-table');
            this.gridSearchTable = this.$gridSearchTable.DataTable({
                dom: "<'row'<'col-sm-5'i><'col-sm-7'p>>" +
                    "<'row'<'col-sm-12'tr>>" +
                    "<'row'<'col-sm-5'i><'col-sm-7'p>>",
                processing: true,
                serverSide: true,
                language: window.Misc.dataTableES(),
                searching: false,
                ajax: {
                    url: window.Misc.urlFull( Route.route('convocatoria.usuario.index') ),
                    data: function( data ) {
                        data.program = _this.model.get('codigo_convocatoria');
                        data.number_register = _this.$searchordenNumber.val();
                        data.email = _this.$searchordenEmail.val();
                        data.phase = _this.$searchordenPhase.val();
                        data.emprendimiento = _this.$searchordenEntrepreneurship.val();
                    }
                },
                columns: [
                    { data: 'numero_registro_convocatoria', name: 'numero_registro_convocatoria' },
                    { data: 'nombre_entidad', name: 'nombre_entidad' },
                    { data: 'nombre_usuario', name: 'nombre_usuario' },
                    { data: 'nombre_ciudad', name: 'nombre_ciudad' },
                    { data: 'fecha_registro_fase_usuario', name: 'fecha_registro_fase_usuario' },
                    { data: 'nombre_fase', name: 'nombre_fase' },
                    { data: 'estado', name: 'estado' },
                    { data: 'puntaje', name: 'puntaje'}
                ],
                order: [
                    [ 0, 'asc' ]
                ],
                columnDefs: [
                    {
                        targets: 0,
                        width: '10%'
                    },
                    {
                        targets: 2,
                        render: function ( data, type, full, row ) {
                            return data + ' <br>' + full.correo_electronico;
                        }
                    },
                    {
                        targets: 8,
                        orderable: false,
                        className: 'text-center',
                        render: function ( data, type, full, row ) {
                            //console.log('Full', full);
                            if( full.visualizacion_usuario ) {
                                return '<a href="#" title="Desactivar notificación" class="btn btn-default btn-xs confirm-turn" data-turn="false" data-register="'+full.codigo_registro_fase_usuario+'" data-scoring="'+full.total_evaluation+'" > <i class="fa fa-toggle-on"></i></a>';
                            }else {
                                return '<a href="#" title="Activar notificación" class="btn btn-default btn-xs confirm-turn" data-turn="true" data-register="'+full.codigo_registro_fase_usuario+'" data-scoring="'+full.total_evaluation+'"> <i class="fa fa-toggle-off"></i></a>';
                            }
                        }
                    },
                    {
                        targets: 9,
                        orderable: false,
                        render: function ( data, type, full, row ) {
                            return '<a href="' + window.Misc.urlPlatform('proyecto/' + full.nickname_usuario) + '" title="Ver emprendimiento" class="btn btn-default btn-xs" ><i class="fa fa-file-text"></i></a>';
                        }
                    },
                    {
                        targets: 10,
                        orderable: false,
                        className: 'text-center',
                        render: function ( data, type, full, row ) {
                            return _this.templateButtonsActionSiinova( {full: full} );
                        }
                    },
                    {
                        targets: 11,
                        orderable: false,
                        "visible": true,
                        className: 'select-checkbox',
                        render: function ( data, type, full, row ) {
                            return '';
                        }
                    }
                ]
            });
        },

        /**
         * render program Terpel 20
         */
        renderProgramTerpel20: function (data) {
            var _this = this;
            this.$gridSearchTable = this.$('#participants-search-table');
            this.gridSearchTable = this.$gridSearchTable.DataTable({
                dom: "<'row'<'col-sm-5'i><'col-sm-7'p>>" +
                    "<'row'<'col-sm-12'tr>>" +
                    "<'row'<'col-sm-5'i><'col-sm-7'p>>",
                processing: true,
                serverSide: true,
                language: window.Misc.dataTableES(),
                searching: false,
                ajax: {
                    url: window.Misc.urlFull( Route.route('convocatoria.usuario.index') ),
                    data: function( data ) {
                        data.program = _this.model.get('codigo_convocatoria');
                        data.number_register = _this.$searchordenNumber.val();
                        data.email = _this.$searchordenEmail.val();
                        data.emprendimiento = _this.$searchordenEntrepreneurship.val();
                        data.city = _this.$searchordenCity.val();
                        data.phase = _this.$searchordenPhase.val();
                    }
                },
                columns: [
                    { data: 'numero_registro_convocatoria', name: 'numero_registro_convocatoria' },
                    { data: 'nombre_entidad', name: 'nombre_entidad' },
                    { data: 'nombre_usuario', name: 'nombre_usuario' },
                    { data: 'nombre_ciudad', name: 'nombre_ciudad' },
                    { data: 'fecha_registro_fase_usuario', name: 'fecha_registro_fase_usuario' },
                    { data: 'fase', name: 'fase'},
                    { data: 'puntaje', name: 'puntaje'}
                ],
                order: [
                    [ 0, 'asc' ]
                ],
                columnDefs: [
                    {
                        targets: 0,
                        width: '10%'
                    },
                    {
                        targets: 2,
                        render: function ( data, type, full, row ) {
                            return data + ' <br>' + full.correo_electronico;
                        }
                    },
                    {
                        targets: 7,
                        orderable: false,
                        className: 'text-center',
                        render: function ( data, type, full, row ) {

                            if( full.visualizacion_usuario ) {  
                                return '<a href="#" title="Desactivar notificación" class="btn btn-default btn-xs confirm-turn" data-turn="false" data-register="'+full.codigo_registro_fase_usuario+'" data-scoring="'+full.total_evaluation+'" > <i class="fa fa-toggle-on"></i></a>';
                            }else {
                                return '<a href="#" title="Activar notificación" class="btn btn-default btn-xs confirm-turn" data-turn="true" data-register="'+full.codigo_registro_fase_usuario+'" data-scoring="'+full.total_evaluation+'"> <i class="fa fa-toggle-off"></i></a>';
                            }
                        }
                    },
                    {
                        targets: 8,
                        orderable: false,
                        render: function ( data, type, full, row ) {
                            return '<a href="' + window.Misc.urlPlatform('proyecto/' + full.nickname_usuario) + '" title="Ver emprendimiento" class="btn btn-default btn-xs" ><i class="fa fa-file-text"></i></a>';
                        }
                    },
                    {
                        targets: 9,
                        orderable: false,
                        className: 'text-center',
                        render: function ( data, type, full, row ) {
                            return _this.templateButtonsAction( {full: full} );
                        }
                    }
                ]
            });
        },

        renderProgramEntidadesProvedores: function (data) {
            var _this = this;
            this.$gridSearchTable = this.$('#participants-search-entitdades-provedores-table');
            this.gridSearchTable = this.$gridSearchTable.DataTable({
                dom: "<'row'<'col-sm-5'i><'col-sm-7'p>>" +
                    "<'row'<'col-sm-12'tr>>" +
                    "<'row'<'col-sm-5'i><'col-sm-7'p>>",
                processing: true,
                serverSide: true,
                language: window.Misc.dataTableES(),
                searching: false,
                ajax: {
                    url: window.Misc.urlFull( Route.route('convocatoria.usuario.index') ),
                    data: function( data ) {
                        data.program = _this.model.get('codigo_convocatoria');
                        data.number_register = _this.$searchordenNumber.val();
                        data.email = _this.$searchordenEmail.val();
                        data.emprendimiento = _this.$searchordenEntrepreneurship.val();
                        data.city = _this.$searchordenCity.val();
                    }
                },
                columns: [
                    { data: 'numero_registro_convocatoria', name: 'numero_registro_convocatoria' },
                    { data: 'nombre_entidad', name: 'nombre_entidad' },
                    { data: 'nombre_usuario', name: 'nombre_usuario' },
                    { data: 'nombre_ciudad', name: 'nombre_ciudad' },
                    { data: 'fecha_registro_fase_usuario', name: 'fecha_registro_fase_usuario' },
                    { data: 'nickname_usuario', name: 'nickname_usuario'}
                ],
                order: [
                    [ 0, 'asc' ]
                ],
                columnDefs: [
                    {
                        targets: 0,
                        width: '10%'
                    },
                    {
                        targets: 2,
                        render: function ( data, type, full, row ) {
                            return data + ' <br>' + full.correo_electronico;
                        }
                    },
                    {
                        targets: 5,
                        orderable: false,
                        className: 'text-center',
                        render: function ( data, type, full, row ) {
                            if( full.provedor ) {
                                return '<a href="#" title="Quitar Rol" class="btn btn-default btn-xs confirm-turn" data-turn="false" data-register="'+full.codigo_registro_fase_usuario+'" data-scoring="'+full.total_evaluation+'" > <i class="fa fa-toggle-on"></i></a>';
                            }else {
                                return '<a href="#" title="Añadir Rol" class="btn btn-default btn-xs confirm-turn" data-turn="true" data-register="'+full.codigo_registro_fase_usuario+'" data-scoring="'+full.total_evaluation+'"> <i class="fa fa-toggle-off"></i></a>';
                            }
                        }
                    },
                    {
                        targets: 6,
                        orderable: false,
                        className: 'text-center',
                        render: function ( data, type, full, row ) {
                            return _this.templateButtonsActionEntidadProvedor( {full: full} );
                        }
                    },
                ]
            });
        },

        /**
         * Load spinner on the request
         */
        loadSpinner: function () {
            window.Misc.setSpinner( this.$gridSearchTable );
        },

        /**
         * response of the server
         */
        responseServer: function ( model, resp, opts ) {

            window.Misc.removeSpinner({
                'wrap': this.$gridSearchTable,
                'direct': true
            });
        },

        search: function(e) {
            e.preventDefault();
            this.gridSearchTable.ajax.reload();
        },

        searchAldea: function(e) {
            e.preventDefault();
            if(this.$searchordenPhase.val() == _tp.getPhases['challenge_1.2'] && this.$searchordenNotified.val() === 'false') {
                $('div.col-sm-1').html('<button class="btn btn-success btn-notify">Notificar</button>');
                this.gridSearchTable.column(9).visible(true);
                this.gridSearchTable.select.style('multi');
                this.gridSearchTable.select.selector('td:last-child');
                this.gridSearchTable.ajax.reload();
            } else if(this.$searchordenPhase.val() == _tp.getPhases['one_challenge'] && this.$searchordenNotified.val() === 'false') {
                $('div.col-sm-1').html('<button class="btn btn-success btn-notify">Notificar</button>');
                this.gridSearchTable.column(9).visible(true);
                this.gridSearchTable.select.style('multi');
                this.gridSearchTable.select.selector('td:last-child');
                this.gridSearchTable.ajax.reload();
            } else if(this.$searchordenPhase.val() == _tp.getPhases['two_challenge'] && this.$searchordenNotified.val() === 'false') {
                $('div.col-sm-1').html('<button class="btn btn-success btn-notify">Notificar</button>');
                this.gridSearchTable.column(9).visible(true);
                this.gridSearchTable.select.style('multi');
                this.gridSearchTable.select.selector('td:last-child');
                this.gridSearchTable.ajax.reload();
            } else {
                $('div.col-sm-1').html('');
                this.gridSearchTable.column(9).visible(false);
                this.gridSearchTable.select.style('api');
                this.gridSearchTable.ajax.reload();
            }
        },

        searchAldea20: function(e) {
            e.preventDefault();
            // Code Here
            if(this.$searchordenPhase.val() == _tp.getPhases['aldea__20__challenge_1']) {
                $('div.col-sm-1').html('<button class="btn btn-success btn-notify">Notificar</button>');
                this.gridSearchTable.column(9).visible(true);
                this.gridSearchTable.select.style('multi');
                this.gridSearchTable.select.selector('td:last-child');
                this.gridSearchTable.ajax.reload();
            } else if(this.$searchordenPhase.val() == _tp.getPhases['aldea__20__challenge_1_2']) {
                $('div.col-sm-1').html('<button class="btn btn-success btn-notify">Notificar</button>');
                this.gridSearchTable.column(9).visible(true);
                this.gridSearchTable.select.style('multi');
                this.gridSearchTable.select.selector('td:last-child');
                this.gridSearchTable.ajax.reload();
            } else if(this.$searchordenPhase.val() == _tp.getPhases['aldea__20__challenge_1_3']) {
                $('div.col-sm-1').html('<button class="btn btn-success btn-notify">Notificar</button>');
                this.gridSearchTable.column(9).visible(true);
                this.gridSearchTable.select.style('multi');
                this.gridSearchTable.select.selector('td:last-child');
                this.gridSearchTable.ajax.reload();
            }  else {
                $('div.col-sm-1').html('');
                this.gridSearchTable.column(9).visible(false);
                this.gridSearchTable.select.style('api');
                this.gridSearchTable.ajax.reload();
            }
        },

        searchEmpodera: function(e) {
            e.preventDefault();
            this.gridSearchTableEmpodera.ajax.reload();
        },

        clear: function(e) {
            e.preventDefault();

            this.$searchordenEmail.val('');
            this.$searchordenNumber.val('');
            this.$searchIdentificacion.val('');


            if (this.model.get('codigo_orientacion_convocatoria') == window._tp.orientationProgram['user']) {
                switch(this.model.get('codigo_convocatoria')) {
                    default:
                        this.$searchordenPhase.val('1');
                        this.$searchordenCity.val('');
                        break;
                }
            }else {
                switch(this.model.get('codigo_convocatoria')) {
                    case window._tp.getProgram['invitation_entrepreneurs']:
                        this.$searchordenEntrepreneurship.val('');
                        this.$searchordenPhase.val('1');
                        this.$searchordenNotified.val('');
                        this.$searchordenSortby.val('');
                        this.$searchordenScoring.val('');
                        this.$searchordenSortby_2.val('');
                        this.$searchordenScoring_2.val('');
                        break;
                    case window._tp.getProgram['terpel__20']:
                        this.$searchordenEntrepreneurship.val('');
                        this.$searchordenCity.val('');
                        this.$searchordenPhase.val('1');
                    break;
                    default:
                        this.$searchordenEntrepreneurship.val('');
                        this.$searchordenPhase.val('1');
                    break;
                }
            }

            this.gridSearchTable.ajax.reload();
        },

        clearAppsCo: function(e) {
            e.preventDefault();

            this.$searchordenEmail.val('');
            this.$searchordenNumber.val('');
            this.$searchordenPhase.val('');
            this.$searchordenCity.val('');
            this.gridSearchTable.ajax.reload();

        },

        clearEmpodera: function(e) {
            e.preventDefault();

            this.$searchordenEmail.val('');
            this.$searchordenNumber.val('');

            switch(this.model.get('codigo_convocatoria')) {
                case window._tp.getProgram['empodera']:
                    this.$searchordenEntrepreneurship.val('');
                    this.$searchordenPhase.val('1');
                    this.$searchordenNotified.val('');
                    this.$searchordenSortby.val('');
                    this.$searchordenScoring.val('');
                    this.$searchordenSortby_2.val('');
                    this.$searchordenScoring_2.val('');
                    break;

                default:
                    this.$searchordenEntrepreneurship.val('');
                    this.$searchordenCity.val('');
                    break;
            }

            this.gridSearchTableEmpodera.ajax.reload();
        },

        clearAldea20: function(e) {
            e.preventDefault();

            this.$searchordenEmail.val('');
            this.$searchordenNumber.val('');


            if (this.model.get('codigo_orientacion_convocatoria') == window._tp.orientationProgram['user']) {
                switch(this.model.get('codigo_convocatoria')) {
                    default:
                        this.$searchordenPhase.val('1');
                        this.$searchordenCity.val('');
                        break;
                }
            }else {
                switch(this.model.get('codigo_convocatoria')) {
                    case window._tp.getProgram['aldea__20']:
                        this.$searchordenEntrepreneurship.val('');
                        this.$searchordenPhase.val('1');
                        this.$searchordenNotified.val('');
                        this.$searchordenSortby.val('');
                        this.$searchordenScoring.val('');
                        this.$searchordenSortby_2.val('');
                        this.$searchordenScoring_2.val('');
                        break;

                    default:
                        this.$searchordenEntrepreneurship.val('');
                        this.$searchordenCity.val('');
                        break;
                }
            }

            this.gridSearchTable.ajax.reload();
        },

        /**
         * confirm turn
         */
        onConfirm: function (e) {
           // console.log('estoy OnConfirm');
            e.preventDefault();
            this.participantProgramModel = new app.ParticipantProgramModel();
            this.listenToOnce( this.participantProgramModel, 'request', this.loadSpinner );

            var _this = this,
                data = {
                    turn: $(e.currentTarget).data('turn'),
                    codigo_registro_fase_usuario: $(e.currentTarget).data('register'),
                    codigo_usuario: this.model.get('codigo_usuario'),
                    // visualizacion_usuario: true

                };

            if( this.acceptConfirm instanceof Backbone.View ) {
                this.acceptConfirm.stopListening();
                this.acceptConfirm.undelegateEvents();
            }

            this.acceptConfirm = new app.ConfirmWindow({
                parameters: {
                    template: this.templateConfirm(this.model.toJSON()) ,
                    titleConfirm: "Confirmación",
                    onConfirm: function () {
                        _this.participantProgramModel.save( data, {patch: true, wait:true,
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

                                if( _.isObject( resp.errors ) ) {

                                    var listError = '<ul>';

                                    $.each(resp.errors, function(field, item) {
                                        listError += '<li>'+ item[0] +'</li>';
                                    });
                                    listError += '</ul>';

                                    text = listError;
                                }

                                var conf = {
                                    'text': text,
                                    'type': type
                                };
                                _.extend(_this.configAlert, conf);

                                //complete proccess
                                if( resp.success !== undefined ){
                                    if( resp.success ){
                                        window.Misc.showAlertBox(_this.configAlert);

                                        switch(_this.model.get('codigo_convocatoria')) {
                                            case window._tp.getProgram['empodera']:
                                                _this.gridSearchTableEmpodera.ajax.reload( null, false );
                                                break;

                                            default:
                                                _this.gridSearchTable.ajax.reload( null, false );
                                       
                                                break;
                                        }

                                    }else{
                                        window.Misc.showAlertBox(_this.configAlert);
                                    }
                                }
                            }
                        });
                    }
                }
            });
            this.acceptConfirm.render();
        },

        /* @BrahianSánchez
            Funcion encargada de la notificacion multiple de usuarios que pasen a la Fase 2
         */

        onNotifyMultiple: function(e) {
            e.preventDefault();
            var dataUser = [];
            var seleccionados = this.gridSearchTable.rows({ selected: true }).data();
            var dataUsersSelected = $.map(this.gridSearchTable.rows('.selected').data(), function (item) {
                return item;
            });
            var _this = this;
            dataUsersSelected.forEach(function (data, index) {
                dataUser.push([data.codigo_registro_fase_usuario, data.visualizacion_usuario]);
            });
            if( this.acceptConfirm instanceof Backbone.View ) {
                this.acceptConfirm.stopListening();
                this.acceptConfirm.undelegateEvents();
            }
            if(!seleccionados.length) {
                this.acceptConfirm = new app.ConfirmWindow({
                    parameters: {
                        template: this.templateError(this.model.toJSON()),
                        titleConfirm: "Error",
                        onConfirm: function () {
                            window.Misc.removeSpinner({
                                'wrap': _this.$el,
                                'direct': false
                            });
                            var conf = {
                                'text': text,
                                'type': type
                            };
                            _.extend(_this.configAlert, conf);
                            window.Misc.showAlertBox(_this.configAlert);
                        }
                    }
                });
            } else {
                this.acceptConfirm = new app.ConfirmWindow({
                    parameters: {
                        template: this.templateConfirm(this.model.toJSON()),
                        titleConfirm: "Confirmación",
                        onConfirm: function() {
                            $.post( window.Misc.urlFull( Route.route('convocatoria.usuario.store') ), {
                                '_token': $('meta[name=csrf-token]').attr('content'),
                                data: {
                                    codigo_registro_fase_usuario: dataUser,
                                    turn: true
                                }
                            }).success(function (resp) {
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

                                if( _.isObject( resp.errors ) ) {

                                    var listError = '<ul>';

                                    $.each(resp.errors, function(field, item) {
                                        listError += '<li>'+ item[0] +'</li>';
                                    });
                                    listError += '</ul>';

                                    text = listError;
                                }

                                var conf = {
                                    'text': text,
                                    'type': type
                                };
                                _.extend(_this.configAlert, conf);

                                //complete proccess
                                if( resp.success !== undefined ){
                                    if( resp.success ){
                                        window.Misc.showAlertBox(_this.configAlert);
                                        _this.gridSearchTable.ajax.reload( null, false );
                                    }else{
                                        window.Misc.showAlertBox(_this.configAlert);
                                    }
                                }
                            });
                        }
                    }
                });
            }
            this.acceptConfirm.render();
        },

        onNotifyEmpodera2020: function(e) {
            e.preventDefault();

            var dataUser = [];
            var seleccionados = this.gridSearchTable.rows({ selected: true }).data();
            var dataUsersSelected = $.map(this.gridSearchTable.rows('.selected').data(), function (item) {
                return item;
            });
            var _this = this;
            dataUsersSelected.forEach(function (data, index) {
                dataUser.push([data.codigo_registro_fase_usuario, data.visualizacion_usuario]);
            });
            if( this.acceptConfirm instanceof Backbone.View ) {
                this.acceptConfirm.stopListening();
                this.acceptConfirm.undelegateEvents();
            }
            if(!seleccionados.length) {
                this.acceptConfirm = new app.ConfirmWindow({
                    parameters: {
                        template: this.templateError(this.model.toJSON()),
                        titleConfirm: "Error",
                        onConfirm: function () {
                            window.Misc.removeSpinner({
                                'wrap': _this.$el,
                                'direct': false
                            });
                            var conf = {
                                'text': text,
                                'type': type
                            };
                            _.extend(_this.configAlert, conf);
                            window.Misc.showAlertBox(_this.configAlert);
                        }
                    }
                });
            } else {
                this.acceptConfirm = new app.ConfirmWindow({
                    parameters: {
                        template: this.templateConfirm(this.model.toJSON()),
                        titleConfirm: "Confirmación",
                        onConfirm: function() {
                            $.post( window.Misc.urlFull( Route.route('convocatoria.usuario.store') ), {
                                '_token': $('meta[name=csrf-token]').attr('content'),
                                data: {
                                    codigo_registro_fase_usuario: dataUser,
                                    turn: true
                                }
                            }).success(function (resp) {
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

                                if( _.isObject( resp.errors ) ) {

                                    var listError = '<ul>';

                                    $.each(resp.errors, function(field, item) {
                                        listError += '<li>'+ item[0] +'</li>';
                                    });
                                    listError += '</ul>';

                                    text = listError;
                                }

                                var conf = {
                                    'text': text,
                                    'type': type
                                };
                                _.extend(_this.configAlert, conf);

                                //complete proccess
                                if( resp.success !== undefined ){
                                    if( resp.success ){
                                        window.Misc.showAlertBox(_this.configAlert);
                                        _this.gridSearchTable.ajax.reload( null, false );
                                    }else{
                                        window.Misc.showAlertBox(_this.configAlert);
                                    }
                                }
                            });
                        }
                    }
                });
            }
            this.acceptConfirm.render();
        },

        /**
         * Filter export
         */
        onFilterExport: function (e) {
            e.preventDefault();
            data = window.Misc.formToJson(this.$('#form-filter-program'));
            data.program = this.model.get('codigo_convocatoria');
            window.Misc.redirect( window.Misc.urlFull( Route.route( 'convocatoria.reporte.index', data) ) );
        },

        onFilterExportNoComplete: function (e) {
            e.preventDefault();
            data = window.Misc.formToJson(this.$('#form-filter-program'));
            data.program = this.model.get('codigo_convocatoria');
            data.type_report = 'form_no_complete';
            window.Misc.redirect( window.Misc.urlFull( Route.route( 'convocatoria.reporte.index', data) ) );
        },

        /**
         * Return to history DOM
         */
        onBack: function (e) {
            e.preventDefault();

            window.history.back();
        },

        /**
         * Forward email four challenge
         */
        onForwardFourChallenge: function (e) {
            e.preventDefault();

            var _this = this,
                codigo_registro_fase_usuario = $(e.currentTarget).data('register');

            if( this.acceptConfirm instanceof Backbone.View ) {
                this.acceptConfirm.stopListening();
                this.acceptConfirm.undelegateEvents();
            }

            this.acceptConfirm = new app.ConfirmWindow({
                parameters: {
                    template: this.templateConfirmForwardFourChallenge(this.model.toJSON()) ,
                    titleConfirm: "Confirmación",
                    onConfirm: function () {
                        // Load spinner
                        _this.loadSpinner();

                        // Send email
                        $.get(document.url + Route.route('convocatoria.fase.notificacion', {'usuario': codigo_registro_fase_usuario}), function (resp) {
                            if( resp.success !== undefined ) {

                                window.Misc.removeSpinner({
                                    'wrap': _this.$el,
                                    'direct': false
                                });

                                var type = resp.success ? 'success' : 'alert',
                                    text = resp.success ?
                                        (resp.message !== undefined ? resp.message : '')
                                        : resp.errors;

                                if( _.isObject(resp.errors) ){
                                    var listError = '<ul>';

                                    $.each(resp.errors, function(field, item) {
                                        listError += '<li>'+ item[0] +'</li>';
                                    });
                                    listError += '</ul>';

                                    text = listError;
                                }

                                var conf = {
                                    'text': text,
                                    'type': type
                                };
                                _.extend(_this.configAlert, conf);

                                // Message
                                window.Misc.showAlertBox( _this.configAlert );
                            }
                        });
                    }
                }
            });

            this.acceptConfirm.render();
        },

        /**
         * On Cancel Action
         */

        onCancel: function(e) {
            e.preventDefault();

            var _this = this,
            codigo_registro_fase_usuario = $(e.currentTarget).data('register'),
            program = $(e.currentTarget).data('program');

            this.acceptConfirm = new app.ConfirmWindow({
                parameters: {
                    template: this.templateConfirmCancel({ program: program }) ,
                    titleConfirm: "Confirmación",
                    onConfirm: function () {
                        // Load spinner
                        _this.loadSpinner();

                        // Cancel register
                        $.post(document.url + Route.route('convocatoria.cancel', {'register': codigo_registro_fase_usuario}), function (resp) {
                            if( resp.success !== undefined ) {

                                window.Misc.removeSpinner({
                                    'wrap': _this.$el,
                                    'direct': false
                                });

                                var type = resp.success ? 'success' : 'alert',
                                    text = resp.success ?
                                        (resp.message !== undefined ? resp.message : '')
                                        : resp.errors;

                                if( _.isObject(resp.errors) ){
                                    var listError = '<ul>';

                                    $.each(resp.errors, function(field, item) {
                                        listError += '<li>'+ item[0] +'</li>';
                                    });
                                    listError += '</ul>';

                                    text = listError;
                                }

                                var conf = {
                                    'text': text,
                                    'type': type
                                };
                                _.extend(_this.configAlert, conf);

                                // Message
                                window.Misc.showAlertBox( _this.configAlert );

                                // Reload Table
                                _this.gridSearchTable.ajax.reload();
                            }
                        });
                    }
                }
            });

            this.acceptConfirm.render();
        }
    });

})(jQuery, this, this.document);
