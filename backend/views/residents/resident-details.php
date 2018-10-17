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
    ->orderBy('createdAt DESC')
    ->one();
$equipments = Equipment::find()
    ->where(['flatUuid' => $model['flatUuid']])
    ->all();
$counts = 0;
$equipment_photo[] = '';
foreach ($equipments as $next_equipment) {
    $photoEquipment = PhotoEquipment::find()
        ->where(['equipmentUuid' => $next_equipment['uuid']])
        ->orderBy('createdAt DESC')
        ->one();
    $equipment[$counts] = $next_equipment;
    $equipment_photo[$counts] = $photoEquipment;
    $counts++;
    if ($counts > 2) break;
}

?>
<div class="kv-expand-row kv-grid-demo">
    <div class="kv-expand-detail skip-export kv-grid-demo">
        <div class="skip-export kv-expanded-row kv-grid-demo" data-index="0" data-key="1">
            <div class="kv-detail-content">
                <h3>
                    <small><?php echo 'ул.' . $model['flat']['house']['street']->title . ', дом ' .
                            $model['flat']['house']->number . ', квартира' .
                            $model['flat']->number ?></small>
                </h3>
                <div class="row">
                    <div class="col-sm-2">
                        <div class="img-thumbnail img-rounded text-center">
                            <?php
                            if ($photoFlat != null)
                                echo '<img src="' .
                                    Html::encode(MainFunctions::getImagePath('flat', $photoFlat['uuid'])) . '
                                    " style="padding:2px;width:100%">';
                            ?>
                            <div class="small text-muted"><?php echo $photoFlat['createdAt'] ?></div>
                        </div>
                    </div>
                    <?php
                    for ($t = 0; $t < $counts; $t++) {
                        if ($equipment_photo[$t] != '') {
                            echo '<div class="col-sm-2">';
                            echo '<div class="img-thumbnail img-rounded text-center">';
                            echo '<img style="padding:2px;width:100%" src="' .
                                Html::encode(MainFunctions::getImagePath('equipment', $equipment_photo[$t]['uuid'])) . '">
                            <div class="small text-muted">' . $equipment_photo[$t]['createdAt'] . '</div></div></div>';
                        }

                        echo '<div class="col-sm-2">';
                        echo '<table class="table table-bordered table-condensed table-hover small kv-table">
                                <tr class="success">
                                <th colspan="2" class="text-center text-danger">Параметры оборудования</th>
                                </tr>
                                <tr><td>Тип</td><td class="text-right">' . $equipment[$t]['equipmentType']->title . '</td></tr>
                                <tr><td>Статус</td>
                                <td class="text-right">' . $equipment[$t]['equipmentStatus']->title . '</td></tr>';
                        echo '<tr><td>Серийный номер</td><td class="text-right">' . $equipment[$t]['serial'] . '</td></tr>';
                        echo '<tr><td>Дата монтажа</td><td class="text-right">' . $equipment[$t]['testDate'] . '</td></tr>
                              </table>';
                        echo '</div>';
                    }
                    ?>
                </div>
            </div>
        </div>
    </div>
</div>
