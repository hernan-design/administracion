/**
* Class EventSessionsListView extends of Backbone View
* @author TuProyecto || Desarrollador : @dortegon
* @link http://TuProyecto.com
*/

//Global App Backbone
app || (app = {});

(function ($, window, document, undefined) {

    app.EventSessionsListView = Backbone.View.extend({
        events: {
            'change .dd': 'onGetNestable',
            'click #add-event-session': 'onModalEventSessions'
        },
        parameters: {
            dataFilter: {}
        },

        /**
        * Constructor Method
        */
        initialize : function(opts) {

            // extends parameters
            if( opts.parameters !== undefined && _.isObject(opts.parameters) )
                this.parameters = $.extend({}, this.parameters, opts.parameters);

            this.configAlert = {
                'wrapper': '#main-wrap',
                'closeTime': 20000,
                'speedOpen': 500,
                'speedClose': 500
            };

            // Events Listener
            this.listenTo( this.collection, 'change', this.render );
			this.listenTo( this.collection, 'add', this.addSession );
			this.listenTo( this.collection, 'reset', this.addAll );
            this.listenTo( this.collection, 'request', this.loadSpinner );
			this.listenTo( this.collection, 'sync', this.responseServer);

            // Events Listener custome
            this.on( 'beforeRender', this.beforeRender );
            this.on( 'afterRender', this.afterRender );

        },

        /*
        * Render View Element
        */
        render: function() {
            // Reference to elements
            this.$wrapperSessions = this.$('#nestable');
        },

        /**
		* fires libraries js and functions
		*/
        ready: function () {
            if( typeof window.initComponent.configNestable == 'function' ){
                window.initComponent.configNestable();
            }

            if( typeof window.initComponent.validateForm == 'function' ){
                window.initComponent.validateForm();
            }

            if( typeof window.initComponent.configEllipsis == 'function' ){
                window.initComponent.configEllipsis();
            }
        },

        /**
		* Render view comment by model
		* @param Object session Model instance
		*/
		addSession: function (session) {

            var view = new app.EventSessionsItemView({
				model: session
			});

			this.$wrapperSessions.find('ol').append( view.render().el );

		},

        /**
		* Render all view Course of the collection
		*/
		addAll: function () {
			this.$wrapperSessions.html('');
			this.collection.forEach( this.addSession, this );
		},

        /**
        * Load spinner on the request
        */
        loadSpinner: function () {
            window.Misc.setSpinner( this.$wrapperSessions );
        },

        /**
        * response of the server
        */
        responseServer: function ( model, resp, opts ) {

            //complete proccess
            window.Misc.removeSpinner({
                'wrap': this.$el,
                'direct': false
            });
        },

        /*
        * Change position
        */
        onGetNestable: function (e) {
            var _this = this,
                sortedItems = $(e.target).nestable('serialize');
                
            _.each(sortedItems, function (sortItem, indx) {
                sortItem.numero_sesion = indx + 1;
            });

            $.post( window.Misc.urlFull( Route.route('evento.sesion.orden') ), {
                '_token': $('meta[name=csrf-token]').attr('content'),
                data: sortedItems
            }).success( function (resp){

                window.Misc.removeSpinner({
                    wrap: _this.$el,
                    direct: false
                });

                //complete proccess
                if( resp.success !== undefined ) {
                    if( resp.success ){
                        window.Misc.showAlertBox({
                            text: resp.message,
                            type: 'success',
                            closeTime: 5000
                        });

                        // Sort new collection
                        _this.collection.set(sortedItems, {merge:true, add:false, remove:false});
                    }else {
                        window.Misc.showAlertBox({
                            text: resp.errors,
                            type: 'alert',
                            closeTime: 5000
                        });

                        // Sort initial collection
                        _this.addAll();
                    }
                }
            });
        },

        /**
        * Modal event Sessions
        */
        onModalEventSessions: function (e) {
            e.preventDefault();

            //  Event session model
            this.sessionModel = new app.SessionModel();

            this.listenToOnce( this.sessionModel, 'change:codigo_evento', function (model) {
                this.collection.add(model);
            });
            this.eventSessionsWindow = new app.EventSessionsWindow({
                model: this.sessionModel,
                parameters: {
                    data:{
                        codigo_evento: this.model.get('codigo_evento'),
                        fecha_evento: this.model.get('fecha_evento'),
                        fecha_fin_evento: this.model.get('fecha_fin_evento')
                    }
                }
            });
            this.eventSessionsWindow.render();
        }
   });

})(window.jQuery, window, document);
