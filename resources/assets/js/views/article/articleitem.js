/**
* Class ArticleItemView extends of Backbone View
* @author TuProyecto || Desarrollador : @dortegon
* @link http://TuProyecto.com
*/

//Global App Backbone
app || (app = {});

(function ($, window, document, undefined) {

    app.ArticleItemView = Backbone.View.extend({
        tagName: 'li',
        className: 'article-list',
        template: _.template( ($('#content-list-tpl').html() || '') ),
        templateConfirm: _.template( ($('#confirm-content-confirm-tpl').html() || '') ),
        events: {
            'click .btn-back': 'onModalConfirm',
            'click .btn-active': 'onModalConfirm',
            'click .btn-approve': 'onModalConfirm',
            'click .btn-inactive': 'onModalConfirm'
        },
        parameters: {
            dataFilter: {}
        },

        /**
        * Constructor Method
        */
        initialize: function (opts) {
            // extends parameters
            if( opts !== undefined && _.isObject(opts.parameters) ) {

                this.parameters = _.extend({}, this.parameters, opts.parameters);
            }

            // Events Listener
			this.listenTo( this.model, "change", this.render );
        },

        /*
        * Render View Element
        */
        render: function () {
            var attributes = this.model.toJSON();
            this.$el.html( this.template(attributes) );

            return this;
        },

        // Modal confirm
        onModalConfirm: function (e) {
            e.preventDefault();

            var _this = this,
                action = $(e.target).attr('data-value'),
                article_name = $(e.target).attr('data-name'),
                data = {
                    'change_state': true,
                    'action': action
                };

            if( this.acceptConfirm instanceof Backbone.View ) {
                this.acceptConfirm.stopListening();
                this.acceptConfirm.undelegateEvents();
            }

            this.acceptConfirm = new app.ConfirmWindow({
                model: this.model,
                parameters: {
                    template: this.templateConfirm({
                        action : action,
                        article_name: article_name
                    }),
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
                    }
                }
            });

            this.acceptConfirm.render();
        }
    });

})(window.jQuery, window, document);
