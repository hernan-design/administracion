/**
* Class ForumCategoriesListView extends of Backbone View
* @author TuProyecto || Desarrollador : @dortegon
* @link http://TuProyecto.com
*/

//Global App Backbone
app || (app = {});

(function ($, window, document, undefined) {

    app.ForumCategoriesListView = Backbone.View.extend({
        el: '#forum-category-content',
        tagName: 'table',
        templateResultCount: _.template( ($('#browse-result-count-tpl').html() || '') ),
        events: {
            'click .btn-create': 'onCreate',
            'click .btn-add': 'onAddCategory',
            'click .cancel-action': 'onModalDestroy'
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
            this.$formFilter    = this.$('#forum-category');
            this.$wrapPagination    = this.$('.wrap-pagination-category');
            this.$blockFiltersMenu  = this.$('#forum-category-filter-content');
            this.$wrapperAssignmentCategory = this.$('#wrapper-assignment-category');

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
                            _this.getData(_.extend( _.pick(newDataFilter, 'forum_category_name') ), true);
                        },
                        'dataFilter': _.extend( _this.parameters.dataFilter )
                    }
                });
            });

            // Events Listener
            this.listenTo( this.collection, 'add', this.addOne );
            this.listenTo( this.collection, 'reset', this.addAll );
            this.listenTo( this.collection, 'request', this.loadSpinner );
            this.listenTo( this.collection, 'sync', this.responseServer );

             // Events Listener custome
            this.on( 'beforeRender', this.beforeRender );
            this.on( 'afterRender', this.afterRender );

            this.on('getdata', this.getData);
        },

        render: function () {
            //
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
        * @param Object forumCategory Model instance
        */
        addOne: function (forumCategory, collection, opts) {
            var view = new app.ForumCategoriesItemView({
                model: forumCategory
            });

            this.$el.find('tbody').append( view.render().el );
            this.ready();
        },

        /**
        * Render all view jury of the collection
        */
        addAll: function () {

            this.$el.find('tbody').html('');
            this.collection.forEach( this.addOne, this );
        },

        /**
        * Create item and send a request to the server
        */
        onCreate: function (e) {
            e.preventDefault();
            e.stopPropagation();

            // Init model
            this.forumCategory = new app.ForumCategoryModel();

            this.listenToOnce( this.forumCategory, 'change:codigo_categoria_foro', function (model) {
                this.collection.add(model);
            });

            var createFormWindow = new app.FormSampleWindow({
                    model: this.forumCategory,
                    parameters: {
                        template: _.template( ($('#create-edit-forum-category-tpl').html() || '') ),
                        title: 'Crear categoría'
                    }
                });

            createFormWindow.render();
        },

        // Add categories
        onAddCategory: function (e) {
            e.preventDefault();

            var _this = this,
                data = window.Misc.formToJson(this.$formFilter),
				newData = _.pick(data, 'codigo_categoria_foro');

            _.extend(newData, {edit: false});

            if(!_.isUndefined(newData.codigo_categoria_foro)){
                this.$('#codigo_categoria_foro').select2('val', '');
                var model = this.collection.find(function (model) {
                    return model.get('codigo_categoria_foro') == newData.codigo_categoria_foro;
                });

                if(_.isUndefined(model)){
                    this.collection.create(newData, {wait:true,
                        success: function (model, resp) {
                            // complete proccess
                            window.Misc.removeSpinner({
                                'wrap': _this.$el,
                                'direct': false
                            });

                            // response success or error
                            var type = resp.success ? 'success' : 'alert',
                                text = resp.success ?
                                    (resp.message !== undefined ? resp.message : '...')
                                    : resp.errors;

                            if( _.isObject( resp.errors ) ){

                                var listError = '<ul>';

                                $.each(resp.errors, function(field, item) {
                                    listError += '<li>'+ item[0] +'</li>';
                                });
                                listError += '</ul>';

                                text = listError;
                            }

                            var config = {
                                'text': text,
                                'type': type
                            }
                            confCrop =  $.extend({}, _this.configAlert, config);

                            //complete proccess
                            if( resp.success !== undefined ){
                                if( resp.success ){
                                    window.Misc.showAlertBox( confCrop );
                                }else{
                                    window.Misc.showAlertBox( confCrop );
                                }
                            }
                        }

                    });
                }else{
                    var conf = {
                        'text': 'La categoría ya se encuentra asignada, verifique',
                        'type': 'alert'
                    };
                    _.extend(this.configAlert, conf);
                    window.Misc.showAlertBox(this.configAlert);
                }

            }else{
                var conf = {
                    'text': 'Debe seleccionar una categoría',
                    'type': 'alert'
                };
                _.extend(this.configAlert, conf);
                window.Misc.showAlertBox(this.configAlert);
            }
        },

        /**
        * Load spinner on the request
        */
        loadSpinner: function ( target, xhr, opts ) {

            if( target instanceof Backbone.Collection ){
                window.Misc.setSpinner( this.$wrapperAssignmentCategory );
            }
        },

        /**
        * response of the server
        */
        responseServer: function ( target, resp, opts ) {

            // remove spinner
            window.Misc.removeSpinner({
                'wrap': this.$wrapperAssignmentCategory,
                'direct': false
            });

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

			this.$('.wrap-result-count-category').html(this.templateResultCount({
				'from': fromResult,
				'to': toResult > this.collection.state.totalRecords ? this.collection.state.totalRecords : toResult,
				'totalRecords': this.collection.state.totalRecords
			}));
		}
   });

})(jQuery, this, this.document);
