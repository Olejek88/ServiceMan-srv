<?php

use yii\web\JsExpression;

$this->title = 'Календарь нарядов';

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

        <?php
        $JSCode = <<<EOF
function(start, end) {
    window.location.replace("/orders/create");
}
EOF;
        $JSDropEvent = <<<EOF
function( event, delta, revertFunc, jsEvent, ui, view ) {
    if (window.keyCode == 16) {
	var jqxhr = $.post("/orders/copy",{ event_start: ""+event.start.format()+"", event_id: ""+event.id+"" },
	function() {
	    //alert( "success" );
	})
	.done(function() {
	    //alert( "second success" );
	})
	.fail(function() {
	    alert( "error" );
	})
	.always(function() {
	    $('#calendar').fullCalendar('refetchEvents');
	    $('#calendar').fullCalendar('rerenderEvents');
	    window.location.replace("/orders/calendar");
	});  
    }
    else {
	var jqxhr = $.post("/orders/move",{ event_start: ""+event.start.format()+"", event_id: ""+event.id+"" },	
	function() {
	    //alert( "success" );
	})
	.done(function() {
	    //alert( "second success" );
	})
	.fail(function() {
	    //alert( "error" );
	})
	.always(function() {
	    //alert( "finished" );
	    $('#calendar').fullCalendar('refetchEvents');
	    $('#calendar').fullCalendar('rerenderEvents');
	    window.location.replace("/orders/calendar");
	});  
    }
}
EOF;
        $JSDragStopEvent = <<<EOF
function( event, jsEvent, ui, view ) {
    //alert("Dropped stop ");
    //alert('Coordinates: ' + jsEvent.pageX + ',' + jsEvent.pageY);
    //alert('Coordinates: ' + $(window).width() + ',' + $(document).width());
    if ((jsEvent.pageY<100) && jsEvent.pageX>($(window).width()-110)) {
	var jqxhr = $.post("/orders/remove",{ event_id: ""+event.id+"" },
	function() {
	})
	.done(function() {
	})
	.fail(function() {
	    alert( "error" );
	})
	.always(function() {
	    window.location.replace("/orders/calendar");
	});  
    }
}
EOF;

        $JSEventClick = <<<EOF
function(calEvent, jsEvent, view) {
//    window.location.replace("http://app.toirus.ru/orders/");
}
EOF;

        ?>

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
                'eventDrop' => new JsExpression($JSDropEvent),
                'eventDragStop' => new JsExpression($JSDragStopEvent),
                'select' => new JsExpression($JSCode),
                'eventClick' => new JsExpression($JSEventClick),
                'defaultDate' => date('Y-m-d'),
                'defaultView' => 'month',
                'customButtons' => [
                    'delete' => [
                        'text' => ' ',
                        'click' => function () {
                            //you code
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
