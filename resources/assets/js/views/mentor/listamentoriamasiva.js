/**
* Class ListaMentoriaMasivaView extends of Backbone View
* @author TuProyecto || Desarrollador : @ayn
* @link http://TuProyecto.com
*/

//Global App Backbone
app || (app = {});

(function ($, window, document, undefined) {

    app.ListaMentoriaMasivaView = Backbone.View.extend({

        el: '#mentoria-masiva-lista',
        //template: _.template( ($('#projects-list-tpl').html() || '') ),

        events: {
            'click #history-back': 'onBack',
        },

        parameters: {
            dataFilter: {}
        },

        /**
         * Constructor Method
         */
        initialize : function(opts) {

        },

        /*
         * Render View Element
         */
        render: function() {
            // Initialize attributes
            this.$mentoriaMasivaTable = this.$('#mentoria-masiva-table');

            // this.$searchIdentificacion  = this.$('#numero_identificacion');

            // overwrite model id attribute
            this.referenceViews();
        },

        /**
         * reference to views
         */
        referenceViews: function () {

            var _this = this;
            this.mentoriaMasivaTable = this.$mentoriaMasivaTable.DataTable({
                dom:"<'row'<'col-sm-5'i><'col-sm-7'p>>" +
                    "<'row'<'col-sm-12'tr>>" +
                    "<'row'<'col-sm-5'i><'col-sm-7'p>>",
                processing: true,
                //serverSide: true,
                // orderable: false,
                // searching: true,
                language: window.Misc.dataTableES(),
                ajax: {
                    url: window.Misc.urlFull( Route.route('mentorias.masivas.lista') ),
                    // data: function( data ) {
                    //     data.identificacion_usuario   = _this.$searchIdentificacion.val();
                    //     data.nombre_usuario   = _this.$searchNombre.val();
                    //     data.codigo_etapa_proyecto  = _this.$searchStageProyect.val();
                    //     data.codigo_categoria_proyecto  = _this.$searchCategoryProyect.val();
                    // },
                },
                columns: [
                    { data: 'codigo_mentoria_masiva', name: 'codigo_mentoria_masiva' },
                    { data: 'nombre_mentor', name: 'nombre_mentor' },
                    { data: 'codigos_asesorias', name: 'codigos_asesorias' },
                    { data: 'fecha_creacion', name: 'fecha_creacion' },
                ],
                order: [
                    [ 0, 'desc' ]
                ],
                columnDefs: [
                    {
                        targets: 0,
                        width: '8%',
                        render: function ( data, type, full, row ) {
                                    return '<a href="' + window.Misc.urlFull( Route.route('mentorias.masivas.show', {mentoria: full.codigo_mentoria_masiva})) + '" title="Ver mentorías">'+full.codigo_mentoria_masiva+'</a>';
                                }
                    },
                    {
                        targets: 3,
                        render: function ( data, type, full, row ) {
                            return moment(full.fecha_creacion).format('LL');
                        }
                    },
                    {
                        targets: [0, 1, 2, 3],
                        className: 'text-center',
                    }
                ]
            });

        },

        /**
         * Return to history DOM
         */
           onBack: function (e) {
            e.preventDefault();
            e.stopImmediatePropagation();

            window.history.back();
        },

    });

})(window.jQuery, window, document);
