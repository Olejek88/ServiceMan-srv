<?php

namespace backend\controllers;

use backend\models\DocumentationSearch;
use common\models\Documentation;
use common\models\EquipmentRegisterType;
use common\models\Users;
use Yii;
use yii\base\DynamicModel;
use yii\base\InvalidConfigException;
use yii\db\Exception;
use yii\db\StaleObjectException;
use yii\web\NotFoundHttpException;
use yii\web\Response;
use yii\web\UploadedFile;
use Throwable;

/**
 * DocumentationController implements the CRUD actions for Documentation model.
 */
class DocumentationController extends ZhkhController
{
    protected $modelClass = Documentation::class;

    /**
     * Lists all Documentation models.
     *
     * @return mixed
     * @throws InvalidConfigException
     * @throws Exception
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
                'title' => 'не привязано!!!'
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
        parent::actionCreate();

        $model = new Documentation();
        $model->entityType = 'm';
        $model->oid = Users::getCurrentOid();
        if ($model->load(Yii::$app->request->post())) {
            if ($model->entityType == 'e') {
                $model->equipmentTypeUuid = null;
            } else if ($model->entityType == 'm') {
                $model->equipmentUuid = null;
            }

            // получаем изображение для последующего сохранения
            $uFile = $model->uploadDocFile('docFile');

            if ($uFile !== false) {
                $filePath = $model->getDocFullPath();
                $targetDir = $model->getFileFullDir();
                if (!file_exists($targetDir)) {
                    mkdir($targetDir, 0777, true);
                }

                $uFile->saveAs($filePath);
            } else {
                if ($model->path == '') {
                    $model->addError('docFile', 'Укажите файл.');
                    return $this->render('create', ['model' => $model]);
                }
            }

            if ($model->save(false)) {
                return $this->redirect('index');
            }
        }

        return $this->render('create', ['model' => $model]);
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
        parent::actionUpdate($id);

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
     * @throws Throwable
     * @throws StaleObjectException
     */
    public function actionDelete($id)
    {
        parent::actionDelete($id);

        $model = $this->findModel($id);
        $this->findModel($id)->delete();
        if ($model) {
            EquipmentRegisterController::addEquipmentRegister($model['equipment']['uuid'],
                EquipmentRegisterType::REGISTER_TYPE_CHANGE_PROPERTIES,
                "Удалена документация " . $model['documentationType']['title'] . ' ' . $model['title']);
        }
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
        $dir = 'storage/doc/';
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
            if (isset($_POST["source"]))
                $source = $_POST["source"];
            else $source = 0;
            if (isset($_POST["folder"]) && $_POST["folder"] == 'true') {
                $model_uuid = $_POST["uuid"];
                $uuid = 0;
            }
            $documentation = new Documentation();

            return $this->renderAjax('../documentation/_add_form', [
                'documentation' => $documentation,
                'source' => $source,
                'equipmentUuid' => $uuid,
                'equipmentTypeUuid' => $model_uuid,
                'equipmentType' => null,
            ]);
        }
        return 0;
    }

    /**
     * @return bool|string|Response
     * @throws InvalidConfigException
     * @throws Exception
     */
    public function actionSave()
    {
        $model = new Documentation();
        $model->equipmentTypeUuid = null;
        $model->equipmentUuid = null;

        if ($model->load(Yii::$app->request->post())) {
            if ($model->equipmentTypeUuid == '')
                $model->equipmentTypeUuid = null;
            if ($model->equipmentUuid == '')
                $model->equipmentUuid = null;
            if (!$model->validate()) {
                return false;
            }

            // получаем изображение для последующего сохранения
            $file = $model->uploadDocFile('docFile');
            if ($file !== false) {
                $filePath = $model->getDocFullPath();
                $targetDir = $model->getFileFullDir();
                if (!file_exists($targetDir)) {
                    mkdir($targetDir, 0777, true);
                }

                $file->saveAs($filePath);
            } else {
                if ($model->path == '') {
                    $model->addError('docFile', 'Укажите файл.');
                    return $this->render('_add_form', [
                        'documentation' => $model,
                        'equipmentType' => $model->equipmentType,
                        'equipmentUuid' => $model->equipmentUuid,
                        'equipmentTypeUuid' => $model->equipmentTypeUuid,
                        'source' => Yii::$app->request->getBodyParam('source'),
                    ]);
                }
            }

            if ($model->save(false)) {
                EquipmentRegisterController::addEquipmentRegister($model['equipment']['uuid'],
                    EquipmentRegisterType::REGISTER_TYPE_CHANGE_PROPERTIES,
                    "Добавлена документация " . $model['documentationType']['title'] . ' ' . $model['title']);

                if (isset($_POST['source']))
                    return $this->redirect($_POST['source']);
                else
                    return $this->redirect(['files']);
            }
        }

        return $this->render('_add_form', [
            'documentation' => $model,
            'equipmentType' => $model->equipmentType,
            'equipmentUuid' => $model->equipmentUuid,
            'equipmentTypeUuid' => null,
            'source' => Yii::$app->request->getBodyParam('source'),
        ]);
    }
}
