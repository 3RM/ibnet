<?php

/* @var $this yii\web\View */

$this->title = 'IBNet Administration Panel';
?>
<?php $this->registerJsFile("https://code.highcharts.com/highcharts.src.js"); ?>

<div class="site-index">

    <div class="body-content">

        <div id="container" style="width:100%; height:400px;"></div>
        

    </div>
</div>
<?php
$script = <<< JS
     $(function () { 
        var myChart = Highcharts.chart('container', {
            chart: {
                type: 'bar'
            },
            title: {
                text: 'Fruit Consumption'
            },
            xAxis: {
                categories: ['Apples', 'Bananas', 'Oranges']
            },
            yAxis: {
                title: {
                    text: 'Fruit eaten'
                }
            },
            series: [{
                name: 'Jane',
                data: [1, 0, 4]
            }, {
                name: 'John',
                data: [5, 7, 3]
            }]
        });
    });
JS;
$this->registerJs($script);
?>