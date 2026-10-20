/**
* Class CategoryProductListView extend of Backbone View
* @author TuProyecto || Desarrollador : @xindykatalina
* @link http://TuProyecto.com
*/

//Global App Backbone
app || (app = {});

(function ($, window, document, undefined) {

    app.CategoryProductListView = Backbone.View.extend({

        tagName: 'ul',
        events: {
            'change .valid-checkbox': 'onValidCheck',
            'ifChecked .valid-checkbox': 'onValidCheck'
        },
        parameters: {
            //dataFilter: {}
        },

        /**
        * Constructor Method
        */
        initialize : function(opts){

            // Extend options parameters
            if( _.isObject(opts.parameters) ){
                _.extend(this.parameters, opts.parameters);
            }

            // Events Listener

            this.listenTo( this.collection, 'add', this.addOne );
            this.listenTo( this.collection, 'reset', this.addAll );
            this.listenTo( this.collection, 'request', this.loadSpinner );
            this.listenTo( this.collection, 'sync', this.responseServer );
            this.listenTo( this.collection, 'all', this.render );

        },

        /*
        * Render View Element
        */
        render: function(){

            var attributes = {},
            _this = this;

            // initialize reference
            this.$noResults = this.$('.no-results-filter');

            return this;
        },

        /**
        * Render view comment by model
        * @param Object CategoryProductModel Model instance
        */
        addOne: function (CategoryProductModel) {

            var view = new app.CategoryProductItemView({
                model: CategoryProductModel,
                parameters: {
                    dataFilter: this.parameters.dataFilter
                }
            });

            this.$el.find('tbody').append( view.render().el );

            // reload plugins
            if( typeof window.initComponent.icheckTrigger == 'function' && _.has($.fn, 'iCheck'))
            window.initComponent.icheckTrigger();
        },

        /**
        * Render all view participant of the collection
        */
        addAll: function () {

            this.$el.find('tbody').html('');
            this.collection.forEach( this.addOne, this );
        },

        /**
        * Load spinner on the request
        */
        loadSpinner: function ( target, xhr, opts ) {

            window.Misc.setSpinner( this.$('table') );
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

            window.Misc.removeSpinner({
                'wrap': this.$('table'),
                'direct': false
            });
        },

        /*
        * validate check multiples
        */
        onValidCheck: function (e) {
            var $elCheck = $(e.target);
            if ( $('.valid-checkbox:checked').length > 3 ) {

                if( $elCheck.data('iCheck') != undefined ) {
                    setTimeout(function(){ $elCheck.iCheck('uncheck'); }, 1);

                    return;
                }

                $elCheck.prop('checked', false);
            }
        }
   });

})(window.jQuery, window, document);
