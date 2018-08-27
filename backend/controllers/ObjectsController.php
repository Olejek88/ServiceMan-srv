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
use yii\helpers\Html;
use yii\web\UnauthorizedHttpException;

use common\models\Objects;
use common\models\Equipment;
use common\models\ObjectType;

use backend\models\ObjectsSearch;

/**
 * ObjectsController implements the CRUD actions for Objects model.
 *
 * @category Category
 * @package  Backend\controllers
 * @author   Максим Шумаков <ms.profile.d@gmail.com>
 * @license  http://www.yiiframework.com/license/ License name
 * @link     http://www.toirus.ru
 */
class ObjectsController extends Controller
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
     * Lists all Objects models.
     *
     * @return mixed
     */
    public function actionIndex()
    {
        $searchModel = new ObjectsSearch();
        $dataProvider = $searchModel->search(Yii::$app->request->queryParams);
        $dataProvider->pagination->pageSize = 50;

        return $this->render(
            'index',
            [
                'searchModel' => $searchModel,
                'dataProvider' => $dataProvider,
            ]
        );
    }

    /**
     * Action list
     *
     * @return mixed
     * @throws UnauthorizedHttpException
     */
    public function actionList()
    {
        $listObjects = Objects::find()
            ->asArray()
            ->all();

        $listType = ObjectType::find()
            ->asArray()
            ->all();

        foreach ($listObjects as $i => $object) {
            foreach ($listType as $l => $objectType) {
                if ($listObjects[$i]['objectTypeUuid'] === $listType[$l]['uuid']) {
                    $listObjects[$i]['objectTypeUuid'] = $listType[$l]['title'];
                }
            }
        }

        return $this->render(
            'list',
            [
                'model' => $listObjects,
                'type' => $listType,
            ]
        );
    }

    /**
     * Displays a single Objects model.
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
     * Creates a new Objects model.
     * If creation is successful, the browser will be redirected to the 'view' page.
     *
     * @return mixed
     */
    public function actionCreate()
    {
        $model = new Objects();
        $searchModel = new ObjectsSearch();
        $dataProvider = $searchModel->search(Yii::$app->request->queryParams);
        $dataProvider->pagination->pageSize = 50;

        if ($model->load(Yii::$app->request->post())) {
            // проверяем все поля, если что-то не так показываем форму с ошибками
            if (!$model->validate()) {
                echo var_dump($model);
                echo var_dump($model->errors);
                return $this->render('create', ['model' => $model, 'dataProvider' => $dataProvider]);
            }

            // получаем изображение для последующего сохранения
            $file = UploadedFile::getInstance($model, 'photo');
            if ($file && $file->tempName) {
                $fileName = self::_saveFile($model, $file);
                if ($fileName) {
                    $model->photo = $fileName;
                } else {
                    // уведомить пользователя, админа о невозможности сохранить файл
                }
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
     * Вспомогательный метод для постройки дерева в три уровня.
     *
     * @param string $uuid Uuid
     *
     * @return array|Objects[]|\yii\db\ActiveRecord[]
     */
    function getDownObject($uuid)
    {
        $objects = Objects::find()
            ->select('*')
            ->where(['parentUuid' => $uuid])
            ->all();
        return $objects;
    }

    /**
     * Вспомогательный метод для постройки дерева в три уровня.
     *
     * @param Objects   $object    Объект
     * @param Equipment $equipment Оборудование
     *
     * @return mixed
     */
    function setObjectParameters($object, $equipment)
    {
        $object['title'] = Html::a(
            $equipment['title'],
            ['equipment/view', 'id' => $equipment['_id']]
        );
        $object['inventory'] = $equipment['inventoryNumber'];
        $object['serial'] = $equipment['serialNumber'];
        $object['date'] = $equipment['startDate'];
        $object['tag'] = $equipment['tagId'];
        $object['type'] = $equipment['equipmentModel']->title;
        $sTitle = $equipment['equipmentStatus']->title;

        if ($sTitle == 'Требует ремонта' || $sTitle == 'Неисправно') {
            $class = 'critical1';
        } elseif ($sTitle == 'Не установлено' || $sTitle == 'Требует проверки') {
            $class = 'critical2';
        } else {
            $class = 'critical3';
        }

        $object['status'] = '<div class="progress"><div class="'
            . $class . '">' . $sTitle . '</div></div>';

        return $object;
    }

    /**
     * Метод для постройки дерева в три уровня.
     *
     * @return string
     */
    public function actionTree()
    {

        $c = 'children';
        $fullTree = array();
        $objects = self::getDownObject('00000000-0000-0000-0000-000000000000');
        $oCnt0 = 0;
        foreach ($objects as $object) {
            $fullTree[$oCnt0]['title'] = Html::a(
                $object['title'],
                ['objects/view', 'id' => $object['_id']]
            );
            $fullTree[$oCnt0]['type'] = $object['objectType']->title;
            $fullTree[$oCnt0]['date'] = $object['createdAt'];

            $equipments = Equipment::find()
                ->select('*')
                ->where(['locationUuid' => $object['uuid']])
                ->all();
            $eCnt = 0;
            foreach ($equipments as $equipment) {
                $fullTree[$oCnt0][$c][$eCnt]['inventory']
                    = $equipment['inventoryNumber'];
                $fullTree[$oCnt0][$c][$eCnt]
                    = self::setObjectParameters(
                        $fullTree[$oCnt0][$c][$eCnt],
                        $equipment
                    );
                $eCnt++;
            }

            $objects1 = self::getDownObject($object['uuid']);
            $oCnt1 = 0;
            foreach ($objects1 as $object1) {
                $fullTree[$oCnt0][$c][$oCnt1]['title']
                    = Html::a(
                        $object1['title'],
                        ['object/view', 'id' => $object1['_id']]
                    );
                $fullTree[$oCnt0][$c][$oCnt1]['type']
                    = $object1['objectType']->title;
                $fullTree[$oCnt0]['children'][$oCnt1]['date']
                    = $object1['createdAt'];
                $equipments = Equipment::find()
                    ->select('*')
                    ->where(['locationUuid' => $object1['uuid']])
                    ->all();
                $eCnt = 0;
                foreach ($equipments as $equipment) {
                    $fullTree[$oCnt0][$c][$oCnt1][$c][$eCnt]['title']
                        = $equipment['title'];
                    $fullTree[$oCnt0][$c][$oCnt1][$c][$eCnt]
                        = self::setObjectParameters(
                            $fullTree[$oCnt0][$c][$oCnt1][$c][$eCnt],
                            $equipment
                        );
                    $eCnt++;
                }
                $objects2 = self::getDownObject($object1['uuid']);
                $oCnt2 = 0;
                foreach ($objects2 as $object2) {
                    $fullTree[$oCnt0][$c][$oCnt1][$c][$oCnt2]['title']
                        = $object2['title'];
                    $fullTree[$oCnt0][$c][$oCnt1][$c][$oCnt2]['type']
                        = $object2['objectType']->title;
                    $fullTree[$oCnt0][$c][$oCnt1][$c][$oCnt2]['date']
                        = $object2['createdAt'];
                    $equipments = Equipment::find()
                        ->select('*')
                        ->where(['locationUuid' => $object2['uuid']])
                        ->all();
                    $eCnt = 0;
                    foreach ($equipments as $equipment) {
                        $fullTree[$oCnt0][$c][$oCnt1][$c][$oCnt2][$c][$eCnt]['title']
                            = $equipment['title'];
                        $fullTree[$oCnt0][$c][$oCnt1][$c][$oCnt2][$c][$eCnt]
                            = self::setObjectParameters(
                                $fullTree[$oCnt0][$c][$oCnt1][$c][$oCnt2][$c][$eCnt],
                                $equipment
                            );
                        $oCnt1++;
                    }
                }
            }

            $oCnt0++;
        }

        return $this->render(
            'tree',
            [
                'objects' => $fullTree
            ]
        );
    }

    /**
     * Updates an existing Object model.
     * If update is successful, the browser will be redirected to the 'view' page.
     *
     * @param integer $id Id
     *
     * @return mixed
     */
    public function actionUpdate($id)
    {
        $model = $this->findModel($id);
        // сохраняем старое значение image
        $oldImage = $model->photo;

        if ($model->load(Yii::$app->request->post())) {
            // получаем изображение для последующего сохранения
            $file = UploadedFile::getInstance($model, 'photo');
            if ($file && $file->tempName) {
                $fileName = self::_saveFile($model, $file);
                if ($fileName) {
                    $model->photo = $fileName;
                } else {
                    $model->photo = $oldImage;
                    // уведомить пользователя, админа о невозможности сохранить файл
                }
            } else {
                $model->photo = $oldImage;
            }

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
     * Deletes an existing Objects model.
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
     * Finds the Objects model based on its primary key value.
     * If the model is not found, a 404 HTTP exception will be thrown.
     *
     * @param integer $id Id
     *
     * @return Objects the loaded model
     * @throws NotFoundHttpException if the model cannot be found
     */
    protected function findModel($id)
    {
        if (($model = Objects::findOne($id)) !== null) {
            return $model;
        } else {
            throw new NotFoundHttpException('The requested page does not exist.');
        }
    }

    /**
     * Сохраняем файл согласно нашим правилам.
     *
     * @param Objects      $model Объект
     * @param UploadedFile $file  Файл
     *
     * @return string | null
     */
    private static function _saveFile($model, $file)
    {
        $dir = $model->getPhotoDir();
        if (!is_dir($dir)) {
            if (!mkdir($dir, 0755, true)) {
                return null;
            }
        }

        $targetDir = Yii::getAlias($dir);
        $fileName = $model->uuid . '.' . $file->extension;
        if ($file->saveAs($targetDir . $fileName)) {
            return $fileName;
        } else {
            return null;
        }
    }
}
