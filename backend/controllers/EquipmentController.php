<?php

namespace backend\controllers;

use backend\models\EquipmentSearch;
use common\components\Errors;
use common\components\MainFunctions;
use common\models\Documentation;
use common\models\Equipment;
use common\models\EquipmentRegister;
use common\models\EquipmentStatus;
use common\models\EquipmentType;
use common\models\House;
use common\models\Measure;
use common\models\Message;
use common\models\Objects;
use common\models\Photo;
use common\models\Street;
use common\models\Task;
use common\models\UserHouse;
use common\models\Users;
use common\models\UserSystem;
use common\models\WorkStatus;
use Exception as ExceptionAlias;
use Throwable;
use Yii;
use yii\db\StaleObjectException;
use yii\filters\VerbFilter;
use yii\helpers\ArrayHelper;
use yii\helpers\Html;
use yii\web\Controller;
use yii\web\NotFoundHttpException;
use yii\web\UnauthorizedHttpException;
use yii\web\UploadedFile;

/**
 * EquipmentController implements the CRUD actions for Equipment model.
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

        if (Yii::$app->getUser()->isGuest) {
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
            if ($_POST['editableAttribute'] == 'serial') {
                $model['serial'] = $_POST['Equipment'][$_POST['editableIndex']]['serial'];
            }
            if ($_POST['editableAttribute'] == 'tag') {
                $model['tag'] = $_POST['Equipment'][$_POST['editableIndex']]['tag'];
            }
            if ($_POST['editableAttribute'] == 'equipmentTypeUuid') {
                $model['equipmentTypeUuid'] = $_POST['Equipment'][$_POST['editableIndex']]['equipmentTypeUuid'];
            }
            if ($_POST['editableAttribute'] == 'equipmentStatusUuid') {
                $model['equipmentStatusUuid'] = $_POST['Equipment'][$_POST['editableIndex']]['equipmentStatusUuid'];
            }
            if ($_POST['editableAttribute'] == 'testDate') {
                $model['testDate'] = date("Y-m-d H:i:s", $_POST['Equipment'][$_POST['editableIndex']]['testDate']);
            }
            $model->save();
            return json_encode('');
        }

        $searchModel = new EquipmentSearch();
        $dataProvider = $searchModel->search(Yii::$app->request->queryParams);
        $dataProvider->pagination->pageSize = 15;
        if (isset($_GET['start_time'])) {
            $dataProvider->query->andWhere(['>=','testDate',$_GET['start_time']]);
            $dataProvider->query->andWhere(['<','testDate',$_GET['end_time']]);
        }

        return $this->render(
            'index',
            [
                'searchModel' => $searchModel,
                'dataProvider' => $dataProvider,
            ]
        );
    }

    /**
     * Lists all Equipment models.
     *
     * @return mixed
     */
    public function actionIndexCheck()
    {
        if (isset($_POST['editableAttribute'])) {
            $model = Equipment::find()
                ->where(['_id' => $_POST['editableKey']])
                ->one();
            if ($_POST['editableAttribute'] == 'serial') {
                $model['serial'] = $_POST['Equipment'][$_POST['editableIndex']]['serial'];
            }
            if ($_POST['editableAttribute'] == 'testDate') {
                $model['testDate'] = date("Y-m-d H:i:s", $_POST['Equipment'][$_POST['editableIndex']]['testDate']);
            }
            $model->save();
            return json_encode('');
        }

        $searchModel = new EquipmentSearch();
        $dataProvider = $searchModel->search(Yii::$app->request->queryParams);
        $dataProvider->pagination->pageSize = 15;
        if (isset($_GET['start_time'])) {
            $dataProvider->query->andWhere(['>=','testDate',$_GET['start_time']]);
            $dataProvider->query->andWhere(['<','testDate',$_GET['end_time']]);
        }

        return $this->render(
            'index-check',
            [
                'searchModel' => $searchModel,
                'dataProvider' => $dataProvider,
            ]
        );
    }

    /**
     * Displays a single Equipment model.
     * @param integer $id Id
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
                echo json_encode($model->errors);
                return $this->render('create', ['model' => $model]);
            }
            // сохраняем запись
            if ($model->save(false)) {
                MainFunctions::register('documentation','Добавлено оборудование',
                    '<a class="btn btn-default btn-xs">'.$model['equipmentType']['title'].'</a> '.$model['title'].'<br/>'.
                    'Серийный номер <a class="btn btn-default btn-xs">'.$model['serial'].'</a>');
                return $this->redirect(['view', 'id' => $model->_id]);
            }
            echo json_encode($model->errors);
        }
        return $this->render('create', ['model' => $model]);
    }

    /**
     * Creates a new Equipment models.
     *
     * @return mixed
     */
    /*    public function actionNew()
        {
            $equipments = array();
            $equipment_count = 0;
            $objects = Objects::find()
                ->select('*')
                ->all();
            foreach ($objects as $object) {
                $equipment = Equipment::find()
                    ->select('*')
                    ->where(['objectUuid' => $object['uuid']])
                    ->one();
                if ($equipment == null) {
                    $equipment = new Equipment();
                    $equipment->uuid = MainFunctions::GUID();
                    $equipment->objectUuid = $object['uuid'];
                    $equipment->equipmentTypeUuid = EquipmentType::EQUIPMENT_HVS;
                    $equipment->equipmentStatusUuid = EquipmentStatus::UNKNOWN;
                    $equipment->serial = '222222';
                    $equipment->tag = '111111';
                    $equipment->testDate = date('Y-m-d H:i:s');
                    $equipment->changedAt = date('Y-m-d H:i:s');
                    $equipment->createdAt = date('Y-m-d H:i:s');
                    $equipment->save();
                    $equipments[$equipment_count] = $equipment;
                    $equipment_count++;
                } else {
                    if ($equipment['equipmentTypeUuid'] != EquipmentType::EQUIPMENT_HVS) {
                        $equipment['equipmentTypeUuid'] = EquipmentType::EQUIPMENT_HVS;
                        $equipment['changedAt'] = date('Y-m-d H:i:s');
                        $equipment->save();
                        echo $equipment['uuid'] . '<br/>';
                    }
                }
            }
            return $this->render('new', ['equipments' => $equipments]);
        }*/


    /**
     * Updates an existing Equipment model.
     * If update is successful, the browser will be redirected to the 'view' page.
     *
     * @param integer $id Id
     * @return mixed
     * @throws NotFoundHttpException
     */
    public function actionUpdate($id)
    {
        $model = $this->findModel($id);
        if ($model->load(Yii::$app->request->post())) {
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
        $c = 'children';
        $fullTree = array();
        $types = EquipmentType::find()
            ->select('*')
            ->orderBy('title')
            ->all();
        $oCnt0 = 0;
        foreach ($types as $type) {
            $fullTree[$oCnt0]['title'] = Html::a(
                $type['title'],
                ['equipment-type/view', 'id' => $type['_id']]
            );
            $fullTree[$oCnt0]['folder'] = true;
            $equipments = Equipment::find()
                ->select('*')
                ->where(['equipmentTypeUuid' => $type['uuid']])
                ->orderBy('serial')
                ->all();
            $oCnt1 = 0;
            foreach ($equipments as $equipment) {
                $fullTree[$oCnt0][$c][$oCnt1]['title']
                    = Html::a(
                    'ул.' . $equipment['object']['house']['street']['title'] .
                    ', д.' . $equipment['object']['house']['number'] .
                    ', кв.' . $equipment['object']['title'],
                    ['equipment/view', 'id' => $equipment['_id']]
                );
                if ($equipment['equipmentStatusUuid'] == EquipmentStatus::NOT_MOUNTED) {
                    $class = 'critical1';
                } elseif ($equipment['equipmentStatusUuid'] == EquipmentStatus::NOT_WORK) {
                    $class = 'critical2';
                } else {
                    $class = 'critical3';
                }
                $fullTree[$oCnt0][$c][$oCnt1]['status'] = '<div class="progress"><div class="'
                    . $class . '">' . $equipment['equipmentStatus']->title . '</div></div>';
                $fullTree[$oCnt0][$c][$oCnt1]['date'] = $equipment['testDate'];
                $fullTree[$oCnt0][$c][$oCnt1]['serial'] = $equipment['serial'];

                $measure = Measure::find()
                    ->select('*')
                    ->where(['equipmentUuid' => $equipment['uuid']])
                    ->orderBy('date DESC')
                    ->one();
                if ($measure) {
                    $fullTree[$oCnt0][$c][$oCnt1]['measure_date'] = $measure['date'];
                    $fullTree[$oCnt0][$c][$oCnt1]['measure_value'] = $measure['value'];
                    $fullTree[$oCnt0][$c][$oCnt1]['measure_user'] = $measure['user']->name;
                } else {
                    $fullTree[$oCnt0][$c][$oCnt1]['measure_date'] = $equipment['changedAt'];
                    $fullTree[$oCnt0][$c][$oCnt1]['measure_value'] = "не снимались";
                    $fullTree[$oCnt0][$c][$oCnt1]['measure_user'] = "-";
                }

                $photo = Photo::find()
                    ->select('*')
                    ->where(['objectUuid' => $equipment['uuid']])
                    ->orderBy('createdAt DESC')
                    ->one();
                if ($photo) {
                    $fullTree[$oCnt0][$c][$oCnt1]['photo_date'] = $photo['createdAt'];
                    $fullTree[$oCnt0][$c][$oCnt1]['photo'] = Html::a(
                        '<img width="100px" src="/storage/equipment/' . $photo['uuid'] . '.jpg" />',
                        ['storage/equipment/' . $photo['uuid'] . '.jpg']
                    );
                    $fullTree[$oCnt0][$c][$oCnt1]['photo_user'] = $photo['user']->name;
                } else {
                    $fullTree[$oCnt0][$c][$oCnt1]['photo_date'] = 'нет фото';
                    $fullTree[$oCnt0][$c][$oCnt1]['photo'] = '-';
                    $fullTree[$oCnt0][$c][$oCnt1]['photo_user'] = '-';
                }
                $oCnt1++;
            }
            $oCnt0++;
        }
        return $this->render(
            'tree',
            ['equipment' => $fullTree]
        );
    }

    /**
     * Build tree of equipment by user
     *
     * @param integer $id Id
     * @param $date_start
     * @param $date_end
     * @return mixed
     */
    public function actionTable($id, $date_start, $date_end)
    {
        ini_set('memory_limit', '-1');
        $c = 'children';
        $fullTree = array();
        $user = Users::find()
            ->select('*')
            ->where(['id' => $id])
            ->one();
        if ($user) {
            $oCnt1 = 0;
            $gut_total_count = 0;
            $object_count = 0;
            $user_houses = UserHouse::find()->select('houseUuid')->where(['userUuid' => $user['uuid']])->all();
            foreach ($user_houses as $user_house) {
                $flats = Objects::find()->select('uuid')->where(['houseUuid' => $user_house['houseUuid']])->all();
                foreach ($flats as $flat) {
                    $equipment = Equipment::find()
                        ->select('*')
                        ->where(['flatUuid' => $flat['uuid']])
                        ->orderBy('changedAt desc')
                        ->one();
                    if ($equipment) {
                        $gut = 0;
                        $object_count++;
                        $fullTree[$oCnt1]['title']
                            = Html::a(
                            'ул.' . $equipment['house']['street']['title'] . ', д.' .
                            $equipment['house']['number'] . ', кв.' . $equipment['flat']['number'],
                            ['equipment/view', 'id' => $equipment['_id']]
                        );

                        $measures = Measure::find()
                            ->where(['equipmentUuid' => $equipment['uuid']])
                            ->orderBy('date DESC')
                            ->all();
                        $oCnt2 = 0;
                        // есть измерение, есть/нет фото
                        foreach ($measures as $measure) {
                            $fullTree[$oCnt1][$c][$oCnt2]
                            ['measure_date'] = $measure['date'];
                            $fullTree[$oCnt1][$c][$oCnt2]
                            ['measure'] = $measure['value'];
                            $photo_flat_count = Photo::find()
                                ->where(['objectUuid' => $equipment['uuid']])
                                ->andWhere(['date' >= ($measure['date'] - 3600)])
                                ->andWhere(['date' < ($measure['date'] + 3600)])
                                ->count();

                            if ($photo_flat_count > 0) {
                                $class = 'critical3';
                                $status = 'А. Отличное';
                            } else {
                                $class = 'critical2';
                                $status = 'Б. Удовлетворительное';
                            }
                            $fullTree[$oCnt1][$c][$oCnt2]['status'] = '<div class="progress"><div class="'
                                . $class . '">' . $status . '</div></div>';
                            $gut = 1;
                            $oCnt2++;
                        }

                        // есть комментарий,  нет измерения, есть/нет фото
                        $messages = Message::find()
                            ->where(['flatUuid' => $equipment['flat']['uuid']])
                            ->orderBy('date DESC')
                            ->all();
                        foreach ($messages as $message) {
                            $measure_count = Measure::find()
                                ->where(['equipmentUuid' => $equipment['uuid']])
                                ->andWhere(['date' >= ($message['date'] - 1200)])
                                ->andWhere(['date' < ($message['date'] + 1200)])
                                ->count();

                            $photo_flat_count = Photo::find()
                                ->where(['objectUuid' => $equipment['uuid']])
                                ->andWhere(['date' >= ($message['date'] - 1200)])
                                ->andWhere(['date' < ($message['date'] + 1200)])
                                ->count();

                            if ($measure_count == 0 && $photo_flat_count > 0) {
                                $fullTree[$oCnt1][$c][$oCnt2]
                                ['measure_date'] = $message['date'];
                                $fullTree[$oCnt1][$c][$oCnt2]
                                ['measure'] = 'есть сообщение';
                                $class = 'critical3';
                                $status = 'Отличное';
                                $fullTree[$oCnt1][$c][$oCnt2]['status'] = '<div class="progress"><div class="'
                                    . $class . '">Б.' . $status . '</div></div>';
                                $oCnt2++;
                                $gut = 1;
                            }
                        }

                        if ($gut == 0) {
                            $class = 'critical1';
                            $status = 'Не удовлетворительное';
                            $fullTree[$oCnt1][$c][$oCnt2]['status'] = '<div class="progress"><div class="'
                                . $class . '">А.' . $status . '</div></div>';
                        } else
                            $gut_total_count++;
                    }
                }
                $fullTree[$oCnt1]['title'] = 'Всего';
                $percent = 0;
                if ($object_count > 0)
                    $percent = number_format($gut_total_count * 100 / $object_count, 2);
                $fullTree[$oCnt1]['measure_date'] = 'Показаний: ' . $gut_total_count . '[' . $percent . ']';
            }
        }
        return $this->render(
            'tree-user',
            ['equipment' => $fullTree]
        );
    }

    /**
     * Build tree of equipment by user
     *
     * @return mixed
     */
    public function actionTreeUser()
    {
        ini_set('memory_limit', '-1');
        $fullTree = array();
        $users = Users::find()
            ->select('*')
            ->where('name != "sUser"')
            ->andWhere('name != "Иванов О.А."')
            ->orderBy('_id')
            ->all();

        foreach ($users as $user) {
            $fullTree['children'][] = [
                'title' => $user['name'],
                'type' => 'user',
                'key' => $user['_id'],
                'folder' => true,
                'expanded' => false
            ];
            $user_houses = UserHouse::find()->select('houseUuid')->where(['userUuid' => $user['uuid']])->all();
            foreach ($user_houses as $user_house) {
                $childIdx = count($fullTree['children']) - 1;
                $houses = House::find()->select('uuid,number')->where(['uuid' => $user_house['houseUuid']])->
                orderBy('number')->all();
                foreach ($houses as $house) {
                    $fullTree['children'][$childIdx]['children'][] =
                        [
                            'title' => 'ул.' . $house['street']['title'] . ', д.' . $house['number'],
                            'type' => 'house',
                            'key' => $house['_id'],
                            'folder' => true
                        ];
                    $childIdx2 = count($fullTree['children'][$childIdx]['children']) - 1;
                    $objects = Objects::find()->select('uuid, title')->where(['houseUuid' => $house['uuid']])->all();
                    foreach ($objects as $object) {
                        $equipments = Equipment::find()->where(['objectUuid' => $object['uuid']])->all();
                        foreach ($equipments as $equipment) {
                            $fullTree['children'][$childIdx]['children'][$childIdx2]['children'][] =
                                self::addEquipment($equipment);
                        }
                    }
                }
            }
        }
        $users = Users::find()->all();
        $items = ArrayHelper::map($users, 'uuid', 'name');

        return $this->render(
            'tree-street',
            [
                'equipment' => $fullTree,
                'users' => $items
            ]
        );
    }

    /**
     * Build tree of equipment by user
     *
     * @return mixed
     */
    public function actionTreeStreet()
    {
        ini_set('memory_limit', '-1');
        $fullTree = array();
        $streets = Street::find()
            ->select('*')
            ->orderBy('title')
            ->all();
        foreach ($streets as $street) {
            $fullTree['children'][] = [
                'title' => $street['title'],
                'type' => 'street',
                'key' => $street['_id'],
                'folder' => true,
                'expanded' => true
            ];
            $childIdx = count($fullTree['children']) - 1;
            $houses = House::find()->select('uuid,number')->where(['streetUuid' => $street['uuid']])->
            orderBy('number')->all();
            foreach ($houses as $house) {
                $user_house = UserHouse::find()->select('_id')->where(['houseUuid' => $house['uuid']])->one();
                $user = Users::find()->where(['uuid' =>
                    UserHouse::find()->where(['houseUuid' => $house['uuid']])->one()
                ])->one();
                $fullTree['children'][$childIdx]['children'][] =
                    [
                        'title' => $house['number'],
                        'type' => 'house',
                        'key' => $house['_id'],
                        'folder' => true
                    ];
                $childIdx2 = count($fullTree['children'][$childIdx]['children']) - 1;
                $objects = Objects::find()->select('uuid, title')->where(['houseUuid' => $house['uuid']])->all();
                foreach ($objects as $object) {
                    $fullTree['children'][$childIdx]['children'][$childIdx2]['children'][] =
                        ['title' => $object['title'], 'key' => $object['_id'] . "",
                            'expanded' => true, 'folder' => true];
                    $childIdx3 = count($fullTree['children'][$childIdx]['children'][$childIdx2]['children']) - 1;
                    $equipments = Equipment::find()->where(['objectUuid' => $object['uuid']])->all();
                    foreach ($equipments as $equipment) {
                        $fullTree['children'][$childIdx]['children'][$childIdx2]['children'][$childIdx3]['children'][] =
                            self::addEquipment($equipment);
                    }
                }
            }
        }
        $users = Users::find()->all();
        $items = ArrayHelper::map($users, 'uuid', 'name');

        return $this->render(
            'tree-street',
            [
                'equipment' => $fullTree,
                'users' => $items
            ]
        );
    }

    /**
     * Build tree of equipment by user
     *
     * @return mixed
     */
    public function actionTreeMeasure()
    {
        ini_set('memory_limit', '-1');
        $fullTree = array();
        $streets = Street::find()
            ->select('*')
            ->orderBy('title')
            ->all();
        $oCnt0 = 0;
        foreach ($streets as $street) {
            $house_count = 0;
            $house_visited = 0;
            $houses = House::find()->select('uuid,number')->where(['streetUuid' => $street['uuid']])->
            orderBy('number')->all();
            foreach ($houses as $house) {
                $objects = Objects::find()->select('uuid,number')->where(['objectUuid' => $house['uuid']])->all();
                foreach ($objects as $object) {
                    $house_count++;
                    $visited = 0;
                    $equipments = Equipment::find()->where(['objectUuid' => $object['uuid']])->all();
                    foreach ($equipments as $equipment) {
                        $fullTree[$oCnt0]['title']
                            = Html::a(
                            'ул.' . $equipment['house']['street']['title'] . ', д.' . $equipment['house']['number'] . ', кв.' . $equipment['object']['number'],
                            ['equipment/view', 'id' => $equipment['_id']]
                        );

                        $measures = Measure::find()
                            ->select('*')
                            ->where(['equipmentUuid' => $equipment['uuid']])
                            ->orderBy('date')
                            ->all();

                        $measure_count_column = 0;
                        $fullTree[$oCnt0]['measure_date0'] = '';
                        $fullTree[$oCnt0]['measure_value0'] = '';
                        $fullTree[$oCnt0]['measure_date1'] = '';
                        $fullTree[$oCnt0]['measure_value1'] = '';
                        $fullTree[$oCnt0]['measure_date2'] = '';
                        $fullTree[$oCnt0]['measure_value2'] = '';
                        $fullTree[$oCnt0]['measure_date3'] = '';
                        $fullTree[$oCnt0]['measure_value3'] = '';
                        $fullTree[$oCnt0]['measure_user'] = '';
                        $measure_first = 0;
                        $measure_last = 0;
                        $measure_date_first = 0;
                        $measure_date_last = 0;
                        foreach ($measures as $measure) {
                            $fullTree[$oCnt0]['measure_date' . $measure_count_column] = $measure['date'];
                            $fullTree[$oCnt0]['measure_value' . $measure_count_column] = $measure['value'];
                            $fullTree[$oCnt0]['measure_user'] = $measure['user']->name;
                            if ($measure_count_column == 0) {
                                $measure_first = $measure['value'];
                                $measure_date_first = $measure['date'];
                            } else {
                                $measure_last = $measure['value'];
                                $measure_date_last = $measure['date'];
                            }
                            $measure_count_column++;
                            if ($measure_count_column > 3) break;
                        }

                        $datetime1 = date_create($measure_date_first);
                        $datetime2 = date_create($measure_date_last);
                        if ($datetime2 && $datetime1) {
                            $diff = $datetime2->diff($datetime1);
                            $interval = $diff->format("%h") + ($diff->days * 24);
                            $value = number_format($measure_last - $measure_first, 2);
                        } else {
                            $interval = 0;
                            $value = 0;
                        }
                        $fullTree[$oCnt0]['interval'] = $interval;
                        $fullTree[$oCnt0]['value'] = $value;
                        if ($interval > 0)
                            $fullTree[$oCnt0]['relative'] = number_format($value / $interval, 2);

                        $message = Message::find()
                            ->select('*')
                            ->orderBy('date DESC')
                            ->where(['flatUuid' => $equipment['flat']['uuid']])
                            ->one();
                        if ($message != null) {
                            $fullTree[$oCnt0]['message'] =
                                mb_convert_encoding(substr($message['message'], 0, 150), 'UTF-8', 'UTF-8');
                            if ($visited == 0)
                                $visited = 1;
                            $house_visited++;
                        }
                        $oCnt0++;
                    }
                }
            }
        }
        return $this->render(
            'tree-measure',
            ['equipment' => $fullTree]
        );
    }

    /**
     * Deletes an existing Equipment model.
     * If deletion is successful, the browser will be redirected to the 'index' page.
     *
     * @param integer $id Id
     *
     * @return mixed
     * @throws NotFoundHttpException
     * @throws StaleObjectException
     * @throws \Throwable
     */
    public
    function actionDelete($id)
    {
        $equipment = $this->findModel($id);
        $photos = Photo::find()
            ->select('*')
            ->where(['equipmentUuid' => $equipment['uuid']])
            ->all();
        foreach ($photos as $photo) {
            $photo->delete();
        }

        $measures = Measure::find()
            ->select('*')
            ->where(['equipmentUuid' => $equipment['uuid']])
            ->all();
        foreach ($measures as $measure) {
            $measure->delete();
        }

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
    protected
    function findModel($id)
    {
        if (($model = Equipment::findOne($id)) !== null) {
            return $model;
        } else {
            throw new NotFoundHttpException('The requested page does not exist.');
        }
    }

    static function getDocDir($documentation)
    {
        if ($documentation['equipmentTypeUuid'] != null) {
            return 'storage/doc/' . $documentation['equipmentTypeUuid'] . '/';
        }

        //$identity = \Yii::$app->user->identity;
        //$userName = $identity->oid;
        //$dir = 'storage/doc/' . $typeUuid . '/';
        $dir = 'storage/doc/';
        //$dir = 'storage/' . $userName . $path;
        //$url = Yii::$app->request->BaseUrl . '/' . $dir;
        return $dir;
    }

    /**
     * функция отрабатывает сигналы от дерева и выполняет связывание выбранного оборудования с пользователем
     *
     * @return mixed
     * @throws ExceptionAlias
     * @throws Throwable
     */
    public function actionMove()
    {
        if (isset($_POST["selected_node"])) {
            $node = $_POST["selected_node"];
            $user = $_POST["user"];
            if ($user && $node)
                $this->updateUserEquipment($user, $node);
        }
        $this->enableCsrfValidation = false;
        return 0;
    }

    /**
     * функция отрабатывает сигналы от дерева и выполняет отвязывание выбранного оборудования от пользователя
     * @return mixed
     * @throws ExceptionAlias
     * @throws Throwable
     */
    public function actionRemove()
    {
        if (isset($_POST["selected_node"])) {
            $node = $_POST["selected_node"];
            if ($node)
                $this->updateUserEquipment(null, $node);
        }
        $this->enableCsrfValidation = false;
        return 0;
    }

    /**
     * функция отрабатывает сигналы от дерева и выполняет переименование оборудования
     * @return mixed
     */
    public function actionRename()
    {
        $id = '';
        $param = '';
        if (isset($_POST["uuid"]))
            $id = $_POST["uuid"];
        if (isset($_POST["param"]))
            $param = $_POST["param"];
        if (isset($_POST["folder"]) && $_POST["folder"] == "false") {
            $equipment = Equipment::find()->where(['_id' => $id])->one();
            if ($equipment) {
                $equipment['title'] = $param;
                if ($equipment->save())
                    return Errors::OK;
                else
                    return Errors::ERROR_SAVE;
            }
        }
        return Errors::GENERAL_ERROR;
    }

    /**
     * функция связывает/отвязывает оборудование от пользователей
     *
     * @param $user
     * @param $node
     * @throws ExceptionAlias
     * @throws Throwable
     */
    function updateUserEquipment($user, $node)
    {
        $equipment = Equipment::find()->where(['_id' => $node])->one();
        if ($equipment['uuid']) {
            /*            if (!$user) {
                            $userEquipment = UserEquipment::find()->select('*')
                                ->where(['equipmentUuid' => $equipment['uuid']])
                                ->one();
                            if ($userEquipment) {
                                $userEquipment->delete();
                                $this->redirect('tree');
                            }
                        } else {
                            $userEquipment = UserEquipment::find()->select('*')
                                ->where(['equipmentUuid' => $equipment['uuid']])
                                ->andWhere(['userUuid' => $user])
                                ->one();
                            if (!$userEquipment) {
                                $userEquipment = new UserEquipment();
                                $userEquipment->uuid = (new \app\commands\MainFunctions)->GUID();
                                $userEquipment->equipmentUuid = $equipment['uuid'];
                                $userEquipment->userUuid = $user;
                                $userEquipment->save();
                                $this->redirect('tree');
                            }
                        }*/
        }
    }

    /**
     * функция добавляет/отвязывает оборудование от пользователей
     *
     * @param $user
     * @param $node
     * @throws ExceptionAlias
     * @throws Throwable
     */
    function addUserEquipment($user, $node)
    {
        $equipment = Equipment::find()->where(['_id' => $node])->one();
        if ($equipment['uuid']) {
            /*            if ($user) {
                            $userEquipment = UserEquipment::find()->select('*')->where(['equipmentUuid' => $equipment['uuid']])
                                ->andWhere(['userUuid' => $user])
                                ->one();
                            if (!$userEquipment) {
                                $userEquipment = new UserEquipment();
                                $userEquipment->uuid = (new MainFunctions)->GUID();
                                $userEquipment->equipmentUuid = $equipment['uuid'];
                                $userEquipment->userUuid = $user;
                                $userEquipment->save();
                            }
                        } else {
                            $userEquipment = UserEquipment::find()->select('*')
                                ->where(['equipmentUuid' => $equipment['uuid']])
                                ->one();
                            if ($userEquipment)
                                $userEquipment->delete();
                        }*/
        }
    }

    /**
     * Сохраняем файл согласно нашим правилам.
     *
     * @param Equipment $model Шаблон задачи
     * @param UploadedFile $file Файл
     *
     * @return string | null
     */
    private
    static function _saveFile($model, $file)
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

    public
    function actionOperations()
    {
        if (isset($_GET["equipmentUuid"])) {
            $tasks = Task::find()->where(['equipmentUuid' => $_GET["equipmentUuid"]])
                ->orderBy('changedAt DESC')
                ->limit(20)
                ->all();
            return $this->renderAjax('/task/_tasks_list', [
                'tasks' => $tasks,
            ]);
        }
        return true;
    }

    public
    function actionMeasures()
    {
        if (isset($_GET["equipmentUuid"])) {
            $measures = Measure::find()->where(['equipmentUuid' => $_GET["equipmentUuid"]])
                ->orderBy('changedAt DESC')
                ->limit(20)
                ->all();
            return $this->renderAjax('/measure/_measure_list', [
                'measures' => $measures,
            ]);
        }
        return true;
    }

    public
    function actionStatus()
    {
        if (isset($_GET["equipmentUuid"])) {
            $model = Equipment::find()->where(['uuid' => $_GET["equipmentUuid"]])
                ->one();
            return $this->renderAjax('_change_form', [
                'model' => $model,
            ]);
        }
        if ($_POST["Equipment"]["equipmentStatusUuid"]) {
            $model = Equipment::find()->where(['_id' => $_POST["Equipment"]["_id"]])
                ->one();
            if ($model) {
                $model["equipmentStatusUuid"] = $_POST["Equipment"]["equipmentStatusUuid"];
                $model->save();
            }
        }
        return false;
    }

    public
    function actionSerial()
    {
        if (isset($_GET["equipmentUuid"])) {
            $model = Equipment::find()->where(['uuid' => $_GET["equipmentUuid"]])
                ->one();
            return $this->renderAjax('_change_serial', [
                'model' => $model,
            ]);
        }
        if ($_POST["Equipment"]["serial"]) {
            $model = Equipment::find()->where(['_id' => $_POST["Equipment"]["_id"]])
                ->one();
            if ($model) {
                $model["serial"] = $_POST["Equipment"]["serial"];
                $model->save();
            }
        }
        return false;
    }

    public
    function actionSelectTask()
    {
        if (isset($_GET["equipmentUuid"])) {
            $model = Equipment::find()->where(['uuid' => $_GET["equipmentUuid"]])
                ->one();
            return $this->renderAjax('_select_task', [
                'model' => $model,
            ]);
        }
        $equipmentStageUuid = null;
        if (isset($_POST["Equipment"]["equipmentStatusUuid"]))
            $equipmentStageUuid = $_POST["Equipment"]["equipmentStatusUuid"];
        if (isset($_POST["equipmentStatusUuid"]))
            $equipmentStageUuid = $_POST["equipmentStatusUuid"];

        if ($equipmentStageUuid) {
            $accountUser = Yii::$app->user->identity;
            $currentUser = Users::findOne(['userId' => $accountUser['id']]);
            $equipment = Equipment::findOne(['_id' => $_POST["Equipment"]['_id']]);
            if ($equipment) {
                $return="";
                //$return = OrderFunctions::createOrder($equipment['uuid'], $currentUser, $equipmentStageUuid, null);
                if ($return['result'] == null)
                    return $return['message'];
                else
                    return false;
            }
        }
        return false;
    }

    /**
     * функция отрабатывает сигналы от дерева и выполняет добавление нового оборудования
     *
     * @return mixed
     */
    public
    function actionNew()
    {
        if (isset($_POST["selected_node"])) {
            $folder = $_POST["folder"];
            if (isset($_POST["type"]))
                $type_id = $_POST["selected_node"];
            else $type_id = 0;
            if (isset($_POST["type_uuid"]))
                $type_uuid = $_POST["type_uuid"];
            else $type_uuid = 0;
            // из дерева объектов
            if (isset($_POST["uuid"]))
                $object_uuid = $_POST["uuid"];
            else $object_uuid = 0;
            if ($folder == "true" && $type_id > 0) {
                $equipmentType = new EquipmentType();
                return $this->renderAjax('../equipment-type/_add_form', [
                    'equipmentType' => $equipmentType,
                    'type_id' => $type_id
                ]);
            }
            if ($folder == "true" && $type_uuid) {
                $equipment = new Equipment();
                return $this->renderAjax('_add_form', [
                    'equipment' => $equipment,
                    'type_uuid' => $type_uuid,
                    'object_uuid' => null
                ]);
            }
            if ($folder == "true" && $object_uuid) {
                $equipment = new Equipment();
                return $this->renderAjax('_add_form', [
                    'equipment' => $equipment,
                    'object_uuid' => $object_uuid,
                    'type_uuid' => null
                ]);
            }
        }
        return 0;
    }

    /**
     * функция отрабатывает сигналы от дерева и выполняет редактирование оборудования
     *
     * @return mixed
     */
    public function actionEdit()
    {
        if (isset($_POST["selected_node"])) {
            $folder = $_POST["folder"];
            $reference = "equipment";
            if (isset($_POST["reference"])) {
                $reference = $_POST["reference"];
            }
            if (isset($_POST["uuid"]))
                $uuid = $_POST["uuid"];
            else $uuid = 0;
            if (isset($_POST["type_uuid"]))
                $type_uuid = $_POST["type_uuid"];
            else $type_uuid = 0;

            if ($folder == "false" && $uuid) {
                $equipment = Equipment::find()->where(['uuid' => $uuid])->one();
                if ($equipment)
                    return $this->renderAjax('_add_form', [
                        'equipment' => $equipment,
                        'type_uuid' => null,
                        'object_uuid' => null,
                        'reference' => $reference
                    ]);
                return "";
            }
            if ($folder == "true" && $type_uuid) {
                $equipmentType = EquipmentType::find()->where(['uuid' => $type_uuid])->one();
                if ($equipmentType)
                    return $this->renderAjax('../equipment-model/_add_form', [
                        'equipmentType' => $equipmentType,
                        'type_uuid' => $equipmentType['uuid'],
                        'object_uuid' => null,
                        'reference' => $reference
                    ]);
                return "";
            }
            return "";
        }
        return "";
    }

    /**
     * Creates a new Equipment model.
     * @return mixed
     */
    public
    function actionSave()
    {
        if (isset($_POST['equipmentUuid']))
            $model = Equipment::find()->where(['uuid' => $_POST['equipmentUuid']])->one();
        else
            $model = new Equipment();
        if ($model->load(Yii::$app->request->post())) {
            $file = UploadedFile::getInstance($model, 'image');
            if ($file && $file->tempName) {
                $fileName = self::_saveFile($model, $file);
                if ($fileName) {
                    $model->image = $fileName;
                }
            }
            if ($model->save(false)) {
                if (isset($_POST['reference']) && $_POST['reference'] == "object")
                    return $this->redirect(['/objects/tree']);
                return $this->redirect(['/equipment/tree']);
            }
        }
        return $this->render('_add_form', [
            'model_uuid' => null,
            'object_uuid' => null,
            'equipment' => $model,
            'reference' => "/equipment/tree"
        ]);
    }

    /**
     * @param $equipment
     * @return array
     */
    public function addEquipment($equipment)
    {
        $userSystems = UserSystem::find()
            ->where(['equipmentSystemUuid' => $equipment['equipmentType']['equipmentSystem']['uuid']])
            ->all();
        $count = 0;
        $userEquipmentName = Html::a('<span class="glyphicon glyphicon-comment"></span>&nbsp',
            ['/request/form', 'equipmentUuid' => $equipment['uuid']],
            [
                'title' => 'Добавить заявку',
                'data-toggle' => 'modal',
                'data-target' => '#modal_request',
            ]
        );
        foreach ($userSystems as $userSystem) {
            if ($count > 0) $userEquipmentName .= ', ';
            $userEquipmentName .= $userSystem['title'];
            $count++;
        }
        if ($count == 0) $userEquipmentName = '<div class="progress"><div class="critical5">не назначен</div></div>';

        $tasks = Task::find()
            ->select('*')
            ->where(['equipmentUuid' => $equipment['uuid']])
            ->orderBy('changedAt DESC')
            ->one();
        $task_text = '<div class="progress"><div class="critical5">задач нет</div></div>';
        if ($tasks) {
            if (strlen($tasks['taskTemplate']->title) > 50)
                $title = substr($tasks['taskTemplate']->title, 0, 50);
            else
                $title = $tasks['taskTemplate']->title;
            $title = mb_convert_encoding($title, "UTF-8", "UTF-8");
            if ($tasks['workStatusUuid'] == WorkStatus::COMPLETE)
                $task_text = '<div class="progress"><div class="critical3">' . $title . '</div></div>';
            else
                $task_text = '<div class="progress"><div class="critical2">' . $title . '</div></div>';
        }
        $task = Html::a($task_text,
            ['select-task', 'equipmentUuid' => $equipment['uuid']],
            [
                'title' => 'Создать задачу обслуживания',
                'data-toggle' => 'modal',
                'data-target' => '#modalAddTask',
            ]
        );
        $status = MainFunctions::getColorLabelByStatus($equipment['equipmentStatus'], "equipment");
        $status = Html::a($status,
            ['/equipment/status', 'equipmentUuid' => $equipment['uuid']],
            [
                'title' => 'Сменить статус',
                'data-toggle' => 'modal',
                'data-target' => '#modalStatus',
            ]
        );

        $documentations = Documentation::find()->where(['equipmentUuid' => $equipment['uuid']])->all();
        $docs = '';
        foreach ($documentations as $documentation) {
            $docs .= Html::a('<span class="glyphicon glyphicon-floppy-disk"></span>&nbsp',
                [self::getDocDir($documentation) . '/' . $documentation['path']], ['title' => $documentation['title']]
            );
        }

        $links = Html::a('<span class="glyphicon glyphicon-check"></span>&nbsp',
            ['/request/form', 'equipmentUuid' => $equipment['uuid']],
            [
                'title' => 'Добавить заявку',
                'data-toggle' => 'modal',
                'data-target' => '#modal_request',
            ]
        );
        $links .= Html::a('<span class="glyphicon glyphicon-briefcase"></span>&nbsp',
            ['/equipment-register/form', 'equipmentUuid' => $equipment['uuid']],
            [
                'title' => 'Добавить запись',
                'data-toggle' => 'modal',
                'data-target' => '#modalChange',
            ]
        );
        $links .= Html::a('<span class="glyphicon glyphicon-stats"></span>&nbsp',
            ['/equipment/measures', 'equipmentUuid' => $equipment['uuid']],
            [
                'title' => 'Измерения',
                'data-toggle' => 'modal',
                'data-target' => '#modalMeasures',
            ]
        );
        $links .= Html::a('<span class="glyphicon glyphicon-calendar"></span>&nbsp',
            ['/equipment-register/list', 'equipmentUuid' => $equipment['uuid']],
            [
                'title' => 'Журнал событий',
                'data-toggle' => 'modal',
                'data-target' => '#modalRegister',
            ]
        );
        $links .= Html::a('<span class="glyphicon glyphicon-phone"></span>&nbsp',
            ['/equipment/operations', 'equipmentUuid' => $equipment['uuid']],
            [
                'title' => 'Перечень операций',
                'data-toggle' => 'modal',
                'data-target' => '#modalTasks',
            ]
        );
        if ($equipment["serial"]) {
            $serial = $equipment["serial"];
        } else {
            $serial = 'отсутствует';
        }
        $serial = Html::a($serial,
            ['/equipment/serial', 'equipmentUuid' => $equipment['uuid']],
            [
                'title' => 'Сменить серийный номер',
                'data-toggle' => 'modal',
                'data-target' => '#modalSN',
            ]
        );
        return ['key' => $equipment['_id'] . "",
            'folder' => false,
            'serial' => $serial,
            'title' => $equipment["title"],
            'tag' => $equipment['tag'],
            'uuid' => $equipment['uuid'],
            'type_uuid' => $equipment['equipmentType']['uuid'],
            'docs' => $docs,
            'start' => "" . date_format(date_create($equipment['testDate']), "Y-m-d H:i:s"),
            'location' => $equipment['object']->title,
            'tasks' => $task,
            'user' => $userEquipmentName,
            'links' => $links,
            'status' => $status];
    }

    public function actionTimelineAll()
    {
        $events = [];
        $equipments = Equipment::find()->all();
        foreach ($equipments as $equipment) {
            $events = self::actionTimeline($equipment['uuid'],0);
        }
        $sort_events = MainFunctions::array_msort($events, ['date' => SORT_DESC]);
        return $this->render(
            'timeline',
            [
                'events' => $sort_events
            ]
        );
    }

    /**
     * Displays a equipment register
     *
     * @param string $uuid equipment.
     *
     * @param $r
     * @return mixed
     */
    public function actionTimeline($uuid, $r)
    {
        $events = [];
        $tasks = Task::find()->where(['equipmentUuid' => $uuid])->orderBy('changedAt DESC')->all();
        foreach ($tasks as $task) {
            if ($task['workStatusUuid'] == WorkStatus::NEW_OPERATION) {
                $text = '<a class="btn btn-default btn-xs">Создана задача для оборудования ' . $task['equipment']['title'] . '</a><br/>
                <i class="fa fa-bar-chart"></i>&nbsp;Задача<br/>';
                $events[] = ['date' => $task['changedAt'], 'event' => self::formEvent($task['changedAt'], 'task',
                    $task['_id'], 'задача', $text)];
            }
            if ($task['workStatusUuid'] == WorkStatus::COMPLETE) {
                $text = '<a class="btn btn-default btn-xs">Закончена задача для оборудования ' . $task['equipment']['title'] . '</a><br/>
                <i class="fa fa-bar-chart"></i>&nbsp;Задача<br/>';
                $events[] = ['date' => $task['changedAt'], 'event' => self::formEvent($task['changedAt'], 'task',
                    $task['_id'], 'задача', $text)];
            }
        }

        $equipment_photos = Photo::find()->where(['objectUuid' => $uuid])->all();
        foreach ($equipment_photos as $equipment_photo) {
            $text = '<a class="btn btn-default btn-xs">Для оборудования сделано фото</a><br/><i class="fa fa-cogs"></i>&nbsp;Фото<br/>';
            $events[] = ['date' => $equipment_photo['date'], 'event' => self::formEvent($equipment_photo['date'], 'photo',
                $equipment_photo['_id'], 'фото', $text)];
        }

        $measures = Measure::find()
            ->where(['=', 'equipmentUuid', $uuid])
            ->all();
        foreach ($measures as $measure) {
            $text = '<a class="btn btn-default btn-xs">' . $measure['equipment']['equipmentType']->title . '</a><br/>
                <i class="fa fa-bar-chart-o"></i>&nbsp;Значения: ' . $measure['value'] . '<br/>';
            $events[] = ['date' => $measure['date'], 'event' => self::formEvent($measure['date'], 'measure',
                $measure['_id'], $measure['equipment']['equipmentType']->title, $text)];
        }

        $equipment_registers = EquipmentRegister::find()
            ->where(['=', 'equipmentUuid', $uuid])
            ->all();
        foreach ($equipment_registers as $register) {
            $text = '<a class="btn btn-default btn-xs">' . $register['equipment']->title . '</a><br/>
                <i class="fa fa-cogs"></i>&nbsp;Тип: ' . $register['registerType']['title'] . '<br/>';
            $events[] = ['date' => $register['date'], 'event' => self::formEvent($register['date'], 'register',
                $register['_id'], $register['equipment']['equipmentType']->title, $text)];
        }

        if ($r > 0) {
            $sort_events = MainFunctions::array_msort($events, ['date' => SORT_DESC]);
            return $this->render(
                'view',
                [
                    'events' => $sort_events
                ]
            );
        } else {
            return $events;
        }
    }

    /**
     * Формируем код записи о событии
     * @param $date
     * @param $type
     * @param $id
     * @param $title
     * @param $text
     *
     * @return string
     */
    public static function formEvent($date, $type, $id, $title, $text)
    {
        $event = '<li>';
        if ($type == 'measure')
            $event .= '<i class="fa fa-bar-chart bg-aqua"></i>';
        if ($type == 'register')
            $event .= '<i class="fa fa-calendar bg-green"></i>';

        $event .= '<div class="timeline-item">';
        $event .= '<span class="time"><i class="fa fa-clock-o"></i> ' . date("M j, Y h:m", strtotime($date)) . '</span>';

        if ($type == 'measure')
            $event .= '<h3 class="timeline-header">' . Html::a('Исполнитель снял данные &nbsp;',
                    ['/measure/view', 'id' => Html::encode($id)]) . $title . '</h3>';

        if ($type == 'register')
            $event .= '<h3 class="timeline-header"><a href="#">Добавлено событие журнала</a></h3>';

        $event .= '<div class="timeline-body">' . $text . '</div>';
        $event .= '</div></li>';
        return $event;
    }
}

