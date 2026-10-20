/**
* Class ProgramCreateEditView extends of Backbone View
* @author TuProyecto || Desarrollador : @dortegon
* @link http://TuProyecto.com
*/

//Global App Backbone
app || (app = {});

(function ($, window, document, undefined) {

    app.ResponseEvaluationView = Backbone.View.extend({
        el: '#response_evaluation_content',
        events: {
            'submit #form-key-access': 'onSubmit'
        },
        parameters: {
            //
        },

        /**
        * Constructor Method
        */
        initialize: function(opts){

            // extends parameters
            if( opts.parameters !== undefined && _.isObject(opts.parameters) ){
                this.parameters = $.extend({}, this.parameters, opts.parameters);
            }

            //Init Attribute
            this.configAlert = {
                'wrapper': '#main-wrap',
                'closeTime': 6000,
                'speedOpen': 500,
                'speedClose': 500
            };

            this.listenTo( this.model, 'request', this.loadSpinner );
            this.listenTo( this.model, 'sync', this.responseServer );
        },

        /*
        * Render View Element
        */
        render: function() {
            console.log(this.parameters);
        },


        onSubmit: function(e) {
            e.preventDefault();

            var data = window.Misc.formToJson(e.target);
            this.model.save(data);
        },

        /**
        * Load spinner on the request
        */
        loadSpinner: function () {
            window.Misc.setSpinner( this.$('#tab_group_five') );
        },

        /**
        * response of the server
        */
       responseServer: function ( model, resp, opts ) {
            var _this = this;
            window.Misc.removeSpinner({
                wrap: this.$('#tab_group_five'),
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
                    window.Misc.showAlertBox(_this.configAlert);

                    window.setTimeout(function() {
                        window.Misc.redirect()
                    }, 3000);
                }else {
                    window.Misc.showAlertBox(this.configAlert);
                    return false;
                }
            }
        },
    });
})(window.jQuery, window, document);
