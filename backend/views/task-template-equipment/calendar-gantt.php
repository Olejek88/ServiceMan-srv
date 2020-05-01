<?php

$this->title = 'Календарь задач по обслуживанию';

/* @var $events
 * @var $categories
 * @var $max
 */

//$this->registerJsFile('/js/vendor/lib/frappe-gantt/frappe-gantt.js');
//$this->registerJsFile('/js/vendor/lib/HighCharts/highcharts-gantt.js');
//$this->registerJsFile('/js/vendor/lib/HighCharts/modules/gantt.js');
$this->registerCssFile('/js/vendor/lib/HighCharts/css/highcharts.css');

?>
<script type="text/javascript" src="/js/vendor/lib/HighCharts/highcharts-gantt.js"></script>
<script type="text/javascript" src="/js/vendor/lib/HighCharts/modules/draggable-points.js"></script>

<div class="col-md-12">
    <div class="panel-group">
        <div class="panel panel-default">
            <div class="panel-heading" style="background-color: #3c8dbc; color: white">
                <h4 class="panel-title"><a href="" data-toggle="collapse">План-график работ</a></h4>
            </div>
            <div class="panel-collapse collapse am-collapse in">
                <div class="panel-body" style="position: relative;">
                    <div class="main-container">
                        <div id="container"></div>
                    </div>
                </div>
            </div>
        </div>
    </div>
</div>
</div>

<script>
    var today = new Date(),
        day = 1000 * 60 * 60 * 24,
        each = Highcharts.each,
        reduce = Highcharts.reduce,
