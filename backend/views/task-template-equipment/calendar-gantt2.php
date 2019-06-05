<?php

$this->title = 'Календарь задач по обслуживанию';

/* @var $events */

//$this->registerJsFile('/js/vendor/lib/angular-gantt/angular.js');
//$this->registerJsFile('/js/vendor/lib/angular-gantt/angular.min.js');
$this->registerJsFile('/js/vendor/lib/angular-gantt/angular-gantt.js');
$this->registerJsFile('/js/vendor/lib/angular-gantt/angular-gantt-plugins.js');
$this->registerJsFile('/js/vendor/lib/angular-gantt/angular-gantt-plugins.js');
$this->registerCssFile('/css/custom/modules/angular-gantt/angular-gantt.css');
$this->registerCssFile('/css/custom/modules/angular-gantt/angular-gantt-plugins.css');
?>
<script src="https://ajax.googleapis.com/ajax/libs/angularjs/1.5.6/angular.js"></script>
<div gantt data=data></div>

<script type="text/javascript">
    var myApp = angular.module('myApp', ['gantt']);
    angular.module('myApp').controller('controllerName', function($scope) {
            $scope.data = <?php echo json_encode($events)?>
        });
</script>
