/**
* Class UserRoleListView extends of Backbone View
* @author TuProyecto || Desarrollador : @dortegon
* @link http://TuProyecto.com
*/

//Global App Backbone
app || (app = {});

(function ($, window, document, undefined) {

    app.UserRoleListView = Backbone.View.extend({
        templateResultCount: _.template( ($('#browse-result-count-tpl').html() || '') ),
        events: {
            //
        },
        parameters: {
			dataFilter: {},
            role: ''
        },

        /**
        * Constructor Method
        */
        initialize: function (opts) {
            var _this = this;

            // extends parameters
            if( opts !== undefined && _.isObject(opts.parameters) ){

                this.parameters = _.extend({}, this.parameters, opts.parameters);
            }

            // initialize reference to elements
            this.$postsWrap = this.$('#list-content-article');
            this.$wrapPagination = this.$('.wrap-pagination');
            this.$noResults = this.$('#article-list > .no-results-filter');

            // Events Listeners
			this.listenTo( this.collection, 'add', this.addOne );
			this.listenTo( this.collection, 'reset', this.addAll );
			this.listenTo( this.collection, 'reset', this.render );
			this.listenTo( this.collection, 'request', this.loadSpinner);
			this.listenTo( this.collection, 'sync', this.responseServer);
        },

        render: function () {
            var _this = this;

            // set paginations
			this.$wrapPagination.each(function(index, el) {

				var pagination = new app.Paginator({
					className: 'right pagination tablet-text-right',
					collection: _this.collection,
					parameters: {
						'data': _this.parameters
					}
				});

				$(el).html( pagination.render().el );

			}.bind(this));
        },

        /**
		* fires libraries js and functions
		*/
        ready: function () {
            if( typeof window.initComponent.configEllipsis == 'function' ){
                window.initComponent.configEllipsis();
            }
        },

        /**
		* Render view comment by model
		* @param Object userRole Model instance
		*/
		addOne: function (userRole) {

		    var view = new app.UserRolItemView({
				model: userRole,
				parameters: {
					dataFilter: this.parameters.dataFilter
				}
			});

			this.$postsWrap.append( view.render().el );

            if( (this.collection.indexOf(userRole) + 1) >= this.collection.length ) {
                this.ready();
            }
		},

		/**
		* Render all view Course of the collection
		*/
		addAll: function () {
            if(!this.collection.length) {
                this.$noResults.show();
                window.Misc.removeSpinner({'wrap': this.$postsWrap});
            }else {
                this.$noResults.hide();
            }

			this.$postsWrap.html('');
			this.collection.forEach( this.addOne, this );
		},

        /**
		* Load spinner on the request
		*/
		loadSpinner: function ( target, xhr, opts ) {

			window.Misc.setSpinner( this.$postsWrap );
		},

        /**
		* response of the server
		*/
		responseServer: function ( target, resp, opts ) {

			window.Misc.removeSpinner({
				'wrap': this.$el,
				'direct': false
			});

			window.Misc.removeSpinner({'wrap': this.$postsWrap})

			// set result count
			this.resultCount(this.collection.state);
		},

		/**
		* set result count
		* @param Object states
		*/
		resultCount: function (states) {
            var fromResult = 0,
                toResult = 0;

			if( !(this.collection instanceof Backbone.PageableCollection) ){
                return;
            }

            if(this.collection.state.totalPages){
                fromResult = this.collection.state.currentPage <= this.collection.state.firstPage ? 1 : (this.collection.state.currentPage * this.collection.state.pageSize) + 1;
            }

			toResult = (this.collection.state.firstPage ? this.collection.state.currentPage : this.collection.state.currentPage + 1) * this.collection.state.pageSize;

			this.$('.wrap-result-count').html(this.templateResultCount({
				'from': fromResult,
				'to': toResult > this.collection.state.totalRecords ? this.collection.state.totalRecords : toResult,
				'totalRecords': this.collection.state.totalRecords
			}));
		},

		/**
		* change order by
		*/
		onOrdering: function (e) {
			var orderBy = $(e.target).find('option:selected').val();
			// filter new data
			this.trigger('getdata', {
				orderBy: orderBy
			});
		}
    });

})(window.jQuery, window, document);
