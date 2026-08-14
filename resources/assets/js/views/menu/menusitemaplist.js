/**
* Class MenuSitemapListView of Backbone View
* @author TuProyecto || Desarrollador : @xindykatalina
* @link http://TuProyecto.com
*/

//Global App Backbone
app || (app = {});

(function ($, window, document, undefined) {

    app.MenuSitemapListView = Backbone.View.extend({

        el: '#menu-content',
        attributes: {
           // 
        },
        events: {

        },

        /**
        * Constructor Method
        */
        initialize : function(){
            // Init Attributes

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
            // Init Attributes
            this.$menusitemap = this.$('#menu-list-sitemap');

            return this;
        },

        /**
        * Render view menu platform model
        * @param Object menuModel Model instance
        */
        addOne: function (menuModel) {
            var view = new app.MenuSitemapItemView({
                model: menuModel,
                attributes: {}
            });

            this.$menusitemap.append( view.render().el );
        },

        /**
        * Render all view category of the collection
        */
        addAll: function () {
            this.$menusitemap.html('');
            this.collection.forEach( this.addOne, this );
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
                'wrap': this.$el,
                'direct': true
            });
        }

   });

})(jQuery, this, this.document);
