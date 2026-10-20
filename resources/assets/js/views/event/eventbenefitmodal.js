/**
* Class EventBenefitWindow extends of Backbone View
* @author TuProyecto || Desarrollador : @dortegon
* @link http://TuProyecto.com
*/

//Global App Backbone
app || (app = {});

(function ($, window, document, undefined) {

    app.EventBenefitWindow = Backbone.View.extend({
        el: '#modal-form',
        template: _.template( ($('#confirm-assist-control-tpl').html() || '') ),
        events: {
            'submit #form-modal-all': 'onSubmit',
            'click .cancel-action': 'onModalDestroy'
		},
        parameters: {
            title: '',
            data: {},
            dataTableView: '',
        },

        /**
        * Constructor Method
        */
        initialize : function(opts) {

            // extends attributtes
            if( opts !== undefined && _.isObject(opts.parameters) ){
                this.parameters = _.extend({}, this.parameters, opts.parameters );
            }

            // Events Listeners
			this.listenTo( this.collection, 'add', this.addOne );
			this.listenTo( this.collection, 'reset', this.addAll );
			this.listenTo( this.collection, 'request', this.loadSpinner);
            this.listenTo( this.collection, 'sync', this.responseServer);

            this.collection.fetch( {data: this.parameters.data, reset: true });
        },

        /*
        * Render View Element
        */
        render: function() {
            var attributes = this.model.toJSON();

            this.$el.find('.modal-title').html(this.parameters.title);
            this.$el.find('.content-modal').html(this.template( attributes ));
            this.$el.modal("show").removeAttr('tabindex');

            // Init attributes
            this.$formModal = this.$('#form-modal-all');
            this.$postsWrap = this.$('#benefits-assist-control-event');

            return this;
        },

        /**
		* Render view comment by model
		* @param Object benefitModel Model instance
		*/
		addOne: function (benefitModel) {

		    if (benefitModel) {
                var view = new app.EventBenefitModalItemView({
                    model: benefitModel
                });

                this.$postsWrap.append( view.render().el );
            } else {
                this.$postsWrap.html( window._tp.messages.notBeneficie );
            }

            // reload plugins
            if( typeof window.initComponent.icheckTrigger == 'function' && _.has($.fn, 'iCheck')){
                window.initComponent.icheckTrigger();
            }
        },

        /**
         * Render all view Benefit of the collection
         */
        addAll: function () {
            this.$postsWrap.html('');
            this.collection.length > 0 ?
                this.collection.forEach( this.addOne, this )
            :
                this.addOne(false)
        },

        /**
         * Load spinner on the request
         */
        loadSpinner: function ( target, xhr, opts ) {
            window.Misc.setSpinner( this.$el );
        },

        /**
         * response of the server
         */
        responseServer: function ( target, resp, opts ) {
            window.Misc.removeSpinner({
                'wrap': this.$el
            });
        },

        /**
         * Submit benefits by assits control
         * @param {*} e
         */
        onSubmit: function (e) {
            e.preventDefault();

            var _this = this,
                data = window.Misc.formToJson( e.target );
            data = _.extend(data, this.parameters.data);

            this.model.save( data, {wait: true,
                success: function (model, resp) {
                    // complete proccess
                    window.Misc.removeSpinner({
                        'wrap': this.$el,
                        'direct': false
                    });

                    _this.$el.modal().off();
                    _this.$el.modal("hide");

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
                            _this.parameters.dataTableView.ajax.reload()

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
        },

        onModalDestroy: function (e){
            this.$el.modal().off();
        }
   });

})(window.jQuery, window, document);
