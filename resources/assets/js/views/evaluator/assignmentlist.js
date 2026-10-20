/**
* Class AssignmentListView extends of Backbone View
* @author TuProyecto || Desarrolladora : @xindykatalina
* @link http://TuProyecto.com
*/

//Global App Backbone
app || (app = {});

(function ($, window, document, undefined) {

    app.AssignmentListView = Backbone.View.extend({

        el: '#evaluations-content',
        template: _.template( ($('#list-assignment-tpl').html() || '') ),
        events: {
            'click .btn-search': 'search',
            'click .btn-clear': 'clear',
            'change .select-program': 'onProgram'
        },
        parameters: {
            dataFilter: {}
        },

        /**
        * Constructor Method
        */
        initialize : function(opts) {
            _.bindAll(this, 'beforeRender', 'render', 'afterRender');

            // extends parameters
            if( opts.parameters !== undefined && _.isObject(opts.parameters) )
                this.parameters = $.extend({}, this.parameters, opts.parameters);

            //Init Attribute

            var _this = this;
            this.render = _.wrap(this.render, function(render) {

                _this.trigger('beforeRender');

                render();

                _this.trigger('afterRender');

                return _this;
            });

            // Events Listener

            // Events Listener custome
            this.on( 'beforeRender', this.beforeRender );
            this.on( 'afterRender', this.afterRender );

        },

        /*
        * Render View Element
        */
        render: function() {

            var attributes = {};
            this.$el.html( this.template(attributes) );

            // Initialize attributes
            this.$assignmentsSearchTable = this.$('#evaluation-search-table');
            this.$searchordenpEntrepreneurship = this.$('#emprendimiento');
            this.$searchordenpProgram = this.$('#program');
            this.$searchordenpPhase = this.$('#phase');
            this.$searchordenNumber = this.$('#number_register');
            this.$searchMail = this.$('#email_evaluator');
            this.$searchDocument = this.$('#documento_usuario');
            this.$searchAssignment = this.$('#slcAsign');

            this.referenceViews();
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

            this.ready();
        },

        /**
        * fires libraries js
        */
        ready: function () {

            var _this = this;

            // reload plugins
            if(typeof window.initComponent.select2Trigger == 'function' )
                window.initComponent.select2Trigger();

            if( typeof(window.initComponent.datepickerTrigger) == 'function')
                window.initComponent.datepickerTrigger();
        },

        /**
        * reference to views
        */
        referenceViews: function () {

            var _this = this;
            this.assignmentsSearchTable = this.$assignmentsSearchTable.DataTable({
                dom:"<'row'<'col-sm-5'i><'col-sm-7'p>>" +
                    "<'row'<'col-sm-12'tr>>" +
                    "<'row'<'col-sm-5'i><'col-sm-7'p>>",
                processing: true,
                serverSide: true,
                language: window.Misc.dataTableES(),
                ajax: {
                    url: window.Misc.urlFull( Route.route('evaluacion.asignacion.index') ),
                    data: function( data ) {
                        data.number_register = _this.$searchordenNumber.val();
                        data.emprendimiento = _this.$searchordenpEntrepreneurship.val();
                        data.program = _this.$searchordenpProgram.val();
                        data.phase = _this.$searchordenpPhase.val();
                        data.mail = _this.$searchMail.val();
                        data.identification_user = _this.$searchDocument.val();
                        data.assignment = _this.$searchAssignment.val();
                    }
                },
                columns: [
                    { data: 'numero_registro_convocatoria', name: 'numero_registro_convocatoria' },
                    { data: 'nombre_usuario', name: 'nombre_usuario' },
                    { data: 'nombre_convocatoria', name: 'nombre_convocatoria' },
                    { data: 'nombre_fase', name: 'nombre_fase' },
                    { data: 'codigo_registro', name: 'codigo_registro' },

                ],
                order: [
                    [ 0, 'desc' ]
                ],
                columnDefs: [
                    {
                        targets: [4],
                        orderable: false,
                        width: '5%',
                        searchable: false,
                        render: function ( data, type, full, row ) {
                            return '<a href="' + window.Misc.urlFull(Route.route('evaluacion.asignacion.show', {asignacion: full.codigo_registro})) + '" title="Ver" class="btn btn-default btn-xs text-center"><i class="fa fa-list-alt"></i></a>';
                        }
                    }
                ]
            });
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

            window.Misc.removeSpinner({
                'wrap': this.$el,
                'direct': true
            });
        },

        search: function(e) {
            e.preventDefault();
            this.assignmentsSearchTable.ajax.reload();
        },

        clear: function(e) {
            e.preventDefault();

            this.$searchordenpEntrepreneurship.val('');
            this.$searchordenpProgram.val('');
            this.$searchordenpPhase.val('');
            this.$searchordenNumber.val('');
            this.$searchMail.val('');
            this.$searchDocument.val('');
            this.$searchAssignment.val('');

            this.assignmentsSearchTable.ajax.reload();
        },

        /**
        * on filter program
        */
        onProgram: function (e) {
            e.preventDefault();
            if($(e.currentTarget).val() != null){
                $.ajax({
                    data:{program: $(e.currentTarget).val() },
                    url: window.Misc.urlFull( Route.route('convocatoria.fase.index')),
                    type: 'get',
                    success: function (data) {
                        $('#phase').html('');
                        $('#phase').append($('<option></option>').text('Seleccione una fase').val(0));
                        //recorremos con el metodo each el objeto
                        $.each(data, function(i) {
                            console.log(i);
                            //Con los parametros que recibimos en nuestro objeto convocatoria creamos las opciones
                            $('#phase').append("<option value=\""+data[i].codigo_fase+"\">"+data[i].nombre_fase+"<\/option>");
                        });
                    }
                });
            }
        }
   });

})(window.jQuery, window, document);
