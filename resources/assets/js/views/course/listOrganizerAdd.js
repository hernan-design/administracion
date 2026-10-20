/**
* Class CourseThemeListView extends of Backbone View
* @author TuProyecto || Desarrollador : @ayn
* @link http://TuProyecto.com
*/

//Global App Backbone
app || (app = {});

(function ($, window, document, undefined) {

	app.ListOrganizerAdd = Backbone.View.extend({
        el: '#list_organizers_add',
        template: _.template( ($('#list-organizers-add-tpl').html() || '') ),
        templateButtonsRemoveOrganizer: _.template( ($('#buttons-RemoveOrganizer-tpl').html() || '') ),

        //templateButtonsAction: _.template( ($('#buttons-action-organizer-tpl').html() || '') ),
       // buttonsDeleteOrganizer: _.template( ($('#buttons-delete-organizer-tpl').html() || '') ),
		events: {

            'click .RemoveOrganizer': 'RemoveOrganizer',
            'click .ShowOrganizersAdd': 'referenceViews',

		},

        initialize : function(opts) {

            // Collection attachments
            this.OrganizerModel = new app.OrganizerModel();

            // Events Listener
            this.listenTo( this.OrganizerModel, 'request', this.loadSpinner );
            this.listenTo( this.OrganizerModel, 'sync', this.responseServer );

            this.render();

		},

        /*
        * Render View Element
        */
       render: function() {

           var attributes = {};
           this.$el.html( this.template(attributes) );

           // Initialize attributes

           this.$organizerSearchTable = this.$('#organizerAdd-search-table');


           $(".tableListOrganizersAdds").hide();

            // this.referenceViews();
        },

        /**
         * reference to views
         */
         referenceViews: function () {
            var _this = this;

          var codigo_curso = app.CodigoCurso.codigo_curso


            $(".tableListOrganizersAdds").slideDown("slow");
     //       $(".ShowOrganizersAdd").hide("slow");

            if ( !this.organizerSearchTable ) {
                var cursoId = $('#codigo_curso').val()
                this.organizerSearchTable = this.$organizerSearchTable.DataTable({
                    dom:"<'row'<'col-sm-5'i><'col-sm-7'p>>" +
                         "<'row'<'col-sm-12'tr>>" +
                         "<'row'<'col-sm-5'i><'col-sm-7'p>>",
                     processing: true,
                     retrieve: true,
                     paging: false,
                     serverSide: true,
                     language: window.Misc.dataTableES(),
                     ajax:
                     {
                         url: window.Misc.urlFull( Route.route('patrocinadorCurso.index' ) ),
                        data: function( data ) {

                             data.codigo_curso   =  cursoId;
                         },
                     },
                     columns: [
                         { data: 'codigo_patrocinador', name: 'id' },
                         { data: 'nombre_patrocinador', name: 'nombre_patrocinador' },
                  //       { data: 'imagen_patrocinador', name: 'imagen_patrocinador' },

                     ],
                     order: [
                         [ 0, 'desc' ]
                     ],
                    columnDefs: [
                         {
                             targets: 2,
                             className: 'text-center',
                             render: function ( data, type, full, row ) {
                                 return _this.templateButtonsRemoveOrganizer( {full: full} )
                             }
                         },
                   /*     {
                             targets: 3,
                             width: '10%',
                            // orderable: false,
                             className: 'text-center',
                             render: function ( data, type, full, row ) {
                                return _this.templateButtonsAction( {full: full} )
                             }
                         },
                         {
                             targets: 4,
                             width: '10%',
                             orderable: false,
                             className: 'text-center',
                             render: function ( data, type, full, row ) {
                                 return _this.buttonsDeleteOrganizer( {full: full} )
                             }
                         }*/
                     ]
                 });
            }
            else {
                this.organizerSearchTable.ajax.reload();
            }




        },


        RemoveOrganizer: function(e){ //REMOVE ORGANIZADOR DEL CURSO
            e.preventDefault();
            e.stopPropagation();

            var _this = this;

            window.Misc.setSpinner( this.$el );

            var id = e.target.value;
            var codigo_curso = app.CodigoCurso.codigo_curso;

          this.organizer = new app.OrganizerCourseModel({ id: id });

          this.organizer.destroy(
                {

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

        },


	});

    new app.ListOrganizerAdd()

})(window.jQuery, window, document);
