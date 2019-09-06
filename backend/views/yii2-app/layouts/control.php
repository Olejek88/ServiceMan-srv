<?php

use common\models\Journal;
use common\models\OrderStatus;
use common\models\Settings;
use yii\helpers\Html;
use yii\widgets\Pjax;

$journals = Journal::find()->select('*')->orderBy('date DESC')->limit(8)->all();

$settings = Settings::find()->all();
$period = "0";
$move = 0;
foreach ($settings as $setting) {
    if ($setting['uuid'] == Settings::SETTING_TASK_PAUSE_BEFORE_WARNING)
        $period = $setting['parameter'];
    if ($setting['uuid'] == Settings::SETTING_SHOW_WARNINGS)
        $warnings = $setting['parameter'];
}

?>
<!-- Control Sidebar -->
<aside class="control-sidebar control-sidebar-dark">
    <!-- Create the tabs -->
    <ul class="nav nav-tabs nav-justified control-sidebar-tabs">
        <li><a href="#control-sidebar-home-tab" data-toggle="tab"><i class="fa fa-home"></i></a></li>
        <li><a href="#control-sidebar-settings-tab" data-toggle="tab"><i class="fa fa-gears"></i></a></li>
    </ul>
    <!-- Tab panes -->
    <div class="tab-content">
        <!-- Home tab content -->
        <div class="tab-pane active" id="control-sidebar-home-tab">
            <h3 class="control-sidebar-heading">Последняя активность</h3>
            <ul class="control-sidebar-menu">
                <?php
                $count = 0;
                foreach ($journals as $journal) {
                    print '<li><a href="javascript:void(0)">';
                    if ($journal['type'] == 'task')
                        print '<i class="menu-icon fa fa-tasks bg-green"></i>';
                    if ($journal['type'] == 'request')
                        print '<i class="menu-icon fa fa-reply bg-blue"></i>';
                    if ($journal['type'] == 'user')
                        print '<i class="menu-icon fa fa-user bg-yellow"></i>';
                    print '<div class="menu-info">
                                <h4 class="control-sidebar-subheading">' . $journal['date'] . '</h4>
                           <p>' . $journal['title'] . '</p>
                           </div></a></li>';
                }
                ?>
            </ul>
        </div>

        <div class="tab-pane" id="control-sidebar-stats-tab">Настройки</div>
        <div class="tab-pane" id="control-sidebar-settings-tab">
            <?php Pjax::begin(['id' => 'options']); ?>
            <?= Html::beginForm(['../site/config'], 'post', ['data-pjax' => '', 'class' => 'form-inline']);
            ?>
            <h3 class="control-sidebar-heading">Основные настройки</h3>
            <input type="hidden" value="<?= $_SERVER['REQUEST_URI'] ?>" id="url" name="url">
            <div class="form-group">
                <label class="control-sidebar-subheading">
                    Время на получение задачи<br/>
                    <select id="period" name="period" style="color: #0a0a0a; font-size: 13px; font-family: inherit">
                        <option value="1" <?= $period == '1' ? ' selected="selected"' : ''; ?>>1 час</option>
                        <option value="2" <?= $period == '2' ? ' selected="selected"' : ''; ?>>2 часа</option>
                        <option value="4" <?= $period == '4' ? ' selected="selected"' : ''; ?>>4 часа</option>
                        <option value="12" <?= $period == '12' ? ' selected="selected"' : ''; ?>>12 часов</option>
                        <option value="24" <?= $period == '24' ? ' selected="selected"' : ''; ?>>1 день</option>
                        <option value="48" <?= $period == '48' ? ' selected="selected"' : ''; ?>>2 дня</option>
                        <option value="10000" <?= $period == '10000' ? ' selected="selected"' : ''; ?>>Не определено
                        </option>
                    </select>
                </label>
                <p>
                    Время на получение задачи до выдачи предупреждения
                </p>
            </div>

            <div class="form-group">
                <label class="control-sidebar-subheading">
                    Предупреждения<br/>
                </label>
                <input type="checkbox" id="warning"
                       name="warning" <?php if ($warnings == 1) echo "checked='checked'"; ?> />
                <p>
                    Показывать предупреждения в таблице задач
                </p>
            </div>
            <br/>
            <br/>
            <button type="submit" class="btn btn-info btn-sm">сохранить настройки</button>
            <?php
            echo Html::endForm();
            Pjax::end();
            ?>
        </div>
    </div>
</aside>
<div class="control-sidebar-bg"></div>
