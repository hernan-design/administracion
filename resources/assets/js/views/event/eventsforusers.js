/**
* Class EventsForUsersView extends of Backbone View
* @author TuProyecto || Desarrollador : @ayrielnoriega
* @link http://TuProyecto.com
*/

//Global App Backbone
app || (app = {});

(function ($, window, document, undefined) {

    app.EventsForUsersView = Backbone.View.extend({

        el: '#events-content',
        template: _.template( ($('#events-for-user-tpl').html() || '') ),
        templateConfirm: _.template( ($('#confirm-event-tpl').html() || '') ),
        //templateButtonsAction: _.template( ($('#buttons-action-tpl').html() || '') ),
        events: {
            'click .btn-search': 'search',
            'click .btn-clear': 'clear',
        },
        parameters: {
            dataFilter: {}
        },

        /**
         * Constructor Method
         */
        initialize : function(opts) {
            //_.bindAll(this, 'render', 'cloneEvent');
        },

        /*
         * Render View Element
         */
        render: function() {

            var attributes = {};
            this.$el.html( this.template(attributes) );

            // Initialize attributes
            this.$eventListSearchTable  = this.$('#event-list-search-table');
            this.$searchIdentificacion  = this.$('#identificacion_usuario');
            this.$searchEmail           = this.$('#correo_electronico');
            this.$searchcodigo_usuario  = this.$('#codigo_usuario');

            // overwrite model id attribute
            this.referenceViews();
        },

        /**
         * reference to views
         */
        referenceViews: function () {

            var _this = this;
            this.eventListSearchTable = this.$eventListSearchTable.DataTable({
                responsive: true,
                dom:"<'row'<'col-sm-5'i><'col-sm-7'p>>" +
                "<'row'<'col-sm-12'tr>>" +
                "<'row'<'col-sm-5'i><'col-sm-7'p>>",
                processing: true,
                serverSide: true,
                language: window.Misc.dataTableES(),
                ajax: {
                    url: window.Misc.urlFull( Route.route('eventos.usuario.show') ),
                    data: function( data ) {
                        data.identificacion_usuario   = _this.$searchIdentificacion.val();
                        data.email_usuario  = _this.$searchEmail.val();
                        data.codigo_usuario  = _this.$searchcodigo_usuario.val();
                    },
                },
                columns: [
                    { data: 'codigo_evento',           name: 'codigo_evento'  },
                    { data: 'nombre_evento',           name: 'nombre_evento'  },
                    { data: 'fecha_evento',            name: 'fecha_evento'  },
                    { data: 'fecha_fin_evento',        name: 'fecha_fin_evento'  },
                    { data: 'asistio',                 name: 'asistio'  }, //4
                    { data: 'certificado_evento',      name: 'certificado_evento'  }, //5
                    { data: 'codigo_usuario_evento',   name: 'codigo_usuario_evento'  }, //6
                    { data: 'factura_evento',          name: 'factura_evento'  }, //7
                ],
                order: [
                    [ 0, 'desc' ]
                ],
                columnDefs: [
                    {
                        targets: 0,
                        width: '5%',
                    },
                    {
                        targets: 1,
                        render: function ( data, type, full, row ) {
                            return '<a href="' + window.Misc.urlFull(Route.route('evento.edit', {evento: full.codigo_evento}) ) + '" title="Ver evento" style="color:#3FC1A5">'+full.nombre_evento+'</a>';
                        }
                    },
                    {
                        targets: 4,
                        render: function ( data, type, full, row ) {
                            return data ? 'Si' : 'No';
                        }
                    },
                    {
                        targets: 5,
                        render: function ( data, type, full, row ) {
                            return data ? full.fecha_certificado : 'No';
                        }
                    },
                    {
                        targets: 6,
                        render: function ( data, type, full, row ) {
                            var certificado_evento = full.certificado_evento;
                            if(certificado_evento)
                                return full.codigo_usuario_evento;
                            else
                                return 'No Certificado';
                        }
                    },
                    {
                        targets: 7,
                        render: function ( data, type, full, row ) {
                            return data ? full.factura_evento : 'No';
                        }
                    }
                   /* {
                        targets: 3,
                        width: '10%',
                        orderable: false,
                        className: 'text-center',
                        render: function ( data, type, full, row ) {
                            return _this.templateButtonsAction( {full: full} );
                        }
                    }
                    */
                ],
                fnRowCallback: function( row, data ) {
                    if ( data.codigo_estado == window._tp.getState['inactive'] ) {
                        $(row).find('.green').attr( "class", "glyphicon glyphicon-remove red" );
                        $(row).find('.success').attr( "title", "Activar evento" );
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
            this.eventListSearchTable.ajax.reload();
        },

        clear: function(e) {
            e.preventDefault();

            this.$searchEmail.val('');
            this.$searchIdentificacion.val('');

            this.eventListSearchTable.ajax.reload();
        }
    });

})(jQuery, this, this.document);
