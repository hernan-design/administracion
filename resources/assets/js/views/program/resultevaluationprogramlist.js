/**
* Class ResultEvaluationProgramListView extend of Backbone View
* @author TuProyecto || Desarrollador : @brahian-dev
* @link http://TuProyecto.com
*/

//Global App Backbone
app || (app = {});

(function ($, window, document, undefined) {

    app.ResultEvaluationProgramListView = Backbone.View.extend({

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
        addOne: function (ResultEvaluationProgramModel) {

            var view = new app.ResultEvaluationProgramItemView({
                model: ResultEvaluationProgramModel,
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
            var collection = this.collection.toJSON();

            this.$el.find('tbody').html('');
            collection[0].response.forEach( this.addOne, this );
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

})(window.jQuery, window, document);
