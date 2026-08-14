/**
* Class RolesUserList extends of Backbone Collection
* @author TuProyecto || Desarrollador : @dortegon
* @link http://TuProyecto.com
*/

//Global App Backbone
app || (app = {});

(function (window, document, undefined) {

    app.RolesUserList = Backbone.Collection.extend({

        url: function () {
            return window.Misc.urlFull( Route.route('usuario.rol.index') );
        },
        model: app.RoleUserModel,

        /**
        * Constructor Method
        */
        initialize : function() {
            //
        }
   });

})(this, this.document);
