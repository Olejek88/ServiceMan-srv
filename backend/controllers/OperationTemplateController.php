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

use common\components\TypeTreeHelper;
use common\models\OperationTypeTree;
use Yii;
use yii\web\Controller;
use yii\web\NotFoundHttpException;
use yii\filters\VerbFilter;
use yii\web\UploadedFile;
use yii\web\UnauthorizedHttpException;

use common\models\OperationTemplate;
use common\models\OperationType;

use backend\models\OperationSearchTemplate;

/**
 * OperationTemplateController implements the CRUD actions
 * for OperationTemplate model.
 *
 * @category Category
 * @package  Backend\controllers
 * @author   Максим Шумаков <ms.profile.d@gmail.com>
 * @license  http://www.yiiframework.com/license/ License name
 * @link     http://www.toirus.ru
 */
class OperationTemplateController extends Controller
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
     * Lists all OperationTemplate models.
     *
     * @return mixed
     */
    public function actionIndex()
    {
        $searchModel = new OperationSearchTemplate();
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
     * Displays a single OperationTemplate model.
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
                'type' => $model->operationType,
            ]
        );
    }

    /**
     * Creates a new OperationTemplate model.
     * If creation is successful, the browser will be redirected to the 'view' page.
     *
     * @return mixed
     */
    public function actionCreate()
    {
        $model = new OperationTemplate();

        // таким макаром можно "поджечь" произвольную ошибку в форме
        //$model->addError('image', 'Не верное значение поля.');

        if ($model->load(Yii::$app->request->post())) {
            // проверяем все поля, если что-то не так показываем форму с ошибками
            if (!$model->validate()) {
                return $this->render('create', ['model' => $model]);
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
                return $this->render('create', ['model' => $model]);
            }
        } else {
            return $this->render('create', ['model' => $model]);
        }
    }

    /**
     * Updates an existing OperationTemplate model.
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
            $oldTypeUuid = $model->getOldAttributes()['operationTypeUuid'];
            $typeChanged = false;
            if ($model->operationTypeUuid != $oldTypeUuid) {
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
                    var_dump($model->image);
                    var_dump($newFilePath);
                    var_dump($oldFilePath);
                    //exit(0);
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
     * Deletes an existing OperationTemplate model.
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
     * Finds the OperationTemplate model based on its primary key value.
     * If the model is not found, a 404 HTTP exception will be thrown.
     *
     * @param integer $id Id
     *
     * @return OperationTemplate the loaded model
     * @throws NotFoundHttpException if the model cannot be found
     */
    protected function findModel($id)
    {
        if (($model = OperationTemplate::findOne($id)) !== null) {
            return $model;
        } else {
            throw new NotFoundHttpException('The requested page does not exist.');
        }
    }

    /**
     * Lists all Tool as tree group by types.
     *
     * @return mixed
     */
    public function actionTree()
    {
        $indexTable = array();
        $typesTree = OperationTypeTree::find()
            ->from([OperationTypeTree::tableName() . ' as ttt'])
            ->innerJoin(
                OperationType::tableName() . ' as tt',
                '`tt`.`_id` = `ttt`.`child`'
            )
            ->all();

        TypeTreeHelper::indexClosure($typesTree, $indexTable);
        if (count($indexTable) == 0) {
            return $this->render('tree', ['templates' => []]);
        }

        $types = OperationType::find()->indexBy('_id')->all();
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
            OperationType::class,
            OperationTemplate::class,
            'operationTypeUuid'
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
     * @param OperationTemplate $model Шаблон задачи
     * @param UploadedFile      $file  Файл
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
