/**
* Class ChannelContentView  of Backbone
* @author TuProyecto || @dortegon
* @link http://TuProyecto.com
*/

//Global App Backbone
app || (app = {});

(function ($, window, document, undefined) {

    app.ChannelContentView = Backbone.View.extend({
        template: _.template( ($('#multiselect-channel-content-tpl').html() || '') ),
		events: {
            //
		},
        parameters:{
            dataFilter:{}
        },

        /**
        * Constructor Method
        */
		initialize: function(opts) {

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

            //Init attributes
            this.collection = new app.ChannelsContentList();

            var data = {
                tipo_contenido: this.parameters.tipo_contenido,
                id_contenido: this.parameters.id_contenido
            }

            this.collection.fetch({data: data ,reset: true});

            // Events Listener
            this.listenTo( this.collection, 'reset', this.render );
			this.listenTo( this.collection, 'request', this.loadSpinner);
			this.listenTo( this.collection, 'sync', this.responseServer);
		},

        render: function () {
            var attributes = {};
            attributes.channels = this.collection.toJSON();
            attributes.id_contenido = this.parameters.id_contenido;
            attributes.tipo_contenido = this.parameters.tipo_contenido;

            this.$el.html( this.template(attributes) );
            this.ready();
        },

        /**
        * fires libraries js
        */
        ready: function () {
            // to fire plugins
            if(typeof window.initComponent.multiselectTrigger == 'function' ){
                window.initComponent.multiselectTrigger();
            }
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
                }else {
                    window.Misc.showAlertBox(this.configAlert);
                    return false;
                }
            }
        },
    });


})(window.jQuery, window, document);
