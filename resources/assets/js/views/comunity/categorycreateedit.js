/**
* Class CategoryCreateEditView extends of Backbone View
* @author TuProyecto || Desarrollador : @dortegon
* @link http://TuProyecto.com
*/

//Global App Backbone
app || (app = {});

(function ($, window, document, undefined) {

    app.CategoryCreateEditView = Backbone.View.extend({
        el: '#category-content',
        events: {
            'submit #form-category': 'addCategory'
        },
        parameters: {

        },

        /**
        * Constructor Method
        */
        initialize : function(opts) {

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

            this.channelCategoryModel = new app.ChannelCategoryModel();

            // Events Listener
            this.listenTo( this.model, 'request', this.loadSpinner );
            this.listenTo( this.model, 'sync', this.responseServer );
        },

        /*
        * Render View Element
        */
        render: function() {
            //
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
                wrap: this.$el,
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
                    app.AppRouter.navigate( Route.route('comunidad.categoria.edit',{categoria: this.model.get('codigo_categoria_canal')}) );

                }else {
                    window.Misc.showAlertBox(this.configAlert);
                }
            }
        },

        /*
         * Add category
        */
        addCategory: function (e) {
            e.preventDefault();

            var data = window.Misc.formToJson( e.target )
            this.model.save( data, {wait: true} );
        }
   });

})(jQuery, this, this.document);
