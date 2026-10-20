/**
* Class LoginView extends of Backbone View
* @author TuProyecto || Desarrollador : @dropecamargo
* @link http://TuProyecto.com
*/

//Global App Backbone
app || (app = {});

(function ($, window, document, undefined) {

    app.LoginView = Backbone.View.extend({

        el: '.login_wrapper',
        events:{

        },

        /**
        * Constructor Method
        */
        initialize : function() {
            // Google recaptcha
            if( this.$('.widget-login-recaptcha').length )
            {
                var widgetGRecaptcha = this.$('.widget-login-recaptcha')[0];
                setTimeout(function() {
                    grecaptcha.render(widgetGRecaptcha, {
                        'sitekey' : $(widgetGRecaptcha).data('key-recaptcha'),
                        'size': 'invisible',
                        'callback' : this.onLogin
                    })
                }.bind(this), 100)
            }
        },

        /*
        * Render View Element
        */
        render: function() {
            //
        },

        /*
        * Event onLogin
        */
        onLogin: function(token) {
            // Google recaptcha
            this.$('#form-login-account').submit();
        }
    });


})(window.jQuery, window, document);
