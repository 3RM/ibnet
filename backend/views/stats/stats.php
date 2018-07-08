<?php

use common\models\Utility;  
use yii\web\View;

/* @var $this yii\web\View */

$this->title = 'Stats';
?>
<?php $this->registerJsFile("https://code.highcharts.com/highcharts.src.js"); ?>

<div class="site-index">

    <div class="body-content">
        <p>Todo: List basic high level stats from Google Analytics.</p>
        
        <div id="container1" style="height:400px;"></div>
        <div id="container2" style="height:400px;"></div>
        <div id="container3" style="height:400px;"></div>

    </div> 
</div> 

<?php
$script = <<< JS
     $(function () { 
        var TotalChart = Highcharts.chart('container1', {
            title: {
                text: 'Profiles - Rolling 12 Month'
            },
            xAxis: {
                type: 'datetime',
                dateTimeLabelFormats: {
                    day: '%b %e, %y'
                }
            },
            yAxis: {
                title: {
                    text: 'Count',
                }
            },
            series: [{
                data: [$total],
                pointStart: Date.UTC($yr, $mo, $dy),
                pointInterval: 7 * 24 * 3600 * 1000,  
                name: 'Total'
            }, {
                data: [$org],
                pointStart: Date.UTC($yr, $mo, $dy),
                pointInterval: 7 * 24 * 3600 * 1000,  
                name: 'Organizations'
            }, {
                data: [$ind],
                pointStart: Date.UTC($yr, $mo, $dy),
                pointInterval: 7 * 24 * 3600 * 1000,  
                name: 'Individuals'
            }]
        
        });

        var OrgChart = Highcharts.chart('container2', {
            title: {
                text: 'Organizations'
            },
           xAxis: {
                type: 'datetime',
                dateTimeLabelFormats: {
                    day: '%e %b, %y'
                }
            },
            yAxis: {
                title: {
                    text: 'Count',
                }
            },
            series: [{
                data: [$church],
                pointStart: Date.UTC($yr, $mo, $dy),
                pointInterval: 7 * 24 * 3600 * 1000, 
                name: 'Church'
            }, {
                data: [$missionAgcy],
                pointStart: Date.UTC($yr, $mo, $dy),
                pointInterval: 7 * 24 * 3600 * 1000,
                name: 'Mission Agency'
            }, {
                data: [$flwship],
                pointStart: Date.UTC($yr, $mo, $dy),
                pointInterval: 7 * 24 * 3600 * 1000,
                name: 'Fellowship'
            }, {
                data: [$ass],
                pointStart: Date.UTC($yr, $mo, $dy),
                pointInterval: 7 * 24 * 3600 * 1000,
                name: 'Association'
            }, {
                data: [$camp],
                pointStart: Date.UTC($yr, $mo, $dy),
                pointInterval: 7 * 24 * 3600 * 1000,
                name: 'Camp'
            }, {
                data: [$school],
                pointStart: Date.UTC($yr, $mo, $dy),
                pointInterval: 7 * 24 * 3600 * 1000,
                name: 'School'
            }, {
                data: [$print],
                pointStart: Date.UTC($yr, $mo, $dy),
                pointInterval: 7 * 24 * 3600 * 1000,
                name: 'Print Ministry'
            }, {
                data: [$music],
                pointStart: Date.UTC($yr, $mo, $dy),
                pointInterval: 7 * 24 * 3600 * 1000,
                name: 'Music Ministry'
            }, {
                data: [$special],
                pointStart: Date.UTC($yr, $mo, $dy),
                pointInterval: 7 * 24 * 3600 * 1000,
                name: 'Special Ministry'
            }]
        
        });

        var IndChart = Highcharts.chart('container3', {
            title: {
                text: 'Individuals'
            },
            xAxis: {
                type: 'datetime',
                dateTimeLabelFormats: {
                    day: '%e %b, %y'
                }
            },
            yAxis: {
                title: {
                    text: 'Count',
                }
            },
            series: [{
                data: [],
                pointStart: Date.UTC($yr, $mo, $dy),
                pointInterval: 7 * 24 * 3600 * 1000,
                name: 'Pastor'
            }, {
                data: [$evangelist],
                pointStart: Date.UTC($yr, $mo, $dy),
                pointInterval: 7 * 24 * 3600 * 1000,
                name: 'Evangelist'
            }, {
                data: [$missionary],
                pointStart: Date.UTC($yr, $mo, $dy),
                pointInterval: 7 * 24 * 3600 * 1000,
                name: 'Missionary'
            }, {
                data: [$chaplain],
                pointStart: Date.UTC($yr, $mo, $dy),
                pointInterval: 7 * 24 * 3600 * 1000,
                name: 'Chaplain'
            }, {
                data: [$staff],
                pointStart: Date.UTC($yr, $mo, $dy),
                pointInterval: 7 * 24 * 3600 * 1000,
                name: 'Staff'
            }]
        
        });
    });
JS;
$this->registerJs($script);
?>