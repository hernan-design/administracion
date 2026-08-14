/**
* Class ChannelProfessionsListView extends of Backbone View
* @author TuProyecto || Desarrollador : @dortegon
* @link http://TuProyecto.com
*/

//Global App Backbone
app || (app = {});

(function ($, window, document, undefined) {

    app.ChannelProfessionsListView = Backbone.View.extend({
        tagName: 'table',
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
        initialize : function(opts){

            // Extend options parameters
            if( opts !== undefined && _.isObject(opts.parameters) ){
                this.parameters = _.extend({}, this.parameters, opts.parameters);
            }

            // Initialize attributes
            this.configAlert = {
                'wrapper': '#main-wrap',
                'closeTime': 6000,
                'speedOpen': 500,
                'speedClose': 500
            };

            var _this = this;

            // initialize reference to elements
            this.$formFilter    = this.$('#channel-profession-filter');
            this.$noResults     = this.$('#article-list > .no-results-filter');
            this.$wrapPagination    = this.$('.wrap-pagination-professions');
            this.$blockFiltersMenu  = this.$('#channel-profession-filter-content');

			// set paginations
			this.$wrapPagination.each(function(index, el) {

				var pagination = new app.Paginator({
					className: 'right pagination tablet-text-right',
					collection: _this.collection,
					parameters: {
						'nameForm': this.$formFilter,
						'data': _.extend(_this.parameters.dataFilter, _this.parameters.data)
					}
				});

				$(el).html( pagination.render().el );

			}.bind(this));

            // set block filter
            this.$blockFiltersMenu.each(function(index, el) {
                var blockFilterMenu = new app.FilterData({
                    el: el,
                    collection: _this.collection,
                    parameters: {
                        'nameForm': _this.$formFilter,
                        'callBackFilter': function (newDataFilter) {
                            _this.getData(_.extend(newDataFilter, _this.parameters.data ? { channel: _this.parameters.channel, resource: window._tp.getResource['professions'] } : _this.parameters.data ), true);
                        },
                        'dataFilter': _.extend({
                            channel: _this.parameters.channel
                        }, _this.parameters.dataFilter)
                    }
                });
            });

            // Events Listener
            this.listenTo( this.collection, 'add', this.addOne );
            this.listenTo( this.collection, 'reset', this.addAll );
            this.listenTo( this.collection, 'request', this.loadSpinner );
            this.listenTo( this.collection, 'sync', this.responseServer );

            // custom events listener
            this.listenTo( this.collection, 'store', this.storeOne );

             // Events Listener custome
            this.on( 'beforeRender', this.beforeRender );
            this.on( 'afterRender', this.afterRender );

            this.on('getdata', this.getData);
        },

        /**
        * get reset data
        *
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
        * fires libraries js
        */
        ready: function () {

            // reload plugins
        },

        /**
        * Render view comment by model
        * @param Object linkChannelModel Model instance
        */
        addOne: function (linkChannelModel, collection, opts) {
            if( _.isUndefined(linkChannelModel.id) ){
                return;
            }

            var view = new app.ChannelProfessionItemView({
                model: linkChannelModel
            });

            this.$el.find('tbody').append( view.render().el );
            this.ready();
        },

        /**
        * Render all view jury of the collection
        */
        addAll: function () {
            if(!this.collection.length) {
                this.$noResults.show();
            }else {
                this.$noResults.hide();
            }

            this.$el.find('tbody').html('');
            this.collection.forEach( this.addOne, this );
        },

        /**
        * stores a assignment jury
        * @param form element
        */
        storeOne: function ( data ) {

            data || (data = {});
            this.collection.create(data, {patch:true, wait:true});
        },

        /**
        * Load spinner on the request
        */
        loadSpinner: function ( target, xhr, opts ) {

            this.$noResults.css('display', 'none');

            if( target instanceof Backbone.Collection )
                window.Misc.setSpinner( this.$el );
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

            // response success or error
            var type = resp.success ? 'success' : 'alert',
                text = resp.success ?
                        (resp.message !== undefined ? resp.message : '...')
                            : resp.errors;

            if( _.isObject( resp.errors ) ) {

                var listError = '<ul>';

                $.each(resp.errors, function(field, item) {
                     listError += '<li>'+ item[0] +'</li>';
                });
                listError += '</ul>';

                text = listError;
            }

            var conf = {
                'text': text,
                'type': type
            };
            _.extend(this.configAlert, conf);

            // remove spinner
            window.Misc.removeSpinner({
                'wrap': this.$el,
                'direct': false
            });

            //complete proccess
            if( resp.success !== undefined ) {
                if( resp.success ){

                    window.Misc.showAlertBox(this.configAlert);

                }else {
                    window.Misc.showAlertBox(this.configAlert);
                    return false;
                }
            }

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

			this.$('.wrap-result-count-professions').html(this.templateResultCount({
				'from': fromResult,
				'to': toResult > this.collection.state.totalRecords ? this.collection.state.totalRecords : toResult,
				'totalRecords': this.collection.state.totalRecords
			}));
		}
   });

})(jQuery, this, this.document);
