<?php

use app\commands\MainFunctions;
use common\models\Equipment;
use common\models\EquipmentRegister;
use common\models\Measure;
use common\models\Photo;
use yii\helpers\Html;

/* @var $model Equipment */

$equipment = Equipment::find()
    ->where(['uuid' => $model['uuid']])
    ->one();

$measures = Measure::find()
    ->where(['equipmentUuid' => $equipment['uuid']])
    ->all();

$photo = Photo::find()
    ->where(['objectUuid' => $equipment['uuid']])
    ->orderBy('createdAt DESC')
    ->one();

$events = [];
$equipment_registers = EquipmentRegister::find()
    ->where(['=', 'equipmentUuid', $equipment['uuid']])
    ->all();

foreach ($equipment_registers as $register) {
    $text = '<a class="btn btn-default btn-xs">' . $register['user']->name . '</a><br/>
                <i class="fa fa-cogs"></i>&nbsp;Тип: ' . $register['registerType']['title'] . '<br/>';
    $event = '<li>';
    $event .= '<i class="fa fa-calendar bg-green"></i>';
    $event .= '<div class="timeline-item">';
    $event .= '<span class="time"><i class="fa fa-clock-o"></i> ' . date("M j, Y h:i", strtotime($register['date'])) . '</span>';
    $event .= '<h3 class="timeline-header"><a href="#">'.$register['description'].'</a></h3>';
    $event .= '<div class="timeline-body">' . $text . '</div>';
    $event .= '</div></li>';

    $events[] = ['date' => $register['date'], 'event' => $event];
}

$sort_events = \common\components\MainFunctions::array_msort($events, ['date' => SORT_DESC]);

?>
<div class="kv-expand-row kv-grid-demo">
    <div class="kv-expand-detail skip-export kv-grid-demo">
        <div class="skip-export kv-expanded-row kv-grid-demo" data-index="0" data-key="1">
            <div class="kv-detail-content">
                <div class="row">
                    <div class="col-sm-2">
                        <div class="img-thumbnail img-rounded text-center">
                            <?php
                            if ($photo != null) {
                                echo '<img src="' .
                                    Html::encode(MainFunctions::getImagePath('equipment', $photo['uuid'])) . '
                                        " style="padding:2px;width:100%">';
                            }
                            ?>
                            <div class="small text-muted"><?php echo $equipment['serial'] ?></div>
                        </div>
                        <table class="table table-bordered table-condensed table-hover small kv-table">
                            <tr class="success">
                                <th colspan="2" class="text-center text-info">Информация</th>
                            </tr>
                            <tr>
                                <td>Дата ввода в эксплуатацию</td>
                                <td class="text-right"><?php
                                    if (isset($equipment['inputDate']))
                                        echo date('d-m-Y', strtotime($equipment['inputDate']));
                                    else
                                        echo 'не задано';
                                    ?></td>
                            </tr>
                            <tr>
                                <td>Дата поверки</td>
                                <td class="text-right"><?php
                                    if (isset($equipment['inputDate']))
                                        echo date('d-m-Y', strtotime($equipment['inputDate']));
                                    else
                                        echo 'не задано';
                                    ?></td>
                            </tr>
                            <tr>
                                <td>Период поверки</td>
                                <td class="text-right"><?php
                                    if (isset($equipment['period']))
                                        echo $equipment['period'];
                                    else
                                        echo 'не задан';
                                    ?></td>
                            </tr>
                            <tr>
                                <td>Дата следующей поверки</td>
                                <td class="text-right"><?php echo date('d-m-Y', strtotime($equipment['nextDate'])) ?></td>
                            </tr>
                        </table>
                    </div>
                    <div class="col-sm-4">
                        <section class="content-header">
                            <h1>
                                История работы над элементом
                            </h1>
                        </section>

                        <!-- Main content -->
                        <section class="content">
                            <div class="row">
                                <ul class="timeline timeline-inverse">
                                    <?php
                                    foreach ($sort_events as $event) {
                                        echo $event['event'];
                                    }
                                    ?>
                                </ul>
                            </div>
                        </section>
                    </div>
                    <?php
                    if (count($measures)) {
                        echo '<div class="col-sm-3">';
                        echo '<table class="table table-bordered table-condensed table-hover small kv-table">';
                        echo '<tr class="success">
                                    <th colspan="2" class="text-center text-danger">Последние показания</th>
                                  </tr>';
                        foreach ($measures as $measure) {
                            echo '<tr><td>' . $measure['date'] . '</td>
                                          <td class="text-right">' . $measure['value'] . '</td></tr>';
                        }
                        echo '</table>';
                        echo '</div>';
                    }
                    ?>
                    <?php
                    if (count($measures)) {
                        echo '<div class="col-sm-3">';
                        echo $this->render('equipment-details-chart', ['equipmentUuid' => $equipment['uuid']]);
                        echo '</div>';
                    }
                    ?>
                </div>
            </div>
        </div>
    </div>
</div>
