/**
* Class CourseThemeListView extends of Backbone View
* @author TuProyecto || Desarrollador : @ayn
* @link http://TuProyecto.com
*/

//Global App Backbone
app || (app = {});

(function ($, window, document, undefined) {

	app.CourseCreateOrganizer= Backbone.View.extend({
        el: '#crear_organizador',
		events: {
            'submit #formAddOrganizers': 'AddOrganizer',
		},

        initialize : function(opts) {

            this.OrganizerModel = new app.OrganizerModel();

			// Events Listener
			//this.listenTo( this.model, 'change', this.render );
			//this.listenTo( this.OrganizerModel, 'add', this.addCourseContent );
			//this.listenTo( this.OrganizerModel, 'reset', this.addCourseContents );
            this.listenTo( this.OrganizerModel, 'request', this.loadSpinner );
            this.listenTo( this.OrganizerModel, 'sync', this.responseServer );


		},

            /**
            * response of the server
            */
        responseServer: function ( model, resp, opts ) {
            console.log('responseServer');
            window.Misc.removeSpinner({
                wrap: _this.$el,
                direct: false
            });

            //complete proccess
            if( resp.success !== undefined ) {
                if( resp.success ){
                    window.Misc.showAlertBox({
                        text: resp.message,
                        type: 'success',
                        closeTime: 5000
                    });


                }else {
                    window.Misc.showAlertBox({
                        text: resp.errors,
                        type: 'alert',
                        closeTime: 5000
                    });

                }
            }


        },

        loadSpinner: function () {
            console.log('loadSpinner');
            window.Misc.setSpinner( this.$el );
        },

        AddOrganizer: function (e) {
            e.preventDefault()
          //  console.log("hola store");
            var _this = this;
            var direction = $('#direction').val();
            var _token2 = $("input[name=_token]").val();
            var orga = $(e.target).nestable('serialize');

              window.Misc.setSpinner( this.$el );

              // Obtén el objeto de archivo real
    var fileInput = $('#imagen_patrocinador')[0];
    var imagenPatrocinador = fileInput.files[0];

             // this.organizer = new app.OrganizerModel({nombre_patrocinador: $('#nombre_patrocinador').val(), imagen_patrocinador:  $('#imagen_patrocinador').val()});
              this.organizer = new app.OrganizerModel();
                // Crea un objeto FormData y agrega los datos del formulario
                var formData = new FormData();
                formData.append('nombre_patrocinador', $('#nombre_patrocinador').val());
                formData.append('descripcion_patrocinador', $('#descripcion_patrocinador').val());
                formData.append('imagen_patrocinador', imagenPatrocinador);
                formData.append('codigo_curso', $('#codigo_curso').val());
              this.organizer.save(null, {
                data: formData,
                processData: false,
        contentType: false,
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

                                e.target.reset()

                            }else {

                                window.Misc.showAlertBox({
                                    text: state.message,
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


                if ( !this.organizer.isValid() ) {

                    window.Misc.showAlertBox({
                        text: this.organizer.validationError,
                        type: 'alert',
                        closeTime: 5000

                    });

                    window.Misc.removeSpinner({
                        wrap: _this.$el,
                        direct: false
                    });

                }
        }

	});

    new app.CourseCreateOrganizer()

})(window.jQuery, window, document);
