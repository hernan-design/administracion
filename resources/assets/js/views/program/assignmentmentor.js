/**
 * Class AsigmentMentor extends of Backbone View
 * @author TuProyecto || Desarrollador : @AyrielNoriega
 * @link http://TuProyecto.com
 */


//Global App Backbone
app || (app = {});

(function ($, window, document, undefined) {

    app.AsigmentMentorView = Backbone.View.extend({
        el: '#convocatoria-asignacion-mentor',
        templateConfirm: _.template( ($('#turn-confirm-tpl').html() || '') ),
        templateMentorList : _.template( ($('#mentor-assignment-massive-list-tpl').html() || '') ),
        templateButtonsAction: _.template( ($('#buttons-action-tpl').html() || '') ),
        templateConfirmAction: _.template($("#confirm-mentoring-tpl").html( ) || ""),
        events: {
            'submit #form-user': 'selectedMentor',
            'click .delete_mentor': 'onDeleteMentor'
        },
        parameters: {
            //
        },

        /**
         * Constructor Method
         */
        initialize : function(opts) {

            //Init Attribute
            this.configAlert = {
                'wrapper': '#main-wrap',
                'closeTime': 6000,
                'speedOpen': 500,
                'speedClose': 500
            };

            this.$listEvaluators = this.$('#list-mentor');

            this.mentor = [];

            this.$assignmentListTable = this.$('#assignment-list-table');

        },

        /*
        * Render View Element
        */
        render: function() {
            this.renderAssignments();
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

            // this.ready();
        },

        /**
         * fires libraries js
         */
        ready: function () {

            //
        },

        /**
         * Asignar mentor y crear proceso de mentoria
         * @param {event target} e
         * @returns void
         */
        selectedMentor: function (e) {
            e.preventDefault();
            e.stopImmediatePropagation();
            var data =  window.Misc.formToJson(e.currentTarget);
            $.extend( data, this.parameters);

            //si no hay correo seleccionado no continua
            if (data.correo_electronico.length == 0) return;

            //deshabilitamos boton para no recibir mas solicitudes hasta que se complete
            $('#add_mentor').prop( "disabled",  true );

            this.$listEvaluators.slideUp();

            this.$listEvaluators.html('');

            _this = this;
            $.post(document.url + Route.route('convocatoria.storementoria') , data, function (resp) {

            }).success (function (response, textStatus, jqXHR ) {
                const text = response.success ? response.message : response.errors;
                const type = response.success ? 'success' : 'alert';

                _this.$listEvaluators.slideDown("slow").append( _this.templateMentorList(response) );

                var dat = {
                    codigo_usuario_mentor: response.codigo_usuario,
                    correo_electronico: response.correo_electronico,
                    codigo_tipo_asesoria: data.codigo_tipo_asesoria
                }
                _this.mentor = [];
                _this.mentor.push(dat);

                $('#add_mentor').prop( "disabled",  false );
                e.currentTarget.reset();
                var config = {
                    'text': text,
                    'type': type
                };

                _.extend( _this.configAlert, config );

                window.Misc.showAlertBox( _this.configAlert );

                _this.assignmentListTable.ajax.reload();
            }).error (function ( XMLHttpRequest, textStatus, errorThrown ) {
                $('#add_mentor').prop( "disabled",  false );

                const text = 'Contacte con el administrador';
                const type = 'alert';

                var config = {
                    'text': text,
                    'type': type
                };

                _.extend( _this.configAlert, config );

                window.Misc.showAlertBox( _this.configAlert );
            });


            return;
        },


        /**
         * render program entity
         */
        renderAssignments: function (data) {
            var _this = this;
            this.assignmentListTable = this.$assignmentListTable.DataTable({
                dom: "<'row'<'col-sm-5'i><'col-sm-7'p>>" +
                    "<'row'<'col-sm-12'tr>>" +
                    "<'row'<'col-sm-5'i><'col-sm-7'p>>",
                processing: true,
                serverSide: true,
                language: window.Misc.dataTableES(),
                searching: false,
                ajax: {
                    url: window.Misc.urlFull( Route.route('convocatoria.getAssignments') ),
                    data: function( data ) {
                        data.codigo_convocatoria = _this.parameters.codigo_convocatoria;
                        data.nickname_usuario = _this.parameters.nickname_usuario;
                    }
                },
                columns: [
                    { data: 'codigo_asesoria', name: 'codigo_asesoria' },
                    { data: 'nombre_fase', name: 'nombre_fase' },
                ],
                order: [
                    [ 0, 'desc' ]
                ],
                columnDefs: [
                    {
                        targets: 0,
                        width: '10%'
                    },
                    {
                        targets: 2,
                        orderable: true,
                        render: function ( data, type, full, row ) {
                            return full.nombre_mentor + ' <br>' + full.correo_mentor;
                        }
                    },
                    {
                        targets: 3,
                        orderable: true,
                        render: function ( data, type, full, row ) {
                            return window._tp.getStateName[full.codigo_estado];
                        }
                    },
                    {
                        targets: 4,
                        orderable: false,
                        className: 'text-center',
                        render: function ( data, type, full, row ) {
                            return _this.templateButtonsAction( {full: full, row: row} );
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

        onDeleteMentor: function (e) {
            e.preventDefault();
            e.stopPropagation();

            var _this = this,
                id = $(e.currentTarget).data('id');
            var data = {
                id: id,
            }

            _this = this;
            this.acceptConfirm = new app.ConfirmWindow({
                parameters: {
                    template: this.templateConfirmAction({
                        action: 'eliminar',
                        nombre_mentor: $(e.currentTarget).attr('data-name')
                    }),
                    titleConfirm: "Eliminar mentor",
                    onConfirm: function () {
                        $.post(document.url + Route.route('convocatoria.deleteMentor') , data, function (resp) {
                        }).success (function (response, textStatus, jqXHR ) {
                            const text = response.success ? response.message : response.errors;
                            const type = response.success ? 'success' : 'alert';

                            var config = {
                                'text': text,
                                'type': type
                            };
            
                            _.extend( _this.configAlert, config );
            
                            window.Misc.showAlertBox( _this.configAlert );
            
                            _this.assignmentListTable.ajax.reload();
                        }).error (function ( XMLHttpRequest, textStatus, errorThrown ) {
                            // $('#add_mentor').prop( "disabled",  false );
            
                            const text = 'Contacte con el administrador';
                            const type = 'alert';
            
                            var config = {
                                'text': text,
                                'type': type
                            };
            
                            _.extend( _this.configAlert, config );
            
                            window.Misc.showAlertBox( _this.configAlert );
                        });

                        // window.Misc.showAlertBox({
                        //     text: resp.message !== undefined ? resp.message : '...',
                        //     type: 'success'
                        // });

                        // window.Misc.removeSpinner({
                        //     'wrap': _this.$el.parents(),
                        //     'direct': false
                        // });

                    }
                }
            });

            this.acceptConfirm.render();
        }

    });

})(window.jQuery, window, document);
