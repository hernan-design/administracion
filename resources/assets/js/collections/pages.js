/**
 * Class PageList of Backbone Collection
 * @author TuProyecto || Desarrollador : @backend01
 * @link http://TuProyecto.com
 */

//Global App Backbone
app || (app = {});

(function (window, document, undefined) {
    app.PageList = Backbone.Collection.extend({
        mode: 'server',
        url: function() {
            return window.Misc.urlFull( Route.route('formulario.index') );
        },
        model: app.PagesModel,
        state: {
            //
        },

        /**
         * Constructor Method
         */
        initialize : function(){
            //
        },
    })
})(this, this.document);
