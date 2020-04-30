<?php

namespace backend\controllers;

use backend\models\RequestSearch;
use common\components\MainFunctions;
use common\models\Comments;
use common\models\Equipment;
use common\models\Journal;
use common\models\Receipt;
use common\models\Request;
use common\models\RequestStatus;
use common\models\Settings;
use common\models\Users;
use common\models\WorkStatus;
use Throwable;
use Yii;
use yii\base\InvalidConfigException;
use yii\db\Exception;
use yii\db\StaleObjectException;
use yii\web\NotFoundHttpException;

/**
 * RequestController implements the CRUD actions for Request model.
 */
class RequestController extends ZhkhController
{
    protected $modelClass = Request::class;

    /**
     * Lists all Request models.
     * @return mixed
     * @throws Exception
     * @throws InvalidConfigException
     * @throws \yii\httpclient\Exception
     */
    public function actionIndex()
    {
        ini_set('memory_limit', '-1');
        //OrderFunctions::checkRequests();
        //self::checkAllRequests();
        if (isset($_POST['editableAttribute'])) {
            $toLog = [
                'type' => 'request',
                'title' => '',
                'description' => '',
            ];
            /** @var Request $model */
            $model = Request::find()
                ->where(['_id' => $_POST['editableKey']])
                ->one();
            if ($_POST['editableAttribute'] == 'closeDate') {
                $model['closeDate'] = date("Y-m-d H:i:s", $_POST['Request'][$_POST['editableIndex']]['closeDate']);
                $toLog['title'] = 'Изменена дата закрытия заявки';
                $toLog['description'] = 'Комментарий: изменена дата закрытия заявки №' . $model['_id'] . ' на ' . $model['closeDate'];
            }
            if ($_POST['editableAttribute'] == 'requestStatusUuid') {
                $model['requestStatusUuid'] = $_POST['Request'][$_POST['editableIndex']]['requestStatusUuid'];
                $toLog['title'] = 'Изменен статус заявки';
                $toLog['description'] = 'Комментарий: изменен статус заявки №' . $model['_id'] . ' на ' . $model['requestStatus']['title'];
                if ($model['requestStatusUuid'] == RequestStatus::COMPLETE) {
                    Request::closeAppeal($model);
                }
            }
            if ($_POST['editableAttribute'] == 'type') {
                $model['type'] = $_POST['Request'][$_POST['editableIndex']]['type'];
                if ($model['type'] == 0)
                    $type = "Бесплатная заявка";
                else $type = "Платная заявка";
                $toLog['title'] = 'Изменен тип заявки';
                $toLog['description'] = 'Комментарий: изменен тип заявки №' . $model['_id'] . ' на ' . $type;
            }
            if ($_POST['editableAttribute'] == 'comment') {
                $model['comment'] = $_POST['Request'][$_POST['editableIndex']]['comment'];
                $toLog['title'] = 'Изменен комментарий заявки';
                $toLog['description'] = 'Комментарий: изменен комментарий заявки №' . $model['_id'] . ' на ' . $model['comment'];
            }
            if ($_POST['editableAttribute'] == 'verdict') {
                $model['verdict'] = $_POST['Request'][$_POST['editableIndex']]['verdict'];
                $toLog['title'] = 'Изменен вердикт заявки';
                $toLog['description'] = 'Комментарий: изменен вердикт заявки №' . $model['_id'] . ' на ' . $model['verdict'];
            }
            if ($_POST['editableAttribute'] == 'result') {
                $model['result'] = $_POST['Request'][$_POST['editableIndex']]['result'];
                $toLog['title'] = 'Изменен результат';
                $toLog['description'] = 'Комментарий: изменен результат контроля заявки №' . $model['_id'] . ' на ' . $model['result'];
            }

            if ($_POST['editableAttribute'] == 'serialNumber') {
                $oldValue = $model['serialNumber'];
                $model['serialNumber'] = $_POST['Request'][$_POST['editableIndex']]['serialNumber'];
                $toLog['title'] = 'Изменен номер заявки';
                $toLog['description'] = 'Комментарий: изменен номер заявки с ' . $oldValue . ' на ' . $model['serialNumber'];
                $model->scenario = Request::SCENARIO_API;
            }

            // костыль для того чтобы можно было изменить обращения которые были созданы без контрагента и оборудования
            if ($model->contragentUuid == null && $model->equipmentUuid == null) {
                $model->scenario = Request::SCENARIO_API;
            }

            if ($model->save()) {
                MainFunctions::register($toLog['type'], $toLog['title'], $toLog['description'], $model['uuid']);
                return json_encode([]);
            } else {
                $message = '';
                foreach ($model->errors as $error) {
                    $message = $error[0] . '<br/>';
                }
                return json_encode(['message' => $message]);
            }
        }

        $searchModel = new RequestSearch();
        $dataProvider = $searchModel->search(Yii::$app->request->queryParams);
        $dataProvider->pagination->pageSize = 50;
        if (isset($_GET['start_time'])) {
            $dataProvider->query->andWhere(['>=', 'request.createdAt', date('Y-m-d 00:00:00', strtotime($_GET['start_time']))]);
            $dataProvider->query->andWhere(['<', 'request.createdAt', date('Y-m-d 00:00:00', strtotime($_GET['end_time']))]);
        }
        //$dataProvider->setSort(['defaultOrder' => ['_id' => SORT_DESC]]);

        if (isset($_GET['house'])) {
            $dataProvider->query->andWhere(['=', 'object.houseUuid', $_GET['house']]);
        }
        if (isset($_GET['object'])) {
            $dataProvider->query->andWhere(['=', 'object.uuid', $_GET['object']]);
        }
        if (Yii::$app->request->isAjax && isset($_POST['house']) && isset($_POST['object'])) {
            if ($_POST['object'] != '0')
                return $this->redirect('../request/index?object=' . $_POST['object']);
        }
        if (Yii::$app->request->isAjax && isset($_POST['house'])) {
            return $this->redirect('../request/index?house=' . $_POST['house']);
        }

        return $this->render('index', [
            'searchModel' => $searchModel,
            'dataProvider' => $dataProvider,
        ]);
    }

