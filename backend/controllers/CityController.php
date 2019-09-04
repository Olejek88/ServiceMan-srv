<?php

namespace backend\controllers;

use backend\models\CitySearch;
use common\models\City;
use common\models\House;
use common\models\Objects;
use common\models\ObjectType;
use common\models\Street;
use Yii;
use yii\base\InvalidConfigException;
use yii\db\Exception;
use yii\db\StaleObjectException;
use yii\helpers\ArrayHelper;
use yii\web\NotFoundHttpException;

/**
 * CityController implements the CRUD actions for City model.
 */
class CityController extends ZhkhController
{
    /**
     * Lists all City models.
     * @return mixed
     */
    public function actionIndex()
    {
        $searchModel = new CitySearch();
        $dataProvider = $searchModel->search(Yii::$app->request->queryParams);
        $dataProvider->pagination->pageSize = 15;

        return $this->render('index', [
            'searchModel' => $searchModel,
            'dataProvider' => $dataProvider,
        ]);
    }

    /**
     * Displays a single City model.
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
     * Creates a new City model.
     * If creation is successful, the browser will be redirected to the 'view' page.
     * @return mixed
     */
    public function actionCreate()
    {
        parent::actionCreate();

        $model = new City();
        $searchModel = new CitySearch();
        $dataProvider = $searchModel->search(Yii::$app->request->queryParams);
        $dataProvider->pagination->pageSize = 50;

        if ($model->load(Yii::$app->request->post()) && $model->save()) {
            $searchModel = new CitySearch();
            $dataProvider = $searchModel->search(Yii::$app->request->queryParams);
            $dataProvider->pagination->pageSize = 15;
            return $this->render('index', [
                'searchModel' => $searchModel,
                'dataProvider' => $dataProvider,
            ]);
        } else {
            return $this->render('create', [
                'model' => $model, 'dataProvider' => $dataProvider
            ]);
        }
    }

    /**
     * Updates an existing City model.
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
     * Deletes an existing City model.
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

        return $this->redirect(['index']);
    }

    /**
     * Finds the City model based on its primary key value.
     * If the model is not found, a 404 HTTP exception will be thrown.
     * @param integer $id
     * @return City the loaded model
     * @throws NotFoundHttpException if the model cannot be found
     */
    protected function findModel($id)
    {
        if (($model = City::findOne($id)) !== null) {
            return $model;
        } else {
            throw new NotFoundHttpException('The requested page does not exist.');
        }
    }

    /**
     * @throws Exception
     * @throws InvalidConfigException
     */
    public function actionStreets()
    {
        if (isset($_POST['id'])) {
            $streets = Street::find()->where(['cityUuid' => $_POST['id']])->all();
            $items = ArrayHelper::map($streets, 'uuid', 'title');
            return json_encode($items);
        }
        return json_encode([]);
    }

    /**
     * @throws Exception
     * @throws InvalidConfigException
     */
    public function actionHouses()
    {
        if (isset($_POST['id'])) {
            $houses = House::find()->where(['streetUuid' => $_POST['id']])->all();
            $items = ArrayHelper::map($houses, 'uuid', 'number');
            return json_encode($items);
        }
        return json_encode([]);
    }

    /**
     * @throws Exception
     * @throws InvalidConfigException
     */
    public function actionObjects()
    {
        if (isset($_POST['id'])) {
            if (isset($_POST['type'])) {
                $objects = Objects::find()
                    ->where(['in', 'objectTypeUuid',
                        [ObjectType::OBJECT_TYPE_FLAT, ObjectType::OBJECT_TYPE_GENERAL, ObjectType::OBJECT_TYPE_COMMERCE]])
                    ->andWhere(['houseUuid' => $_POST['id']])
                    ->all();
            } else {
                $objects = Objects::find()->where(['houseUuid' => $_POST['id']])->all();
            }
            $items = ArrayHelper::map($objects, 'uuid', function ($data) {
                return $data['objectType']['title'] . ' ' . $data['title'];
            });
            return json_encode($items);
        }
        return json_encode([]);
    }

}
