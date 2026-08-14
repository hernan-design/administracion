/**
* Class MentoriaMasivaView extends of Backbone View
* @author TuProyecto || Desarrollador : @ayn
* @link http://TuProyecto.com
*/

//Global App Backbone
app || (app = {});

(function ($, window, document, undefined) {

    app.MentoriaMasivaView = Backbone.View.extend({

        el: '#mentoria-masiva',
        template: _.template( ($('#projects-list-tpl').html() || '') ),
        templateConfirm: _.template( ($('#confirm-project-tpl').html() || '') ),
        templateConfirmExportData: _.template( ($('#confirm-export-data-tpl').html() || '') ),
        templateButtonsAction: _.template( ($('#buttons-action-tpl').html() || '') ),
        templateItemEntity: _.template( ($('#item-entity-tpl').html() || '') ),
        templateMentorList : _.template( ($('#mentor-assignment-massive-list-tpl').html() || '') ),


        events: {
            'click .btn-search': 'search',
            'click .btn-clear': 'clear',
            'click #delete-mentor': 'onDeleteMentor',
            'change .checkbox_select_entity': 'selectedEntity',
            'click #add_mentorships': 'addMentorships',
            'submit #form-user': 'selectedMentor',
            'click .close-button': 'removeEntity',
            // 'click #mentoria-masiva-lista': 'onViewMentoringList',
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

            this.entities = [];
            this.mentor = [];

            this.$listEvaluators = this.$('#list-mentor');



        },

        /*
         * Render View Element
         */
        render: function() {

            // Initialize attributes
            this.$projectsListTable = this.$('#projects-list-table');
            this.$mentoriaMasivaTable = this.$('#mentoria-masiva-table');

            this.$email  = this.$('#email');
            this.$emprendimiento  = this.$('#emprendimiento');
            this.$phase  = this.$('#phase');

            // overwrite model id attribute
            this.referenceViews();
        },

        /**
         * reference to views
         */
        referenceViews: function () {
            // console.log('reference s');

            var _this = this;
            this.projectsListTable = this.$projectsListTable.DataTable({
                dom:"<'row'<'col-sm-5'i><'col-sm-7'p>>" +
                    "<'row'<'col-sm-12'tr>>" +
                    "<'row'<'col-sm-5'i><'col-sm-7'p>>",
                processing: true,
                //serverSide: true,
                // orderable: false,
                // searching: true,
                language: window.Misc.dataTableES(),
                ajax: {
                    url: window.Misc.urlFull( Route.route('mentorias.masivas.index') ),
                    data: function( data ) {
                        data.email   = _this.$email.val();
                        data.emprendimiento   = _this.$emprendimiento.val();
                        data.phase  = _this.$phase.val();
                    },
                },
                columns: [
                    // { data: 'numero_registro_convocatoria', name: 'numero_registro_convocatoria' },
                    { data: 'nombre_entidad', name: 'nombre_entidad' },
                    { data: 'nombre_usuario', name: 'nombre_usuario' },
                    { data: 'nombre_ciudad', name: 'nombre_ciudad' },
                    { data: 'fecha_registro_fase_usuario', name: 'fecha_registro_fase_usuario' },
                    { data: 'nombre_fase', name: 'nombre_fase' },
                    { data: 'estado', name: 'estado' },
                    // { data: 'puntaje', name: 'puntaje'}
                ],
                order: [
                    [ 0, 'desc' ]
                ],
                columnDefs: [
                    {
                        targets: 0,
                        width: '8%',
                    },
                    // {
                    //     targets: 1,
                    //     render: function ( data, type, full, row ) {
                    //         return '<a href="' + window.Misc.urlFull( Route.route('entidad.show', {entidad: full.nickname_usuario})) + '" title="Ver proyecto">'+full.nombre_usuario+'</a>';
                    //     }
                    // },
                    {
                        targets: 6,
                        width: '10%',
                        orderable: false,
                        className: 'text-center',
                        render: function ( data, type, full, row ) {
                            return _this.templateButtonsAction( {full: full, row: row} )
                        }
                    },
                    {
                        targets: [0, 1, 2, 3, 4],
                        className: 'text-center',
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
            this.projectsListTable.ajax.reload();
        },

        clear: function(e) {
            e.preventDefault();
            this.$email.val('');
            this.$emprendimiento.val('');
            this.$phase.val('1');

            this.projectsListTable.ajax.reload();
        },

        /**
         *  Quitar mentor de la vista
         * @param {target event} e
         * @returns {void}
         */
        onDeleteMentor: function (e){
            e.preventDefault();

            this.$listEvaluators.slideUp();

            this.$listEvaluators.html('');

            //limpiamos formulario
            document.getElementById('form-user').reset();

            delete this.entities.codigo_tipo_asesoria;
            delete this.entities.codigo_usuario_mentor;

            return;
        },

        /**
         * Controlador de eventos que se ejecuta cuando un elemento de la
         * lista es seleccionado o deseleccionado
         * @param {Event} e
         * @returns {void}
         */
        selectedEntity: function (e){
            e.preventDefault();

            var value = e.currentTarget.value;
            var entity = JSON.parse(value);
            var row = entity.row = e.currentTarget.id

            if (e.currentTarget.checked) {
                this.entities.push(entity);
            } else {
                this.entities = this.entities.filter(function(element){
                    return element.row != row;
                })

            }

            // actualiza el elemento HTML con la plantilla
            this.$('.item-entity').html( this.templateItemEntity( this.entities ) );

        },

        /**
         * permite remover entidad desde la lista
         * de entidades seleccionadas
         *
         * @param {Event target} e
         */
        removeEntity: function (e) {
            e.preventDefault();

            var row = e.currentTarget.id

            this.entities = this.entities.filter(function(element){
                return element.row != row;
            })

            //para deseleccionar elemento del check de la tabla
            document.getElementById(row).checked = false;

            // actualiza el elemento HTML con la plantilla
            this.$('.item-entity').html( this.templateItemEntity( this.entities ) );
        },

        /**
         * agrega mentoria masiva
         * @param {event target} e
         * @returns respuesta del servidor
         */
        addMentorships: function(e){
            e.preventDefault();
            e.stopImmediatePropagation();
            _this = this;

            //alerta para cuando no ha seleccionado mentor o entidad
            if (this.entities.length < 1) {
                text = 'Debe seleccionar al menos una entidad';
                window.Misc.alertDialog({
                    text: text
                });
                return;
            } else if (this.mentor.length < 1) {
                text = 'Debe seleccionar un mentor';
                window.Misc.alertDialog({
                    text: text
                });
                return;
            }

            var data = {
                data: this.entities,
                mentor : this.mentor
            }

            window.Misc.setSpinner( $('#confirm-entities') );


            $.post(document.url + Route.route('mentorias.masivas.store'), data,function(resp) {

                // console.log();
            }).success (function (data, textStatus, jqXHR ) {
                window.Misc.removeSpinner({
                    'wrap': $('#confirm-entities'),
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

                //recargar datatable para limpiar checkbox
                _this.projectsListTable.ajax.reload();

                //limpiar array de entidades
                _this.entities = [];

                // actualiza el elemento HTML con la plantilla
                _this.$('.item-entity').html( '' );


            }).error (function ( XMLHttpRequest, textStatus, errorThrown ) {
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

        /**
         * renderiza evaluador seleccionado
         * @param {event target} e
         * @returns void
         */
        selectedMentor: function (e) {
            e.preventDefault();
            var data =  window.Misc.formToJson(e.currentTarget);

            this.$listEvaluators.slideUp();

            this.$listEvaluators.html('');

            _this = this;

            $.get(document.url + Route.route('mentorias.getMentor') , data, function (resp) {

            }).success (function (response, textStatus, jqXHR ) {
                const text = response.success ? response.message : response.errors;
                const type = response.success ? 'success' : 'alert';
                // console.log('response', response);

                _this.$listEvaluators.slideDown("slow").append( _this.templateMentorList(response) );

                // _.extend( _this.entities, {codigo_usuario_mentor: response.codigo_usuario} );
                // _.extend( _this.entities, {codigo_tipo_asesoria: data.codigo_tipo_asesoria} );
                // _this.entities.push({codigo_usuario_mentor: response.codigo_usuario})
                var dat = {
                    codigo_usuario_mentor: response.codigo_usuario,
                    correo_electronico: response.correo_electronico,
                    codigo_tipo_asesoria: data.codigo_tipo_asesoria
                }
                _this.mentor = [];
                _this.mentor.push(dat);
                // console.log(_this.mentor);

            }).error (function ( XMLHttpRequest, textStatus, errorThrown ) {

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



    });

})(jQuery, this, this.document);