//        btnShowDialog = document.getElementById('btnShowDialog'),
//         btnAddTask = document.getElementById('btnAddTask'),
//         btnCancelAddTask = document.getElementById('btnCancelAddTask'),
//         addTaskDialog = document.getElementById('addTaskDialog'),
//         inputName = document.getElementById('inputName'),
//         selectEquipment = document.getElementById('selectEquipment'),
//         chkMilestone = document.getElementById('chkMilestone'),
        isAddingTask = false;

    // Set to 00:00:00:000 today
    today.setUTCHours(0);
    today.setUTCMinutes(0);
    today.setUTCSeconds(0);
    today.setUTCMilliseconds(0);
    today = today.getTime();


    // btnRemoveTask = document.getElementById('btnRemoveSelected'),

    // Update disabled status of the remove button, depending on whether or not we
    // have any selected points.
    // function updateRemoveButtonStatus() {
    //     var chart = this.series.chart;
    //     // Run in a timeout to allow the select to update
    //     setTimeout(function () {
    //         btnRemoveTask.disabled = !chart.getSelectedPoints().length ||
    //             isAddingTask;
    //     }, 10);
    // }

    /* Add button handlers for add/remove tasks */
    // btnRemoveTask.onclick = function () {
    //     var points = chart.getSelectedPoints();
    //     each(points, function (point) {
    //         point.remove();
    //     });
    // };


    Highcharts.setOptions({
        lang: {
            months: [
                'Январь', 'Февраль', 'Март', 'Апрель',
                'Май', 'Июнь', 'Июль', 'Август',
                'Сентябрь', 'Октябрь', 'Ноябрь', 'Декабрь'
            ],
            weekdays: [
                'Воскресенье', 'Понедельник', 'Вторник', 'Среда', 'Четверг',
                'Пятница', 'Суббота'
            ],
            shortMonths: [
                'Янв', 'Фев', 'Мар', 'Апр',
                'Май', 'Июн', 'Июл', 'Авг',
                'Сен', 'Окт', 'Нбр', 'Дкб'
            ],
            shortWeekdays: [
                'Вс', 'Пн', 'Вт', 'Ср',
                'Чт', 'Пт', 'Сб'
            ],
            rangeSelectorZoom: 'Масштаб',
            rangeSelectorFrom: 'От',
            rangeSelectorTo: 'До'
        }
    });

    var series, map = Highcharts.map;
    var events = [];
    events = <?php echo json_encode($events) ?>;
    series = events.map(function (event, i) {
        var dateIdx = 0;
        var data = event.data.map(function (task_event) {
            return {
                id: task_event.id,
                title: task_event.title,
                start: task_event.start,
                end: task_event.end,
                y: i,
                dateIdx: dateIdx++,
                user: task_event.user

            };
        });
        return {
            name: event.title,
            title: event.title,
            address: event.address,
            data: data,
            id: i,
            y: i
        };
    });

    var start = '';

    // Create the chart
    var chart = Highcharts.ganttChart('container', {
        series: series,
        chart: {
            spacingLeft: 1,
            styledMode: true,
            spacing: [4, 4, 5, 5]
        },
        annotations: {
            draggable: 'x'
        },
        title: {
            text: ''
        },
        tooltip: {
            pointFormat: '<span><b>{point.title}</b></span><br/><span>Дата: {point.start:%e. %b}</span><br/>' +
                '<span>Исполнитель: {point.user}</span>'
        },
        scrollbar: {
            enabled: true,
            showFull: false,
            barBackgroundColor: '#3c8dbc',
            buttonBackgroundColor: '#3c8dbc'
        },
        rangeSelector: {
            enabled: true,
            buttons: [{
                type: 'month',
                count: 1,
                text: '1мес'
            }, {
                type: 'month',
                count: 3,
                text: '3мес'
            }, {
                type: 'month',
                count: 6,
                text: '6мес'
            }, {
                type: 'year',
                count: 1,
                text: '1г'
            }],
            buttonTheme: { // styles for the buttons
                fill: 'none',
                stroke: 'none',
                'stroke-width': 0,
                r: 8,
                style: {
                    color: '#039',
                    fontWeight: 'bold'
                },
                states: {
                    hover: {},
                    select: {
                        fill: '#039',
                        style: {
                            color: 'red'
                        }
                    }
                }
            },
            selected: 1
        },

        plotOptions: {
            series: {
                animation: false, // Do not animate dependency connectors
                dragDrop: {
                    draggableX: true,
                    draggableY: false,
                    dragMinY: 0,
                    dragMaxY: 2,
                    dragPrecisionX: day / 3 // Snap to eight hours
                },
                dataLabels: {
                    enabled: true,
                    format: '{point.name}',
                    style: {
                        cursor: 'default',
                        pointerEvents: 'none'
                    }
                },
                allowPointSelect: true,
                point: {
                    events: {
                        // select: updateRemoveButtonStatus,
                        // unselect: updateRemoveButtonStatus,
                        // remove: updateRemoveButtonStatus,
                        drag: function (data) {
                            //start = data.newPoint.start;
                            //console.warn(data);
                        },
                        click: function (data) {
                        },
                        drop: function (data) {
                            $.ajax({
                                url: "move",
                                type: "post",
                                data: {
                                    id: data.target.id,
                                    dateIdx: data.target.dateIdx,
                                    start: data.target.start,
                                    end: data.target.end
                                },
                                success: function (data) {
                                    console.log("ok");
                                },
                                error: function (error) {
                                    console.log(error);
                                }
                            });
                        }
                    }
                }
            }
        },

        yAxis: {
            type: 'category',
            grid: {
                enabled: true,
                borderColor: '#3c8dbc',
                borderWidth: 1,
                columns: [{
                    title: {
                        text: 'Элементы'
                    },
                    categories: map(series, function (s) {
                        return s.title;
                    })
                }, {
                    title: {
                        text: 'Адрес'
                    },
                    categories: map(series, function (s) {
                        return s.address;
                    })
                }]
            },
            min: 0,
            max: <?php echo $max ?>
        },

        xAxis: [{ // first x-axis
            currentDateIndicator: true,
            gridLineColor: '#3c8dbc',
            min: today - 28 * day,
            max: today + 28 * day,
            softMin: today - 28 * day,
            softMax: today + 100 * day,
            labels: {
                format: '{value:%d}'
            },
            tickInterval: 1000 * 60 * 60 * 24
        }, { // second x-axis
            tickInterval: 1000 * 60 * 60 * 24 * 30
        }],

        exporting: {
            sourceWidth: 1000
        }
    });

    // btnShowDialog.onclick = function () {
    //     // Update dependency list
    //     var depInnerHTML = '<option value=""></option>';
    //     each(chart.series[0].points, function (point) {
    //         depInnerHTML += '<option value="' + point.id + '">' + point.name +
    //             ' </option>';
    //     });
    //
    //     // Show dialog by removing "hidden" class
    //     addTaskDialog.className = 'overlay';
    //     isAddingTask = true;
    //
    //     // Focus name field
    //     inputName.value = '';
    //     inputName.focus();
    // };

    // btnAddTask.onclick = function () {
    //     // Get values from dialog
    //     var series = chart.series[0],
    //         name = inputName.value,
    //         undef,
    //         y = parseInt(
    //             selectEquipment.options[selectEquipment.selectedIndex].value,
    //             10
    //         ),
    //         maxEnd = reduce(series.points, function (acc, point) {
    //             return point.y === y && point.end ? Math.max(acc, point.end) : acc;
    //         }, 0);
    //
    //     // Empty category
    //     if (maxEnd === 0) {
    //         maxEnd = today;
    //     }
    //
    //     // Add the point
    //     series.addPoint({
    //         start: maxEnd + (day),
    //         end: maxEnd + day,
    //         y: y,
    //         name: name
    //     });
    //
    //     // Hide dialog
    //     addTaskDialog.className += ' hidden';
    //     isAddingTask = false;
    // };

    // btnCancelAddTask.onclick = function () {
    //     // Hide dialog
    //     addTaskDialog.className += ' hidden';
    //     isAddingTask = false;
    // };
</script>
