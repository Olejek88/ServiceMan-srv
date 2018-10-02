<?php

use backend\assets\AdminLteAsset;
use miloschuman\highcharts\Highcharts;
use miloschuman\highcharts\HighchartsAsset;

AdminLteAsset::register($this);

HighchartsAsset::register($this)->withScripts(['highcharts', 'modules/gantt', 'modules/gantt']);
?>


<div class="measured-value-index">
</div>

<?php
echo Highcharts::widget([
    'options' => [
        'title' => ['text' => 'Наряды ремонтных групп'],
        'xAxis' => [
            'currentDateIndicator' => 'true',
            'scrollbar' => [
                'enabled' => 'true'
            ],
        ],
        'series' => [
            [   'name' => 'Наряды ремонтных групп',
                'data' => [$chart]]
            ]
        ]
    ]);
?>
