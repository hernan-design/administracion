/**
* Class ActivitiesLogListView extend of Backbone View
* @author TuProyecto || Desarrollador : @dortegon
* @link http://TuProyecto.com
*/

//Global App Backbone
app || (app = {});

(function ($, window, document, undefined) {

	app.ActivitiesLogListView = Backbone.View.extend({
		tagName: 'ul',
		className: 'list-activities-log',
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
		templateBtnMore: _.template( ($('#btn-view-more-tpl').html() || '') ),

		/**
		* Constructor Method
		*/
		initialize : function(opts){
			_.bindAll(this, 'showMore');

			// Extend attributes
			if( opts.parameters !== undefined && _.isObject(opts.parameters) ){
				_.extend( this.parameters, opts.parameters );
			}else{
				_.extend( this.parameters, this.attributes );
			}

			//Init Attributes
			this.$elmSpinner = this.$el;
			this.collection = new window.app.ActivitiesLogList();
			this.$wrapViewMore = $( this.templateBtnMore() );
			this.$spinnerActivitiesLog = $('<div>').attr('id','spinner-load-userlikes');

			// Events Listener
			this.listenTo( this.collection, 'add', this.addOne );
			this.listenTo( this.collection, 'reset', this.addAll );
			this.listenTo( this.collection, 'request', this.loadSpinner);
			this.listenTo( this.collection, 'sync', this.responseServer);
			this.$wrapViewMore.on( 'click', '.btn-view-more', this.showMore);
		},

		/*
		* Render View Element
		*/
		render: function(){

			this.$wrapViewMore.hide();
			this.collection.getFirstPage( {data: {
				'subject': this.parameters['subject'],
				'subject_type': this.parameters['subject_type'],
				'causer': this.parameters['causer'],
				'type': this.parameters['type']
			}, reset: true });

			return this;
		},

		/**
		* Render view comment by model
		* @param Object activityLogModel Model instance
		*/
		addOne: function (activityLogModel) {
			var view = new app.ActivityLogItemView( {model: activityLogModel} );
			this.$el.append( view.render().el );
		},

		/**
		* Render all view comment of the collection
		*/
		addAll: function () {
			this.$el.html('');
			this.collection.forEach( this.addOne, this );
		},

		/**
		* Load spinner on the request
		*/
		loadSpinner: function ( target, xhr, opts ) {
			if( this.collection.state.currentPage >= this.collection.state.lastPage ){
				this.$elmSpinner = this.$el;
			}else{
				this.$elmSpinner = this.$wrapViewMore;
				this.$spinnerActivitiesLog.appendTo( this.$elmSpinner );
			}

			window.Misc.setSpinner( this.$elmSpinner );
		},

		/**
		* response of the server
		*/
		responseServer: function ( target, resp, opts ) {

			// complete process
			window.Misc.removeSpinner({
				'wrap': this.$elmSpinner,
				'direct': false
			});

			if( this.collection.state.currentPage >= this.collection.state.lastPage ){
				this.$wrapViewMore.hide();
			}else{
				this.$wrapViewMore.show();
			}

		},

		/**
		* Show more activities log it
		*/
		showMore: function (e) {
			e.preventDefault();

			if( this.collection.hasNextPage() ){
				this.collection.getNextPage( {
                    data: {
    					'subject': this.parameters['subject'],
    					'subject_type': this.parameters['subject_type'],
    					'causer': this.parameters['causer'],
    					'type': this.parameters['type']
                    }, wait:true
				});
			}
		}
   });

})(jQuery, this, this.document);
