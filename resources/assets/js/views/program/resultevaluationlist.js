/**
* Class ResultEvaluationListView extend of Backbone View
* @author TuProyecto || Desarrollador : @xindykatalina
* @link http://TuProyecto.com
*/

//Global App Backbone
app || (app = {});

(function ($, window, document, undefined) {

    app.ResultEvaluationListView = Backbone.View.extend({

        tagName: 'ul',
        events: {
            //
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
        * @param Object ResultEvaluationModel Model instance
        */
        addOne: function (ResultEvaluationModel) {

            var view = new app.ResultEvaluationItemView({
                model: ResultEvaluationModel,
                parameters: {
                    dataFilter: this.parameters.dataFilter
                }
            });

            this.$el.find('tbody').append( view.render().el );
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
        }
   });

})(jQuery, this, this.document);





