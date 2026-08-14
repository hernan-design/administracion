/**
* Class ProgramListView extends of Backbone View
* @author TuProyecto || Desarrollador : @dortegon
* @link http://TuProyecto.com
*/

//Global App Backbone
app || (app = {});

(function ($, window, document, undefined) {

    app.ProgramListView = Backbone.View.extend({

        el: '#programs-content',
        templateResultCount: _.template( ($('#browse-result-count-tpl').html() || '') ),
        events: {
            //
        },
        parameters: {
            dataFilter: {}
        },

        /**
        * Constructor Method
        */
        initialize : function(opts) {
            _.bindAll(this, 'beforeRender', 'render', 'afterRender');

            // extends parameters
            if( opts.parameters !== undefined && _.isObject(opts.parameters) )
                this.parameters = $.extend({}, this.parameters, opts.parameters);

            //Init Attribute

            var _this = this;
            this.render = _.wrap(this.render, function(render) {

                _this.trigger('beforeRender');

                render();

                _this.trigger('afterRender');

                return _this;
            });

            // initialize reference to elements
            this.$formFilter = this.$('#form-program-filter');
            this.$postsWrap = this.$('#tp_list-content-article');
            this.$wrapPagination = this.$('.wrap-pagination');
            this.$blockFiltersMenu = this.$('#content-form-program-filter');
            this.$noResults = this.$('#article-list > .no-results-filter');

			// set paginations
			this.$wrapPagination.each(function(index, el) {
                console.log(el);
				var pagination = new app.Paginator({
					className: 'right pagination tablet-text-right',
					collection: _this.collection,
					parameters: {
						// 'nameForm': '#form-program-filter',
						'data': _this.parameters.dataFilter
					}
				});
				$(el).html( pagination.render().el );
			}.bind(this));

            // Events Listener custome
			this.listenTo( this.collection, 'add', this.addOne );
			this.listenTo( this.collection, 'reset', this.addAll );
			this.listenTo( this.collection, 'request', this.loadSpinner);
			this.listenTo( this.collection, 'sync', this.responseServer);
            this.on('getdata', this.getData);
            this.on( 'beforeRender', this.beforeRender );
            this.on( 'afterRender', this.afterRender );

        },

        render: function () {
            var _this = this;
            // set block filter
            this.$blockFiltersMenu.each(function(index, el) {
                var blockFilterMenu = new app.FilterData({
                    el: el,
                    collection: _this.collection,
                    parameters: {
                        'nameForm': _this.$formFilter,
                        'callBackFilter': function (newDataFilter) {

                            // navigate to route
                            app.AppRouter.navigate(
                                Route.route('convocatoria.index'),
                                {trigger: false, replace: true}
                            );
                            _this.getData(newDataFilter, true);
                        },
                        'dataFilter': _.extend({}, _this.parameters.dataFilter)
                    }
                });
            });
        },

        /**
        * get reset data
        * @param dataFilter Object
        * @param replace Boolean
        */
        getData: function  (dataFilter, replace) {
            dataFilter || (dataFilter = {});
            replace || (replace = false);

            // if data replaced to override dataFilter
            if(replace) {
                _.extend(this.parameters.dataFilter, _.omit(this.parameters.dataFilter, function (value, key, object) {
                    if( !_.findKey(dataFilter, key) ) {
                        delete object[key];
                    }
                    return !_.findKey(dataFilter, key);
                }), dataFilter);
            }else{
                _.extend(this.parameters.dataFilter, dataFilter);
            }

            var passedPage = parseInt(this.parameters.dataFilter.page),
                firstPage = this.collection.state.firstPage,
                pageSize = this.collection.state.pageSize;

            // Calculate passed page from query params
            passedPage = !_.isNaN(passedPage) ? (firstPage && passedPage) || passedPage - 1 : firstPage;

            this.collection.getPage(passedPage, {
                reset: true,
                data: _.clone(this.parameters.dataFilter)
            });
        },

        /**
        * Fires before of run render function
        */
        beforeRender: function () {
            //
        },

        /**
        * Fires before of run render function
        */
        afterRender: function () {

            this.ready();
        },

        /**
        * fires libraries js
        */
        ready: function () {

            var _this = this;

            // reload plugins
            if(typeof window.initComponent.select2Trigger == 'function' ){
                window.initComponent.select2Trigger();
            }

            if( typeof(window.initComponent.datepickerTrigger) == 'function'){
                window.initComponent.datepickerTrigger();
            }

            if( typeof window.initComponent.configEllipsis == 'function' ){
                window.initComponent.configEllipsis();
            }
        },

        /**
		* Render view comment by model
		* @param Object programModel Model instance
		*/
		addOne: function (programModel) {

		   var view = new app.ProgramItemView({
				model: programModel,
				parameters: {
					dataFilter: this.parameters.dataFilter
				}
			});

			this.$postsWrap.append( view.render().el );

            if( (this.collection.indexOf(programModel) + 1) >= this.collection.length ) {
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
        loadSpinner: function () {
            window.Misc.setSpinner( this.$postsWrap );
        },

        /**
		* response of the server
		*/
		responseServer: function ( target, resp, opts ) {

			window.Misc.removeSpinner({
				'wrap': this.$postsWrap,
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

			if( !(this.collection instanceof Backbone.PageableCollection) )
				return;

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

})(jQuery, this, this.document);
