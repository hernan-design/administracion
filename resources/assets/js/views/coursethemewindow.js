/**
* Class CourseContentWindow extends of Backbone View
* @author TuProyecto || Desarrollador : @dortegon
* @link http://TuProyecto.com
*/

//Global App Backbone
app || (app = {});

(function ($, window, document, undefined) {

    app.CourseThemeWindow = Backbone.View.extend({

        el: '#modal-form',
        template: _.template( ($('#theme-modal-tpl').html() || '') ),
        events: {
            'submit #form-modal-all': 'onSubmit',
            'click .cancel-action': 'onModalDestroy',
		},
        parameters: {
            title: '',
            template: _.template( ($('#theme-modal-tpl').html() || '') ),
            dataFilter: { },
            data: { },
        },

        /**
        * Constructor Method
        */
        initialize : function(opts) {
            _.bindAll(this, 'render');

            // extends attributtes
            if( opts !== undefined && _.isObject(opts.parameters) )
                this.parameters = _.extend({}, this.parameters, opts.parameters );


            var _this = this;
            this.render = _.wrap(this.render, function(render) {

                _this.trigger('beforeRender');

                render();

                _this.trigger('afterRender');

                return _this;
            });

            // Init attributes
            this.$formModal = this.$('#form-modal-all');

            // Init Attributes
            this.configAlert = {
                'wrapper': '#main-wrap',
                'closeTime': 6000,
                'speedOpen': 500,
                'speedClose': 500
            };
        },

        /*
        * Render View Element
        */
        render: function() {
            this.setElement(this.el);
            var attributes = {};

            // extend attributes confirm window
            if( this.model instanceof Backbone.Model ) _.extend(attributes, this.model);
            this.model.set({ totalTheme: this.parameters.totalthemes });

            console.log('Attributes', this.model.toJSON());

            // Change modal text and content
            this.$el.find('.content-modal').html(this.template(this.model.toJSON()));
              // render editor
              ReactDOM.render(React.createElement(SimpleEditor, {
                content: this.model,
                formCreate: this.$formModal,
                nameField: "nombre_tema_curso",
                minHeight: 50,
                value: this.model.get('nombre_tema_curso'),
                column: "col-md-12",
                previewHide: true,
                required: true
            }, null), document.getElementById('name-course-theme'));

            // render editor
            ReactDOM.render(React.createElement(SimpleEditor, {
                content: this.model,
                formCreate: this.$formModal,
                nameField: "objetivo_tema_curso",
                value: this.model.get('objetivo_tema_curso'),
                column: "col-md-12",
                previewHide: true
            }, null), document.getElementById('description-course-theme'));

            this.$el.modal("show");

            return this;
        },

        /**
        * fires libraries js
        */
        ready: function () {
            // reload plugins
            if( typeof(window.initComponent.autosizeTrigger) == 'function')
                window.initComponent.autosizeTrigger();

        },



        /*
        * Submit data
        */
        onSubmit: function (e) {
            e.preventDefault();

            var _this = this,
                data = window.Misc.formToJson( this.$formModal );
                 _.extend(data, this.parameters.data);
            this.model.save(data, {wait: true,
                success: function (model, resp) {
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
        },

        onModalDestroy: function (e){
            this.$el.modal().off();
        }
   });

})(jQuery, this, this.document);
