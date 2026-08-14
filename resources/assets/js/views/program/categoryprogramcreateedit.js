/**
* Class CategoryProgramCreateEditView extends of Backbone View
* @author TuProyecto || Desarrollador : @dortegon
* @link http://TuProyecto.com
*/

//Global App Backbone
app || (app = {});

(function ($, window, document, undefined) {

    app.CategoryProgramCreateEditView = Backbone.View.extend({
        el: '#category-content',
        events: {
            'submit #form-category': 'addCategory'
        },
        parameters: {
            //
        },

        /**
        * Constructor Method
        */
        initialize: function(opts){

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

            // Events Listener
            this.listenTo( this.model, 'request', this.loadSpinner );
            this.listenTo( this.model, 'sync', this.responseServer );
        },

        /*
        * Render View Element
        */
        render: function() {
            // Reference to elements
            this.$formCreate = this.$('#form-profession');
        },

        /**
        * Load spinner on the request
        */
        loadSpinner: function () {
            window.Misc.setSpinner( this.$('#section-content') );
        },

        /**
        * Add new profession
        */
        addCategory: function (e) {
            e.preventDefault();

            var data = window.Misc.formToJson( e.target );
            this.model.save( data, {wait: true, patch:true} );
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
                        window.Misc.redirect( app.AppRouter.navigate( Route.route('convocatoria.categoria.edit',{categorium: this.model.get('codigo_categoria_proceso')}) ));
                    }
                }else {
                    window.Misc.showAlertBox(this.configAlert);
                    return false;
                }
            }
        }
    });
})(jQuery, this, this.document);
