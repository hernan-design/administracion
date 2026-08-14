/**
* Class RoleCreateEditView extends of Backbone View
* @author TuProyecto || Desarrollador : @dortegon
* @link http://TuProyecto.com
*/

//Global App Backbone
app || (app = {});

(function ($, window, document, undefined) {

    app.RoleCreateEditView = Backbone.View.extend({
        el: '#programs-content',
        templateMultiSelectAdministrator: _.template( ($('#multiselect-administrator-tpl').html() || '') ),
        templateMultiSelectPlatform: _.template( ($('#multiselect-platform-tpl').html() || '') ),
        events: {
            'submit #form-role': 'addRole'
        },
        parameters: {
            'role': ''
        },

        /**
        * Constructor Method
        */
        initialize: function(opts){

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

            // references
            this.$wrapperCourseUsers = this.$('#wrapper-render-users');

            // Collection permissions
            this.modulePermissionsPlatformList = new app.ModulePermissionsList();
            this.modulePermissionsAdministratorList = new app.ModulePermissionsList();

            // Events Listener
            this.listenTo( this.model, 'request', this.loadSpinner );
            this.listenTo( this.model, 'sync', this.responseServer );
            this.listenTo( this.modulePermissionsAdministratorList, 'reset', this.loadPermissions );
            this.listenTo( this.modulePermissionsPlatformList, 'reset', this.loadPermissions );
        },

        /*
        * Render View Element
        */
        render: function() {
            this.referenceViews();
            this.$multipleSelectAdministrator = this.$('#multiple-select-administrator');
            this.$multipleSelectPlatform = this.$('#multiple-select-platform');

            if(this.$('li[role="permissions_administrator"]').length) {
                // Collection permissons administrator
                this.modulePermissionsAdministratorList.fetch( {data: {role: this.model.get('id'), type: window._tp.getTypeModule['administrator']}, reset: true} );
            }

            if(this.$('li[role="permissions_platform"]').length) {
                // Collection permissions platform
                this.modulePermissionsPlatformList.fetch( {data: {role: this.model.get('id'), type: window._tp.getTypeModule['platform']}, reset: true} );
            }
        },

        /**
        * fires libraries js
        */
        ready: function () {
            // reload plugins
            if(typeof window.initComponent.multiselectTrigger == 'function' ){
                window.initComponent.multiselectTrigger();
            }
        },

        loadPermissions: function () {
            var attributesAdministrator = {};
            attributesAdministrator.role = this.model.get('id');
            attributesAdministrator.permissionsAdministrator = this.modulePermissionsAdministratorList.toJSON();
            this.$multipleSelectAdministrator.html( this.templateMultiSelectAdministrator( attributesAdministrator ) );

            var attributesPlatform = {};
            attributesPlatform.role = this.model.get('id');
            attributesPlatform.permissionsPlatform = this.modulePermissionsPlatformList.toJSON();
            this.$multipleSelectPlatform.html( this.templateMultiSelectPlatform( attributesPlatform ) );

            this.ready();
        },

        /**
        * reference to views
        */
        referenceViews: function () {
            this.userRolListView = new app.UserRoleListView({
                el: this.$wrapperCourseUsers,
    			model: this.model,
                collection: this.collection,
    			parameters: _.extend(this.parameters, {
    				template: 'templateUserRole'
    			})
            });

            if ( this.viewActivityLog instanceof Backbone.View ){
                this.viewActivityLog.stopListening();
                this.viewActivityLog.undelegateEvents();
            }

            this.viewActivityLog = new app.ActivityLogView({
                parameters: {
                    'subject': this.model.get('id'),
                    'subject_type' : _.escape('App.Models.Seguridad.Rol'),
                }
            });
        },

        /**
        * Load spinner on the request
        */
        loadSpinner: function () {
            window.Misc.setSpinner( this.$('#section-content') );
        },

        /**
        * Add new role
        */
        addRole: function (e) {
            e.preventDefault();

            var data = window.Misc.formToJson( e.target );
            this.model.save( data, {wait: true, patch:true} );
        },

        /**
        * response of the server
        */
        responseServer: function ( model, resp, opts ) {

            window.Misc.removeSpinner({
                wrap: this.$('#section-content'),
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

                    if(resp.redirect){
                        window.Misc.redirect( app.AppRouter.navigate( Route.route('rol.edit',{rol: this.model.get('id')}) ));
                    }
                }else {
                    window.Misc.showAlertBox(this.configAlert);
                    return false;
                }
            }
        }
    });
})(jQuery, this, this.document);
