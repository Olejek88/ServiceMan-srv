<?php

use yii\helpers\Html;
use yii\web\JsExpression;

$this->title = 'Календарь задач';

/* @var $events */

?>

<script type="text/javascript">
    document.addEventListener("keydown", keyDownTextField, false);

    function keyDownTextField(e) {
        window.keyCode = e.keyCode;
    }
</script>

<div class="modal remote fade" id="modalAddOrder">
    <div class="modal-dialog" style="width: 800px">
        <div class="modal-content loader-lg" id="modalContent">
        </div>
    </div>
</div>

<div class="site-index">
    <div class="body-content">
<?php
    $JSCode = <<<EOF
    function(start, end) {
        $.get("/task/new",{ start: ""+start.format()+"" },
        function() {	})
        .done(function(data) {
            $('#modalAddOrder').modal('show');
            $('#modalContent').html(data);
    	})
    }
EOF;
        $JSDropEvent = <<<EOF
    function( event, delta, revertFunc, jsEvent, ui, view ) {
        if (window.keyCode == 16) {
	        var jqxhr = $.post("/task/copy",{ event_start: ""+event.start.format()+"", event_id: ""+event.id+"" },
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
	            window.location.replace("/task/calendar");
	        });  
        }
        else {
	        var jqxhr = $.post("/task/move",{ event_start: ""+event.start.format()+"", event_id: ""+event.id+"" },	
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
	            window.location.replace("/task/calendar");
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
	    var jqxhr = $.post("/task/remove",{ event_id: ""+event.id+"" },
	    function() {
	    })
	    .done(function() {
	    })
	    .fail(function() {
	        alert( "error" );
	    })
	    .always(function() {
	        window.location.replace("/task/calendar");
	    });  
        }
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
                'defaultDate' => date('Y-m-d'),
                'defaultView' => 'month',
                'columnFormat' => 'ddd',
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
