/**
* Class DiscussionView of Backbone View
* @author TuProyecto || Desarrollador : @xindykatalina
* @link http://TuProyecto.com
*/

//Global App Backbone
app || (app = {});

(function ($, window, document, undefined) {

    app.DiscussionView = Backbone.View.extend({
        el: '#discussion-content',
        tagName: 'article',
        className: 'discussion-post-box',
        template: _.template( ($('#discussion-tpl').html() || '') ),
        templateComments: _.template( ($('#discussion-item-tpl').html() || '') ),
        events: {
            'submit .form-comment-discussion': 'onStoreComment',
            'click .discussion-post-hide': 'onHide',
            'click .post-footer-box .show-user-likes': 'onShowUserLikes'
        },
        parameters: {
            //
        },

        /**
        * Constructor Method
        */
        initialize : function(opts){

            _.bindAll(this, 'beforeRender', 'render', 'afterRender');

            // Extend options parameters
            if( opts.parameters !== undefined && _.isObject(opts.parameters) ){
                this.parameters = _.extend({}, this.parameters, opts.parameters);
            }

            //Init Attributes
            this.$wrapperPosts = $('#wrapper-posts-discussion');
            this.collectionComments = new app.DiscussionCommentsList();
            this.discussionParticipantsList = new app.DiscussionParticipantsList();
            this.likeList = new app.LikeList();
            this.$modalBase = $('#modal-base-tp');

            var _this = this;
            this.render = _.wrap(this.render, function(render) {

                _this.trigger('beforeRender');

                render();

                _this.trigger('afterRender');

                return _this;
            });

            this.configAlert = {
                'wrapper': '#main-wrap',
                'closeTime': 6000,
                'speedOpen': 500,
                'speedClose': 500
            };
            // Events Listener
            this.listenTo( this.model, 'change:id_discucion', this.render );
            this.listenTo( this.model, "change:contador_comentarios_discucion", this.incrementComments );
            this.listenTo( this.model, 'destroy', this.remove );
            this.listenTo( this.model, 'sync', this.responseServer );
            this.listenTo( this.collectionComments, 'add', this.countNewComment );

            // Events Listener custome
            this.on( 'beforeRender', this.beforeRender );
            this.on( 'afterRender', this.afterRender );
        },

        /*
        * Render View Element
        */
        render: function () {

            this.attributes = this.model.toJSON();

            this.$el.html( this.template(this.attributes ) )

            // link show more
            window.Misc.showMore({'container': this.$containerMore, 'maxlen': 260});

            // overwrite model id attribute
            if( this.model.changed.id_discucion !== undefined ) {
                // Overwrite id attribute of the model
                this.model.idAttribute = 'codigo_discucion';
                this.model.set( {'codigo_discucion': this.model.get('codigo_discucion')}, {'silent':true} );

                // reference to views
                this.renderDiscussionComments();
                this.referenceViews();
            }
        },

        /**
        * Fires before of run render function
        */
        beforeRender: function () {
            //
        },

        /**
        * Fires before of run render function
        */
        afterRender: function () {
            this.ready();
        },

        /**
        * fires libraries js
        */
        ready: function () {

            var _this = this;

            // trigger libraries
            if( _.has($.fn, 'foundation') ) {
            	$(document).foundation('abide', 'reflow');
            }

            // reload plugins
            if( typeof window.initComponent.initConfigForm == 'function' )
            	window.initComponent.initConfigForm();
        },

        /**
        * Reference to views
        */
        referenceViews: function () {

            this.$wrapperParticipants = this.$('#participant-discussion');
            this.$formComment = this.$('.form-comment-discussion');
            this.$commentsList = this.$('.comments-discussion-list');
            this.$dataCountComments = this.$('.data-count-comments');
            this.$containerMore = this.$('.container-more');
            this.$likeLink = this.$('.post-footer-box .TPlikelink');

            this.$countLikes = this.$('.post-footer-box .data-count-likes');

            this.$el.find('.discussion-comments-box').slideToggle(100);
            this.$el.find('.comments-discussion-list').slideToggle(100);

            // instance of user participants discussion view
            this.discussionParticipantsListView = new app.DiscussionParticipantsListView({
                el: this.$wrapperParticipants,
                collection: this.discussionParticipantsList,
                parameters: {
                    'discussion': this.model.get('codigo_discucion')
                }
            });

            //to render like link for post
            this.likeLinkView = new app.LikeView({
                el: this.$likeLink,
                collection: this.likeList,
                attributes: {
                    'data-srcparams': this.model.get('srcparams')
                }
            });
            this.likeLinkView.$countLikes = this.$countLikes;

            // to render view of the comment list
            this.viewComments = new app.DiscussionCommentListView({
                el: this.$commentsList,
                collection: this.collectionComments,
                parameters: {
                    fecha_discusion: this.model.get('fecha_discusion'),
                    unique_view: true,
                    dataFilter: {
                        'codigo_discucion': this.model.get('codigo_discucion')
                    }
                }
            });

            if( !this.collectionComments.length ){
                this.viewComments.render();
                this.viewComments.trigger('getdata');
                return;
            }
        },

        /*
        * Remove View Element
        */
        remove: function(){

            var _this = this;
            this.$el.fadeOut('normal', function (){
                _this.$el.remove();
            });
            url = document.url + this.attributes['origin']['url_origen'];
            return window.Misc.redirect(url);
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
        responseServer: function ( model, resp, opts) {

            window.Misc.removeSpinner({
                'wrap': this.$el,
                'direct': true
            });
        },

        /**
        * hides discussion post
        */
        onHide: function (e) {
            e.preventDefault();

            var _this = this,
            	cancelConfirm = new app.ConfirmWindow({
            		model: this.model,
	                parameters: {
	                    template: _.template($("#discussion-confirm-hide-tpl").html() || ""),
	                    titleConfirm: "Eliminar Entrada"
	                }
	            });

            cancelConfirm.render();
        },

        /**
        * show user likes
        */
        onShowUserLikes: function (e) {
            e.preventDefault();

            // to render view user likes
            this.viewUserLikes = new window.app.UserLikesListView({
                attributes: {
                    'data-srcparams': this.model.get('srcparams')
                }
            });
            this.$modalBase.find('.content-modal').html( this.viewUserLikes.render().el );
            this.$modalBase.find('.content-modal').append( this.viewUserLikes.$wrapViewMore );

            this.$modalBase.foundation('reveal','open');
        },

        /**
        * Count the new commnet
        */
        countNewComment: function () {
            var count = parseInt( this.model.get('contador_comentarios_discucion') );
            count++;

            this.model.set( {'contador_comentarios_discucion': count} );
        },

        /**
        *  increment comments value
        */
        incrementComments: function (model, value) {
            this.$dataCountComments.html( value );
        },

        /**
        * Event for store a comment
        */
        onStoreComment: function (e) {
            e.preventDefault();

            var data = window.Misc.formToJson( e.target );

            data.texto_comentario_discucion = window.Misc.stripTags(data.texto_comentario_discucion, 'a');
            data.texto_comentario_discucion = data.texto_comentario_discucion.replace(/(\&nbsp;|<[a-z\?\!\/]([a-z0-9\_\:\.])*(\s[^>]*)?(>|$))/gi, ' $&');
            data.texto_comentario_discucion = window.Misc.urlWrapLink(data.texto_comentario_discucion);

            this.collectionComments.trigger('store', data, e.target);
        },

        /**
        * render info Discussion Comments
        */
        renderDiscussionComments: function (model, value, opts) {

            this.$('#discussion-posts-wrap').html( this.templateComments(this.model.toJSON()) );
            this.ready();
        }
    });

})(jQuery, this, this.document);
