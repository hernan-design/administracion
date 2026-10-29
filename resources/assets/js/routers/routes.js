/**
* Class AppRouter of Backbone Router
* @author TuProyecto || @dropecamargo
* @link http://tuproyecto.com
*/

//Global App Backbone
app || (app = {});

(function ($, window, document, undefined) {

    app.AppRouter = new( Backbone.Router.extend({
        routes : {
            '(/)': 'getDashboard',
            'login': 'getLogin',
            'menu(/)': 'getMenuMain',
            'convocatoria(/)': 'getProgramMain',
            'convocatoria/create': 'getProgramCreate',
            'convocatoria/:convocatoria/edit': 'getProgramEdit',
            'convocatoria/categoria(/)': 'getCategoryProgramMain',
            'convocatoria/categoria/create': 'getCategoryProgramCreate',
            'convocatoria/categoria/:categorium/edit': 'getCategoryProgramEdit',
            'convocatoria/:convocatoria(?*queryString)': 'getProgram',
            'convocatoria/fase/documentador/:documentador': 'getDocumenter',
            'convocatoria/fase/compromiso/:compromiso': 'getCommitment',
            'convocatoria/respuestas/evaluacion/:evaluacion': 'getResponseEvaluation',
            'convocatoria/:convocatoria/asignacionmentor/:nickname_usuario': 'getAssignmentMentor',
            'convocatoria/fase/voucher/:codigo_registro_fase_usuario': 'assignmentVoucher',
            'institucion': 'getInstitutionMain',
            'institucion/create': 'getInstitutionCreate',
            'institucion/:institucion/edit': 'getInstitutionEdit',
            'contenido': 'getArticleMain',
            'contenido/create': 'getArticleCreate',
            'contenido/:article/edit': 'getArticleCreate',
            'curso(/)': 'getCourseMain',
            'curso/create': 'getCourseCreate',
            'curso/evaluacion/:evaluation(?*queryString)':'getCourseEvaluation',
            'curso/:course/edit': 'getCourseEdit',
            'curso/:course(?*queryString)': 'getCourse',
            'evaluacion/comentario/create': 'getEvaluationCommentCreate',
            'evaluacion/comentario(/)': 'getEvaluationCommentMain',
            'evaluacion/asignacion': 'getAssignmentMain',
            'evaluacion/asignacion/:asignacion': 'getAssignment',
            'evaluacion(/)': 'getEvaluationMain',
            'evaluacion/create': 'getEvaluationCreate',
            'evaluacion/:evaluacion/edit': 'getEvaluationEdit',
            'evaluacion/:evaluacion(?*queryString)': 'getEvaluation',
            'marketplace(/)': 'getProductMain',
            'marketplace/transacciones(/)': 'getTransactionsMain',
            'marketplace/transacciones/acuerdo/:acuerdo': 'getAgreement',
            'marketplace/transacciones/evaluacion/:compra': 'getMarketplaceEvaluation',
            'marketplace/vouchers(/)': 'getMarketplaceVouchers',
            'marketplace/vouchers/:vouchers': 'getMarketplaceVoucher',
            'marketplace/asignaciones(/)': 'getMarketplaceAssignmentsMain',
            'marketplace/asignaciones/:assignment': 'getMarketplaceAssignment',
            'marketplace/promociones(/)': 'getMarketplacePromotionsMain',
            'mentorias/masivas': 'viewMentoriasMasivas',
            'mentorias/masivas/lista': 'ListaMentoriaMasivaView',
            'mentorias/masivas/:mentoria': 'MentoriaMasivaShowView',

            'usuario/profesion(/)' : 'getProfessionMain',
            'usuario/profesion/create': 'getProfessionCreate',
            'usuario/profesion/:profesion/edit': 'getProfessionEdit',
            'usuario/ocupacion(/)' : 'getOccupationMain',
            'usuario/ocupacion/create': 'getOccupationCreate',
            'usuario/ocupacion/:ocupacion/edit': 'getOccupationEdit',
            'usuario(/)' : 'getUserMain',
            'usuario/create(/)' : 'getUserCreate',
            'usuario/:usuario': 'getUserDetail',
            'rol(/)' : 'getRoleMain',
            'rol/create': 'getRoleCreate',
            'rol/:rol/edit': 'getRoleEdit',
            'reportes/rutan/convocatorias' : 'export_report_convocatoria_rutan',
            'comunidad/canal(/)' : 'getChannelMain',
            'comunidad/canal/create': 'getChannelCreate',
            'comunidad/canal/:canal/edit(?*queryString)': 'getChannelEdit',
            'comunidad/categoria(/)' : 'getCategoryMain',
            'comunidad/categoria/create': 'getCategoryCreate',
            'comunidad/categoria/:categoria/edit': 'getCategoryEdit',
            'comunidad/publicacion(/)': 'getPublicacion',
            'mentoria/mentores(/)' : 'getMentorsMain',
            'mentoria(/)' : 'getMentorMain',
            'mentoria/:mentoria': 'getMentor',
            'formulario(/)' : 'getFormMain',
            'formulario/create': 'getFormCreate',
            'formulario/:formulario/edit': 'getFormEdit',
            'formulario/:formulario': 'getForm',
            'evento(/)' : 'getEventMain',
            'eventos/usuario(/)' : 'getEventsForUser',
            'evento/create': 'getEventCreate',
            'evento/:evento/edit': 'getEventEdit',
            'diagnosticos(/)' : 'getDiagnosticMain',
            'diagnosticos/create': 'getDiagnosticCreate',
            'diagnosticos/:diagnostico/edit': 'getDiagnosticEdit',
            'asignacion-diagnostico(/)' : 'getDiagnosticModuleMain',
            'entidad/create(/)' : 'getProjectCreate',
            'entidad/:entidad/edit' : 'getProjectEdit',
            'entidad(/)' : 'getProjectMain',
            'entidad/etapa(/)' : 'getProjectStagesMain',
            'entidad/etapa/create': 'getProjectStageCreate',
            'entidad/etapa/:etapa/edit': 'getProjectStageEdit',
            'entidad/industria(/)' : 'getIndustryMain',
            'entidad/industria/create': 'getIndustryCreate',
            'entidad/industria/:industrium/edit': 'getIndustryEdit',
            'entidad/:entidad' : 'getProjectDetail',
            'foro(/)' : 'getForumMain',
            'foro/categoria(/)' : 'getForoCategoryMain',
            'reportes/gestion': 'getGestionMain',
            'notificacion/output' : 'getNotificationOutputMain',
            'notificacion/output/create' : 'getNotificationOutputCreate',
            'reportes(/)': 'getReports'
        },

        /**
        * Constructor Method
        */
        initialize : function ( opts ){

            // Initialize resources
            this.globalView = new app.GlobalView();

            // Initialize resource view
            this.searchUserView = new app.ComponentSearchUserView();

            // Initialize resource view
            // this.createUserView = new app.ComponentCreateUserView();

            // Roles user Resources
            this.rolesUserList = new app.RolesUserList();

            // Users role Resources
            this.usersRoleList = new app.UsersRoleList();

            // Users channel Resources
            this.channelUsersList = new app.UsersChannelList();

            // Module Permissions Resources
            this.modulePermissionModel = new app.ModulePermissionModel();

            // Program Resources
            this.programList = new app.ProgramList();

            // Institution Resources
            this.institutionList = new app.InstitutionList();

            // Program phases
            this.programPhasesList = new app.ProgramPhasesList();

            // Course Resources
            this.courseList = new app.CourseList();

            // Course Themes Resources
            this.courseThemesList = new app.CourseThemeList();

            // Evaluation Resources
            this.evaluationList = new app.EvaluationList();

            // Voucher resources
            this.marketplaceVouchersView = new app.MarketplaceVouchersView();

            // Marketplace resources
            this.marketplaceAssignmentsView = new app.MarketplaceAssignmentsView();
        },

        /**
        * Start Backbone history
        */
        start: function () {
            var config = { pushState: true };

            if( document.domain.search("localhost|192.168.0.3") != '-1')
                config.root = '/administrator/public/';

            Backbone.history.start( config );
        },

        /**
        * Parse queryString to object
        */
        parseQueryString : function(queryString) {
            var params = {};
            if(queryString) {
                _.each(
                    _.map(decodeURI(queryString).split(/&/g),function(el,i){
                        var aux = el.split('='), o = {};
                        if(aux.length >= 1){
                            var val = undefined;
                            if(aux.length == 2)
                                val = aux[1];
                            o[aux[0]] = val;
                        }
                        return o;
                    }),
                    function(o){
                        _.extend(params,o);
                    }
                );
            }
            return params;
        },

        /**
        * get menu for institution
        */
        getLogin : function() {
            /*Menu Resources*/
            this.loginView = new app.LoginView();
        },

        /**
        * get menu for institution
        */
        getMenuMain : function() {
            /*Menu Resources*/
            this.baseMenuView = new app.BaseMenuView();
            this.baseMenuView.render();
        },

        /**
        * get dashboard for institution
        */
        getDashboard : function() {
            this.dashboardModel = new app.DashboardModel();

            this.dashboardView = new app.DashboardView({model: this.dashboardModel});
            this.dashboardModel.fetch();
        },

        /**
        * show view in DetailDocumenter
        * @param String show
        */
        getDocumenter: function (documenter) {
            var parameters = {
                dataFilter: {}
            };

            this.documenterModel = new app.DocumenterModel();
            this.documenterModel.idAttribute = 'codigo_registro_fase_usuario';
            this.documenterModel.set( {'codigo_registro_fase_usuario': documenter}, {'silent':true} );

            if ( this.programDocumenterView instanceof Backbone.View ){
                this.programDocumenterView.stopListening();
                this.programDocumenterView.undelegateEvents();
            }

            this.programDocumenterView = new app.ProgramDocumenterView({ model: this.documenterModel, parameters: parameters });
            this.documenterModel.fetch();
        },

        /**
        * get program for institution
        */
        getProgramMain : function() {

            if ( this.programListView instanceof Backbone.View ){
                this.programListView.stopListening();
                this.programListView.undelegateEvents();
            }

            this.programListView = new app.ProgramListView( {
                collection: this.programList
            } );

            this.programListView.render();

            // Render list program
            this.programListView.trigger('getdata');
        },

        getProgramCreate: function() {
            this.programModel = new app.ProgramModel();

            if( this.programCreateEditView instanceof Backbone.View ) {
                this.programCreateEditView.stopListening;
                this.programCreateEditView.undelegateEvents;
            }

            // Render create view program
            this.programCreateEditView = new app.ProgramCreateEditView({
                model: this.programModel,
                parameters: {
                    codigo_convocatoria: ''
                }
            });

            this.programCreateEditView.render();

            ReactDOM.render(React.createElement(ProjectEditor, {
                content: this.programModel,
                formCreate: this.programCreateEditView.$formCreate,
                nameField: "descripcion_convocatoria",
                required: true
            }, null), document.getElementById('description-program-editor'));

            ReactDOM.render(React.createElement(ProjectEditor, {
                content: this.programModel,
                formCreate: this.programCreateEditView.$formCreate,
                nameField: "terminos_convocatoria",
            }, null), document.getElementById('terms-program-editor'));

            ReactDOM.render(React.createElement(ProjectEditor, {
                content: this.programModel,
                formCreate: this.programCreateEditView.$formCreate,
                nameField: "contenido_aceptacion_terminos"
            }, null), document.getElementById('content-acceptance-terms-editor'));

            ReactDOM.render(React.createElement(ProjectEditor, {
                content: this.programModel,
                formCreate: this.programCreateEditView.$formCreate,
                nameField: "politicas_convocatoria"
            }, null), document.getElementById('policies-program-editor'));

            $('#upload-program-editor').html(this.programCreateEditView.renderUploaderFile());
        },

        getProgramEdit: function(program) {
            this.programModel = new app.ProgramModel();
            this.programModel.set( {'codigo_convocatoria': program}, {'silent':true} );

            if( this.programCreateEditView instanceof Backbone.View ) {
                this.programCreateEditView.stopListening;
                this.programCreateEditView.undelegateEvents;
            }

            // Render create view program
            this.programCreateEditView = new app.ProgramCreateEditView({
                model: this.programModel,
                collection: this.programPhasesList,
                parameters: {
                    codigo_convocatoria: program
                }
            });

            this.programModel.fetch();

            // Collection fases
            this.programPhasesList.fetch( {data: {program: program}} );


            if ( this.AssignmentAutoView instanceof Backbone.View ){
                this.AssignmentAutoView.stopListening();
                this.AssignmentAutoView.undelegateEvents();
            }
            this.AssignmentAutoView = new app.AssignmentAutoView({
                // model: this.evaluationAssignmentModel
                parameters: {
                    codigo_convocatoria: program
                }
            });


            ReactDOM.render(React.createElement(ProjectEditor, {
                content: this.programModel,
                formCreate: this.programCreateEditView.$formCreate,
                nameField: "descripcion_convocatoria",
                required: true
            }, null), document.getElementById('description-program-editor'));

            ReactDOM.render(React.createElement(ProjectEditor, {
                content: this.programModel,
                formCreate: this.programCreateEditView.$formCreate,
                nameField: "terminos_convocatoria"
            }, null), document.getElementById('terms-program-editor'));

            ReactDOM.render(React.createElement(ProjectEditor, {
                content: this.programModel,
                formCreate: this.programCreateEditView.$formCreate,
                nameField: "contenido_aceptacion_terminos"
            }, null), document.getElementById('content-acceptance-terms-editor'));

            ReactDOM.render(React.createElement(ProjectEditor, {
                content: this.programModel,
                formCreate: this.programCreateEditView.$formCreate,
                nameField: "politicas_convocatoria"
            }, null), document.getElementById('policies-program-editor'));

            $('#upload-program-editor').html(this.programCreateEditView.renderUploaderFile());

            $('#render-categories-entities').html($('#categories-and-entities-tables-tpl').html());

        },

        /**
        * get program for institution
        */
        getCategoryProgramMain : function() {

            if ( this.CategoryProgramView instanceof Backbone.View ){
                this.CategoryProgramView.stopListening();
                this.CategoryProgramView.undelegateEvents();
            }

            this.CategoryProgramView = new app.CategoriesProgramListView();

            // Render list ProjectStages
            this.CategoryProgramView.render();
        },

        getCategoryProgramCreate: function() {
            this.categoryProgramModel = new app.CategoryProgramModel();

            // Render create view category
            this.categoryProgramCreateEditView = new app.CategoryProgramCreateEditView({
                model: this.categoryProgramModel
            });

            this.categoryProgramCreateEditView.render();
        },

        getCategoryProgramEdit: function(category) {

            this.categoryProgramModel = new app.CategoryProgramModel();
            this.categoryProgramModel.set( {'codigo_categoria_proceso': category}, {'silent':true} );

            // Render list category
            this.categoryProgramCreateEditView = new app.CategoryProgramCreateEditView({
                model: this.categoryProgramModel,
                parameters: {
                    category: category
                }
            });

            // Render list user category
            this.categoryProgramCreateEditView.render();

        },

        /**
        * show view in DetailProgram
        * @param String show
        */
        getProgram: function (program, queryString) {

            var queries = this.parseQueryString(queryString);
            queries.tab || (queries.tab = 'inscritos');

            var parameters = {
                dataFilter: queries
            };

            this.programModel = new app.ProgramModel();
            this.programModel.idAttribute = 'codigo_convocatoria';
            this.programModel.set( {'codigo_convocatoria': program}, {'silent':true} );

            if ( this.programDetailView instanceof Backbone.View ){
                this.programDetailView.stopListening();
                this.programDetailView.undelegateEvents();
            }

            this.programDetailView = new app.ProgramDetailView({ model: this.programModel, parameters: parameters });
            this.programModel.fetch();
        },

        /**
         * Main Instituction
         */

         getInstitutionMain: function() {

            this.institutionModel = new app.InstitutionModel();


            if ( this.institutionListView instanceof Backbone.View ){
                this.institutionListView.stopListening();
                this.institutionListView.undelegateEvents();
            }

            this.institutionListView = new app.InstitutionListView( {
                collection: this.institutionList
            } );

            this.institutionListView.render();
         },

         /**
          * Create Institution
          */

         getInstitutionCreate: function() {
            this.institutionModel = new app.InstitutionModel();

            this.institutionModel.set({'codigo_institucion': null});

            // Render create view institution

            this.institutionCreateEdit = new app.InstitutionCreateEdit({
                model: this.institutionModel,
            });

            this.institutionCreateEdit.render();

            ReactDOM.render(React.createElement(ProjectEditor, {
                content: this.institutionModel,
                formCreate: this.institutionCreateEdit.$formCreateInstitution,
                nameField: "contacto_institucion",
                required: true
            }, null), document.getElementById('contact-institution-editor'));


            ReactDOM.render(React.createElement(ProjectEditor, {
                content: this.institutionModel,
                formCreate: this.institutionCreateEdit.$formCreateInstitution,
                nameField: "texto_registro_institucion",
                required: false
            }, null), document.getElementById('texto-registro-institution-editor'));

            ReactDOM.render(React.createElement(ProjectEditor, {
                content: this.institutionModel,
                formCreate: this.institutionCreateEdit.$formCreateInstitution,
                nameField: "contenido_registro_institucion",
                required: false
            }, null), document.getElementById('contenido-registro-institution-editor'));

            ReactDOM.render(React.createElement(ProjectEditor, {
                content: this.institutionModel,
                formCreate: this.institutionCreateEdit.$formCreateInstitution,
                nameField: "recomendaciones_marketplace_institucion",
                required: false
            }, null), document.getElementById('recomendaciones_marketplace_institucion'));

         },

         getInstitutionEdit: function(institution) {
            this.institutionModel = new app.InstitutionModel();

            this.institutionModel.set({ codigo_institucion: institution });
            // Render create view institution

            this.institutionCreateEdit = new app.InstitutionCreateEdit({
                model: this.institutionModel,
                parameters: {
                    institution: institution
                }
            });

            this.institutionModel.fetch();

            ReactDOM.render(React.createElement(ProjectEditor, {
                content: this.institutionModel,
                formCreate: this.institutionCreateEdit.$formCreateInstitution,
                nameField: "contacto_institucion",
                required: true
            }, null), document.getElementById('contact-institution-editor'));


            ReactDOM.render(React.createElement(ProjectEditor, {
                content: this.institutionModel,
                formCreate: this.institutionCreateEdit.$formCreateInstitution,
                nameField: "texto_registro_institucion",
                required: false
            }, null), document.getElementById('texto-registro-institution-editor'));

            ReactDOM.render(React.createElement(ProjectEditor, {
                content: this.institutionModel,
                formCreate: this.institutionCreateEdit.$formCreateInstitution,
                nameField: "contenido_institucion_registro",
                required: false
            }, null), document.getElementById('contenido-registro-institution-editor'));

            ReactDOM.render(React.createElement(ProjectEditor, {
                content: this.institutionModel,
                formCreate: this.institutionCreateEdit.$formCreateInstitution,
                nameField: "recomendaciones_marketplace_institucion",
                required: false
            }, null), document.getElementById('recomendaciones_marketplace_institucion'));

            ReactDOM.render(React.createElement(ProjectEditor, {
                content: this.institutionModel,
                formCreate: this.institutionCreateEdit.$formCreateInstitution,
                nameField: "terminos_condiciones",
                required: true
            }, null), document.getElementById('terminos_condiciones'));

            ReactDOM.render(React.createElement(ProjectEditor, {
                content: this.institutionModel,
                formCreate: this.institutionCreateEdit.$formCreateInstitution,
                nameField: "politicas_privacidad",
                required: true
            }, null), document.getElementById('politicas_privacidad'));

            ReactDOM.render(React.createElement(ProjectEditor, {
                content: this.institutionModel,
                formCreate: this.institutionCreateEdit.$formCreateInstitution,
                nameField: "politica_ventas",
                required: true
            }, null), document.getElementById('politica_ventas'));

            ReactDOM.render(React.createElement(ProjectEditor, {
                content: this.institutionModel,
                formCreate: this.institutionCreateEdit.$formCreateInstitution,
                nameField: "politicas_mentoria",
                required: true
            }, null), document.getElementById('politicas_mentoria'));

            ReactDOM.render(React.createElement(ProjectEditor, {
                content: this.institutionModel,
                formCreate: this.institutionCreateEdit.$formCreateInstitution,
                nameField: "politicas_evaluadores",
                required: true
            }, null), document.getElementById('politicas_evaluadores'));

            $('#upload_logo_institucion').html(this.institutionCreateEdit.renderUploaderFileLogo());
            $('#upload_imagen_mail_institucion').html(this.institutionCreateEdit.renderUploaderFileImageMail());
            $('#upload_imagen_administrador_institucion').html(this.institutionCreateEdit.renderUploaderFileImageAdmin());
            $('#upload_imagen_reporte_institucion').html(this.institutionCreateEdit.renderUploaderFileImageReport());

         },



        /**
        * get all articles
        */
        getArticleMain: function () {
            // Article Resources
            this.articleList = new app.ArticleList();

            if ( this.articleListView instanceof Backbone.View ){
                this.articleListView.stopListening();
                this.articleListView.undelegateEvents();
            }

            this.articleListView = new app.ArticleListView( {
                collection: this.articleList
            } );

            this.articleListView.render();

            // Render list program
            this.articleListView.trigger('getdata');
        },

        /**
        * article create
        */
        getArticleCreate : function(article) {
            this.articleModel = new app.ArticleModel();
            this.articleModel.set( {'codigo_contenido': article}, {'silent':true} );
            this.articleModel.fetch();

            if( this.articleCreateEditView instanceof Backbone.View ) {
                this.articleCreateEditView.stopListening;
                this.articleCreateEditView.undelegateEvents;
            }
            // Render list article
            this.articleCreateEditView = new app.ArticleCreateEditView({
                model: this.articleModel,
                parameters: {
                    codigo_contenido: article
                }
            });

            this.articleCreateEditView.render();

            ReactDOM.render(React.createElement(ProjectEditor, {
                content: this.articleModel,
                formCreate: this.articleCreateEditView.$formCreate,
                nameField: "texto_contenido",
                required: true
            }, null), document.getElementById('article-project-editor'));
        },

        /**
        * get all courses
        */
        getCourseMain: function () {

            if ( this.courseListView instanceof Backbone.View ){
                this.courseListView.stopListening();
                this.courseListView.undelegateEvents();
            }

            this.courseListView = new app.CourseListView({
                collection: this.courseList
            });
            this.courseListView.render();

            // Render list program
            this.courseListView.trigger('getdata');
        },

        /**
        * course create
        */
        getCourseCreate : function() {
            this.courseModel = new app.CourseModel();

            // Render create view course
            this.courseCreateEditView = new app.CourseCreateEditView({
                model: this.courseModel,
                parameters: {
                    codigo_curso: ''
                }
            });

            this.courseCreateEditView.render();
        },

        /**
        * course edit
        */
        getCourseEdit : function(course) {
            this.courseModel = new app.CourseModel();
            this.courseModel.set( {'codigo_curso': course}, {'silent':true} );
            this.courseModel.fetch();

            // Collection themes
            this.courseThemesList.fetch( {data: {course: course}, reset: true} );

            // Render list course
            this.courseCreateEditView = new app.CourseCreateEditView({
                model: this.courseModel,
                collection: this.courseThemesList,
                parameters: {
                    codigo_curso: course
                }
            });

            this.courseCreateEditView.render();

            $('#upload-attach-course').html(this.courseCreateEditView.renderUploaderFile());
        },


        /**
        * show detail view of course
        * @param String show
        */
        getCourse: function (course, queryString) {

            var queries = this.parseQueryString(queryString);
            queries.tab || (queries.tab = 'tab_main');

            var parameters = {
                dataFilter: queries,
                course: course
            };

            this.courseModel = new app.CourseModel();
            this.courseModel.idAttribute = 'codigo_curso';
            this.courseModel.set( {'codigo_curso': course}, {'silent':true} );
            this.courseModel.fetch();

            // Collection themes
            this.courseThemesList.fetch( {data: {course: course}, reset: true} );


            if ( this.courseDetailView instanceof Backbone.View ){
                this.courseDetailView.stopListening();
                this.courseDetailView.undelegateEvents();
            }


            this.courseDetailView = new app.CourseDetailView({
                    model: this.courseModel,
                    parameters: parameters
                });

            this.courseDetailView.render();


    },

     /**
        * show detail view of course evalution
        * @param String show
        */
       getCourseEvaluation: function (evaluacion, queryString) {

        var queries = this.parseQueryString(queryString);

        var parameters = {
            dataFilter: queries,
           evaluacion: evaluacion
        };

        var formModel = new app.FormModel();
        formModel.idAttribute = 'codigo_encuesta';
        formModel.set( {'codigo_encuesta': evaluacion}, {'silent':true} );
        formModel.fetch();

        if ( this.courseEvaluationDetailView instanceof Backbone.View ){
            this.courseEvaluationDetailView.stopListening();
            this.courseEvaluationDetailView.undelegateEvents();
        }

        this.courseEvaluationDetailView = new app.CourseEvaluationDetailView( {
            model: formModel,
            parameters: parameters

        });

        // Render list anwers
        this.courseEvaluationDetailView.render();
    },

        /**
        * get evaluations for institution
        */
        getEvaluationMain : function() {

            if ( this.evaluationListView instanceof Backbone.View ){
                this.evaluationListView.stopListening();
                this.evaluationListView.undelegateEvents();
            }

            this.evaluationListView = new app.EvaluationListView( {parameters:  ''} );

            // Render list evaluation
            this.evaluationListView.render();
        },

        getEvaluationCreate : function() {
            this.EvaluationModel = new app.EvaluationModel();

            // create view form
            this.EvaluationCreateView = new app.EvaluationCreateView({
                model: this.EvaluationModel
            });

            this.EvaluationCreateView.render();

        },

        getEvaluationEdit : function(evaluation) {
            var EvaluationModel = new app.EvaluationModel();
            console.log()
            EvaluationModel.idAttribute = 'codigo_evaluacion_encuesta';
            EvaluationModel.set( {'codigo_evaluacion_encuesta': evaluation}, {'silent':true} );
            EvaluationModel.fetch();

            var ItemEvaluationEmprendimientoModel = new app.ItemEvaluationEmprendimientoModel();
            ItemEvaluationEmprendimientoModel.idAttribute = 'codigo_evaluacion_encuesta';
            ItemEvaluationEmprendimientoModel.set( {'codigo_evaluacion_encuesta': evaluation}, {'silent':true} );
            ItemEvaluationEmprendimientoModel.fetch();

             // Combine both models into a single object
             var combinedModelData = {
                evaluationModel: EvaluationModel.toJSON(),
                itemEvaluationEmprendimientoModel: ItemEvaluationEmprendimientoModel.toJSON()
            };

            // create view form
            this.EvaluationCreateEditView = new app.EvaluationCreateEditView({
                model: EvaluationModel
            });

            this.EvaluationCreateEditView.render();

        },

         /**
        * get evaluations for institution
        */
       getEvaluationCommentMain : function() {

        if ( this.evaluationCommentListView instanceof Backbone.View ){
            this.evaluationCommentListView.stopListening();
            this.evaluationCommentListView.undelegateEvents();
        }

        this.evaluationCommentListView = new app.EvaluationCommentListView( {parameters:  ''} );

        // Render list evaluation
        this.evaluationCommentListView.render();
    },

    getEvaluationCommentCreate: function() {


        if( this.evaluationCommentCreateEditView instanceof Backbone.View ) {
            this.evaluationCommentCreateEditView.stopListening;
            this.evaluationCommentCreateEditView.undelegateEvents;
        }
        // Render create view evaluationComment
         this.evaluationCommentCreateEditView = new app.EvaluationCommentCreateEditView({
             model: new app.EvaluationCommentModel(),
             parameters: {

             }
         });

         // Render list evaluation
        this.evaluationCommentCreateEditView.render();
    },

        /**
        * show view in DetailEvaluation
        * @param String show
        */
        getEvaluation: function (evaluation, queryString) {

            this.evaluationModel = new app.EvaluationModel();
            this.evaluationModel.idAttribute = 'codigo_evaluacion_emprendimiento';
            this.evaluationModel.set( {'codigo_evaluacion_emprendimiento': evaluation}, {'silent':true} );

            if ( this.evaluationDetailView instanceof Backbone.View ){
                this.evaluationDetailView.stopListening();
                this.evaluationDetailView.undelegateEvents();
            }

            this.evaluationDetailView = new app.EvaluationDetailView({ model: this.evaluationModel, parameters: '' });
            this.evaluationModel.fetch();
        },

        /**
        * show view list Assignment
        * @param String show
        */
        getAssignmentMain: function () {

            if ( this.assignmentListView instanceof Backbone.View ){
                this.assignmentListView.stopListening();
                this.assignmentListView.undelegateEvents();
            }

            this.assignmentListView = new app.AssignmentListView({parameters: '' });
            this.assignmentListView.render();
        },

        /**
        * show view in DetailAssigment
        * @param String show
        */
        getAssignment: function (assignment) {

            this.evaluationAssignmentModel = new app.EvaluationAssignmentModel();
            this.evaluationAssignmentModel.idAttribute = 'codigo_registro_fase_usuario';
            this.evaluationAssignmentModel.set( {'codigo_registro_fase_usuario': assignment}, {'silent':true} );

            if ( this.assignmentDetailView instanceof Backbone.View ){
                this.assignmentDetailView.stopListening();
                this.assignmentDetailView.undelegateEvents();
            }

            this.assignmentDetailView = new app.AssignmentDetailView({
                model: this.evaluationAssignmentModel
            });
            this.evaluationAssignmentModel.fetch();
        },

        /**
        * get product for marketplace
        */
        getProductMain : function() {

            if ( this.MarketplaceListView instanceof Backbone.View ){
                this.MarketplaceListView.stopListening();
                this.MarketplaceListView.undelegateEvents();
            }

            this.MarketplaceListView = new app.MarketplaceListView();

            // Render list product
            this.MarketplaceListView.render();
        },

        /**
        * get Transactions for marketplace
        */
        getTransactionsMain : function() {

            if ( this.marketplaceTransactionListView instanceof Backbone.View ){
                this.marketplaceTransactionListView.stopListening();
                this.marketplaceTransactionListView.undelegateEvents();
            }

            this.marketplaceTransactionListView = new app.MarketplaceTransactionListView( {parameters: ''} );

            // Render list transactions
            this.marketplaceTransactionListView.render();
        },

        /**
        * show view in DetailAgreement
        * @param String show
        */
        getAgreement: function (agreement) {
            var parameters = {
                dataFilter: {}
            };

            this.agreementModel = new app.AgreementModel();
            this.agreementModel.idAttribute = 'codigo_acuerdo_compra';
            this.agreementModel.set( {'codigo_acuerdo_compra': agreement}, {'silent':true} );

            if ( this.agreementView instanceof Backbone.View ){
                this.agreementView.stopListening();
                this.agreementView.undelegateEvents();
            }

            this.agreementView = new app.AgreementView({ model: this.agreementModel, parameters: parameters });
            this.agreementModel.fetch();
        },

        /**
        * show view in Detail Letter of commitment
        * @param String show
        */
        getCommitment: function (commitment) {
            var parameters = {
                dataFilter: {}
            };

            this.commitmentModel = new app.CommitmentModel();
            this.commitmentModel.idAttribute = 'codigo_registro_fase_usuario';
            this.commitmentModel.set( {'codigo_registro_fase_usuario': commitment}, {'silent':true} );

            if ( this.programCommitmentView instanceof Backbone.View ){
                this.programCommitmentView.stopListening();
                this.programCommitmentView.undelegateEvents();
            }

            this.programCommitmentView = new app.ProgramCommitmentView({ model: this.commitmentModel, parameters: parameters });
            this.commitmentModel.fetch();
        },

        /**
         * get User for marketplace
         */
        getUserMain: function(){
            if ( this.userListView instanceof Backbone.View ){
                this.userListView.stopListening();
                this.userListView.undelegateEvents();
            }

            this.userListView = new app.UserListView();

            // Render list user
            this.userListView.render();
        },

        /**
        * User create
        */
        getUserCreate : function() {
            this.usuarioModel = new app.UserModel();

            // // Render create view occupation
            this.userCreateEditView  = new app.UserCreateEditView ({
                model: this.usuarioModel
            });

            this.userCreateEditView.render();
        },

        /**
        * show view list roles user
        * @param String show
        */
        getUserDetail: function (user) {
            if ( this.userDetailView instanceof Backbone.View ){
                this.userDetailView.stopListening();
                this.userDetailView.undelegateEvents();
            }


            this.userDetailView = new app.UserDetailView({
                collection: this.rolesUserList,
                parameters: {
                    userCode: user,
                }
            });

            this.rolesUserList.fetch( {data: {user: user}, reset:true} );

            $('#fnuploader-attach-file').html(this.userDetailView.renderUploaderMentorFile());
        },

        /**
         * get Role
         */
        getRoleMain: function(){
            if ( this.roleListView instanceof Backbone.View ){
                this.roleListView.stopListening();
                this.roleListView.undelegateEvents();
            }

            this.roleListView = new app.RoleListView();

            // Render list role
            this.roleListView.render();
        },

        /**
        * role create
        */
        getRoleCreate : function() {
            this.roleModel = new app.RoleModel();

            // Render create view role
            this.roleCreateEditView = new app.RoleCreateEditView({
                model: this.roleModel,
                parameters: {
                    id: ''
                }
            });

            this.roleCreateEditView.render();
        },

        /**
        * role edit
        */
        getRoleEdit: function(role){
            this.roleModel = new app.RoleModel();
            this.roleModel.set( {'id': role} );

            // Render list role
            this.roleCreateEditView = new app.RoleCreateEditView({
                model: this.roleModel,
                collection: this.usersRoleList,
                parameters: {
                    role: role
                }
            });

            // Collection users
            this.usersRoleList.fetch( {data: {role: role}, reset: true} );

            // Render list user role
            this.roleCreateEditView.render();
        },

        /**
        * get Channel
        */
        getChannelMain: function(){

            if ( this.channelListView instanceof Backbone.View ){
                this.channelListView.stopListening();
                this.channelListView.undelegateEvents();
            }

            this.channelListView = new app.ChannelListView();

            // Render list role
            this.channelListView.render();
        },

        /**
        * Channel create
        */
        getChannelCreate : function() {
            this.channelModel = new app.ChannelModel();

            // Render create view role
            this.channelCreateEditView = new app.ChannelCreateEditView({
                model: this.channelModel,
                parameters: {
                    codigo_canal: ''
                }
            });

            this.channelCreateEditView.render();
        },

        /**
        * Channel edit
        */
        getChannelEdit: function(channel, queryString){
            var queries = this.parseQueryString(queryString);

            var parameters = {
                dataFilter: queries
            };

            this.channelModel = new app.ChannelModel();
            this.channelModel.set( {'codigo_canal': channel}, {'silent':true} );

            // Render channel
            this.channelCreateEditView = new app.ChannelCreateEditView({
                model: this.channelModel,
                collection: this.channelUsersList,
                parameters: parameters
            });

            // Render channel
            this.channelCreateEditView.render();
        },

        /**
        * get Category
        */
        getCategoryMain: function(){

            if ( this.categorylListView instanceof Backbone.View ){
                this.categorylListView.stopListening();
                this.categorylListView.undelegateEvents();
            }

            this.categorylListView = new app.CategoyListView();

            // Render list role
            this.categorylListView.render();
        },

        /**
        * Category create
        */
        getCategoryCreate : function() {
            this.channelCategoryModel = new app.ChannelCategoryModel();

            // Render create view role
            this.categoryCreateEditView = new app.CategoryCreateEditView({
                model: this.channelCategoryModel,
                parameters: {
                    codigo_categoria_canal: ''
                }
            });

            this.categoryCreateEditView.render();
        },

        /**
        * Category edit
        */
        getCategoryEdit: function(category){
            this.channelCategoryModel = new app.ChannelCategoryModel();
            this.channelCategoryModel.set( {'codigo_categoria_canal': category}, {'silent':true} );

            // Render category
            this.categoryCreateEditView = new app.CategoryCreateEditView({
                model: this.channelCategoryModel,
                parameters: {
                    codigo_categoria_canal: category
                }
            });

            // Render category
            this.categoryCreateEditView.render();
        },

        /**
        * Publish
        */
        getPublicacion : function() {

            // create view form
            this.publishCreateEditView = new app.PublishCreateEditView();
            this.publishCreateEditView.render();
        },

        /**
        * get Mentoria List
        */
        getMentorMain: function(){

            if ( this.mentorListView instanceof Backbone.View ){
                this.mentorListView.stopListening();
                this.mentorListView.undelegateEvents();
            }

            this.mentorListView = new app.MentorListView();

            // Render list mentoring
            this.mentorListView.render();
        },

        /**
        * show view in DetailMentor
        * @param String show
        */
        getMentor: function (mentor) {

            this.mentorDetailView = new app.MentorDetailView({
                parameters: {
                    codigo_asesoria: mentor
                }
            });

            this.mentorDetailView.render();
        },

        /**
        * get Mentors
        */
        getMentorsMain: function(){

            if ( this.mentorsListView instanceof Backbone.View ){
                this.mentorsListView.stopListening();
                this.mentorsListView.undelegateEvents();
            }

            this.mentorsListView = new app.MentorsListView();

            // Render list mentoring
            this.mentorsListView.render();
        },

        /**
        * get Form
        */
        getFormMain: function(){
            if ( this.formListView instanceof Backbone.View ){
                this.formListView.stopListening();
                this.formListView.undelegateEvents();
            }

            this.formListView = new app.FormListView();

            // Render list role
            this.formListView.render();
        },

        /**
        * form create
        */
        getFormCreate : function() {
            this.formModel = new app.FormModel();

            // create view form
            this.formCreateEditView = new app.FormCreateEditView({
                model: this.formModel
            });

            this.formCreateEditView.render();

            ReactDOM.render(React.createElement(ProjectEditor, {
                content: this.formModel,
                formCreate: this.formCreateEditView.$formCreate,
                nameField: "respuesta_encuesta",
                required: true
            }, null), document.getElementById('form-project-editor'));
        },

        /**
        * form edit
        */
        getFormEdit : function(form) {
            this.formModel = new app.FormModel();
            console.log(this.formModel, form);
            this.formModel.set( {codigo_encuesta: form}, {silent: true} );

            if ( this.formCreateEditView instanceof Backbone.View ){
                this.formCreateEditView.stopListening();
                this.formCreateEditView.undelegateEvents();
            }

            // create view form
            this.formCreateEditView = new app.FormCreateEditView({
                model: this.formModel
            });

            this.formModel.fetch();

            ReactDOM.render(React.createElement(ProjectEditor, {
                content: this.formModel,
                formCreate: this.formCreateEditView.$formCreate,
                nameField: "respuesta_encuesta",
                required: true
            }, null), document.getElementById('form-project-editor'));
        },

        /**
        * show view in DetailForm
        * @param String show
        */
        getForm: function (survey) {
            this.formModel = new app.FormModel();
            this.formModel.idAttribute = 'codigo_encuesta';
            this.formModel.set( {'codigo_encuesta': survey}, {'silent':true} );

            if ( this.formDetailView instanceof Backbone.View ){
                this.formDetailView.stopListening();
                this.formDetailView.undelegateEvents();
            }

            this.formDetailView = new app.FormDetailView( {
                model: this.formModel
            });
            // Render list anwers
            this.formModel.fetch();
        },

        /**
        * get Notifications list
        */
        getNotificationOutputMain: function(){
            if ( this.notificationListView instanceof Backbone.View ){
                this.notificationListView.stopListening();
                this.notificationListView.undelegateEvents();
            }

            this.notificationListView = new app.NotificationListView();

            // Render list notifications output
            this.notificationListView.render();
        },

        /**
        * create notification output
        */
        getNotificationOutputCreate: function() {
            // create view form
            this.notificationCreateEditView = new app.NotificationsCreateEditView();
            this.notificationCreateEditView.render();
        },

        /**
        * get event
        */
        getEventMain: function(){
            if ( this.eventListView instanceof Backbone.View ){
                this.eventListView.stopListening();
                this.eventListView.undelegateEvents();
            }

            this.eventListView = new app.EventListView();

            // Render list role
            this.eventListView.render();
        },

        /**
        * get diagnostic
        */
        getDiagnosticMain: function(){
            if ( this.diagnosticListView instanceof Backbone.View ){
                this.diagnosticListView.stopListening();
                this.diagnosticListView.undelegateEvents();
            }

            this.diagnosticListView = new app.DiagnosticListView();

            // Render list role
            this.diagnosticListView.render();
        },

        getDiagnosticModuleMain: function(){
            console.log("ola");
            if ( this.diagnosticModuleListView instanceof Backbone.View ){
                this.diagnosticModuleListView.stopListening();
                this.diagnosticModuleListView.undelegateEvents();
            }

            this.diagnosticModuleListView = new app.DiagnosticModuleListView();



            // Render list role
            this.diagnosticModuleListView.render();
        },

        /**
        * get events for user
        */
        getEventsForUser: function(){
            if ( this.eventsForUsersView instanceof Backbone.View ){
                this.eventsForUsersView.stopListening();
                this.eventsForUsersView.undelegateEvents();
            }

            this.eventsForUsersView = new app.EventsForUsersView();

            // Render list role
            this.eventsForUsersView.render();
        },

        /**
        * event create
        */
        getEventCreate : function() {
            this.eventModel = new app.EventModel();

            // create view form
            this.eventCreateEditView = new app.EventCreateEditView({
                model: this.eventModel
            });

            this.eventCreateEditView.render();

            ReactDOM.render(React.createElement(ProjectEditor, {
                content: this.eventModel,
                formCreate: this.eventCreateEditView.$formCreate,
                nameField: "descripcion_evento",
                required: true
            }, null), document.getElementById('event-project-editor'));
        },

        /**
        * diagnostic create
        */
        getDiagnosticCreate : function() {
            this.diagnosticModel = new app.DiagnosticModel();

            // create view form
            this.diagnosticCreateEditView = new app.DiagnosticCreateEditView({
                model: this.diagnosticModel
            });

            this.diagnosticCreateEditView.render();

            // ReactDOM.render(React.createElement(ProjectEditor, {
            //     content: this.eventModel,
            //     formCreate: this.eventCreateEditView.$formCreate,
            //     nameField: "descripcion_evento",
            //     required: true
            // }, null), document.getElementById('event-project-editor'));
        },

        /**
        * event edit
        */
        getEventEdit : function(event) {
            this.eventModel = new app.EventModel();
            this.eventModel.set( {'codigo_evento': event}, {'silent':true} );
            this.eventModel.fetch();

            this.eventOrganizersList = new app.EventOrganizersList();

            // create view event
            this.eventCreateEditView = new app.EventCreateEditView({
                model: this.eventModel,
                collection: this.eventOrganizersList
            });

            // Collection event organizers
            this.eventOrganizersList.fetch( {data: {evento: event}, reset: true} );

            this.eventCreateEditView.render();

            ReactDOM.render(React.createElement(ProjectEditor, {
                content: this.eventModel,
                formCreate: this.eventCreateEditView.$formCreate,
                nameField: "descripcion_evento",
                required: true
            }, null), document.getElementById('event-project-editor'));
        },

        /**
        * event edit
        */
        getDiagnosticEdit : function(event) {
            this.diagnosticModel = new app.DiagnosticModel();
            this.diagnosticModel.set( {'codigo_diagnostico': event}, {'silent':true} );
            this.diagnosticModel.fetch();

            // this.eventOrganizersList = new app.EventOrganizersList();

            // create view event
            this.diagnosticCreateEditView = new app.DiagnosticCreateEditView({
                model: this.diagnosticModel,
                // collection: this.eventOrganizersList
            });

            // // Collection event organizers
            // this.eventOrganizersList.fetch( {data: {evento: event}, reset: true} );

            this.diagnosticCreateEditView.render();

            // ReactDOM.render(React.createElement(ProjectEditor, {
            //     content: this.eventModel,
            //     formCreate: this.eventCreateEditView.$formCreate,
            //     nameField: "descripcion_evento",
            //     required: true
            // }, null), document.getElementById('event-project-editor'));
        },

        getExport : function(event) {
            alert('Ingreso');
            // this.eventModel = new app.EventModel();
            // this.eventModel.set( {'codigo_evento': event}, {'silent':true} );
            // this.eventModel.fetch();
            //
            // this.eventOrganizersList = new app.EventOrganizersList();
            //
            // // create view event
            // this.eventCreateEditView = new app.EventCreateEditView({
            //     model: this.eventModel,
            //     collection: this.eventOrganizersList
            // });
            //
            // // Collection event organizers
            // this.eventOrganizersList.fetch( {data: {evento: event}, reset: true} );
            //
            // this.eventCreateEditView.render();
            //
            // ReactDOM.render(React.createElement(ProjectEditor, {
            //     content: this.eventModel,
            //     formCreate: this.eventCreateEditView.$formCreate,
            //     nameField: "descripcion_evento",
            //     required: true
            // }, null), document.getElementById('event-project-editor'));
        },

        /**
        * List professions
        */
        getProfessionMain : function() {
            if ( this.professionsView instanceof Backbone.View ){
                this.professionsView.stopListening();
                this.professionsView.undelegateEvents();
            }

            this.professionsView = new app.ProfessionsListView();

            // Render list professions
            this.professionsView.render();
        },

        /**
        * Profession create
        */
        getProfessionCreate : function() {
            this.professionModel = new app.ProfessionModel();

            // Render create view profession
            this.professionCreateEditView = new app.ProfessionCreateEditView({
                model: this.professionModel
            });

            this.professionCreateEditView.render();
        },

        /**
        * Profession edit
        */
        getProfessionEdit: function(profession){
            this.professionModel = new app.ProfessionModel();
            this.professionModel.set( {'codigo_profesion_persona': profession}, {'silent':true} );

            // Render list profession
            this.professionCreateEditView = new app.ProfessionCreateEditView({
                model: this.professionModel,
                parameters: {
                    profession: profession
                }
            });

            // Render list user profession
            this.professionCreateEditView.render();
        },

        /**
        * List occupations
        */
        getOccupationMain : function() {
            if ( this.occupationsView instanceof Backbone.View ){
                this.occupationsView.stopListening();
                this.occupationsView.undelegateEvents();
            }

            this.occupationsView = new app.OccupationsListView();

            // Render list occupations
            this.occupationsView.render();
        },

        /**
        * Occupation create
        */
        getOccupationCreate : function() {
            this.occupationModel = new app.OccupationModel();

            // Render create view occupation
            this.occupationCreateEditView = new app.OccupationCreateEditView({
                model: this.occupationModel
            });

            this.occupationCreateEditView.render();
        },

        /**
         * Occupation edit
         */
        getOccupationEdit: function(occupation){
            this.occupationModel = new app.OccupationModel();
            this.occupationModel.set( {'codigo_ocupacion_persona': occupation}, {'silent':true} );

            // Render list occupation
            this.occupationCreateEditView = new app.OccupationCreateEditView({
                model: this.occupationModel,
                parameters: {
                    occupation: occupation
                }
            });

            // Render list user occupation
            this.occupationCreateEditView.render();
        },

        /**
         * List industries
         */
        getIndustryMain : function() {
            if ( this.industriesView instanceof Backbone.View ){
                this.industriesView.stopListening();
                this.industriesView.undelegateEvents();
            }

            this.industriesView = new app.IndustriesListView();

            // Render list industries
            this.industriesView.render();
        },

        /**
         * Industry create
         */
        getIndustryCreate : function() {
            this.industryModel = new app.IndustryModel();

            // Render create view industry
            this.industryCreateEditView = new app.IndustryCreateEditView({
                model: this.industryModel
            });

            this.industryCreateEditView.render();
        },

        /**
         * Industry edit
         */
        getIndustryEdit: function(industry){
            this.industryModel = new app.IndustryModel();
            this.industryModel.set( {'codigo_categoria_proyecto': industry}, {'silent':true} );

            // Render list industry
            this.industryCreateEditView = new app.IndustryCreateEditView({
                model: this.industryModel,
                parameters: {
                    industry: industry
                }
            });

            // Render list user industry
            this.industryCreateEditView.render();
        },

        /**
         * List Projects
         */
        getProjectMain : function() {

            if ( this.projectsView instanceof Backbone.View ){
                this.projectsView.stopListening();
                this.projectsView.undelegateEvents();
            }

            this.projectsView = new app.ProjectsListView();

            // Render list ProjectStages
            this.projectsView.render();
        },

        export_report_convocatoria_rutan : function() {

            if ( this.reportConvocatoria instanceof Backbone.View ){
                this.reportConvocatoria.stopListening();
                this.reportConvocatoria.undelegateEvents();
            }

            this.reportConvocatoria = new app.ReportConvocatoriaView();

            // Render
            this.reportConvocatoria.render();
        },

        /**
         * Create new project
         */
        getProjectCreate: function() {
            this.projectModel = new app.ProjectModel();

            if( this.projectCreateEditView instanceof Backbone.View ) {
                this.projectCreateEditView.stopListening;
                this.projectCreateEditView.undelegateEvents;
            }

            // Render create view project
            this.projectCreateEditView = new app.ProjectCreateEditView({
                model: this.projectModel
            });

            this.projectCreateEditView.render();

            ReactDOM.render(React.createElement(ProjectEditor, {
                content: this.projectModel,
                formCreate: this.projectCreateEditView.$formCreate,
                nameField: "biografia_usuario",
                required: true
            }, null), document.getElementById('description-project-editor'));

        },

        /**
         * Create new project
         */
        getProjectEdit: function(entidad) {
            this.projectModel = new app.ProjectModel();
            this.projectModel.idAttribute = 'nickname_usuario';
            this.projectModel.set( {'nickname_usuario': entidad}, {'silent':true} );
            var _this = this;
            this.projectModel.fetch({wait:true}).done(function(){
                if (_this.projectCreateEditView) {
                    _this.projectCreateEditView.render();
                }
            });;


            if( this.projectCreateEditView instanceof Backbone.View ) {
                this.projectCreateEditView.stopListening;
                this.projectCreateEditView.undelegateEvents;
            }

            // Render create view project
            this.projectCreateEditView = new app.ProjectCreateEditView({
                model: this.projectModel,
                parameters: {
                    entidad: entidad
                }
            });

            this.projectCreateEditView.render();



            ReactDOM.render(React.createElement(ProjectEditor, {
                content: this.projectModel,
                formCreate: this.projectCreateEditView.$formCreate,
                nameField: "biografia_usuario",
                required: true
            }, null), document.getElementById('description-project-editor'));

        },

        /**
         * Detail Projects
         */
        getProjectDetail : function(entity) {

            if ( this.projectDetailView instanceof Backbone.View ){
                this.projectDetailView.stopListening();
                this.projectDetailView.undelegateEvents();
            }

            this.projectDetailView = new app.ProjectDetailView({
                parameters: {
                    entity: entity
                }
            });

            this.projectDetailView.render();
        },

        /**
         * List Project Stages
         */
        getProjectStagesMain : function() {
            if ( this.projectStagesView instanceof Backbone.View ){
                this.projectStagesView.stopListening();
                this.projectStagesView.undelegateEvents();
            }

            this.projectStagesView = new app.ProjectStagesListView();

            // Render list ProjectStages
            this.projectStagesView.render();
        },

        /**
         * Project stage create
         */
        getProjectStageCreate : function() {
            this.projectStageModel = new app.ProjectStageModel();

            // Render create view project stage
            this.projectStageCreateEditView = new app.ProjectStageCreateEditView({
                model: this.projectStageModel
            });

            this.projectStageCreateEditView.render();
        },

        /**
         * Project stage edit
         */
        getProjectStageEdit: function(stage){
            this.projectStageModel = new app.ProjectStageModel();
            this.projectStageModel.set( {'codigo_etapa_proyecto': stage}, {'silent':true} );

            // Render list stage
            this.projectStageCreateEditView = new app.ProjectStageCreateEditView({
                model: this.projectStageModel,
                parameters: {
                    stage: stage
                }
            });

            // Render list project stage
            this.projectStageCreateEditView.render();
        },

        /**
         * get Evaluation
         */
        getMarketplaceEvaluation: function(compra){
            this.evaluationPurchaseModel = new app.EvaluationPurchaseModel();
            this.evaluationPurchaseModel.idAttribute = 'codigo_compra';
            this.evaluationPurchaseModel.set({'codigo_compra': compra}, {'silent': true});

            if(this.marketplaceEvaluationView instanceof Backbone.View){
                this.marketplaceEvaluationView.stopListening();
                this.marketplaceEvaluationView.undelegateEvents();
            }

            this.marketplaceEvaluationView = new app.MarketplaceEvaluationView({model: this.evaluationPurchaseModel});
            this.evaluationPurchaseModel.fetch();

            //$('#wrapper-uploader-agreement').html(this.marketplaceEvaluationView.renderUploaderFileBill());
        },

        /**
         * get marketplace assignments
         */
        getMarketplaceAssignmentsMain: function () {

            this.marketplaceAssignmentsView.render();
        },

        /**
         * get marketplace assignment detail
         */
        getMarketplaceAssignment: function (assignment) {
            this.MarketplaceAssignmentView = new app.MarketplaceAssignmentDetailView({
                parameters: {
                    codigo_registro_fase: assignment
                }
            });
            this.MarketplaceAssignmentView.render();
        },

        /**
         * get marketplace promotion
         */
        getMarketplacePromotionsMain: function () {
            this.marketplacePromotionListView = new app.MarketplacePromotionListView();
            this.marketplacePromotionListView.render();
        },

        /**
         * view Mentorias Masivas
         */
        viewMentoriasMasivas: function () {

            this.MentoriaMasivaView = new app.MentoriaMasivaView();
            this.MentoriaMasivaView.render();


        },

        /**
         * view Lista de Mentorias Masivas
         */
        ListaMentoriaMasivaView: function () {

            this.ListaMentoriaMasivaView = new app.ListaMentoriaMasivaView();
            this.ListaMentoriaMasivaView.render();
        },

         /**
         * ver Una Mentoria Masiva
         */
         MentoriaMasivaShowView: function () {
            this.MentoriaMasivaShowView = new app.MentoriaMasivaShowView();
            this.MentoriaMasivaShowView.render();
        },

        /**
         * get marketplace assignments
         */
        getMarketplaceVouchers: function () {

            this.marketplaceVouchersView.render();
        },

        /**
         * get marketplace voucher detail
         */
        getMarketplaceVoucher: function (vouchers) {

            this.MarketplaceVoucherView = new app.MarketplaceVoucherDetailView({
                parameters: {
                    codigo_detalle_voucher: vouchers
                }
            });
            this.MarketplaceVoucherView.render();
        },

        /**
        * get Forum List
        */
       getForumMain: function(){

        if ( this.forumListView instanceof Backbone.View ){
            this.forumListView.stopListening();
            this.forumListView.undelegateEvents();
        }

        this.forumListView = new app.ForumListView();

        // Render list mentoring
        this.forumListView.render();
        },

        /**
         * get Forum Category
         */
        getForoCategoryMain: function (){
            // Course Themes Resources
            this.forumCategoriesList = new app.ForumCategoriesList();

            if ( this.forumCategorylListView instanceof Backbone.View ){
                this.forumCategorylListView.stopListening();
                this.forumCategorylListView.undelegateEvents();
            }

            this.forumCategorylListView = new app.ForumCategoriesListView({
                collection: this.forumCategoriesList
			});

            // Render list category
            this.forumCategorylListView.render();
            this.forumCategorylListView.trigger('getdata');
        },

        /**
         * Get Gestion reports
         */
        getGestionMain: function() {
            this.managementListView = new app.ManagementListView();

            // Render list reports
            this.managementListView.render();
        },

        /**
         * Get Gneral reports
         */
        getReports: function() {
            this.GeneralReportsView = new app.GeneralReportsView();

            // Render list reports
            this.GeneralReportsView.render();
        },

        getResponseEvaluation: function(userSurvey) {

            this.ResponseEvaluationModel = new app.RespuestaEvaluacionModel({ codigo_usuario_encuesta: userSurvey });

            this.ResponseEvaluationView = new app.ResponseEvaluationView({
                model: this.ResponseEvaluationModel,
                parameters: {
                    user_survey: userSurvey
                }
            });

            this.ResponseEvaluationView.render();
        },

        getAssignmentMentor: function (codigo_convocatoria, nickname_usuario) {
            this.$asigmentMentorView = new app.AsigmentMentorView();
            this.$asigmentMentorView.parameters = {
                    codigo_convocatoria: codigo_convocatoria,
                    nickname_usuario: nickname_usuario
            };
            this.$asigmentMentorView.render();
        },

        assignmentVoucher: function (codigo_registro_fase_usuario) {
            this.$asigmentVoucherView = new app.AsigmentVoucherView();
            this.$asigmentVoucherView.parameters = {
                    codigo_registro_fase_usuario: codigo_registro_fase_usuario,
            };
            this.$asigmentVoucherView.render();
        },
    }) );

})(window.jQuery, window, document);
