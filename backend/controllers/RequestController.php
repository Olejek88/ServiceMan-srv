<?php

namespace backend\controllers;

use backend\models\RequestSearch;
use common\components\MainFunctions;
use common\models\Equipment;
use common\models\Receipt;
use common\models\Request;
use common\models\RequestStatus;
use common\models\Users;
use Yii;
use yii\db\StaleObjectException;
use yii\filters\VerbFilter;
use yii\web\Controller;
use yii\web\NotFoundHttpException;
use yii\web\UnauthorizedHttpException;
use yii\web\UploadedFile;

/**
 * RequestController implements the CRUD actions for Request model.
 */
class RequestController extends Controller
{
    /**
     * @inheritdoc
     */
    public function behaviors()
    {
        return [
            'verbs' => [
                'class' => VerbFilter::class,
                'actions' => [
                    'delete' => ['POST'],
                ],
            ],
        ];
    }

    /**
     * @throws UnauthorizedHttpException
     */
    public function init()
    {

        if (Yii::$app->getUser()->isGuest) {
            throw new UnauthorizedHttpException();
        }

    }

    /**
     * Lists all Request models.
     * @return mixed
     */
    public function actionIndex()
    {
        //OrderFunctions::checkRequests();
        if (isset($_POST['editableAttribute'])) {
            $model = Request::find()
                ->where(['_id' => $_POST['editableKey']])
                ->one();
            if ($_POST['editableAttribute'] == 'closeDate') {
                $model['closeDate'] = date("Y-m-d H:i:s", $_POST['Request'][$_POST['editableIndex']]['closeDate']);
            }
            if ($_POST['editableAttribute'] == 'requestStatusUuid') {
                $model['requestStatusUuid'] = $_POST['Request'][$_POST['editableIndex']]['requestStatusUuid'];
            }
            if ($_POST['editableAttribute'] == 'comment') {
                $model['comment'] = $_POST['Request'][$_POST['editableIndex']]['comment'];
            }
            if ($_POST['editableAttribute'] == 'verdict') {
                $model['verdict'] = $_POST['Request'][$_POST['editableIndex']]['verdict'];
            }
            if ($_POST['editableAttribute'] == 'result') {
                $model['result'] = $_POST['Request'][$_POST['editableIndex']]['result'];
            }

            if ($_POST['editableAttribute'] == 'orderVerdictUuid') {
                $model['orderVerdictUuid'] = $_POST['Orders'][$_POST['editableIndex']]['orderVerdictUuid'];
            }

            if ($model->save())
                return json_encode('success');
            return json_encode('failed');
        }
        $searchModel = new RequestSearch();
        $dataProvider = $searchModel->search(Yii::$app->request->queryParams);
        $dataProvider->pagination->pageSize = 50;
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
     */
    public function actionCreate()
    {
        $model = new Request();
        if ($model->load(Yii::$app->request->post()) && $model->save()) {
            if ($model->equipmentUuid)
                MainFunctions::register('request', 'Создана заявка по оборудованию ' . $model['equipment']['title'],
                    'Комментарий: ' . $model->comment);
            if ($model->objectUuid)
                MainFunctions::register('request', 'Создана заявка по объекту ' . $model['equipment']['title'],
                    'Комментарий: ' . $model->comment);
            return $this->redirect(['index', 'id' => $model->_id]);
        } else {
            return $this->render('create', [
                'model' => $model,
            ]);
        }
    }

    public function actionForm()
    {
        $receiptUuid = "";
        if (isset($_GET["uuid"])) {
            $model = Request::find()->where(['uuid' => $_GET["uuid"]])->one();
        } else {
            $model = new Request();
            if (isset($_GET["equipmentUuid"]))
                $model->equipmentUuid = $_GET["equipmentUuid"];
            if (isset($_GET["objectUuid"]))
                $model->objectUuid = $_GET["objectUuid"];
            if (isset($_GET["user"]))
                $model->userUuid = $_GET["user"];
            if (isset($_GET["receiptUuid"]))
                $receiptUuid = $_GET["receiptUuid"];
        }
        return $this->renderAjax('_add_request', ['model' => $model, 'receiptUuid' => $receiptUuid]);
    }

    /**
     * Creates a new Request model.
     * @return mixed
     * @var $model Request
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
    public
    function actionUpdate($id)
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
     * @throws \Throwable
     * @throws StaleObjectException
     */
    public
    function actionDelete($id)
    {
        $model = $this->findModel($id);
        if ($model) {
            $model->delete();
            $accountUser = Yii::$app->user->identity;
            $currentUser = Users::findOne(['user_id' => $accountUser['id']]);
            if ($currentUser) {
                // если заявку создал текущий пользователь или у него роль заказчика
                if ($model->userUuid == $currentUser['uuid']) {
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
}
