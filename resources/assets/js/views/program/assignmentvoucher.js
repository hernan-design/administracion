/**
 * Class AsigmentMentor extends of Backbone View
 * @author TuProyecto || Desarrollador : @AyrielNoriega
 * @link http://TuProyecto.com
 */


//Global App Backbone
app || (app = {});

(function ($, window, document, undefined) {

    app.AsigmentVoucherView = Backbone.View.extend({
        el: '#convocatoria-asignacion-voucher',
        templateConfirm: _.template(($('#turn-confirm-tpl').html() || '')),
        templateMentorList: _.template(($('#mentor-assignment-massive-list-tpl').html() || '')),
        templateButtonsAction: _.template(($('#buttons-action-tpl').html() || '')),
        templateButtonsSelectSession: _.template(($('#buttons-select-session-tpl').html() || '')),
        templateConfirmNoVoucher: _.template($("#confirm-no-voucher-tpl").html() || ""),
        templateConfirmVoucher: _.template($("#confirm-voucher-tpl").html() || ""),
        templateQuestionVoucher: _.template($("#section-question-voucher-tpl").html() || ""),
        templateFormVouchers: _.template($("#form-voucher-toggle-tpl").html() || ""),
        events: {
            'submit #form-assignment-voucher': 'assignmentvoucher',
            'click #no_voucher': 'onNoAsignmentVoucher',
            'click #si_voucher': 'onAsignmentVoucher',
            'click .select_session': 'onSelectSession',

        },
        parameters: {
            //
        },

        /**
         * Constructor Method
         */
        initialize: function (opts) {
            //Init Attribute
            this.configAlert = {
                'wrapper': '#main-wrap',
                'closeTime': 6000,
                'speedOpen': 500,
                'speedClose': 500
            };

            this.$listEvaluators = this.$('#list-mentor');

            this.mentor = [];

            this.$assignmentVoucherListTable;
            this.$mentoringsVouchersTable = this.$('#mentorings-vouchers-table');

            //numero de sesion de mentoria
            this.$sessionmentoria = 0;
            this.$categoriasesion = 0;
            this.$observacion = '';

        },

        /*
        * Render View Element
        */
        render: function () {
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
        assignmentvoucher: function (e) {
            e.preventDefault();
            e.stopImmediatePropagation();
            var data = window.Misc.formToJson(e.currentTarget);

            data.sessionmentoria = this.$sessionmentoria;
            $.extend(data, this.parameters);


            if (!data.hasOwnProperty('codigo_categoria_producto')) {
                data.codigo_categoria_producto = '';
            }

            window.Misc.setSpinner( e.currentTarget );

            // this.$listEvaluators.slideUp();

            // this.$listEvaluators.html('');

            _this = this;

            this.acceptConfirm = new app.ConfirmWindow({
                parameters: {
                    template: this.templateConfirmVoucher(),
                    titleConfirm: "Confirmación",
                    onConfirm: function () {
                        $.ajax({
                            url: window.Misc.urlFull(Route.route('convocatoria.fase.voucher.update')),
                            type: 'PUT',
                            data: data
                        }).success(function (response, textStatus, jqXHR) {
                            const text = response.success ? response.message : response.errors;
                            const type = response.success ? 'success' : 'alert';

                            var config = {
                                'text': text,
                                'type': type
                            };

                            _.extend(_this.configAlert, config);

                            window.Misc.showAlertBox(_this.configAlert);

                            window.Misc.removeSpinner({
                                'wrap': e.currentTarget,
                                'direct': false
                            });

                            if (response.success)
                                window.location.reload()
                        }).error(function (XMLHttpRequest, textStatus, errorThrown) {
                            // $('#add_mentor').prop( "disabled",  false );

                            const text = 'Contacte con el administrador';
                            const type = 'alert';

                            var config = {
                                'text': text,
                                'type': type
                            };

                            _.extend(_this.configAlert, config);

                            window.Misc.showAlertBox(_this.configAlert);
                        });
                    }
                }
            });

            this.acceptConfirm.render();

        },


        /**
         *
         */
        renderAssignments: function (data) {
            var _this = this;
            this.mentoringsVouchersTable = this.$mentoringsVouchersTable.DataTable({
                dom: "<'row'<'col-sm-5'i><'col-sm-7'p>>" +
                    "<'row'<'col-sm-12'tr>>" +
                    "<'row'<'col-sm-5'i><'col-sm-7'p>>",
                // scrollY: '400px',
                // scrollCollapse: true,
                // paging: false,
                processing: true,
                serverSide: false,
                language: window.Misc.dataTableES(),
                searching: false,
                ajax: {
                    url: window.Misc.urlFull(Route.route('convocatoria.fase.voucher.getMentorignsPhase')),
                    data: function (data) {
                        data.codigo_registro_fase_usuario = _this.parameters.codigo_registro_fase_usuario
                    }
                },
                columns: [
                    { data: 'codigo_asesoria', name: 'codigo_asesoria' },
                    // { data: 'nombre_fase', name: 'nombre_fase' },
                    // { data: 'nombre_mentor', name: 'nombre_mentor' },
                    // { data: 'nombre_categoria_producto', name: 'nombre_categoria_producto' },
                ],
                order: [
                    [0, 'desc']
                ],
                columnDefs: [
                    {
                        targets: 0,
                        width: '10%'
                    },
                    {
                        targets: 1,
                        orderable: true,
                        render: function (data, type, full, row) {
                            return window._tp.getStateName[full.codigo_estado];
                        }
                    },
                    {
                        targets: 2,
                        orderable: true,
                        render: function (data, type, full, row) {
                            return full.nombre_fase;
                        }
                    },
                    {
                        targets: 3,
                        orderable: true,
                        render: function (data, type, full, row) {
                            return full.nombre_mentor + ' <br>' + full.correo_mentor;
                        }
                    },
                    {
                        targets: 4,
                        orderable: true,
                        render: function (data, type, full, row) {
                            return full.codigo_mentoria;
                        }
                    },
                    {
                        targets: 5,
                        orderable: true,
                        render: function (data, type, full, row) {
                            if (full.estado_sesion != null) {
                                return window._tp.getStateName[full.estado_sesion];
                            }

                            return '';
                        }
                    },
                    {
                        targets: 6,
                        orderable: false,
                        className: 'text-center',
                        render: function (data, type, full, row) {
                            if (!_.isNull(full.codigo_mentoria)) {
                                return _this.templateButtonsSelectSession({ full: full, row: row });
                            }
                            return '';
                        }
                    },
                ]
            });
        },

        /**
         * cardar datos en tabla assignmentVoucherListTable
         */
        renderTableVouchers: function () {
            var _this = this;
            this.assignmentVoucherListTable = this.$assignmentVoucherListTable.DataTable({
                dom: "<'row'<'col-sm-5'i><'col-sm-7'p>>" +
                    "<'row'<'col-sm-12'tr>>" +
                    "<'row'<'col-sm-5'i><'col-sm-7'p>>",
                scrollY: '400px',
                scrollCollapse: true,
                paging: false,
                processing: true,
                serverSide: false,
                language: window.Misc.dataTableES(),
                searching: false,
                ajax: {
                    url: window.Misc.urlFull(Route.route('convocatoria.fase.voucher.index')),
                    data: function (data) {
                        data.codigo_registro_fase_usuario = _this.parameters.codigo_registro_fase_usuario,
                            data.codigo_mentoria = _this.$sessionmentoria
                    }
                },
                columns: [
                    { data: 'codigo_categoria_producto', name: 'codigo_categoria_producto' },
                    { data: 'nombre_categoria_producto', name: 'nombre_categoria_producto' },
                ],
                order: [
                    [0, 'desc']
                ],
                columnDefs: [
                    // {
                    //     targets: 0,
                    //     width: '10%'
                    // },
                    // {
                    //     targets: 2,
                    //     orderable: true,
                    //     render: function ( data, type, full, row ) {
                    //         return full.nombre_mentor + ' <br>' + full.correo_mentor;
                    //     }
                    // },
                    // {
                    //     targets: 3,
                    //     orderable: true,
                    //     render: function ( data, type, full, row ) {
                    //         return window._tp.getStateName[full.codigo_estado];
                    //     }
                    // },
                    {
                        targets: 0,
                        // orderable: false,
                        className: 'text-center',
                        render: function (data, type, full, row) {
                            return _this.templateButtonsAction({ full: full, row: row });
                        }
                    },
                ]
            });
        },

        /**
         * Load spinner on the request
         */
        loadSpinner: function () {
            window.Misc.setSpinner(this.$gridSearchTable);
        },

        onNoAsignmentVoucher: function (e) {
            e.preventDefault();
            e.stopPropagation();

            // quitar formulario vouchers
            $('#form-voucher-toggle').html('');

            var _this = this,
                data = {
                    sessionmentoria: this.$sessionmentoria,
                    codigo_registro_fase_usuario: this.parameters.codigo_registro_fase_usuario,
                }

            _this = this;
            this.acceptConfirm = new app.ConfirmWindow({
                parameters: {
                    template: this.templateConfirmNoVoucher({
                        // action: 'eliminar',
                        // nombre_mentor: $(e.currentTarget).attr('data-name')
                    }),
                    titleConfirm: "¿Está seguro de no asignar vouchers?",
                    onConfirm: function () {
                        var comment = {
                            comentario_19: $('#comentario_19').val()
                        }

                        $.extend(data, comment);

                        $.post(document.url + Route.route('convocatoria.fase.voucher.storeComment'), data, function (resp) {
                        }).success(function (response, textStatus, jqXHR) {
                            const text = response.success ? response.message : response.errors;
                            const type = response.success ? 'success' : 'alert';

                            var config = {
                                'text': text,
                                'type': type
                            };

                            _.extend(_this.configAlert, config);

                            window.Misc.showAlertBox(_this.configAlert);

                            // _this.assignmentVoucherListTable.ajax.reload();
                        }).error(function (XMLHttpRequest, textStatus, errorThrown) {
                            const text = 'Contacte con el administrador';
                            const type = 'alert';

                            var config = {
                                'text': text,
                                'type': type
                            };

                            _.extend(_this.configAlert, config);

                            window.Misc.showAlertBox(_this.configAlert);
                        });
                    }
                }
            });

            this.acceptConfirm.render();
        },

        /**
         * renderiza tabla formulario de vouchers
         * al hacer click en Si.
         * @param {event} e 
         * @returns void
         */
        onAsignmentVoucher: function (e) {
            e.preventDefault();
            e.stopPropagation();

            // mostrar formulario de vouchers
            $('#form-voucher-toggle').html(this.templateFormVouchers({comentario: this.$observacion, categoriasesion: this.$categoriasesion}));
            this.$assignmentVoucherListTable = this.$('#assignmentvoucher-list-table');
            if (this.$categoriasesion > 0) {
                $('#submit-voucher').css('display', 'none');
            }

            this.renderTableVouchers();

        },

        onSelectSession: function (e) {
            e.preventDefault();
            e.stopPropagation();

            //reset background color to all bottons
            $('.select_session').css('background-color', '');
            //set background color to botton
            e.target.style.backgroundColor = '#427B6F';

            this.$sessionmentoria = e.target.value;
            this.$categoriasesion = e.target.dataset.categoriasesion;
            this.$observacion = e.target.dataset.observacion;

            $('#form-voucher-toggle').html('');

            if (this.$categoriasesion > 0) {
                $('#section_question_voucher').html('');
                this.onAsignmentVoucher(e);
            } else {
                $('#section_question_voucher').html(this.templateQuestionVoucher({ session: this.$sessionmentoria }));

                if (this.assignmentVoucherListTable !== undefined) {
                    this.assignmentVoucherListTable.ajax.reload();
                }
            }

        },

    });

})(jQuery, this, this.document);
