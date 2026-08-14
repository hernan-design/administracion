/**
* Class DiagnosticMadurezListView extends of Backbone View
* @author TuProyecto || Desarrolladora : @dortegon
* @link http://TuProyecto.com
*/

//Global App Backbone
app || (app = {});

(function ($, window, document, undefined) {

    app.DiagnosticUnidadesListView = Backbone.View.extend({
        el: '#diagnostics-content',
        templateButtonsAction: _.template( ($('#buttons-action-tpl-unidades').html() || '') ),
        events: {
            'submit #form-add-madurez-diagnostic' : 'onAddBenefitToEvent',
            'click .btn-edit-unidad': 'onModalMadurezCreate',
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
                this.unidades = new app.UnidadesProductivasModel();
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
            this.$formAddMadurez = this.$('#form-add-unidades-diagnostic');

            var attributes = {};
            // this.$el.html( this.template(attributes) );

            // Initialize attributes
            this.$diagnosticListSearchTable  = this.$('#unidades-list-search-table');
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
                    url: window.Misc.urlFull( Route.route('diagnosticos.unidades.edit', { 'nivel': codigo_diagnostico }) ),
                },
                columns: [
                    { data: 'nombre_unidad_productiva',    name: 'nombre_unidad_productiva'  },
                    { data: 'descripcion_unidad_productiva',    name: 'descripcion_unidad_productiva'  },
                    { data: 'porcentaje',    name: 'porcentaje'  },
                    { data: 'estado',    name: 'estado'  }
                ],
                order: [
                    [ 0, 'asc' ]
                ],
                columnDefs: [
                    {
                        targets: 3,
                        render: function (data, type, full, row) {
                            return data ? 'Activo' : 'Inactivo';
                        }
                    },
                    {
                        targets: 4,
                        orderable: false,
                        className: 'text-center',
                        render: function ( data, type, full, row ) {
                            return _this.templateButtonsAction( {full: full} );
                        }
                    }
                ],
            });

            // Agregar evento draw.dt para acceder al rango mínimo y máximo después de que se ha dibujado la tabla
            this.$diagnosticListSearchTable.on('draw.dt', function () {
                if (_this.diagnosticListSearchTable.rows().count() > 0) {
                    var total = 0;
                    _this.$diagnosticListSearchTable.find('tbody tr').each(function () {
                        var rowData = _this.diagnosticListSearchTable.row(this).data();
                        total = parseFloat(rowData.porcentaje) + total;

                    });

                    $('#progress-bar-unidades').css('width', total+'%').attr('aria-valuenow', total).text(total+'%');
                    if(total >= 100){
                        $('.btn-create-unidades').css('display', 'none');
                    }
                } else {
                    // Establecer el ancho de la barra de progreso en 0 cuando no hay registros
                    $('#progress-bar-unidades').css('width', '0%').attr('aria-valuenow', 0).text('0%');
                    $('#progress-bar-unidades').css('color', '#000')
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
            var currentUnidad = $(e.currentTarget).data('unidad');

            this.modalCreateUnidades = new app.FormSampleWindow({
                model: this.unidades,
                parameters: {
                    tagInfo: "#tab_unidades",
                    edit: true,
                    template: _.template( ($('#create-unidad-diagnostic-tpl').html() || '') ),
                    title: 'Editar Unidad Productiva',
                    currentMadurez: currentUnidad
                }
            });

            this.modalCreateUnidades.render();
		},
   });

})(jQuery, this, this.document);
