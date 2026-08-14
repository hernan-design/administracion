/**
* Class CourseContentList of Backbone Collection
* @author TuProyecto || Desarrollador : @dortegon
* @link http://TuProyecto.com
*/

//Global App Backbone
app || (app = {});

(function (window, document, undefined) {

    app.CourseContentList = Backbone.Collection.extend({

        url: function() {
            return window.Misc.urlFull( Route.route('curso.tema.contenido.index') );
        },
        model: app.CourseContentModel,
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
            //
        },

   });

})(this, this.document);
