<?php
/* @var $tasks_completed
 * @var $tasks
 * @var $users
 */

use common\components\MainFunctions;
use kartik\select2\Select2;
use kartik\widgets\DatePicker;
use yii\helpers\Html;

$this->title = Yii::t('app', 'ТОИРУС ЖКХ::Отчет по исполнителям');

$start_date = '2018-12-31';
$end_date = '2021-12-31';
$user = '';
$system = '';
if (isset($_GET['end_time']))
    $end_date = $_GET['end_time'];
if (isset($_GET['start_time']))
    $start_date = $_GET['start_time'];
if (isset($_GET['user']))
    $user = $_GET['user'];
if (isset($_GET['system_select']))
    $system = $_GET['system_select'];

?>
<div id="requests-table-container" class="panel table-responsive kv-grid-container" style="overflow: auto">
    <form action="">
        <table style="width: 1200px; padding: 3px">
            <tr>
                <td style="width: 300px">
                    <?php
                    echo DatePicker::widget([
                            'name' => 'start_time',
                            'value' => $start_date,
                            'removeButton' => false,
                            'pluginOptions' => [
                                'autoclose' => true,
                                'format' => 'yyyy-mm-dd'
                            ]
                        ]) . '</td><td style="width: 300px">' .
                        DatePicker::widget([
                            'name' => 'end_time',
                            'value' => $end_date,
                            'removeButton' => false,
                            'pluginOptions' => [
                                'autoclose' => true,
                                'format' => 'yyyy-mm-dd'
                            ]
                        ]) . '<td style="width: 300px">' .
                        Select2::widget([
                            'id' => 'user',
                            'name' => 'user',
                            'value' => $user,
                            'language' => 'ru',
                            'data' => $users,
                            'options' => ['placeholder' => 'Выберите пользователя...'],
                            'pluginOptions' => [
                                'allowClear' => true
                            ],
                        ]) . '</td><td>&nbsp;</td><td style="width: 100px">' . Html::submitButton(Yii::t('app', 'Выбрать'), [
                            'class' => 'btn btn-success']) . '';
                    ?>
                </td>
            </tr>
        </table>
    </form>
    <br/>
    <table class="kv-grid-table table table-hover table-bordered table-condensed kv-table-wrap">
        <thead>
        <tr class="kartik-sheet-style" style="height: 20px; background-color: green; color: white">
            <th colspan="10">Выполненные задачи</th>
        </tr>
        <tr class="kartik-sheet-style" style="height: 20px">
            <th class="text-center kv-align-middle" data-col-seq="0" style="width: 3%;"></th>
            <th class="text-center kv-align-middle" data-col-seq="1" style="width: 20%;">Задача</th>
            <th class="text-center kv-align-center kv-align-middle" data-col-seq="2" style="width: 25%;">Элемент</th>
            <th class="text-center kv-align-middle" data-col-seq="3">Адрес</th>
            <th class="text-center kv-align-center kv-align-middle" data-col-seq="4">Статус</th>
            <th class="text-center kv-align-center kv-align-middle" data-col-seq="5">Вердикт</th>
            <th class="kv-align-center kv-align-middle" data-col-seq="6">Дата назначения</th>
            <th class="kv-align-center kv-align-middle" data-col-seq="8">Дата выполнения</th>
            <th class="kv-align-center kv-align-middle" data-col-seq="9">Автор</th>
            <th class="kv-align-center kv-align-middle" data-col-seq="10">Комментарий</th>

        </tr>
        </thead>
        <tbody>
        <?php
        $count = 1;
        foreach ($tasks_completed as $data)
            if ($data) {
                echo '<tr data-key="1">';
                echo '<td class="table_class kv-align-middle" style="width: 40px; text-align: center;" data-col-seq="0">' . $count . '</td>';
                echo '<td class="kv-align-center kv-align-middle" data-col-seq="1">' . $data['taskTemplate']->title . '</td>';
                echo '<td class="kv-align-center kv-align-middle" data-col-seq="2">' . $data['equipment']['title'] . '</td>';
                echo '<td class="kv-align-center kv-align-middle" data-col-seq="2">' . $data['equipment']['object']->getFullTitle() . '</td>';
                echo '<td class="kv-align-center kv-align-middle" style="text-align: center" data-col-seq="2">' . MainFunctions::getColorLabelByStatus($data['workStatus'], 'work_status_edit') . '</td>';
                echo '<td class="kv-align-center kv-align-middle" style="text-align: center" data-col-seq="2">' . $data['taskVerdict']['title'] . '</td>';
                if (strtotime($data->taskDate))
                    $value = date("d-m-Y H:i", strtotime($data->taskDate));
                else
                    $value = 'не назначена';
                echo '<td class="kv-align-center kv-align-middle" style="text-align: center" data-col-seq="2">' . $value . '</td>';
                if (strtotime($data->endDate))
                    $value = date("d-m-Y H:i", strtotime($data->endDate));
                else
                    $value = 'не закрыта';
                echo '<td class="kv-align-center kv-align-middle" style="text-align: center" data-col-seq="2">' . $value . '</td>';
                if ($data['author'])
                    $value = $data['author']->name;
                else
                    $value = 'отсутствует';
                echo '<td class="kv-align-center kv-align-middle" style="text-align: center" data-col-seq="2">' . $value . '</td>';
                if (isset($data['comment'])) {
                    $value = $data['comment'];
                } else {
                    $value = 'неизвестно';
                }
                echo '<td class="kv-align-center kv-align-middle" data-col-seq="2">' . $value . '</td>';
                echo '</tr>';
                $count++;
            }
        ?>
        </tbody>
    </table>
    <br/>
    <table class="kv-grid-table table table-hover table-bordered table-condensed kv-table-wrap">
        <thead>
        <tr class="kartik-sheet-style" style="height: 20px; background-color: grey; color: white">
            <th colspan="10">Не выполненные задачи</th>
        </tr>
        <tr class="kartik-sheet-style" style="height: 20px">
            <th class="text-center kv-align-middle" data-col-seq="0" style="width: 3%;"></th>
            <th class="text-center kv-align-middle" data-col-seq="1" style="width: 20%;">Задача</th>
            <th class="text-center kv-align-center kv-align-middle" data-col-seq="2" style="width: 25%;">Элемент</th>
            <th class="text-center kv-align-middle" data-col-seq="3">Адрес</th>
            <th class="text-center kv-align-center kv-align-middle" data-col-seq="4">Статус</th>
            <th class="text-center kv-align-center kv-align-middle" data-col-seq="5">Вердикт</th>
            <th class="kv-align-center kv-align-middle" data-col-seq="6">Дата назначения</th>
            <th class="kv-align-center kv-align-middle" data-col-seq="8">Дата выполнения</th>
            <th class="kv-align-center kv-align-middle" data-col-seq="9">Автор</th>
            <th class="kv-align-center kv-align-middle" data-col-seq="10">Комментарий</th>

        </tr>
        </thead>
        <tbody>
        <?php
        $count = 1;
        foreach ($tasks as $data)
            if ($data) {
                echo '<tr data-key="1">';
                echo '<td class="table_class kv-align-middle" style="width: 40px; text-align: center;" data-col-seq="0">' . $count . '</td>';
                echo '<td class="kv-align-center kv-align-middle" data-col-seq="1">' . $data['taskTemplate']->title . '</td>';
                echo '<td class="kv-align-center kv-align-middle" data-col-seq="2">' . $data['equipment']['title'] . '</td>';
                echo '<td class="kv-align-center kv-align-middle" data-col-seq="2">' . $data['equipment']['object']->getFullTitle() . '</td>';
                echo '<td class="kv-align-center kv-align-middle" style="text-align: center" data-col-seq="2">' . MainFunctions::getColorLabelByStatus($data['workStatus'], 'work_status_edit') . '</td>';
                echo '<td class="kv-align-center kv-align-middle" style="text-align: center" data-col-seq="2">' . $data['taskVerdict']['title'] . '</td>';
                if (strtotime($data->taskDate))
                    $value = date("d-m-Y H:i", strtotime($data->taskDate));
                else
                    $value = 'не назначена';
                echo '<td class="kv-align-center kv-align-middle" style="text-align: center" data-col-seq="2">' . $value . '</td>';
                if (strtotime($data->endDate))
                    $value = date("d-m-Y H:i", strtotime($data->endDate));
                else
                    $value = 'не закрыта';
                echo '<td class="kv-align-center kv-align-middle" style="text-align: center" data-col-seq="2">' . $value . '</td>';
                if ($data['author'])
                    $value = $data['author']->name;
                else
                    $value = 'отсутствует';
                echo '<td class="kv-align-center kv-align-middle" style="text-align: center" data-col-seq="2">' . $value . '</td>';
                if (isset($data['comment'])) {
                    $value = $data['comment'];
                } else {
                    $value = 'неизвестно';
                }
                echo '<td class="kv-align-center kv-align-middle" data-col-seq="2">' . $value . '</td>';
                echo '</tr>';
                $count++;
            }
        ?>
        </tbody>
    </table>
</div>
