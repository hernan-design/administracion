/**
* Class ActivityLogView extend of Backbone View
* @author TuProyecto || Desarrollador : @dortegon
* @link http://TuProyecto.com
*/

//Global App Backbone
app || (app = {});

(function ($, window, document, undefined) {

	app.ActivityLogView = Backbone.View.extend({
        el: '#activities-log',
        events: {
            //
        },
		attributes: {
			'data-srcparams': ''
		},
		parameters: {
            'subject': '',
            'subject_type': '',
            'causer': '',
            'type': 'basic'
		},

		/**
		* Constructor Method
		*/
		initialize : function(opts){
			_.bindAll(this, 'viewMore', 'viewLess');

			// Extend attributes
			if( opts.parameters !== undefined && _.isObject(opts.parameters) ){
				_.extend( this.parameters, opts.parameters );
			}else{
				_.extend( this.parameters, this.attributes );
			}

            this.$btnViewMore = $('.content-btn-view-more');
            this.$btnViewMore.on('click', '.activity-view-more', this.viewMore);
            this.$btnViewMore.on('click', '.activity-view-less', this.viewLess);
		},

		/*
		* Render View Element
		*/
		render: function(){
            this.onShowActivitiesLog();
		},

        // Load activities log
        viewMore: function (e) {
            e.preventDefault();
            e.stopPropagation();

            $(e.currentTarget).text('Ocultar historial de actividad')
                .removeClass('activity-view-more')
                .addClass('activity-view-less');

            this.onShowActivitiesLog();
        },

        // Remove activities log
        viewLess: function (e) {
            e.preventDefault();

            $(e.currentTarget).text('Mostrar historial de actividad')
                .removeClass('activity-view-less')
                .addClass('activity-view-more');

            $('#activities-log').html('');
        },

        // Show activities log
        onShowActivitiesLog: function () {

            // to render view activities log
            this.viewActivitiesLog = new app.ActivitiesLogListView({
                parameters: {
                    'subject': this.parameters['subject'],
                    'subject_type': this.parameters['subject_type'],
                    'causer': this.parameters['causer'],
                    'type': this.parameters['type'],
                }
            });

            this.$el.html( this.viewActivitiesLog.render().el );
            this.$el.append( this.viewActivitiesLog.$wrapViewMore );
        }
   });

})(window.jQuery, window, document);
