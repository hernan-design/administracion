/**
* Class MentorDetailView extends of Backbone View
* @author TuProyecto || Desarrollador : @dortegon
* @link http://TuProyecto.com
*/

//Global App Backbone
app || (app = {});

(function ($, window, document, undefined) {

    app.MentorDetailView = Backbone.View.extend({
        el: '#mentor-content',
        events: {
            'click #sessions-tab': 'renderTableListSessions',
        },
        parameters: {
            codigo_asesoria: null
        },

        /**
         * Constructor Method
         */
        initialize : function(opts) {
            // extends parameters
            if( opts.parameters !== undefined && _.isObject(opts.parameters) )
                this.parameters = $.extend({}, this.parameters, opts.parameters);

            //Init Attribute
            this.discussionList = new app.DiscussionMentoringList();

            this.sessionsListView = new app.SessionsListView();
        },

        /*
         * Render View Element
         */
        render: function() {
            // references
            this.$wrapperDiscussion = this.$('#wrapper-sale-agreement-discussion');

            // instance to discussion
            this.discussionListView = new app.DiscussionListView({
                el: this.$wrapperDiscussion,
                collection: this.discussionList,
                parameters: {
                    readonly: true,
                    dataFilter: {
                        'codigo_asesoria' : this.parameters.codigo_asesoria
                    }
                }
            });
        },

        /**
         * reference to views
         */
        renderTableListSessions: function () {
            this.sessionsListView.render();
        },
    });

})(window.jQuery, window, document);
