/**
* Class SelectEditView extends of Backbone View
* @author TuProyecto || Desarrollador : @danielortegon
* @link http://TuProyecto.com
*/

//Global App Backbone
app || (app = {});

(function ($, window, document, undefined) {

	app.SelectEditView = Backbone.View.extend({

        tagName: 'div',
        className: 'edit-form',
		template: _.template( ($('#edit-form-select-field-tpl').html() || '') ),
		events: {
            'submit #form-edit-select-field': 'onEditForm',
            'change .edit-field-text': 'onEditForm',
            'keydown .edit-field-text': 'revertOnEscape'
		},
		parameters: {
            callback: null,
            attrField: ''
		},

		/**
		* Constructor Method
		*/
		initialize : function(opts) {

			// extend parameters
			if(!_.isUndefined(opts) && _.isObject(opts.parameters))
				this.parameters = $.extend({}, this.parameters, opts.parameters);

			// Events Listener
			this.listenTo( this.model, 'change', this.render );

            // Config alert
            this.configAlert = {
                'wrapper': '#main-wrap',
                'closeTime': 20000,
                'speedOpen': 500,
                'speedClose': 500
            };
		},

		// view render
		render : function () {
            var attributes = this.model.toJSON();
            attributes['attrField'] = this.parameters.attrField;

            this.$el.html( this.template(attributes) );

            // Initialize references
            this.$editFieldTxt = this.$('.edit-field-text');
            this.$formEdit = this.$('#form-edit-select-field');

            return this;
		},

        /**
        * update user fields
        */
        onEditForm: function (e) {
            e.preventDefault();
            var _this = this;

            // if it is not editing
            if( !this.$el.parents('.editable-item').hasClass('editing') ){
                return;
            }

            // if the value was not modified
            var data = window.Misc.formToJson( this.$formEdit );
            if( data[this.parameters.attrField] == this.model.get(this.parameters.attrField) && this.$formEdit.parsley().isValid() ){
                this.close();
                this.$editFieldTxt.blur();

                return;
            }

            if( this.$formEdit.parsley().isValid() ) {
                this.model.save(data, {patch:false, wait:true, success: function(model, resp, opts){
                    // response success or error
                    var type = resp.success ? 'success' : 'alert',
                        text = resp.success ?
                                    (resp.message !== undefined ? resp.message : '')
                                    : resp.errors;

                    if( _.isObject( resp.errors ) ) {
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
                    };
                    _.extend( _this.configAlert, config );

                    if( resp.success ) {
                        if(resp.message) {
                            window.Misc.showAlertBox( _this.configAlert );
                        }

                        // return callback
                        if( ({}).toString.call(_this.parameters.callback).slice(8,-1) === 'Function' )
                            _this.parameters.callback( resp );

                    }else {
                        window.Misc.showAlertBox( _this.configAlert );
                    }
                }});
            }
        },

        /**
        * Load spinner on the request
        */
        loadSpinner: function ( target, xhr, opts ) {
            window.Misc.setSpinner( this.$el );
        },

        /**
        * response of server
        */
        responseServer: function ( target, resp, opts ) {
            if( resp.success ) {
                this.close();
            }
        },

        /**
        * revert with scape key
        */
        revertOnEscape: function  (e) {
            if( e.which === 27 ){
                this.close();
                this.$editFieldTxt.blur();
            }
        },

        /**
        * close text edit form
        */
        close: function () {
            this.$el.parents('.editable-item').removeClass('editing');
            this.$editFieldTxt.find('options[value="'+ this.model.get(this.parameters.attrField) +'"]').attr('selected', true);
        }
	});

})(jQuery, this, this.document);
