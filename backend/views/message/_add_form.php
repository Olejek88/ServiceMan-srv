<?php
/* @var $message Message */
/* @var $toUser User */

use common\components\MainFunctions;
use common\models\Message;
use common\models\User;
use common\models\Users;
use kartik\select2\Select2;
use kartik\widgets\FileInput;
use yii\bootstrap\ActiveForm;
use yii\helpers\ArrayHelper;
use yii\helpers\Html;

?>

<?php $form = ActiveForm::begin([
    'enableAjaxValidation' => false,
    'action' => '../message/save',
    'options' => [
        'id' => 'form',
        'enctype' => 'multipart/form-data'
    ]]);
?>
<div class="modal-header">
    <button type="button" class="close" data-dismiss="modal">&times;</button>
    <h4 class="modal-title">Новое сообщение</h4>
</div>
<div class="modal-body">
    <?php
/*    if ($message['uuid']) {
        echo Html::hiddenInput("messageUuid", $message['uuid']);
        echo $form->field($message, 'uuid')->hiddenInput(['value' => $message['uuid']])->label(false);
    } else {*/
    echo $form->field($message, 'uuid')
        ->hiddenInput(['value' => MainFunctions::GUID()])
        ->label(false);
    if ($message['uuid']) {
//    if (isset($toUser) && $toUser) {
        echo '<span style="font-weight: bold">Кому: </span>&nbsp;'.Html::textInput('toUser', $message['toUser']['name'],['readonly' => true]);
        echo $form->field($message, 'toUserUuid')->hiddenInput(['value' => $message['fromUserUuid']])->label(false);
    } else {
        $user = Users::find()
            ->joinWith('user')
            ->andWhere(['user.status' => User::STATUS_ACTIVE])
            ->all();
        $items = ArrayHelper::map($user,'uuid','name');
        echo $form->field($message, 'toUserUuid')->widget(Select2::class,
            [
                'data' => $items,
                'language' => 'ru',
                'options' => [
                    'placeholder' => 'Получатель',
                    'style' => ['height' => '42px', 'padding-top' => '10px']
                ],
                'pluginOptions' => [
                    'allowClear' => true
                ],
            ]);
    }
    $accountUser = Yii::$app->user->identity;
    $currentUser = Users::findOne(['user_id' => $accountUser['id']]);

    //echo $form->field($message, 'fromUserUuid')->hiddenInput(['value' => $message['fromUserUuid']])->label(false);
    echo $form->field($message, 'fromUserUuid')->hiddenInput(['value' => $currentUser['uuid']])->label(false);
    echo $form->field($message, 'oid')->hiddenInput(['value' => Users::getCurrentOid()])->label(false);

   // echo $form->field($message, 'title')->textInput(['maxlength' => true]);
    if ($message['uuid']) {
        //echo '<i>'.Html::textarea('textLetter',$message['text']).'</i><br/>';
        echo $form->field($message, 'text')->textarea(['rows' => 8]);
    } else {
        echo $form->field($message, 'text')->textarea(['rows' => 8]);
    }
    echo $form->field($message, 'status')->hiddenInput(['value' => 0])->label(false);
    echo $form->field($message, 'date')->hiddenInput(['value' => date("Ymdhms")])->label(false);

    echo FileInput::widget([
            'id' => 'imageFiles',
        'name' => 'images[]',
        'options' => [
                'accept' => '*',
                'multiple' => true,
                'id' => 'imageFile'
        ]
    ]);
    ?>
</div>
<div class="modal-footer">
    <?php echo Html::submitButton(Yii::t('app', 'Отправить'), ['class' => 'btn btn-success']) ?>
    <button type="button" class="btn btn-default" data-dismiss="modal">Закрыть</button>
</div>
<?php ActiveForm::end(); ?>
