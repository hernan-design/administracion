/**
* Class AssignmentJuryItemView of Backbone View
* @author TuProyecto || Desarrollador : @xindykatalina
* @link http://TuProyecto.com
*/

//Global App Backbone
app || (app = {});

(function ($, window, document, undefined) {

    app.AssignmentJuryItemView = Backbone.View.extend({

        tagName: 'tr',
        template: _.template( ($('#assignment-jury-item-tpl').html() || '') ),
        events: {
            'click .remove-jury': 'onRemove',
            'click .edit-jury': 'onEdit'
        },
        parameters: {
            asignacion_especialidad: false
        },

        /**
        * Constructor Method
        */
        initialize : function(opts){
             _.bindAll(this, 'beforeRender', 'render', 'afterRender');

            // extends parameters
            if( opts.parameters !== undefined && _.isObject(opts.parameters) )
                this.parameters = $.extend({}, this.parameters, opts.parameters);

            var _this = this;
            this.render = _.wrap(this.render, function(render) {

                _this.trigger('beforeRender');

                render();

                _this.trigger('afterRender');

                return _this;
            });

            //Init Attributes

            // Events Listener
            this.listenTo( this.model, "change", this.render );
            this.listenTo( this.model, 'destroy', this.remove );
            this.listenTo( this.model, 'request', this.loadSpinner );
            this.listenTo( this.model, 'sync', this.responseServer );

            // Events Listener custome
            this.on( 'beforeRender', this.beforeRender );
            this.on( 'afterRender', this.afterRender );
        },

        /*
        * Render View Element
        */
        render: function () {

            if( this.model.id !== undefined ) {
                var attributes = this.model.toJSON();
                attributes.asignacion_especialidad = this.parameters.asignacion_especialidad

                this.$el.html( this.template(attributes) );
            }

            return this;
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

            // input mask
            this.$(":input").inputmask();
        },

        /**
        * Load spinner on the request
        */
        loadSpinner: function () {

            window.Misc.setSpinner( this.$el );
        },

        /**
        * response server
        * @param Object res
        */
        responseServer: function ( model, resp, opts) {
            var _this = this;

            // response success or error
            var type = resp.success ? 'success' : 'alert',
                txt = resp.success ?
                    (resp.message !== undefined ? resp.message : '...')
                    : resp.errors;

            if( _.isObject( resp.errors ) ){

                var listError = '<ul>';

                $.each(resp.errors, function(field, item) {
                     listError += '<li>'+ item[0] +'</li>';
                });
                listError += '</ul>';

                txt = listError;
            }
            //complete proccess
            if( resp.success !== undefined ){
                if( resp.success ){
                    new PNotify({
                        title: 'Procesando..',
                        text:  txt,
                        type: 'success',
                        addclass: "stack-custom2"
                    });
                }else{
                   new PNotify({
                        title: 'Error!',
                        text: txt
                    });
                }
            }

            //complete proccess
            window.Misc.removeSpinner({
                'wrap': this.$el,
                'direct': false
            });
        },

        /*
        * Remove View Element
        */
        remove: function(){
            var _this = this;
            this.$el.fadeOut('slow', function (){
                $(this).remove();
            });
        },

        /**
        * Remove element
        */
        onRemove: function (e) {
            e.preventDefault();

            this.model.destroy({
                'processData': true,
                wait: true,
                patch: true,
                data: {
                    '_token': $('meta[name="csrf-token"]').attr('content'),
                    codigo_asignacion_jurado: this.model.get('codigo_asignacion_jurado'),
                    codigo_usuario: this.model.get('codigo_usuario')
                }
            });
        },

        /**
        * Edit element
        */
        onEdit: function (e) {

            e.preventDefault();
            var data = {};
            data.codigo_asignacion_jurado = this.model.get('codigo_asignacion_jurado');
            data.codigo_usuario = this.model.get('codigo_usuario');
            data.lugar_evaluacion = this.$('#lugar_evaluacion_'+this.model.get('codigo_asignacion_jurado')).val();
            data.fecha_evaluacion = this.$('#fecha_evaluacion_'+this.model.get('codigo_asignacion_jurado')).val();
            data.codigo_especialidad = this.$('#codigo_especialidad_'+this.model.get('codigo_asignacion_jurado')).val();

            this.model.save( data, {patch: true, wait:true} );
        }

    });

})(window.jQuery, window, document);

