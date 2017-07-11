<?php

/* @var $this yii\web\View */

$this->title = 'Stats';
?>
<?php $this->registerJsFile("https://code.highcharts.com/highcharts.src.js"); ?>

<div class="site-index">

    <div class="body-content">

        <div id="container1" style="width:60%; height:300px;"></div>
         <div id="container2" style="width:60%; height:300px;"></div>
          <div id="container3" style="width:60%; height:300px;"></div>
        

    </div>
</div>
<?php
$script = <<< JS
     $(function () { 

        var myChart = Highcharts.chart('container1', {
            title: {
                text: 'Profiles'
            },
            xAxis: {
                categories: ['Jan', 'Feb', 'Mar', 'Apr', 'May', 'Jun', 'Jul', 'Aug', 'Sep', 'Oct', 'Nov', 'Dec']
            },
            series: [{
                data: [5, 6, 7, 8, null, 10, 11, null, 13],
                step: 'center',
                name: 'Total'
            }, {
                data: [9, 10, 11, 12, null, 14, 15, null, 17],
                step: 'left',
                name: 'Organizations'
            }, {
                data: [5, 6, 7, 8, null, 10, 11, null, 13],
                step: 'center',
                name: 'Individuals'
            }]
        
        });

        var myChart = Highcharts.chart('container2', {
            title: {
                text: 'Organizations'
            },
            xAxis: {
                categories: ['Jan', 'Feb', 'Mar', 'Apr', 'May', 'Jun', 'Jul', 'Aug', 'Sep', 'Oct', 'Nov', 'Dec']
            },
            series: [{
                data: [5, 6, 7, 8, null, 10, 11, null, 13],
                step: 'center',
                name: 'Church'
            }, {
                data: [9, 10, 11, 12, null, 14, 15, null, 17],
                step: 'left',
                name: 'Mission Agency'
            }, {
                data: [5, 6, 7, 8, null, 10, 11, null, 13],
                step: 'center',
                name: 'Fellowship'
            }, {
                data: [5, 6, 7, 8, null, 10, 11, null, 13],
                step: 'center',
                name: 'Association'
            }, {
                data: [5, 6, 7, 8, null, 10, 11, null, 13],
                step: 'center',
                name: 'Camp'
            }, {
                data: [5, 6, 7, 8, null, 10, 11, null, 13],
                step: 'center',
                name: 'School'
            }, {
                data: [5, 6, 7, 8, null, 10, 11, null, 13],
                step: 'center',
                name: 'Print Ministry'
            }, {
                data: [5, 6, 7, 8, null, 10, 11, null, 13],
                step: 'center',
                name: 'Music Ministry'
            }, {
                data: [5, 6, 7, 8, null, 10, 11, null, 13],
                step: 'center',
                name: 'Special Ministry'
            }]
        
        });

        var myChart = Highcharts.chart('container3', {
            title: {
                text: 'Individuals'
            },
            xAxis: {
                categories: ['Jan', 'Feb', 'Mar', 'Apr', 'May', 'Jun', 'Jul', 'Aug', 'Sep', 'Oct', 'Nov', 'Dec']
            },
            series: [{
                data: [5, 6, 7, 8, null, 10, 11, null, 13],
                step: 'center',
                name: 'Pastor'
            }, {
                data: [9, 10, 11, 12, null, 14, 15, null, 17],
                step: 'left',
                name: 'Evangelist'
            }, {
                data: [5, 6, 7, 8, null, 10, 11, null, 13],
                step: 'center',
                name: 'Missionary'
            }, {
                data: [5, 6, 7, 8, null, 10, 11, null, 13],
                step: 'center',
                name: 'Chaplain'
            }, {
                data: [5, 6, 7, 8, null, 10, 11, null, 13],
                step: 'center',
                name: 'Staff'
            }]
        
        });
    });
JS;
$this->registerJs($script);
?>