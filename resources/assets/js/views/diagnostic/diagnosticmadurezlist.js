/**
* Class DiagnosticMadurezListView extends of Backbone View
* @author TuProyecto || Desarrolladora : @dortegon
* @link http://TuProyecto.com
*/

//Global App Backbone
app || (app = {});

(function ($, window, document, undefined) {

    app.DiagnosticMadurezListView = Backbone.View.extend({
        el: '#diagnostics-content',
        templateButtonsAction: _.template( ($('#buttons-action-tpl').html() || '') ),
        events: {
            'submit #form-add-madurez-diagnostic' : 'onAddBenefitToEvent',
            'click .btn-edit-madurez': 'onModalMadurezCreate',
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
                this.madurez = new app.NivelMadurezModel();
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
            this.$formAddMadurez = this.$('#form-add-madurez-diagnostic');

            var attributes = {};
            // this.$el.html( this.template(attributes) );

            // Initialize attributes
            this.$diagnosticListSearchTable  = this.$('#madurez-list-search-table');
            // this.$searchNombre          = this.$('#nombre_evento');
            // this.$searchState           = this.$('#codigo_estado');
            // this.$formFilters           = this.$('#form-event-filter');

            // overwrite model id attribute
            this.referenceViews();
        },

        /**
         * reference to views
         */
        referenceViews: function () {
            var _this = this;
            var codigo_diagnostico = $('#codigo_diagnostico').val();
            this.diagnosticListSearchTable = this.$diagnosticListSearchTable.DataTable({
                dom:"<'row'<'col-sm-5'i><'col-sm-7'p>>" +
                "<'row'<'col-sm-12'tr>>" +
                "<'row'<'col-sm-5'i><'col-sm-7'p>>",
                processing: true,
                serverSide: true,
                language: window.Misc.dataTableES(),
                ajax: {
                    url: window.Misc.urlFull( Route.route('diagnosticos.madurez.edit', { 'nivel': codigo_diagnostico }) ),
                },
                columns: [
                    { data: 'nombre_nivel_madurez',    name: 'nombre_nivel_madurez' },
                    { data: 'descripcion_nivel_madurez',    name: 'descripcion_nivel_madurez' },
                    { data: 'rango_minimo',    name: 'rango_minimo' },
                    { data: 'rango_maximo',    name: 'rango_maximo' },
                    { data: 'estado',    name: 'estado'  }
                ],
                order: [
                    [ 3, 'asc' ]
                ],
                columnDefs: [
                    {
                        targets: 4,
                        render: function (data, type, full, row) {
                            return data ? 'Activo' : 'Inactivo';
                        }
                    },
                    {
                        targets: 5,
                        width: '10%',
                        orderable: false,
                        className: 'text-center',
                        render: function ( data, type, full, row ) {
                            return _this.templateButtonsAction({ full: full });
                        }
                    }
                ],
            });

            // Agregar evento draw.dt para acceder al rango mínimo y máximo después de que se ha dibujado la tabla
            this.$diagnosticListSearchTable.on('draw.dt', function () {
                if (_this.diagnosticListSearchTable.rows().count() > 0) {
                    var maximo = 0;
                    _this.$diagnosticListSearchTable.find('tbody tr').each(function () {
                        var rowData = _this.diagnosticListSearchTable.row(this).data();
                        var rangoMinimo = rowData.rango_minimo;
                        var rangoMaximo = parseInt(rowData.rango_maximo);

                        if (rangoMaximo > maximo) {
                            maximo = rangoMaximo;
                        }
                    });

                    $('#progress-bar-madurez').css('width', maximo+'%').attr('aria-valuenow', maximo).text(maximo+'%');

                    if(maximo >= 100){
                        $('.btn-create-madurez').css('display', 'none');
                    }
                } else {
                    // Establecer el ancho de la barra de progreso en 0 cuando no hay registros
                    $('#progress-bar-madurez').css('width', '0%').attr('aria-valuenow', 0).text('0%');
                    $('#progress-bar-madurez').css('color', '#000')
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
         *
         * Modal create Madurez
         */
         onModalMadurezCreate: function(e) {
            e.preventDefault();
            e.stopPropagation();
            // Obtener el código del nivel de madurez del elemento clickeado
            var currentMadurez = $(e.currentTarget).data('madurez');

            this.modalCreateMadurez = new app.FormSampleWindow({
                model: this.madurez,
                parameters: {
                    tagInfo: "#tab_madurez",
                    edit: true,
                    template: _.template( ($('#create-madurez-diagnostic-tpl').html() || '') ),
                    title: 'Editar Nivel de Madurez',
                    currentMadurez: currentMadurez
                }
            });

            this.modalCreateMadurez.render();

		},
   });

})(window.jQuery, window, document);
