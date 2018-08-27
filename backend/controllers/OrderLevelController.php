<?php
/**
 * PHP Version 7.0
 *
 * @category Category
 * @package  Backend\controllers
 * @author   Максим Шумаков <ms.profile.d@gmail.com>
 * @license  http://www.yiiframework.com/license/ License name
 * @link     http://www.toirus.ru
 */

namespace backend\controllers;

use Yii;
use yii\web\Controller;
use yii\web\NotFoundHttpException;
use yii\filters\VerbFilter;
use yii\web\UploadedFile;
use yii\web\UnauthorizedHttpException;

use common\models\OrderLevel;

use backend\models\OrderSearchLevel;

/**
 * OrderLevelController implements the CRUD actions for OrderLevel model.
 *
 * @category Category
 * @package  Backend\controllers
 * @author   Максим Шумаков <ms.profile.d@gmail.com>
 * @license  http://www.yiiframework.com/license/ License name
 * @link     http://www.toirus.ru
 */
class OrderLevelController extends Controller
{
    /**
     * Behaviors
     *
     * @inheritdoc
     *
     * @return array
     */
    public function behaviors()
    {
        return [
            'verbs' => [
                'class' => VerbFilter::className(),
                'actions' => [
                    'delete' => ['POST'],
                ],
            ],
        ];
    }

    /**
     * Init
     *
     * @return void
     * @throws UnauthorizedHttpException
     */
    public function init()
    {

        if (\Yii::$app->getUser()->isGuest) {
            throw new UnauthorizedHttpException();
        }

    }

    /**
     * Lists all OrderLevel models.
     *
     * @return mixed
     */
    public function actionIndex()
    {
        $searchModel = new OrderSearchLevel();
        $dataProvider = $searchModel->search(Yii::$app->request->queryParams);
        $dataProvider->pagination->pageSize = 15;

        return $this->render(
            'index',
            [
                'searchModel' => $searchModel,
                'dataProvider' => $dataProvider,
            ]
        );
    }

    /**
     * Displays a single OrderLevel model.
     *
     * @param integer $id Id
     *
     * @return mixed
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
     * Creates a new OrderLevel model.
     * If creation is successful, the browser will be redirected to the 'view' page.
     *
     * @return mixed
     */
    public function actionCreate()
    {
        $model = new OrderLevel();

        if ($model->load(Yii::$app->request->post())) {

            $file = UploadedFile::getInstance($model, 'icon');

            if ($file && $file->tempName) {

                $model->icon = $file;

                if ($model->upload()) {

                    $uuidFile = $model->uuid;
                    $dbName = \Yii::$app->session->get('user.dbname');
                    $iconFile = 'storage/' . $dbName . '/' . $uuidFile . '/';
                    $fileName = $model->uuid . '.' . $model->icon->extension;

                    if (!is_dir($iconFile)) {
                        mkdir($iconFile, 0755, true);
                    }

                    $dir = Yii::getAlias($iconFile);
                    $model->icon->saveAs($dir . $fileName);
                    $model->icon = $fileName;

                    if ($model->save(false)) {
                        return $this->redirect('index');
                    }
                } else {
                    return $model->icon;
                }
            }
        }

        if ($model->load(Yii::$app->request->post()) && $model->save()) {
            return $this->redirect(['view', 'id' => $model->_id]);
        } else {
            return $this->render(
                'create',
                [
                    'model' => $model,
                ]
            );
        }
    }

    /**
     * Updates an existing OrderLevel model.
     * If update is successful, the browser will be redirected to the 'view' page.
     *
     * @param integer $id Id
     *
     * @return mixed
     */
    public function actionUpdate($id)
    {
        $model = $this->findModel($id);
        $baseImage = $model->icon;

        if ($model->load(Yii::$app->request->post())) {

            $file = UploadedFile::getInstance($model, 'icon');

            if ($file && $file->tempName) {

                $model->icon = $file;

                if ($model->upload()) {

                    $uuidFile = $model->uuid;
                    $dbName = \Yii::$app->session->get('user.dbname');
                    $iconFile = 'storage/' . $dbName . '/' . $uuidFile . '/';
                    $fileName = $model->uuid . '.' . $model->icon->extension;

                    if (!is_dir($iconFile)) {
                        mkdir($iconFile, 0755, true);
                    }

                    $dir = Yii::getAlias($iconFile);
                    $model->icon->saveAs($dir . $fileName);
                    $model->icon = $fileName;

                    if ($model->save(false)) {
                        return $this->redirect('index');
                    }
                } else {
                    return $model->icon;
                }
            } else {
                $model->icon = $baseImage;
                if ($model->save(false)) {
                    return $this->redirect('index');
                }
            }
        }

        if ($model->load(Yii::$app->request->post()) && $model->save()) {
            return $this->redirect(['view', 'id' => $model->_id]);
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
     * Deletes an existing OrderLevel model.
     * If deletion is successful, the browser will be redirected to the 'index' page.
     *
     * @param integer $id Id
     *
     * @return mixed
     */
    public function actionDelete($id)
    {
        $this->findModel($id)->delete();

        return $this->redirect(['index']);
    }

    /**
     * Finds the OrderLevel model based on its primary key value.
     * If the model is not found, a 404 HTTP exception will be thrown.
     *
     * @param integer $id Id
     *
     * @return OrderLevel the loaded model
     * @throws NotFoundHttpException if the model cannot be found
     */
    protected function findModel($id)
    {
        if (($model = OrderLevel::findOne($id)) !== null) {
            return $model;
        } else {
            throw new NotFoundHttpException('The requested page does not exist.');
        }
    }
}
