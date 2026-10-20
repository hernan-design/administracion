/**
* Class PublishCreateEditView extends of Backbone View
* @author TuProyecto || Desarrollador : @dortegon
* @link http://TuProyecto.com
*/

//Global App Backbone
app || (app = {});

(function ($, window, document, undefined) {

    app.PublishCreateEditView = Backbone.View.extend({
        el: '#publish-content',
        templateCalendar: _.template( ($('#module-resource-calendar-tpl').html() || '') ),
        templateBlog: _.template( ($('#module-resource-blog-tpl').html() || '') ),
        templateCourse: _.template( ($('#module-resource-course-tpl').html() || '') ),
        templateProgram: _.template( ($('#module-resource-program-tpl').html() || '') ),
        events: { 
            'submit #form-publish-channels': 'addPublish',
            'change #codigo_modulo': 'onDataModule',
            'change #codigo_recurso_modulo': 'onResourceModule',
            'ifChanged #publish_general': 'onPublishGeneral',
            'ifChanged .publish_channels': 'onPublishchannels'
        },
        parameters: {
            //
        },

        /**
        * Constructor Method
        */
        initialize : function(opts) {
            // initialize reference to elements
            this.$sectionContent = this.$('#content-list-channels');
            this.$postsWrap = this.$('#list-channels-publish');
            this.$noResults = this.$('#content-list-channels > .no-results-filter');

            this.configAlert = {
                'wrapper': '#main-wrap',
                'closeTime': 20000,
                'speedOpen': 500,
                'speedClose': 500
            };

            // initialize model
            this.model = new app.PublishModel();

            // initialize collection
            this.channelsContentList = new app.ChannelsContentList();

            // Events Listeners
            this.listenTo( this.model, 'request', this.loadSpinner );
            this.listenTo( this.model, 'sync', this.responseServer );
			this.listenTo( this.channelsContentList, 'add', this.addOne );
			this.listenTo( this.channelsContentList, 'reset', this.addAll );
			this.listenTo( this.channelsContentList, 'change', this.addAll );
			this.listenTo( this.channelsContentList, 'request', this.loadSpinner);
			this.listenTo( this.channelsContentList, 'sync', this.responseServer);
        },

        /*
        * Render View Element
        */
        render: function() {
            //
        },

        /**
		* Render view comment by model
		* @param Object channelContent Model instance
		*/
		addOne: function (channelContent) {
            if(channelContent.get('codigo_canal')){
                var view = new app.PublishChannelsItemView({
                    model: channelContent
                });

                this.$postsWrap.append( view.render().el );

                if( (this.channelsContentList.indexOf(channelContent) + 1) >= this.channelsContentList.length ) {
                    this.ready();
                }
            }
		},

		/**
		* Render all view Course of the collection
		*/
		addAll: function () {

            if(!this.channelsContentList.length) {
                this.$noResults.show();
            }else {
                this.$noResults.hide();
            }

			this.$postsWrap.html('');
			this.channelsContentList.forEach( this.addOne, this );
		},

        /**
        * fires libraries jscourseModel
        */
        ready: function () {
            if( typeof window.initComponent.select2Trigger == 'function' ){
                window.initComponent.select2Trigger();
            }

            if( typeof window.initComponent.icheckTrigger == 'function' ){
                window.initComponent.icheckTrigger();
            }
        },

        // Get data module
        onDataModule: function (e) {
            e.preventDefault();
            var module_code = $(e.target).val();

            if(module_code == window._tp.getModule['calendar']){
                this.$('#module-resource').html( this.templateCalendar );
            }

            if(module_code == window._tp.getModule['course']){
                this.$('#module-resource').html( this.templateCourse );
            }

            if(module_code == window._tp.getModule['blog']){
                this.$('#module-resource').html( this.templateBlog );
            }

            if(module_code == window._tp.getModule['program']){
                this.$('#module-resource').html( this.templateProgram );
            }

            this.ready();
            this.onClearCollection();
        },

        // Get data resource module
        onResourceModule: function (e) {
            e.preventDefault();

            var resource_module = $(e.target).val(),
                module_code = this.$('#codigo_modulo').val(),
                type = 'list';

            if(resource_module){
                this.channelsContentList.fetch({data: {tipo_contenido: module_code, id_contenido: resource_module, type: type}, reset: true});
            }

            this.onClearCollection();
        },

        // Clear collection list wiew
        onClearCollection: function () {
            if(this.channelsContentList.length){
                var collection = this.channelsContentList;
                _.each(_.clone(collection.models), function(model) {
                    model.clear();
                    collection.remove(model, {reset: true});
                });
            }
        },

        // Remove checkboxes cheched
        onPublishGeneral: function (e) {
            e.preventDefault();
            var checkedChannels = this.$('.publish_channels').parent('[class*="icheckbox"]');

            // for (var i = 0; i < checkedChannels.length; i++) {
            //     if (this.$(checkedChannels[i]).hasClass("checked")) {
            //         this.$(checkedChannels[i]).iCheck('uncheck');
            //     }
            // }
        },

        // Remove chechbox general
        onPublishchannels: function () {
            var checkedChannels = this.$('#publish_general').parent('[class*="icheckbox"]');

            // if (this.$(checkedChannels).hasClass("checked")) {
            //     this.$(checkedChannels).iCheck('uncheck');
            // }
        },

        addPublish: function (e) {
            e.preventDefault();

            var data = window.Misc.formToJson( e.target );

            if(!data.codigo_modulo){
                window.Misc.showAlertBox( {text: 'Debes seleccionar un módulo', type: 'alert'} );
                return;
            }

            if(!data.codigo_recurso_modulo){
                var resource = '';

                if(data.codigo_modulo == window._tp.getModule['calendar']){
                    resource = 'un evento';
                }

                if(data.codigo_modulo == window._tp.getModule['course']){
                    resource = 'un curso';
                }

                if(data.codigo_modulo == window._tp.getModule['blog']){
                    resource = 'un blog';
                }

                if(data.codigo_modulo == window._tp.getModule['program']){
                    resource = 'una convocatoria';
                }

                window.Misc.showAlertBox( {text: 'Debes seleccionar ' + resource, type: 'alert'} );
                return;
            }

            if(!data.channel){
                window.Misc.showAlertBox( {text: 'Debes seleccionar mínimo un canal o la opción general', type: 'alert'} );
                return;
            }

            this.model.save( data, {wait: true} );

        },

        /**
        * Load spinner on the request
        */
        loadSpinner: function () {
            window.Misc.setSpinner( this.$sectionContent );
        },

        /**
        * response of the server
        */
        responseServer: function ( model, resp, opts ) {
            var _this = this;

            window.Misc.removeSpinner({
                wrap: this.$sectionContent,
                direct: false
            });

            // response success or error
            var type = resp.success ? 'success' : 'alert',
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
                    window.Misc.showAlertBox( this.configAlert );
                    this.onClearCollection();
                    this.clearFields();
                }else {
                    window.Misc.showAlertBox( this.configAlert );
                }
            }
        },

        clearFields: function () {
            this.$('#codigo_modulo').val('');
            this.$('#section-content-resource').remove();
            this.onPublishchannels();
        }

    });

})(window.jQuery, window, document);
