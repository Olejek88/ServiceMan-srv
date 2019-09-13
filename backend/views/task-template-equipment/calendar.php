<?php

$this->title = 'Календарь задач по обслуживанию';

/* @var $events */

?>

<script type="text/javascript">
    //    var keyCode;
    document.addEventListener("keydown", keyDownTextField, false);

    function keyDownTextField(e) {
        window.keyCode = e.keyCode;
    }
</script>

<div class="site-index">
    <div class="body-content">
        <?= yii2fullcalendar\yii2fullcalendar::widget(array(
            'id' => 'calendar',
            'options' => [
                'lang' => 'ru',
            ],
            'clientOptions' => [
                'selectable' => true,
                'selectHelper' => true,
                'droppable' => true,
                'editable' => true,
                'defaultDate' => date('Y-m-d'),
                'defaultView' => 'month',
                'columnFormat' => 'ddd',
                'displayEventTime' => false,
                'customButtons' => [
                    'delete' => [
                        'text' => ' ',
                        'click' => function () {
                        }
                    ]
                ],
                'header' => [
                    'left' => 'prev,next today month,agendaWeek,listYear',
                    'center' => 'title',
                    'right' => 'delete'
                ],
            ],
            'ajaxEvents' => $events,
        ));
        ?>
    </div>
</div>
