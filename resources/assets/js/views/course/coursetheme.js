/**
* Class CourseThemeView extends of Backbone View
* @author TuProyecto || Desarrolladora : @dortegon
* @link http://TuProyecto.com
*/

//Global App Backbone
app || (app = {});

(function ($, window, document, undefined) {

    app.CourseThemeView = Backbone.View.extend({
        template: _.template( ($('#item-theme-course-tpl').html() || '') ),
        events: {
            'submit #form-add-themes-course' : 'onAddTheme'
        },
        parameters: {
            dataFilter: {},
            totalthemes: 0
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

            // Reference to elements
            this.$wrapperThemes = this.$('#accordion');

            // Events Listener
            this.listenTo( this.collection, 'change', this.render );
			this.listenTo( this.collection, 'add', this.addOne );
			this.listenTo( this.collection, 'reset', this.addAll );
			this.listenTo( this.collection, 'sync', this.responseServer);
            this.listenTo( this.collection, 'request', this.loadSpinner );

            // Events Listener custome
            this.on( 'beforeRender', this.beforeRender );
            this.on( 'afterRender', this.afterRender );

        },

        /*
        * Render View Element
        */
        render: function() {
            this.$formAddThemes = this.$('#form-add-themes-course');
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
		* Render view comment by model
		* @param Object courseTheme Model instance
		*/
		addOne: function (courseTheme) {
            var view = new app.CourseThemeListView({
				model: courseTheme,
				parameters: _.extend(this.parameters, {
                    template: 'templateThemeCourse',
                    totalthemes: this.parameters.totalthemes
				})
			});

			this.$wrapperThemes.append( view.render().el );

            this.resetFields( this.$formAddThemes.find('[name="fields_themes_course"]') );
		},

        /**
		* Render all view Course of the collection
		*/
		addAll: function () {
            this.parameters.totalthemes = this.collection.length;
			this.$wrapperThemes.html('');
			this.collection.forEach( this.addOne, this );
		},

        /**
		* add new development activity
		*/
		onAddTheme: function (e) {
			e.preventDefault();

			var _this = this,
                data = window.Misc.formToJson(this.$formAddThemes),
				newData = _.pick(data, 'nombre_tema_curso', 'objetivo_tema_curso');
			newData['codigo_curso'] = this.model.get('codigo_curso');

            this.collection.create(newData, {success: function(model, resp, opts){

                window.Misc.removeSpinner({
                    'wrap': this.$el,
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
                        window.Misc.showAlertBox( _.extend({title:'Mensaje'}, _this.configAlert) );
                    }else{
                        window.Misc.showAlertBox( _.extend({title:'Información'}, _this.configAlert) );
                    }
                }
            }.bind(this)});
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

})(jQuery, this, this.document);
