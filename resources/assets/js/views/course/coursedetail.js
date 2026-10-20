/**
* Class CourseDetailView extends of Backbone View
* @author TuProyecto || Desarrollador : @rfigueredo
* @link http://TuProyecto.com
*/

//Global App Backbone
app || (app = {});

(function ($, window, document, undefined) {

    app.CourseDetailView = Backbone.View.extend({

        el: '#course-content',
        templateConfirm: _.template( ($('#confirm-form-tpl').html() || '') ),
        templateButtonsAction: _.template( ($('#buttons-action-tpl').html() || '') ),
        events: {
            'click .btn-search': 'search',
            'submit #form-filter': 'search',
            'click .btn-clear': 'clear',
            'click .success': 'onModalConfirm',
            'click .danger': 'onModalConfirm'
        },
        parameters: {
            dataFilter: {},
            course: null
        },

        /**
         * Constructor Method
         */
        initialize : function(opts) {

            // extends parameters
             if( opts.parameters !== undefined && _.isObject(opts.parameters) ) {
                this.parameters = $.extend({}, this.parameters, opts.parameters);
            }



            var _this = this;


            // Events Listener custome
            this.on( 'beforeRender', this.beforeRender );
            this.on( 'afterRender', this.afterRender );

        },

        /*
         * Render View Element
         */
        render: function() {

            // Initialize attributes
            this.$formFilters   = this.$('#form-filter');
            this.$searchState   = this.$('#codigo_estado');
            this.$searchNombre  = this.$('#nombre_evaluacion');
            this.$formListSearchTable   = this.$('#form-list-search-table');

            // overwrite model id attribute
           this.referenceViews();
        },

         /**
         * reference to views
         */
        referenceViews: function () {

            var _this = this;
            this.formListSearchTable = this.$formListSearchTable.DataTable({
                dom:"<'row'<'col-sm-5'i><'col-sm-7'p>>" +
                "<'row'<'col-sm-12'tr>>" +
                "<'row'<'col-sm-5'i><'col-sm-7'p>>",
                processing: true,
                serverSide: true,
                language: window.Misc.dataTableES(),
                ajax: {
                    url: window.Misc.urlFull( Route.route('curso.evaluacion.index') ),
                    data: function( data ) {
                        data.nombre_encuesta  = _this.$searchNombre.val();
                        data.codigo_estado  = _this.$searchState.val();
                        data.codigo_curso = _this.parameters.course;
                    },
                },
                columns: [
                    { data: 'nombre_encuesta', name: 'nombre_encuesta' },
                ],
                order: [
                    [ 0, 'asc' ]
                ],
                columnDefs: [
                    {
                        targets: 0,
                        render: function ( data, type, full, row ) {
                            var ucFirst = full.nombre_encuesta.charAt(0).toUpperCase() + full.nombre_encuesta.slice(1);

                            return '<a href="' + window.Misc.urlFull(Route.route('curso.evaluacion.show', {evaluacion: full.codigo_encuesta, curso: _this.parameters.course}) ) + '" title="Ver formulario" style="color:#3FC1A5">'+full.nombre_encuesta+'</a>';
                        }
                    },
                    {
                        targets: 1,
                        width: '10%',
                        orderable: false,
                        className: 'text-center',
                        render: function ( data, type, full, row ) {
                            return _this.templateButtonsAction( {full: full} )
                        }
                    }
                ]
            });
        },

        /**
        * Load spinner on the request
        */
        loadSpinner: function () {
            window.Misc.setSpinner( this.$gridSearchTable );
        },

        search: function(e) {
            e.preventDefault();
            this.formListSearchTable.ajax.reload();
        },

        clear: function(e) {
            e.preventDefault();

            this.$searchState.val('');
            this.$searchNombre.val('');

            this.formListSearchTable.ajax.reload();
        },

        // Modal confirm
        onModalConfirm: function (e) {
            e.preventDefault();
            this.formModel = new app.FormModel();
            this.formModel.idAttribute = 'codigo_encuesta';
            this.formModel.set( {'codigo_encuesta': $(e.currentTarget).data('id')});
            this.formModel.fetch();
            this.listenToOnce( this.formModel, 'request', this.loadSpinner );

            var _this = this,
                action = $(e.currentTarget).attr('data-action'),
                form_name = $(e.currentTarget).attr('data-name'),
                data = {
                    'id': $(e.currentTarget).data('id'),
                    'change_state': true
                };

            if( this.acceptConfirm instanceof Backbone.View ) {
                this.acceptConfirm.stopListening();
                this.acceptConfirm.undelegateEvents();
            }

            this.acceptConfirm = new app.ConfirmWindow({
                parameters: {
                    template: this.templateConfirm({
                        action : action,
                        form_name: form_name
                    }),
                    titleConfirm: "Confirmación",
                    onConfirm: function () {
                        _this.formModel.save(data, {wait: true, patch: true,
                            success: function (model, resp) {
                                // complete proccess
                                window.Misc.removeSpinner({
                                    'wrap': _this.$el,
                                    'direct': false
                                });

                                // response success or error
                                var type = resp.success ? 'success' : 'alert',
                                    text = resp.success ?
                                        (resp.message !== undefined ? resp.message : '...')
                                        : resp.errors;

                                if( _.isObject( resp.errors ) ){

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
                                }
                                confCrop =  $.extend({}, _this.configAlert, config);

                                //complete proccess
                                if( resp.success !== undefined ){
                                    if( resp.success ){
                                        window.Misc.showAlertBox( confCrop );
                                        _this.formListSearchTable.ajax.reload( null, false );
                                    }else{
                                        window.Misc.showAlertBox( confCrop );
                                    }
                                }
                            }
                        });
                    }
                }
            });

            this.acceptConfirm.render();
        }
    });

})(window.jQuery, window, document);
