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

use common\components\FancyTreeHelper;
use common\models\Defect;
use common\models\EquipmentModel;
use common\models\EquipmentRegister;
use common\models\EquipmentType;
use common\models\EquipmentTypeTree;
use common\models\Operation;
use Yii;
use yii\db\ActiveRecord;
use yii\helpers\Html;
use yii\web\Controller;
use yii\web\NotFoundHttpException;
use yii\filters\VerbFilter;
use yii\web\UploadedFile;
use yii\web\UnauthorizedHttpException;

use common\models\Equipment;

use backend\models\EquipmentSearch;

/**
 * EquipmentController implements the CRUD actions for Equipment model.
 *
 * @category Category
 * @package  Backend\controllers
 * @author   Максим Шумаков <ms.profile.d@gmail.com>
 * @license  http://www.yiiframework.com/license/ License name
 * @link     http://www.toirus.ru
 */
class EquipmentController extends Controller
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
     * Lists all Equipment models.
     *
     * @return mixed
     */
    public function actionIndex()
    {
        if (isset($_POST['editableAttribute'])) {
            $model = Equipment::find()
                ->where(['_id' => $_POST['editableKey']])
                ->one();
            if ($_POST['editableAttribute']=='title') {
                $model['title']=$_POST['Equipment'][$_POST['editableIndex']]['title'];
            }
            if ($_POST['editableAttribute']=='inventoryNumber') {
                $model['inventoryNumber']=$_POST['Equipment'][$_POST['editableIndex']]['inventoryNumber'];
            }
            if ($_POST['editableAttribute']=='equipmentModelUuid') {
                $model['equipmentModelUuid']=$_POST['Equipment'][$_POST['editableIndex']]['equipmentModelUuid'];
            }
            if ($_POST['editableAttribute']=='locationUuid') {
                $model['locationUuid']=$_POST['Equipment'][$_POST['editableIndex']]['locationUuid'];
            }
            if ($_POST['editableAttribute']=='equipmentStatusUuid') {
                $model['equipmentStatusUuid']=$_POST['Equipment'][$_POST['editableIndex']]['equipmentStatusUuid'];
            }
            if ($_POST['editableAttribute']=='startDate') {
                $model['startDate']=date("Y-m-d H:i:s",$_POST['Equipment'][$_POST['editableIndex']]['startDate']);
            }
            $model->save();
            return json_encode('');
        }

        $searchModel = new EquipmentSearch();
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
     * Displays a single Equipment model.
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
     * Creates a new Equipment model.
     * If creation is successful, the browser will be redirected to the 'view' page.
     *
     * @return mixed
     */
    public function actionCreate()
    {
        $model = new Equipment();

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
     * Updates an existing Equipment model.
     * If update is successful, the browser will be redirected to the 'view' page.
     *
     * @param integer $id Id
     *
     * @return mixed
     */
    public function actionUpdate($id)
    {
        // TODO: реализовать перенос файлов документации в новый каталог
        // если изменилась модель оборудования при редактировании оборудования!
        // так как файлы документации должны храниться в папке с uuid
        // модели оборудования

        $model = $this->findModel($id);
        // сохраняем старое значение image
        $oldImage = $model->image;

        if ($model->load(Yii::$app->request->post())) {
            // проверяем на изменение типа операции
            // если тип изменился, переместить файл изображения в новый каталог
            $oldTypeUuid = $model->getOldAttributes()['equipmentModelUuid'];
            $typeChanged = false;
            if ($model->equipmentModelUuid != $oldTypeUuid) {
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
     * Build tree of equipment
     *
     * @return mixed
     */
    public function actionTree()
    {
        $fullTree= array();
        $operations= array();

        $indexTable = array();
        $typesTree = EquipmentTypeTree::find()
            ->from([EquipmentTypeTree::tableName() . ' as ttt'])
            ->innerJoin(
                EquipmentType::tableName() . ' as tt',
                '`tt`.`_id` = `ttt`.`child`'
            )
            ->orderBy('title')
            ->all();

        FancyTreeHelper::indexClosure($typesTree, $indexTable);
        if (count($indexTable) == 0) {
            return $this->render('tree', ['defects' => [], 'equipment' => [],
                'registers' => [], 'operations' => [], $defectsCount=0]);
        }

        $types = EquipmentType::find()->indexBy('_id')->all();
        $tree = array();
        $startLevel = 1;
        foreach ($indexTable['levels']['backward'][$startLevel] as $node_id) {
            $expanded=false;
            if (isset($_GET['typeUuid']) && $_GET['typeUuid']==$types[$node_id]->uuid)
                $expanded=true;
            $tree[] = [
                'title' => $types[$node_id]->title,
                'key' => $node_id,
                'folder' => true,
                'expanded' => $expanded,
                'children' => FancyTreeHelper::closureToTree($node_id, $indexTable),
            ];
        }
        unset($indexTable);
        unset($types);
        $fullTree2 = self::addEquipmentToTree ($tree,
            EquipmentModel::class,
            Equipment::class,
            'equipmentModelUuid'
        );


        $equipmentTypes = EquipmentType::find()
            ->select('*')
            ->orderBy('title')
            ->all();
        $equipmentTypeCount = 0;
        $defectsCount =0 ;
        foreach ($equipmentTypes as $equipmentType) {
            $fullTree[$equipmentTypeCount]["title"] = $equipmentType['title'];

            $equipmentModels = EquipmentModel::find()
                ->select('*')
                ->where(['equipmentTypeUuid' => $equipmentType['uuid']])
                ->orderBy('title')
                ->all();
            $equipmentModelCount = 0;
            $sumModelDefects = 0;

            foreach ($equipmentModels as $equipmentModel) {
                $fullTree[$equipmentTypeCount]["children"][$equipmentModelCount]["title"] = Html::a($equipmentModel['title'],
                    ['equipment-model/view', 'id' => $equipmentModel['_id']]);
                $equipments = Equipment::find()
                    ->select('*')
                    ->where(['equipmentModelUuid' => $equipmentModel['uuid']])
                    ->orderBy('title')
                    ->all();

                $equipmentCount = 0;
                $sumDefects = 0;
                foreach ($equipments as $equipment) {
                    //$defects[$defectsCount] = Defect::find()
                    $defects = Defect::find()
                        ->select('*')
                        ->where(['equipmentUuid' => $equipment['uuid']])
                        ->all();

                    $sumDefects += count($defects);
                    $fullTree[$equipmentTypeCount]["children"][$equipmentModelCount]["children"][$equipmentCount]["defects"] = '
                    <a href="#" data-toggle="modal" rel="#modalDefects"" data-id="'.$defectsCount.'" id="'.$defectsCount.'" data-target="#modalDefects">'.count($defects).'</a>';
                    $fullTree[$equipmentTypeCount]["children"][$equipmentModelCount]["children"][$equipmentCount]["serial"] = $equipment["serialNumber"];
                    if (count($defects)==0)
                        $fullTree[$equipmentTypeCount]["children"][$equipmentModelCount]["children"][$equipmentCount]["coefficient"] = '<div class="progress"><div class="critical3">Высокий</div></div>';
                    if (count($defects)==1)
                        $fullTree[$equipmentTypeCount]["children"][$equipmentModelCount]["children"][$equipmentCount]["coefficient"] = '<div class="progress"><div class="critical2">Средний</div></div>';
                    if (count($defects)>1)
                        $fullTree[$equipmentTypeCount]["children"][$equipmentModelCount]["children"][$equipmentCount]["coefficient"] = '<div class="progress"><div class="critical1">Низкий</div></div>';

                    $fullTree[$equipmentTypeCount]["children"][$equipmentModelCount]["children"][$equipmentCount]["title"] = Html::a($equipment["title"],
                        ['equipment/view', 'id' => $equipment["_id"]]);
                    if ($equipment['criticalType']->_id==1)
                        $fullTree[$equipmentTypeCount]["children"][$equipmentModelCount]["children"][$equipmentCount]["critical"] = '<div class="progress"><div class="critical1">'.$equipment['criticalType']->title.'</div></div>';
                    if ($equipment['criticalType']->_id==2)
                        $fullTree[$equipmentTypeCount]["children"][$equipmentModelCount]["children"][$equipmentCount]["critical"] = '<div class="progress"><div class="critical2">'.$equipment['criticalType']->title.'</div></div>';
                    if ($equipment['criticalType']->_id>2)
                        $fullTree[$equipmentTypeCount]["children"][$equipmentModelCount]["children"][$equipmentCount]["critical"] = '<div class="progress"><div class="critical3">'.$equipment['criticalType']->title."</div></div>";
                    $fullTree[$equipmentTypeCount]["children"][$equipmentModelCount]["children"][$equipmentCount]["start"] = '
                    <a href="#" data-toggle="modal" rel="#modalRegister"" data-id="'.$defectsCount.'" id="'.$defectsCount.'" data-target="#modalRegister">'.date_format(date_create($equipment['startDate']), "Y-m-d H:i:s").'</a>';
                    $fullTree[$equipmentTypeCount]["children"][$equipmentModelCount]["children"][$equipmentCount]["tag"] = $equipment['tagId'];
                    if ($equipment['equipmentStatus']->title=='Требует ремонта' || $equipment['equipmentStatus']->title=='Неисправно')
                        $fullTree[$equipmentTypeCount]["children"][$equipmentModelCount]["children"][$equipmentCount]["status"] = '<div class="progress"><div class="critical1"><a href="#" data-toggle="modal" rel="#modalTasks" data-target="#modalTasks" style="color:white">'.$equipment['equipmentStatus']->title.'</a></div></div>';
                    elseif ($equipment['equipmentStatus']->title=='Не установлено' || $equipment['equipmentStatus']->title=='Требует проверки')
                        $fullTree[$equipmentTypeCount]["children"][$equipmentModelCount]["children"][$equipmentCount]["status"] = '<div class="progress"><div class="critical2"><a href="#" data-toggle="modal" rel="#modalTasks" data-target="#modalTasks" style="color:white">'.$equipment['equipmentStatus']->title.'</a></div></div>';
                    else
                        $fullTree[$equipmentTypeCount]["children"][$equipmentModelCount]["children"][$equipmentCount]["status"] = '<div class="progress"><div class="critical3"><a href="#" data-toggle="modal" rel="#modalTasks" data-target="#modalTasks" style="color:white">'.$equipment['equipmentStatus']->title.'</a></div></div>';
                    $fullTree[$equipmentTypeCount]["children"][$equipmentModelCount]["children"][$equipmentCount]["inventory"] = $equipment['inventoryNumber'];
                    $fullTree[$equipmentTypeCount]["children"][$equipmentModelCount]["children"][$equipmentCount]["location"] = $equipment['location']->title;
                    $equipmentCount++;
                    $defectsCount++;
                }
                $fullTree[$equipmentTypeCount]["children"][$equipmentModelCount]["defects"] = $sumDefects;
                if ($equipmentCount>0) $coefficient = 101-1-($sumDefects*3.14/$equipmentCount);
                else $coefficient = 101;
                if ($coefficient==100)
                    $fullTree[$equipmentTypeCount]["children"][$equipmentModelCount]["coefficient"] = '<div class="progress"><div class="critical3">'.number_format($coefficient,2).'%</div></div>';
                if ($coefficient>=95 && $coefficient<100)
                    $fullTree[$equipmentTypeCount]["children"][$equipmentModelCount]["coefficient"] = '<div class="progress"><div class="critical4">'.number_format($coefficient,2).'%</div></div>';
                if ($coefficient<95)
                    $fullTree[$equipmentTypeCount]["children"][$equipmentModelCount]["coefficient"] = '<div class="progress"><div class="critical1">'.number_format($coefficient,2).'%</div></div>';
                $sumModelDefects += $sumDefects;
                $equipmentModelCount++;
            }
            $fullTree[$equipmentTypeCount]["defects"] = $sumModelDefects;
            if ($equipmentModelCount>0) $coefficient = 101-1-($sumModelDefects*3.14/$equipmentModelCount);
            else $coefficient = 101;
            if ($coefficient==100)
                $fullTree[$equipmentTypeCount]["coefficient"] = '<div class="progress"><div class="critical3">'.number_format($coefficient,2).'%</div></div>';
            if ($coefficient>=95 && $coefficient<100)
                $fullTree[$equipmentTypeCount]["coefficient"] = '<div class="progress"><div class="critical4">'.number_format($coefficient,2).'%</div></div>';
            if ($coefficient<95)
                $fullTree[$equipmentTypeCount]["coefficient"] = '<div class="progress"><div class="critical1">'.number_format($coefficient,2).'%</div></div>';

            $equipmentTypeCount++;
        }
        $defectsCount--;
        //var_dump($fullTree);
        // TODO нужно выводить модельном окне только дефекты соответствующего оборудования
        $defects = Defect::find()
            ->select('*')
            ->all();
        // TODO нужно выводить модельном окне только операции соответствующего оборудования
        $all_operations = Operation::find()
            ->select('*')
            ->all();
        // TODO нужно выводить модельном окне только операции соответствующего оборудования
        $all_register = EquipmentRegister::find()
            ->select('*')
            ->all();
        $registers=array();
        $operationCnt=0;
        foreach ($all_operations as $operation) {
            $operations[$operationCnt]['verdict']=$operation['operationVerdict']->title;
            $operations[$operationCnt]['date']=$operation['startDate'];
            $operations[$operationCnt]['user']=json_encode($operation->getTaskStage()['task']['order']['user']);
            $operations[$operationCnt]['id']=$operation['_id'];
            $operations[$operationCnt]['title']=$operation['operationTemplate']->title;
            $operationCnt++;
        }
        $registerCnt=0;
        foreach ($all_register as $register) {
            $registers[$registerCnt]['type']=$register['registerType']->title;
            $registers[$registerCnt]['date']=$register['date'];
            $registers[$registerCnt]['user']=$register['user']->name;
            $registers[$registerCnt]['uuid']=$register['uuid'];
            $registerCnt++;
        }


        return $this->render('tree', [
            'equipment' => $fullTree2,
            'defectsCount' => $defectsCount,
            'defects' => $defects,
            'registers' => $registers,
            'operations' => $operations
        ]);
    }

    /**
     * Deletes an existing Equipment model.
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
     * Finds the Equipment model based on its primary key value.
     * If the model is not found, a 404 HTTP exception will be thrown.
     *
     * @param integer $id Id
     *
     * @return Equipment the loaded model
     * @throws NotFoundHttpException if the model cannot be found
     */
    protected function findModel($id)
    {
        if (($model = Equipment::findOne($id)) !== null) {
            return $model;
        } else {
            throw new NotFoundHttpException('The requested page does not exist.');
        }
    }

    /**
     * Сохраняем файл согласно нашим правилам.
     *
     * @param Equipment    $model Шаблон задачи
     * @param UploadedFile $file  Файл
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

    /**
     *
     * @param array $tree Массив в котором нужно изменить индексы
     * @param ActiveRecord|string $modelClass Класс модели
     * @param ActiveRecord|string $entityClass Класс сущности
     * @param string $linkField Поле через которое связывается
     *
     * @return mixed
     */
    public function addEquipmentToTree($tree, $modelClass, $entityClass, $linkField)
    {
        if (is_array($tree)) {
            $tree = array_slice($tree, 0);
            foreach ($tree AS $key => $value) {
                if (is_array($value)) {
                    $tree[$key] = self::addEquipmentToTree(
                        $value, $modelClass, $entityClass, $linkField
                    );
                }
            }
        }

        if (isset($tree['key'])) {
            $type = EquipmentType::findOne($tree['key']);
            $models = $modelClass::find()->where(['equipmentTypeUuid' => $type['uuid']])->all();
            foreach ($models as $model) {
                $expanded=false;
                if (isset($_GET['typeUuid']) && $_GET['typeUuid']==$type->uuid)
                    $expanded=true;
                $tree['children'][] = ['title' => $model['title'], 'key' => $model['_id']."",
                    'expanded' => $expanded, 'folder' => true];
                $childIdx = count($tree['children'])-1;
                $equipments = $entityClass::find()->where(['equipmentModelUuid' => $model['uuid']])->all();
                $defectsCount=0;
                foreach ($equipments as $equipment) {
                    //if (isset($_GET['uuid']) && $_GET['uuid']==$equipment['uuid'])
                    $defects = Defect::find()
                        ->select('*')
                        ->where(['equipmentUuid' => $equipment['uuid']])
                        ->all();

                    $coefficient='<div class="progress"><div class="critical3"></div></div>';
                    $critical='<div class="progress"><div class="critical3"></div></div>';
                    if (count($defects)==0)
                        $coefficient = '<div class="progress"><div class="critical3">Высокий</div></div>';
                    if (count($defects)==1)
                        $coefficient = '<div class="progress"><div class="critical2">Средний</div></div>';
                    if (count($defects)>1)
                        $coefficient= '<div class="progress"><div class="critical1">Низкий</div></div>';

                    if ($equipment['criticalType']->_id==1)
                        $critical = '<div class="progress"><div class="critical1">'.$equipment['criticalType']->title.'</div></div>';
                    if ($equipment['criticalType']->_id==2)
                        $critical = '<div class="progress"><div class="critical2">'.$equipment['criticalType']->title.'</div></div>';
                    if ($equipment['criticalType']->_id>2)
                        $critical = '<div class="progress"><div class="critical3">'.$equipment['criticalType']->title.'</div></div>';

                    if ($equipment['equipmentStatus']->title=='Требует ремонта' || $equipment['equipmentStatus']->title=='Неисправно')
                        $status = '<div class="progress"><div class="critical1"><a href="#" data-toggle="modal" rel="#modalTasks" data-target="#modalTasks" style="color:white">'.$equipment['equipmentStatus']->title.'</a></div></div>';
                    elseif ($equipment['equipmentStatus']->title=='Не установлено' || $equipment['equipmentStatus']->title=='Требует проверки')
                        $status = '<div class="progress"><div class="critical2"><a href="#" data-toggle="modal" rel="#modalTasks" data-target="#modalTasks" style="color:white">'.$equipment['equipmentStatus']->title.'</a></div></div>';
                    else
                        $status = '<div class="progress"><div class="critical3"><a href="#" data-toggle="modal" rel="#modalTasks" data-target="#modalTasks" style="color:white">'.$equipment['equipmentStatus']->title.'</a></div></div>';

                    $tree['children'][$childIdx]['children'][] =
                        ['key' => $equipment['_id']."",
                            'folder' => false,
                            'defects' => '<a href="#" data-toggle="modal" rel="#modalDefects" 
                            data-id="'.$defectsCount.'" id="'.$defectsCount.'" data-target="#modalDefects">'.count($defects).'</a>',
                            'serial' => $equipment["serialNumber"],
                            'title' => Html::a($equipment["title"], ['equipment/view', 'id' => $equipment["_id"]]),
                            'tag' => $equipment['tagId'],
                            'start' => '<a href="#" data-toggle="modal" rel="#modalRegister" id="'.$defectsCount.'" 
                            data-target="#modalRegister">'.date_format(date_create($equipment['startDate']), "Y-m-d H:i:s").'</a>',
                            'inventory' => $equipment['inventoryNumber'],
                            'location' => $equipment['location']->title,
                            'coefficient' => $coefficient,
                            'critical' => $critical,
                            'status' => $status];

                    $defectsCount++;
                }
            }
        }
        return ($tree);
    }

}
