/**
* Class EditingView extends of Backbone View
* @author TuProyecto || Desarrollador : @danielortegon
* @link http://TuProyecto.com
*/

//Global App Backbone
app || (app = {});

(function ($, window, document, undefined) {

    app.EditingView = Backbone.View.extend({

        el: '#project-content',
        events: {
            'click .edit-field': 'onOpenEditForm'
        },
        parameters: {
            callback: null
        },
        renderTo: null,

        /**
        * Constructor Method
        */
        initialize : function(opts) {

            _.bindAll(this, 'beforeRender', 'render', 'afterRender');

            // extend parameters
            if(!_.isUndefined(opts) && _.isObject(opts.parameters))
                this.parameters = $.extend({}, this.parameters, opts.parameters);

            //Init Attributes
            this.editForms = {
                'form-textarea': {'nameView': 'textareaEditView', 'nameClassView': 'TextareaEditView', 'renderTo': '#edit-textarea-container .field-editing'},
                'form-select': {'nameView': 'selectEditView', 'nameClassView': 'SelectEditView', 'renderTo': '#edit-select-container .field-editing'},
                'form-datepicker': {'nameView': 'datepickerEditView', 'nameClassView': 'TypeFieldEditView', 'renderTo': '#edit-datepicker-container .field-editing'},
                'form-number': {'nameView': 'numberEditView', 'nameClassView': 'TypeFieldEditView', 'renderTo': '#edit-number-container .field-editing'},
                'form-text': {'nameView': 'textEditView', 'nameClassView': 'TypeFieldEditView', 'renderTo': '#edit-text-container .field-editing'}
            };

            var _this = this;

            this.render = _.wrap(this.render, function(render) {

                _this.trigger('beforeRender');

                render();

                _this.trigger('afterRender');

                return _this;
            });

            // Events Listener
            this.on( 'beforeRender', this.beforeRender );
            this.on( 'afterRender', this.afterRender );

        },

        /*
        * Render View Element
        */
        render: function() {

            $(this.el).off('click', '.edit-field');
            this.delegateEvents({
                'click .edit-field': 'onOpenEditForm'
            });

            return this;
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
            //
        },

        /**
        * open edit form
        */
        onOpenEditForm: function (e) {
            e.preventDefault();

            var nameFormTmpl = '',
                $editField = $(e.currentTarget),
                params = {
                    parameters: { callback: this.parameters.callback }
                };

            if( $editField.data('edit-template') ) {
                nameFormTmpl = $editField.data('edit-template');
            }
            this.renderTo = $editField.data('edit-renderto');

            if( $editField.data('edit-attr') ) {
                params['parameters']['attrField'] = $editField.data('edit-attr');
            }

            if( $editField.data('edit-type') ) {
                params['parameters']['typeField'] = $editField.data('edit-type');
            }

            // switch form template
            this.swicthEditForm(nameFormTmpl, params);
        },

        /**
        * switch for edit forms
        * @param String nameForm
        */
        swicthEditForm: function (nameForm, params) {
            nameForm || (nameForm = '');
            params || (params = {});

            var _this = this;

            var toRenderView = {
                'form-textarea' : function() {
                    _this.renderViewToEdit('form-textarea', params);
                },

                'form-select' : function() {
                    _this.renderViewToEdit('form-select', params);
                },

                'form-datepicker' : function() {
                    params['parameters']['typeField'] || (params['parameters']['typeField'] = 'datepicker');
                    _this.renderViewToEdit('form-datepicker', params);
                },

                'form-number' : function() {
                    params['parameters']['typeField'] || (params['parameters']['typeField'] = 'number');
                    _this.renderViewToEdit('form-number', params);
                },

                'default' : function() {
                    params['parameters']['typeField'] || (params['parameters']['typeField'] = 'text');
                    _this.renderViewToEdit('form-text', params);
                }
            };

            if (toRenderView[nameForm]) {
                toRenderView[nameForm]();
            } else {
                toRenderView['default']();
            }
        },

        /**
        * to render edit view
        * @param String nameForm
        * @param Object params
        */
        renderViewToEdit: function (nameForm, params) {
            params || (params = {});

            var config = {
                model: this.model
            };
            _.extend( config, params );

            // if it exists form template
            if( this.editForms[nameForm] ) {

                if( this[this.editForms[nameForm].nameView] instanceof Backbone.View ){
                    this[this.editForms[nameForm].nameView].stopListening();
                    this[this.editForms[nameForm].nameView].undelegateEvents();
                }

                if( window.app[this.editForms[nameForm].nameClassView] !== undefined ) {

                    this[this.editForms[nameForm].nameView] = new window.app[this.editForms[nameForm].nameClassView]( config );
                    // set view inside container
                    if( this.renderTo ) {
                        this.$(this.renderTo).html( this[this.editForms[nameForm].nameView].render().el );
                        this.renderTo = null;

                    }else if( this.editForms[nameForm].renderTo ){
                        this.$(this.editForms[nameForm].renderTo).html( this[this.editForms[nameForm].nameView].render().el );
                    }else {
                        this[this.editForms[nameForm].nameView].render();
                    }

                    // render tags
                    if( typeof this[this.editForms[nameForm].nameView].renderTagsPost == 'function' )
                        this[this.editForms[nameForm].nameView].renderTagsPost();

                    if( typeof this[this.editForms[nameForm].nameView].renderImportTags == 'function' )
                        this[this.editForms[nameForm].nameView].renderImportTags();

                    // fire plugins
                    if( typeof window.initComponent.select2Trigger == 'function' && _.has($.fn, 'select2') ) {
                        window.initComponent.select2Trigger();
                    }

                    if( typeof window.initComponent.datepickerTrigger == 'function' ) {
                        window.initComponent.datepickerTrigger();
                    }

                    this.$(':input[data-inputmask]').inputmask();
                }
            }
        }
    });

})(jQuery, this, this.document);
