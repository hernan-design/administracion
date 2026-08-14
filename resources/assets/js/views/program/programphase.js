/**
 * Class CourseThemeView extends of Backbone View
 * @autor TuProyecto || Desarrolladora : @dortegon
 * @link http://TuProyecto.com
 */

//Global App Backbone
app || (app = {});

(function ($, window, document, undefined) {

    app.ProgramPhaseView = Backbone.View.extend({
        template: _.template( ($('#item-phases-tpl').html() || '') ),
        events: {
            'click #add-program-phase': 'onModalProgramPhases',
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

            // Reference to elements
            this.$wrapperPhases = this.$('#accordion');

            // Events Listener
            this.listenTo( this.model, 'change', this.render );
            this.listenTo( this.collection, 'add', this.addOne );
            this.listenTo( this.collection, 'reset', this.addAll );
            this.listenTo( this.collection, 'sync', this.responseServer);
            this.listenTo( this.collection, 'request', this.loadSpinner );

            // Events Listener custome
            this.on( 'beforeRender', this.beforeRender );
            this.on( 'afterRender', this.afterRender );

            // Call addAll to render all phases on initialization
            this.addAll();
        },

        /*
        * Render View Element
        */
        render: function() {
            this.$formAddThemes = this.$('#form-add-themes-course');
        },

        /**
         * fires libraries js and functions
         */
        ready: function () {
            if( typeof window.initComponent.validateForm == 'function' ){
                window.initComponent.validateForm();
            }

            if( typeof window.initComponent.configNestable == 'function' ){
                window.initComponent.configNestable();
            }

            if( typeof window.initComponent.configEllipsis == 'function' ){
                window.initComponent.configEllipsis();
            }
        },

        /**
         * Render view comment by model
         * @param Object courseTheme Model instance
         */
        addOne: function (phase) {

            var view = new app.ProgramPhasesListView({
                model: phase,
                parameters: _.extend(this.parameters, {
                    template: 'templatePhase'
                })
            });

            this.$wrapperPhases.append( view.render().el );

            // this.resetFields( this.$formAddThemes.find('[name="fields_themes_course"]') );
        },

        /**
         * Render all view Course of the collection
         */
        addAll: function() {
            this.$wrapperPhases.html('');
            if (this.collection) {
                this.collection.forEach(this.addOne, this);
            } else {
                console.error('La colección está indefinida.');
            }
        },

        /**
         * add new development activity
         */
        onAddTheme: function (e) {
            e.preventDefault();

            var _this = this,
                data = window.Misc.formToJson(this.$formAddThemes),
                newData = _.pick(data, 'nombre_tema_curso', 'objetivo_tema_curso');
            newData['codigo_curso'] = this.model.get('codigo_curso');

            this.collection.create(newData, {success: function(model, resp, opts){

                    window.Misc.removeSpinner({
                        'wrap': this.$el,
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
                    _.extend(_this.configAlert, config);

                    //complete proccess
                    if( resp.success !== undefined ){
                        if( resp.success ){
                            window.Misc.showAlertBox( _.extend({title:'Mensaje'}, _this.configAlert) );
                        }else{
                            window.Misc.showAlertBox( _.extend({title:'Información'}, _this.configAlert) );
                        }
                    }
                }.bind(this)});
        },

        /**
         * Load spinner on the request
         */
        loadSpinner: function () {
            window.Misc.setSpinner( this.$wrapperThemes );
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

        /**
         * reset form fields
         * @param parentForm Element
         */
        resetFields: function (parentForm) {
            // reset inputs
            $(':input', parentForm).not(':button, :submit, :reset, :hidden, :checkbox, :radio').val('').removeAttr();

            this.ready();
        },

        onModalProgramPhases: function (e) {
            e.preventDefault();

            //  Program phase model
            this.phaseModel = new app.PhaseModel();

            this.listenToOnce( this.phaseModel, 'change:codigo_convocatoria', function (model) {
                this.collection.add(model);
            });

            this.programPhasesWindow = new app.ProgramPhasesWindow({
                model: this.phaseModel,
                parameters: {
                    template: 'templateAddPhase',
                    data:{
                        codigo_convocatoria: this.model.get('codigo_convocatoria'),
                        action: true    // TRUE: NEW PHASE / FALSE: UPDATE PHASE
                    }
                }
            });
            this.programPhasesWindow.render();
        }
    });

})(jQuery, this, this.document);
