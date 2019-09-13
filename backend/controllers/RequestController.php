<?php

namespace backend\controllers;

use backend\models\RequestSearch;
use common\components\MainFunctions;
use common\models\Equipment;
use common\models\Journal;
use common\models\Receipt;
use common\models\Request;
use common\models\Users;
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
    /**
     * Lists all Request models.
     * @return mixed
     * @throws Exception
     * @throws InvalidConfigException
     */
    public function actionIndex()
    {
        ini_set('memory_limit', '-1');
        //OrderFunctions::checkRequests();
        if (isset($_POST['editableAttribute'])) {
            $model = Request::find()
                ->where(['_id' => $_POST['editableKey']])
                ->one();
            if ($_POST['editableAttribute'] == 'closeDate') {
                $model['closeDate'] = date("Y-m-d H:i:s", $_POST['Request'][$_POST['editableIndex']]['closeDate']);
                MainFunctions::register('request', 'Изменена дата закрытия заявки',
                    'Комментарий: изменена дата закрытия заявки №' . $model['_id'] . ' на ' . $model['closeDate'], $model['uuid']);
            }
            if ($_POST['editableAttribute'] == 'requestStatusUuid') {
                $model['requestStatusUuid'] = $_POST['Request'][$_POST['editableIndex']]['requestStatusUuid'];
                MainFunctions::register('request', 'Изменен статус заявки',
                    'Комментарий: изменен статус заявки №' . $model['_id'] . ' на ' . $model['requestStatus']['title'], $model['uuid']);
            }
            if ($_POST['editableAttribute'] == 'comment') {
                $model['comment'] = $_POST['Request'][$_POST['editableIndex']]['comment'];
                MainFunctions::register('request', 'Изменен комментарий заявки',
                    'Комментарий: изменен комментарий заявки №' . $model['_id'] . ' на ' . $model['comment'], $model['uuid']);
            }
            if ($_POST['editableAttribute'] == 'verdict') {
                $model['verdict'] = $_POST['Request'][$_POST['editableIndex']]['verdict'];
                MainFunctions::register('request', 'Изменен вердикт заявки',
                    'Комментарий: изменен вердикт заявки №' . $model['_id'] . ' на ' . $model['verdict'], $model['uuid']);
            }
            if ($_POST['editableAttribute'] == 'result') {
                $model['result'] = $_POST['Request'][$_POST['editableIndex']]['result'];
                MainFunctions::register('request', 'Изменен результат',
                    'Комментарий: изменен результат контроля заявки №' . $model['_id'] . ' на ' . $model['result'], $model['uuid']);
            }

            if ($model->save())
                return json_encode('success');
            return json_encode('failed');
        }
        $searchModel = new RequestSearch();
        $dataProvider = $searchModel->search(Yii::$app->request->queryParams);
        $dataProvider->pagination->pageSize = 50;
        if (isset($_GET['start_time'])) {
            $dataProvider->query->andWhere(['>=', 'createdAt', $_GET['start_time']]);
            $dataProvider->query->andWhere(['<', 'createdAt', $_GET['end_time']]);
        }
        $dataProvider->setSort(['defaultOrder' => ['_id' => SORT_DESC]]);
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
     * Action info.
     *
     * @param integer $id Id
     *
     * @return string
     * @throws NotFoundHttpException
     */
    public function actionInfo($id)
    {
        return $this->render(
            'info',
            [
                'model' => $this->findModel($id),
            ]
        );
    }

    /**
     * Action search.
     *
     * @return string
     */
    public function actionSearch()
    {
        return $this->render('search', []);
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
        parent::actionCreate();

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
        $source = 'table';
        if (isset($_GET["uuid"])) {
            $model = Request::find()->where(['uuid' => $_GET["uuid"]])->one();
        } else {
            $model = new Request();
            if (isset($_GET["objectUuid"]))
                $model->objectUuid = $_GET["objectUuid"];
            if (isset($_GET["user"]))
                $model->contragentUuid = $_GET["user"];
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
            'source' => $source]);
    }

    /**
     * Creates a new Request model.
     * @return mixed
     * @throws InvalidConfigException
     * @throws Exception
     */
    public
    function actionNew()
    {
        if (isset($_POST['requestUuid']))
            $model = Request::find()->where(['uuid' => $_POST['requestUuid']])->one();
        else
            $model = new Request();
        if ($model->load(Yii::$app->request->post())) {
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
                    $model_contragent =  $model['contragent'];
                    if ($model_contragent && ($model_contragent['phone']!=$_POST['phoneNumber'])) {
                        $model_contragent["phone"] = $_POST['phoneNumber'];
                        $model_contragent->save();
                    }
                }

                MainFunctions::register('request', 'Создана заявка #' . $model['_id'],
                    'Комментарий: заявитель ' . $model['contragent']['title'], $model->uuid);

                if ($model['requestType']['taskTemplateUuid']) {
                    $user = $model['equipment']->getUser();
                    if ($user)
                        $task = MainFunctions::createTask($model['requestType']['taskTemplate'], $model->equipmentUuid,
                            $model->comment, $model->oid, $user['uuid'],null);
                    else
                        $task = MainFunctions::createTask($model['requestType']['taskTemplate'], $model->equipmentUuid,
                            $model->comment, $model->oid, null,null);
                    if ($task) {
                        MainFunctions::register('task', 'Создана задача',
                            '<a class="btn btn-default btn-xs">' . $model['requestType']['taskTemplate']['taskType']['title'] . '</a> ' .
                            $model['requestType']['taskTemplate']['title'] . '<br/>' .
                            '<a class="btn btn-default btn-xs">' . $model['equipment']['title'] . '</a> ' . $model['comment'],
                            $task->uuid);
                        $model['taskUuid'] = $task['uuid'];
                        $model->save();
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
        parent::actionUpdate($id);

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
     * @throws \Throwable
     * @throws StaleObjectException
     */
    public function actionDelete($id)
    {
        parent::actionDelete($id);

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
    protected
    function findModel($id)
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
}
