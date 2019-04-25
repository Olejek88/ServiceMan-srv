<?php

namespace backend\controllers;

use backend\models\RequestSearch;
use common\components\MainFunctions;
use common\models\Request;
use common\models\RequestStatus;
use common\models\Users;
use Yii;
use yii\web\Controller;
use yii\filters\VerbFilter;
use yii\web\NotFoundHttpException;
use yii\web\UnauthorizedHttpException;

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

    public function init()
    {

        if (\Yii::$app->getUser()->isGuest) {
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
        $searchModel = new RequestSearch();
        $dataProvider = $searchModel->search(Yii::$app->request->queryParams);
        $dataProvider->pagination->pageSize = 50;

        return $this->render('table', [
            'searchModel' => $searchModel,
            'dataProvider' => $dataProvider,
        ]);
    }

    /**
     * Lists all Request models.
     * @return mixed
     */
    public function actionTable()
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
            if ($model->save())
                return json_encode('success');
            return json_encode('failed');
        }
        $searchModel = new RequestSearch();
        $dataProvider = $searchModel->search(Yii::$app->request->queryParams);
        $dataProvider->pagination->pageSize = 50;
        return $this->render('table', [
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
            MainFunctions::register('Создана заявка ' . $model->comment);
            return $this->redirect(['table', 'id' => $model->_id]);
        } else {
            echo json_encode($model->errors);
            return $this->render('create', [
                'model' => $model,
            ]);
        }
    }

    public function actionForm()
    {
        $model = new Request();
        if (isset($_GET["equipmentUuid"]))
            $model->equipmentUuid = $_GET["equipmentUuid"];
        if (isset($_GET["objectUuid"]))
            $model->objectUuid = $_GET["objectUuid"];
        if (isset($_GET["user"]))
            $model->userUuid = $_GET["user"];
        return $this->renderAjax('_add_request', [
            'model' => $model,
        ]);
    }

    /**
     * Creates a new Request model.
     * @var $model \common\models\Request
     * @return mixed
     */
    public function actionNew()
    {
        $model =  new Request();
        $request = \Yii::$app->getRequest();
        if ($request->isPost && $model->load($request->post())) {
            $old_request=0;
            if ($model->equipmentUuid) {
                $old_request = Request::find()
                    ->where(['requestStatusUuid' => RequestStatus::NEW_REQUEST])
                    ->andWhere(['equipmentUuid' => $model->equipmentUuid])
                    ->one();
                MainFunctions::log("request.log",json_encode($old_request));
            }
            if ($model->objectUuid) {
                $old_request = Request::find()
                    ->where(['requestStatusUuid' => RequestStatus::NEW_REQUEST])
                    ->andWhere(['objectUuid' => $model->objectUuid])
                    ->one();
                MainFunctions::log("request.log",json_encode($old_request));
            }
            if (!$old_request) {
                if (isset($_POST["Request"]["equipmentUuid"]))
                    $model->equipmentUuid = $_POST["Request"]["equipmentUuid"];
                if (isset($_POST["Request"]["objectUuid"]))
                    $model->objectUuid = $_POST["Request"]["objectUuid"];
                if (isset($_POST["Request"]["userUuid"]))
                    $model->userUuid = $_POST["Request"]["userUuid"];
                $model->comment = $_POST["Request"]["comment"];
                $model->requestStatusUuid = RequestStatus::NEW_REQUEST;
                $model->uuid = MainFunctions::GUID();
                $model->save();
                //ActiveForm::validate($model);
                if ($model->validate() && $model->equipmentUuid) {
/*                    $result = OrderFunctions::createOrder($model->equipmentUuid, $model['user'], StageType::STAGE_TYPE_VIEW);
                    $stage = $result['stage'];
                    if ($stage) {
                        $model->stageUuid = $stage['uuid'];
                        $model->requestStatusUuid = RequestStatus::IN_WORK;
                        $model->save();
                    }*/
                } else {
                    MainFunctions::log("request.log","error request creating");
                }
            } else
                MainFunctions::log("request.log","request already present");
            return true;
        }
        return false;
    }


    /**
     * Updates an existing Request model.
     * If update is successful, the browser will be redirected to the 'view' page.
     * @param integer $id
     * @return mixed
     */
    public function actionUpdate($id)
    {
        $model = $this->findModel($id);

        if ($model->load(Yii::$app->request->post()) && $model->save()) {
            return $this->redirect(['table', 'id' => $model->_id]);
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
     */
    public function actionDelete($id)
    {
        $model = $this->findModel($id);
        if ($model) {
            $accountUser = Yii::$app->user->identity;
            $currentUser = Users::findOne(['userId' => $accountUser['id']]);
            if ($currentUser) {
                // если заявку создал текущий пользователь или у него роль заказчика
                if ($model->userUuid == $currentUser['uuid']) {
                    $this->findModel($id)->delete();
                }
            }
        }
        return $this->redirect(['table']);
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
}
