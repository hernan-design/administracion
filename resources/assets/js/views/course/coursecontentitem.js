/**
* Class CourseContentItemView extends of Backbone View
* @author TuProyecto || Desarrollador : @dortegon
* @link http://TuProyecto.com
*/

//Global App Backbone
app || (app = {});

(function ($, window, document, undefined) {

    app.CourseContentItemView = Backbone.View.extend({
        tagName: 'li',
        className: 'dd-item dd3-item',
        templateCourseContent: _.template( ($('#item-attachment-theme-tpl').html() || '') ),
        templateConfirm: _.template( ($('#course-attachment-delete-tpl').html() || '') ),
        events: {
            'click .edit-course-content': 'onEdit',
            'click .active-course-content-theme': 'onModalConfirm',
            'click .inactive-course-content-theme': 'onModalConfirm',
        },
		parameters: {
		   template: 'templateCourseContent'
		},
        /**
        * Constructor Method
        */
        initialize: function (opts) {
            // extends parameters
            if( opts !== undefined && _.isObject(opts.parameters) ) {

                this.parameters = _.extend({}, this.parameters, opts.parameters);
            }

            this.$el.data('codigo_contenido_curso', this.model.get('codigo_contenido_curso'));
            this.$el.data('orden_contenido_curso', this.model.get('orden_contenido_curso'));

            // Events Listener
			this.listenTo( this.model, "change", this.render );
        },

        /*
        * Render View Element
        */
        render: function () {
            var attributes = this.model.toJSON();
			this.$el.html( this[this.parameters.template](attributes) );

            return this;
        },

        /*
        * Modal edit content
        */
        onEdit: function (e) {
            e.preventDefault();

            this.sendCourseContent = new app.CourseContentWindow({
                model: this.model,
                parameters: {
                    data:{
                        codigo_contenido_curso: this.model.get('codigo_contenido_curso'),
                        codigo_curso: this.parameters.codigo_curso,
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

                                var config = {
                                    'text': text,
                                    'type': type
                                }
                                _.extend(_this.configAlert, config);

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
    });

})(jQuery, this, this.document);
