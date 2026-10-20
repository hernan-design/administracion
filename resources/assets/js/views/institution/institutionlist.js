/**
* Class ProgramListView extends of Backbone View
* @author TuProyecto || Desarrollador : @dortegon
* @link http://TuProyecto.com
*/

//Global App Backbone
app || (app = {});

(function ($, window, document, undefined) {

    app.InstitutionListView = Backbone.View.extend({

        el: '#institution-content',
        templateButtonsAction: _.template( ($('#buttons-action-tpl').html() || '') ),
        events: {
            'click .btn-search': 'onSearch',
            'click .btn-clear': 'onClear'
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

        },

        render: function () {
            var _this = this;
            
            // Reference Elements
            this.$institutionsListSearchTable = this.$('#institution-search-table');
            this.$institutionName = this.$('#nombre_institucion');
            this.$institutionState = this.$('#estado_institucion');

            this.renderInsitutionsTable();

            this.ready();
        },

        renderInsitutionsTable: function() {
            var _this = this;
            
            this.institutionsListSearchTable = this.$institutionsListSearchTable.DataTable({
                dom: "<'row'<'col-sm-5'l><'col-sm-7'p>>" +
                "<'row'<'col-sm-12'tr>>" + "<'row'<'col-sm-11'i><'col-sm-1'>>",
                "lengthMenu": [[15, 25, 50, 100], [15, 25, 50, 100]],
                processing: true,
                serverSide: true,
                language: window.Misc.dataTableES(),
                searching: true,
                ajax: {
                    url: window.Misc.urlFull( Route.route('institucion.index') ),
                    data: function( data ) {
                        data.institution_name =  _this.$institutionName.val();
                        data.institution_state =  _this.$institutionState.val();
                    }
                },
                columns: [
                    { data: 'codigo_institucion',     name: 'codigo_institucion'    },
                    { data: 'nombre_institucion',     name: 'nombre_institucion'    },
                    { data: 'codigo_estado',          name: 'codigo_estado'         },
                ],
                order: [
                    [ 2, 'asc' ]
                ],
                columnDefs: [
                    {
                        targets: 3,
                        orderable: false,
                        className: 'text-center',
                        render: function ( data, type, full, row ) {
                            return _this.templateButtonsAction( {full: full} );
                        }
                    },
                ],
                fnRowCallback: function( row, data ) {
                }
            });        
        },

        onSearch: function(e) {
            e.preventDefault();

            this.institutionsListSearchTable.ajax.reload();
        },

        onClear: function(e) {
            e.preventDefault();

            this.$institutionName.val('');
            this.$institutionState.val('');
            this.institutionsListSearchTable.ajax.reload();
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

            if( typeof(window.initComponent.autosizeTrigger) == 'function')
                window.initComponent.autosizeTrigger();

            if( typeof window.initComponent.icheckTrigger == 'function' ){
                window.initComponent.icheckTrigger();
            }

            if(typeof window.initComponent.multiselectTrigger == 'function' ){
                window.initComponent.multiselectTrigger();
            }
            // input mask
            this.$(":input").inputmask();
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

})(window.jQuery, window, document);
