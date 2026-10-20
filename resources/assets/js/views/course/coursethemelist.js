/**
* Class CourseThemeListView extends of Backbone View
* @author TuProyecto || Desarrollador : @dortegon
* @link http://TuProyecto.com
*/

//Global App Backbone
app || (app = {});

(function ($, window, document, undefined) {

	app.CourseThemeListView = Backbone.View.extend({
        templateThemeCourse: _.template( ($('#item-theme-course-tpl').html() || '') ),
        templateConfirm: _.template( ($('#course-theme-delete-tpl').html() || '') ),
		events: {
            'change .dd': 'onGetNestable',
            'click .active-theme': 'onModalConfirm',
            'click .inactive-theme': 'onModalConfirm',
            'click .edit-course-theme': 'onEdit',
            'click .attachment-accordion': 'onGetAttachment',
            'click .add-attachment-theme': 'onModalAttachment'
		},
		parameters: {
		   template: 'templateThemeCourse'
		},

		/**
		* Constructor Method
		*/
		initialize : function(opts) {

			// extend parameters
			if(!_.isUndefined(opts) && _.isObject(opts.parameters))
				this.parameters = $.extend({}, this.parameters, opts.parameters);

            // Collection attachments
            this.courseContentList = new app.CourseContentList();
            this.courseContentList.comparator = 'orden_contenido_curso';

			// Events Listener
			this.listenTo( this.model, 'change', this.render );
			this.listenTo( this.courseContentList, 'add', this.addCourseContent );
			this.listenTo( this.courseContentList, 'reset', this.addCourseContents );
            this.listenTo( this.courseContentList, 'request', this.loadSpinner );
            this.listenTo( this.courseContentList, 'sync', this.responseServer );
		},

		// view render
		render : function () {
            var attributes = this.model.toJSON();
			this.$el.html( this[this.parameters.template](attributes) );

            // Reference to elements
            this.$wrapperContent = this.$('#nestable');

			return this;
		},

        /**
		* fires libraries js and functions
		*/
        ready: function () {
            if( typeof window.initComponent.configNestable == 'function' ){
                window.initComponent.configNestable();
            }

            if( typeof window.initComponent.configEllipsis == 'function' ){
                window.initComponent.configEllipsis();
            }
        },

        /**
        * Change position
        */
        onGetNestable: function (e) {
            var _this = this;
            var sortedItems = $(e.target).nestable('serialize');

            _.each(sortedItems, function (sortItem, indx) {
                sortItem.orden_contenido_curso = indx + 1;
            });

            $.post( window.Misc.urlFull( Route.route('curso.tema.contenido.orden') ), {
                '_token': $('meta[name=csrf-token]').attr('content'),
                data: sortedItems
            }).success( function (resp){

                window.Misc.removeSpinner({
                    wrap: _this.$el,
                    direct: false
                });

                //complete proccess
                if( resp.success !== undefined ) {
                    if( resp.success ){
                        window.Misc.showAlertBox({
                            text: resp.message,
                            type: 'success',
                            closeTime: 5000
                        });

                        // Sort new collection
                        _this.courseContentList.set(sortedItems, {merge:true, add:false, remove:false});
                    }else {
                        window.Misc.showAlertBox({
                            text: resp.errors,
                            type: 'alert',
                            closeTime: 5000
                        });

                        // Sort initial collection
                        _this.addCourseContents();
                    }
                }
            });

        },

        /**
		* Render view comment by model
		* @param Object coursecontent Model instance
		*/
		addCourseContent: function (coursecontent) {
            var view = new app.CourseContentItemView({
				model: coursecontent,
				parameters: {
					template: 'templateCourseContent',
                    codigo_curso:this.model.get('codigo_curso')
				}
			});

			this.$wrapperContent.find('ol').append( view.render().el );
            this.ready();
		},

        /**
		* Render all view Course of the collection
		*/
		addCourseContents: function () {
			this.$wrapperContent.find('ol').html('');
			this.courseContentList.forEach( this.addCourseContent, this );
		},

        /**
        * Load spinner on the request
        */
        loadSpinner: function () {
            window.Misc.setSpinner( this.$el );
        },

        /**
        * response of the server
        */
        responseServer: function ( model, resp, opts ) {
            window.Misc.removeSpinner({
                wrap: this.$el,
                direct: false
            });
        },

        /*
        * Modal edit content
        */
       onEdit: function (e) {
        e.preventDefault();

        console.log("Edit THEME!", this.model.toJSON());

        this.editCourseTheme = new app.CourseThemeWindow({
            model: this.model,
            parameters: {
                data:{
                    //
                },
                totalthemes: this.parameters.totalthemes
            }
        });

        this.editCourseTheme.render();
    },

        /**
        * Modal course content
        */
		onModalAttachment: function (e) {
			e.preventDefault();

            // Course content model
            this.courseContentModel = new app.CourseContentModel();
            this.listenToOnce( this.courseContentModel, 'change:codigo_contenido_curso', function (model) {
                this.courseContentList.add(model);
            });

            this.sendCourseContent = new app.CourseContentWindow({
                model: this.courseContentModel,
                parameters: {
                    data:{
                        codigo_curso: this.model.get('codigo_curso'),
                        codigo_tema_curso: this.model.get('codigo_tema_curso')
                    }
                }
            });

            this.sendCourseContent.render();
		},

        /*
        * Modal Confirm
        */
        onModalConfirm: function (e) {
            e.preventDefault();
            e.stopPropagation();

            var _this = this,
                data = {
                    'change_state': true
                };

            if( this.acceptConfirm instanceof Backbone.View ) {
                this.acceptConfirm.stopListening();
                this.acceptConfirm.undelegateEvents();
            }

            this.acceptConfirm = new app.ConfirmWindow({
                model: this.model,
                parameters: {
                    template: this.templateConfirm,
                    titleConfirm: "Confirmación",
                    onConfirm: function () {
                        this.model.save(data, {wait: true,
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

                                //complete proccess
                                if( resp.success !== undefined ){
                                    if( resp.success ){
                                        window.Misc.showAlertBox({
                                            text: resp.message,
                                            type: 'success',
                                            closeTime: 5000
                                        });
                                    }else{
                                        window.Misc.showAlertBox({
                                            text: resp.error,
                                            type: 'alert',
                                            closeTime: 5000
                                        });
                                    }
                                }
                            }
                        });
                    }
                }
            });

            this.acceptConfirm.render();
        },

        /*
        * Get collection from theme
        */
        onGetAttachment: function (e){
            e.preventDefault();

            var ariaExpanded = $(e.currentTarget).attr('aria-expanded');

            if( ariaExpanded == 'false' ){
                this.courseContentList.fetch({
                    data: {themeCourse: this.model.get('codigo_tema_curso')},
                    reset: true
                });
            }
        }

	});

})(window.jQuery, window, document);
