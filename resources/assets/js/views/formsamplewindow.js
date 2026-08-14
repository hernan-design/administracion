/**
* Class FormSampleWindow extends of Backbone View
* @author TuProyecto || Desarrollador : @dortegon
* @link http://TuProyecto.com
*/

//Global App Backbone
app || (app = {});

(function ($, window, document, undefined) {

    app.FormSampleWindow = Backbone.View.extend({
        el: '#modal-form',
        events: {
            'submit #form-modal-all': 'onSubmit',
            'click .cancel-action': 'onModalDestroy'
		},
        parameters: {
            data: {},
            title: '',
            edit: false,
            template: '',
            dataFilter: {},
            currentMadurez: null,
            tagInfo: null
        },

        /**
        * Constructor Method
        */
        initialize : function(opts) {

            // extends attributtes
            if( opts !== undefined && _.isObject(opts.parameters) ){
                this.parameters = _.extend({}, this.parameters, opts.parameters );
            }

            // Init attributes
            this.$formModal = this.$('#form-modal-all');

            // Init Attributes
            this.configAlert = {
                'wrapper': '#main-wrap',
                'closeTime': 6000,
                'speedOpen': 500,
                'speedClose': 500
            };
        },

        /*
        * Render View Element
        */
        render: function() {
            var attributes = this.model.toJSON();
            console.log(attributes);
            this.$el.find('.modal-title').html(this.parameters.title);
            this.$el.find('.content-modal').html(this.parameters.template( attributes ));
            this.$el.modal("show").removeAttr('tabindex');

            var nivel = this.parameters.currentMadurez;

            if(nivel != null){
                if(nivel.nombre_nivel_madurez != undefined){
                    $("#nombre_nivel_madurez").val(nivel.nombre_nivel_madurez);
                }
                if(nivel.rango_minimo != undefined){
                    $("#rango_minimo").val(nivel.rango_minimo);
                }
                if(nivel.rango_maximo != undefined){
                    $("#rango_maximo").val(nivel.rango_maximo);
                }
                if(nivel.descripcion_nivel_madurez != undefined){
                    $("#descripcion_nivel_madurez").val(nivel.descripcion_nivel_madurez);
                }

                //Unidades productivas
                if(nivel.nombre_unidad_productiva != undefined){
                    $("#nombre_unidad_productiva").val(nivel.nombre_unidad_productiva);
                }
                if(nivel.descripcion_unidad_productiva != undefined){
                    $("#descripcion_unidad_productiva").val(nivel.descripcion_unidad_productiva);
                }
                if(nivel.porcentaje != undefined){
                    $("#porcentaje").val(nivel.porcentaje);
                }

            }

            console.log(this.parameters.currentMadurez);

            return this;
        },

        /**
        * fires libraries js
        */
        ready: function () {
            // reload plugins
            if( typeof(window.initComponent.autosizeTrigger) == 'function'){
                window.initComponent.autosizeTrigger();
            }
        },

        /*
        * Submit data
        */
        onSubmit: function (e) {
            e.preventDefault();

            window.Misc.setSpinner( this.$el );

            var _this = this,
                data = window.Misc.formToJson( this.$formModal );

            _.extend(data, this.parameters);

            var pestana = this.parameters.tagInfo;
            console.log(this.parameters.tagInfo)

            if (this.parameters.currentMadurez == null) {

                this.model.save(data, {wait: true,
                    success: function (model, resp) {
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
                        confCrop =  $.extend({}, _this.configAlert, config);

                        //complete proccess
                        if( resp.success !== undefined ){
                            if( resp.success ){
                                window.Misc.showAlertBox( confCrop );
                                setTimeout(function() {

                                    var diagnostic = $('#codigo_diagnostico').val();

                                    if (diagnostic != undefined){
                                        var baseUrl = window.location.protocol + '//' + window.location.host + '/';

                                        window.location.href = baseUrl + 'diagnosticos/' + diagnostic + '/edit' + pestana;
                                        window.location.reload();
                                    } else{
                                        var baseUrl = window.location.protocol + '//' + window.location.host + '/';

                                        window.location.href = baseUrl + 'diagnosticos/' + diagnostic + '/edit';
                                        window.location.reload();
                                    }

                                }, 1000);
                            }else{
                                window.Misc.showAlertBox( confCrop );
                            }
                        }

                    }
                });
            } else {
                // Si estás editando, usa el método save con el método PUT
        this.model.save(data, {wait: true, patch: true,
            success: function (model, resp) {
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
                confCrop =  $.extend({}, _this.configAlert, config);

                //complete proccess
                if( resp.success !== undefined ){
                    if( resp.success ){
                        window.Misc.showAlertBox( confCrop );
                        setTimeout(function() {
                            var diagnostic = $('#codigo_diagnostico').val();

                            console.log(diagnostic);
                            if (diagnostic != undefined){
                                var baseUrl = window.location.protocol + '//' + window.location.host + '/';

                                window.location.href = baseUrl + 'diagnosticos/' + diagnostic + '/edit' + pestana;
                                window.location.reload();
                            } else{
                                var baseUrl = window.location.protocol + '//' + window.location.host + '/';

                                        window.location.href = baseUrl + 'diagnosticos/' + diagnostic + '/edit';
                                        window.location.reload();
                            }
                        }, 1000);
                    }else{
                        window.Misc.showAlertBox( confCrop );
                    }
                }
            }
        });
            }
        },

        onModalDestroy: function (e){
            this.$el.modal().off();
        }
   });

})(jQuery, this, this.document);
