<?php

namespace backend\controllers;

use backend\models\RequestSearch;
use common\components\MainFunctions;
use common\models\Equipment;
use common\models\Journal;
use common\models\Receipt;
use common\models\Request;
use common\models\Users;
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
     */
    public function actionIndex()
    {
        ini_set('memory_limit', '-1');
        //OrderFunctions::checkRequests();
        if (isset($_POST['editableAttribute'])) {
            $toLog = [
                'type' => 'request',
                'title' => '',
                'description' => '',
            ];
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

            // костыль для того чтобы можно было изменить обращения которые были созданы без контрагента и оборудования
            if ($model->contragentUuid == null && $model->equipmentUuid == null) {
                $model->scenario = Request::SCENARIO_API;
            }

            if ($model->save()) {
                MainFunctions::register($toLog['type'], $toLog['title'], $toLog['description'], $model['uuid']);
                return json_encode([]);
            } else {
                return json_encode(['message' => 'failed']);
            }
        }

        $searchModel = new RequestSearch();
        $dataProvider = $searchModel->search(Yii::$app->request->queryParams);
        $dataProvider->pagination->pageSize = 50;
        if (isset($_GET['start_time'])) {
            $dataProvider->query->andWhere(['>=', 'createdAt', $_GET['start_time']]);
            $dataProvider->query->andWhere(['<', 'createdAt', $_GET['end_time']]);
        }
        //$dataProvider->setSort(['defaultOrder' => ['_id' => SORT_DESC]]);

        if (isset($_GET['house'])) {
            $dataProvider->query->andWhere(['=', 'object.houseUuid', $_GET['house']]);
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
                    $accountUser = Yii::$app->user->identity;
                    $currentUser = Users::find()
                        ->where(['user_id' => $accountUser['id']])
                        ->asArray()
                        ->one();
                    if ($user)
                        $task = MainFunctions::createTask($model['requestType']['taskTemplate'], $model->equipmentUuid,
                            $model->comment, $model->oid, $user['uuid'], null, time(), $currentUser['uuid']);
                    else
                        $task = MainFunctions::createTask($model['requestType']['taskTemplate'], $model->equipmentUuid,
                            $model->comment, $model->oid, null, null, time(), $currentUser['uuid']);
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
}