    /**
     * Displays a single Request model.
     *
     * @param integer $id Id
     *
     * @return mixed
     * @throws NotFoundHttpException
     */
    public function actionView($id)
    {
        $model = $this->findModel($id);
        return $this->render(
            'view',
            [
                'model' => $model,
            ]
        );
    }

    /**
     * Creates a new Request model.
     * If creation is successful, the browser will be redirected to the 'view' page.
     * @return mixed
     * @throws InvalidConfigException
     * @throws Exception
     */
    public function actionCreate()
    {
        $model = new Request();
        if ($model->load(Yii::$app->request->post()) && $model->save()) {
            if ($model->equipmentUuid)
                MainFunctions::register('request', 'Создана заявка по оборудованию ' . $model['equipment']['title'],
                    'Комментарий: ' . $model->comment, $model->uuid);
            if ($model->objectUuid)
                MainFunctions::register('request', 'Создана заявка по объекту ' . $model['equipment']['title'],
                    'Комментарий: ' . $model->comment, $model->uuid);
            return $this->redirect(['index', 'id' => $model->_id]);
        } else {
            return $this->render('create', [
                'model' => $model,
            ]);
        }
    }

    /**
     * @return string
     * @throws Exception
     * @throws InvalidConfigException
     */
    public function actionForm()
    {
        $receiptUuid = "";
        $phone = "";
        $source = 'table';
        if (isset($_GET["uuid"])) {
            $model = Request::find()->where(['uuid' => $_GET["uuid"]])->one();
        } else {
            $model = new Request();
            if (isset($_GET["objectUuid"]))
                $model->objectUuid = $_GET["objectUuid"];
            if (isset($_GET["user"]))
                $model->contragentUuid = $_GET["user"];
            if (isset($_GET["phone"]))
                $phone = $_GET["phone"];
            if (isset($_GET["receiptUuid"]))
                $receiptUuid = $_GET["receiptUuid"];
            if (isset($_GET["source"]))
                $source = $_GET["source"];
            if (isset($_GET["equipmentUuid"])) {
                $model->equipmentUuid = $_GET["equipmentUuid"];
                $equipment = Equipment::find()->where(['uuid' => $_GET["equipmentUuid"]])->one();
                $model->objectUuid = $equipment['object']['uuid'];
            }
        }
        return $this->renderAjax('_add_request', ['model' => $model, 'receiptUuid' => $receiptUuid,
            'source' => $source, 'phone' => $phone]);
    }

