<?php

namespace backend\controllers;

use backend\models\EquipmentSearch;
use common\components\Errors;
use common\components\MainFunctions;
use common\components\Tag;
use common\models\Contragent;
use common\models\Defect;
use common\models\Documentation;
use common\models\Equipment;
use common\models\EquipmentRegister;
use common\models\EquipmentRegisterType;
use common\models\EquipmentSystem;
use common\models\EquipmentType;
use common\models\House;
use common\models\Measure;
use common\models\Message;
use common\models\Objects;
use common\models\ObjectType;
use common\models\Photo;
use common\models\Street;
use common\models\Task;
use common\models\UserHouse;
use common\models\Users;
use common\models\UserSystem;
use common\models\WorkStatus;
use Throwable;
use Yii;
use yii\base\DynamicModel;
use yii\base\InvalidConfigException;
use yii\db\Exception;
use yii\db\StaleObjectException;
use yii\helpers\ArrayHelper;
use yii\helpers\Html;
use yii\web\NotFoundHttpException;

/**
 * EquipmentController implements the CRUD actions for Equipment model.
 */
class EquipmentController extends ZhkhController
{
    /**
     * Lists all Equipment models.
     *
     * @return mixed
     * @throws InvalidConfigException
     * @throws Exception
     */
    public function actionIndex()
    {
        if (isset($_POST['editableAttribute'])) {
            $model = Equipment::find()
                ->where(['_id' => $_POST['editableKey']])
                ->one();
            if ($_POST['editableAttribute'] == 'serial') {
                $model['serial'] = $_POST['Equipment'][$_POST['editableIndex']]['serial'];
                EquipmentRegisterController::addEquipmentRegister($model['uuid'],
                    EquipmentRegisterType::REGISTER_TYPE_CHANGE_PROPERTIES,
                    "Сменили серийный номер на " . $model['serial']);
            }
            if ($_POST['editableAttribute'] == 'tag') {
                $model['tag'] = $_POST['Equipment'][$_POST['editableIndex']]['tag'];
                EquipmentRegisterController::addEquipmentRegister($model['uuid'],
                    EquipmentRegisterType::REGISTER_TYPE_CHANGE_PROPERTIES,
                    "Смена тега на " . $model['tag']);
            }
            if ($_POST['editableAttribute'] == 'equipmentTypeUuid') {
                $model['equipmentTypeUuid'] = $_POST['Equipment'][$_POST['editableIndex']]['equipmentTypeUuid'];
                EquipmentRegisterController::addEquipmentRegister($model['uuid'],
                    EquipmentRegisterType::REGISTER_TYPE_CHANGE_PROPERTIES,
                    "Смена типа элемента на " . $model['equipmentType']['title']);
            }
            if ($_POST['editableAttribute'] == 'equipmentStatusUuid') {
                $model['equipmentStatusUuid'] = $_POST['Equipment'][$_POST['editableIndex']]['equipmentStatusUuid'];
                EquipmentRegisterController::addEquipmentRegister($model['uuid'],
                    EquipmentRegisterType::REGISTER_TYPE_CHANGE_STATUS,
                    "Смена статуса на " . $model['equipmentStatus']['title']);
            }
            if ($_POST['editableAttribute'] == 'testDate') {
                $model['testDate'] = $_POST['Equipment'][$_POST['editableIndex']]['testDate'];
                EquipmentRegisterController::addEquipmentRegister($model['uuid'],
                    EquipmentRegisterType::REGISTER_TYPE_CHANGE_PROPERTIES,
                    "Смена даты поверки на " . $model['testDate']);
            }
            if ($_POST['editableAttribute'] == 'inputDate') {
                $model['inputDate'] = $_POST['Equipment'][$_POST['editableIndex']]['inputDate'];
                EquipmentRegisterController::addEquipmentRegister($model['uuid'],
                    EquipmentRegisterType::REGISTER_TYPE_CHANGE_PROPERTIES,
                    "Смена даты ввода в эксплуатацию на " . $model['inputDate']);
            }
            $model->save();
            return json_encode($model['inputDate']);
        }
        $searchModel = new EquipmentSearch();
        $dataProvider = $searchModel->search(Yii::$app->request->queryParams);
        $dataProvider->pagination->pageSize = 15;
        if (isset($_GET['start_time'])) {
            $dataProvider->query->andWhere(['>=', 'testDate', $_GET['start_time']]);
            $dataProvider->query->andWhere(['<', 'testDate', $_GET['end_time']]);
        }
        if (isset($_GET['address'])) {
            $dataProvider->query->andWhere(['or', ['like', 'house.number', '%' . $_GET['address'] . '%', false],
                    ['like', 'object.title', '%' . $_GET['address'] . '%', false],
                    ['like', 'street.title', '%' . $_GET['address'] . '%', false]]
            );
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
     * @return string
     * @throws Exception
     * @throws InvalidConfigException
     */
    public function actionMeasure()
    {
        $searchModel = new EquipmentSearch();
        $dataProvider = $searchModel->search(Yii::$app->request->queryParams);
        $dataProvider->query->andWhere(['IN', 'equipmentTypeUuid', [
            EquipmentType::EQUIPMENT_ELECTRICITY_COUNTER,
            EquipmentType::EQUIPMENT_HVS_COUNTER,
            EquipmentType::EQUIPMENT_HEAT_COUNTER
        ]]);
        if (isset($_GET['start_time'])) {
            $dataProvider->query->andWhere(['>=', 'testDate', $_GET['start_time']]);
            $dataProvider->query->andWhere(['<', 'testDate', $_GET['end_time']]);
        }
        if (isset($_GET['address'])) {
            $dataProvider->query->andWhere(['or', ['like', 'house.number', '%' . $_GET['address'] . '%', false],
                    ['like', 'object.title', '%' . $_GET['address'] . '%', false],
                    ['like', 'street.title', '%' . $_GET['address'] . '%', false]]
            );
        }
        if (isset($_GET['type']) && $_GET['type'] != '') {
            $dataProvider->query->andWhere(['=', 'equipmentTypeUuid', $_GET['type']]);
        }
        $dataProvider->pagination->pageSize = 150;

        return $this->render(
            'measure',
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
     * @throws Exception
     * @throws InvalidConfigException
     * @throws Exception
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
        $dataProvider->query->andWhere(['IN', 'equipmentTypeUuid', [
            EquipmentType::EQUIPMENT_ELECTRICITY_COUNTER,
            EquipmentType::EQUIPMENT_HVS_COUNTER,
            EquipmentType::EQUIPMENT_HEAT_COUNTER
        ]]);

        if (isset($_GET['start_time'])) {
            $dataProvider->query->andWhere(['>=', 'testDate', $_GET['start_time']]);
            $dataProvider->query->andWhere(['<', 'testDate', $_GET['end_time']]);
        }
        if (isset($_GET['address'])) {
            $dataProvider->query->andWhere(['or', ['like', 'house.number', '%' . $_GET['address'] . '%', false],
                    ['like', 'object.title', '%' . $_GET['address'] . '%', false],
                    ['like', 'street.title', '%' . $_GET['address'] . '%', false]]
            );
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
     * @throws Exception
     * @throws InvalidConfigException
     */
    public function actionCreate()
    {
        parent::actionCreate();

        $model = new Equipment();
        $tagTypeList = [
            Tag::TAG_TYPE_DUMMY => 'Пустая',
            Tag::TAG_TYPE_GRAPHIC_CODE => 'QR код',
            Tag::TAG_TYPE_NFC => 'NFC метка',
            Tag::TAG_TYPE_UHF => 'UHF метка'
        ];
        $tagType = new DynamicModel(['tagType']);
        $tagType->addRule(['tagType'], 'required');
        $tagType->addRule(['tagType'], 'in', ['range' => array_keys($tagTypeList)]);

        if ($model->load(Yii::$app->request->post()) && $tagType->load(Yii::$app->request->post())) {
            // проверяем все поля, если что-то не так показываем форму с ошибками
            if (!$model->validate() || !$tagType->validate()) {
                if (Yii::$app->request->isAjax) {
                    echo json_encode($model->errors);
                }

                return $this->render('create', [
                    'model' => $model,
                    'tagType' => $tagType,
                    'tagTypeList' => $tagTypeList,
                ]);
            }

            $model->tag = Tag::getTag($tagType->tagType, $model->tag);
            // сохраняем запись
            if ($model->save(false)) {
                MainFunctions::register('documentation', 'Добавлено оборудование',
                    '<a class="btn btn-default btn-xs">' . $model['equipmentType']['title'] . '</a> ' . $model['title'] . '<br/>' .
                    'Серийный номер <a class="btn btn-default btn-xs">' . $model['serial'] . '</a>', $model->uuid);
                EquipmentRegisterController::addEquipmentRegister($model['uuid'],
                    EquipmentRegisterType::REGISTER_TYPE_CHANGE_STATUS,
                    "Добавлено оборудование");
                $model->setNextDate();
                return $this->redirect(['view', 'id' => $model->_id]);
            }
            echo json_encode($model->errors);
        }

        return $this->render('create', [
            'model' => $model,
            'tagType' => $tagType,
            'tagTypeList' => $tagTypeList,
        ]);
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
        parent::actionUpdate($id);

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
     * @throws Exception
     * @throws InvalidConfigException
     * @throws Exception
     */
    public function actionTree()
    {
        $fullTree = array();
        $documentations = Documentation::find()->all();
        $userSystems = UserSystem::find()->all();
        $tasks = Task::find()->orderBy('changedAt DESC')->all();

        $systems = EquipmentSystem::find()
            ->orderBy('title')
            ->all();
        foreach ($systems as $system) {
            $fullTree['children'][] = [
                'title' => $system['title'],
                'address' => '',
                'uuid' => $system['uuid'],
                'type' => 'system',
                'key' => $system['_id'],
                'folder' => true,
                'expanded' => false
            ];
            $childIdx = count($fullTree['children']) - 1;
            $types = EquipmentType::find()
                ->where(['equipmentSystemUuid' => $system['uuid']])
                ->orderBy('title')
                ->all();
            foreach ($types as $type) {
                $fullTree['children'][$childIdx]['children'][] = [
                    'title' => $type['title'],
                    'address' => '',
                    'uuid' => $type['uuid'],
                    'type' => 'type',
                    'key' => $type['_id'],
                    'folder' => true,
                    'expanded' => false
                ];
                $childIdx2 = count($fullTree['children'][$childIdx]['children']) - 1;
                $equipments = Equipment::find()->where(['equipmentTypeUuid' => $type['uuid']])
                    ->andWhere(['deleted' => false])
                    ->all();
                foreach ($equipments as $equipment) {
                    $fullTree['children'][$childIdx]['children'][$childIdx2]['children'][] =
                        self::addEquipment($equipment, $documentations, $userSystems, $tasks, "../equipment/tree");
                }
            }
        }
        $users = Users::find()->all();
        $items = ArrayHelper::map($users, 'uuid', 'name');

        return $this->render(
            'tree',
            [
                'equipment' => $fullTree,
                'users' => $items
            ]
        );
    }

    /**
     * Build tree of equipment by user
     *
     * @param integer $id Id
     * @param $date_start
     * @param $date_end
     * @return mixed
     * @throws Exception
     * @throws InvalidConfigException
     * @throws Exception
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
                $flats = Objects::find()->select('uuid')
                    ->where(['houseUuid' => $user_house['houseUuid']])
                    ->andWhere(['deleted' => 0])
                    ->all();
                foreach ($flats as $flat) {
                    $equipment = Equipment::find()
                        ->select('*')
                        ->where(['flatUuid' => $flat['uuid']])
                        ->andWhere(['deleted' => false])
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
     * @throws Exception
     * @throws InvalidConfigException
     * @throws Exception
     */
    public function actionTreeUser()
    {
        ini_set('memory_limit', '-1');
        $documentations = Documentation::find()->all();
        $userSystems = UserSystem::find()->all();
        $tasks = Task::find()->orderBy('changedAt DESC')->all();

        $fullTree = array();
        $users = Users::find()
            ->select('_id,uuid,name')
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
                                self::addEquipment($equipment, $documentations, $userSystems, $tasks, "../equipment/tree");
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
     * @throws Exception
     * @throws InvalidConfigException
     * @throws Exception
     */
    public function actionTreeStreet()
    {
        ini_set('memory_limit', '-1');
        $documentations = Documentation::find()->all();
        $userSystems = UserSystem::find()->all();
        $tasks = Task::find()->orderBy('changedAt DESC')->all();

        $fullTree = array();
        $streets = Street::find()
            ->select('*')
            ->andWhere(['deleted' => false])
            ->orderBy('title')
            ->all();
        foreach ($streets as $street) {
            $fullTree['children'][] = [
                'title' => 'ул.' . $street['title'],
                'address' => $street['city']['title'] . ', ул.' . $street['title'],
                'uuid' => $street['uuid'],
                'type' => 'street',
                'key' => $street['_id'],
                'folder' => true,
                'expanded' => false
            ];
            $childIdx = count($fullTree['children']) - 1;
            $houses = House::find()->select('_id,uuid,number')->where(['streetUuid' => $street['uuid']])
                ->andWhere(['deleted' => false])
                ->orderBy('number')->all();
            foreach ($houses as $house) {
                //$user_house = UserHouse::find()->select('_id')->where(['houseUuid' => $house['uuid']])->one();
                $user = Users::find()->where(['uuid' =>
                    UserHouse::find()->where(['houseUuid' => $house['uuid']])->one()
                ])->one();
                if (!$user)
                    $user_name = $user['name'];
                else
                    $user_name = "";
                $docs = '';

                foreach ($documentations as $documentation) {
                    if ($documentation['houseUuid'] == $house['uuid']) {
                        $docs .= Html::a('<span class="glyphicon glyphicon-floppy-disk"></span>&nbsp',
                            [$documentation->getDocLocalPath()], ['title' => $documentation['title']]
                        );
                    }
                }
                $fullTree['children'][$childIdx]['children'][] =
                    [
                        'title' => $house['number'],
                        'address' => $street['title'] . ', ' . $house['number'],
                        'type' => 'house',
                        'expanded' => false,
                        'user' => $user_name,
                        'docs' => $docs,
                        'uuid' => $house['uuid'],
                        'key' => $house['_id'],
                        'folder' => true
                    ];
                $childIdx2 = count($fullTree['children'][$childIdx]['children']) - 1;
                $objects = Objects::find()->where(['houseUuid' => $house['uuid']])
                    ->andWhere(['deleted' => false])
                    ->all();
                foreach ($objects as $object) {
                    if ($object['objectTypeUuid'] == ObjectType::OBJECT_TYPE_FLAT)
                        $title = $object['objectType']['title'] . ' ' . $object['title'];
                    else
                        $title = $object['title'];
                    $fullTree['children'][$childIdx]['children'][$childIdx2]['children'][] =
                        [
                            'title' => $title,
                            'address' => $street['title'] . ', ' . $object['house']['number'] . ', ' . $object['title'],
                            'type' => 'object',
                            'uuid' => $object['uuid'],
                            'user' => $user_name,
                            'key' => $object['_id'] . "",
                            'expanded' => false,
                            'folder' => true
                        ];
                    $childIdx3 = count($fullTree['children'][$childIdx]['children'][$childIdx2]['children']) - 1;
                    $equipments = Equipment::find()->where(['objectUuid' => $object['uuid']])
                        ->andWhere(['deleted' => false])
                        ->all();
                    foreach ($equipments as $equipment) {
                        $fullTree['children'][$childIdx]['children'][$childIdx2]['children'][$childIdx3]['children'][] =
                            self::addEquipment($equipment, $documentations, $userSystems, $tasks, "../equipment/tree-street");
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
     * Deletes an existing Equipment model.
     * If deletion is successful, the browser will be redirected to the 'index' page.
     *
     * @param integer $id Id
     *
     * @return mixed
     * @throws NotFoundHttpException
     * @throws StaleObjectException
     * @throws Throwable
     */
    public function actionDelete($id)
    {
        parent::actionDelete($id);

        $equipment = $this->findModel($id);
        $photos = Photo::find()
            ->select('*')
            ->where(['objectUuid' => $equipment['uuid']])
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

        $equipment = $this->findModel($id);
        $equipment['deleted'] = true;
        $equipment->save();

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
     * функция отрабатывает сигналы от дерева и выполняет связывание выбранного оборудования с пользователем
     *
     * @return mixed
     * @throws StaleObjectException
     * @throws Throwable
     */
    public function actionMove()
    {
        if (isset($_POST["selected_node"])) {
            $node = $_POST["selected_node"];
            $user = $_POST["user"];
            if ($user && $node)
                $this->updateUserHouse($user, $node);
        }
        $this->enableCsrfValidation = false;
        return 0;
    }

    /**
     * @return int
     * @throws Exception
     * @throws InvalidConfigException
     */
    public function actionDeleted()
    {
        if (isset($_POST["type"]))
            $type = $_POST["type"];
        else $type = 0;

        if (isset($_POST["selected_node"]) && $type) {
            if ($type == 'street') {
                $street = Street::find()->where(['uuid' => $_POST["selected_node"]])->one();
                if ($street) {
                    $street['deleted'] = true;
                    $street->save();
                }
            }
            if ($type == 'house') {
                $house = House::find()->where(['uuid' => $_POST["selected_node"]])->one();
                if ($house) {
                    $house['deleted'] = true;
                    $house->save();
                }
            }
            if ($type == 'object') {
                $object = Objects::find()->where(['uuid' => $_POST["selected_node"]])->one();
                if ($object) {
                    $object['deleted'] = true;
                    $object->save();
                }
            }
            if ($type == 'equipment') {
                $equipment = Equipment::find()->where(['uuid' => $_POST["selected_node"]])->one();
                if ($equipment) {
                    $equipment['deleted'] = true;
                    $equipment->save();
                }
            }
        }
        $this->enableCsrfValidation = false;
        return 0;
    }

    /**
     * функция отрабатывает сигналы от дерева и выполняет отвязывание выбранного оборудования от пользователя
     * @return mixed
     * @throws StaleObjectException
     * @throws Throwable
     */
    public function actionRemove()
    {
        if (isset($_POST["selected_node"])) {
            $node = $_POST["selected_node"];
            if ($node)
                $this->updateUserHouse(null, $node);
        }
        $this->enableCsrfValidation = false;
        return 0;
    }

    /**
     * функция отрабатывает сигналы от дерева и выполняет переименование оборудования
     * @return mixed
     * @throws InvalidConfigException
     * @throws Exception
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
     * @throws Exception
     * @throws InvalidConfigException
     * @throws StaleObjectException
     * @throws Throwable
     */
    function updateUserHouse($user, $node)
    {
        $house = House::find()->where(['uuid' => $node])->one();
        if ($house['uuid']) {
            if (!$user) {
                $userHouse = UserHouse::find()->select('*')
                    ->where(['houseUuid' => $house['uuid']])
                    ->one();
                if ($userHouse) {
                    $userHouse->delete();
                    $this->redirect('tree-street');
                }
            } else {
                $userHouse = UserHouse::find()->select('*')
                    ->where(['houseUuid' => $house['uuid']])
                    ->andWhere(['userUuid' => $user])
                    ->one();
                if (!$userHouse) {
                    $userHouse = new UserHouse();
                    $userHouse->uuid = (new MainFunctions)->GUID();
                    $userHouse->houseUuid = $house['uuid'];
                    $userHouse->oid = Users::getCurrentOid();
                    $userHouse->userUuid = $user;
                    $userHouse->save();
                    $this->redirect('tree-street');
                }
            }
        }
    }

    /**
     * функция добавляет/отвязывает оборудование от пользователей
     *
     * @param $user
     * @param $node
     * @throws StaleObjectException
     * @throws Throwable
     */
    function addUserHouse($user, $node)
    {
        $house = House::find()->where(['uuid' => $node])->one();
        if ($house['uuid']) {
            if ($user) {
                $userHouse = UserHouse::find()->select('*')->where(['houseUuid' => $house['uuid']])
                    ->andWhere(['userUuid' => $user])
                    ->one();
                if (!$userHouse) {
                    $userHouse = new UserHouse();
                    $userHouse->uuid = (new MainFunctions)->GUID();
                    $userHouse->houseUuid = $house['uuid'];
                    $userHouse->userUuid = $user;
                    $userHouse->save();
                }
            } else {
                $userHouse = UserHouse::find()->select('*')
                    ->where(['houseUuid' => $house['uuid']])
                    ->one();
                if ($userHouse)
                    $userHouse->delete();
            }
        }
    }

    /**
     * @return bool|string
     * @throws Exception
     * @throws InvalidConfigException
     * @throws Exception
     */
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

    /**
     * @return bool|string
     * @throws Exception
     * @throws InvalidConfigException
     */
    public function actionMeasures()
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

    /**
     * @return bool|string
     * @throws Exception
     * @throws InvalidConfigException
     * @throws Exception
     */
    public function actionStatus()
    {
        if (isset($_GET["equipmentUuid"])) {
            $model = Equipment::find()->where(['uuid' => $_GET["equipmentUuid"]])
                ->one();
            return $this->renderAjax('_change_form', [
                'model' => $model,
                'source' => null,
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

    /**
     * @return bool|string
     * @throws Exception
     * @throws InvalidConfigException
     * @throws Exception
     */
    public function actionSerial()
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

    /**
     * @return string
     * @throws Exception
     * @throws InvalidConfigException
     */
    public function actionSelectTask()
    {
        if (isset($_GET["equipmentUuid"])) {
            $model = new Task();
            if (isset($_GET["defectUuid"])) {
                $defectUuid = $_GET["defectUuid"];
                $defect = Defect::find()->where(['uuid' => $defectUuid])->one();
                $defect['defectStatus'] = 1;
                $defect->save();
                $model->comment = 'Задача создана по дефекту ' . $defect['title'];
            } else
                $defectUuid = 0;
            return $this->renderAjax('_select_task', [
                'model' => $model,
                'equipmentUuid' => $_GET["equipmentUuid"],
                'defectUuid' => $defectUuid
            ]);
        }
        $model = new Task();
        return $this->renderAjax('_select_task', [
            'model' => $model
        ]);
    }

    /**
     * функция отрабатывает сигналы от дерева и выполняет добавление нового оборудования
     *
     * @return mixed
     */
    public function actionNew()
    {
        if (isset($_POST["selected_node"])) {
            $folder = $_POST["folder"];
            if (isset($_POST["uuid"]))
                $uuid = $_POST["uuid"];
            else $uuid = 0;
            if (isset($_POST["type"]))
                $type = $_POST["type"];
            else $type = 0;
            if (isset($_POST["source"]))
                $source = $_POST["source"];
            else $source = '../equipment/tree-street';

            if ($folder == "true" && $uuid && $type) {
                if ($type == 'street') {
                    $house = new House();
                    return $this->renderAjax('../object/_add_house_form', [
                        'streetUuid' => $uuid,
                        'house' => $house,
                        'source' => $source
                    ]);
                }
                if ($type == 'house') {
                    $object = new Objects();
                    return $this->renderAjax('../object/_add_object_form', [
                        'houseUuid' => $uuid,
                        'object' => $object,
                        'source' => $source
                    ]);
                }
                if ($type == 'object') {
                    $equipment = new Equipment();
                    $tagTypeList = [
                        Tag::TAG_TYPE_DUMMY => 'Пустая',
                        Tag::TAG_TYPE_GRAPHIC_CODE => 'QR код',
                        Tag::TAG_TYPE_NFC => 'NFC метка',
                        Tag::TAG_TYPE_UHF => 'UHF метка'
                    ];
                    $tagType = new DynamicModel(['tagType']);
                    $tagType->addRule(['tagType'], 'required');
                    $tagType->addRule(['tagType'], 'in', ['range' => array_keys($tagTypeList)]);
                    return $this->renderAjax('_add_form', [
                        'equipment' => $equipment,
                        'objectUuid' => $uuid,
                        'tagType' => $tagType,
                        'tagTypeList' => $tagTypeList,
                        'equipmentTypeUuid' => null,
                        'source' => '../equipment/tree-street'
                    ]);
                }
                if ($type == 'type') {
                    $equipment = new Equipment();
                    $tagTypeList = [
                        Tag::TAG_TYPE_DUMMY => 'Пустая',
                        Tag::TAG_TYPE_GRAPHIC_CODE => 'QR код',
                        Tag::TAG_TYPE_NFC => 'NFC метка',
                        Tag::TAG_TYPE_UHF => 'UHF метка'
                    ];
                    $tagType = new DynamicModel(['tagType']);
                    $tagType->addRule(['tagType'], 'required');
                    $tagType->addRule(['tagType'], 'in', ['range' => array_keys($tagTypeList)]);
                    return $this->renderAjax('_add_form', [
                        'equipment' => $equipment,
                        'objectUuid' => null,
                        'tagType' => $tagType,
                        'tagTypeList' => $tagTypeList,
                        'equipmentTypeUuid' => $uuid,
                        'source' => '../equipment/tree'
                    ]);
                }
            }
        }
        return 'Нельзя добавить объект в этом месте';
    }

    /**
     * функция отрабатывает сигналы от дерева и выполняет редактирование оборудования
     *
     * @return mixed
     * @throws Exception
     * @throws InvalidConfigException
     * @throws Exception
     */
    public function actionEdit()
    {
        if (isset($_POST["uuid"]))
            $uuid = $_POST["uuid"];
        else $uuid = 0;
        if (isset($_POST["type"]))
            $type = $_POST["type"];
        else $type = 0;
        if (isset($_POST["source"]))
            $source = $_POST["source"];
        else $source = '../equipment/tree-street';

        if ($uuid && $type) {
            if ($type == 'street') {
                $street = Street::find()->where(['uuid' => $uuid])->one();
                if ($street) {
                    return $this->renderAjax('../object/_add_street_form', [
                        'street' => $street,
                        'streetUuid' => $uuid,
                        'source' => $source
                    ]);
                }
            }
            if ($type == 'house') {
                $house = House::find()->where(['uuid' => $uuid])->one();
                if ($house) {
                    return $this->renderAjax('../object/_add_house_form', [
                        'houseUuid' => $uuid,
                        'house' => $house,
                        'source' => $source,
                        'streetUuid' => null,
                    ]);
                }
            }
            if ($type == 'object') {
                $object = Objects::find()->where(['uuid' => $uuid])->one();
                if ($object) {
                    return $this->renderAjax('../object/_add_object_form', [
                        'objectUuid' => $uuid,
                        'object' => $object,
                        'source' => $source,
                        'houseUuid' => null,
                    ]);
                }
            }
            if ($type == 'contragent') {
                $contragent = Contragent::find()->where(['uuid' => $uuid])->one();
                return $this->renderAjax('../object/_add_contragent_form', [
                    'contragentUuid' => $uuid,
                    'contragent' => $contragent,
                    'source' => $source,
                    'address' => null,
                    'objectUuid' => null,
                ]);
            }
            if ($type == 'equipment') {
                $equipment = Equipment::find()->where(['uuid' => $uuid])->one();
                $tagTypeList = [
                    Tag::TAG_TYPE_DUMMY => 'Пустая',
                    Tag::TAG_TYPE_GRAPHIC_CODE => 'QR код',
                    Tag::TAG_TYPE_NFC => 'NFC метка',
                    Tag::TAG_TYPE_UHF => 'UHF метка'
                ];
                $tagType = new DynamicModel(['tagType']);
                $tagType->addRule(['tagType'], 'required');
                $tagType->addRule(['tagType'], 'in', ['range' => array_keys($tagTypeList)]);

                return $this->renderAjax('../equipment/_add_form', [
                    'contragentUuid' => $uuid,
                    'equipment' => $equipment,
                    'tagType' => $tagType,
                    'tagTypeList' => $tagTypeList,
                    'reference' => '../equipment/tree-street',
                    'source' => $source,
                    'equipmentTypeUuid' => null,
                    'objectUuid' => null,
                ]);
            }
        }
        return "";
    }

    /**
     * Creates a new Equipment model.
     * @return mixed
     * @throws Exception
     * @throws InvalidConfigException
     * @throws Exception
     */
    public function actionSave()
    {
        if (isset($_POST["type"]))
            $type = $_POST["type"];
        else $type = 0;
        if (isset($_POST["source"]))
            $source = $_POST["source"];
        else $source = 0;

        if ($type) {
            if ($type == 'street') {
                if (isset($_POST['streetUuid'])) {
                    $model = Street::find()->where(['uuid' => $_POST['streetUuid']])->one();
                    if ($model->load(Yii::$app->request->post())) {
                        if ($model->save(false)) {
                            if ($source)
                                return $this->redirect([$source]);
                            return $this->redirect(['../equipment/tree-street']);
                        }
                    }
                }
            }
            if ($type == 'house') {
                if (isset($_POST['houseUuid']))
                    $model = House::find()->where(['uuid' => $_POST['houseUuid']])->one();
                else
                    $model = new House();
                if ($model->load(Yii::$app->request->post())) {
                    if ($model->save(false)) {
                        if ($source)
                            return $this->redirect([$source]);
                        return $this->redirect(['../equipment/tree-street']);
                    }
                }
            }
            if ($type == 'object') {
                if (isset($_POST['objectUuid']))
                    $model = Objects::find()->where(['uuid' => $_POST['objectUuid']])->one();
                else
                    $model = new Objects();
                if ($model->load(Yii::$app->request->post())) {
                    if ($model->save(false)) {
                        if ($source)
                            return $this->redirect([$source]);
                        return $this->redirect(['../equipment/tree-street']);
                    }
                }
            }
            if ($type == 'equipment') {
                $tagTypeList = [
                    Tag::TAG_TYPE_DUMMY => 'Пустая',
                    Tag::TAG_TYPE_GRAPHIC_CODE => 'QR код',
                    Tag::TAG_TYPE_NFC => 'NFC метка',
                    Tag::TAG_TYPE_UHF => 'UHF метка'
                ];
                $tagType = new DynamicModel(['tagType']);
                $tagType->addRule(['tagType'], 'required');
                $tagType->addRule(['tagType'], 'in', ['range' => array_keys($tagTypeList)]);

                if (isset($_POST['equipmentUuid'])) {
                    $model = Equipment::find()->where(['uuid' => $_POST['equipmentUuid']])->one();
                } else {
                    $model = new Equipment();
                }
                if ($model->load(Yii::$app->request->post()) && $tagType->load(Yii::$app->request->post())) {
                    $model->tag = Tag::getTag($tagType->tagType, $model->tag);
                    //public static function addEquipmentRegister($equipmentUuid, $registerTypeUuid, $description)
                    $changed_attributes = array_diff_assoc($model->getOldAttributes(), $model->getAttributes());
                    foreach ($changed_attributes as $field => $value) {
                        //адрес, завод.номер,статус, дата ввода в эксп.
                        if ($field == 'objectUuid') {
                            EquipmentRegisterController::addEquipmentRegister($model['uuid'],
                                EquipmentRegisterType::REGISTER_TYPE_CHANGE_PLACE,
                                "Элемент отнесли к " . $model['object']->getFullTitle());
                        }
                        if ($field == 'serial') {
                            EquipmentRegisterController::addEquipmentRegister($model['uuid'],
                                EquipmentRegisterType::REGISTER_TYPE_CHANGE_PROPERTIES,
                                "Сменили серийный номер на " . $value);
                        }
                        if ($field == 'equipmentStatusUuid') {
                            EquipmentRegisterController::addEquipmentRegister($model['uuid'],
                                EquipmentRegisterType::REGISTER_TYPE_CHANGE_STATUS,
                                "Смена статуса на " . $model['equipmentStatus']['title']);
                        }
                        if ($field == 'inputDate') {
                            EquipmentRegisterController::addEquipmentRegister($model['uuid'],
                                EquipmentRegisterType::REGISTER_TYPE_CHANGE_PROPERTIES,
                                "Смена даты ввода в эксплуатацию на " . $model['inputDate']);
                        }
                        if ($field == 'tag') {
                            EquipmentRegisterController::addEquipmentRegister($model['uuid'],
                                EquipmentRegisterType::REGISTER_TYPE_CHANGE_PROPERTIES,
                                "Смена тега на " . $model['tag']);
                        }
                        if ($field == 'period') {
                            EquipmentRegisterController::addEquipmentRegister($model['uuid'],
                                EquipmentRegisterType::REGISTER_TYPE_CHANGE_PROPERTIES,
                                "Смена периода поверки на " . $model['period']);
                        }
                        if ($field == 'testDate') {
                            EquipmentRegisterController::addEquipmentRegister($model['uuid'],
                                EquipmentRegisterType::REGISTER_TYPE_CHANGE_PROPERTIES,
                                "Смена даты поверки на " . $model['testDate']);
                        }
                        if ($field == 'replaceDate') {
                            EquipmentRegisterController::addEquipmentRegister($model['uuid'],
                                EquipmentRegisterType::REGISTER_TYPE_CHANGE_PROPERTIES,
                                "Смена даты замены на " . $model['replaceDate']);
                        }
                        if ($field == 'objectTypeUuid') {
                            EquipmentRegisterController::addEquipmentRegister($model['uuid'],
                                EquipmentRegisterType::REGISTER_TYPE_CHANGE_PROPERTIES,
                                "Смена типа элемента на " . $model['objectType']['title']);
                        }
                    }
                    if ($model->save(false) && isset($_POST['equipmentUuid'])) {
                        if ($source)
                            return $this->redirect([$source]);
                        return $this->redirect(['../equipment/tree-street']);
                    }
                }
            }
        }
        if ($source)
            return $this->redirect([$source]);
        return $this->redirect(['/object/tree-street']);
    }

    /**
     * @param Equipment $equipment
     * @param $documentations
     * @param $userSystems
     * @param $tasks
     * @param $source
     * @return array
     */
    public function addEquipment($equipment, &$documentations, &$userSystems, &$tasks, $source)
    {
        $count = 0;
        $equipmentSystemUuid = $equipment['equipmentType']['equipmentSystem']['uuid'];
        $equipmentUuid = $equipment->uuid;
        $equipmentTypeUuid = $equipment['equipmentTypeUuid'];
        $userEquipmentName = Html::a('<span class="glyphicon glyphicon-comment"></span>&nbsp',
            ['/request/form', 'equipmentUuid' => $equipmentUuid, 'source' => 'tree'],
            [
                'title' => 'Добавить заявку',
                'data-toggle' => 'modal',
                'data-target' => '#modalRequest',
            ]
        );
        foreach ($userSystems as $userSystem) {
            if ($userSystem['equipmentSystemUuid'] == $equipmentSystemUuid) {
                if ($count > 0) $userEquipmentName .= ', ';
                $userEquipmentName .= $userSystem['user']['name'];
                $count++;
            }
        }
        if ($count == 0) $userEquipmentName = '<div class="progress"><div class="critical5">не назначен</div></div>';

        $task_text = '<div class="progress"><div class="critical5">задач нет</div></div>';
        foreach ($tasks as $task) {
            if ($task['equipmentUuid'] == $equipmentUuid) {
                if (strlen($task['taskTemplate']->title) > 50)
                    $title = substr($task['taskTemplate']->title, 0, 50);
                else
                    $title = $task['taskTemplate']->title;
                $title = mb_convert_encoding($title, "UTF-8", "UTF-8");
                if ($task['workStatusUuid'] == WorkStatus::COMPLETE)
                    $task_text = '<div class="progress"><div class="critical3">' . $title . '</div></div>';
                else
                    $task_text = '<div class="progress"><div class="critical2">' . $title . '</div></div>';
                break;
            }
        }
        $task = Html::a($task_text,
            ['select-task', 'equipmentUuid' => $equipmentUuid, 'source' => $source],
            [
                'title' => 'Создать задачу обслуживания',
                'data-toggle' => 'modal',
                'data-target' => '#modalAddTask',
            ]
        );
        $status = MainFunctions::getColorLabelByStatus($equipment['equipmentStatus'], "equipment");
        $status = Html::a($status,
            ['/equipment/status', 'equipmentUuid' => $equipmentUuid, 'source' => $source],
            [
                'title' => 'Сменить статус',
                'data-toggle' => 'modal',
                'data-target' => '#modalStatus',
            ]
        );

        $docs = '';
        foreach ($documentations as $documentation) {
            if ($documentation['equipmentUuid'] == $equipmentUuid) {
                $docs .= Html::a('<span class="glyphicon glyphicon-floppy-disk"></span>&nbsp',
                    [$documentation->getDocLocalPath()], ['title' => $documentation['title']]
                );
            }
        }
        $links = Html::a('<span class="fa fa-exclamation-circle"></span>&nbsp',
            ['/defect/list', 'equipmentUuid' => $equipmentUuid],
            [
                'title' => 'Дефекты',
                'data-toggle' => 'modal',
                'data-target' => '#modalDefects',
            ]
        );
        /*
                $links .= Html::a('<span class="glyphicon glyphicon-briefcase"></span>&nbsp',
                    ['/equipment-register/form', 'equipmentUuid' => $equipment['uuid']],
                    [
                        'title' => 'Добавить запись',
                        'data-toggle' => 'modal',
                        'data-target' => '#modalChange',
                    ]
                );*/
        if ($equipmentTypeUuid == EquipmentType::EQUIPMENT_ELECTRICITY_COUNTER ||
            $equipmentTypeUuid == EquipmentType::EQUIPMENT_HVS_COUNTER ||
            $equipmentTypeUuid == EquipmentType::EQUIPMENT_HEAT_COUNTER) {
            $links .= Html::a('<span class="fa fa-line-chart"></span>&nbsp',
                ['/equipment/measures', 'equipmentUuid' => $equipmentUuid],
                [
                    'title' => 'Измерения',
                    'data-toggle' => 'modal',
                    'data-target' => '#modalMeasures',
                ]
            );
            $links .= Html::a('<span class="fa fa-plus-circle"></span>&nbsp',
                ['/measure/add', 'equipmentUuid' => $equipmentUuid, 'source' => $source],
                [
                    'title' => 'Добавить измерение',
                    'data-toggle' => 'modal',
                    'data-target' => '#modalMeasure',
                ]
            );
        }
        $links .= Html::a('<span class="fa fa-book"></span>&nbsp',
            ['/equipment-register/list', 'equipmentUuid' => $equipmentUuid],
            [
                'title' => 'Журнал событий',
                'data-toggle' => 'modal',
                'data-target' => '#modalRegister',
            ]
        );
        $links .= Html::a('<span class="fa fa-list"></span>&nbsp',
            ['/equipment/operations', 'equipmentUuid' => $equipmentUuid],
            [
                'title' => 'История работ',
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
            ['/equipment/serial', 'equipmentUuid' => $equipmentUuid],
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
            'type' => 'equipment',
            'uuid' => $equipmentUuid,
            'type_uuid' => $equipment['equipmentType']['uuid'],
            'docs' => $docs,
            'start' => "" . date_format(date_create($equipment['inputDate']), "d-m-Y"),
            'location' => $equipment->object->getFullTitle(),
            'tasks' => $task,
            'user' => $userEquipmentName,
            'links' => $links,
            'status' => $status];
    }

    /**
     * @return string
     * @throws Exception
     * @throws InvalidConfigException
     * @throws Exception
     */
    public function actionTimelineAll()
    {
        $events = [];
        $equipments = Equipment::find()->all();
        foreach ($equipments as $equipment) {
            $new_events = self::actionTimeline($equipment['uuid'], 0);
            foreach ($new_events as $new_event) {
                array_push($events, $new_event);
            }
            //echo json_encode($events);
        }
        $sort_events = MainFunctions::array_msort($events, ['date' => SORT_DESC]);
        //echo json_encode($events);

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
     * @throws Exception
     * @throws InvalidConfigException
     * @throws Exception
     */
    public function actionTimeline($uuid, $r)
    {
        $events = [];
        $tasks = Task::find()->where(['equipmentUuid' => $uuid])->orderBy('changedAt DESC')->all();
        foreach ($tasks as $task) {
            if ($task['workStatusUuid'] == WorkStatus::NEW) {
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
                    'events' => $sort_events,
                    'model' => null,
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
        if ($type == 'task')
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

    /**
     * функция отрабатывает сигналы от дерева и выполняет добавление нового оборудования
     *
     * @return mixed
     */
    public function actionAdd()
    {
        $source = '../equipment';
        $equipment = new Equipment();
        $tagTypeList = [
            Tag::TAG_TYPE_DUMMY => 'Пустая',
            Tag::TAG_TYPE_GRAPHIC_CODE => 'QR код',
            Tag::TAG_TYPE_NFC => 'NFC метка',
            Tag::TAG_TYPE_UHF => 'UHF метка'
        ];
        $tagType = new DynamicModel(['tagType']);
        $tagType->addRule(['tagType'], 'required');
        $tagType->addRule(['tagType'], 'in', ['range' => array_keys($tagTypeList)]);
        $tagType->setAttributes(['tagType' => Tag::getTagType($equipment->tag)]);
        return $this->renderAjax('_add_form', [
            'tagTypeList' => $tagTypeList,
            'tagType' => $tagType,
            'equipment' => $equipment,
            'type' => 'equipment',
            'source' => $source,
            'reference' => null,
            'equipmentTypeUuid' => null,
            'objectUuid' => null,
        ]);
    }

    /**
     * функция отрабатывает сигналы от дерева и выполняет добавление нового оборудования
     *
     * @return mixed
     * @throws Exception
     * @throws InvalidConfigException
     * @throws Exception
     */
    public function actionEditTable()
    {
        if (isset($_GET["uuid"]))
            $uuid = $_GET["uuid"];
        else $uuid = 0;

        $source = '../equipment';
        $equipment = Equipment::find()->where(['uuid' => $uuid])->one();

        $tagTypeList = [
            Tag::TAG_TYPE_DUMMY => 'Пустая',
            Tag::TAG_TYPE_GRAPHIC_CODE => 'QR код',
            Tag::TAG_TYPE_NFC => 'NFC метка',
            Tag::TAG_TYPE_UHF => 'UHF метка'
        ];
        $tagType = new DynamicModel(['tagType']);
        $tagType->addRule(['tagType'], 'required');
        $tagType->addRule(['tagType'], 'in', ['range' => array_keys($tagTypeList)]);
        $tagType->setAttributes(['tagType' => Tag::getTagType($equipment->tag)]);
        $equipment->tag = Tag::getTagId($equipment->tag);
        return $this->renderAjax('../equipment/_add_form', [
            'tagTypeList' => $tagTypeList,
            'tagType' => $tagType,
            'contragentUuid' => $uuid,
            'equipment' => $equipment,
            'reference' => '../equipment',
            'source' => $source,
            'equipmentTypeUuid' => null,
            'objectUuid' => null,
        ]);
    }
}

