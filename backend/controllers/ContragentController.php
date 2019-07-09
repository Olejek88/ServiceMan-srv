<?php

namespace backend\controllers;

use backend\models\ContragentSearch;
use common\components\MainFunctions;
use common\models\Contragent;
use common\models\ObjectContragent;
use common\models\Users;
use Yii;
use yii\base\InvalidConfigException;
use yii\db\StaleObjectException;
use yii\web\NotFoundHttpException;
/**
 * ContragentController implements the CRUD actions for Contragent model.
 */
class ContragentController extends ZhkhController
{
    /**
     * @return mixed
     */
    public function actionIndex()
    {
        return self::actionTable();
    }

    /**
     * @return mixed
     * @throws InvalidConfigException
     */
    public function actionTable()
    {
        if (isset($_POST['editableAttribute'])) {
            $model = Contragent::find()
                ->where(['_id' => $_POST['editableKey']])
                ->one();
            if ($_POST['editableAttribute'] == 'title') {
                $model['title'] = $_POST['Contragent'][$_POST['editableIndex']]['title'];
            }
            if ($_POST['editableAttribute'] == 'inn') {
                $model['inn'] = $_POST['Contragent'][$_POST['editableIndex']]['inn'];
            }
            if ($_POST['editableAttribute'] == 'contragentTypeUuid') {
                $model['contragentTypeUuid'] = $_POST['Contragent'][$_POST['editableIndex']]['contragentTypeUuid'];
            }
            if ($_POST['editableAttribute'] == 'phone') {
                $model['phone'] = $_POST['Contragent'][$_POST['editableIndex']]['phone'];
            }
            if ($_POST['editableAttribute'] == 'director') {
                $model['director'] = $_POST['Contragent'][$_POST['editableIndex']]['director'];
            }
            if ($_POST['editableAttribute'] == 'address') {
                $model['address'] = $_POST['Contragent'][$_POST['editableIndex']]['address'];
            }
            if ($_POST['editableAttribute'] == 'account') {
                $model['account'] = $_POST['Contragent'][$_POST['editableIndex']]['account'];
            }
            if ($_POST['editableAttribute'] == 'email') {
                $model['email'] = $_POST['Contragent'][$_POST['editableIndex']]['email'];
            }
            $model->save();
            return json_encode('');
        }

        $searchModel = new ContragentSearch();
        $dataProvider = $searchModel->search(Yii::$app->request->queryParams);
        $dataProvider->pagination->pageSize = 15;

        return $this->render('table', [
            'searchModel' => $searchModel,
            'dataProvider' => $dataProvider,
        ]);
    }

    /**
     * Displays a single Contragent model.
     * @param integer $id
     * @return mixed
     * @throws NotFoundHttpException
     */
    public function actionView($id)
    {
        return $this->render('view', [
            'model' => $this->findModel($id),
        ]);
    }

    /**
     * Creates a new Contragent model.
     * If creation is successful, the browser will be redirected to the 'view' page.
     * @return mixed
     */
    public function actionCreate()
    {
        parent::actionCreate();

        $model = new Contragent();
        $searchModel = new ContragentSearch();
        $dataProvider = $searchModel->search(Yii::$app->request->queryParams);
        $dataProvider->pagination->pageSize = 15;

        if ($model->load(Yii::$app->request->post()) && $model->save()) {
            $objectContragent = new ObjectContragent();
            $objectContragent->contragentUuid = $model['uuid'];
            $objectContragent->uuid = MainFunctions::GUID();
            $objectContragent->oid = Users::getCurrentOid();
            $objectContragent->objectUuid = $_POST['objectUuid'];
            $objectContragent->save();

            return $this->render('table', [
                'searchModel' => $searchModel,
                'dataProvider' => $dataProvider,
            ]);
        } else {
            return $this->render('create', [
                'model' => $model,
                'dataProvider' => $dataProvider
            ]);
        }
    }

    /**
     * Updates an existing Contragent model.
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
            return $this->redirect(['view', 'id' => $model->_id]);
        } else {
            return $this->render('update', [
                'model' => $model,
            ]);
        }
    }

    /**
     * Deletes an existing Contragent model.
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

        $this->findModel($id)->delete();

        return $this->redirect(['table']);
    }

    /**
     * Finds the Contragent model based on its primary key value.
     * If the model is not found, a 404 HTTP exception will be thrown.
     * @param integer $id
     * @return Contragent the loaded model
     * @throws NotFoundHttpException if the model cannot be found
     */
    protected function findModel($id)
    {
        if (($model = Contragent::findOne($id)) !== null) {
            return $model;
        } else {
            throw new NotFoundHttpException('The requested page does not exist.');
        }
    }

    /**
     * @return string
     * @throws InvalidConfigException
     */
    public function actionPhone()
    {
        if (isset($_POST['id']))
        if (($model = Contragent::find()->where(['uuid' => $_POST['id']])->one()) !== null) {
            return $model['phone'];
        } else return '';
    }

}
