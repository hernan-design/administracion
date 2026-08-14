/**
* Class EchartsView extends of Backbone View
* @author TuProyecto || Desarrollador : @dortegon
* @link http://TuProyecto.com
*/

//Global App Backbone
app || (app = {});

(function ($, window, document, undefined) {

    app.EchartsView = Backbone.View.extend({
        template: _.template( ($('#dashboard-list-tpl').html() || '') ),
        events: {
            //
        },
        parameters: {
            data: {}
        },

        /**
        * Constructor Method
        */
        initialize : function(opts) {

            if ( opts !== undefined && _.isObject(opts.parameters) ) {
                this.parameters = $.extend({},this.parameters, opts.parameters);
            };

        },

        /*
        * Render View Element
        */
        render: function() {
            var _this = this,
                attributes = this.parameters.data;

            this.$el.html( this.template( attributes ) );
            return this;
        },

        referenceChartsViews: function () {
            var valuesEchart = [],
                nameEchart = [],
                arrayValues = [];

            if(this.parameters.data['type'] == 'pie'){
                $.each(this.parameters.data['values'], function(key, value){
                    // valuesEchart
                    arrayValues = {
                        value: value,
                        name: key
                    };

                    nameEchart.push(key);
                    valuesEchart.push(arrayValues);
                });

                this.echartPie(nameEchart, valuesEchart);
            }

            if(this.parameters.data['type'] == 'bar'){
                $.each(this.parameters.data['values'], function(key, value){

                    nameEchart.push(key);
                    valuesEchart.push(value);
                });

                this.echartBar(nameEchart, valuesEchart);
            }
        },

        // Pie
        echartPie: function (nameEchart, valuesEchart) {
            // specify chart configuration item and data
            var option = {
                title : {
                    text: this.parameters.data['title'],
                    x:'center',
                    top: 40
                },
                tooltip : {
                    trigger: 'item',
                    formatter: "{a} <br/>{b} : {c} ({d}%)"
                },
                legend: {
                    bottom: 10,
                    left: 'center',
                    data: nameEchart
                },
                series : [
                    {
                        name: this.parameters.data['title'],
                        type: this.parameters.data['type'],
                        radius: ['30%', '50%'],
                        center: ['50%', '60%'],
                        label: {
                            normal: {
                                show: false,
                                position: 'left'
                            }
                        },
                        labelLine: {
                            normal: {
                                show: false
                            }
                        },
                        data: valuesEchart,
                        color: this.parameters.data['colors'],
                        itemStyle: {
                            emphasis: {
                                shadowBlur: 10,
                                shadowOffsetX: 0,
                                shadowColor: 'rgba(0, 0, 0, 0.5)'
                            }
                        }
                    }
                ]
            };

            this.renderEchart(option);
        },

        // Bar
        echartBar: function (nameEchart, valuesEchart) {
            var option = {
                title : {
                    text: this.parameters.data['title'],
                    x:'center'
                },
                color: this.parameters.data['colors'],
                tooltip : {
                    trigger: 'axis',
                    axisPointer : {
                        type : 'shadow'
                    }
                },
                grid: {
                    left: '3%',
                    right: '4%',
                    bottom: '3%',
                    containLabel: true
                },
                xAxis : [
                    {
                        type : 'category',
                        data : nameEchart,
                        axisTick: {
                            alignWithLabel: true
                        },
                        axisLabel: {
                            interval: 0,
                            rotate: 45
                        },
                    }
                ],
                yAxis : [
                    {
                        type : 'value'
                    }
                ],
                series : [
                    {
                        name: this.parameters.data['title'],
                        type: this.parameters.data['type'],
                        barWidth: '30%',
                        data: valuesEchart
                    }
                ]
            };

            this.renderEchart(option);
        },

        renderEchart: function (option) {
            var _this = this;

            // based on prepared DOM, initialize echarts instance use configuration item and data specified to show chart
            var eChart = echarts.init(_this.$('#echart_'+_this.parameters.data['elementId'])[0]).setOption(option);
        }
    });

})(jQuery, this, this.document);
