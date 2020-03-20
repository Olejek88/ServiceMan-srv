<?php
/* @var $users Users[] */

/* @var $user_property */

use common\models\User;
use common\models\Users;
use yii\helpers\Html;

$this->title = Yii::t('app', 'Пользователи');
?>
<div class="orders-index box-padding-index">
    <a href="/users/create" class="btn btn-success">Добавить</a>
</div>
<div class="orders-index box-padding-index">

    <h2 class="page-header">Пользователи</h2>
    <?php
    $count = 0;
    foreach ($users as $user) {
        if ($count % 4 == 0) {
            if ($count > 0) print '</div>';
            print '<div class="row">';
        }
        $path = $user->getPhotoUrl();
        if (!$path || !$user['image']) {
            $path = '/images/unknown.png';
        }

        print '<div class="col-md-3">
                        <div class="box box-widget widget-user-2">
                        <div class="widget-user-header ' . ($user->user->status == User::STATUS_ACTIVE ? 'bg-yellow' : 'bg-red') . '">
                        <div class="widget-user-image">';
        echo Html::a('<img class="img-circle" src="' . Html::encode($path) . '" style="width:65px; float: left">',
            ['/users/view', 'id' => Html::encode($user['_id'])]);
        print '</div>';
        print '<h3 class="widget-user-username" style="color: white; font-size: 24px">' . $user['name'] . '</h3>';
        print '<h5 class="widget-user-desc" style="color: white; font-size: 14px">' . $user['contact'] . '</h5>';

        print '</div>
                        <div class="box-footer no-padding">
                        <ul class="nav nav-stacked">
                                <li style="height:100px">'.Html::a('Специализация ' . $user_property[$count]['systems'],
                ['/users/add-system', 'userUuid' => $user['uuid']],
                [ 'title' => 'Добавить специализацию', 'data-toggle' => 'modal',
                    'data-target' => '#modalAddSystem']
            ).'</li>
                            <li><a href="#">Домов привязано 
                            <span class="pull-right badge bg-green">' . $user_property[$count]['alarms'] . '</span></a></li>
                            <li><a href="#">Задач
                            <span class="pull-right badge bg-orange">' . $user_property[$count]['tasks'] . '</span></a>
                            </li>
                            <li><a href="#">Треков передвижения 
                            <span class="pull-right badge bg-yellow">' . $user_property[$count]['tracks'] . '</span></a></li>
                        </ul>
                    </div>
                    </div>
                </div>';
        $count++;
    }
    ?>
</div>

<?php
$this->registerJs('$("#modalAddSystem").on("hidden.bs.modal",
function () {
window.location.replace("dashboard");
})');
?>

<div class="modal remote fade" id="modalAddSystem">
    <div class="modal-dialog">
        <div class="modal-content loader-lg" id="modalContentSystem">
        </div>
    </div>
</div>
