/**
* Class QuestionFormWindow extends of Backbone View
* @author TuProyecto || Desarrollador : @dortegon
* @link http://TuProyecto.com
*/

//Global App Backbone
app || (app = {});

(function ($, window, document, undefined) {

    app.QuestionFormWindow = Backbone.View.extend({
        el: '#modal-form',
        template: _.template( ($('#form-options-question-tpl').html() || '') ),
        templateField: _.template( ($('#type-question-field-tpl').html() || '') ),
        templateOptionsQuestion: _.template( ($('#type-options-question-tpl').html() || '') ),
        templateQuestionMatriz: _.template( ($('#type-question-matriz-tpl').html() || '' ) ),
        templateOptionsQuestionFather: _.template( ($('#options-question-father-tpl').html() || '') ),
        templateActions: _.template( ( $('#actions-question-tpl').html()  || '' ) ),
        templateSelectWithoutFather: _.template( ( $('#slc-type-question-without-father').html() || '' ) ),
        templateSelectFather: _.template( ( $('#slc-type-question-father').html() || '' )),
        events: {
            'submit #form-modal-all': 'onSubmit',
            'click .cancel-action': 'onModalDestroy',
            'click .q-elements-content': 'onRenderTypeQuestion',
            'click .add-option': 'onAddOption',
            'click .add-option-matriz': 'onAddOptionMatriz',
            'change #cor_codigo_pregunta_encuesta': 'onChangeFatherQuestion',
            'change #tipo_pregunta_encuesta': 'onChangeTypeQuestion',
            'ifChecked .padre_check': 'onCheckedFather',
            'ifUnchecked .padre_check': 'onUnCheckedFather',
            'change #tipo_pregunta_matriz': 'onChangeTypeQuestionMatriz'
		},
        parameters: {
            view: {},
            data: {},
            template: '',
            dataFilter: {},
            father: false,
        },

        /**
        * Constructor Method
        */
        initialize : function(opts) {
            _.bindAll(this, 'render');
            // extends attributtes
            if( opts !== undefined && _.isObject(opts.parameters) ){
                this.parameters = _.extend({}, this.parameters, opts.parameters );
            }

            //Instance new collection
            this.optionList = new app.OptionsQuestionList();
            this.formQuestionsList = new app.FormQuestionsList();

            // Models

            this.FormQuestionModel = new app.FormQuestionModel();

            // Init attributes
            this.$formModal = this.$('#form-modal-all');

            // Init Attributes
            this.configAlert = {
                'wrapper': '#main-wrap',
                'closeTime': 6000,
                'speedOpen': 500,
                'speedClose': 500
            };

            this.$wrapperOption = this.$('#field-option');

            this.listenTo( this.optionList, "add", this.onOneOption );
            this.listenTo( this.optionList, "reset", this.onAllOptions );
            this.listenTo( this.optionList, 'request', this.loadSpinner );
			this.listenTo( this.optionList, 'sync', this.responseServer);
        },

        /*
        * Render View Element
        */
        render: function() {
            var attributes = this.model.toJSON();
            console.log(this.model);
            _.extend(attributes, this.parameters.data, {codigo_version_encuesta: this.parameters.version ? this.parameters.version : this.parameters.data.codigo_version_encuesta});

            // Change modal text and content
            this.$el.find('.modal-title').html('Agregar pregunta');
            this.$el.find('.content-modal').html( this.template(attributes) );
            this.$el.find('#slc_type_question').html(this.templateSelectWithoutFather(attributes));

            this.parameters.data.update ? this.$el.find('.q-elements-content').prop('disabled', false) : this.$el.find('.q-elements-content').prop('disabled', true);

            this.$el.find('#cor_codigo_pregunta_encuesta').prop('disabled', true);
            this.$el.find('#periodo_pregunta_encuesta').prop('disabled', true);
            this.$el.find('#numero_pregunta_encuesta').prop('disabled', true);
            this.$el.find('.padre_check').attr('disabled', true);

            if (attributes.tipo_campo_pregunta == window._tp.typeQuestionField['select']) {
                this.onTypeQuestionEdit(attributes.tipo_campo_pregunta);
            }

            if (attributes.clase_pregunta_encuesta == 2) {
                this.$el.find('#cor_codigo_pregunta_encuesta').prop('disabled', false);

                var optionsQuestionFather = this.$('.options-question-father'), _this = this;

                $.get(window.Misc.urlFull( Route.route('formulario.pregunta.index', {
                    codigo_pregunta_encuesta: attributes.padre,
                    codigo_version_encuesta: this.parameters.version ? this.parameters.version : this.parameters.data.codigo_version_encuesta,
                    codigo_hijo_pregunta: attributes.codigo_pregunta_encuesta,
                    options: true
                }) ) )
                .done(function( data ) {
                    optionsQuestionFather.html( _this.templateOptionsQuestionFather( { options: data } ) );

                    _this.ready();
                });
            }

            this.$el.modal("show").removeAttr('tabindex');
            this.ready();

            // render editor
            ReactDOM.render(React.createElement(SimpleEditor, {
                content: this.model,
                formCreate: this.$formModal,
                nameField: "texto_pregunta_encuesta",
                value: this.model.get('texto_pregunta_encuesta'),
                minHeight: 50,
                column: "col-md-12 col-sm-12 col-xs-12",
                previewHide: true,
                required: true
            }, null), document.getElementById('name-question'));

            ReactDOM.render(React.createElement(SimpleEditor, {
                content: this.model,
                formCreate: this.$formModal,
                nameField: "texto_opcional_pregunta",
                value: this.model.get('texto_opcional_pregunta'),
                minHeight: 50,
                column: "col-md-12 col-sm-12 col-xs-12",
                previewHide: true
            }, null), document.getElementById('name-question-help'));

            if(this.parameters.data['squestion'] || this.model.get('pregunta_padre') ){
                this.$el.find('input[name=numero_pregunta_encuesta]').removeAttr('required');
            }

            if( !_.isUndefined(this.model.id) ){
                this.$el.find('input[name=texto_pregunta_encuesta]').val( this.model.get('texto_pregunta_encuesta') );
                this.$el.find('input[name=texto_opcional_pregunta]').val( this.model.get('texto_opcional_pregunta') );

                this.onTypeQuestion({tipo_pregunta_encuesta: this.model.get('tipo_pregunta_encuesta'), tipo_campo_pregunta: this.model.get('tipo_campo_pregunta'), longitud: this.model.get('longitud_pregunta_encuesta')});
            }

            this.renderElement();

            if(!_.isUndefined(this.model.get('options')) && this.model.get('options').length){
                this.optionList.reset( this.model.get('options') );
            }
            return this;
        },

        /**
        * fires libraries js
        */
        ready: function () {
            // reload plugins
            if(typeof window.initComponent.select2Trigger == 'function' ){
                window.initComponent.select2Trigger();
            }

            if( typeof(window.initComponent.datepickerTrigger) == 'function'){
                window.initComponent.datepickerTrigger();
            }

            if( typeof window.initComponent.icheckTrigger == 'function' ){
                window.initComponent.icheckTrigger();
            }
        },

        // Render Element
        renderElement: function () {
            this.$wrapperOption = this.$('#field-option');
        },

        onChangeTypeQuestionMatriz: function(e) {
            e.preventDefault();

            var content = $(e.currentTarget).val();

            if (content == 2) {
                this.$el.find('#tipo_pregunta_encuesta_matriz').prop('disabled', false);
            } else {
                this.$el.find('#tipo_pregunta_encuesta_matriz').prop('disabled', true);
            }
        },

        // Change option father question
        onChangeFatherQuestion: function (e) {
            e.preventDefault();
            e.stopPropagation();

            var fatherQuestion = $(e.currentTarget).val(),
                _this = this,
                optionsQuestionFather = this.$('.options-question-father');

                $.get(window.Misc.urlFull( Route.route('formulario.pregunta.index', {
                    codigo_pregunta_encuesta: fatherQuestion,
                    codigo_version_encuesta: this.parameters.version,
                    options: true
                }) ) )
                .done(function( data ) {
                    console.log(data);
                    optionsQuestionFather.html( _this.templateOptionsQuestionFather( { options: data } ) );

                    _this.ready();
                });
        },

        onCheckedFather: function(e) {
            e.preventDefault();

            var attributes = this.model.toJSON();
            _.extend(attributes, this.parameters.data);

            this.$el.find('#slc_type_question').html(this.templateSelectFather(attributes));
        },

        onUnCheckedFather: function(e) {
          e.preventDefault();

            var attributes = this.model.toJSON();
            _.extend(attributes, this.parameters.data);

            this.$el.find('#slc_type_question').html(this.templateSelectWithoutFather(attributes));
        },

        // render type question
        onRenderTypeQuestion: function (e) {
            var data = $(e.target).val() ? $(e.target).val().split("_") : '',
                dataNew = [];

            dataNew.tipo_pregunta_encuesta = data[0];
            dataNew.tipo_campo_pregunta = data[1];

            this.onTypeQuestion(dataNew);
        },

        onTypeQuestionEdit: function(type) {
            var typeQuestion = this.$('.type-question-field');

            if (type) {
                typeQuestion.html( this.templateOptionsQuestion( { id: type } ) );
            }
        },

        // Type question
        onTypeQuestion: function (data) {
            var typeQuestion = this.$('.type-question-field');

            if(data.tipo_campo_pregunta){

                if( data.tipo_campo_pregunta == window._tp.typeQuestionField['singleText']){
                    typeQuestion.html( this.templateField( { id: data.tipo_campo_pregunta, longitud_pregunta_encuesta: data.longitud } ) )
                }

                if( data.tipo_campo_pregunta == window._tp.typeQuestionField['longText']){
                    typeQuestion.html( this.templateField( { id: data.tipo_campo_pregunta, longitud_pregunta_encuesta: data.longitud } ) )
                }

                if( data.tipo_campo_pregunta == window._tp.typeQuestionField['select'] ){
                    typeQuestion.html( this.templateOptionsQuestion( { id: data.tipo_campo_pregunta} ) )
                }

                if (data.tipo_campo_pregunta != window._tp.typeQuestionField['singleText'] && data.tipo_campo_pregunta != window._tp.typeQuestionField['longText'] && data.tipo_campo_pregunta != window._tp.typeQuestionField['select']) {
                    typeQuestion.html('');
                }

                if (data.tipo_campo_pregunta ==  window._tp.typeQuestionField['multiple_check'] || data.tipo_campo_pregunta ==  window._tp.typeQuestionField['currency'] || data.tipo_campo_pregunta ==  window._tp.typeQuestionField['single_check']) {
                    typeQuestion.html(this.templateQuestionMatriz( { id: data.tipo_campo_pregunta} ));
                }
            }

            if(!data.tipo_campo_pregunta && data.tipo_pregunta_encuesta){
                if( data.tipo_pregunta_encuesta == window._tp.typeQuestionSurvey['single'] || data.tipo_pregunta_encuesta == window._tp.typeQuestionSurvey['multiple'] ){
                    typeQuestion.html( this.templateOptionsQuestion( { id: data.tipo_pregunta_encuesta} ) )
                } else {
                    typeQuestion.html('');
                }
            }

            if(typeQuestion.find('#tipo_pregunta_encuesta').length){
                $('#tipo_pregunta_encuesta').remove();
            }
        },

        // Travel data for create view of options
        onOneOption: function (option) {
            var view = new app.OptionsQuestionsListView({
                model: option
            });
            this.$('#field-option').append( view.render().el );
        },

        onAddOptionMatriz: function() {
            var listOption = 0,
            data = window.Misc.formToJson(this.$formModal),
            last = _.last(this.optionList.pluck('codigo_opcion_encuesta'));
            _.extend(data, this.parameters.data);
            if(!_.isUndefined(last)){
                listOption = last;
            }

            data.matrizOption = true;
            data.codigo_opcion_encuesta = parseInt(listOption) + 1;
            if(!_.isEmpty(data.texto_opcion_encuesta) && !_.isEmpty(data.tipo_pregunta_matriz)){
                if (data.tipo_pregunta_matriz == 2 && _.isEmpty(data.tipo_pregunta_encuesta_matriz)) {
                    var config = {
                        'text': 'Debe tener el tipo de pregunta de opción',
                        'type': 'alert'
                    };
                    _.extend( this.configAlert, config );

                    window.Misc.showAlertBox( this.configAlert );
                } else {
                    if(this.model.id){
                        data.codigo_pregunta_encuesta = this.model.get('codigo_pregunta_encuesta');

                        // New model data
                        this.optionList.create(data, {wait:true, success: function(model, resp, opts){
                            var type = resp.success ? 'success' : 'alert',
                            text = resp.success ?
                            (resp.message !== undefined ? resp.message : '')
                            : resp.errors;

                            if( _.isObject( resp.errors ) ) {
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

                            window.Misc.showAlertBox( this.configAlert );
                        }.bind(this)});
                    } else {
                        this.renderElement();
                        this.optionList.add(data);
                    }

                    // Clear inputs
                    this.resetFields( this.$formModal.find('[name="fields_option_question"]') );

                }

            } else {
                var config = {
                    'text': 'Debe tener texto y tipo de opción',
                    'type': 'alert'
                };
                _.extend( this.configAlert, config );

                window.Misc.showAlertBox( this.configAlert );
            }

            console.log(data);
        },

        // Add one to one options
        onAddOption: function () {
            var listOption = 0,
                data = window.Misc.formToJson(this.$formModal),
                last = _.last(this.optionList.pluck('codigo_opcion_encuesta'));
                _.extend(data, this.parameters.data);
            if(!_.isUndefined(last)){
                listOption = last;
            }

            data.codigo_opcion_encuesta = parseInt(listOption) + 1;
            if(!_.isEmpty(data.texto_opcion_encuesta)){
                if(this.model.id){
                    data.codigo_pregunta_encuesta = this.model.get('codigo_pregunta_encuesta');

                    // New model data
                    this.optionList.create(data, {wait:true, success: function(model, resp, opts){
                        var type = resp.success ? 'success' : 'alert',
                        text = resp.success ?
                        (resp.message !== undefined ? resp.message : '')
                        : resp.errors;

                        if( _.isObject( resp.errors ) ) {
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

                        window.Misc.showAlertBox( this.configAlert );
                    }.bind(this)});
                }else {
                    this.renderElement();
                    this.optionList.add(data);
                }

                // Clear inputs
                this.resetFields( this.$formModal.find('[name="fields_option_question"]') );
            }else{
                var config = {
                    'text': 'Debe tener texto opción',
                    'type': 'alert'
                };
                _.extend( this.configAlert, config );

                window.Misc.showAlertBox( this.configAlert );
            }
        },

        // Get all options
        onAllOptions: function () {
            this.$wrapperOption.html('');
            this.optionList.forEach( this.onOneOption, this );
        },

        /**
        * Load spinner on the request
        */
        loadSpinner: function () {
            console.log('Load');
            window.Misc.setSpinner( this.wrapperOption );
        },

        /**
        * response of the server
        */
        responseServer: function ( model, resp, opts ) {
            console.log('Response');
            //complete proccess
            window.Misc.removeSpinner({
                'wrap': this.wrapperOption,
                'direct': false
            });
        },

        /**
		* reset form fields
		* @param parentForm Element
		*/
		resetFields: function (parentForm) {
			// reset inputs
            $(':input', parentForm).not(':button, :submit, :reset, :hidden, :checkbox, :radio').val('');

			this.ready();
		},

        /*
        * Submit data
        */
        onSubmit: function (e) {
            e.preventDefault();
            var _this = this,
                data = window.Misc.formToJson( this.$formModal );

            if(this.optionList.length){
                data.options = this.optionList.toJSON();
            }

            data.codigo_grupo_pregunta_encuesta = this.parameters.group;
            data.codigo_encuesta = this.parameters.encuesta;
            data.codigo_version_encuesta = this.parameters.version;
            data.codigo_pagina_encuesta = this.parameters.page;

            _.extend(data, this.parameters.data);
            this.$wrapperQuestions = this.$('.container-questions');
            var dataTipoPregunta = data.tipo_pregunta_encuesta, dataCampoPregunta = data.tipo_campo_pregunta;
            var type_question = $('#tipo_pregunta_encuesta').val(), checked = false;


            $('#opciones_padre:checked').each(function() {
                checked = true;
            });

            if (type_question == 2) {
                if (checked == true) {
                    if(this.parameters.data.update){
                        data.tipo_pregunta_encuesta = dataTipoPregunta == 0 ? this.model.toJSON().tipo_pregunta_encuesta :  dataTipoPregunta;
                        data.tipo_campo_pregunta = typeof dataCampoPregunta == 'undefined'  ? this.model.toJSON().tipo_campo_pregunta : dataCampoPregunta;

                        this.FormQuestionModel.save(data,
                            {wait: true, success: function (model, resp) {

                                var view = new app.FormQuestionItem({
                                    model: model,
                                    collection: _this.formQuestionsList
                                });

                                _this.formQuestionsList.reset(model.toJSON());

                                window.Misc.removeSpinner({
                                    'wrap': _this.$el,
                                    'direct': false
                                });

                                _this.$el.modal().off();
                                _this.$el.modal('hide');

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
                                };
                                confCrop =  _.extend({}, _this.configAlert, config);
                                //complete proccess
                                if( resp.success !== undefined ){
                                    if( resp.success ){

                                        _this.model.changed;
                                        window.Misc.showAlertBox( confCrop );
                                    }else{
                                        window.Misc.showAlertBox( confCrop );
                                    }
                                }
                            }
                        });
                    }else{
                        this.collection.create(data, {wait: true,
                            success: function (model, resp) {

                                // console.log(model);

                                var view = new app.FormQuestionItem({
                                    model: model,
                                    className: '',
                                    collection: _this.formQuestionsList
                                });

                                _this.formQuestionsList.add(model);

                                // complete proccess
                                window.Misc.removeSpinner({
                                    'wrap': _this.$el,
                                    'direct': false
                                });

                                _this.$el.modal().off();
                                _this.$el.modal('hide');

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
                                confCrop =  _.extend({}, _this.configAlert, config);
                                //complete proccess
                                if( resp.success !== undefined ){
                                    if( resp.success ){
                                        window.Misc.showAlertBox( confCrop );
                                    }else{
                                        window.Misc.showAlertBox( confCrop );
                                    }
                                }
                            }, error: function() {
                                console.log('Error');
                            }
                        });
                    }
                } else {
                    if (!this.parameters.data.update) {
                        _this.$el.modal().off();
                        _this.$el.modal('hide');

                        var config = {
                            'text': 'Debes seleccionar una opcion para la pregunta',
                        }

                        confCrop =  _.extend({}, _this.configAlert, config);
                        window.Misc.showAlertBox( confCrop );
                    } else {
                        if(this.parameters.data.update){
                            data.tipo_pregunta_encuesta = dataTipoPregunta == 0 ? this.model.toJSON().tipo_pregunta_encuesta :  dataTipoPregunta;
                            data.tipo_campo_pregunta = typeof dataCampoPregunta == 'undefined'  ? this.model.toJSON().tipo_campo_pregunta : dataCampoPregunta;

                            this.FormQuestionModel.save(data,
                                {wait: true, success: function (model, resp) {

                                    var view = new app.FormQuestionItem({
                                        model: model,
                                        collection: _this.formQuestionsList
                                    });

                                    _this.formQuestionsList.reset(model.toJSON());

                                    window.Misc.removeSpinner({
                                        'wrap': _this.$el,
                                        'direct': false
                                    });

                                    _this.$el.modal().off();
                                    _this.$el.modal('hide');

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
                                    };
                                    confCrop =  _.extend({}, _this.configAlert, config);
                                    //complete proccess
                                    if( resp.success !== undefined ){
                                        if( resp.success ){

                                            _this.model.changed;
                                            window.Misc.showAlertBox( confCrop );
                                        }else{
                                            window.Misc.showAlertBox( confCrop );
                                        }
                                    }
                                }
                            });
                        }else{
                            this.collection.create(data, {wait: true,
                                success: function (model, resp) {

                                    // console.log(model);

                                    var view = new app.FormQuestionItem({
                                        model: model,
                                        className: '',
                                        collection: _this.formQuestionsList
                                    });

                                    _this.formQuestionsList.add(model);

                                    // complete proccess
                                    window.Misc.removeSpinner({
                                        'wrap': _this.$el,
                                        'direct': false
                                    });

                                    _this.$el.modal().off();
                                    _this.$el.modal('hide');

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
                                    confCrop =  _.extend({}, _this.configAlert, config);
                                    //complete proccess
                                    if( resp.success !== undefined ){
                                        if( resp.success ){
                                            window.Misc.showAlertBox( confCrop );
                                        }else{
                                            window.Misc.showAlertBox( confCrop );
                                        }
                                    }
                                }, error: function() {
                                    console.log('Error');
                                }
                            });
                        }
                    }
                }
            } else {
                if(this.parameters.data.update){
                    data.tipo_pregunta_encuesta = dataTipoPregunta == 0 ? this.model.toJSON().tipo_pregunta_encuesta :  dataTipoPregunta;
                    data.tipo_campo_pregunta = typeof dataCampoPregunta == 'undefined'  ? this.model.toJSON().tipo_campo_pregunta : dataCampoPregunta;

                    this.FormQuestionModel.save(data,
                        {wait: true, success: function (model, resp) {

                            var view = new app.FormQuestionItem({
                                model: model,
                                collection: _this.formQuestionsList
                            });

                            _this.formQuestionsList.reset(model.toJSON());

                            window.Misc.removeSpinner({
                                'wrap': _this.$el,
                                'direct': false
                            });

                            _this.$el.modal().off();
                            _this.$el.modal('hide');

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
                            };
                            confCrop =  _.extend({}, _this.configAlert, config);
                            //complete proccess
                            if( resp.success !== undefined ){
                                if( resp.success ){

                                    _this.model.changed;
                                    window.Misc.showAlertBox( confCrop );
                                }else{
                                    window.Misc.showAlertBox( confCrop );
                                }
                            }
                        }
                    });
                }else{
                    this.collection.create(data, {wait: true,
                        success: function (model, resp) {

                            // console.log(model);

                            var view = new app.FormQuestionItem({
                                model: model,
                                className: '',
                                collection: _this.formQuestionsList
                            });

                            _this.formQuestionsList.add(model);

                            // complete proccess
                            window.Misc.removeSpinner({
                                'wrap': _this.$el,
                                'direct': false
                            });

                            _this.$el.modal().off();
                            _this.$el.modal('hide');

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
                            confCrop =  _.extend({}, _this.configAlert, config);
                            //complete proccess
                            if( resp.success !== undefined ){
                                if( resp.success ){
                                    window.Misc.showAlertBox( confCrop );
                                }else{
                                    window.Misc.showAlertBox( confCrop );
                                }
                            }
                        }, error: function() {
                            console.log('Error');
                        }
                    });
                }
            }
        },

        // Destroy modal
        onModalDestroy: function (e){
            this.$el.modal().off();
        },

        onChangeTypeQuestion: function(e) {
            e.preventDefault();

            var value = e.target.value;

            if (value == 1) {
              this.$el.find('.q-elements-content').prop('disabled', false);
              this.$el.find('#cor_codigo_pregunta_encuesta').prop( 'disabled', true );
              this.$el.find('.padre_check').removeAttr('disabled', true);
              this.$el.find('.icheckbox_flat-green').removeClass('disabled');
              this.$el.find('#periodo_pregunta_encuesta').prop('disabled', false);
              this.$el.find('#numero_pregunta_encuesta').prop('disabled', false);
            } else if (value == 2)  {
                this.$el.find('.q-elements-content').prop('disabled', false);
                this.$el.find('#cor_codigo_pregunta_encuesta').prop( "disabled", false );
                this.$el.find('.padre_check').prop('checked', false);
                this.$el.find('.padre_check').attr('disabled', true);
                this.$el.find('.icheckbox_flat-green').addClass('disabled');
                this.$el.find('#periodo_pregunta_encuesta').prop('disabled', false);
                this.$el.find('#numero_pregunta_encuesta').prop('disabled', false);
            } else if (value == 0) {
                this.$el.find('.q-elements-content').prop('disabled', true);
                this.$el.find('#cor_codigo_pregunta_encuesta').prop('disabled', true);
                this.$el.find('#periodo_pregunta_encuesta').prop('disabled', true);
                this.$el.find('#numero_pregunta_encuesta').prop('disabled', true);
                this.$el.find('.padre_check').attr('disabled', true);
            }
        },

   });

})(jQuery, this, this.document);
