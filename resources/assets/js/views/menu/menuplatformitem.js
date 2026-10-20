/**
* Class MenuPlatformItemView extend of Backbone View
* @author TuProyecto || Desarrollador : @xindykatalina
* @link http://TuProyecto.com
*/

//Global App Backbone
app || (app = {});

(function ($, window, document, undefined) {

 	app.MenuPlatformItemView = Backbone.View.extend({
        template: _.template( ($('#menu-item-list-tpl').html() || '') ),
        events: {
            'click .menu-post-hide': 'onHide'
        },

        /**
        * Constructor Method
        */
        initialize : function(opts){

            // Extend options parameters
            if( _.isObject(opts.parameters) ){
                _.extend(this.parameters, opts.parameters);
            }

            this.configAlert = {
                'wrapper': '#main-wrap',
                'closeTime': 6000,
                'speedOpen': 500,
                'speedClose': 500
            };

            //Init Attributes
            this.confirmHide = new app.MenuHideView({ model:this.model });

            // Events Listener
            this.listenTo( this.model, "change", this.render );
            this.listenTo( this.model, 'request', this.loadSpinner );
            // this.listenTo( this.model, 'destroy', this.remove );
            this.listenTo( this.model, 'sync', this.responseServer );
        },


        /*
        * Render View Element
        */
        render: function(){

            //Init Attributes
            var attributes = this.model.toJSON();

            this.$el.html( this.template(attributes) );

            return this;
        },

        /**
        * Load spinner on the request
        */
        loadSpinner: function ( target, xhr, opts ) {

            window.Misc.setSpinner( this.$el );
        },

        /*
        * Remove View Element
        */
        remove: function(){

            var _this = this;
            this.$el.fadeOut('normal', function (){
                _this.$el.remove();
            });
        },

        /**
        * hides item menu
        */
        onHide: function (e) {
            e.preventDefault();  
            this.confirmHide.render();
        },

        /**
        * response of the server
        */
        responseServer: function (target, resp, opts) {

            // response success or error
            var text = !resp.success ? resp.errors: '',
                type = resp.success ? 'success' : 'alert';

            if( _.isObject(resp.errors) ){

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
            };
            _.extend( this.configAlert, config );

            //complete proccess
            this.$el.find('#spinner-load-posts').remove();
            window.Misc.removeSpinner({
                'wrap': this.$el,
                'direct': true
            });
            if( resp.success !== undefined ){
                if( resp.success ){
                    this.remove();
                }else if(!resp.success){
                    new PNotify({
                        title: "ADVERTENCIA",
                        type: "info",
                        text: this.configAlert.text,
                        addclass: 'dark',
                        styling: 'bootstrap3',
                        hide: true,
                        mobile: {
                            swipe_dismiss: false
                        }
                    });
                }
            }
        }
    });

})(window.jQuery, window, document);
