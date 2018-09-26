<?php
/* @var $users \common\models\Users[] */
/* @var $user_property */

use yii\helpers\Html;
use yii\grid\GridView;
use yii\helpers\Url;

$this->title = Yii::t('app', 'Пользователи');
?>
<div class="orders-index box-padding-index">

    <h2 class="page-header">Пользователи</h2>
        <?php
        $count=0;
        foreach ($users as $user) {
            if ($count%4==0) {
                if ($count>0) print '</div>';
                print '<div class="row">';
            }
            $path='/images/unknown.png';
            print '<div class="col-md-4">
                        <div class="box box-widget widget-user-2">
                        <div class="widget-user-header bg-yellow">
                        <div class="widget-user-image">';
                echo Html::a('<img class="img-circle" src="'.Html::encode($path).'" style="width:65px; float: left">',
                ['/users/view', 'id' => Html::encode($user['_id'])]);
                print '</div>';
                print '<h3 class="widget-user-username" style="color: white; font-size: 24px">'.$user['name'].'</h3>';
                print '<h5 class="widget-user-desc" style="color: white; font-size: 14px">'.$user['contact'].'</h5>';
                print '</div>
                        <div class="box-footer no-padding">
                        <ul class="nav nav-stacked">
                            <li><a href="#">Фотографий
                            <span class="pull-right badge bg-blue">'.$user_property[$count]['photos'].'</span></a></li>
                            <li><a href="#">Аварий 
                            <span class="pull-right badge bg-red">'.$user_property[$count]['alarms'].'</span></a></li>
                            <li><a href="#">Сообщений 
                            <span class="pull-right badge bg-green">'.$user_property[$count]['messages'].'</span></a></li>
                        </ul>
                    </div>
                    </div>
                </div>';
            $count++;
        }
        ?>
    </div>
</div>
