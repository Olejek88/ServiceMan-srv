<?php
/* @var $messages \common\models\Message[] */
/* @var $income \common\models\Message[] */
/* @var $sent \common\models\Message[] */

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
                <?php echo Html::a('Новое','create', ['class' => 'btn btn-primary btn-block margin-bottom']) ?>
                <div class="box box-solid">
                    <div class="box-header with-border">
                        <h3 class="box-title">Папки</h3>
                        <div class="box-tools">
                            <button type="button" class="btn btn-box-tool" data-widget="collapse"><i class="fa fa-minus"></i>
                            </button>
                        </div>
                    </div>
                </div>
                <!-- /. box -->
                <div class="box box-solid">
                    <div class="box-header with-border">
                        <h3 class="box-title">Ярлыки</h3>
                        <div class="box-tools">
                            <button type="button" class="btn btn-box-tool" data-widget="collapse"><i class="fa fa-minus"></i>
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
                        <h3 class="box-title">Входящие</h3>

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
                        <div class="mailbox-controls">
                            <!-- Check all button -->
                            <button type="button" class="btn btn-default btn-sm checkbox-toggle"><i class="fa fa-square-o"></i>
                            </button>
                            <div class="btn-group">
                                <button type="button" class="btn btn-default btn-sm"><i class="fa fa-trash-o"></i></button>
                                <button type="button" class="btn btn-default btn-sm"><i class="fa fa-reply"></i></button>
                            </div>
                            <button type="button" class="btn btn-default btn-sm"><i class="fa fa-refresh"></i></button>
                            <!-- /.pull-right -->
                        </div>
                        <div class="table-responsive mailbox-messages">
                            <table class="table table-hover table-striped">
                                <tbody>
                                <?php
                                if (isset($_GET['type']) && $_GET['type']=='sent')
                                    $messages = $sent;
                                foreach ($messages as $message) {
                                    print '<tr><td><input type="checkbox"></td>';
                                    print '<td class="mailbox-star"><i class="fa fa-star text-yellow"></i></td>';
                                    print '<td class="mailbox-name">
                                    <a href="/users/view?id='.$message['user']->_id.'">'.$message['user']->name.'</a></td>';
                                    print '<td class="mailbox-subject">'.$message['message'].'</td>';
                                    print '<td class="mailbox-attachment">'.$message['flat']->number.'</td>';
                                    print '<td class="mailbox-date">'.$message['date'].'</td></tr>';
                                }
                                ?>
                                </tbody>
                            </table>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </section>
</div>
