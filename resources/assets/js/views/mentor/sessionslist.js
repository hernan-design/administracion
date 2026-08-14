/**
* Class MentorListView extends of Backbone View
* @author TuProyecto || Desarrollador : @dortegon
* @link http://TuProyecto.com
*/

//Global App Backbone
app || (app = {});

(function ($, window, document, undefined) {

    app.SessionsListView = Backbone.View.extend({

        el: '#sessions-content',
        templateButtonsStateMentoring: _.template( ($('#button-StateMentoring-tpl').html() || '') ),
        templateConfirm: _.template( ($('#confirm-content-confirm-activate-mentoring-tpl').html() || '') ),
        events: {
            'click .btn-search': 'search',
            'click .btn-clear': 'clear',
            'click .success': 'onModalConfirm',
            'click .danger': 'onModalConfirm',
            'click .ActivateMentoring': 'onModalConfirm',
            'click .data-export-sessions': 'onExportSessions',
        },
        parameters: {
            dataFilter: {}
        },

        /**
         * Constructor Method
         */
        initialize : function(opts) {
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

            // Initialize attributes
            this.$sessionsListSearchTable = this.$('#sessions-list-search-table');

            this.$codigo_asesoria  = this.$('#codigo_asesoria');
            /*this.$searchMentor  = this.$('#correo_electronico_mentor_filter_data');
            this.$filterMentor  = this.$('#nombre_mentor_filter_data');
            this.$searchEntrepreneur  = this.$('#correo_electronico_emprendedor_filter_data');
            this.$filterEntrepreneur  = this.$('#nombre_emprendedor_filter_data');
            this.$searchState   = this.$('#codigo_estado');*/

            // overwrite model id attribute
            this.referenceViews();
        },

        /**
         * reference to views
         */
        referenceViews: function () {

            var _this = this;
            var table;

            if ( $.fn.dataTable.isDataTable( this.$sessionsListSearchTable ) ) {
                table = this.sessionsListSearchTable = this.$sessionsListSearchTable.DataTable();
            }
            else {
                table = this.sessionsListSearchTable = this.$sessionsListSearchTable.DataTable({
                        dom:"<'row'<'col-sm-5'i><'col-sm-7'p>>" +
                        "<'row'<'col-sm-12'tr>>" +
                        "<'row'<'col-sm-5'i><'col-sm-7'p>>",
                        processing: true,
                       // paging: false,
                        serverSide: true,
                        language: window.Misc.dataTableES(),
                        ajax: {
                            url: window.Misc.urlFull( Route.route('mentoria.sesiones') ),
                            data: function( data ) {
                                data.codigo_asesoria = _this.$codigo_asesoria.val();
                              /*  data.correo_electronico_mentor = _this.$searchMentor.val();
                                data.correo_electronico_emprendedor = _this.$searchEntrepreneur.val();
                                data.codigo_estado = _this.$searchState.val();*/
                            },
                        },
                        columns: [
                            { data: 'codigo_sesion',    name: 'codigo_sesion'},
                            { data: 'nombre_evento',    name: 'nombre_evento' },
                            { data: 'fecha_evento',     name: 'fecha_evento' },
                            { data: 'fecha_fin_evento', name: 'fecha_fin_evento' },
                            { data: 'fecha_finalizo_mentoria', name: 'fecha_finalizo_mentoria' },
                            { data: 'codigo_estado', name: 'codigo_estado' },
                            { data: 'duration_mentoring', name: 'duration_mentoring' },
                        ],
                        order: [
                            [ 0, 'asc' ]
                        ],
                        columnDefs: [
                            {
                                targets: 5,
                                render: function ( data, type, full, row ) {
                                    return window._tp.getStateName[data];
                                }
                            },
                            {
                                targets: 6,
                                className: 'text-center',
                                render: function ( data, type, full, row ) {
                                    if (data != null) {
                                        var horas = data.split(',')
                                        if (horas[0] == '') {
                                            horas[0] = 0;
                                        }
                                        if (horas[1] == '') {
                                            horas[1] = 1;
                                        }
                                        return horas[0] + " horas y " + horas[1] + " minutos"
                                    }

                                    return '---';
                                }
                            }
                        ],

                });
            }
        },

        /**
        * Load spinner on the request
        */
        loadSpinner: function () {
            window.Misc.setSpinner( this.$gridSearchTable );
        },

        search: function(e) {
            e.preventDefault();
            this.sessionsListSearchTable.ajax.reload();
        },

        clear: function(e) {
            e.preventDefault();

            this.$searchMentorType.val('');
            this.$searchMentor.val('');
            this.$filterMentor.val('');
            this.$searchState.val('');
            this.$searchEntrepreneur.val('');
            this.$filterEntrepreneur.val('');

            this.sessionsListSearchTable.ajax.reload();
        },

        onExportSessions: function (e) {
            e.preventDefault();

            var data = {codigo_asesoria: this.$codigo_asesoria.val()}

            _this = this;
            if(data.codigo_asesoria != null && data.codigo_asesoria != ''){
                $.ajax({
                    data: data,
                    url: window.Misc.urlFull( Route.route('mentoria.reporte.index')),
                    type: 'get',
                    success: function (data, textStatus, jqXHR ) {
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
 
        },


    });

})(jQuery, this, this.document);
