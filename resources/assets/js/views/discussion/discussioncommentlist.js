/**
* Class DiscussionCommentListView of Backbone View
* @author TuProyecto || Desarrollador : @xindykatalina
* @link http://TuProyecto.com
*/

//Global App Backbone
app || (app = {});

(function ($, window, document, undefined) {

    app.DiscussionCommentListView = Backbone.View.extend({

        className: 'comments-discussion-list',
        templateBtnMore: _.template( ($('#btn-view-more-tpl').html() || '') ),
        events: {
        },
        parameters: {
            dataFilter: {
                codigo_discucion: '',
                unique_view: false
            }
        },

        /**
        * Constructor Method
        */
        initialize : function(opts){

            _.bindAll(this, 'showMore');

            // Extend options parameters
            if( opts !== undefined && _.isObject(opts.parameters) ){
                this.parameters = _.extend({},this.parameters, opts.parameters);
            }

            //Init Attributes
            this.$wrapViewMore = $( this.templateBtnMore() );
            this.$spinnerComments = $('<div>').attr('id','spinner-load-comments');

            // Events Listener
            this.listenTo( this.collection, 'add', this.addOne );
            this.listenTo( this.collection, 'reset', this.addAll );
            this.listenTo( this.collection, 'store', this.storeOne );
            this.listenTo( this.collection, 'request', this.loadSpinner);
            this.listenTo( this.collection, 'sync', this.responseServer);
            // this.listenTo( this.collection, 'all', this.render);

            this.$wrapViewMore.on( 'click', '.btn-view-more', this.showMore);

            this.on('getdata', this.getFirstData);
            this.collection.setSorting('id_comentario', '1');
        },

        /*
        * Render View Element
        */
        render: function(){
            if(!this.parameters['unique_view']){
                this.$el.slideToggle('normal');
            }
            this.$el.parent().append( this.$wrapViewMore );
        },

        /**
        * Render view comment by model
        * @param Object commentDiscussionModel Model instance
        */
        addOne: function (commentDiscussionModel) {

            var view = new app.DiscussionCommentItemView({
                model: commentDiscussionModel
            });

            if( commentDiscussionModel.isNew() || commentDiscussionModel.changed.id_comentario !== undefined ) {
                //anteponer
                this.$el.prepend( view.render().el );
            }else{
                //adjuntar
                this.$el.append( view.render().el );
            }
        },

        /**
        * Render all view comment of the collection
        */
        addAll: function () {

            this.$el.html('');
            this.collection.forEach( this.addOne, this );
        },

        /**
        * stores a forum comment
        * @param form element
        */
        storeOne: function ( data, form ) {
            this.collection.create( data, {patch:true, wait:true} );
            $(form).find(':submit').addClass('disabled');

        },

        /**
        * Load spinner on the request
        */
        loadSpinner: function ( target, xhr, opts ) {

            this.$elmSpinner = this.$wrapViewMore;

            if( target instanceof Backbone.Collection ){
                this.$spinnerComments.appendTo( this.$elmSpinner );
            }else{
                this.$spinnerComments.prependTo( this.$elmSpinner );
            }

            window.Misc.setSpinner( this.$elmSpinner );
        },

        /**
        * response of the server
        */
        responseServer: function ( target, resp, opts ) {
            //complete process
            window.Misc.removeSpinner({
                'wrap': this.$elmSpinner,
                'direct': false,
                'callback': (function ($spinner) {
                    return function () {
                        $spinner.remove();
                    };
                })(this.$spinnerComments)
            });

            if( this.collection.state.currentPage >= this.collection.state.lastPage){
                this.$wrapViewMore.hide();
            }else{
                this.$wrapViewMore.show();
            }
        },

        /**
        * get reset data
        *
        */
        getFirstData: function  (dataFilter) {

            dataFilter || (dataFilter = {});

            _.extend(this.parameters.dataFilter, dataFilter);

            this.$wrapViewMore.hide();
            this.collection.getFirstPage( {reset:true, data: this.parameters.dataFilter} );
        },

        /**
        * Show more comments
        */
        showMore: function (e) {
            e.preventDefault();

            if( this.collection.hasNextPage() ){
                this.collection.getNextPage( {wait:true, data: this.parameters.dataFilter} );
            }
        }
   });

})(jQuery, this, this.document);
