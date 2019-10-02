<?php

namespace backend\controllers;

use backend\models\MessageSearch;
use common\components\MainFunctions;
use common\models\Message;
use common\models\Users;
use Exception;
use Throwable;
use Yii;
use yii\base\InvalidConfigException;
use yii\db\StaleObjectException;
use yii\helpers\Html;
use yii\web\BadRequestHttpException;
use yii\web\NotFoundHttpException;
use yii\web\Response;
use yii\web\UploadedFile;

/**
 * MessageController implements the CRUD actions for Message model.
 */
class MessageController extends ZhkhController
{
    protected $modelClass = Message::class;

    /**
     * Lists all Message models.
     * @return mixed
     */
    public function actionIndex()
    {
        $searchModel = new MessageSearch();
        $dataProvider = $searchModel->search(Yii::$app->request->queryParams);
        $dataProvider->pagination->pageSize = 25;

        return $this->render('index', [
            'searchModel' => $searchModel,
            'dataProvider' => $dataProvider,
        ]);
    }

    /**
     * Displays a single Message model.
     * @param integer $id
     * @return mixed
     * @throws NotFoundHttpException
     * @throws InvalidConfigException
     * @throws \yii\db\Exception
     */
    public function actionView($id)
    {
        $model = $this->findModel($id);
        if ($model) {
            $userImage = $model['fromUser']->getPhotoUrl();
            if (!$userImage)
                $userImage = Yii::$app->request->baseUrl . '/images/unknown2.png';

            $accountUser = Yii::$app->user->identity;
            $currentUser = Users::find()
                ->where(['user_id' => $accountUser['id']])
                ->asArray()
                ->one();

            $messages = Message::find()->where('status != '.Message::MESSAGE_DELETED)
                ->andWhere(['OR', ['fromUserUuid' => $currentUser['uuid']], ['toUserUuid' => $currentUser['uuid']]])
                ->orderBy('date DESC')
                ->all();

            $income = Message::find()->where(['toUserUuid' => $currentUser['uuid']])
                ->andWhere('status != '.Message::MESSAGE_DELETED)
                ->orderBy('date DESC')
                ->all();

            $sent = Message::find()->where(['fromUserUuid' => $currentUser['uuid']])
                ->andWhere('status != '.Message::MESSAGE_DELETED)
                ->orderBy('date DESC')
                ->all();
            $deleted = Message::find()->where(['status' => Message::MESSAGE_DELETED])
                ->orderBy('date DESC')
                ->all();

            $model->status = Message::MESSAGE_READ;
            $model->save();

            if (isset($_GET["type"])) {
                if ($_GET["type"] == "income")
                    $messages = $income;
                if ($_GET["type"] == "sent")
                    $messages = $sent;
                if ($_GET["type"] == "deleted")
                    $messages = $deleted;
            }

            return $this->render('view', [
                'model' => $model,
                'userImage' => $userImage,
                'messages' => $messages,
                'income' => $income,
                'deleted' => $deleted,
                'sent' => $sent
            ]);
        }
    }

    /**
     * Displays a message_box
     * @return mixed
     * @throws InvalidConfigException
     * @throws \yii\db\Exception
     */
    public function actionList()
    {
        $accountUser = Yii::$app->user->identity;
        $currentUser = Users::find()
            ->where(['user_id' => $accountUser['id']])
            ->asArray()
            ->one();

        $messages = Message::find()->where('status != '.Message::MESSAGE_DELETED)
            ->andWhere(['OR', ['fromUserUuid' => $currentUser['uuid']], ['toUserUuid' => $currentUser['uuid']]])
            ->orderBy('date DESC')
            ->all();

        $income = Message::find()->where(['toUserUuid' => $currentUser['uuid']])
            ->andWhere('status != '.Message::MESSAGE_DELETED)
            ->orderBy('date DESC')
            ->all();

        $sent = Message::find()->where(['fromUserUuid' => $currentUser['uuid']])
            ->andWhere('status != '.Message::MESSAGE_DELETED)
            ->orderBy('date DESC')
            ->all();
        $deleted = Message::find()->where(['status' => Message::MESSAGE_DELETED])
            ->orderBy('date DESC')
            ->all();

        if (isset($_GET["type"])) {
            if ($_GET["type"] == "income")
                $messages = $income;
            if ($_GET["type"] == "sent")
                $messages = $sent;
            if ($_GET["type"] == "deleted")
                $messages = $deleted;
        }

        return $this->render('list', [
            'messages' => $messages,
            'income' => $income,
            'deleted' => $deleted,
            'sent' => $sent
        ]);
    }

    public function actionSearch()
    {
        /**
         * [Базовые определения]
         * @var [type]
         */
        $model = 'Test';

        return $this->render('search', [
            'model' => $model,
        ]);
    }

    /**
     * Creates a new Message model.
     * If creation is successful, the browser will be redirected to the 'view' page.
     * @return mixed
     */
    public function actionCreate()
    {
        $model = new Message();
        $searchModel = new MessageSearch();
        $dataProvider = $searchModel->search(Yii::$app->request->queryParams);
        $dataProvider->pagination->pageSize = 10;
        $dataProvider->setSort(['defaultOrder' => ['_id' => SORT_DESC]]);

        if ($model->load(Yii::$app->request->post()) && $model->save()) {
            return $this->redirect(['view', 'id' => $model->_id]);
        } else {
            return $this->render('create', [
                'model' => $model,
                'dataProvider' => $dataProvider
            ]);
        }
    }

