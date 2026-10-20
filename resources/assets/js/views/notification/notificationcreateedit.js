/**
* Class NotificationsCreateEditView extends of Backbone View
* @author TuProyecto || Desarrollador : @dortegon
* @link http://TuProyecto.com
*/

//Global App Backbone
app || (app = {});

(function ($, window, document, undefined) {

    app.NotificationsCreateEditView = Backbone.View.extend({
        el: '#output-content',
        templateBlog: _.template( ($('#module-resource-blog-tpl').html() || '') ),
        templateCourse: _.template( ($('#module-resource-course-tpl').html() || '') ),
        templateProgram: _.template( ($('#module-resource-program-tpl').html() || '') ),
        templateResultCount: _.template( ($('#browse-result-count-tpl').html() || '') ),
        templateUsersSearch: _.template( ($('#search-users-toggle-tpl').html() || '') ),
        templateAddressee: _.template( ($('#addressee-notification-tpl').html() || '') ),
        templateConfirmNoti: _.template( ($('#confirm-notification-tpl').html() || '') ),
        templateCalendar: _.template( ($('#module-resource-calendar-tpl').html() || '') ),
        templateNotificationText: _.template( ($('#notification-text-tpl').html() || '') ),
        templateFiltersAddressee: _.template( ($('#filters-addressee-tpl').html() || '') ),
        templateTextNotificaction: _.template( ($('#type-text-notification-tpl').html() || '') ),
        templateMultiSelect: _.template( ($('#multiselect-channel-notification-tpl').html() || '') ),
        templateModuleNotificaction: _.template( ($('#type-module-notification-tpl').html() || '') ),
        events: {
            'change #codigo_modulo': 'onDataModule',
            'submit #form-output-notifications': 'addNotification',
            'change #tipo_notificacion': 'onChangeTypeNotification',
            'click #add-user-notification': 'onAddUserNotification',
            'change #destinatarios_notificacion': 'onChangeAddressee',
            'click .btn-clear-user-notification': 'clearUserNotification'
        },
        parameters: {

        },

        /**
        * Constructor Method
        */
        initialize : function(opts) {

            // extends parameters
            if( opts !== undefined && _.isObject(opts.parameters) ){
                this.parameters = $.extend({}, this.parameters, opts.parameters);
            }

            //Init Attribute
            this.configAlert = {
                'wrapper': '#main-wrap',
                'closeTime': 6000,
                'speedOpen': 500,
                'speedClose': 500
            };

            // initialize reference to elements
            this.$contentNotification = this.$('.content-notification');
            this.$formOutputNotification = this.$('#form-output-notifications');

            // Initialize models
            this.event = new app.EventModel();
            this.notification = new app.NotificationOutputModel();
            
            // Initialize collections
            this.usersList = new app.UsersNotificationList();

            // Events Listener
            this.listenTo( this.usersList, 'add', this.addOneNotification );
            this.listenTo( this.usersList, 'remove', this.addAllNotifications );
            this.listenTo( this.notification, 'request', this.loadSpinner );
            this.listenTo( this.notification, 'sync', this.responseServer );
        },

        /*
        * Render View Element
        */
        render: function() {
            var typeNotification = this.$('#tipo_notificacion').val(); 

            this.$typeNotification  = this.$('#type-notification');
            this.$addresseeNotification  = this.$('#addressee-notification');

            if(typeNotification == window._tp.getTypeNotification['text']){
                this.onRenderChangeTNotification(typeNotification);
            }

            this.channels = new app.ChannelsContentList();
            this.channels.fetch();
        },

        /**
        * fires libraries jscourseModel
        */
        ready: function () {
            if( typeof window.initComponent.select2Trigger == 'function' ){
                window.initComponent.select2Trigger();
            }

            if(typeof window.initComponent.multiselectTrigger == 'function' ){
                window.initComponent.multiselectTrigger();
            }
        },

        // Add user by notification
        onAddUserNotification: function (e) {
            e.preventDefault();

            var data = window.Misc.formToJson(this.$formOutputNotification);

            newData = _.pick(data, 'correo_electronico');
            newData.item = true;
                
            this.usersList.create(newData, {wait: true, reset: true,
                success: function(model, resp, opts){

                window.Misc.removeSpinner({
                    'wrap': this.$el,
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
                _.extend(this.configAlert, config);

                //complete proccess
                if( resp.success !== undefined ){
                    if( resp.success ){
                        window.Misc.showAlertBox( this.configAlert );
                        this.$('#correo_electronico_filter_data').val('');
                        this.$('#nombre_usuario_filter_data').val('');
                    }else{
                        window.Misc.showAlertBox( this.configAlert );
                    }
                }
            }.bind(this)});
        },

        // Model one to one 
        addOneNotification: function (user) {
            var view = new app.NotificacionUserItemView({
                model: user,
                collection: this.usersList,
                parameters: {
                    dataFilter: this.parameters.dataFilter
                }
            });

            this.$postsWrap.append( view.render().el );
        },

        /**
		* Render all view notification of the collection
		*/
		addAllNotifications: function () {

			this.$postsWrap.html('');
			this.usersList.forEach( this.addOneNotification, this );
		},

        // Cahange type notification
        onChangeTypeNotification: function (e) {
            e.preventDefault();

            this.onRenderChangeTNotification( $(e.target).val() );
        },

        // Render type change notification
        onRenderChangeTNotification: function (type) {
            if(type == window._tp.getTypeNotification['module']){
                this.$typeNotification.html( this.templateModuleNotificaction() );
            }
            
            if(type == window._tp.getTypeNotification['text']){
                this.$typeNotification.html( this.templateTextNotificaction() );
            }
        },

        // Get data module
        onDataModule: function (e) {
            e.preventDefault();
            var module_code = $(e.target).val();

            this.$('#notification-text').html( this.templateNotificationText({ module_code: module_code }) );
            this.moduleResourceNotification = this.$('#module-resource-notification');

            if(module_code == window._tp.getModule['calendar']){
                this.moduleResourceNotification.html( this.templateCalendar );
            }

            if(module_code == window._tp.getModule['course']){
                this.moduleResourceNotification.html( this.templateCourse );
            }

            if(module_code == window._tp.getModule['blog']){
                this.moduleResourceNotification.html( this.templateBlog );
            }

            if(module_code == window._tp.getModule['program']){
                this.moduleResourceNotification.html( this.templateProgram );
            }

            this.ready();
        },

        // Render addressee to notification
        onChangeAddressee: function (e) {
            var attributes = {},
                addressee = $(e.target).val(),
                channelContent = this.$('#section-channel-content'); 
            
            if(addressee == window._tp.getAddressee['addressee_to']){
                this.$addresseeNotification.html( this.templateAddressee() );

                this.$usersSearch   = this.$('#search-users-toggle');
                this.$postsWrap     = this.$('#list-content-article');

                attributes.channels = this.channels.toJSON();
                channelContent.html( this.templateMultiSelect( attributes ) );
                this.ready();
            }

            if(addressee == window._tp.getAddressee['global']){
                this.$addresseeNotification.html('');
                channelContent.html('');
                
                if(!_.isEmpty(this.usersList)){
                    this.usersList.reset();                
                }
            }

            if(addressee == window._tp.getAddressee['filters']){
                this.$addresseeNotification.html( this.templateFiltersAddressee() );

                this.$searchProgram = this.$('#codigo_convocatoria');
                this.$searchBirthdate = this.$('#fecha_nacimiento_usuario');
                this.$searchLevelEducation = this.$('#codigo_nivel_educacion');
                this.$searchEmployment = this.$('#codigo_lista_ocupacion');
                this.$searchCity = this.$('#codigo_ciudad_usuario');
                this.$searchLocations = this.$('#codigo_localidad_ciudad');
                this.$searchZone = this.$('#codigo_barrio_localidad');
                this.$searchStratum = this.$('#codigo_estrato');
                this.$searchHousingType = this.$('#codigo_tipo_vivienda');
                this.$searchHeadOfFamily = this.$('#jefe_hogar'); 
                this.$searchWorkSector = this.$('#codigo_sector_trabajo');
                this.$searchEnterprise = this.$('#enterprise');
                this.$searchSelfDenomination = this.$('#codigo_autodenominacion');
                this.$searchPopulationGroup = this.$('#codigo_grupo_poblacional');
                this.$searchCommunityGroup = this.$('#codigo_grupo_comunitario');
                this.$searchSocialGroup = this.$('#codigo_grupo_social');
                this.$searchDisability = this.$('#disability');
                this.ready();
            }
        },

        /**
        * Load spinner on the request
        */
        loadSpinner: function () {
            window.Misc.setSpinner( this.$contentNotification );
        },

        /**
        * response of the server
        */
        responseServer: function ( model, resp, opts ) {

            window.Misc.removeSpinner({
                wrap: this.$contentNotification,
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

                    window.Misc.showAlertBox(this.configAlert);
                    app.AppRouter.navigate( Route.route('notificacion.output.index') );

                }else {
                    window.Misc.showAlertBox(this.configAlert);
                }
            }
        },

        /*
         * Add notification
        */
        addNotification: function (e) {
            e.preventDefault();

            var _this = this,
                data = window.Misc.formToJson( e.target );

            if(data.destinatarios_notificacion == window._tp.getAddressee['addressee_to']){
                data.users = this.usersList.pluck('codigo_usuario');
            }

            this.notification.clear();

            if(data.codigo_modulo){
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
    
                    window.Misc.showAlertBox( {text: 'Debes seleccionar ' +resource, type: 'alert'} );
                    return;
                }                
            }

            if( this.acceptConfirm instanceof Backbone.View ) {
                this.acceptConfirm.stopListening();
                this.acceptConfirm.undelegateEvents();
            }

            this.acceptConfirm = new app.ConfirmWindow({
                parameters: {
                    template: this.templateConfirmNoti(),
                    titleConfirm: "Confirmación",
                    onConfirm: function () {
                        _this.notification.save(data, {wait: true, 
                            success: function(model, resp, options){
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
                                        window.Misc.redirect( app.AppRouter.navigate( Route.route('notificacion.output.index') ) );
                                    }
                                }
                            }
                        });
                    }
                }
            });

            this.acceptConfirm.render();
        },

        /**
         * Clear filters of search from user
         * @param {*} e 
         */
        clearUserNotification: function (e) {
            e.preventDefault();
console.log('entra');

            this.$searchProgram.select2('val', '');
            this.$searchBirthdate.val('');
            this.$searchLevelEducation.val('');
            this.$searchEmployment.val('');
            this.$searchCity.select2('val', '');
            this.$searchLocations.val('');
            this.$searchZone.val('');
            this.$searchStratum.val('');
            this.$searchHousingType.val('');
            this.$searchHeadOfFamily.val('');
            this.$searchWorkSector.val('');
            this.$searchEnterprise.val('');
            this.$searchSelfDenomination.val('');
            this.$searchPopulationGroup.val('');
            this.$searchCommunityGroup.val('');
            this.$searchSocialGroup.val('');
            this.$searchDisability.val('');
        }
   });

})(window.jQuery, window, document);
