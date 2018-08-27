<?php

use common\models\Defect;
use common\models\Equipment;
use common\models\ExternalEvent;
use common\models\ExternalSystem;
use common\models\ExternalTag;
use yii\helpers\Html;

/* @var $model common\models\ExternalSystem */

$system = ExternalSystem::find()
    ->where(['uuid' => $model['uuid']])
    ->one();
$models = ExternalSystem::findOne($model['_id']);

$tags = ExternalTag::find()
    ->where(['systemUuid' => $model['uuid']])
    ->limit(5)
    ->all();
$events=[];
foreach ($tags as $tag) {
    $tag_events = ExternalEvent::find()
        ->where(['tagUuid' => $tag['uuid']])
        ->all();
    foreach ($tag_events as $event) {
        $events[count($events)]=$event;
    }
}
/*
$events = ExternalEvent::find()
    ->limit(5)
    ->all();
*/
?>
<div class="kv-expand-row kv-grid-demo">
    <div class="kv-expand-detail skip-export kv-grid-demo">
        <div class="skip-export kv-expanded-row kv-grid-demo" data-index="0" data-key="1"><div class="kv-detail-content">
                <h3><?php echo $system['title'] ?> </h3>
                <div class="row">
                    <div class="col-sm-4">
                        <table class="table table-bordered table-condensed table-hover small kv-table">
                            <tr class="success">
                                <th colspan="5" class="text-center text-danger">Теги</th>
                            </tr>
                            <tr class="active">
                                <th class="text-center">#</th>
                                <th>Тег</th>
                                <th>Значение</th>
                                <th>Оборудование</th>
                                <th>Действие</th>
                            </tr>
                            <?php
                            foreach ($tags as $tag) {
                                echo '<tr>
                                          <td class="text-center">'.$tag['_id'].'</td>
                                          <td>'.$tag['tag'].'</td>
                                          <td>'.$tag['value'].'</td>
                                          <td>'.$tag['equipment']->title.'</td>
                                          <td>'.$tag['actionType']->title.'</td>
                                          </tr>';
                            }
                            ?>
                        </table>
                    </div>
                    <div class="col-sm-2">
                    </div>
                    <div class="col-sm-4">
                        <table class="table table-bordered table-condensed table-hover small kv-table">
                            <tr class="danger">
                                <th colspan="6" class="text-center text-success">События</th>
                            </tr>
                            <tr class="active">
                                <th class="text-center">#</th>
                                <th>Тег</th>
                                <th>Действие</th>
                                <th class="text-right">Дата</th>
                                <th>Статус</th>
                                <th>Вердикт</th>
                            </tr>
                            <?php
                                foreach ($events as $event) {
                                    echo '<tr>
                                          <td class="text-center">'.$event['_id'].'</td>
                                          <td>'.$event['externalTag']->tag.'</td>
                                          <td>'.$event['actionType']->title.'</td>
                                          <td class="text-right">'.$event['date'].'</td>
                                          <td>'.$event['status'].'</td>
                                          <td>'.$event['verdict'].'</td>
                                          </tr>';
                                }
                            ?>
                        </table>
                    </div>
                </div>
            </div>
        </div>
    </div>
</div>
