/**
* Init Class
*/

/*global*/
var app = app || {};

(function ($, window, document, undefined) {

    var InitComponent = function(){

        //Init Attributes
        $.ajaxSetup({
            cache: false,
            headers: {
                'X-CSRF-TOKEN': $('meta[name="csrf-token"]').attr('content')
            }
        });
    }

    InitComponent.prototype = {

        /**
        * Constructor or Initialize Method
        */
        initialize: function () {
            //Initialize
            this.initApp();
            this.select2Trigger();
            this.datepickerTrigger();
            this.daterangepickerTrigger();
            this.datetimepickerTrigger();
            this.autosizeTrigger();
            this.icheckTrigger();
            this.validateForm();
            this.editingForm();
            this.configSidemenu();
			this.disabledButton();
			this.configEllipsis();
			this.configCollapse();
			this.configNestable();
			this.spinnerTrigger();
			this.multiselectTrigger();
			this.colorpickerTrigger();
        },

        /**
        * Init Backbone Application
        */
        initApp: function () {
            window.app.AppRouter.start({ pushState: true });
        },

        /**
        * icheck trigger plugin
        */
        icheckTrigger: function () {
            var config = {
                'table input[type=checkbox], table input[type=radio]': {
                    checkboxClass: 'icheckbox_flat-green',
                    radioClass: 'iradio_flat-green'
                },
                'input.flat': {
                    checkboxClass: 'icheckbox_flat-green',
                    radioClass: 'iradio_flat-green'
                }
            };

            for (var selector in config) {

                $(selector).each(function (index, el) {
                    var $el = $(el);

                    if($el.data('iCheck') == undefined) {
                        $el.iCheck(config[selector]);
                    }
                });
            }
        },

        /**
        * init select2 plugin
        */
        select2Trigger: function () {

			var _this = this,
				config = {
				  '.choice-select' : {language:"es", placeholder:'Seleccione una opción'},
				  '.choice-select-deselect'  : {language:"es", placeholder:'Seleccione una opción', allowClear: true},
				  '.choice-select-no-single' : {},
				  '.choice-select-multiple': {},
				  '.choice-select-autocomplete': {
					language: "es",
                    allowClear: true,
					placeholder:'Seleccione una opción',
					ajax: {
                        delay: 250,
						data: function (params) {
                            return {
								q: params.term,
								page: params.page,
                                denied:  $(this).data('denied'),
                                lang:  $(this).data('lang'),
                                data: $(this).data('param'),
                                city: $($(this).data('val')).find(":selected").val(),
                                location: $('#codigo_localidad_ciudad').find(":selected").val()
							};
						},
						processResults: function (data, params) {
							params.page = params.page || 1;
							return {
								results: data,
								pagination: {
									more: (params.page * 30) < data.total_count
								}
							};
						},
						escapeMarkup: function (markup) { return markup; },
						cache: true,
						minimumInputLength: 1
					}
				  }
				};

			// Instance selects to choice plugin
			for (var selector in config){

				$(selector).each(function(index, el) {
					var $el = $(el);

					if( $el.data('select2') == undefined ){
						$el.select2(config[selector]);

						$el.on('select2:open', function (evt) {
							var elSelect = $(evt.target);
							var w = elSelect.next('.select2-container').css('width');

							$('.select2-dropdown').css('min-width', w);
						});

                        // set default option
                        if(selector == '.choice-select-autocomplete') {

                            var initialId = $el.data('initial-value');
                            var $option = null;

                            if(initialId) {
                                var ajaxOptions = $el.data('select2').dataAdapter.ajaxOptions;

                                $option = $('<option selected>Loading...</option>').val(initialId);
                                $el.append($option).trigger('change');

                                $.get( ajaxOptions.url, {id:initialId}, function(data) {
                                    $option.text(data[0].text).val(data[0].id);
                                    $option.removeData();
                                    $el.trigger('change');
                                });
                            }

                            $el.off('select2:unselect')
                            $el.on('select2:unselect', function (event) {
                                var $option = $('<option value="" selected></option>');
                                var $selected = $(event.target);

                                $selected.find('option:selected')
                                .remove()
                                .end()
                                .append($option)
                                .trigger('change');

                            });
                        }
					}
				});
			}
		},

        /**
        * Trigger datepicker plugin
        */
        datepickerTrigger: function () {

            var config = {
                '.dateDefault': {
                    startDate: new Date(),
                    singleDatePicker: true,
                    autoUpdateInput: false,
                    showDropdowns: true,
                    locale: {
                        format: 'YYYY-MM-DD',
                        cancelLabel: 'Clear'
                    }
                },
                '.datepicker' : {
                    startDate: new Date(),
                    minDate: new Date(),
                    singleDatePicker: true,
                    autoUpdateInput: false,
                    locale: {
                        format: 'YYYY-MM-DD',
                        cancelLabel: 'Clear'
                    }
                }
            };
            
            // set Date Range Picker
            for (var selector in config){
                $(selector).each(function(index, el) {
                    var $el = $(el);

                    $($el).on('apply.daterangepicker', function(ev, picker) {
                    
                        $(this).val(picker.startDate.format('YYYY-MM-DD'));
                    });
        
                    $($el).on('cancel.daterangepicker', function(ev, picker) {
                        $(this).val('');
                    });

                    if( $el.data('daterangepicker') === undefined ){
                        $el.daterangepicker(config[selector]);

                        $el.on('apply.daterangepicker', function (ev, picker) {
                            $el.parsley().validate();
                        });
                    }
                });
            }
        },

        /**
        * Trigger daterangepicker plugin
        */
        daterangepickerTrigger: function () {

            var config = {
                '.daterange': {
                    timePicker: true,
                    timePicker24Hour: true,
                    autoUpdateInput: false,
                    opens: 'left',
                    locale: {
                        format: 'YYYY-MM-DD HH:mm',
                        cancelLabel: 'Clear',
                        applyLabel: 'Aplicar',
                        cancelLabel: 'Cancelar', 
                    }
                }
            };

            $('.daterange').on('apply.daterangepicker', function(ev, picker) {
                $(this).val(picker.startDate.format('YYYY-MM-DD HH:mm') + ' - ' + picker.endDate.format('YYYY-MM-DD HH:mm'));
            });

            $('.daterange').on('cancel.daterangepicker', function(ev, picker) {
                $(this).val('');
            });
            
            // set Date Range Picker
            for (var selector in config){
                $(selector).each(function(index, el) {
                    var $el = $(el);

                    if( $el.data('daterangepicker') === undefined ){
                        $el.daterangepicker(config[selector]);

                        $el.on('apply.daterangepicker', function (ev, picker) {
                            $el.parsley().validate();
                        });
                    }
                });
            }
        },

        /**
        * Trigger date time picker
        */
        datetimepickerTrigger: function () {
            var config = {
                '.datetimepicker' : {
                    format: 'LT'
                }
            };

            for (var selector in config){
                $(selector).each(function(index, el) {
                    var $el = $(el);
                    if( $el.data('DateTimePicker') === undefined ){
                        $el.datetimepicker(config[selector]);

                        $el.on('apply.DateTimePicker', function (ev, picker) {
                            $el.parsley().validate();
                        });
                    }
                });
            }
        },

        /**
        * Trigger autosize plugin
        */
        autosizeTrigger: function () {

            autosize($('.resizable_textarea'));
        },

        /**
        * validate forms
        */
        validateForm: function () {
            // initialize the function
            _.extend(validator.message, {
                invalid         : 'campo incorrecto',
                empty           : 'campo obligatorio',
                min             : 'el campo es muy corto',
                max             : 'el campo es muy largo',
                number_min      : 'demasiado bajo',
                number_max      : 'demasiado alto',
                url             : 'URL incorrecta',
                number          : 'no es un numero',
                email           : 'dirección de email incorrecta',
                email_repeat    : 'los emails no coinciden',
                password_repeat : 'las contraseñas no coinciden',
                repeat          : 'no coincide',
                complete        : 'El campo no esta completo',
                select          : 'por favor seleccione una opción'
            });

            // validate a field on "blur" event, a 'select' on 'change' event & a '.reuired' classed multifield on 'keyup':
            $(document)
                .on('blur', 'form:not([data-parsley-validate]) input[required], form:not([data-parsley-validate]) input.optional, form:not([data-parsley-validate]) select.required', validator.checkField)
                .on('change', 'form:not([data-parsley-validate]) select.required', validator.checkField)
                .on('keypress', 'form:not([data-parsley-validate]) input[required][pattern]', validator.checkField);

            $(document).on('keyup blur', '.multi.required input', function() {
                validator.checkField.apply( $(this).siblings().last()[0] );
            });

            // validate with parsleyjs
            if( _.has($.fn, 'parsley') ) {
                // excluded and include fields for validation
                window.Parsley.options['excluded'] = 'input[type=button], input[type=submit], input[type=reset], :not([name])';
                window.Parsley.options['inputs'] = 'input, textarea, select, input[type=hidden], :hidden';

                $.listen('parsley:field:validate', function(event) {
                    var $parentForm = event.$element.closest('form');

                    validateFront($parentForm);
                });
                $(document)
                    .on('click', 'form:submit', function (event) {
                        var $parentForm = $(event.target).closest('form');

                        $parentForm.parsley().validate();
                        validateFront($parentForm);
                    })
                    .on('blur', 'form input[required], form textarea[required], form select[required]', function (event) {
                        var $fieldValid = $(event.target);

                        $fieldValid.parsley().validate();
                    })
                    .on('change', 'form select[required]', function (event) {
                        var $fieldValid = $(event.target);

                        $fieldValid.parsley().validate();
                    })
                    .on('keypress', 'form input[required][pattern]', function (event) {
                        var $fieldValid = $(event.target);

                        $fieldValid.parsley().validate();
                    });

                var validateFront = function($form) {
                    if ( true === $form.parsley().isValid() ) {
                        $('.bs-callout-info').removeClass('hidden');
                        $('.bs-callout-warning').addClass('hidden');
                    } else {
                        $('.bs-callout-info').addClass('hidden');
                        $('.bs-callout-warning').removeClass('hidden');
                    }
                };
                try {
                    hljs.initHighlightingOnLoad();
                } catch (err) {}
            }
        },

        /**
        * Trigger wysiwyg plugin
        */
        wysiwygTrigger: function () {

            function initToolbarBootstrapBindings() {
              var fonts = ['Serif', 'Sans', 'Arial', 'Arial Black', 'Courier',
                  'Courier New', 'Comic Sans MS', 'Helvetica', 'Impact', 'Lucida Grande', 'Lucida Sans', 'Tahoma', 'Times',
                  'Times New Roman', 'Verdana'
                ],
                fontTarget = $('[title=Font]').siblings('.dropdown-menu');
              $.each(fonts, function(idx, fontName) {
                fontTarget.append($('<li><a data-edit="fontName ' + fontName + '" style="font-family:\'' + fontName + '\'">' + fontName + '</a></li>'));
              });
              $('a[title]').tooltip({
                container: 'body'
              });
              $('.dropdown-menu input').click(function() {
                  return false;
                })
                .change(function() {
                  $(this).parent('.dropdown-menu').siblings('.dropdown-toggle').dropdown('toggle');
                })
                .keydown('esc', function() {
                  this.value = '';
                  $(this).change();
                });

              $('[data-role=magic-overlay]').each(function() {
                var overlay = $(this),
                  target = $(overlay.data('target'));
                overlay.css('opacity', 0).css('position', 'absolute').offset(target.offset()).width(target.outerWidth()).height(target.outerHeight());
              });

              if ("onwebkitspeechchange" in document.createElement("input")) {
                var editorOffset = $('#editor').offset();

                $('.voiceBtn').css('position', 'absolute').offset({
                  top: editorOffset.top,
                  left: editorOffset.left + $('#editor').innerWidth() - 35
                });
              } else {
                $('.voiceBtn').hide();
              }
            }

            function showErrorAlert(reason, detail) {
              var msg = '';
              if (reason === 'unsupported-file-type') {
                msg = "Unsupported format " + detail;
              } else {
                console.log("error uploading file", reason, detail);
              }
              $('<div class="alert"> <button type="button" class="close" data-dismiss="alert">&times;</button>' +
                '<strong>File upload error</strong> ' + msg + ' </div>').prependTo('#alerts');
            }

            initToolbarBootstrapBindings();

            $('#editor').wysiwyg({
              fileUploadError: showErrorAlert
            });

            window.prettyPrint;
            prettyPrint();
        },

        /**
		* functionality of editable form
		*/
		editingForm: function () {

			$(document).on('click', '.editable-item .field-for-edit > p, .editable-item .field-for-edit .prepared-to-edit', function (event) {
				event.preventDefault();

				var $fieldEditing = $(event.currentTarget);
				$fieldEditing.parents('.editable-item').addClass('editing');
				$fieldEditing.parents('.editable-item').find('.edit-field-text').focus();
			});

            $(document).on('click', '.editable-item .edit-field-button', function(event) {
				event.preventDefault();

				var $fieldButton = $(event.currentTarget);
				$fieldButton.parents('.editable-item').addClass('editing');
				$fieldButton.parents('.editable-item').find('.edit-field-text').focus();
			});

			$(document).on('keydown', '.field-editing .edit-field-text', function(event) {

				if (event.which === 27 || event.type == 'focusout' ) {

					var $fieldOut = $(event.target);
					$fieldOut.parents('.editable-item').removeClass('editing');
				}
			});

			$(document).on('click', '.editable-item .close-editing', function (event) {
				event.preventDefault();

				var $fieldOut = $(event.currentTarget);
				$fieldOut.parents('.editable-item').removeClass('editing');
			});
		},

        /**
        * config side menu
        */
        configSidemenu: function () {
            if(Backbone && Backbone.history) {
                window.CURRENT_URL = window.Misc.urlFull( Backbone.history.fragment.split('/', 2).join('/') );
            }
        },

        /**
		* disabled button
		*/
		disabledButton: function () {
			$(document).on('click focus', 'button.disabled, .button.disabled, a.disabled', null, function(e) {

				e.preventDefault();
				e.stopPropagation();
				e.stopImmediatePropagation();

				return false;
			});
		},

        /**
		* Trigger Ellipsis plugin config
		*/
		configEllipsis: function () {
			var config = {
				'.ellipsis': {}
			};

			for (var selector in config) {

				$(selector).each(function(index, el) {
					var $el = $(el);

					if( $el.data('dotdotdot') == undefined ){
						$el.dotdotdot(config[selector]);
					}
				});
			}
		},

        /**
        * Trigger Collapse config
        */
        configCollapse: function () {
            $(document).on('show.bs.collapse', function (e) {
                $(e.target).parent().find('.collaps-arrow').removeClass('fa-chevron-right')
                $(e.target).parent().find('.collaps-arrow').addClass('fa-chevron-down')
			});

            $(document).on('hide.bs.collapse', function (e) {
                $(e.target).parent().find('.collaps-arrow').removeClass('fa-chevron-down')
                $(e.target).parent().find('.collaps-arrow').addClass('fa-chevron-right')
			});
        },

        /*
        * Nestable config
        */
        configNestable: function () {
            $('.dd').nestable();
        },

        /**
		* spinner for text fields
		*/
		spinnerTrigger: function () {
			var config = {
				'.spinner-currency' : {
					step: 5,
					start: 1000,
					min: 0,
					numberFormat: "C",
					culture: 'es-CO',
					currency: 'COP'
				},
				'.spinner-quantity': {
					step: 1,
					start: 1,
					min: 0,
					numberFormat: "n"
				}
			};

			for (var selector in config) {

				$(selector).each(function(index, el) {

				});
			}
		},

        /*
        * multi-select
        */
        multiselectTrigger: function () {

            var setPostXhr = function (toUrl, sendData, method) {
                toUrl || (toUrl = '');
                method || (method = 'POST');
                sendData || (sendData = {});

                $.ajax({
                    method: method,
                    url: window.Misc.urlFull(toUrl),
                    data: {id: id, data: sendData},
                    success: function(data) {
                        // response success or error
                        if( data.success !== undefined ){
                            if( data.success ){
                                window.Misc.showAlertBox({
                                    text: data.message,
                                    type: 'success',
                                    closeTime: 3000
                                });
                            }else{
                                window.Misc.showAlertBox({
                                    text: data.errors,
                                    type: 'alert',
                                    closeTime: 3000
                                });
                            }
                        }
                    }
                });
            }

            var id, toUrl, toRoute,
            sendData = {};

            var config = {
                '.defaultMulti': {
                    keepOrder: true,
                },
                '.keepOrder' : {
                    keepOrder: true,
                },
                '.selectableOptgroup' : {
                    selectableOptgroup: true,
                }
            };

            for (var selector in config) {
                $(selector).each(function(index, el) {
                    var $el = $(el);
                    if( $el.data('multiSelect') === undefined ){
                        if( $el.hasClass('keepOrder') ) {

                            _.extend(config[selector], {
                                afterSelect: function(values){
                                    var toRoute = $el.data('route');
                                    sendData.data_id = $el.data('id');
                                    sendData.option_id = values[0];
                                    sendData.data_param = $el.data('param');
                                    setPostXhr(Route.route( toRoute + '.index' ), sendData);
                                },
                                afterDeselect: function(values){

                                    var method = 'DELETE',
                                        toRoute = $el.data('route'),
                                        params = {};

                                    if(!_.isUndefined($el.data('params'))){
                                        var dataParams = JSON.parse(JSON.stringify($el.data('params'))),

                                        params = _.mapObject(dataParams, function (param) {
                                            return values[param];
                                        });
                                    }else{
                                        params[toRoute] = values[0];
                                    }

                                    sendData.data_id = $el.data('id');
                                    sendData.data_param = $el.data('param');

                                    setPostXhr(Route.route( toRoute  + '.destroy', params  ), sendData, method);
                                }
                            })
                        }

                        if( $el.hasClass('selectableOptgroup') ) {
                            _.extend(config[selector], {
                                afterSelect: function(values){
                                var toRoute = $el.data('route');
                                sendData.data_id = $el.data('id');
                                sendData.option_id = values;
                                setPostXhr(Route.route( toRoute + '.index' ), sendData);
                                },
                                afterDeselect: function(values){
                                    var method = 'DELETE',
                                        toRoute = $el.data('route'),
                                        params = {};

                                    if(!_.isUndefined($el.data('params'))){
                                        var dataParams = JSON.parse(JSON.stringify($el.data('params'))),

                                        params = _.mapObject(dataParams, function (param) {
                                            return values[param];
                                        });
                                    }else{
                                        params[toRoute] = values[0];
                                    }

                                    sendData.data_id = $el.data('id');
                                    sendData.option_id = values;
                                    setPostXhr(Route.route( toRoute  + '.destroy', params  ), sendData, method);
                                }
                            })
                        }

                        $el.multiSelect(config[selector]);
                    }
                });
            }
        },

        /**
         * Trigger chosen plugin config
         */
        chosenTrigger: function () {
            var _this = this,
                config = {
                    'select.chosen-select': {width:'100%', no_results_text:"No se encontrarón resultados", placeholder_text_single:' '},
                    'select.chosen-select-nosearch': {width:'100%', no_results_text:"No se encontrarón resultados", placeholder_text_single:' ', disable_search: true},
                    'select.chosen-select-deselect': {width:'100%', no_results_text:"No se encontrarón resultados", placeholder_text_single:' ', allow_single_deselect: true},
                    'select.chosen-select-deselect-nosearch': {width:'100%', no_results_text:"No se encontrarón resultados", placeholder_text_single:' ', allow_single_deselect: true, disable_search: true},
                    'select.chosen-select-no-single': {disable_search_threshold:10},
                    'select.chosen-select-multiple': {
                        max_selected_options: 6,
                        display_selected_options: false,
                        no_results_text:'¡No se encontrarón resultados! de:'
                    }
                };

            // chosen events listener
            $(document).off('chosen:ready', 'select[data-chosenopts]', this.getItemsChosen);
            $(document).on('chosen:ready', 'select[data-chosenopts]', this.getItemsChosen);
            $(document).on('chosen:ready', 'select[class*=chosen-select]', function (event, params) {
                var chosen = params.chosen,
                    $chosenResults = chosen.search_results;

                $chosenResults.addClass('scroll-chosen');
                _this.configScrollbar();
            });

            // Instance selects to chosen plugin
            for (var selector in config) {

                $(selector).each(function(index, el) {
                    var $el = $(el);

                    if( $el.data('chosen') == undefined ){
                        $el.chosen(config[selector]);
                    }
                });
            }

        },

        /*
        * colorpicker
        */
        colorpickerTrigger: function () {
            $('.sample-selector').colorpicker({
                format: "hex",
                colorSelectors: {
                    'black': '#000000',
                    'white': '#ffffff',
                    'red': '#FF0000',
                    'default': '#777777',
                    'primary': '#337ab7',
                    'success': '#5cb85c',
                    'info': '#5bc0de',
                    'warning': '#f0ad4e',
                    'danger': '#d9534f'
                }
            });
        }
    };

    // load plugin currency
    // --------------------
    !(function () {
        window.copCurrencyF = wNumb({
            mark: ',',
            thousand: '.',
            decimals: 0,
            prefix: '$',
            negativeBefore: '- '
        });
        window.dolarCurrencyF = wNumb({
            mark: '.',
            thousand: ',',
            decimals: 0,
            prefix: 'USD ',
            negativeBefore: '- '
        });
    })();

    //Init App Components
    //-----------------------
    $(function() {
        window.initComponent = new InitComponent();
        window.initComponent.initialize();
    });

})(window.jQuery, window, document);
