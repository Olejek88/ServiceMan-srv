<?php
/* @var $model Message */
/* @var $userImage string */
/* @var $this yii\web\View */
/* @var $messages Message[] */
/* @var $income Message[] */
/* @var $deleted Message[] */

/* @var $sent Message[] */

use common\models\Message;
use yii\helpers\Html;

$this->title = Yii::t('app', 'Сообщения');
?>
<!-- Content Wrapper. Contains page content -->
<div class="content-wrapper">
    <!-- Content Header (Page header) -->
    <section class="content-header">
        <h1>
            Сообщения
            <small><?php count($messages) ?> сообщений в папке</small>
        </h1>
    </section>

    <!-- Main content -->
    <section class="content">
        <div class="row">
            <div class="col-md-3">
                <?php
                echo Html::a('Новое', 'new', ['class' => 'btn btn-primary btn-block margin-bottom',
                    'title' => 'Новое',
                    'data-toggle' => 'modal',
                    'data-target' => '#modalAddMessage',
                ]) ?>
                <div class="box box-solid">
                    <div class="box-header with-border">
                        <h3 class="box-title">Папки</h3>
                        <div class="box-tools">
                            <button type="button" class="btn btn-box-tool" data-widget="collapse">
                                <i class="fa fa-minus"></i>
                            </button>
                        </div>
                    </div>
                    <div class="box-body no-padding">
                        <ul class="nav nav-pills nav-stacked">
                            <li class="<?php if (!isset($_GET['type']) || $_GET['type'] == "income") echo 'active'; ?>">
                                <a href="/message/list"><i class="fa fa-inbox"></i> Входящие
                                    <span class="label label-primary pull-right"><?php echo count($income) ?></span></a>
                            </li>
                            <li class="<?php if (isset($_GET['type']) && $_GET['type'] == "sent") echo 'active'; ?>">
                                <a href="/message/list?type=sent"><i class="fa fa-envelope-o"></i> Отправленные
                                    <span class="label label-primary pull-right"><?php echo count($sent) ?></span></a>
                            </li>
                            <li class="<?php if (isset($_GET['type']) && $_GET['type'] == "deleted") echo 'active'; ?>">
                                <a href="/message/list?type=deleted"><i class="fa fa-trash-o"></i> Корзина
                                    <span class="label label-primary pull-right"><?php echo count($deleted) ?></span>
                                </a></li>
                        </ul>
                    </div>
                    <!-- /.box-body -->
                </div>
            </div>
            <!-- /.col -->
            <div class="col-md-9">
                <div class="box box-primary">
                    <div class="box-header with-border">
                        <div>
                            <?php
                            echo '<img src="' . $userImage . '" class="img-circle" alt="" style="width:25px">&nbsp;';
                            echo '<span style="font-weight: bold">От:</span>&nbsp;';
                            echo Html::textInput('fromUser',$model['fromUser']->name,['readonly' => true, 'class' => "border border-primary"]);
                            ?>
                            <br/>
                            <?php
                            echo '<img src="' . $userImage . '" class="img-circle" alt="" style="width:25px">&nbsp;';
                            echo '<span style="font-weight: bold">Кому:</span>&nbsp;';
                            echo Html::textInput('fromUser',$model['toUser']->name,['readonly' => true]);
                            ?>
                        </div>
                    </div>
                    <!-- /.box-header -->
                    <div class="box-body no-padding">
                        <form action="delete-one" method="post">
                            <div class="mailbox-controls">
                                <div class="btn-group">
                                    <button type="submit" class="btn btn-default btn-sm"><i class="fa fa-trash-o"></i>
                                    </button>
                                    <?php
                                    echo Html::a('<button type="button" class="btn btn-default btn-sm"><i class="fa fa-reply"></i>
                                        </button>', ['new', 'id' => $model['_id']],
                                        ['title' => 'Ответить',
                                        'data-toggle' => 'modal',
                                        'data-target' => '#modalAddMessage',
                                    ]) ?>
                                </div>
                            </div>
                            <div class="table-responsive mailbox-messages">
                                <table class="table table-hover table-striped">
                                    <tbody>
                                    <?php
                                    echo Html::hiddenInput('id', $model['_id']);
                                    print '<tr><td>'.$model['text'].'</td></tr>';
                                    ?>
                                    </tbody>
                                </table>
                            </div>
                        </form>
                    </div>
                </div>
            </div>
        </div>
    </section>
</div>
<div class="modal remote fade" id="modalAddMessage">
    <div class="modal-dialog">
        <div class="modal-content loader-lg" id="modalContentMessage">
        </div>
    </div>
</div>
