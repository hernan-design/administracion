/**
* Class DiscussionListView extends of Backbone View
* @author TuProyecto || Desarrollador : @krobing
* @link http://TuProyecto.com
*/

//Global App Backbone
app || (app = {});

(function ($, window, document, undefined) {

    app.DiscussionListView = Backbone.View.extend({

        el: '#wrapper-posts-discussion',
        templateBtnMore: _.template( ($('#btn-view-more-tpl').html() || '') ),
        events: {
            //
        },
        parameters: {
            readonly: false,
        	formDiscussion: '',
        	postsWrap: '#discussion-posts-wrap',
            dataFilter: {}
        },

        /**
        * Constructor Method
        */
        initialize : function(opts){
            _.bindAll(this, 'showMore');

            // Extend options parameters
            if( opts.parameters !== undefined && _.isObject(opts.parameters) ){
                this.parameters = _.extend({}, this.parameters, opts.parameters);
            }

            //Init attributes and references
            this.$wrapViewMore = $( this.templateBtnMore() );
            this.$postsWrap = this.$( this.parameters['postsWrap'] );

            this.$textDiscussion = this.$('#texto_discucion');
            this.$noResults = this.$('.no-results-filter');
            this.$spinnerPosts = $('<div>').attr('id','spinner-load-posts');
            this.pageOld = this.collection.state.currentPage;

            // Events Listener
            this.listenTo( this.collection, 'add', this.addOne );
            this.listenTo( this.collection, 'reset', this.addAll );
            this.listenTo( this.collection, 'store', this.storeOne );
            this.listenTo( this.collection, 'request', this.loadSpinner);
            this.listenTo( this.collection, 'sync', this.responseServer);
            this.listenTo( this.collection, 'all', this.render);

            // customize events
            this.on('getdata', this.getFirstData);
            this.$wrapViewMore.on( 'click', '.btn-view-more', this.showMore);

            this.trigger('getdata');
        },

        /*
        * Render View Element
        */
        render: function(){

            if( this.collection.length )
                this.$noResults.css('display', 'none');

            this.$el.append( this.$wrapViewMore );

            // renference to elements
            this.$formDiscussion = this.$(this.parameters['formDiscussion']);

            // delegate events
            var delegatesEvents = {};
            delegatesEvents[ 'submit '+ this.parameters['formDiscussion'] ] = 'onStore';
            this.delegateEvents(delegatesEvents);

            return this;
        },

        /**
        * get reset data
        *
        */
        getFirstData: function  (dataFilter, replace) {
            dataFilter || (dataFilter = {});
			replace || (replace = false);

            this.$wrapViewMore.hide();

			if(replace) {
				this.parameters.dataFilter = dataFilter;
			}else{
				_.extend( this.parameters.dataFilter, dataFilter );
			}

			this.collection.getFirstPage( {reset:true, data:_.clone(this.parameters.dataFilter)} );
			this.pageOld = this.collection.state.currentPage;
        },

        /**
        * Render view comment by model
        * @param Object discussionModel Model instance
        */
        addOne: function (discussionModel) {

            var view = new app.DiscussionItemView({
                model: discussionModel,
                parameters: {
                    readonly: this.parameters.readonly
                }
            });

            if( discussionModel.isNew() ) {
                this.$postsWrap.prepend( view.render().el );
            }else{
                this.$postsWrap.append( view.render().el );
            }
        },

        /**
        * Render all view comment of the collection
        */
        addAll: function () {

            this.$postsWrap.html('');
            this.collection.forEach( this.addOne, this );
        },

        /**
        * stores a forum comment
        * @param form element
        */
        storeOne: function ( data ) {

            this.collection.create( data, {patch:true, wait:false} );
            this.$formDiscussion.find(':submit').addClass('disabled');
            // window.Misc.setSpinner( this.$formDiscussion );
        },

        /**
        * Store posts
        */
        onStore: function (e) {
            e.preventDefault();

            var data = window.Misc.formToJson( e.target );


            // extend with data filters
            _.extend(data, this.parameters.dataFilter);

            data.texto_discucion = window.Misc.stripTags( data.texto_discucion, 'a' );
            data.texto_discucion = data.texto_discucion.replace(/(\&nbsp;|<[a-z\?\!\/]([a-z0-9\_\:\.])*(\s[^>]*)?(>|$))/gi, ' $&');
            data.texto_discucion = window.Misc.urlWrapLink( data.texto_discucion );

            this.collection.trigger('store', data);
        },

        /**
        * Load spinner on the request
        */
        loadSpinner: function ( target, xhr, opts ) {

            this.$noResults.css('display', 'none');

            this.$elmSpinner = this.$wrapViewMore;

            if( target instanceof Backbone.Collection ){

                this.$spinnerPosts.appendTo( this.$elmSpinner );
            }else{

                this.$spinnerPosts.prependTo( this.$elmSpinner );
            }

            window.Misc.setSpinner( this.$elmSpinner );
        },

        /**
        * response of the server
        */
        responseServer: function ( target, resp, opts ) {

            if( !target.length )
                this.$noResults.fadeIn('fast');

            // complete process
            window.Misc.removeSpinner({
                'wrap': this.$elmSpinner,
                'direct': false,
                'callback': (function ($formDiscussion, $elSpinner) {
                    return function () {
                        $formDiscussion.find( '[name="texto_discucion"]' ).val('');
                        $formDiscussion.find(':submit').removeClass('disabled');
                        $elSpinner.remove();
                    };
                })(this.$formDiscussion, this.$spinnerPosts)
            });

            if( this.collection.state.currentPage >= this.collection.state.lastPage){
                this.$wrapViewMore.hide();
            }else{
                this.$wrapViewMore.show();
            }
        },

        /**
        * Show more discussion program
        */
        showMore: function (e) {
            e.preventDefault();

            if( this.collection.hasNextPage() ){
                this.collection.getNextPage( {wait:true, data: this.parameters['dataFilter']} );
            }
        }
   });

})(jQuery, this, this.document);
