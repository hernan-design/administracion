/**
* Class DashboardView extends of Backbone View
* @author TuProyecto || Desarrollador : @dortegon
* @link http://TuProyecto.com
*/

//Global App Backbone
app || (app = {});

(function ($, window, document, undefined) {

    app.DashboardView = Backbone.View.extend({
        el: '#dashboard-content',
        // template: _.template( ($('#dashboard-list-tpl').html() || '') ),
        events: {
            //
            'click #send_filter': 'handleFilterButtonClick'
        },
        parameters: {
			dataFilter: {}
        },

        /**
        * Constructor Method
        */
        initialize: function (opts) {
            var _this = this;
            // extends parameters
            if( opts !== undefined && _.isObject(opts.parameters) ){
                this.parameters = _.extend({}, this.parameters, opts.parameters);
            }

            var information = opts.model.attributes;

                // Configurar fechas por defecto
           var endDate = new Date().toISOString().split('T')[0]; // Fecha actual
           var startDate = new Date(); // Fecha actual
           startDate.setMonth(startDate.getMonth() - 5); // Restar 5 meses
           startDate = startDate.toISOString().split('T')[0];

           // Configurar los valores iniciales de los campos de fecha
           $('#start_filter').val(startDate);
           $('#end_filter').val(endDate);

               this.$rowContent = this.$('.tp_row-content');
               this.$wrapDashboard = this.$('#dashboard-list .container-dashboard');
               // Agregar elementos HTML para mostrar mensajes de error
               this.$startFilterError = $('<span class="text-danger"></span>');
               this.$endFilterError = $('<span class="text-danger"></span>');
               var input_start = $('#start_filter');
               var fechaActual = new Date().toISOString().split('T')[0];
               input_start.attr('max', fechaActual);
               var input_end = $('#end_filter');
               var fechaActual = new Date().toISOString().split('T')[0];
               input_end.attr('max', fechaActual);
               $('#start_filter').after(this.$startFilterError);
               $('#end_filter').after(this.$endFilterError);
               // Agregar evento change al campo start_filter
               $('#start_filter').on('change', function () {
                   var startDate = $(this).val();
                   $('#end_filter').attr('min', startDate);
               });

               // Agregar evento change al campo end_filter
               $('#end_filter').on('change', function () {
                   var endDate = $(this).val();
                   $('#start_filter').attr('max', endDate);
               });
               // Events Listeners
               this.listenTo( this.model, 'change', this.render );
               this.listenTo( this.model, 'request', this.loadSpinner);
               this.listenTo( this.model, 'sync', this.responseServer);
                // this.$('#dashboard-list').empty();
                // // return;
        },

        render: function () {

            // Initialize Attributes
            //(this.attributes);
            var _this = this,
                attributes = this.model.toJSON();

            if (this.attributes!=undefined) {
                var _this = this,
                    attributes = this.attributes;
            }
            // var miDiv = document.getElementsByClassName('container-dashboard');
            // (miDiv);

            // // Elimina todos los elementos hijos usando innerHTML
            // miDiv.innerHTML = '';
            // Número de columnas por fila
            var columnsPerRow = 3;
            // Contador para realizar un seguimiento del número de elementos en la fila actual
            var elementsInCurrentRow = 0;
            // Contador para realizar un seguimiento del índice actual
            var currentIndex = 0;
            var $currentRow;

            var colors = attributes.colors.data[0];

            (colors);

            if (Object.keys(attributes).length > 0) {
                this.$('#dashboard-list').removeClass('hidden');
                $.each(attributes, function (key, value) {

                    //switch (key) {
                              (key, value);
                            // Verifica si es necesario abrir una nueva fila
                            // if (elementsInCurrentRow === 0) {
                            //     $currentRow = $('<div class="row"></div>');
                            //     _this.$wrapDashboard.append($currentRow);
                            // }
                            // Crea un elemento canvas y ajústalo según sea necesario
                            if (key != "registros" && key != 'cursos' && key != 'entidades' && key != 'productos') {
                                var canvas = document.getElementById(key + '-chart');
                                if (canvas) {
                                   // Selecciona el elemento h2 por su clase y lo elimina
                                        // $('.container_'+key+'').remove();
                                        Chart.getChart(canvas).destroy();
                                        canvas.parentNode.removeChild(canvas);
                                 }
                                var canvas = document.createElement('canvas');
                                canvas.id = key + '-chart';
                                canvas.width = 200;
                                canvas.height = 200;

                                // Agrega el canvas a la columna actual

                                var $column = $('<div class="col-12 col-sm-6 col-md-6 col-lg-4"></div>');
                                var container = $('.container_'+key);
                                var tittle = $('<h5 class='+key+' style="color: #000;font-weight: bold;">'+value.name+'</h5>');
                                // $column.append(container);
                                // container.append(tittle);
                                container.append(canvas);
                                // $currentRow.append($column);


                                var ctx = canvas.getContext('2d');
                                var labels = [];
                                var data = [];
                                // value.data[0].map(function(item) {
                                //     data.push(item.y);
                                //     if (key=='usuarios') {
                                //         // Convertir la fecha en el formato deseado, por ejemplo, solo el nombre del mes
                                //         var fecha = new Date(); // Obtén la fecha actual
                                //         var nombreMes = fecha.toLocaleString('default', { month: 'long' });
                                //         labels.push(nombreMes);
                                //     } else {
                                //         // ("aqui");
                                //         labels.push(item.x);
                                //     }

                                // });
                                var datasets = [];
                                 if (key=='usuarios') {
                                    for (var i = 0; i < value.data[0].length; i++) {
                                        // (value.data[0][i]);
                                        var data =value.data[0][i];

                                        var datasetData = [];
                                        var numericKeys = Object.keys(data).filter(function (key) {
                                            return !isNaN(key);
                                        });
                                        var length = numericKeys.length;

                                        for (var j = 0; j < length; j++) {
                                            var entry = data[j];
                                            var fecha = new Date(entry.x);
                                            var nombreMes = fecha.toLocaleDateString('es-ES', {month: 'long' });
                                            datasetData.push({
                                                x: nombreMes,
                                                y: entry.y
                                            });
                                        }
                                        var year = new Date(entry.x);
                                        var year = year.toLocaleDateString('es-ES', {year: 'numeric'});

                                        // var color_back = colors[0].color_primario;
                                        // if (key=='mentorias') {
                                            color_back = [colors[0].color_primario, colors[0].color_secundario];
                                        // }
                                        var dataset = {
                                            label: year,
                                            data: datasetData,
                                            borderRadius: 10,
                                            borderColor: color_back,
                                            backgroundColor: color_back,
                                        };

                                        datasets.push(dataset);
                                    }
                                } else if(key=='roles' || key=='cursos' || key=='eventos'  || key=='mentorias'){
                                    for (var i = 0; i < value.data[0].length; i++) {
                                        if (key!='roles') {
                                            canvas.style.maxHeight = '315px';
                                        }

                                        // (value.data[0][i]);
                                        var data =value.data[0][i];

                                        var datasetData = [];
                                        var numericKeys = Object.keys(data).filter(function (key) {
                                            return !isNaN(key);
                                        });
                                        var length = numericKeys.length;

                                        for (var j = 0; j < length; j++) {
                                            var entry = data[j];
                                            datasetData.push({
                                                x: entry.x,
                                                y: entry.y
                                            });
                                        }

                                        var color_back = colors[0].color_primario;
                                        // if (key=='mentorias') {
                                        //     color_back = [colors[0].color_primario, colors[0].color_secundario];
                                        // }
                                        // (data[0].x);
                                        var dataset = {
                                            label: data[0].x,
                                            data: datasetData,
                                            borderRadius: 10,
                                            borderColor: color_back,
                                            backgroundColor: color_back,
                                        };

                                        datasets.push(dataset);
                                    }
                                } else {
                                    canvas.style.maxHeight = '315px';
                                    (value);
                                    value.data[0].map(function(item) {
                                        data.push(item.y);
                                            // ("aqui");
                                            labels.push(item.x);
                                    });
                                    var color_back = colors[0].color_primario;
                                    var dataset = {
                                        data: data,
                                        borderRadius: 10,
                                        borderColor: color_back,
                                            backgroundColor: color_back,
                                        // borderColor: value.colors[0],
                                        // backgroundColor: value.colors[1],
                                    };
                                    if (key=='discusiones') {
                                        color_back = colors[0].color_secundario;
                                        dataset = {
                                            label: 'Discusiones',
                                            data: data,
                                            borderRadius: 10,
                                            borderColor: color_back,
                                                backgroundColor: color_back,
                                            // borderColor: value.colors[0],
                                            // backgroundColor: value.colors[1],
                                        };
                                    } else if (key=='cantidad_convocatorias' || key=='articulos' || key=='etapas' || key=='mentores' || key == 'convocatorias_activas') {
                                        color_back = [colors[0].color_primario, colors[0].color_secundario];
                                        dataset = {
                                            data: data,
                                            borderRadius: 10,
                                            borderColor: color_back,
                                                backgroundColor: color_back,
                                            // borderColor: value.colors[0],
                                            // backgroundColor: value.colors[1],
                                        };
                                    }

                                    datasets.push(dataset);
                                }




                                //  (datasets);
                                // Configurar los datos del gráfico (reemplaza esto con tus propios datos)
                                var chartData = {
                                    labels: labels,
                                    datasets: datasets,
                                };

                                // (chartData);
                                // Configurar las opciones del gráfico (puedes personalizar esto según tus necesidades)
                                var chartOptions = value.options;

                                // Crear una instancia del gráfico Chart.js
                                var chart = new Chart(ctx, {
                                    type: value.type, // Tipo de gráfico (cambia según el tipo de gráfico que desees)
                                    data: chartData,
                                    options: chartOptions
                                });
                            } else if (key == "registros") {
                                var imagen_completos = $(".container_registros .registros_completos img");
                                imagen_completos.css("background", colors[0].color_primario);

                                var imagen_incompletos = $(".container_registros .registros_incompletos img");
                                imagen_incompletos.css("background", "#e15453");
                                var qty_complete = $(".container_registros .registros_completos .qty");
                                var value_qty_complete = value.data[0][0].y;
                                qty_complete.html(value_qty_complete);

                                var qty_incomplete = $(".container_registros .registros_incompletos .qty");
                                var value_qty_incomplete = value.data[0][1].y;
                                qty_incomplete.html(value_qty_incomplete);

                                var total_accuary = value_qty_complete + value_qty_incomplete;
                                var total_accuary_complete = '('+Math.round((value_qty_complete*100)/total_accuary)+'%)';
                                var total_accuary_incomplete = '('+Math.round((value_qty_incomplete*100)/total_accuary)+'%)';

                                var accuary_complete = $(".container_registros .registros_completos .accuary");
                                var accuary_incomplete = $(".container_registros .registros_incompletos .accuary");

                                accuary_complete.html(total_accuary_complete);
                                accuary_incomplete.html(total_accuary_incomplete);

                            } else if (key=="cursos"){
                                ('here');
                                var cursos_activos = $(".container_cursos_activos .cursos_activos");
                                var value_cursos_activos = value.data[0][0][0].y;
                                cursos_activos.html(value_cursos_activos);

                                var cursos_inactivos = $(".container_cursos_inactivos .cursos_inactivos");
                                var value_cursos_inactivos = 0;
                                if (value.data[0][1]) {
                                    value_cursos_inactivos = value.data[0][1][0].y;
                                }
                                cursos_inactivos.html(value_cursos_inactivos);
                            } else if (key=="entidades"){
                                ('here');
                                var empresas = $(".container_entidades_empresas .empresas_registradas");
                                var value_empresas = value.data[0][0].y;
                                empresas.html(value_empresas);

                                var proyectos = $(".container_entidades_proyectos .proyectos_registrados");
                                var value_proyectos = "";
                                var proyectos_data = value.data[0];
                                for (var index = 0; index < proyectos_data.length; index++) {
                                    if (proyectos_data[index].x=='Proyecto (No está constituido ante ningún ente de control)') {
                                        value_proyectos = proyectos_data[index].y;
                                    }

                                }
                                proyectos.html(value_proyectos);
                            } else if (key=='productos') {
                                (value);
                                var productos = value.data[0];
                                var container = $('.products-block');
                                container.empty();
                                var h5 = '<h5 class="cursos" style="display: block;color: #000;width: 100%;font-weight: bold;padding-left: 10px;font-size: 1.2vw;">Productos y/o servicios (Marketplace)</h5><p style="color: #464647;"><b>(Esta información es general)</b></p>';
                                    container.append(h5);
                                    var total_productos = 0;
                                    for (var i = 0; i < productos.length; i++) {

                                        var valor = productos[i].y;

                                        total_productos += valor;
                                    }

                                for (var index = 0; index < productos.length; index++) {
                                    var producto = productos[index];
                                     // Crear el contenedor div
                                    var divContainer = document.createElement('div');
                                    divContainer.className = 'col-12 col-sm-6 col-md-6 col-lg-4';

                                    // Seleccionar el tipo de producto (Activo, Inactivo, En revision)
                                    var estadoProducto = producto.x; // Cambiar esto según la lógica que determines

                                    var valor = producto.y;

                                    var porcentaje = '('+Math.round((valor * 100)/total_productos)+'%)';
                                    // Crear el div interno con la estructura proporcionada
                                    var innerHTML = '<div class="container_productos_' + estadoProducto + '" style="margin: 5px;border: none;padding: 10px;border-radius: 10px;background: #dcd7d3;">'
                                                + '<h5 class="cursos" style="color: #000;font-weight: bold;font-size: 1.2vw;display:inline;"> ' + estadoProducto + '</h5>'
                                                + '<div class="value_block" style="display: inline;padding-left: 40%;right: 0;">'
                                                + '<span class="cursos_' + estadoProducto + '" style="font-size: 2vw; color: #000; font-weight: bold;">' + valor + '</span>'
                                                + '<span class="accuary" style="font-size: vw;">' + porcentaje +'</span>'
                                                + '</div></div>';

                                    divContainer.innerHTML = innerHTML;

                                    // Agregar el contenedor al documento
                                    container.append(divContainer);

                                }
                            }



                            // Verifica si es necesario cerrar la fila actual
                    //}

                    // var view = new app.EchartsView({
                    //     parameters: {
                    //         data: {
                    //             title: !_.isUndefined(value['name']) ? value['name'] : key,
                    //             elementId: key,
                    //             type: value.type,
                    //             values: value['data'],
                    //             colors: value['colors']
                    //         }
                    //     }
                    // });
                    // // view.render();
                    // // _this.$el.append( view.render() );
                    // _this.$wrapDashboard.append( view.render().el );
                    // view.referenceChartsViews();
                });
            } else {
                _this.$('#dashboard-list').empty();
                // // return;
            }


        },

        handleFilterButtonClick: function () {
            var startDate = $('#start_filter').val();
            var endDate = $('#end_filter').val();

            // Restablecer los mensajes de error
            this.$startFilterError.text('');
            this.$endFilterError.text('');

            if (!startDate) {
                this.$startFilterError.text('Por favor, ingrese una fecha de inicio.');
            }

            if (!endDate) {
                this.$endFilterError.text('Por favor, ingrese una fecha de fin.');
            }

            if (startDate && endDate) {
                // Ambos campos de fecha tienen valores, puedes realizar la solicitud Ajax aquí
                this.performAjaxRequest(startDate, endDate);
            }
        },

        performAjaxRequest: function (startDate, endDate) {
            var _this = this;
            // this.$wrapDashboard.empty();
            $.ajax({
                url: Route.route('/'), // Reemplaza con la URL de tu backend
                method: 'GET', // Puedes ajustar el método HTTP según tu necesidad
                data: {
                    start_date: startDate,
                    end_date: endDate
                },
                dataType: 'json', // Ajusta el tipo de datos de respuesta según tu necesidad
                success: function (data) {
                    // Maneja la respuesta exitosa del servidor aquí
                    // data contiene la respuesta del servidor en formato JSON
                    // (data);
                    _this.attributes = data;
                    console.log(data);
                    if (data.length == 0) {
                        this.$wrapDashboard.empty();
                    } else{
                        _this.render();
                    }

                    // Puedes actualizar tu gráfico o realizar otras acciones en función de la respuesta
                    // _this.updateChart(data);

                    // Si deseas re-renderizar la vista después de la solicitud Ajax, puedes hacerlo aquí
                    // _this.render();
                },
                error: function (xhr, textStatus, errorThrown) {
                    // Maneja errores de la solicitud Ajax aquí
                    console.error('Error en la solicitud Ajax:', textStatus, errorThrown);
                }
            });
        },
        /**
		* fires libraries js and functions
		*/
        ready: function () {
            if( typeof window.initComponent.configEllipsis == 'function' ){
                window.initComponent.configEllipsis();
            }
        },

        /**
		* Load spinner on the request
		*/
		loadSpinner: function ( target, xhr, opts ) {

			window.Misc.setSpinner( this.$rowContent );
		},

        /**
		* response of the server
		*/
		responseServer: function ( target, resp, opts ) {

			window.Misc.removeSpinner({
				'wrap': this.$rowContent,
				'direct': false
			});
		}
    });

})(jQuery, this, this.document);
