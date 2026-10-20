/**
* Class ChannelCreateEditView extends of Backbone View
* @author TuProyecto || Desarrollador : @dortegon
* @link http://TuProyecto.com
*/

//Global App Backbone
app || (app = {});

(function ($, window, document, undefined) {

    app.ChannelCreateEditView = Backbone.View.extend({
        el: '#channel-content',
        templateProfessionSearch: _.template( ($('#search-professions-toggle-tpl').html() || '') ),
        events: {
            'click .round-button': 'onModal',
            'submit #form-channel': 'addChannel',
            'click .btn-add-profession': 'onProfessionSearchToggle',
            'submit #form-profession-channel': 'onAddProfession',
            'click #profession-tab': 'onGetProfessions'
        },
        parameters: {
            //
        },

        /**
        * Constructor Method
        */
        initialize : function(opts) {

            // extends parameters
            if( opts.parameters !== undefined && _.isObject(opts.parameters) )
                this.parameters = $.extend({}, this.parameters, opts.parameters);

            //Init Attribute
            this.configAlert = {
                'wrapper': '#main-wrap',
                'closeTime': 6000,
                'speedOpen': 500,
                'speedClose': 500
            };

            this.$professionsSearch = this.$('#search-professions-toggle');

            this.channelCategoryModel = new app.ChannelCategoryModel();
            this.channelProfessionList = new app.ChannelProfessionsList();

            // Events Listener
            this.listenTo( this.model, 'request', this.loadSpinner );
            this.listenTo( this.model, 'sync', this.responseServer );
        },

        /*
        * Render View Element
        */
        render: function() {
            this.$searchName = this.$('#nombnre_profesion_persona');
            this.referenceViews();
        },

        /**
		* fires libraries js and functions
		*/
        ready: function () {

            if(typeof window.initComponent.select2Trigger == 'function' ){
                window.initComponent.select2Trigger();
            }
        },

        // Reference to view
        referenceViews: function () {
            var _this = this;

            // View list users channel
            this.userChannelModel = new app.UserChannelModel();

            var channelUserListView = new app.ChannelUserListView({
                el: this.$('.tp_x_content'),
				model: this.userChannelModel,
                collection: this.collection,
				parameters: {
                    data: _this.parameters.dataFilter,
                    channel: _this.model.get('codigo_canal')
				}
			});

            channelUserListView.trigger('getdata', {channel: this.model.get('codigo_canal')});
        },

        onGetProfessions: function () {

            // View list professions channel
            channelProfessionsListView = new app.ChannelProfessionsListView({
                el: this.$('.tp_x_content'),
                collection: this.channelProfessionList,
				parameters: {
                    channel: this.model.get('codigo_canal'),
                    data: { resource: window._tp.getResource['professions'] }
				}
            });

            channelProfessionsListView.trigger('getdata', {
                channel: this.model.get('codigo_canal'),
                resource: window._tp.getResource['professions']
            });
        },

        // Slide compenent search profession
        onProfessionSearchToggle: function (e) {
            e.preventDefault();

            // Render template in mode hide
            this.$professionsSearch.html( this.templateProfessionSearch() ).hide();
            this.$professionsSearch.slideToggle('slow');
            this.ready();
        },

        // Add user collection
        onAddProfession: function (e) {
            e.preventDefault();

            var _this = this,
                data = window.Misc.formToJson( e.target );

            data.channel = this.model.get('codigo_canal');
            data.codigo_tabla_vinculo_canal = window._tp.getResource['professions'];

            // Init spinner
            window.Misc.setSpinner( this.$el );

            this.channelProfessionList.trigger('store', data);

            window.Misc.removeSpinner({
                'wrap': this.$el,
                'direct': false
            });
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

            // response success or error
            var _this = this,
                type = resp.success ? 'success' : 'alert',
                text = resp.success ?
                        (resp.message !== undefined ? resp.message : '...')
                            : resp.errors;

            if( _.isObject( resp.errors ) ) {

                var listError = '<ul>';

                $.each(resp.errors, function(field, item) {
                     listError += '<li>'+ item[0] +'</li>';
                });
                listError += '</ul>';

                text = listError;
            }

            var conf = {
                'text': text,
                'type': type
            };
            _.extend(this.configAlert, conf);

            //complete proccess
            if( resp.success !== undefined ) {
                if( resp.success ){
                    window.Misc.showAlertBox(this.configAlert);

                    if(resp.redirect){
                        setTimeout(function(){
                            window.Misc.redirect( app.AppRouter.navigate( Route.route('comunidad.canal.edit',{canal: _this.model.get('codigo_canal') }) ) );
                        }, 3000);
                    }
                }else {
                    window.Misc.showAlertBox(this.configAlert);
                }
            }
        },

        /*
         * Add channel
        */
        addChannel: function (e) {
            e.preventDefault();

            var data = window.Misc.formToJson( e.target )
            this.model.save( data, {wait: true} );
        },

        // Open modal view
        onModal: function (e) {
            e.preventDefault();
            e.stopPropagation();

            this.modalChannelCategory = new app.FormSampleWindow({
                model: this.channelCategoryModel,
                parameters: {
                    template: _.template( ($('#form-modal-tpl').html() || '') ),
                    title: 'Agregar categoría'
                }
            });

            this.modalChannelCategory.render();
        }
   });

})(window.jQuery, window, document);
