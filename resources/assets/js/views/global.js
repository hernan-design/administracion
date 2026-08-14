/**
* Class GlobalView  of Backbone
* @author KOI || @dropecamargo
* @link http://koi-ti.com
*/

//Global App Backbone
app || (app = {});

(function ($, window, document, undefined) {

    app.GlobalView = Backbone.View.extend({

		el: 'body',
		events: {
			'click #menu_toggle': 'clickSidebar'
		},

        /**
        * Constructor Method
        */
		initialize: function() {
			// Initialize
		},	

		clickSidebar: function(e) {
			e.preventDefault();

			var expiration = new Date();
			expiration.setFullYear(expiration.getFullYear() + 1);

			// Create or update the cookie:
			document.cookie = "menu_toggle=" + (this.$el.hasClass('nav-md') ? 'nav-md' : 'nav-sm') + "; path=/; expires=" + expiration.toUTCString();
		}
    });


})(jQuery, this, this.document);
