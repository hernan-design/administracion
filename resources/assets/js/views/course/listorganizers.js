/**
* Class CourseThemeListView extends of Backbone View
* @author TuProyecto || Desarrollador : @ayn
* @link http://TuProyecto.com
*/

//Global App Backbone
app || (app = {});

(function ($, window, document, undefined) {

	app.ListOrganizer= Backbone.View.extend({
        el: '#list_organizers',
        template: _.template( ($('#organizers-list-tpl').html() || '') ),
        templateButtonsAction: _.template( ($('#buttons-action-organizer-tpl').html() || '') ),
        buttonsDeleteOrganizer: _.template( ($('#buttons-delete-organizer-tpl').html() || '') ),
		events: {
            'click .DeleteOrganizerDB': 'DeleteOrganizerDB',
            'click .AddOrganizer': 'AddOrganizer',
            'click .RemoveOrganizer': 'RemoveOrganizer'
		},
        initialize : function(opts) {
            // Collection attachments
            this.OrganizerModel = new app.OrganizerModel();
			// Events Listener
            this.render();
		},
        // Render View Element
        render: function() {
            var attributes = {};
            this.$el.html( this.template(attributes) );
            // Initialize attributes
            this.$organizerSearchTable = this.$('#organizer-search-table');
            // overwrite model id attribute
            this.referenceViews();
        },
        // reference to views
        referenceViews: function () {
            var _this = this;
            this.organizerSearchTable = this.$organizerSearchTable.DataTable({
               dom:"<'row'<'col-sm-5'i><'col-sm-7'p>>" +
                    "<'row'<'col-sm-12'tr>>" +
                    "<'row'<'col-sm-5'i><'col-sm-7'p>>",
                processing: true,
                serverSide: true,
                language: window.Misc.dataTableES(),
                ajax:
                {
                    url: window.Misc.urlFull( Route.route('patrocinador.index') )

                },
                columns: [
                    { data: 'codigo_patrocinador', name: 'codigo_patrocinador' },
                    { data: 'nombre_patrocinador', name: 'nombre_patrocinador' },
                    { data: 'descripcion_patrocinador', name: 'descripcion_patrocinador'},
                    { data: 'imagen_patrocinador', name: 'imagen_patrocinador' },

                ],
                order: [
                    [ 0, 'desc' ]
                ],
               columnDefs: [
                    {
                        targets: 3,
                        render: function ( data, type, full, row ) {
                            console.log(data);
                            return '<img src="' + data + '" alt="imagen del patrocinador" height="20px"> '
                        }
                    },
                   {
                        targets: 4,
                        width: '10%',
                       // orderable: false,
                        className: 'text-center',
                        render: function ( data, type, full, row ) {
                           return _this.templateButtonsAction( {full: full} )
                           //return '<a href="#" class="btn btn-success btn-sm ">Agregar</a> <a href="#" class="btn btn-danger btn-sm ">Quitar</a>';
                        }
                    },
                    {
                        targets: 5,
                        width: '10%',
                        orderable: false,
                        className: 'text-center',
                        render: function ( data, type, full, row ) {
                            return _this.buttonsDeleteOrganizer( {full: full} )
                          // return '<a href="' + window.Misc.urlFull( Route.route('patrocinador.destroy', {id: full.codigo_patrocinador})) + '" title="Ver proyecto">'+full.codigo_patrocinador+'</a>';

                        }
                    }
                ]
            });
        },

        DeleteOrganizerDB: function(e){

            e.preventDefault();
            e.stopPropagation();
            var _this = this;
            window.Misc.setSpinner( this.$el );
            var codigo_patrocinador = e.target.value;
            this.organizer = new app.OrganizerModel({ codigo_patrocinador: codigo_patrocinador });

            this.organizer.destroy({
                success: function (resp, state, jqXHR) {
                    _this.organizerSearchTable.ajax.reload();
                    window.Misc.removeSpinner({
                        wrap: _this.$el,
                        direct: false
                    });
                    //complete proccess
                    if( state.success == true ){
                        window.Misc.showAlertBox({
                            text: state.message,
                            type: 'success',
                            closeTime: 5000
                        });
                    }else {
                        window.Misc.showAlertBox({
                            text: 'No se puede eliminar organizador, puede que pertenezca a un Curso o a un Evento',
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

        },
        //ADD ORGANIZADOR AL CURSO
        AddOrganizer: function(e){

            e.preventDefault();
            e.stopPropagation();
            var _this = this;
            window.Misc.setSpinner( this.$el );

            var codigo_patrocinador = e.target.value;
            var codigo_curso = app.CodigoCurso.codigo_curso;

            this.organizer = new app.OrganizerCourseModel();
            this.organizer.save(
                {
                    codigo_patrocinador: codigo_patrocinador,
                    codigo_curso: codigo_curso
                },
                {
                    success: function (resp, state, jqXHR) {
                        window.Misc.removeSpinner({
                            wrap: _this.$el,
                            direct: false
                        });
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
                }
            );
        },
        //REMOVE ORGANIZADOR DEL CURSO
        RemoveOrganizer: function(e){

            e.preventDefault();
            e.stopPropagation();
            var _this = this;
            window.Misc.setSpinner( this.$el );

            var codigo_patrocinador = e.target.value;
            var codigo_curso = app.CodigoCurso.codigo_curso;

            this.organizer = new app.OrganizerCourseModel(
                {
                    codigo_patrocinador: codigo_patrocinador,
                    codigo_curso: codigo_curso
                }
            );
        },
        search: function(e) {
            e.preventDefault();
            this.organizerSearchTable.ajax.reload();
        },
        loadSpinner: function () {

            window.Misc.setSpinner( this.$el );
        },
	});
    new app.ListOrganizer()
})(jQuery, this, this.document);
