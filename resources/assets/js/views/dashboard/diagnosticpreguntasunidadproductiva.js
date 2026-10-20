/**
* Class DiagnosticMadurezListView extends of Backbone View
* @author TuProyecto || Desarrolladora : @dortegon
* @link http://TuProyecto.com
*/

//Global App Backbone
app || (app = {});

(function ($, window, document, undefined) {

    app.DiagnosticPreguntasUnidadesListView = Backbone.View.extend({
        el: '#diagnostics-content',
        templateButtonsAction: _.template( ($('#buttons-action-tpl-unidades').html() || '') ),
        events: {
            'submit #form-add-madurez-diagnostic' : 'onAddBenefitToEvent',
            'click .btn-create-preguntas-up': 'onSaveQuestions'
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
            this.$wrapperMadurez = this.$('.to_do');

            if(this.model.id){
                // Init Models
                this.unidades = new app.PreguntasUnidadProductivaModel();
            }

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
            this.$formAddMadurez = this.$('#form-add-preguntas-up');

            var attributes = {};
            // this.$el.html( this.template(attributes) );

            // Initialize attributes
            // this.$diagnosticListSearchTable  = this.$('#unidades-list-search-table');
            // this.$searchNombre          = this.$('#nombre_evento');
            // this.$searchState           = this.$('#codigo_estado');
            // this.$formFilters           = this.$('#form-event-filter');

            // overwrite model id attribute
            // this.referenceViews();
        },


         /**
        * Load spinner on the request
        */
         loadSpinner: function () {
            window.Misc.setSpinner( this.$gridSearchTable );
        },

        search: function(e) {
            e.preventDefault();
            this.diagnosticListSearchTable.ajax.reload();
        },

        /**
		* Render view comment by model
		* @param Object benefit Model instance
		*/
		addOne: function (benefit) {

            var view = new app.EventBenefitItemView({
				model: benefit
			});

			this.$wrapperMadurez.append( view.render().el );
		},

        /**
		* Render all view Course of the collection
		*/
		addAll: function () {
			this.$wrapperMadurez.html('');
			this.collection.forEach( this.addOne, this );
		},

        /**
        * add new development activity
        */
        onAddBenefitToEvent: function (e) {
            e.preventDefault();

            var _this = this,
                data = window.Misc.formToJson(this.$formAddMadurez);
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
            window.Misc.setSpinner( this.$wrapperMadurez );
        },

        /**
        * response of the server
        */
        responseServer: function ( model, resp, opts ) {

            //complete proccess
            window.Misc.removeSpinner({
                'wrap': this.$wrapperMadurez,
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
		},

         /**
         * Asignar Pregunta a nivel de madurez
         */
        onSaveQuestions: function(e){
            console.log("aqui");
            e.preventDefault();
            e.stopPropagation();

            const selects = document.querySelectorAll('.unidad-select');
            const allSelectedValues = {};

            selects.forEach(select => {
                const selectedValues = Array.from(select.selectedOptions).map(option => option.value);

                // Remover la palabra "unidad" del id
                const codigoUnidadProductiva = select.id.replace('unidad', '');

                allSelectedValues[codigoUnidadProductiva] = selectedValues;
            });

            console.log('information ', allSelectedValues);

            this.model.save(allSelectedValues, {
                wait: true,
                type: 'POST', // Forzar el método POST
                success: function (model, resp) {
                    //complete proccess
                    if( resp.success !== undefined ){
                        if( resp.success ){
                            window.Misc.showAlertBox({
                                text: resp.message,
                                type: 'success',
                                closeTime: 5000
                            });

                            setTimeout(function() {
                                var diagnostic = $('#codigo_diagnostico').val();

                                if (diagnostic != undefined){
                                    var baseUrl = window.location.protocol + '//' + window.location.host + '/';

                                    window.location.href = baseUrl + 'diagnosticos/' + diagnostic + '/edit#tab_madurez';
                                } else{
                                    var baseUrl = window.location.protocol + '//' + window.location.host + '/';

                                            window.location.href = baseUrl + 'diagnosticos/' + diagnostic + '/edit';
                                }
                            }, 1000);
                        }else{
                            window.Misc.showAlertBox({
                                text: resp.error,
                                type: 'alert',
                                closeTime: 5000
                            });
                        }
                    }
                }
            });
        }
   });

})(window.jQuery, window, document);
