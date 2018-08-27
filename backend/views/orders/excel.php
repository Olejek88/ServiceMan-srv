<?php
/* @var $searchModel backend\models\OrderSearch */

\common\components\ExcelGrid::widget([
        'dataProvider' => $dataProvider,
        'filterModel' => $searchModel,
        //'extension'=>'xlsx',
        //'filename'=>'excel',
        'properties' =>[
            //'creator'	=>'',
            //'title' 	=> '',
            //'subject' 	=> '',
            //'category'	=> '',
            //'keywords' 	=> '',
            //'manager' 	=> '',
            //'description'=>'BSOURCECODE',
            //'company'	=>'BSOURCE',
        ],
        'columns' => [
            ['class' => 'yii\grid\SerialColumn'],
            '_id',
            'startDate',
        ],
    ]);
