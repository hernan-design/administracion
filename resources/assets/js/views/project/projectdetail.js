/**
* Class ProjectDetailView extends of Backbone View
* @author TuProyecto || Desarrollador : @dortegon
* @link http://TuProyecto.com
*/

//Global App Backbone
app || (app = {});

(function ($, window, document, undefined) {

    app.ProjectDetailView = Backbone.View.extend({

        el: '#user-content',
        events: {
            //
        },
        parameters: {
            entity: ''
        },

        /**
        * Constructor Method
        */
        initialize : function(opts) {

            if( _.isObject(opts) && opts.parameters ) {
                this.parameters = _.extend({}, this.parameters, opts.parameters);
            }

            this.mentoringList = new app.MentoringList();

        },

        /*
        * Render View Element
        */
        render: function() {

            this.onShowActivitiesLog();

            var projectMentorsView = new app.PojectMentorsListView({
                collection: this.mentoringList,
                parameters: {
                    data: {
                        entity: this.parameters.entity
                    }
                }
            });

            projectMentorsView.trigger('getdata', {entity: this.parameters.entity});
        },

        // show activities log
        onShowActivitiesLog: function () {
            $.get(window.Misc.urlFull( Route.route('entidad.index', {
                entity: this.parameters.entity
            }) ) )
            .done(function( data ) {
                if ( this.viewActivityLog instanceof Backbone.View ){
                    this.viewActivityLog.stopListening();
                    this.viewActivityLog.undelegateEvents();
                }

                this.viewActivityLog = new app.ActivityLogView({
                    parameters: {
                        'subject': data.codigo_entidad,
                        'subject_type' : _.escape('App.Models.Core.Entidad'),
                    }
                });
            });
        }
   });

})(jQuery, this, this.document);
