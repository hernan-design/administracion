/**
 * Class OptionsQuestionsListView extends of Backbone View
 * @author TuProyecto || Desarrollador : @dortegon
 * @link http://TuProyecto.com
 */

//Global App Backbone
app || (app = {});

(function ($, window, document, undefined) {

    app.OptionsQuestionsListView = Backbone.View.extend({
        tagName: 'tr',
        templateOption: _.template( ( $('#field-option-tpl').html()  || '' ) ),
        templateOptionMatriz: _.template( ($('#field-option-matriz-tpl').html() || '' ) ),
        events: {
            'click .btn-inactive': 'onDelete'
        },
        parameters: {
            dataFilter: {}
        },

        /**
         * Constructor Method
         */
        initialize: function (opts) {

            // extends parameters
            if( opts !== undefined && _.isObject(opts.parameters) ){
                this.parameters = _.extend({}, this.parameters, opts.parameters);
            }

            // Events Listeners
            this.listenTo( this.model, 'change', this.render );
        },

        render: function () {
            var attributes = this.model.toJSON();
            if (attributes.matrizOption) {
                this.$el.html( this.templateOptionMatriz(attributes) );
            } else {
                this.$el.html( this.templateOption(attributes) );
            }

            return this;
        },

        // Inactive option question
        onDelete: function (e) {
            e.preventDefault();

            var _this = this, dataOption = $(e.currentTarget).data('option');

            if(dataOption > 10 && !this.model.get('codigo_encuesta')){
                this.model.destroy({wait: true, silent: true,
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
                                _this.$el.closest('tr').remove();
                                window.Misc.showAlertBox( confCrop );
                            }else{
                                window.Misc.showAlertBox( confCrop );
                            }
                        }
                    }
                });
            }else{
                $(e.target).closest('tr').remove();

                var config = {
                    'text': 'La opción ha sido removida correctamente!!',
                    'type': 'success'
                }
                confCrop =  $.extend({}, _this.configAlert, config);
                window.Misc.showAlertBox( confCrop );
            }
        }
    });

})(window.jQuery, window, document);
