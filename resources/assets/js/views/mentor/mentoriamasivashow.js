/**
* Class MentoriaMasivaShowView extends of Backbone View
* @author TuProyecto || Desarrollador : @ayn
* @link http://TuProyecto.com
*/


//Global App Backbone
app || (app = {});

(function ($, window, document, undefined) {

    app.MentoriaMasivaShowView = Backbone.View.extend({

        el: '#mentoria-masiva-show',
        templateCreateMentoring: _.template( ($('#create-request-mentoring-tpl').html() || '') ),
        templateConfirmCreateSessions: _.template( ($('#confirm-create-sessions-tpl').html() || '') ),
        templateConfirmDevelopmentSessions: _.template( ($('#confirm-development-sessions-tpl').html() || '') ),
        templateDevelopmentSessions: _.template( ($('#request-development-sessions-tpl').html() || '') ),
        templateButtonsItemAction: _.template( ($('#buttons-action-item-tpl').html() || '') ),
        events: {
            'submit #form-create-calendar-event' : 'onCreateSession',
            'submit #form_mentoring_description' : 'onAddDescription',
            'submit #form-mentoring-task-session' : 'onAddMentoringTask',
            'submit #form-mentoring-tags' : 'onAddMentoringTags',
            'submit #form-mentoring-objetive-general' : 'onAddMentoringObjetiveGeneral',
            'submit #form-mentoring-duration' : 'onAddMentoringDuration',
            'submit #form-mentoring-phases' : 'onAddMentoringPhases',
            'submit #form-mentoring-diagnosis' : 'onAddMentoringDiagnosis',
            'submit #form-mentoring-recommends' : 'onAddMentoringRecommends',
            'submit #form-mentoring-tracing' : 'onAddMentoringTracing',
            'click .select_session' : 'onSearchSession',
            'change #mentoring-phase' : 'onChangePhase',
            'change .select-all-items': 'onSelectAllItems',
            'change .select-one-item': 'onSelectOneItem',
            'submit #form-mentoring-finish': 'onMentoringFinish',
            'submit #form-mentoring-cancel': 'onMentoringCancel',
            'click #history-back': 'onBack',
        },

        parameters: {
            dataFilter: {}
        },

        /**
         * Constructor Method
         */
        initialize : function(opts) {

            //configuración de alerta
            this.configAlert = {
                'wrapper': '#main-wrap',
                'closeTime': 15000,
                'speedOpen': 500,
                'speedClose': 500
            };

            this.$array_sesiones = [];

        },

        /*
         * Render View Element
         */
        render: function() {
            // Initialize attributes
            this.$mentoriaMasivaShowTable = this.$('#mentoria-masiva-show-table');
            this.$sesionMasivaTabla = this.$('#sesion-masiva-tabla');
            this.$sesionUnicaTabla = this.$('#sesion-unica-tabla');
            this.$mentoria_masiva = this.$('#mentoria_masiva').val();
            this.$count_asesorias = this.$('#count_asesorias').val();
            this.$count_desarrollo = 0;
            this.$searchIdSession  = 0;

            this.$form_create_session = this.$('#form-create-session');
            this.$form_development_session = this.$('#form-development-session');

            this.$codigo_sesion_masiva = null;
            // overwrite model id attribute
            this.referenceViews();
        },

        /**
         * reference to views
         */
        referenceViews: function () {

            var _this = this;
            this.mentoriaMasivaShowTable = this.$mentoriaMasivaShowTable.DataTable({
                dom:"<'row'<'col-sm-5'i><'col-sm-7'p>>" +
                    "<'row'<'col-sm-12'tr>>" +
                    "<'row'<'col-sm-5'i><'col-sm-7'p>>",
                processing: true,
                //serverSide: true,
                // orderable: false,
                // searching: true,
                language: window.Misc.dataTableES(),
                ajax: {
                    url: window.Misc.urlFull( Route.route('mentorias.masivas.show.table', {mentor: _this.$mentoria_masiva}) ),
                    // data: function( data ) {
                    //     data.identificacion_usuario   = _this.$searchIdentificacion.val();
                    //     data.nombre_usuario   = _this.$searchNombre.val();
                    //     data.codigo_etapa_proyecto  = _this.$searchStageProyect.val();
                    //     data.codigo_categoria_proyecto  = _this.$searchCategoryProyect.val();
                    // },
                },
                columns: [
                    { data: 'codigo_asesoria', name: 'codigo_asesoria' },
                    { data: 'nombre_entidad', name: 'nombre_entidad' },
                    { data: 'nombre_usuario', name: 'nombre_usuario' },
                    { data: 'correo_usuario', name: 'correo_usuario' },
                    { data: 'fecha_asesoria', name: 'fecha_asesoria' },
                ],
                order: [
                    [ 0, 'desc' ]
                ],
                columnDefs: [
                    {
                        targets: 0,
                        width: '15%',
                        // render: function ( data, type, full, row ) {
                        //             return '<a href="' + window.Misc.urlFull( Route.route('mentorias.masivas.show', {mentoria: full.codigo_mentoria_masiva})) + '" title="Ver mentorías">'+full.codigo_mentoria_masiva+'</a>';
                        //         }
                    },
                    {
                        targets: 4,
                        render: function ( data, type, full, row ) {
                            return moment(full.fecha_asesoria).format('LL');
                        }
                    },
                    {
                        targets: [0, 1, 2, 3, 4],
                        className: 'text-center',
                    }
                ]
            });

            //cargamos formulario creación de sesiones
            this.$form_create_session.html( this.templateCreateMentoring );

            //cargamos datepicker para campo fecha
            this.onDatepicker();

            this.sesionMasivaTabla = this.$sesionMasivaTabla.DataTable({
                dom:"<'row'<'col-sm-5'i><'col-sm-7'p>>" +
                    "<'row'<'col-sm-12'tr>>" +
                    "<'row'<'col-sm-5'i><'col-sm-7'p>>",
                processing: true,
                language: window.Misc.dataTableES(),
                ajax: {
                    url: window.Misc.urlFull( Route.route('mentorias.sesiones.table', {mentoriamasiva: _this.$mentoria_masiva}) ),
                    // data: function( data ) {
                    //     data.identificacion_usuario   = _this.$searchIdentificacion.val();
                    // },
                },
                columns: [
                    { data: 'codigo_sesion_masiva', name: 'codigo_sesion_masiva' },
                    { data: 'creacion_sesion_masiva', name: 'creacion_sesion_masiva' },
                ],
                // order: [
                //     [ 0, 'desc' ]
                // ],
                columnDefs: [
                    {
                        targets: 0,
                        // width: '8%',
                        render: function ( data, type, full, row ) {
                                // _this.$codigo_sesion_masiva = full.codigo_sesion_masiva;
                                    return '<input type="button" class="select_session"' + 'value="'+full.codigo_sesion_masiva+ '" title="Ver sesión masiva">';
                                }
                    },
                    {
                        targets: 1,
                        render: function ( data, type, full, row ) {
                            return moment(full.created_at).format('LL');
                        }
                    },
                    {
                        targets: [0, 1],
                        className: 'text-center',
                    }
                ]
            });

            //tabla de session unica
            this.sesionUnicaTabla = this.$sesionUnicaTabla.DataTable({
                dom:"<'row'<'col-sm-5'i><'col-sm-7'p>>" +
                    "<'row'<'col-sm-12'tr>>" +
                    "<'row'<'col-sm-5'i><'col-sm-7'p>>",
                processing: true,
                serverSide: false,
                language: window.Misc.dataTableES(),
                ajax: {
                    url: window.Misc.urlFull( Route.route('mentorias.sesion.table', {mentoriamasiva: _this.$mentoria_masiva}) ),
                    data: function( data ) {
                        data.idsesionmasiva   = _this.$searchIdSession;
                    },
                },
                columns: [
                    { data: 'codigo_sesion_masiva',   name: 'codigo_sesion_masiva' },
                    { data: 'codigo_mentoria', name: 'codigo_mentoria' },
                    { data: 'codigo_estado', name: 'codigo_estado' },
                    { data: 'nombre_entidad', name: 'nombre_entidad' },
                    { data: 'nombre_usuario', name: 'nombre_usuario' },
                    { data: 'correo_usuario', name: 'correo_usuario' },
                    { data: 'fecha_evento', name: 'fecha_evento' },
                    { data: 'descripcion_mentoria', name: 'descripcion_mentoria' },
                    { data: 'nombre_tema_principal_evento', name: 'nombre_tema_principal_evento' },
                    { data: 'descripcion_tarea_mentoria', name: 'descripcion_tarea_mentoria' },
                ],
                // order: [
                //     [ 0, 'desc' ]
                // ],
                columnDefs: [
                    // {
                    //     targets: 0,
                    //     width: '8%',
                    //     render: function ( data, type, full, row ) {
                    //                 return '<input type="button" class="select_session"' + 'value="'+full.codigo_sesion_masiva+ '" title="Ver sesión masiva">';
                    //             }
                    // },
                    {
                        targets: 2,
                        render: function ( data, type, full, row ) {
                            return window._tp.getStateName[data];
                        }
                    },
                    {
                        targets: 6,
                        render: function ( data, type, full, row ) {
                            return moment(full.fecha_evento).format('LL');
                        }
                    },
                    {
                        targets: 10,
                        orderable: false,
                        render: function ( data, type, full, row ) {
                            return _this.templateButtonsItemAction({row: row});
                        }
                    },
                    {
                        targets: [0, 1, 2, 3, 4, 5, 6, 7, 8, 9, 10],
                        className: 'text-center',
                    },
                ]
            });

        },

        /**
         * marcar todos los items
         * @param {event} e
         */
        onSelectAllItems: function (e) {
            e.preventDefault();
            if($(e.target).is(':checked')){
                var _this = this;

                this.$array_sesiones = [];

                this.sesionUnicaTabla.rows().every( function ( rowIdx, tableLoop, rowLoop ) {
                    document.getElementById(rowIdx).checked = true;

                    var obj = _this.sesionUnicaTabla.rows(rowIdx).data().toArray();
                    var codigo_mentoria;
                    obj.forEach(function(row, i) {
                        codigo_mentoria = row.codigo_mentoria;
                    });
                    _this.$array_sesiones.push(codigo_mentoria);
                    
                } );

                //reestablecemos este valor para indicar el numero de sesiones seleccionadas
                this.$count_desarrollo = this.$array_sesiones.length;

            } else {
                this.sesionUnicaTabla.rows().every( function ( rowIdx, tableLoop, rowLoop ) {
                    document.getElementById(rowIdx).checked = false;
                } );

                this.$array_sesiones = [];
                this.$count_desarrollo = 0;

            }
        },

        /**
         * marcar un solo item
         * @param {event} e
         */
        onSelectOneItem: function (e) {
            e.preventDefault();
            var rowIdx = e.currentTarget.id;
            if($(e.target).is(':checked')){
                // var obj = this.sesionUnicaTabla.rows(rowIdx).data().splice();
                var obj = this.sesionUnicaTabla.rows(rowIdx).data().toArray();
                var codigo_mentoria;
                obj.forEach(function(row, i) {
                    codigo_mentoria = row.codigo_mentoria;
                });
                this.$array_sesiones.push(codigo_mentoria);

                //reestablecemos este valor para indicar el numero de sesiones seleccionadas
                // this.$count_desarrollo = this.$array_sesiones.length;

            } else {
                var obj = this.sesionUnicaTabla.rows(rowIdx).data().toArray();
                this.$array_sesiones = this.$array_sesiones.filter(function(element){
                    return element != obj[0].codigo_mentoria;
                });
            }
            //reestablecemos este valor para indicar el numero de sesiones seleccionadas
            this.$count_desarrollo = this.$array_sesiones.length;

            document.getElementById('select_all_items').checked = false;
        },

        /**
         * permite formatear campo para datetimepicker
         */
        onDatepicker: function () {

            //capturar elementos
            var start_event = $('.datetimepicker_start_event');
            var end_event = $('.datetimepicker_end_event');


            start_event.datetimepicker({
                minDate: new Date(),
                format: 'YYYY-MM-DD HH:mm a',

            });
            end_event.datetimepicker({
                useCurrent: false,
                minDate: new Date(),
                format: 'YYYY-MM-DD HH:mm a',
            });

            //se establece configuración Linked Pickers: https://getdatepicker.com/4/#linked-pickers
            start_event.on("dp.change", function (e) {
                end_event.data("DateTimePicker").minDate(e.date);
            });
            end_event.on("dp.change", function (e) {
                start_event.data("DateTimePicker").maxDate(e.date);
            });
        },

        /**
         * para cargar ciudades en el formulario
         * @return void
         */
        onCiudad: function (e) {

            var _this = this;

            $.ajax({
                url: window.Misc.urlFull(Route.route('ciudades.index')),
                type: 'GET',
                // data: { email: userSearch, role: role },
                // beforeSend: function() {
                //     _this.$inputName.val('');
                //     window.Misc.setSpinner( _this.$wraperConten );
                // }
            })
            .success (function (data, textStatus, jqXHR ) {

                const text = data.success ? data.message : data.errors;
                const type = data.success ? 'success' : 'alert';

                _.each(data, function(ciudad) {

                    $('#codigo_ciudad').append(function(){
                        return '<option value="'+ ciudad.id +'">'+ ciudad.text +'</option>'
                    });
                }, this);

            })
            .error (function ( XMLHttpRequest, textStatus, errorThrown ) {
                const text = 'Contacte con el administrador';
                const type = 'alert';

                var config = {
                    'text': text,
                    'type': type
                };

                _.extend( _this.configAlert, config );

                window.Misc.showAlertBox( _this.configAlert );
            });
        },


        onCreateSession: function (e) {
            e.preventDefault();
            var data = window.Misc.formToJson( e.target );
            _.extend(data, {codigo_mentoria_masiva: this.$mentoria_masiva});

            var _this = this;

            if( this.acceptConfirm instanceof Backbone.View ) {
                this.acceptConfirm.stopListening();
                this.acceptConfirm.undelegateEvents();
            }

            this.acceptConfirm = new app.ConfirmWindow({
                parameters: {
                    template: this.templateConfirmCreateSessions({
                        count_asesorias: _this.$count_asesorias
                    }),
                    titleConfirm: "Confirmación",
                    onConfirm: function () {

                        $.ajax({
                            url: window.Misc.urlFull(Route.route('mentorias.sesiones.store')),
                            type: 'POST',
                            data: data,
                            beforeSend: function() {
                                // _this.$inputName.val('');
                                window.Misc.setSpinner( $('#form-create-calendar-event') );
                            }
                        })
                        .success (function (data, textStatus, jqXHR ) {

                            window.Misc.removeSpinner({
                                'wrap': $('#form-create-calendar-event'),
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

                            //se limpia formulario de creacion de sesiones
                            e.target.reset();

                            // recargamos datal de sesionesf masivas
                            _this.sesionMasivaTabla.ajax.reload();
                        })
                        .error (function ( XMLHttpRequest, textStatus, errorThrown ) {
                            const text = 'Contacte con el administrador';
                            const type = 'alert';

                            var config = {
                                'text': text,
                                'type': type
                            };

                            _.extend( _this.configAlert, config );

                            window.Misc.showAlertBox( _this.configAlert );
                        });

                    }
                }
            });

            this.acceptConfirm.render();

        },

        /**
         * guarda pregunta : descripcion intervencion
         * @param {event} e
         */
        onAddDescription: function (e) {
            e.preventDefault();
            e.stopImmediatePropagation();

            var data = window.Misc.formToJson( e.target );
            _.extend(data, {
                                codigo_mentoria_masiva: this.$mentoria_masiva,
                                codigo_sesion_masiva: this.$codigo_sesion_masiva,
                                array_sesiones: this.$array_sesiones
                            });

            var route = 'mentorias.descripcion';
            var field = 'Descripción de la intervención';
            this.onRequest(data, route, field, e.target);

        },

        /**
         * guarda pregunta : Acuerdos plan/accion
         * @param {event} e 
         */
        onAddMentoringTask: function (e) {
            e.preventDefault();
            e.stopImmediatePropagation();

            var data = window.Misc.formToJson( e.target );
            _.extend(data, {
                                codigo_mentoria_masiva: this.$mentoria_masiva,
                                codigo_sesion_masiva: this.$codigo_sesion_masiva,
                                array_sesiones: this.$array_sesiones
                            });

            var route = 'mentorias.tasks';
            var field = 'Acuerdos/Plan de acción';
            this.onRequest(data, route, field, e.target);

        },

        /**
         * guarda pregunta : Informacion del emprendimiento
         * @param {event} e
         */
        onAddMentoringTags: function (e) {
            e.preventDefault();
            e.stopImmediatePropagation();

            var data = window.Misc.formToJson( e.target );
            _.extend(data, {
                                codigo_mentoria_masiva: this.$mentoria_masiva,
                                codigo_sesion_masiva: this.$codigo_sesion_masiva,
                                array_sesiones: this.$array_sesiones
                            });

            var route = 'mentorias.TagSesion';
            var field = 'Información del emprendimiento';
            this.onRequest(data, route, field, e.target);

        },

        /**
         * guarda pregunta : objetivo general / observaciones
         * @param {event} e
         */
        onAddMentoringObjetiveGeneral: function (e) {
            e.preventDefault();
            e.stopImmediatePropagation();

            var data = window.Misc.formToJson( e.target );
            _.extend(data, {
                                codigo_mentoria_masiva: this.$mentoria_masiva,
                                codigo_sesion_masiva: this.$codigo_sesion_masiva,
                                array_sesiones: this.$array_sesiones
                            });

            var route = 'mentorias.generalObjetive';
            var field = 'Observaciones';
            this.onRequest(data, route, field, e.target);

        },

        /**
         * guarda pregunta : duracion de la sesion
         * @param {event} e
         */
        onAddMentoringDuration: function (e) {
            e.preventDefault();
            e.stopImmediatePropagation();

            var data = window.Misc.formToJson( e.target );
            _.extend(data, {
                                codigo_mentoria_masiva: this.$mentoria_masiva,
                                codigo_sesion_masiva: this.$codigo_sesion_masiva,
                                array_sesiones: this.$array_sesiones
                            });

            var route = 'mentorias.duracion';
            var field = 'Duración de la sesión';
            this.onRequest(data, route, field, e.target);

        },

        /**
         * guarda pregunta : fases del proceso
         * @param {event} e
         */
        onAddMentoringPhases: function (e) {
            e.preventDefault();
            e.stopImmediatePropagation();

            var data = window.Misc.formToJson( e.target );
            _.extend(data, {
                                codigo_mentoria_masiva: this.$mentoria_masiva,
                                codigo_sesion_masiva: this.$codigo_sesion_masiva,
                                array_sesiones: this.$array_sesiones
                            });

            var route = 'mentorias.phase';
            var field = 'Fases del proceso';
            this.onRequest(data, route, field, e.target);

        },

        /**
         * guarda pregunta : Diadnóstico
         * @param {event} e
         */
        onAddMentoringDiagnosis: function (e) {
            e.preventDefault();
            e.stopImmediatePropagation();

            var data = window.Misc.formToJson( e.target );
            _.extend(data, {
                                codigo_mentoria_masiva: this.$mentoria_masiva,
                                codigo_sesion_masiva: this.$codigo_sesion_masiva,
                                array_sesiones: this.$array_sesiones
                            });

            var route = 'mentorias.diagnosis';
            var field = 'Diagnóstico';
            this.onRequest(data, route, field, e.target);

        },

        /**
         * guarda pregunta : ¿Se recomienda para la siguiente etapa?
         * @param {event} e
         */
        onAddMentoringRecommends: function (e) {
            e.preventDefault();
            e.stopImmediatePropagation();

            var data = window.Misc.formToJson( e.target );
            _.extend(data, {
                                codigo_mentoria_masiva: this.$mentoria_masiva,
                                codigo_sesion_masiva: this.$codigo_sesion_masiva,
                                array_sesiones: this.$array_sesiones
                            });

            var route = 'mentorias.recommends';
            var field = '¿Se recomienda para la siguiente etapa?';
            this.onRequest(data, route, field, e.target);

        },

        /**
         * guarda pregunta : Seguimiento
         * @param {event} e
         */
        onAddMentoringTracing: function (e) {
            e.preventDefault();
            e.stopImmediatePropagation();

            var data = window.Misc.formToJson( e.target );
            _.extend(data, {
                                codigo_mentoria_masiva: this.$mentoria_masiva,
                                codigo_sesion_masiva: this.$codigo_sesion_masiva,
                                array_sesiones: this.$array_sesiones
                            });

            var route = 'mentorias.seguimientos';
            var field = 'Seguimiento';
            this.onRequest(data, route, field, e.target);

        },

        /**
         * se encarga se hacer la solicitud al controlador
         * @param {object} data datos para enviar en la solicitud
         * @param {string} route ruta que atienda la solicitud
         * @return {void} void
         */
        onRequest: function (data, route, field_name, target) {
            var _this = this;
            //validar si hay sesiones seleccionadas
            if (!this.$count_desarrollo > 0) {
                text = 'Debe seleccionar al menos una sesión';

                var config = {
                    'text': text,
                    'type': 'info'
                };

                _.extend( _this.configAlert, config );

                window.Misc.showAlertBox( _this.configAlert );

                return;
            }
            
            if( this.acceptConfirm instanceof Backbone.View ) {
                this.acceptConfirm.stopListening();
                this.acceptConfirm.undelegateEvents();
            }

            this.acceptConfirm = new app.ConfirmWindow({
                parameters: {
                    template: this.templateConfirmDevelopmentSessions({
                        count_asesorias: _this.$count_desarrollo,
                        field_name : field_name
                    }),
                    titleConfirm: "Confirmación",
                    onConfirm: function () {

                        $.ajax({
                            url: window.Misc.urlFull(Route.route(route)),
                            type: 'POST',
                            data: data,
                            // beforeSend: function() {
                            //     var config = {
                            //         'text': 'Espere mientras se cargan los datos...',
                            //         'type': 'info'
                            //     };
    
                            //     _.extend( _this.configAlert, config );
    
                            //     window.Misc.showAlertBox( _this.configAlert );
                            //     window.Misc.setSpinner( $('#form-development-sessions') );
                            // }
                        })
                        .success (function (data, textStatus, jqXHR ) {

                            window.Misc.removeSpinner({
                                'wrap': $('#form-development-sessions'),
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

                            //limpiamos formulario
                            document.getElementById(target.id).reset();

                            // recargamos tabla de sesiones
                            _this.onReloadSesionUnicaTabla(route);
                        })
                        .error (function ( XMLHttpRequest, textStatus, errorThrown ) {
                            const text = 'Contacte con el administrador';
                            const type = 'alert';

                            var config = {
                                'text': text,
                                'type': type
                            };

                            _.extend( _this.configAlert, config );

                            window.Misc.showAlertBox( _this.configAlert );
                        });

                    }
                }
            });

            this.acceptConfirm.render();
        },

        /**
         * Busca sesión masiva y carga Mentorias/sesiones asociadas
         * @param {event} e
         */
        onSearchSession: function (e) {
            e.preventDefault();
            // e.stopPropagation();
            // e.stopImmediatePropagation();
            if (e.target.value == null) return;

            this.$searchIdSession = this.$codigo_sesion_masiva = e.target.value;

            this.$array_sesiones = [];
            this.$count_desarrollo = 0;

            this.sesionUnicaTabla.ajax.reload();

            this.$form_development_session.html( this.templateDevelopmentSessions );

        },

        /**
         * Habilita/deshabilita campo Diagnostico
         * de acuerdo al requerimiento
         * @param {event} e
         */
        onChangePhase: function (e) {
            e.preventDefault();
            if (e.target.value == 'diagnóstico'){
                $("#diagnosis-row").removeClass("hidden");
            } else {
                $("#mentoring-diagnosis")[0].selectedIndex = 0;
                $("#diagnosis-row").addClass("hidden");
            }
        },

        /**
         * finalizar sesión de mentoria
         * @param {event} e 
         */
        onMentoringFinish: function (e) {
            e.preventDefault();

            var data = window.Misc.formToJson( e.target );
            _.extend(data, {
                                codigo_mentoria_masiva: this.$mentoria_masiva,
                                codigo_sesion_masiva: this.$codigo_sesion_masiva,
                                array_sesiones: this.$array_sesiones
                            });

            var route = 'mentorias.finalizar';
            var field = 'La Finalización';
            this.onRequest(data, route, field, e.target);

        },

        /**
         * cancelar sesión de mentoria
         * @param {event} e 
         */
        onMentoringCancel: function (e) {
            e.preventDefault();

            var data = window.Misc.formToJson( e.target );
            _.extend(data, {
                                codigo_mentoria_masiva: this.$mentoria_masiva,
                                codigo_sesion_masiva: this.$codigo_sesion_masiva,
                                array_sesiones: this.$array_sesiones
                            });

            var route = 'mentorias.cancelar';
            var field = 'Cancelar';
            this.onRequest(data, route, field, e.target);
        },

        /**
         * Return to history DOM
         */
        onBack: function (e) {
            e.preventDefault();
            e.stopImmediatePropagation();

            window.history.back();
        },

        /**
         * @param {string} route
         * @return {void} void
         */
        onReloadSesionUnicaTabla: function (route) {

            if (
                route === 'mentorias.finalizar'
                || route === 'mentorias.cancelar'
            ) {
                // recargamos tabla de sesiones
                this.sesionUnicaTabla.ajax.reload();

                this.$array_sesiones = [];
                this.$count_desarrollo = 0;
            }

            return;
        },
    });

})(jQuery, this, this.document);
