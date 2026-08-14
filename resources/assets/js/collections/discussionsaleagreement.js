/**
* Class DiscussionSaleAgreementList extends of Backbone Collection
* @author TuProyecto || Desarrollador : @krobing
* @link http://TuProyecto.com
*/

//Global App Backbone
app || (app = {});

(function (window, document, undefined) {

	app.DiscussionSaleAgreementList = Backbone.PageableCollection.extend({

		mode: 'server',
		url: function (){
		   return window.Misc.urlFull( Route.route('marketplace.transacciones.discusion.index') );
		},
		model: app.DiscussionSaleAgreementModel,

		state: {
			firstPage: 0,
			pageSize: 15 //number of records to show by page
		},

		queryParams: {
			totalPages: null,
			totalRecords: null,
			pageSize: "limit",
			currentPage: null,
			offset: function () { return this.state.currentPage * this.state.pageSize; }
		},

		/**
		* Constructor Method
		*/
		initialize : function(){
		},

		/**
		* parse state of the paginator collection
		*/
		parseState: function (resp, queryParams, state, options) {
			return {totalRecords: resp.total_entries};
		},

		/**
		* Return all the posts
		* @param Object resp, response of the server
		*/
		parseRecords: function (resp) {
			return resp.items;
		}
   });

})(this, this.document);
