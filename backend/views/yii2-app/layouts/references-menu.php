<div class="panel panel-default" style="float: left; width: 20%; padding: 3px">
    <?php

    use yii\helpers\Html;

    echo Html::a("Предупреждения статус", ['../alarm-status/create'], ['class' => 'btn btn-info btn100']);
    echo Html::a("Предупреждения типы", ['../alarm-type/create'], ['class' => 'btn btn-primary btn100']);
    echo Html::a("Документация типы", ['../documentation-type/create'], ['class' => 'btn btn-primary btn100']);
    echo Html::a("Контрагенты типы", ['../contragent-type/create'], ['class' => 'btn btn-primary btn100']);
    echo Html::a("Журнал типы записей", ['../equipment-register-type/create'], ['class' => 'btn btn-primary btn100']);
    echo Html::a("Статусы оборудования", ['../equipment-status/create'], ['class' => 'btn btn-info btn100']);
    echo Html::a("Типы оборудования", ['../equipment-type/create'], ['class' => 'btn btn-primary btn100']);
    echo Html::a("Иженерные системы", ['../equipment-system/create'], ['class' => 'btn btn-primary btn100']);
    echo Html::a("Статусы домов", ['../house-status/create'], ['class' => 'btn btn-info btn100']);
    echo Html::a("Типы домов", ['../house-type/create'], ['class' => 'btn btn-primary btn100']);
    echo Html::a("Статусы объектов", ['../object-status/create'], ['class' => 'btn btn-info btn100']);
    echo Html::a("Типы объектов", ['../object-type/create'], ['class' => 'btn btn-primary btn100']);
    echo Html::a("Статусы запрооов", ['../request-status/create'], ['class' => 'btn btn-info btn100']);
    echo Html::a("Типы запросов", ['../request-type/create'], ['class' => 'btn btn-primary btn100']);
    echo Html::a("Типы задач", ['../task-type/create'], ['class' => 'btn btn-primary btn100']);
    echo Html::a("Вердикты задач", ['../task-verdict/create'], ['class' => 'btn btn-primary btn100']);
    echo Html::a("Статусы работ", ['../work-status/create'], ['class' => 'btn btn-info btn100']);
    ?>
</div>
