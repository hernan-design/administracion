/**
* Class EvaluationCreateEditView extends of Backbone View
* @author TuProyecto || Desarrollador : @dortegon
* @link http://TuProyecto.com
*/

//Global App Backbone
app || (app = {});

(function ($, window, document, undefined) {

    app.EvaluationCreateEditView = Backbone.View.extend({
        el: '#tab_form-questions',
        events: {
            'submit #form-survey': 'addSurvey',
            'click #agregar_ev': 'agregarItemEvaluacion'
        },
        parameters: {
            dataFilter: {}
        },

        /**
        * Constructor Method
        */
        initialize: function(opts){
            // _.bindAll(this, 'beforeRender', 'render', 'afterRender');

            // extends parameters
            if( opts.parameters !== undefined && _.isObject(opts.parameters) ) {
                this.parameters = $.extend({}, this.parameters, opts.parameters);
            }

            var _this = this;
            // this.render = _.wrap(this.render, function(render) {

            //     _this.trigger('beforeRender');

            //     render();

            //     _this.trigger('afterRender');

            //     return _this;
            // });

            // reference to elements
            this.$contentx = this.$('.content-x');
            this.$formCreate = this.$('#form-survey');
            console.log('hola');

             // Events Listeners
             this.listenTo( this.model, 'change', this.render );
             this.listenTo( this.model, 'request', this.loadSpinner);
             this.listenTo( this.model, 'sync', this.responseServer);
        },

        /*
        * Render View Element
        */
        render: function() {

        },

        agregarItemEvaluacion: function() {
            console.log('Click en agregarTipoEvaluacionItem');

            // Encuentra el último contenedor y obtén su índice

            var ultimoIndice = $('#numberItems').val()-1;
            console.log(ultimoIndice);
            // Clona el último contenedor
            var nuevoContenedor = $('#item-evaluation-' + ultimoIndice).clone();
            console.log(nuevoContenedor);
            // Incrementa el índice y ajusta los identificadores y valores
            var nuevoIndice = ultimoIndice + 1;

            nuevoContenedor.attr('id', 'item-evaluation-' + nuevoIndice);

            console.log(nuevoContenedor);

            // Ajusta los identificadores y valores dentro del contenedor clonado
            nuevoContenedor.find('[name^="tipo_evaluacion_item_"]').attr('name', 'tipo_evaluacion_item_' + nuevoIndice).val('');
            nuevoContenedor.find('[name^="grup_opc_eval_pregunta_"]').attr('name', 'grup_opc_eval_pregunta_' + nuevoIndice).val('');
            nuevoContenedor.find('[name^="porcentaje_item_eval_emprendi_"]').attr('name', 'porcentaje_item_eval_emprendi_' + nuevoIndice).val('');
            nuevoContenedor.find('[name^="orden_item_eval_emprendi_"]').attr('name', 'orden_item_eval_emprendi_' + nuevoIndice).val('');
            nuevoContenedor.find('[name^="grupo_item_"]').attr('name', 'grupo_item_' + nuevoIndice).val('');
            nuevoContenedor.find('[name^="nombre_item_evaluacion_"]').attr('name', 'nombre_item_evaluacion_' + nuevoIndice).val('');
            nuevoContenedor.find('[name^="descripcion_item_evaluacion_"]').attr('name', 'descripcion_item_evaluacion_' + nuevoIndice).val('');

            // Agrega el nuevo contenedor al final del formulario
            this.$('#item-evaluation-' + ultimoIndice).after(nuevoContenedor);
            $('#numberItems').val(nuevoIndice+1)
        },

        /**
        * fires libraries js
        */
        ready: function () {

        },

        /**
        * Load spinner on the request
        */
        loadSpinner: function () {
            window.Misc.setSpinner( this.$contentx );
        },

        /**
        * response of the server
        */
        responseServer: function ( model, resp, opts ) {

            window.Misc.removeSpinner({
                wrap: this.$contentx,
                direct: false
            });
        },

        /*
         * Add survey
        */
        addSurvey: function (e) {
            e.preventDefault();
            var data = window.Misc.formToJson(e.target);
            console.log(data);
            this.parameters.action = true;
            console.log(this.model);
            
            this.model.save(data, {
                wait: true,
                success: function () {
                    console.log(save);
                    location.reload();
                }
            });
            
        },

    });
})(window.jQuery, window, document);
