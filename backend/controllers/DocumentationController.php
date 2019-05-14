<?php

namespace backend\controllers;

use backend\models\DocumentationSearch;
use common\models\Documentation;
use Yii;
use yii\base\DynamicModel;
use yii\db\StaleObjectException;
use yii\filters\VerbFilter;
use yii\web\Controller;
use yii\web\NotFoundHttpException;
use yii\web\UnauthorizedHttpException;
use yii\web\UploadedFile;

/**
 * DocumentationController implements the CRUD actions for Documentation model.
 */
class DocumentationController extends Controller
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
                'class' => VerbFilter::class,
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
     * Lists all Documentation models.
     *
     * @return mixed
     */
    public function actionIndex()
    {
        if (isset($_POST['editableAttribute'])) {
            $model = Documentation::find()
                ->where(['_id' => $_POST['editableKey']])
                ->one();
            if ($_POST['editableAttribute'] == 'title') {
                $model['title'] = $_POST['Documentation'][$_POST['editableIndex']]['title'];
            }
            $model->save();
            return json_encode('');
        }

        $searchModel = new DocumentationSearch();
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
     * Displays a single Documentation model.
     *
     * @param integer $id Id
     *
     * @return mixed
     * @throws NotFoundHttpException
     */
    public function actionView($id)
    {
        $model = $this->findModel($id);
        if ($model->equipmentUuid != null) {
            $entity = [
                'label' => 'Оборудование',
                'title' => $model->equipment['title']
            ];
        } else if ($model->equipmentTypeUuid != null) {
            $entity = [
                'label' => 'Модель',
                'title' => $model->equipmentType['title']
            ];
        } else {
            $entity = [
                'label' => '-------',
                'title' => 'не привязанно!!!'
            ];
        }

        return $this->render(
            'view',
            [
                'model' => $model,
                'entity' => $entity,
            ]
        );
    }

    /**
     * Creates a new Documentation model.
     * If creation is successful, the browser will be redirected to the 'view' page.
     *
     * @return mixed
     */
    public function actionCreate()
    {
        $model = new Documentation();
        $entityType = new DynamicModel(['entityType']);
        $entityType->addRule(['entityType'], 'string', ['max' => 45]);
        $entityType->entityType = 'e';

        if ($model->load(Yii::$app->request->post())) {
            $entityType->load(Yii::$app->request->post());
            // проверяем все поля, если что-то не так показываем форму с ошибками
            if (!$model->validate()) {
                return $this->render(
                    'create',
                    ['model' => $model, 'entityType' => $entityType]
                );
            }

            // получаем изображение для последующего сохранения
            $file = UploadedFile::getInstance($model, 'path');
            if ($file && $file->tempName) {
                $fileName = self::_saveFile($model, $file);
                if ($fileName) {
                    $model->path = $fileName;
                }
            }

            if ($entityType['entityType'] == 'e') {
                $model->equipmentTypeUuid = null;
            } else {
                $model->equipmentUuid = null;
            }

            // сохраняем запись
            if ($model->save(false)) {
                return $this->redirect(['view', 'id' => $model->_id]);
            } else {
                return $this->render(
                    'create',
                    ['model' => $model, 'entityType' => $entityType]
                );
            }
        } else {
            return $this->render(
                'create',
                ['model' => $model, 'entityType' => $entityType]
            );
        }
    }

    /**
     * Updates an existing Documentation model.
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
        $entityType = new DynamicModel(['entityType']);
        $entityType->addRule(['entityType'], 'string', ['max' => 45]);
        // значение по умолчанию
        $entityType['entityType'] = 'e';

        // сохраняем старое значение image
        $oldPath = $model->path;

        if ($model->equipmentTypeUuid != '') {
            $modelUuidOld = $model->equipmentTypeUuid;
            $entityType['entityType'] = 'm';
        } else if ($model->equipmentUuid != '') {
            $modelUuidOld = $model->equipment->equipmentTypeUuid;
            $entityType['entityType'] = 'e';
        } else {
            // ошибка, такого не должно быть что не указана модель или оборудование
            return $this->render(
                'update',
                [
                    'model' => $model,
                    'entityType' => $entityType
                ]
            );
        }

        if ($model->load(Yii::$app->request->post())) {
            $entityType->load(Yii::$app->request->post());
            // не проверяется момент когда установлены оба поля, в качестве
            // основного используем модель оборудования
            $t = $entityType['entityType'];
            if ($t == 'm' && $model->equipmentTypeUuid != '') {
                $modelUuidNew = $model->equipmentTypeUuid;
                $model->equipmentUuid = null;
            } else if ($t == 'e' && $model->equipmentUuid != '') {
                $modelUuidNew = $model->equipment->equipmentTypeUuid;
                $model->equipmentTypeUuid = null;
            } else {
                // такого не должно быть что не указана модель или оборудование
                return $this->render(
                    'update',
                    [
                        'model' => $model,
                        'entityType' => $entityType
                    ]
                );
            }

            // проверяем на изменение модели оборудования
            // если модель изменилась, переместить файл изображения в новый каталог
            $modelChanged = false;
            if ($modelUuidOld != $modelUuidNew) {
                $modelChanged = true;
            }

            $fileChanged = false;
            // получаем изображение для последующего сохранения
            $file = UploadedFile::getInstance($model, 'path');
            if ($file && $file->tempName) {
                $fileName = self::_saveFile($model, $file);
                if ($fileName) {
                    $model->path = $fileName;
                    $fileChanged = true;
                } else {
                    $model->path = $oldPath;
                    // уведомить пользователя, админа о невозможности сохранить файл
                }
            } else {
                $model->path = $oldPath;
            }

            if ($modelChanged) {
                if (!$fileChanged && $model->path != '') {
                    // переместить файл в новую папку
                    $newFilePath = $model->getDocDir() . $oldPath;
                    $oldFilePath = $model->getDocDirType($modelUuidOld) . $oldPath;
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
                        'entityType' => $entityType
                    ]
                );
            }
        } else {
            return $this->render(
                'update',
                [
                    'model' => $model,
                    'entityType' => $entityType
                ]
            );
        }
    }

    /**
     * Deletes an existing Documentation model.
     * If deletion is successful, the browser will be redirected to the 'index' page.
     *
     * @param integer $id Id
     *
     * @return mixed
     * @throws NotFoundHttpException
     * @throws \Throwable
     * @throws StaleObjectException
     */
    public function actionDelete($id)
    {
        $this->findModel($id)->delete();
        return $this->redirect(['index']);
    }

    /**
     * Finds the Documentation model based on its primary key value.
     * If the model is not found, a 404 HTTP exception will be thrown.
     *
     * @param integer $id Id
     *
     * @return Documentation the loaded model
     * @throws NotFoundHttpException if the model cannot be found
     */
    protected function findModel($id)
    {
        if (($model = Documentation::findOne($id)) !== null) {
            return $model;
        } else {
            throw new NotFoundHttpException('The requested page does not exist.');
        }
    }

    /**
     * Сохраняем файл согласно нашим правилам.
     *
     * @param Documentation $model Документация
     * @param UploadedFile $file Файл
     *
     * @return string | null
     */
    private static function _saveFile($model, $file)
    {
        $dir = $model->getDocDir();
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

    public function actionAdd()
    {
        if (isset($_POST["selected_node"])) {
            if (isset($_POST["uuid"]))
                $uuid = $_POST["uuid"];
            else $uuid = 0;
            if (isset($_POST["model_uuid"]))
                $model_uuid = $_POST["model_uuid"];
            else $model_uuid = 0;
            $documentation = new Documentation();
            return $this->renderAjax('../documentation/_add_form', [
                'documentation' => $documentation,
                'equipmentUuid' => $uuid,
                'equipmentModelUuid' => $model_uuid
            ]);
        }
        return 0;
    }

    public function actionSave()
    {
        $model = new Documentation();

        if ($model->load(Yii::$app->request->post())) {
            if (!$model->validate()) {
                return false;
            }

            // получаем изображение для последующего сохранения
            $file = UploadedFile::getInstance($model, 'path');
            if ($file && $file->tempName) {
                $fileName = self::_saveFile($model, $file);
                if ($fileName) {
                    $model->path = $fileName;
                }
            }

            if ($model->save(false)) {
                return $this->redirect(['/equipment/tree']);
            }
        }
        return $this->render('_add_form', ['model' => $model]);
    }
}
