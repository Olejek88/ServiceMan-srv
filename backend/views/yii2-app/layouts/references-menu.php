<div class="panel panel-default" style="float: left; width: 20%; padding: 3px">
    <?php

    use yii\helpers\Html;

    echo Html::a("Предупреждения статус", ['../alarm-status'], ['class' => 'btn btn-info btn100']);
    echo Html::a("Предупреждения типы", ['../alarm-type'], ['class' => 'btn btn-primary btn100']);
    echo Html::a("Документация типы", ['../documentation-type'], ['class' => 'btn btn-primary btn100']);
    echo Html::a("Контрагенты типы", ['../contragent-type'], ['class' => 'btn btn-primary btn100']);
    echo Html::a("Журнал типы записей", ['../equipment-register-type'], ['class' => 'btn btn-primary btn100']);
    echo Html::a("Статусы элементов", ['../equipment-status'], ['class' => 'btn btn-info btn100']);
    echo Html::a("Типы элементов", ['../equipment-type'], ['class' => 'btn btn-primary btn100']);
    echo Html::a("Иженерные системы", ['../equipment-system'], ['class' => 'btn btn-primary btn100']);
    echo Html::a("Типы домов", ['../house-type'], ['class' => 'btn btn-primary btn100']);
    echo Html::a("Статусы объектов", ['../object-status'], ['class' => 'btn btn-info btn100']);
    echo Html::a("Типы объектов", ['../object-type'], ['class' => 'btn btn-primary btn100']);
    echo Html::a("Статусы запросов", ['../request-status'], ['class' => 'btn btn-info btn100']);
    echo Html::a("Характеры обращений", ['../request-type'], ['class' => 'btn btn-primary btn100']);
    echo Html::a("Типы задач", ['../task-type'], ['class' => 'btn btn-primary btn100']);
    echo Html::a("Вердикты задач", ['../task-verdict'], ['class' => 'btn btn-primary btn100']);
    echo Html::a("Статусы работ", ['../work-status'], ['class' => 'btn btn-info btn100']);
    echo Html::a("Типы измерений", ['../measure-type'], ['class' => 'btn btn-primary btn100']);
    echo Html::a("Типы дефектов", ['../defect-type'], ['class' => 'btn btn-primary btn100']);

    echo Html::a("Шаблоны задач", ['../task-template'], ['class' => 'btn btn-info btn100']);
    ?>
</div>
