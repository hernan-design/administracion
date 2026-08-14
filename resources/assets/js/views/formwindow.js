/**
* Class FormVersionsWindow extends of Backbone View
* @author TuProyecto || Desarrollador : @dortegon
* @link http://TuProyecto.com
*/

//Global App Backbone
app || (app = {});

(function ($, window, document, undefined) {

    app.FormWindow = Backbone.View.extend({
        el: '#modal-form',
        template: _.template( ($('#form-modal-group-tpl').html() || '') ),
        templateConfirmRemove: _.template( ($('#confirmation-remove-group-tpl').html() || '') ),
        events: {
            'submit #form-modal-all': 'onSubmit',
            'click .cancel-action': 'onModalDestroy'
		},
        parameters: {
            data: {},
            title: '',
            dataFilter: {},
            groups: []
        },

        /**
        * Constructor Method
        */
        initialize : function(opts) {

            _.bindAll(this, 'render');

            // extends attributtes
            if( opts !== undefined && _.isObject(opts.parameters) ){
                this.parameters = _.extend({}, this.parameters, opts.parameters );
            }

            // Init attributes
            this.$formModal = this.$('#form-modal-all');

            // Init Attributes
            this.configAlert = {
                'wrapper': '#main-wrap',
                'closeTime': 6000,
                'speedOpen': 500,
                'speedClose': 500
            };

            // Collections

            this.formQuestionGroup = new app.FormQuestionGroupsList;

            // View

            // this.formViewGroup = new app.FormGroup({
            //     el: '#pagination',
            //     tagName: 'section',
            //     model: this.model,
            //     collection: this.formQuestionGroup,
            // });
        },

        /*
        * Render View Element
        */
        render: function() {
            var remove = this.parameters.data.remove, removeQuestion = this.parameters.data.removeQuestion;
            this.$el.find('.modal-title').html(this.parameters.title);

            if (!remove && !removeQuestion) {
                this.$el.find('.content-modal').html(this.template(this.model.toJSON()));

                // render editor
                ReactDOM.render(React.createElement(SimpleEditor, {
                    content: this.model,
                    formCreate: this.$formModal,
                    nameField: "nombre_grupo_pregunta_encuesta",
                    minHeight: 50,
                    value: this.model.get('nombre_grupo_pregunta_encuesta'),
                    column: "col-md-12",
                    previewHide: true,
                    required: true
                }, null), document.getElementById('title-question-group'));

                // render editor
                ReactDOM.render(React.createElement(SimpleEditor, {
                    content: this.model,
                    formCreate: this.$formModal,
                    nameField: "desc_grupo_pregunta_encuesta",
                    value: this.model.get('desc_grupo_pregunta_encuesta'),
                    column: "col-md-12",
                    previewHide: true
                }, null), document.getElementById('description-question-group'));

            } else {
                this.$el.find('.content-modal').html(this.parameters.template);

                // Change button text to "Eliminar" specifically for delete question modal
                if (removeQuestion) {
                    this.$el.find('.confirm-action').text('Eliminar');
                }
            }
            this.$el.modal("show").removeAttr('tabindex');

            return this;
        },

        /**
        * fires libraries js
        */
        ready: function () {
            // reload plugins
            if( typeof(window.initComponent.autosizeTrigger) == 'function'){
                window.initComponent.autosizeTrigger();
            }
        },

        /*
        * Submit data
        */
        onSubmit: function (e) {
            e.preventDefault();
            window.Misc.setSpinner( this.$formModal );

            var _this = this,
                data = window.Misc.formToJson( this.$formModal ),
                group = 0, attributes = {},

            group = group + 1;
            _.extend(attributes, this.parameters.data, data);

            if (!attributes.update && !attributes.remove && !attributes.removeQuestion) {
                this.model.save(attributes, {
                    wait: true, type: 'POST', success: function (model, resp) {

                        var view = new app.FormGroup({
                           el: '#pagination',
                           tagName: 'section',
                           model: _this.model,
                           collection: _this.formQuestionGroup,
                        });

                        _this.formQuestionGroup.add(model);

                        // complete proccess

                        window.Misc.removeSpinner({
                            'wrap': _this.$formModal,
                            'direct': false
                        });

                        _this.$el.modal().off();
                        _this.$el.modal('hide');

                        // response success or error

                        var type = resp.success ? 'success' : 'alert',
                            text = resp.success ?
                                (resp.message !== undefined ? resp.message : '...')
                                : resp.errors;

                        if (_.isObject(resp.errors)) {

                            var listError = '<ul>';

                            $.each(resp.errors, function (field, item) {
                                listError += '<li>' + item[0] + '</li>';
                            });
                            listError += '</ul>';

                            text = listError;
                        }

                        var config = {
                            'text': text,
                            'type': type
                        }
                        confCrop = _.extend({}, _this.configAlert, config);

                        //complete proccess

                        if (resp.success !== undefined) {
                            if (resp.success) {
                                window.Misc.showAlertBox(confCrop);
                            } else {
                                window.Misc.showAlertBox(confCrop);
                            }
                        }
                    }
                });
            } else if (attributes.update && !attributes.remove && !attributes.removeQuestion) {
                this.model.save(attributes, {
                    wait: true, patch: true, success: function (model, resp) {

                        var view = new app.FormGroup({ el: '#pagination', collection: _this.formQuestionGroup });
                        _this.formQuestionGroup.reset(model);

                        // complete proccess

                        window.Misc.removeSpinner({
                            'wrap': _this.$el,
                            'direct': false
                        });

                        _this.$el.modal().off();
                        _this.$el.modal('hide');

                        // response success or error

                        var type = resp.success ? 'success' : 'alert',
                            text = resp.success ?
                                (resp.message !== undefined ? resp.message : '...')
                                : resp.errors;

                        if (_.isObject(resp.errors)) {

                            var listError = '<ul>';

                            $.each(resp.errors, function (field, item) {
                                listError += '<li>' + item[0] + '</li>';
                            });
                            listError += '</ul>';

                            text = listError;
                        }

                        var config = {
                            'text': text,
                            'type': type
                        }
                        confCrop = _.extend({}, _this.configAlert, config);

                        //complete proccess

                        if (resp.success !== undefined) {
                            if (resp.success) {
                                window.Misc.showAlertBox(confCrop);
                            } else {
                                window.Misc.showAlertBox(confCrop);
                            }
                        }
                    }
                });
            } else if (attributes.remove && !attributes.update && !attributes.removeQuestion) {
                this.model.set({ codigo_grupo_pregunta_encuesta: this.parameters.data.group },  {silent: true});
                this.model.destroy( { wait: true, success: function(model, resp) {

                        var view = new app.FormGroup({ el: '#pagination', collection: _this.formQuestionGroup });
                        view.onDelete(model.get('codigo_grupo_pregunta_encuesta'));

                        // complete proccess

                        window.Misc.removeSpinner({
                            'wrap': _this.$el,
                            'direct': false
                        });

                        _this.$el.modal().off();
                        _this.$el.modal('hide');

                        // response success or error

                        var type = resp.success ? 'success' : 'alert',
                            text = resp.success ?
                                (resp.message !== undefined ? resp.message : '...')
                                : resp.errors;

                        if (_.isObject(resp.errors)) {

                            var listError = '<ul>';

                            $.each(resp.errors, function (field, item) {
                                listError += '<li>' + item[0] + '</li>';
                            });
                            listError += '</ul>';

                            text = listError;
                        }

                        var config = {
                            'text': text,
                            'type': type
                        }
                        confCrop = _.extend({}, _this.configAlert, config);

                        //complete proccess

                        if (resp.success !== undefined) {
                            if (resp.success) {
                                window.Misc.showAlertBox(confCrop);
                            } else {
                                window.Misc.showAlertBox(confCrop);
                            }
                        }
                    }
                });
            } else if (!attributes.remove && !attributes.update && attributes.removeQuestion) {

                this.model.set({ codigo_pregunta_encuesta: this.parameters.data.question }, { silent: true });
                this.model.destroy( { wait: true, success: function(model, resp) {

                    if (this.formQuestionItemView instanceof Backbone.View) {
                        // console.log('Vista instanciada');
                        this.formQuestionItemView.stopListening();
                        this.formQuestionItemView.undelegateEvents();
                    }

                    this.formQuestionItemView = new app.FormQuestionItem({
                        model: model,
                        parameters: {
                            respDestroy: resp
                        }
                    });

                    this.formQuestionItemView.onDeleteQuestion(model);

                        // complete proccess

                        window.Misc.removeSpinner({
                            'wrap': _this.$el,
                            'direct': false
                        });

                        _this.$el.modal().off();
                        _this.$el.modal('hide');

                        _this.$el.find('.modal-backdrop').remove();

                        // response success or error

                        var type = resp.success ? 'success' : 'alert',
                            text = resp.success ?
                                (resp.message !== undefined ? resp.message : '...')
                                : resp.errors;

                        if (_.isObject(resp.errors)) {

                            var listError = '<ul>';

                            $.each(resp.errors, function (field, item) {
                                listError += '<li>' + item[0] + '</li>';
                            });
                            listError += '</ul>';

                            text = listError;
                        }

                        var config = {
                            'text': text,
                            'type': type
                        }
                        confCrop = _.extend({}, _this.configAlert, config);

                        //complete proccess

                        if (resp.success !== undefined) {
                            if (resp.success) {
                                window.Misc.showAlertBox(confCrop);
                            } else {
                                window.Misc.showAlertBox(confCrop);
                            }
                        }
                    }
                });
            }
        },

        onModalDestroy: function (e){
            e.preventDefault();

            this.$el.modal().off();
        },
   });

})(jQuery, this, this.document);