    /**
     * Creates a new Request model.
     * @return mixed
     * @throws InvalidConfigException
     * @throws Exception
     */
    public function actionNew()
    {
        if (isset($_POST['requestUuid']))
            $model = Request::find()->where(['uuid' => $_POST['requestUuid']])->one();
        else
            $model = new Request();
        if ($model->load(Yii::$app->request->post())) {
            if ($model["equipmentUuid"] === '') {
                $model["equipmentUuid"] = null;
            }

            if (!isset($model["objectUuid"]) && isset($model["equipmentUuid"])) {
                $model["objectUuid"] = $model["equipment"]["objectUuid"];
            }

            if ($model->save(false)) {
                if (isset($_POST['receiptUuid'])) {
                    $model_receipt = Receipt::find()->where(['uuid' => $_POST['receiptUuid']])->one();
                    if ($model_receipt) {
                        $model_receipt["requestUuid"] = $model['uuid'];
                        $model_receipt->save();
                    }
                }
                if (isset($_POST['phoneNumber'])) {
                    $model_contragent = $model['contragent'];
                    if ($model_contragent && ($model_contragent['phone'] != $_POST['phoneNumber'])) {
                        $model_contragent["phone"] = $_POST['phoneNumber'];
                        $model_contragent->save();
                    }
                }

                MainFunctions::register('request', 'Создана заявка #' . $model['serialNumber'],
                    'Комментарий: заявитель ' . $model['contragent']['title'], $model->uuid);

                if ($model['requestType']['taskTemplateUuid']) {
                    $userUuid = $model['equipment'] == null ? null : ($model['equipment']->getUser())['uuid'];
                    $accountUser = Yii::$app->user->identity;
                    $currentUser = Users::find()
                        ->where(['user_id' => $accountUser['id']])
                        ->asArray()
                        ->one();
                    // TODO: в форме нужно сделать условие - если выбран характер обращения с шаблоном задачи,
                    // обязательно нужно выбрать оборудование !!!!
                    $task = ['result' => null];
                    if ($model['equipment'] != null) {
                        $task = MainFunctions::createTask($model['requestType']['taskTemplate'], $model->equipmentUuid,
                            $model->comment, $model->oid, $userUuid, null, time(), $currentUser['uuid']);
                    }

                    if ($task['result']) {
                        MainFunctions::register('task', 'Создана задача',
                            '<a class="btn btn-default btn-xs">' . $model['requestType']['taskTemplate']['taskType']['title'] . '</a> ' .
                            $model['requestType']['taskTemplate']['title'] . '<br/>' .
                            '<a class="btn btn-default btn-xs">' . $model['equipment']['title'] . '</a> ' . $model['comment'],
                            $task['task']['uuid']);
                        $model['taskUuid'] = $task['task']['uuid'];
                        $model->save();
                    } else {
                        return "";
                    }
                }
                return true;
            }
        }
        return true;
    }


    /**
     * Updates an existing Request model.
     * If update is successful, the browser will be redirected to the 'view' page.
     * @param integer $id
     * @return mixed
     * @throws NotFoundHttpException
     */
    public function actionUpdate($id)
    {
        $model = $this->findModel($id);

        if ($model->load(Yii::$app->request->post()) && $model->save()) {
            return $this->redirect(['index', 'id' => $model->_id]);
        } else {
            return $this->render('update', [
                'model' => $model,
            ]);
        }
    }

