/**
* Class EventBenefitsListView extends of Backbone View
* @author TuProyecto || Desarrolladora : @dortegon
* @link http://TuProyecto.com
*/

//Global App Backbone
app || (app = {});

(function ($, window, document, undefined) {

    app.EventBenefitsListView = Backbone.View.extend({
        events: {
            'submit #form-add-benefits-event' : 'onAddBenefitToEvent',
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
            this.$wrapperBenefits = this.$('.to_do');

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
            this.$formAddBenefitss = this.$('#form-add-benefits-event');
        },

        /**
		* Render view comment by model
		* @param Object benefit Model instance
		*/
		addOne: function (benefit) {

            var view = new app.EventBenefitItemView({
				model: benefit
			});

			this.$wrapperBenefits.append( view.render().el );
		},

        /**
		* Render all view Course of the collection
		*/
		addAll: function () {
			this.$wrapperBenefits.html('');
			this.collection.forEach( this.addOne, this );
		},

        /**
        * add new development activity
        */
        onAddBenefitToEvent: function (e) {
            e.preventDefault();

            var _this = this,
                data = window.Misc.formToJson(this.$formAddBenefitss);
                data['codigo_evento'] = this.model.get('codigo_evento');

            if(data.codigo_beneficio_evento){
                this.collection.create(data, {wait: true, success: function(model, resp, opts){

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
                window.Misc.showAlertBox( {text: 'Debe seleccionar un beneficio', type: 'alert'} );
            }
        },

        /**
        * Load spinner on the request
        */
        loadSpinner: function () {
            window.Misc.setSpinner( this.$wrapperBenefits );
        },

        /**
        * response of the server
        */
        responseServer: function ( model, resp, opts ) {

            //complete proccess
            window.Misc.removeSpinner({
                'wrap': this.$wrapperBenefits,
                'direct': false
            });
        },

        /**
		* reset form fields
		* @param parentForm Element
		*/
		resetFields: function (parentForm) {
			// reset inputs
            this.$('#codigo_beneficio_evento').select2('val', '');
		}
   });

})(window.jQuery, window, document);
