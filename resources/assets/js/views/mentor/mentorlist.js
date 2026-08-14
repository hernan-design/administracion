/**
* Class MentorListView extends of Backbone View
* @author TuProyecto || Desarrollador : @dortegon
* @link http://TuProyecto.com
*/

//Global App Backbone
app || (app = {});

(function ($, window, document, undefined) {

    app.MentorListView = Backbone.View.extend({

        el: '#mentor-content',
        templateButtonsStateMentoring: _.template( ($('#button-StateMentoring-tpl').html() || '') ),
       // elModal: '#modal-form',
       templateConfirm: _.template( ($('#confirm-content-confirm-activate-mentoring-tpl').html() || '') ),
        events: {
            'click .btn-search': 'search',
            'click .btn-clear': 'clear',
            'click .success': 'onModalConfirm',
            'click .danger': 'onModalConfirm',
            'click .ActivateMentoring': 'onModalConfirm',
        },
        parameters: {
            dataFilter: {}
        },

        /**
         * Constructor Method
         */
        initialize : function(opts) {
            //
        },

        /*
         * Render View Element
         */
        render: function() {

            // Initialize attributes
            this.$mentorListSearchTable = this.$('#mentor-list-search-table');
            this.$searchMentorType  = this.$('#codigo_tipo_asesoria');
            this.$searchMentor  = this.$('#correo_electronico_mentor_filter_data');
            this.$filterMentor  = this.$('#nombre_mentor_filter_data');
            this.$searchEntrepreneur  = this.$('#correo_electronico_emprendedor_filter_data');
            this.$filterEntrepreneur  = this.$('#nombre_emprendedor_filter_data');
            this.$searchState   = this.$('#codigo_estado');

            // overwrite model id attribute
            this.referenceViews();
        },

        /**
         * reference to views
         */
        referenceViews: function () {

            var _this = this;
            this.mentorListSearchTable = this.$mentorListSearchTable.DataTable({
                dom:"<'row'<'col-sm-5'i><'col-sm-7'p>>" +
                "<'row'<'col-sm-12'tr>>" +
                "<'row'<'col-sm-5'i><'col-sm-7'p>>",
                processing: true,
                serverSide: true,
                language: window.Misc.dataTableES(),
                ajax: {
                    url: window.Misc.urlFull( Route.route('mentoria.index') ),
                    data: function( data ) {
                        data.codigo_tipo_asesoria = _this.$searchMentorType.val();
                        data.correo_electronico_mentor = _this.$searchMentor.val();
                        data.correo_electronico_emprendedor = _this.$searchEntrepreneur.val();
                        data.codigo_estado = _this.$searchState.val();
                    },
                },
                columns: [
                    { data: 'codigo_asesoria', name: 'codigo_asesoria'},
                    { data: 'nombre_mentor',        name: 'nombre_mentor' },
                    { data: 'nombre_emprendedor',   name: 'nombre_emprendedor' },
                    { data: 'nombre_emprendimiento',   name: 'nombre_emprendimiento' },
                    { data: 'fecha_asesoria',       name: 'fecha_asesoria' },
                    { data: 'hora_asesoria',       name: 'hora_asesoria', orderable: false },
                    { data: 'nombre_estado',        name: 'nombre_estado' },
                ],
                order: [
                    [ 4, 'desc' ]
                ],
                columnDefs: [
                    {
                        targets: 0,
                        render: function ( data, type, full, row ) {
                            var ucFirst = full.codigo_asesoria;
                            return '<a href="' + window.Misc.urlFull(Route.route('mentoria.show', {mentorium: full.codigo_asesoria}) ) + '" title="Ver mentoría" style="color:#3FC1A5">'+ucFirst+'</a>';
                        }
                    },
                    {

                        targets: 7,
                        className: 'text-center',
                        render: function ( data, type, full, row ) {
                           return _this.templateButtonsStateMentoring( {full: full} )
                        }

 
       
                        

                    }
                ],
                fnRowCallback: function( row, data ) {
                    if( data.codigo_estado == window._tp.getState['active'] ){
                        if ( data.dias >= 3 && data.dias < 5 && data.contador_discusiones_asesoria == 0 ) {
                            $(row).css( {"color":"#E5A61E"} );
                        }

                        if( data.dias >= 6 && data.contador_discusiones_asesoria == 0 ){
                            $(row).css( {"color":"#C9302C"} );
                        }
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
            this.mentorListSearchTable.ajax.reload();
        },

        clear: function(e) {
            e.preventDefault();

            this.$searchMentorType.val('');
            this.$searchMentor.val('');
            this.$filterMentor.val('');
            this.$searchState.val('');
            this.$searchEntrepreneur.val('');
            this.$filterEntrepreneur.val('');

            this.mentorListSearchTable.ajax.reload();
        },

        // Modal confirm activate mentoring
        onModalConfirm: function (e) {
            e.preventDefault();

            var _this = this,
                id = e.target.value
                action = 'activar',
                article_name = id;

            if( this.acceptConfirm instanceof Backbone.View ) {
                this.acceptConfirm.stopListening();
                this.acceptConfirm.undelegateEvents();
            }

            this.acceptConfirm = new app.ConfirmWindow({
                parameters: {
                    template: this.templateConfirm({
                        action : action,
                        article_name: article_name
                    }),
                    titleConfirm: "Confirmación",
                    onConfirm: function () {

                        this.mentoringModel = new app.MentoringModel({ codigo_asesoria: id });

                        this.mentoringModel.save(
                            'codigo_estado', 8,
                            {   
                                success: function (resp, state, jqXHR) {
                                _this.mentorListSearchTable.ajax.reload();
                
                                    //complete proccess                  
                                        if( state.success == true ){
                                            window.Misc.showAlertBox({
                                                text: state.message,
                                                type: 'success',
                                                closeTime: 5000
                                            });
                                
                                        }else {
                                        
                                            window.Misc.showAlertBox({
                                                text: state.errors,
                                                type: 'alert',
                                                closeTime: 5000
                                            });        
                                        }
                        
                                },
                                error: function (xhr, ajaxOptions, thrownError) {
                                    window.Misc.showAlertBox({
                                        text: 'Error, recarge navegador',
                                        type: 'alert',
                                        closeTime: 5000
                                    });
                                }
                            });
                    }
                }
            });

            this.acceptConfirm.render();

        }

    });

})(jQuery, this, this.document);
