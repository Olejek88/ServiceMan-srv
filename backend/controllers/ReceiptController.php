<?php
namespace backend\controllers;

use backend\models\ReceiptSearch;
use common\models\Receipt;
use Throwable;
use Yii;
use yii\base\InvalidConfigException;
use yii\db\Exception;
use yii\db\StaleObjectException;
use yii\web\NotFoundHttpException;

/**
 * ReceiptController implements the CRUD actions for Receipt model.
 */
class ReceiptController extends ZhkhController
{
    protected $modelClass = Receipt::class;

    /**
     * Lists all Receipt models.
     *
     * @return mixed
     * @throws InvalidConfigException
     * @throws Exception
     */
    public function actionIndex()
    {
        if (isset($_POST['editableAttribute'])) {
            $model = Receipt::find()
                ->where(['_id' => $_POST['editableKey']])
                ->one();
            if ($_POST['editableAttribute'] == 'result') {
                $model['result'] = $_POST['Receipt'][$_POST['editableIndex']]['result'];
            }
            if ($_POST['editableAttribute'] == 'userCheck') {
                $model['userCheck'] = $_POST['Receipt'][$_POST['editableIndex']]['userCheck'];
            }
            if ($_POST['editableAttribute'] == 'closed') {
                $model['closed'] = $_POST['Receipt'][$_POST['editableIndex']]['closed'];
            }
            if ($_POST['editableAttribute'] == 'description') {
                $model['description'] = $_POST['Receipt'][$_POST['editableIndex']]['description'];
            }
            if ($_POST['editableAttribute'] == 'userUuid') {
                $model['userUuid'] = $_POST['Receipt'][$_POST['editableIndex']]['userUuid'];
            }
            if ($_POST['editableAttribute'] == 'date') {
                $model['date'] = $_POST['Receipt'][$_POST['editableIndex']]['date'];
            }
            if ($_POST['editableAttribute'] == 'requestUuid') {
                $model['requestUuid'] = $_POST['Receipt'][$_POST['editableIndex']]['requestUuid'];
            }
            if ($_POST['editableAttribute'] == 'userCheckWho') {
                $model['userCheckWho'] = $_POST['Receipt'][$_POST['editableIndex']]['userCheckWho'];
            }
            if ($model->save())
                return json_encode('success');
            return json_encode('failed');
        }

        $searchModel = new ReceiptSearch();
        $dataProvider = $searchModel->search(Yii::$app->request->queryParams);
        $dataProvider->pagination->pageSize = 50;
        return $this->render('index', [
            'searchModel' => $searchModel,
            'dataProvider' => $dataProvider,
        ]);
    }


    /**
     * Action list
     *
     * @return mixed
     * @throws Exception
     * @throws InvalidConfigException
     */
    public function actionList()
    {
        $listReceipt = Receipt::find()
            ->asArray()
            ->all();

        return $this->render(
            'list',
            [
                'model' => $listReceipt
            ]
        );
    }

    /**
     * Displays a single Receipt model.
     *
     * @param integer $id Id
     *
     * @return mixed
     * @throws NotFoundHttpException
     */
    public function actionView($id)
    {
        return $this->render(
            'view',
            [
                'model' => $this->findModel($id),
            ]
        );
    }

    /**
     * Creates a new Receipt model.
     * If creation is successful, the browser will be redirected to the 'view' page.
     *
     * @return mixed
     * @throws Exception
     * @throws InvalidConfigException
     */
    public function actionCreate()
    {
        $model = new Receipt();
        $searchModel = new ReceiptSearch();
        $dataProvider = $searchModel->search(Yii::$app->request->queryParams);
        $dataProvider->pagination->pageSize = 50;

        if ($model->load(Yii::$app->request->post())) {
            // проверяем все поля, если что-то не так показываем форму с ошибками
            if (!$model->validate()) {
                return $this->render('create', ['model' => $model, 'dataProvider' => $dataProvider]);
            }

            // сохраняем запись
            if ($model->save(false)) {
                return $this->redirect(['view', 'id' => $model->_id]);
            } else {
                return $this->render('create', ['model' => $model, 'dataProvider' => $dataProvider]);
            }
        } else {
            return $this->render('create', ['model' => $model, 'dataProvider' => $dataProvider]);
        }
    }

    /**
     * Updates an existing Receipt model.
     * If update is successful, the browser will be redirected to the 'view' page.
     *
     * @param integer $id Id
     *
     * @return mixed
     * @throws NotFoundHttpException
     */
    public function actionUpdate($id)
    {
        $model = $this->findModel($id);
        if ($model->load(Yii::$app->request->post())) {
            // сохраняем модель
            if ($model->save()) {
                return $this->redirect(['view', 'id' => $model->_id]);
            } else {
                return $this->render(
                    'update',
                    [
                        'model' => $model,
                    ]
                );
            }
        } else {
            return $this->render(
                'update',
                [
                    'model' => $model,
                ]
            );
        }
    }

    /**
     * Deletes an existing Receipt model.
     * If deletion is successful, the browser will be redirected to the 'index' page.
     *
     * @param integer $id Id
     *
     * @return mixed
     * @throws NotFoundHttpException
     * @throws Throwable
     * @throws StaleObjectException
     */
    public function actionDelete($id)
    {
        $this->findModel($id)->delete();

        return $this->redirect(['index']);
    }

    /**
     * Finds the Receipt model based on its primary key value.
     * If the model is not found, a 404 HTTP exception will be thrown.
     *
     * @param integer $id Id
     *
     * @return Receipt the loaded model
     * @throws NotFoundHttpException if the model cannot be found
     */
    protected function findModel($id)
    {
        if (($model = Receipt::findOne($id)) !== null) {
            return $model;
        } else {
            throw new NotFoundHttpException('The requested page does not exist.');
        }
    }

    public function actionForm()
    {
        $model = new Receipt();
        return $this->renderAjax('_add_receipt', [
            'model' => $model,
        ]);
    }

    /**
     * Creates a new Receipt model.
     * @return mixed
     * @throws InvalidConfigException
     * @throws Exception
     */
    public
    function actionNew()
    {
        if (isset($_POST['receiptUuid']))
            $model = Receipt::find()->where(['uuid' => $_POST['receiptUuid']])->one();
        else
            $model = new Receipt();
        if ($model->load(Yii::$app->request->post())) {
            if ($model->save(false)) {
                return true;
            }
        }
        return true;
    }
}
