<?php
/* @var $order common\models\Orders */
/* @var $tree mixed */

use common\models\OrderStatus;
use common\models\StageStatus;
use common\models\StageVerdict;
use common\models\TaskStatus;
use common\models\TaskVerdict;
use yii\helpers\Html;

$this->title = Yii::t('app', 'Наряд :: '.$order['title']);
?>
<!-- Content Header (Page header) -->
<section class="content-header">
    <h1>
        <?php echo $order['title'] ?>
        <small>Хронология выполнения наряда</small>
    </h1>
</section>
<style>
    .btn { text-transform: none; }
</style>
<section class="content">
    <div class="row"><div class="col-md-12">
            <ul class="timeline">
                <li class="time-label">
                <?php
                if ($order['startDate']) {
                    echo '<span class="bg-gray">Назначен на: ';
                    echo date("M j, Y", strtotime($order['startDate']));
                    echo '</span>';
                }
                ?>
                </li>
                <?php
                if ($order['orderStatusUuid'] != OrderStatus::NEW_ORDER) {
                    for ($taskCount = 0; $taskCount < count($tree); $taskCount++) {
                        $task = $tree[$taskCount];
                        print '<li>';
                        print '<i class="fa fa-tasks bg-blue"></i>';
                        print '<div class="timeline-item">
                                   <span class="time"><i class="fa fa-clock-o"></i>
                                   ' . $task['startDate'] . ' - ' . $task['endDate'] . '</span>
                                   <h3 class="timeline-header">' . $task['title'] . ' 
                                   [#<a href="/task/view?id=' . $task['id'] . '">' . $task['id'] . ']</a></h3>
                                   <div class="timeline-body">
                                   <a class="btn btn-default btn-xs">' . $task['type'] . '</a>
                                   ' . $task['description'] . '<br/>                                  
                                   <i class="fa fa-user"></i>&nbsp;' . $task['user'] . '<br/>
                                   <i class="fa fa-comments"></i>&nbsp;' . $task['comment'] . '</div>
                                   <div class="timeline-footer">';
                        switch ($task['status']) {
                            case TaskStatus::COMPLETE:
                                print '<a class="btn btn-success btn-xs">Закончена</a>&nbsp;';
                                break;
                            case TaskStatus::CANCELED:
                                print '<a class="btn btn-danger btn-xs">Отменена</a>&nbsp;';
                                break;
                            case TaskStatus::UN_COMPLETE:
                                print '<a class="btn btn-warning btn-xs">Не закончен</a>&nbsp;';
                                break;
                            default:
                                print '<a class="btn btn-warning btn-xs">Не определен</a>&nbsp;';
                        }
                        if ($task['verdict'] == TaskVerdict::INSPECTED)
                            print '<a class="btn btn-success btn-xs">Вердикт: Осмотрено</a>';
                        else
                            print '<a class="btn btn-danger btn-xs">Вердикт: не установлен</a>';
                        print '</div></div>';
                        print '<br/>';
                        print '<ul class="timeline stage">';
                        if (isset($tree[$taskCount]['child']) > 0)
                            for ($stageCount = 0; $stageCount < count($tree[$taskCount]['child']); $stageCount++) {
                                $stage = $tree[$taskCount]['child'][$stageCount];
                                print '<li>';
                                print '<i class="fa fa-list bg-green"></i>';
                                print '<div class="timeline-item">
                                   <span class="time"><i class="fa fa-clock-o"></i>
                                   ' . $stage['startDate'] . ' - ' . $stage['endDate'] . '</span>
                                   <h3 class="timeline-header">' . $stage['title'] . ' 
                                   [<a href="/task/view?id=' . $stage['id'] . '">' . $stage['id'] . ']</a></h3>
                                   <div class="timeline-body">
                                   <a class="btn btn-default btn-xs">' . $stage['type'] . '</a>
                                   ' . $stage['description'] . '<br/>
                                   <i class="fa fa-comments"></i>&nbsp;' . $stage['comment'] . '<br/>
                                   <i class="fa fa-cogs"></i>&nbsp;Оборудование: ' . $stage['equipment'] . '<br/>
                                   <i class="fa fa-map-marker"></i>&nbsp;Локация: ' . $stage['location'] . '</div>
                                   <div class="timeline-footer">';
                                switch ($stage['status']) {
                                    case StageStatus::COMPLETE:
                                        print '<a class="btn btn-success btn-xs">Закончена</a>&nbsp;';
                                        break;
                                    case StageStatus::CANCELED:
                                        print '<a class="btn btn-danger btn-xs">Отменена</a>&nbsp;';
                                        break;
                                    case StageStatus::UN_COMPLETE:
                                        print '<a class="btn btn-warning btn-xs">Не закончен</a>&nbsp;';
                                        break;
                                    default:
                                        print '<a class="btn btn-warning btn-xs">Не определен</a>&nbsp;';
                                }
                                if ($stage['verdict'] == StageVerdict::NO_VERDICT)
                                    print '<a class="btn btn-success btn-xs">Вердикт: не указан</a>';
                                else
                                    print '<a class="btn btn-danger btn-xs">Вердикт: не установлен</a>';
                                print '</div></div>';
                                print '<br/>';
                                print '<ul class="timeline stage">';

                                //var_dump($stage['child']);
                                if (isset($stage['child']) > 0)
                                    for ($operationCount = 0; $operationCount < count($tree[$taskCount]['child'][$stageCount]['child']); $operationCount++) {
                                        $operation = $tree[$taskCount]['child'][$stageCount]['child'][$operationCount];
                                        print '<li>';
                                        print '<i class="fa fa-list bg-green"></i>';
                                        print '<div class="timeline-item">
                                               <span class="time"><i class="fa fa-clock-o"></i>&nbsp;' .
                                                $operation['startDate'] . ' - ' . $operation['endDate'] . '</span>
                                                <h3 class="timeline-header">' . $operation['title'] . ' 
                                                [<a href="/operation/view?id=' . $operation['id'] . '">' . $operation['id'] . ']</a></h3>
                                                <div class="timeline-body">';

                                        foreach ($operation['files'] as $file) {
                                            $url = Html::encode($file->getImageUrl());
                                            if ($url!='')
                                                echo '<img src="'.$url.'" style="width:150px" class="margin">';
                                        }

                                        print '<a class="btn btn-default btn-xs">' . $operation['type'] . '</a>
                                                ' . $operation['description'] . '<br/>
                                                ' . $operation['comment'] . '</div>
                                                <div class="timeline-footer">';
                                        switch ($operation['status']) {
                                            case StageStatus::COMPLETE:
                                                print '<a class="btn btn-success btn-xs">Закончена</a>&nbsp;';
                                                break;
                                            case StageStatus::CANCELED:
                                                print '<a class="btn btn-danger btn-xs">Отменена</a>&nbsp;';
                                                break;
                                            case StageStatus::UN_COMPLETE:
                                                print '<a class="btn btn-warning btn-xs">Не закончен</a>&nbsp;';
                                                break;
                                            default:
                                                print '<a class="btn btn-warning btn-xs">Не определен</a>&nbsp;';
                                        }
                                        if ($operation['verdict'] == StageVerdict::NO_VERDICT)
                                            print '<a class="btn btn-success btn-xs">Вердикт: не указан</a>';
                                        else
                                            print '<a class="btn btn-danger btn-xs">Вердикт: не установлен</a>';
                                        print '</div></div>';
                                        print '</li>';
                                    }
                                print '</ul>';
                                print '</li>';
                            }
                        print '</ul>';
                        print '</li>';
                    }
                }
                ?>

                <li class="time-label">
                    <?php
                    if ($order['openDate'] > 0) {
                        if ($order['orderStatusUuid'] == OrderStatus::COMPLETE) echo '<span class="bg-green">';
                        if ($order['orderStatusUuid'] == OrderStatus::UN_COMPLETE) echo '<span class="bg-gray">';
                        if ($order['orderStatusUuid'] == OrderStatus::CANCELED) echo '<span class="bg-red">';
                        if ($order['orderStatusUuid'] == OrderStatus::IN_WORK) echo '<span class="bg-gray">';
                        if ($order['orderStatusUuid'] == OrderStatus::NEW_ORDER) echo '<span class="bg-green">';
                        echo date("F j, Y", strtotime($order['openDate']));
                        echo '</span>';
                    }
                    ?>
                </li>

                <li class="time-label">
                    <?php
                    if ($order['closeDate'] > 0) {
                        if ($order['orderStatusUuid'] == OrderStatus::COMPLETE) echo '<span class="bg-green">';
                        if ($order['orderStatusUuid'] == OrderStatus::UN_COMPLETE) echo '<span class="bg-gray">';
                        if ($order['orderStatusUuid'] == OrderStatus::CANCELED) echo '<span class="bg-red">';
                        if ($order['orderStatusUuid'] == OrderStatus::IN_WORK) echo '<span class="bg-gray">';
                        if ($order['orderStatusUuid'] == OrderStatus::NEW_ORDER) echo '<span class="bg-green">';
                        echo date("F j, Y", strtotime($order['closeDate']));
                        echo '</span>';
                    }
                    ?>
                </li>
            </ul>
        </div>
    </div>
</section>
