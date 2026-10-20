/**
* Class DiagnosticCreateEditView extends of Backbone View
* @author TuProyecto || Desarrollador : @dortegon
* @link http://TuProyecto.com
*/

//Global App Backbone
app || (app = {});

(function ($, window, document, undefined) {

    app.DiagnosticCreateEditView = Backbone.View.extend({
        el: '#diagnostics-content',
        events: {
            'submit #form-diagnostic': 'addDiagnostic',
            'click .btn-create-madurez': 'onModalMadurezCreate',
            'click .btn-create-unidades': 'onModalUnidadesCreate',
            'click .btn-create-asignacion': 'onAsignationModules'
        },
        parameters: {
            haveBenefits: 0
        },

        /**
        * Constructor Method
        */
        initialize: function(opts){
            // Obtener el fragmento (hash) de la URL
    var hash = window.location.hash;

    // Si hay un hash, activar la pestaña correspondiente
    if (hash) {
        // Remover la clase 'active' de la pestaña y el contenido actual
        $('#myTab li').removeClass('active');
        $('.tab-pane').removeClass('in active');

        // Agregar la clase 'active' a la pestaña correspondiente
        $('a[href="' + hash + '"]').closest('li').addClass('active');
        $(hash).addClass('in active');
    }

    // Cambiar el hash en la URL al cambiar de pestaña
    $('a[data-toggle="tab"]').on('shown.bs.tab', function (e) {
        window.location.hash = e.target.hash;
    });
            _.bindAll(this, 'beforeRender', 'render', 'afterRender');

            // extends parameters
            if( opts.parameters !== undefined && _.isObject(opts.parameters) )
                this.parameters = $.extend({}, this.parameters, opts.parameters);

                //Init Attribute
            this.configAlert = {
                'wrapper': '#main-wrap',
                'closeTime': 6000,
                'speedOpen': 500,
                'speedClose': 500
            };

            var _this = this;

            this.render = _.wrap(this.render, function(render) {

                _this.trigger('beforeRender');

                render();

                _this.trigger('afterRender');

                return _this;
            });

            if(this.model.id){
                // Init Models
                this.madurez = new app.NivelMadurezModel();
                this.unidades = new app.UnidadesProductivasModel();
                this.organizer = new app.OrganizerModel();

                // Init Collections
                this.nivelesMadurezList = new app.NivelesMadurezList();
                this.unidadesProductivasList = new app.UnidadesProductivasList();
                this.eventSessionsList = new app.EventSessionsList();
                this.eventSessionsList.fetch( {data: {event: this.model.get('codigo_evento')}} );
            }

            // Events Listener
            this.listenTo( this.model, 'request', this.loadSpinner );
            this.listenTo( this.model, 'sync', this.responseServer );

            // Events listener custom
            this.on( 'beforeRender', this.beforeRender );
            this.on( 'afterRender', this.afterRender );
        },

        /*
        * Render View Element
        */
        render: function() {
            var attributes = this.model.toJSON();
            // Reference to elements
            this.$formCreate = this.$('#form-diagnostic');
            this.$wrapperDiagnosticMadurez = this.$('#wrapper-render-madurez');
            this.$wrapperDiagnosticUnidades = this.$('#wrapper-render-unidades');
            this.$wrapperDiagnosticPreguntasUnidades = this.$('#wrapper-render-preguntas-up');
            // this.$modalComponent = $('#modal-base-create');
            // this.$wrapperOrganizers = this.$('#wrapper-render-organizers');
            // this.$wrapperImgToCrop = this.$('#wrap-event-uploader-tocrop');
            // this.$wrapperEventSessions = this.$('#wrapper-render-sessions');
            // this.$wrapperEventBenefits = this.$('#wrapper-render-madurez');
            // this.$searchOrganizerId = this.$('#id_organizador_filter_data');
            // this.$searchOrganizerName = this.$('#organizador_filter_data');
            // this.$gridSearchTableOrganizer = this.$('#organizer-search-table');
            // this.$btnCertificate = this.$('.btn-certificate');

            // Initialize attributes
            this.$diagnosticListSearchTable  = this.$('#madurez-list-search-table');

            this.referenceViews();

            // if (attributes.codigo_diagnostico !== undefined) {
            //     this.renderTableOrganizer();
            //     this.renderTableUserResponsable();
            // }
            this.ready();
        },

        /**
        * fires libraries js
        */
        ready: function () {
            // reload plugins
            // if( typeof window.initComponent.select2Trigger == 'function' ){
            //     window.initComponent.select2Trigger();
            // }

            // if( typeof(window.initComponent.autosizeTrigger) == 'function')
            //     window.initComponent.autosizeTrigger();

            // if( typeof window.initComponent.icheckTrigger == 'function' ){
            //     window.initComponent.icheckTrigger();
            // }

            // if(typeof window.initComponent.multiselectTrigger == 'function' ){
            //     window.initComponent.multiselectTrigger();
            // }

            // input mask
            this.$(":input").inputmask();
        },

        /**
        * Fires before of run render function
        */
        beforeRender: function () {
            //
        },

        /**
        * Fires before of run render function
        */
        afterRender: function () {

            // // render uploader and cropper of image
            // this.setImgUploaderCrop();
        },

        /**
        * reference to views
        */
        referenceViews: function () {

            if(this.model.id){
                // Render view Madurez Diagnostic
                this.diagnosticMadurezListView = new app.DiagnosticMadurezListView({
                    el: this.$wrapperDiagnosticMadurez,
                    model: this.model,
                    collection: this.nivelesMadurezList
                });

                this.diagnosticMadurezListView.render();
                this.nivelesMadurezList.fetch( );

                // Render view Madurez Diagnostic
                this.diagnosticUnidadesListView = new app.DiagnosticUnidadesListView({
                    el: this.$wrapperDiagnosticUnidades,
                    model: this.model,
                    collection: this.unidadesProductivasList
                });

                this.diagnosticUnidadesListView.render();
                this.unidadesProductivasList.fetch( );

                // Render view Madurez Diagnostic
                this.diagnosticPreguntasUnidadesListView = new app.DiagnosticPreguntasUnidadesListView({
                    el: this.$wrapperDiagnosticPreguntasUnidades,
                    model: new app.PreguntasUnidadProductivaModel(),
                    collection: this.PreguntasUnidadProductivaList
                });

                this.diagnosticPreguntasUnidadesListView.render();
                this.PreguntasUnidadProductivaList.fetch( );
            }
        },

        /**
        * Load spinner on the request
        */
        loadSpinner: function () {
            window.Misc.setSpinner( this.$('#section-content') );
        },

         /**
        * response of the server
        */
         responseServer: function ( model, resp, opts ) {

            window.Misc.removeSpinner({
                wrap: this.$('#section-content'),
                direct: false
            });

            // response success or error
            var type = resp.success ? 'success' : 'alert',
                text = resp.success ?
                        (resp.message !== undefined ? resp.message : '...')
                            : resp.errors;

            if( _.isObject( resp.errors ) ) {

                var listError = '<ul>';

                $.each(resp.errors, function(field, item) {
                     listError += '<li>'+ item[0] +'</li>';
                });
                listError += '</ul>';

                text = listError;
            }

            var conf = {
                'text': text,
                'type': type
            };
            _.extend(this.configAlert, conf);

            //complete proccess
            if( resp.success !== undefined ) {
                if( resp.success ){
                    window.Misc.showAlertBox(this.configAlert);

                    if(resp.redirect){
                        window.Misc.redirect( app.AppRouter.navigate( Route.route('diagnosticos.edit',{diagnostico: this.model.get('codigo_diagnostico')}) ) );
                    }
                }else {
                    window.Misc.showAlertBox(this.configAlert);
                    return false;
                }
            }

        },

        /*
         * Add Diagnostic
        */
        addDiagnostic: function (e) {
            e.preventDefault();
            var data = window.Misc.formToJson( e.target );
            this.model.save( data, { wait: true });
        },

        /**
         *
         * Modal create Madurez
         */
        onModalMadurezCreate: function(e) {
            e.preventDefault();
            e.stopPropagation();

            this.modalCreateMadurez = new app.FormSampleWindow({
                model: this.madurez,
                parameters: {
                    tagInfo: "#tab_madurez",
                    template: _.template( ($('#create-madurez-diagnostic-tpl').html() || '') ),
                    title: 'Crear Nivel de Madurez'
                }
            });

            this.modalCreateMadurez.render();
            var _this = this;
             // Agregar evento draw.dt para acceder al rango mínimo y máximo después de que se ha dibujado la tabla
             var minimo, maximo;

             $('#madurez-list-search-table_wrapper tr').each(function () {
                 var rowData = $(this).find('td');
                 var rangoMinimo = rowData[2]?.innerText;
                 var rangoMaximo = parseFloat(rowData[3]?.innerText);

                minimo = rangoMaximo == undefined || isNaN(rangoMaximo) ? 0 : rangoMaximo;
                maximo = 100;

                 if (maximo === undefined || rangoMaximo > maximo) {
                     maximo = rangoMaximo;
                 }
             });

             $('#rango_minimo').val(minimo == 0 ? minimo : minimo+1).attr('min',minimo == 0 ? minimo : minimo+1);
             $('#rango_maximo').val(minimo == 0 ? minimo+1 : minimo+2).attr('min',minimo == 0 ? minimo+1 : minimo+2);


             // Escuchar el evento onChange del campo rango mínimo después de renderizar el modal
            this.modalCreateMadurez.$('#rango_minimo').on('change', function(e) {
                var rangoMinimo = $(e.target).val();

                $('#rango_maximo').attr('min',parseInt(rangoMinimo)+1);
            });
		},

        /**
         *
         * Modal create Unidades
         */
        onModalUnidadesCreate: function(e) {
            e.preventDefault();
            e.stopPropagation();
            this.modalCreateUnidades = new app.FormSampleWindow({
                model: this.unidades,
                parameters: {
                    tagInfo: "#tab_unidades",
                    template: _.template( ($('#create-unidad-diagnostic-tpl').html() || '') ),
                    title: 'Crear Unidad Productiva'
                }
            });

            this.modalCreateUnidades.render();

            // Agregar evento draw.dt para acceder al rango mínimo y máximo después de que se ha dibujado la tabla
            var total;

            $('#unidades-list-search-table_wrapper tr').each(function () {
                var rowData = $(this).find('td');
                var porcentaje = rowData[2]?.innerText;
               total = porcentaje == undefined ? 0 : parseFloat(porcentaje) + total;

            });

            $('#porcentaje').attr('min',1).attr('max', 100-total);
		},

        /**
         * Asignar Módulos al diagnostico
         */
        onAsignationModules: function(e){
            e.preventDefault();
            e.stopPropagation();

             // Obtener los valores seleccionados
            var selectedModules = [];
            $('input[name="modules[]"]:checked').each(function() {
                selectedModules.push($(this).val());
            });

            this.model.save( selectedModules, { wait: true });
        },
    });
})(window.jQuery, window, document);
