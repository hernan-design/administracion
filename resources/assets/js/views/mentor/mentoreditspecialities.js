/**
* Class EventOrganizerView extends of Backbone View
* @author TuProyecto || Desarrolladora : @dortegon
* @link http://TuProyecto.com
*/

//Global App Backbone
app || (app = {});

(function ($, window, document, undefined) {

    app.MentorEditSpecialities = Backbone.View.extend({
        template: _.template( ($('#item-specialities-tpl').html() || '') ),
        events: {
            'submit #form-add-organizers-event' : 'onAddOrganizerToEvent',
        },
        parameters: {
            dataFilter: {}
        },

        /**
        * Constructor Method
        */
        initialize : function(opts) {
            // extends parameters
            if( opts.parameters !== undefined && _.isObject(opts.parameters) )
                this.parameters = $.extend({}, this.parameters, opts.parameters);

            this.configAlert = {
                'wrapper': '#main-wrap',
                'closeTime': 20000,
                'speedOpen': 500,
                'speedClose': 500
            };

            // Reference to elements
            this.$wrapperEventOrganizers = this.$('.to_do');

            // Events Listener
			this.listenTo( this.collection, 'add', this.addOne );
			this.listenTo( this.collection, 'reset', this.addAll );
			this.listenTo( this.collection, 'sync', this.responseServer);
            this.listenTo( this.collection, 'request', this.loadSpinner );
        },

        /*
        * Render View Element
        */
        render: function() {
            this.$formAddOrganizers = this.$('#form-add-organizers-event');
        },

        /**
		* Render view comment by model
		* @param Object eventOrganizer Model instance
		*/
		addOne: function (eventOrganizer) {

            var view = new app.EventOrganizerListView({
				model: eventOrganizer
			});

			this.$wrapperEventOrganizers.append( view.render().el );
		},

        /**
		* Render all view Course of the collection
		*/
		addAll: function () {
			this.$wrapperEventOrganizers.html('');
			this.collection.forEach( this.addOne, this );
		},

        /**
        * add new development activity
        */
        onAddOrganizerToEvent: function (e) {
            e.preventDefault();

            var _this = this,
                data = window.Misc.formToJson(this.$formAddOrganizers);
                data['codigo_evento'] = this.model.get('codigo_evento');

            if(data.codigo_patrocinador){
                this.collection.create(data, {wait: true, success: function(model, resp, opts){

                    window.Misc.removeSpinner({
                        'wrap': this.$el,
                        'direct': false
                    });

                    // response success or error
                    var type = resp.success ? 'success' : 'alert',
                        text = resp.success ?
                            (resp.message !== undefined ? resp.message : '...')
                            : resp.errors;

                    if( _.isObject( resp.errors ) ){

                        var listError = '<ul>';

                        $.each(resp.errors, function(field, item) {
                             listError += '<li>'+ item[0] +'</li>';
                        });
                        listError += '</ul>';

                        text = listError;
                    }

                    var config = {
                        'text': text,
                        'type': type
                    }
                    _.extend(_this.configAlert, config);

                    //complete proccess
                    if( resp.success !== undefined ){
                        if( resp.success ){
                            window.Misc.showAlertBox( _this.configAlert );
                            this.resetFields();
                        }else{
                            this.collection.remove(model);
                            window.Misc.showAlertBox( _this.configAlert );
                        }
                    }
                }.bind(this)});
            }else{
                window.Misc.showAlertBox( {text: 'Debe seleccionar un organizador', type: 'alert'} );
            }
        },

        /**
        * Load spinner on the request
        */
        loadSpinner: function () {
            window.Misc.setSpinner( this.$el );
        },

        /**
        * response of the server
        */
        responseServer: function ( model, resp, opts ) {

            //complete proccess
            window.Misc.removeSpinner({
                'wrap': this.$el,
                'direct': false
            });
        },

        /**
		* reset form fields
		* @param parentForm Element
		*/
		resetFields: function (parentForm) {
			// reset inputs
            this.$('#codigo_patrocinador').select2('val', '');
		}
   });

})(jQuery, this, this.document);
