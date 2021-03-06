<?php
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
                            <button type="button" class="btn btn-box-tool" data-widget="collapse"><i
                                        class="fa fa-minus"></i>
                            </button>
                        </div>
                    </div>
                    <div class="box-body no-padding">
                        <ul class="nav nav-pills nav-stacked">
                            <li class="<?php if (!isset($_GET['type']) || $_GET['type']=="income") echo 'active'; ?>">
                                <a href="/message/list"><i class="fa fa-inbox"></i> Входящие
                                    <span class="label label-primary pull-right"><?php echo count($income) ?></span></a>
                            </li>
                            <li class="<?php if (isset($_GET['type']) && $_GET['type']=="sent") echo 'active'; ?>">
                                <a href="/message/list?type=sent"><i class="fa fa-envelope-o"></i> Отправленные
                                    <span class="label label-primary pull-right"><?php echo count($sent) ?></span></a>
                            </li>
                            <li class="<?php if (isset($_GET['type']) && $_GET['type']=="deleted") echo 'active'; ?>">
                                <a href="/message/list?type=deleted"><i class="fa fa-trash-o"></i> Корзина
                                    <span class="label label-primary pull-right"><?php echo count($deleted) ?></span>
                                </a></li>
                        </ul>
                    </div>
                    <!-- /.box-body -->
                </div>
                <!-- /. box -->
                <div class="box box-solid">
                    <div class="box-header with-border">
                        <h3 class="box-title">Ярлыки</h3>
                        <div class="box-tools">
                            <button type="button" class="btn btn-box-tool" data-widget="collapse"><i
                                        class="fa fa-minus"></i>
                            </button>
                        </div>
                    </div>
                    <div class="box-body no-padding">
                        <ul class="nav nav-pills nav-stacked">
                            <li><a href="#"><i class="fa fa-circle-o text-red"></i> Важные</a></li>
                            <li><a href="#"><i class="fa fa-circle-o text-yellow"></i> Пользователи</a></li>
                            <li><a href="#"><i class="fa fa-circle-o text-light-blue"></i> Системные</a></li>
                        </ul>
                    </div>
                </div>
            </div>
            <!-- /.col -->
            <div class="col-md-9">
                <div class="box box-primary">
                    <div class="box-header with-border">
                        <h3 class="box-title">
                            <?php if (isset($_GET['type'])) {
                                if ($_GET['type'] == 'sent')
                                    echo 'Отправленные';
                                if ($_GET['type'] == 'deleted')
                                    echo 'Удаленные';
                            } else {
                                echo 'Входящие';
                            }
                            ?>
                        </h3>

                        <div class="box-tools pull-right">
                            <div class="has-feedback">
                                <input type="text" class="form-control input-sm" placeholder="Поиск">
                                <span class="glyphicon glyphicon-search form-control-feedback"></span>
                            </div>
                        </div>
                        <!-- /.box-tools -->
                    </div>
                    <!-- /.box-header -->
                    <div class="box-body no-padding">
                        <form action="deletes" method="post">
                            <div class="mailbox-controls">
                                <div class="btn-group">
                                    <button type="submit" class="btn btn-default btn-sm"><i class="fa fa-trash-o"></i>
                                    </button>
                                    <button type="button" class="btn btn-default btn-sm"><i class="fa fa-reply"></i>
                                    </button>
                                </div>
                                <button type="button" class="btn btn-default btn-sm"><i class="fa fa-refresh"></i>
                                </button>
                                <!-- /.pull-right -->
                            </div>
                            <div class="table-responsive mailbox-messages">
                                <table class="table table-hover table-striped">
                                    <tbody>
                                    <?php
                                    foreach ($messages as $message) {
                                        if (strstr($message['text'], '<'))
                                            $title = strstr($message['text'], '<', true);
                                        else $title = $message['text'];
                                        $title = substr($title,0,50);
                                        $title = '<a href="/message/view?id=' . $message['_id'] . '">'.$title.'</a>';

                                        print '<tr><td><input type="checkbox" name="' . $message["_id"] . '"></td>';
                                        if ($message['status'] > 0)
                                            print '<td class="mailbox-star"><i class="fa fa-star text-yellow"></i></td>';
                                        else
                                            print '<td class="mailbox-star"><i class="fa fa-star-o text-yellow"></i></td>';
                                        if ($message['status']==Message::MESSAGE_NEW) {
                                            print '<td class="mailbox-name"><span style="font-weight: bold">
                                            <a href="/users/view?id=' . $message['toUser']->id . '">' . $message['toUser']->name . '</a></span></td>';
                                            print '<td class="mailbox-subject"><span style="font-weight: bold">' . $title . '</span></td>';
                                            print '<td class="mailbox-attachment"></td>';
                                            print '<td class="mailbox-date"><span style="font-weight: bold">' . $message['date'] . '</span></td></tr>';
                                        } else {
                                            print '<td class="mailbox-name">
                                            <a href="/users/view?id=' . $message['toUser']->id . '">' . $message['toUser']->name . '</a></td>';
                                            print '<td class="mailbox-subject">' . $title . '</td>';
                                            print '<td class="mailbox-attachment"></td>';
                                            print '<td class="mailbox-date">' . $message['date'] . '</td></tr>';
                                        }
                                    }
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
