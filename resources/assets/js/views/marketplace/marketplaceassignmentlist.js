/**
* Class MarketplaceAssignmentListView extends of Backbone View
* @author TuProyecto || Desarrollador : @krobing
* @link http://TuProyecto.com
*/

//Global App Backbone
app || (app = {});

(function ($, window, document, undefined) {

    app.MarketplaceAssignmentListView = Backbone.View.extend({
        tagName: 'table',
        events: {
            //
        },
        parameters: {
            codigo_registro_fase: null
        },

        /**
        * Constructor Method
        */
        initialize : function(opts){
            _.bindAll(this, 'beforeRender', 'render', 'afterRender');

            // Extend options parameters
            if( opts !== undefined && _.isObject(opts.parameters) ){
                this.parameters = _.extend({}, this.parameters, opts.parameters);
            }

            // Initialize attributes
            this.$formData = null;
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

            // Events Listener
            this.listenTo( this.collection, 'add', this.addOne );
            this.listenTo( this.collection, 'reset', this.addAll );
            this.listenTo( this.collection, 'request', this.loadSpinner );
            this.listenTo( this.collection, 'sync', this.responseServer );

            // custom events listener
            this.listenTo( this.collection, 'store', this.storeOne );

             // Events Listener custome
            this.on( 'beforeRender', this.beforeRender );
            this.on( 'afterRender', this.afterRender );
        },

        /*
        * Render View Element
        */
        render: function(){

            // initialize reference to element
            this.$noResults = this.$('.no-results-filter');

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

            // reload plugins
        },

        /**
        * Render view comment by model
        * @param Object assignmentModel Model instance
        */
        addOne: function (assignmentModel, collection, opts) {
            if( _.isUndefined(assignmentModel.id) )
                return;

            var view = new app.MarketplaceAssignmentMemberView({
                model: assignmentModel
            });

            this.$el.find('tbody').append( view.render().el );
            this.ready();
        },

        /**
        * Render all view jury of the collection
        */
        addAll: function () {
            this.$el.find('tbody').html('');
            this.collection.forEach( this.addOne, this );
        },

        /**
        * stores a assignment jury
        * @param form element
        */
        storeOne: function ( data, $formData ) {
            data || (data = {});

            var _this = this;

            data['codigo_registro_fase_usuario'] = this.parameters['codigo_registro_fase'];

            if( $formData.length ) this.$formData = $formData;

            this.collection.create(data, {patch:true, wait:true});
        },

        /**
        * Load spinner on the request
        */
        loadSpinner: function ( target, xhr, opts ) {

            this.$noResults.css('display', 'none');

            if( target instanceof Backbone.Collection )
                window.Misc.setSpinner( this.$el );
        },

        /**
        * response of the server
        */
        responseServer: function ( target, resp, opts ) {

            if( target instanceof Backbone.Collection && !target.length ){
                this.$noResults.fadeIn('fast');
            }else {
                this.$noResults.css('display', 'none');
            }

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

            // remove spinner
            window.Misc.removeSpinner({
                'wrap': this.$el,
                'direct': false
            });

            //complete proccess
            if( resp.success !== undefined ) {
                if( resp.success ){

                    window.Misc.showAlertBox(this.configAlert);

                    if( this.$formData ){
                        this.clearForm( this.$formData );
                    }
                }else {
                    window.Misc.showAlertBox(this.configAlert);
                    return false;
                }
            }
        },

        /**
        * clean form
        * @param Object | jQuery $formReset
        */
        clearForm: function ($formReset) {

            $formReset[0].reset();
        }
   });

})(window.jQuery, window, document);
