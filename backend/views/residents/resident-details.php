<?php

use app\commands\MainFunctions;
use common\models\Equipment;
use common\models\PhotoEquipment;
use common\models\PhotoFlat;
use yii\helpers\Html;

/* @var $model \common\models\Equipment */
/* @var $resident \common\models\Resident */

$photoFlat = PhotoFlat::find()
    ->where(['flatUuid' => $model['flatUuid']])
    ->one();
$equipments = Equipment::find()
    ->where(['flatUuid' => $model['flatUuid']])
    ->all();
$counts=0;
foreach ($equipments as $next_equipment) {
    $photo = PhotoEquipment::find()
        ->where(['equipmentUuid' => $next_equipment['uuid']])
        ->all();
    $equipment[$counts]=$next_equipment;
    $equipment_photo[$counts]=$photo;
    $counts++;
    if ($counts>2) break;
}

?>
<div class="kv-expand-row kv-grid-demo">
    <div class="kv-expand-detail skip-export kv-grid-demo">
        <div class="skip-export kv-expanded-row kv-grid-demo" data-index="0" data-key="1"><div class="kv-detail-content">
                <h3><small><?php echo 'ул.'.$model['flat']['house']['street']->title.', дом '.
                                    $model['flat']['house']->number.', квартира'.
                                    $model['flat']->number ?></small>
                </h3>
                <div class="row">
                    <div class="col-sm-2">
                        <div class="img-thumbnail img-rounded text-center">
                            <img src="<?php echo Html::encode(MainFunctions::getImagePath('flat',$model['uuid'])) ?>" style="padding:2px;width:100%">
                            <div class="small text-muted"><?php echo $model['flat']['flatStatus']->title ?></div>
                        </div>
                    </div>
                    <?php
                    for ($t=0; $t<$counts; $t++) {
                        echo '<div class="img-thumbnail img-rounded text-center">';
                        echo '<img style="padding:2px;width:100%" src="'.
                            Html::encode(MainFunctions::getImagePath('equipment',$equipment[0]['uuid'])).'">';
                        echo '<div class="small text-muted">' . $equipment[$t]['equipmentStatus']->title . '</div>
                              </div>
                              <div class="col-sm-4">
                                <table class="table table-bordered table-condensed table-hover small kv-table">
                                <tr class="success">
                                <th colspan="2" class="text-center text-danger">Параметры оборудования</th>
                                </tr>
                                <tr><td>Тип</td><td class="text-right">' . $equipment[$t]['type']->title . '</td></tr>
                                <tr><td>Статус</td>
                                <td class="text-right">' . $equipment[$t]['status']->title . '</td></tr>';
                        echo '<tr><td>Серийный номер</td><td class="text-right">'.$equipment[$t]['serialNumber'].'</td></tr>';
                        echo '<tr><td>Дата монтажа</td><td class="text-right">'.$equipment[$t]['startDate'].'</td></tr>
                              </table></div>';
                        }
                    ?>
                    <div class="col-sm-1">
                        <div class="kv-button-stack">
                            <?php
                            echo Html::a('<span class="glyphicon glyphicon-book"></span>',
                                ['/documentation','uuid' => $model['uuid']], ['class'=>'btn btn-default btn-lg',
                                    'type' => 'button', 'title' => 'Документация', 'data-toggle' => 'tooltip']);
                            ?>
                            <?php
                            echo Html::a('<span class="glyphicon glyphicon-list"></span>',
                                ['/equipment-register','uuid' => $model['uuid']], ['class'=>'btn btn-default btn-lg',
                                    'type' => 'button', 'title' => 'Журнал', 'data-toggle' => 'tooltip']);
                            ?>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </div>
</div>
