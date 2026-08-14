/**
* Class FilterData extends of Backbone View
* @author TuProyecto || Desarrollador : @krobing
* @link http://TuProyecto.com
*/

//Global App Backbone
app || (app = {});

(function ($, window, document, undefined) {

	app.FilterData = Backbone.View.extend({

		className: 'block-filters-choice',
		events: {
			'change select.change-choice': 'onChoice',
			'change .check-filter': 'onChecked',
			'keydown .keyup-search': 'onSearchName',
			'click .clean-filters': 'onCleanFilters',
            'click .btn-search': 'onSearchName'
		},
		parameters: {
			nameForm: '',
			dataFilter: {},
			callBackFilter: null
		},
		searching: false,
		oldXhr: false,

		/**
		* Constructor Method
		*/
		initialize: function(opts) {

			if(opts !== undefined && _.isObject(opts.parameters))
				this.parameters = _.extend({}, this.parameters, opts.parameters);

			//Init Attributes
			this.$formFilter = this.$(this.parameters.nameForm);

			// delegate events
			this.events['submit '+ this.parameters.nameForm] = 'onSubmitFilter';
			this.undelegateEvents();
			this.delegateEvents(this.events);

			// Events Listener
			// this.listenTo( this.collection, 'add', this.addOne );
			this.listenTo( this.collection, 'reset', this.render );
			this.listenTo( this.collection, 'request', this.requestServer );
			this.listenTo( this.collection, 'sync', this.responseServer );

			this.render();
            this.fillFields();
		},

		/*
		* Render View Element
		*/
		render: function () {

			return this;
		},

        fillFields: function () {
            _.each(this.parameters.dataFilter, function (value, name) {
                this.$formFilter.find('[name="'+ name +'"]').val(value);
            }.bind(this));
        },

		/**
		* request server
		*/
		requestServer: function (target, xhr, opts) {

			if(this.oldXhr) this.oldXhr.abort();
			delete this.oldXhr;

			this.oldXhr = xhr;
		},

		// response server
		responseServer: function (target, resp, opts) {

			this.$(this.parameters.nameForm).find(':submit').removeClass('disabled');
		},

		/**
		* on submit form
		*/
		onSubmitFilter: function (e) {
			e.preventDefault();

			var $formFilter = $(e.target);

			this.filter( $formFilter );
		},

		/**
		* change options on selects
		*/
		onChoice: function (e) {

		   var $formFilter = $(e.currentTarget).closest('form');

		   this.filter( $formFilter );
		},

		/**
		* change checked option filters
		*/
		onChecked: function (e) {

			var $formFilter = $(e.currentTarget).closest('form'),
				$check = $(e.target),
				replaces = {};

			replaces[ $check.attr('name') ] = $check.prop('checked');

			this.filter( $formFilter, replaces);
		},

		/**
		* with each key up filter
		*/
		onSearchName: function (e) {

			var $formFilter = $(e.currentTarget).closest('form');

			this.searchTimeout( $formFilter );
		},

		/**
		* clean data filters
		*/
		onCleanFilters: function (e){
			e.preventDefault();
			var $formFilter = $(e.currentTarget).closest('form');

			// reset fields
			this.resetFields($formFilter);

			this.filter( $formFilter, 'reset' );
		},

		/**
		* filter form data
		* @param Object|jQuery $formFilter
		*/
		filter: function ($formFilter, replaces) {

			replaces || (replaces = {});

			var _this = this,
				data = window.Misc.formToJson( $formFilter );

			// replace data attributes
			if( _.isObject(replaces) ) {
				_.each(replaces, function (value, key) {
					if( value == false ) data[ key ] = '';
				});
			}else if( replaces.toLowerCase() == 'reset' ) {
				this.parameters.dataFilter = {};
			}

			// extend dataFilter
			_.extend(this.parameters.dataFilter, data);

			// delete form token
			if( ({}).hasOwnProperty.call(this.parameters.dataFilter, '_token') ) {
				delete this.parameters.dataFilter['_token'];
			}

			// detele empty params
			_.each(_this.parameters.dataFilter, function (value, key) {

				if( value == '' || value === null || value === undefined ) {
					delete _this.parameters.dataFilter[key];
				}
			});

			$formFilter.find(':submit').addClass('disabled');

			if( typeof this.parameters.callBackFilter == 'function' ) {
				this.parameters.callBackFilter.call(null, this.parameters.dataFilter );

			}else {
				if( Backbone.PageableCollection !== undefined && this.collection instanceof Backbone.PageableCollection ) {
					this.collection.getFirstPage( {reset:true, data:this.parameters.dataFilter} );
					return;
				}

				this.collection.fetch(this.parameters.dataFilter, {reset:true, wait:true});
			}
		},

		/**
		* searching in a time established
		* @param $formFilter Element
		* @param replace Object
		*/
		searchTimeout: function ($formFilter, replaces) {

			window.clearTimeout( this.searching );
			delete this.searching;

			this.searching = window.setTimeout(function () {

	   			this.filter( $formFilter, replaces);

			}.bind(this), 500);
		},

		/**
		* reset form fields
		* @param parentForm Element
		*/
		resetFields: function (parentForm) {
			// reset inputs
			var $resetedInputs = $(':input', parentForm).not(':button, :submit, :reset, :hidden, :checkbox, :radio, .select-dropdown').val('');

			// reset selects
			parentForm[0].reset();
			$(parentForm).find('select').trigger('change.select2')
										.prop('selectedIndex', 0);
		}
	});

})(jQuery, this, this.document);
