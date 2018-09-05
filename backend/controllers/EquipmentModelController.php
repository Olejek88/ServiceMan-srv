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

use backend\models\EquipmentSearchModel;
use common\components\TypeTreeHelper;
use common\models\EquipmentModel;
use common\models\EquipmentType;
use common\models\EquipmentTypeTree;
use Yii;
use yii\filters\VerbFilter;
use yii\web\Controller;
use yii\web\NotFoundHttpException;
use yii\web\UnauthorizedHttpException;
use yii\web\UploadedFile;

/**
 * EquipmentModelController implements the CRUD actions for EquipmentModel model.
 *
 * @category Category
 * @package  Backend\controllers
 * @author   Максим Шумаков <ms.profile.d@gmail.com>
 * @license  http://www.yiiframework.com/license/ License name
 * @link     http://www.toirus.ru
 */
class EquipmentModelController extends Controller
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
     * Lists all EquipmentModel models.
     *
     * @return mixed
     */
    public function actionIndex()
    {
        $searchModel = new EquipmentSearchModel();
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
     * Displays a single EquipmentModel model.
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
                'type' => $model->equipmentType,
            ]
        );
    }

    /**
     * Creates a new EquipmentModel model.
     * If creation is successful, the browser will be redirected to the 'view' page.
     *
     * @return mixed
     */
    public function actionCreate()
    {
        $model = new EquipmentModel();
        $searchModel = new EquipmentSearchModel();
        $dataProvider = $searchModel->search(Yii::$app->request->queryParams);
        $dataProvider->pagination->pageSize = 15;

        if ($model->load(Yii::$app->request->post())) {
            // проверяем все поля, если что-то не так показываем форму с ошибками
            if (!$model->validate()) {
                return $this->render('create', ['model' => $model, 'dataProvider' => $dataProvider]);
            }

            // получаем изображение для последующего сохранения
            $file = UploadedFile::getInstance($model, 'image');
            if ($file && $file->tempName) {
                $fileName = self::_saveFile($model, $file);
                if ($fileName) {
                    $model->image = $fileName;
                } else {
                    // уведомить пользователя, админа о невозможности сохранить файл
                }
            }

            // сохраняем запись
            if ($model->save(false)) {
                return $this->redirect(['view', 'id' => $model->_id]);
            } else {
                return $this->render('create', ['model' => $model,
                    'dataProvider' => $dataProvider]);
            }
        } else {
            return $this->render('create', [
                'model' => $model,
                'dataProvider' => $dataProvider
            ]);
        }
    }

    /**
     * Updates an existing EquipmentModel model.
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
        $oldImage = $model->image;

        if ($model->load(Yii::$app->request->post())) {
            // проверяем на изменение типа операции
            // если тип изменился, переместить файл изображения в новый каталог
            $oldTypeUuid = $model->getOldAttributes()['equipmentTypeUuid'];
            $typeChanged = false;
            if ($model->equipmentTypeUuid != $oldTypeUuid) {
                $typeChanged = true;
            }

            $fileChanged = false;
            // получаем изображение для последующего сохранения
            $file = UploadedFile::getInstance($model, 'image');
            if ($file && $file->tempName) {
                $fileName = self::_saveFile($model, $file);
                if ($fileName) {
                    $model->image = $fileName;
                    $fileChanged = true;
                } else {
                    $model->image = $oldImage;
                    // уведомить пользователя, админа о невозможности сохранить файл
                }
            } else {
                $model->image = $oldImage;
            }

            if ($typeChanged) {
                if (!$fileChanged && $model->image != '') {
                    // переместить файл в новую папку
                    $newFilePath = $model->getImageDir() . $oldImage;
                    $oldFilePath = $model->getImageDirType($oldTypeUuid) . $oldImage;
                    if (!is_dir(dirname($newFilePath))) {
                        if (!mkdir(dirname($newFilePath), 0755)) {
                            // уведомить пользователя,
                            // админа о невозможности создать каталог
                        }
                    }

                    if (rename($oldFilePath, $newFilePath)) {
                        // уведомить пользователя,
                        // админа о невозможности переместить файл
                    }
                }
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
     * Deletes an existing EquipmentModel model.
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
     * Finds the EquipmentModel model based on its primary key value.
     * If the model is not found, a 404 HTTP exception will be thrown.
     *
     * @param integer $id Id
     *
     * @return EquipmentModel the loaded model
     * @throws NotFoundHttpException if the model cannot be found
     */
    protected function findModel($id)
    {
        if (($model = EquipmentModel::findOne($id)) !== null) {
            return $model;
        } else {
            throw new NotFoundHttpException('The requested page does not exist.');
        }
    }

    /**
     * Lists all EquipmentModel as tree group by types.
     *
     * @return mixed
     */
    public function actionTree()
    {
        $indexTable = array();
        $typesTree = EquipmentTypeTree::find()
            ->from([EquipmentTypeTree::tableName() . ' as ttt'])
            ->innerJoin(
                EquipmentType::tableName() . ' as tt',
                '`tt`.`_id` = `ttt`.`child`'
            )
            ->all();

        TypeTreeHelper::indexClosure($typesTree, $indexTable);
        if (count($indexTable) == 0) {
            return $this->render('tree', ['templates' => []]);
        }

        $types = EquipmentType::find()->indexBy('_id')->all();
        $tree = array();
        $startLevel = 1;
        foreach ($indexTable['levels']['backward'][$startLevel] as $node_id) {
            $tree[] = [
                'text' => $types[$node_id]->title,
                'id' => $node_id,
                'nodes' => TypeTreeHelper::closureToTree($node_id, $indexTable),
            ];
        }

        unset($indexTable);
        unset($types);

        $resultTree = TypeTreeHelper::resetMulti(
            $tree,
            EquipmentType::class,
            EquipmentModel::class,
            'equipmentTypeUuid'
        );
        unset($tree);

        return $this->render(
            'tree',
            [
                'templates' => $resultTree
            ]
        );
    }

    /**
     * Сохраняем файл согласно нашим правилам.
     *
     * @param EquipmentModel $model Шаблон задачи
     * @param UploadedFile   $file  Файл
     *
     * @return string | null
     */
    private static function _saveFile($model, $file)
    {
        $dir = $model->getImageDir();
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