    /**
     * @param $action
     * @return bool
     * @throws BadRequestHttpException
     */
    public function beforeAction($action)
    {
        $this->enableCsrfValidation = false;
        if ($action->id === 'index' || $action->id === 'create'
            || $action->id === 'update' || $action->id === 'delete') {
            $this->enableCsrfValidation = true;
        }
        return parent::beforeAction($action);
    }

    /**
     * Creates a new Message model in chat for all users
     * @return mixed
     */
    public function actionSend()
    {
        $this->enableCsrfValidation = false;
        $model = new Message();
        $model->uuid = MainFunctions::GUID();
        $accountUser = Yii::$app->user->identity;
        $currentUser = Users::findOne(['user_id' => $accountUser['id']]);
        $model->fromUserUuid = $currentUser['uuid'];
        $model->text = $_POST["message"];
        $model->toUserUuid = $model->fromUserUuid;
        $model->status = 0;
        $model->date = date("Ymd");
        $model->save();
        return $this->redirect(['/site/dashboard']);
    }

    /**
     * Updates an existing Message model.
     * If update is successful, the browser will be redirected to the 'view' page.
     * @param integer $id
     * @return mixed
     * @throws NotFoundHttpException
     */
    public function actionUpdate($id)
    {
        $model = $this->findModel($id);

        if ($model->load(Yii::$app->request->post()) && $model->save()) {
            return $this->redirect(['view', 'id' => $model->_id]);
        } else {
            return $this->render('update', [
                'model' => $model,
            ]);
        }
    }

    /**
     * Deletes an existing Message model.
     * If deletion is successful, the browser will be redirected to the 'index' page.
     * @param integer $id
     * @return mixed
     * @throws NotFoundHttpException
     * @throws Exception
     * @throws Throwable
     * @throws StaleObjectException
     */
    public function actionDelete($id)
    {
        $this->findModel($id)->delete();
        return $this->redirect(['index']);
    }

    /**
     * @return Response
     * @throws NotFoundHttpException
     * @throws StaleObjectException
     * @throws Throwable
     */
    public function actionDeleteOne()
    {
        if (isset($_POST['id']))
            $this->findModel($_POST['id'])->delete();
        return $this->redirect(['list']);
    }

    /**
     * Finds the Message model based on its primary key value.
     * If the model is not found, a 404 HTTP exception will be thrown.
     * @param integer $id
     * @return Message the loaded model
     * @throws NotFoundHttpException if the model cannot be found
     */
    protected function findModel($id)
    {
        if (($model = Message::findOne($id)) !== null) {
            return $model;
        } else {
            throw new NotFoundHttpException('The requested page does not exist.');
        }
    }

    /**
     * функция отрабатывает сигналы от дерева и выполняет добавление нового оборудования
     *
     * @return mixed
     * @throws InvalidConfigException
     * @throws \yii\db\Exception
     */
    public function actionNew()
    {
        if (!isset($_GET['id']))
            $message = new Message();
        else
            $message = Message::find()->where(['_id' => $_GET['id']])->one();
        return $this->renderAjax('_add_form', [
            'message' => $message
        ]);
    }

    /**
     * Creates a new Equipment model.
     * @return mixed
     */
    public function actionSave()
    {
        $model = new Message();
        if ($model->load(Yii::$app->request->post())) {
            $count = 0;
            if ($_FILES["images"]) {
                $files = UploadedFile::getInstancesByName('images');
                //echo json_encode($file);
                foreach ($files as $image) {
                    echo json_encode($image);
                    if ($image && $image->tempName) {
                        $path_parts = pathinfo($image->name);
                        $fileName = self::_saveFile($image, $model['uuid'] . '-' . $count . '.' . $path_parts['extension']);
                        if ($fileName) {
                            $model->text .= '<br/>';
                            $model->text .= 'Вложение ' . Html::a('<span class="fa fa-file"></span>',
                                    $fileName, ['title' => 'вложение']);
                        }
                        $count++;
                    }
                }
            }
        }
        $model->save();
        return $this->redirect('list');
    }

    /**
     */
    public function actionDeletes()
    {
        foreach ($_POST as $key => $value) {
            if ($value == "on") {
                if (($model = Message::findOne($key)) !== null) {
                    $model->status = Message::MESSAGE_DELETED;
                    $model->save();
                }
            }
        }
        return $this->redirect('list');
    }

    /**
     * Сохраняем файл согласно нашим правилам.
     *
     * @param $file string
     *
     * @param $save_filename
     * @return string | null
     */
    private static function _saveFile($file, $save_filename)
    {
        $dir = 'storage/files/';
        if (!is_dir($dir)) {
            if (!mkdir($dir, 0755, true)) {
                return null;
            }
        }

        $targetDir = Yii::getAlias($dir);
        if ($file->saveAs($targetDir . $save_filename)) {
            return $dir . $save_filename;
        } else {
            return null;
        }
    }

    function incoming_files()
    {
        $files = $_FILES;
        $files2 = [];
        foreach ($files as $input => $infoArr) {
            $filesByInput = [];
            foreach ($infoArr as $key => $valueArr) {
                if (is_array($valueArr)) { // file input "multiple"
                    foreach ($valueArr as $i => $value) {
                        $filesByInput[$i][$key] = $value;
                    }
                } else { // -> string, normal file input
                    $filesByInput[] = $infoArr;
                    break;
                }
            }
            $files2 = array_merge($files2, $filesByInput);
        }
        $files3 = [];
        foreach ($files2 as $file) { // let's filter empty & errors
            if (!$file['error']) $files3[] = $file;
        }
        return $files3;
    }
}
