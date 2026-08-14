/**
* Class DiscussionItemView extends of Backbone View
* @author TuProyecto || Desarrollador : @krobing
* @link http://TuProyecto.com
*/

//Global App Backbone
app || (app = {});

(function ($, window, document, undefined) {

    app.DiscussionItemView = Backbone.View.extend({

        tagName: 'article',
        className: 'discussion-post-box',
        template: _.template( ($('#discussion-item-tpl').html() || '') ),
        events: {
            'submit .form-comment-discussion': 'onStoreComment',
            // 'click .discussion-post-hide': 'onHide',
            'click .show-comment-field': 'onShowField'
        },
        parameters: {
            readonly: false
        },

        /**
        * Constructor Method
        */
        initialize : function(opts){

            // Extend options parameters
            if( opts.parameters !== undefined && _.isObject(opts.parameters) ){
                this.parameters = _.extend({}, this.parameters, opts.parameters);
            }

            //Init attributes & references
            this.$wrapperPosts = $('#wrapper-posts-discussion');
            this.collectionComments = new app.DiscussionCommentsList();
            this.$modalBase = $('#modal-base-tp');

            this.configAlert = {
                'wrapper': '#main-wrap',
                'closeTime': 6000,
                'speedOpen': 500,
                'speedClose': 500
            };

            // Events Listener
            this.listenTo( this.model, "change", this.render );
            this.listenTo( this.model, "change:contador_comentarios_discucion", this.incrementComments );
            this.listenTo( this.model, 'destroy', this.remove );
            this.listenTo( this.model, 'sync', this.responseServer );
            this.listenTo( this.collectionComments, 'add', this.countNewComment );
        },

        /*
        * Render View Element
        */
        render: function () {

            if( this.model.id === undefined || this.model.changed.contador_comentarios_discucion !== undefined )
                return this;

            var attributes = this.model.toJSON();
            attributes.readonly = this.parameters.readonly;

            this.$el.html( this.template(attributes) )

            this.$formComment = this.$('.form-comment-discussion');
            this.$commentsList = this.$('.comments-discussion-list');

            this.$dataCountComments = this.$('.data-count-comments');
            this.$containerMore = this.$('.container-more');

            // link show more
            window.Misc.showMore({'container': this.$containerMore, 'maxlen': 260});

            // to render view of the comment list
            this.viewComments = new window.app.DiscussionCommentListView({
                el: this.$commentsList,
                collection: this.collectionComments,
                parameters: {
                    dataFilter: {
                        'codigo_discucion': this.model.get('codigo_discucion')
                    }
                }
            });

            return this;
        },

        /**
        * Show comment field
        */
        onShowField: function (e) {
            e.preventDefault();

            this.showField();
            this.$formComment.find('.comment-discussion').focus();
        },
        /**
        * Show comments
        */
        showField: function () {
            this.$el.find('.discussion-comments-box').slideToggle(100);
            if( !this.collectionComments.length ){
                this.viewComments.render();
                this.viewComments.trigger('getdata');
                return;
            }

            this.viewComments.render();
        },

        /*
        * Remove View Element
        */
        remove: function(){

            var _this = this;
            this.$el.fadeOut('normal', function (){
                _this.$el.remove();
            });
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
        responseServer: function (target, resp, opts) {

            // response success or error
            var text = !resp.success ? resp.errors: '',
                type = resp.success ? 'success' : 'alert';


            if( _.isObject(resp.errors) ){

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
            };
            _.extend( this.configAlert, config );

            //complete proccess
            window.Misc.removeSpinner({
                'wrap': this.$wrapperPosts,
                'direct': false,
                'callback': (function (conf, re, $wrap) {
                    return function () {
                        $wrap.find('#spinner-load-posts').remove();

                        if( !re.success )
                            window.Misc.showAlertBox( conf );

                        if( re.success ){
                        	$wrap.find('#spinner-load-posts').remove();
                        }
                    }
                })(this.configAlert, resp, this.$wrapperPosts)
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
        }
    });

})(jQuery, this, this.document);
