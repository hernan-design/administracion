/**
* Class BaseMenuView extends of Backbone View
* @author TuProyecto || Desarrolladora : @xindykatalina
* @link http://TuProyecto.com
*/

//Global App Backbone
app || (app = {});

(function  ($, window, document, undefined) {

    app.BaseMenuView = Backbone.View.extend({

        el: '#menu-content',
        template: _.template( ($('#menu-list-tpl').html() || '') ),
        events: {
        },

        /**
        * Constructor Method
        */
        initialize: function ( ) {
            // Init Attributes
            this.menuList = new app.MenuList();
            this.menuSitemapList = new app.MenuList();
        },

        /*
        * Render View Element
        */
        render: function () {

            // Initialize Attributes
            var attributes = {};
            this.$el.html( this.template(attributes) );

          // reference to views
            this.referenceViews();
        }, 

        /**
        * reference to views
        */
        referenceViews: function () {

            if( this.menuPlatformListView instanceof Backbone.View ) {
                this.menuPlatformListView.stopListening();
                this.menuPlatformListView.undelegateEvents();
            } 

            // Intance and get Data of Menu Platform list view
            this.menuPlatformListView = new app.MenuPlatformListView( {
                collection: this.menuList,
                parameters: {
                    dataFilter: {} 
               }
            } );
            this.menuPlatformListView.render();
            this.menuPlatformListView.trigger('getdata');
            this.menuList.fetch( {data: {'type': 2}, reset:true} );


            // if( this.menuSitemapListView instanceof Backbone.View ) {
            //     this.menuSitemapListView.stopListening();
            //     this.menuSitemapListView.undelegateEvents();
            // } 

            // // Intance and get Data of Menu Platform list view
            // this.menuSitemapListView = new app.MenuSitemapListView( {
            //     collection: this.menuSitemapList,
            //     parameters: {
            //         dataFilter: {} 
            //    }
            // } );
            // this.menuSitemapListView.render();
            // this.menuSitemapListView.trigger('getdata');
            // this.menuSitemapList.fetch({data: {'type': 3}, reset:true});
        }

    });

})(jQuery, this, this.document);
