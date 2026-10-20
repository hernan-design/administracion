/**
* Class MenuSitemapItemView extend of Backbone View
* @author TuProyecto || Desarrollador : @xindykatalina
* @link http://TuProyecto.com
*/

//Global App Backbone
app || (app = {});

(function ($, window, document, undefined) {

 	app.MenuSitemapItemView = Backbone.View.extend({

        tagName: 'dd',
        className: 'accordion-navigation-theme',
        template: _.template( ($('#menu-item-sitemap-list-tpl').html() || '') ),

        /*
        * Render View Element
        */
        render: function(){

            var attributes = this.model.toJSON();
            this.$el.html( this.template(attributes) );

            //Init Attributes
            return this;
        }

    });

})(window.jQuery, window, document);
