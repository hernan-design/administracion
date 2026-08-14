/**
* Class ModulePermissionsList extends of Backbone Collection
* @author TuProyecto || Desarrollador : @dortegon
* @link http://TuProyecto.com
*/

//Global App Backbone
app || (app = {});

(function (window, document, undefined) {

    app.ModulePermissionsList = Backbone.Collection.extend({
        url: function () {
            return window.Misc.urlFull( Route.route('rol.permiso.index') );
        },
        model: app.ModulePermissionModel,

        /**
        * Constructor Method
        */
        initialize : function(){
        },
   });

})(this, this.document);
