/**
* Class UserRolItemView extends of Backbone View
* @author TuProyecto || Desarrolladora : @dortegon
* @link http://TuProyecto.com
*/

//Global App Backbone
app || (app = {});

(function ($, window, document, undefined) {

    app.UserRolItemView = Backbone.View.extend({
        tagName: 'li',
        className: 'tp_list-content',
        template: _.template( ($('#user-role-list-tpl').html() || '') ),
        events: {
            // 'submit #form-add-themes-course' : 'onAddTheme',
        },
        parameters: {
            dataFilter: {}
        },

        /**
        * Constructor Method
        */
        initialize : function(opts) {

            // extends parameters
            if( opts.parameters !== undefined && _.isObject(opts.parameters) )
                this.parameters = $.extend({}, this.parameters, opts.parameters);

            this.configAlert = {
                'wrapper': '#main-wrap',
                'closeTime': 20000,
                'speedOpen': 500,
                'speedClose': 500
            };

            // Events Listener
			this.listenTo( this.model, "change", this.render );
        },

        /*
        * Render View Element
        */
        render: function () {
            var attributes = this.model.toJSON();
            this.$el.html( this.template(attributes) );
            return this;
        },

        /**
		* fires libraries js and functions
		*/
        ready: function () {
            if( typeof window.initComponent.validateForm == 'function' ){
                window.initComponent.validateForm();
            }

            if( typeof window.initComponent.configEllipsis == 'function' ){
                window.initComponent.configEllipsis();
            }
        },

        /**
        * reference to views
        */
        referenceViews: function () {
            //
        },

        /**
        * Load spinner on the request
        */
        loadSpinner: function () {
            window.Misc.setSpinner( this.$wrapperThemes );
        },


        /**
        * response of the server
        */
        responseServer: function ( model, resp, opts ) {

            //complete proccess
            window.Misc.removeSpinner({
                'wrap': this.$el,
                'direct': false
            });
        },

        /**
		* reset form fields
		* @param parentForm Element
		*/
		resetFields: function (parentForm) {

			// reset inputs
            $(':input', parentForm).not(':button, :submit, :reset, :hidden, :checkbox, :radio').val('').removeAttr();

			this.ready();
		}
   });

})(window.jQuery, window, document);
