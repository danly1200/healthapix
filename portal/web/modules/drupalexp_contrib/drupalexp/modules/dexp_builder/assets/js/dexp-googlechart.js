(function ($, Drupal) {
    "use strict";
    Drupal.behaviors.dexp_shortcodes_googlechart = {
        attach: function () {
            $('.dexp-google-chart').once('shortcode').each(function () {                
                var $this = $(this),
                    id = $this.attr('id'),
                    type = $this.data('chart-type'),
                    values = $this.data('chart-values'),
                    options = $this.data('chart-options');
                if(options.trim().length){
                    options = eval('(' + options + ')');
                }else{
                    options = {
                        legend: 'none'
                    }
                }
                if(!$.isArray(values)){
                    values = eval('(' + values + ')');
                }
                if(type == 'bar'){
                    google.charts.load('current', {'packages':['bar']});
                }else if(type == 'column'){
                    google.charts.load('current', {'packages':['corechart', 'bar']});
                }else{
                    google.charts.load('current', {'packages':['corechart']});
                }
                google.charts.setOnLoadCallback(function(){
                    var data = new google.visualization.arrayToDataTable(values);
                    switch(type){
                        case 'bar':
                            var chart = new google.visualization.BarChart(document.getElementById(id));
                            break;
                        case 'column':
                            var chart = new google.visualization.ColumnChart(document.getElementById(id));
                            break;
                        case 'line':
                            var chart = new google.visualization.LineChart(document.getElementById(id));
                            break;
                        case 'pie':
                            var chart = new google.visualization.PieChart(document.getElementById(id));
                            break;
                        default:
                            var chart = new google.visualization.AreaChart(document.getElementById(id));
                    }
                    chart.draw(data,options);
                });
            });
        }
    };
})(jQuery, Drupal);