    /**
     * Deletes an existing Request model.
     * If deletion is successful, the browser will be redirected to the 'index' page.
     * @param integer $id
     * @return mixed
     * @throws NotFoundHttpException
     * @throws Throwable
     * @throws StaleObjectException
     */
    public function actionDelete($id)
    {
        $model = $this->findModel($id);
        if ($model) {
            $model->delete();
            $accountUser = Yii::$app->user->identity;
            $currentUser = Users::findOne(['user_id' => $accountUser['id']]);
            if ($currentUser) {
                // если заявку создал текущий пользователь или у него роль заказчика
                if ($model->contragentUuid == $currentUser['uuid']) {
                    $this->findModel($id)->delete();
                }
            }
        }
        return $this->redirect(['index']);
    }

    /**
     * Finds the Request model based on its primary key value.
     * If the model is not found, a 404 HTTP exception will be thrown.
     * @param integer $id
     * @return Request the loaded model
     * @throws NotFoundHttpException if the model cannot be found
     */
    protected function findModel($id)
    {
        if (($model = Request::findOne($id)) !== null) {
            return $model;
        } else {
            throw new NotFoundHttpException('The requested page does not exist.');
        }
    }

    /**
     * @return string
     */
    public function actionHistory()
    {
        $registers = [];
        if (isset($_GET["uuid"])) {
            $registers = Journal::find()->where(['referenceUuid' => $_GET["uuid"]])->all();
        }
        return $this->renderAjax('_history', ['registers' => $registers]);
    }

    /**
     * @return string
     */
    public function actionSearchForm()
    {
        return $this->renderAjax('_search_filter');
    }

    /**
     * Lists all Comments models for Request.
     * @return mixed
     * @throws InvalidConfigException
     * @throws Exception
     */
    public function actionMessages()
    {
        $comments = [];
        if (isset($_GET['uuid'])) {
            $comments = Comments::find()
                ->where(['entityUuid' => $_GET['uuid']])
                ->all();
        }
        return $this->renderAjax('_message_list', [
            'comments' => $comments
        ]);
    }

    /**
     * Add new Comment model for Request
     * @return mixed
     */
    public function actionAddMessage()
    {
        if (isset($_GET['uuid']) && isset($_GET['requestId'])) {
            $comment = new Comments();
            return $this->renderAjax('../request/_add_comment', [
                'model' => $comment,
                'entityUuid' => $_GET['uuid'],
                'extParentId' => $_GET['requestId']
            ]);
        }
        return null;
    }

    /**
     * Creates a new Comment to Request.
     * @return mixed
     * @throws Exception
     * @throws InvalidConfigException
     * @throws \yii\httpclient\Exception
     */
    public function actionSaveComment()
    {
        /** @var Comments $model */
        $model = new Comments();
        if ($model->load(Yii::$app->request->post())) {
            /** @var Request $request */
            $request = Request::find()->where(['uuid' => $model->entityUuid])->one();
            if ($request) {
                $model->date = date('Y-m-d H:i:s');
                $model->integrationClass = $request->integrationClass;
                $model->extParentType = "null";
                $model->extParentId = $request->extId;
                if ($model->save(false)) {
                    /** @var Request $request */
                    if ($request && $request->extId && $request->integrationClass) {
                        $id = Request::sendComment($request, $model->text);
                        $model->extId = "" . $id;
                        $model->save();
                        return 0;
                    }
                    return -1;
                }
                return -2;
            }
            return -3;
        }
        return -4;
    }

    /**
     * @throws Exception
     * @throws InvalidConfigException
     * @throws \yii\httpclient\Exception
     */
    public function checkAllRequests()
    {
        $change_status = 0;
        $setting = Settings::find()->where(['uuid' => Settings::SETTING_REQUEST_STATUS_FROM_TASK])->one();
        if ($setting && $setting['parameter'] == "1")
            $change_status = 1;
        $requests = Request::find()->all();
        /** @var Request $request */
        foreach ($requests as $request) {
            // если закончили задачу
            if ($request->requestStatusUuid != RequestStatus::COMPLETE && $request->taskUuid) {
                if ($request->task->workStatusUuid == WorkStatus::COMPLETE && $change_status) {
                    Request::closeAppeal($request);
                    $request->requestStatusUuid = RequestStatus::COMPLETE;
                    $request->save();
                }
            }
        }
    }
}
