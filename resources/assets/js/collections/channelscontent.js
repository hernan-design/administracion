/**
* Class ChannelsContentList of Backbone Collection
* @author TuProyecto || Desarrollador : @dortegon
* @link http://TuProyecto.com
*/

//Global App Backbone
app || (app = {});

(function (window, document, undefined) {

    app.ChannelsContentList = Backbone.Collection.extend({

        url: function() {
            return window.Misc.urlFull( Route.route('contenidocanal.index') );
        },
        model: app.ChannelContentModel,

        /**
        * Constructor Method
        */
        initialize : function(){
            //
        },

   });

})(window, document);
