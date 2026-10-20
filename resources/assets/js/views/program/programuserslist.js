/**
* Class ProgramUsersListView extends of Backbone View
* @author TuProyecto || Desarrollador : @dortegon
* @link http://TuProyecto.com
*/

//Global App Backbone
app || (app = {});

(function ($, window, document, undefined) {

    app.ProgramUsersListView = Backbone.View.extend({
        templateResultCount: _.template( ($('#browse-result-count-tpl').html() || '') ),
        templateUsersSearch: _.template( ($('#search-users-toggle-tpl').html() || '') ),
        events: {
            'click .btn-add': 'onUserSearchToggle',
            'submit #form-user-program': 'onAddUser',
			'click .btn-create': 'onModalUserCreate'
        },
        parameters: {
            data: {},
			dataFilter: {}
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

            //Init Attribute
            this.configAlert = {
                'wrapper': '#main-wrap',
                'closeTime': 6000,
                'speedOpen': 500,
                'speedClose': 500
            };

			this.usuarioModel = new app.UserModel();

            // initialize reference to elements
            this.$modalComponent = $('#modal-base-create');
            this.$formFilter    = this.$('#program-user-filter');
            this.$usersSearch   = this.$('#search-users-toggle');
            this.$wrapPagination    = this.$('.wrap-pagination');
            this.$postsWrap     = this.$('#list-content-article');
            this.$blockFiltersMenu  = this.$('#program-user-filter-content');
            this.$noResults     = this.$('#article-list > .no-results-filter');
            
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
                            _this.getData(_.extend(newDataFilter, _this.parameters.data ), true);
                        },
                        'dataFilter': _.extend({
                            program: _this.parameters.program
                        }, _this.parameters.dataFilter)
                    }
                });
            });

            // Events Listeners
			this.listenTo( this.collection, 'add', this.addOne );
            this.listenTo( this.collection, 'reset', this.addAll );
			this.listenTo( this.collection, 'request', this.loadSpinner);
			this.listenTo( this.collection, 'sync', this.responseServer);
			this.listenTo( this.usuarioModel, 'request', this.loadSpinner);
			this.listenTo( this.usuarioModel, 'sync', this.responseServer);
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
		* fires libraries js and functions
		*/
        ready: function () {
            if( typeof window.initComponent.configEllipsis == 'function' ){
                window.initComponent.configEllipsis();
            }
        },

        /**
		* Render view comment by model
		* @param Object userProgram Model instance
		*/
		addOne: function (userProgram) {
            
            var view = new app.ProgramUserItemView({
                model: userProgram,
                parameters: {
                    dataFilter: this.parameters.dataFilter
                }
            });

            this.$postsWrap.append( view.render().el );

            if( (this.collection.indexOf(userProgram) + 1) >= this.collection.length ) {
                this.ready();
            }
		},

		/**
		* Render all view Course of the collection
		*/
		addAll: function () {
            if(!this.collection.length) {
                this.$noResults.show();
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
                'wrap': this.$postsWrap,
				'direct': false
            });
                
            if(resp.add_register){        
                this.$modalComponent.modal('hide');

                resp.program = this.model.get('codigo_convocatoria');        
                this.addUser(resp)
            }

			// set result count
			this.resultCount(this.collection.state);
		},

        // Slide compenent search user
        onUserSearchToggle: function (e) {
            e.preventDefault();

            // Render template in mode hide
            this.$usersSearch.html( this.templateUsersSearch() ).hide();
            this.$usersSearch.slideToggle('slow');
        },

        // Add user collection
        onAddUser: function (e) {
            e.preventDefault();
            e.stopPropagation();

            var data = window.Misc.formToJson( e.target );
            
            if(_.isEmpty(data.correo_electronico)){
                var config = {
                    'text': 'Debe seleccionar un usuario, verifique.',
                    'type': 'alert'
                }
                _.extend(this.configAlert, config);
                window.Misc.showAlertBox( this.configAlert );
                return;
            }

            data.program = this.model.get('codigo_convocatoria');

            this.addUser(data);
        },

        // Add user to collection
        addUser: function (data) {
            var _this = this;

            window.Misc.setSpinner( this.$postsWrap );

            this.collection.create(data, {wait: true, reset: true,
                success: function(model, resp, opts){

                window.Misc.removeSpinner({
                    'wrap': this.$postsWrap,
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
                _.extend(_this.configAlert, config);

                //complete proccess
                if( resp.success !== undefined ){
                    if( resp.success ){
                        window.Misc.showAlertBox( _this.configAlert );
                        this.$('#correo_electronico_filter_data').val('');
                        this.$('#nombre_usuario_filter_data').val('');
                    }else{
                        window.Misc.showAlertBox( _this.configAlert );
                    }
                }
            }.bind(this)});
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
		},
        
        /**
         * 
         * Create modal 
         */
        onModalUserCreate: function(e) {
            e.preventDefault();
            			
			if ( this.userCreateEditView instanceof Backbone.View ){
                this.userCreateEditView.stopListening();
                this.userCreateEditView.undelegateEvents();
            }

            // Render template
			this.$modalComponent.find('.modal-title').html( $(e.target).attr('data-title') );
			
			this.userCreateEditView = new app.ComponentCreateUserView({
				el: this.$modalComponent.find('.content-modal'),
                model: this.usuarioModel,
                parameters: {
                    view: true
                }
			});

            this.$modalComponent.find('.content-modal').html( this.userCreateEditView.render() );
			this.$modalComponent.modal('show');
		}
    });

})(window.jQuery, window, document);
