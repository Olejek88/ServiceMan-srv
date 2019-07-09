<?php

namespace backend\controllers;

use backend\models\ObjectsSearch;
use common\components\MainFunctions;
use common\models\Contragent;
use common\models\Equipment;
use common\models\EquipmentStatus;
use common\models\EquipmentType;
use common\models\House;
use common\models\HouseType;
use common\models\ObjectContragent;
use common\models\Objects;
use common\models\ObjectStatus;
use common\models\ObjectType;
use common\models\Street;
use common\models\UserHouse;
use common\models\Users;
use Yii;
use yii\base\InvalidConfigException;
use yii\db\StaleObjectException;
use yii\web\NotFoundHttpException;
/**
 * ObjectController implements the CRUD actions for Object model.
 */
class ObjectController extends ZhkhController
{
    /**
     * Lists all Object models.
     * @return mixed
     * @throws InvalidConfigException
     */
    public function actionIndex()
    {
        return self::actionTable();
    }

    /**
     * Lists all Object models.
     * @return mixed
     * @throws InvalidConfigException
     */
    public function actionTable()
    {
        if (isset($_POST['editableAttribute'])) {
            $model = Objects::find()
                ->where(['_id' => $_POST['editableKey']])
                ->one();
            if ($_POST['editableAttribute'] == 'square') {
                $model['square'] = $_POST['Objects'][$_POST['editableIndex']]['square'];
            }
            if ($_POST['editableAttribute'] == 'objectTypeUuid') {
                $model['objectTypeUuid'] = $_POST['Objects'][$_POST['editableIndex']]['objectTypeUuid'];
            }
            if ($model->save())
                return json_encode('success');
            return json_encode('failed');
        }
        $searchModel = new ObjectsSearch();
        $dataProvider = $searchModel->search(Yii::$app->request->queryParams);
        $dataProvider->pagination->pageSize = 1200;

        return $this->render('table', [
            'searchModel' => $searchModel,
            'dataProvider' => $dataProvider,
        ]);
    }

    /**
     * Displays a single Object model.
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
     * Creates a new Flat model.
     * If creation is successful, the browser will be redirected to the 'view' page.
     * @return mixed
     * @throws InvalidConfigException
     */
    public function actionCreate()
    {
        parent::actionCreate();

        $model = new Objects();
        $searchModel = new ObjectsSearch();
        $dataProvider = $searchModel->search(Yii::$app->request->queryParams);
        $dataProvider->pagination->pageSize = 50;

        if ($model->load(Yii::$app->request->post()) && $model->save()) {
            $searchModel = new ObjectsSearch();
            $dataProvider = $searchModel->search(Yii::$app->request->queryParams);
            $dataProvider->pagination->pageSize = 15;
            //if ($_GET['from'])
            MainFunctions::register('object', 'Добавлен объект',
                '<a class="btn btn-default btn-xs">' . $model['objectType']['title'] . '</a> ' . $model['title'] . '<br/>' .
                '<a class="btn btn-default btn-xs">Адрес</a> ул.' . $model['house']['street']['title'] . ',д.' . $model['house']['number']);

            return $this->render('table', [
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
     * Updates an existing Object model.
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
     * Deletes an existing Object model.
     * If deletion is successful, the browser will be redirected to the 'table' page.
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

        return $this->redirect(['table']);
    }

    /**
     * Finds the Object model based on its primary key value.
     * If the model is not found, a 404 HTTP exception will be thrown.
     * @param integer $id
     * @return Objects
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
     * Build tree of equipment by user
     *
     * @return mixed
     * @throws InvalidConfigException
     */
    public function actionTree()
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
                'address' => $street['city']['title'] . ', ул.' . $street['title'],
                'type' => 'street',
                'source' => '../object/tree',
                'expanded' => false,
                'uuid' => $street['uuid'],
                'folder' => true
            ];
            $houses = House::find()->select('uuid, number')->where(['streetUuid' => $street['uuid']])->
            orderBy('number')->all();
            foreach ($houses as $house) {
                $user_house = UserHouse::find()->select('_id')->where(['houseUuid' => $house['uuid']])->one();
                $user = Users::find()->where(['uuid' =>
                    UserHouse::find()->where(['houseUuid' => $house['uuid']])->one()
                ])->one();
                $childIdx = count($fullTree['children']) - 1;
                $fullTree['children'][$childIdx]['children'][] = [
                    'title' => $house['number'],
                    'address' => $street['title'] . ', ' . $house['number'],
                    'type' => 'house',
                    'source' => '../object/tree',
                    'expanded' => false,
                    'uuid' => $house['uuid'],
                    'folder' => true
                ];
                $objects = Objects::find()->where(['houseUuid' => $house['uuid']])->all();
                foreach ($objects as $object) {
                    if (!$object['deleted']) {
                        if ($object['objectTypeUuid']==ObjectType::OBJECT_TYPE_FLAT)
                            $title = $object['objectType']['title'] . ' ' . $object['title'];
                        else
                            $title = $object['title'];
                        $childIdx2 = count($fullTree['children'][$childIdx]['children']) - 1;
                        $fullTree['children'][$childIdx]['children'][$childIdx2]['children'][] = [
                            'title' => $title,
                            'address' => $street['title'] . ', ' . $object['house']['number'] . ', ' . $object['title'],
                            'square' => $object['square'],
                            'source' => '../object/tree',
                            'type' => 'object',
                            'uuid' => $object['uuid'],
                            'folder' => true
                        ];
                        $objectContragents = ObjectContragent::find()->where(['objectUuid' => $object['uuid']])->all();
                        foreach ($objectContragents as $objectContragent) {
                            if (!$objectContragent['contragent']['deleted']) {
                                $childIdx3 = count($fullTree['children'][$childIdx]['children'][$childIdx2]['children']) - 1;
                                $fullTree['children'][$childIdx]['children'][$childIdx2]['children'][$childIdx3]['children'][] = [
                                    'title' => $objectContragent['contragent']['title'],
                                    'address' => $objectContragent['contragent']['address'],
                                    'inn' => $objectContragent['contragent']['inn'],
                                    'phone' => $objectContragent['contragent']['phone'],
                                    'email' => $objectContragent['contragent']['email'],
                                    'contragentType' => $objectContragent['contragent']['contragentType']['title'],
                                    'date' => $objectContragent['contragent']['createdAt'],
                                    'type' => 'contragent',
                                    'source' => '../object/tree',
                                    'uuid' => $objectContragent['contragent']['uuid'],
                                    'folder' => false
                                ];
                            }
                        }
                    }
                }
            }
        }
        return $this->render(
            'tree',
            ['contragents' => $fullTree]
        );
    }

    /**
     * функция отрабатывает сигналы от дерева и выполняет добавление нового оборудования или объекта
     *
     * @return mixed
     */
    public
    function actionNew()
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
            else $source = '../object/tree';

            if ($folder == "true" && $uuid && $type) {
                if ($type == 'street') {
                    $house = new House();
                    return $this->renderAjax('_add_house_form', [
                        'streetUuid' => $uuid,
                        'house' => $house,
                        'source' => $source
                    ]);
                }
                if ($type == 'house') {
                    $object = new Objects();
                    return $this->renderAjax('_add_object_form', [
                        'houseUuid' => $uuid,
                        'object' => $object,
                        'source' => $source
                    ]);
                }
                if ($type == 'object') {
                    $contragent = new Contragent();
                    return $this->renderAjax('_add_contragent_form', [
                        'objectUuid' => $uuid,
                        'contragent' => $contragent,
                        'source' => $source
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
     * @throws InvalidConfigException
     */
    public function actionEdit()
    {
        if (isset($_POST["selected_node"])) {
            if (isset($_POST["uuid"]))
                $uuid = $_POST["uuid"];
            else $uuid = 0;
            if (isset($_POST["type"]))
                $type = $_POST["type"];
            else $type = 0;

            if ($uuid && $type) {
                if ($type == 'street') {
                    $street = Street::find()->where(['uuid' => $uuid])->one();
                    if ($street) {
                        return $this->renderAjax('_add_street_form', [
                            'street' => $street,
                            'streetUuid' => $uuid
                        ]);
                    }
                }
                if ($type == 'house') {
                    $house = House::find()->where(['uuid' => $uuid])->one();
                    if ($house) {
                        return $this->renderAjax('_add_house_form', [
                            'houseUuid' => $uuid,
                            'house' => $house
                        ]);
                    }
                }

                if ($type == 'object') {
                    $object = Objects::find()->where(['uuid' => $uuid])->one();
                    if ($object) {
                        return $this->renderAjax('_add_object_form', [
                            'objectUuid' => $uuid,
                            'object' => $object
                        ]);
                    }
                }
                if ($type == 'contragent') {
                    $contragent = Contragent::find()->where(['uuid' => $uuid])->one();
                    return $this->renderAjax('_add_contragent_form', [
                        'contragentUuid' => $uuid,
                        'contragent' => $contragent
                    ]);
                }
            }
        }
        return 'Нельзя отредактировать этот объект';
    }

    /**
     * функция отрабатывает сигналы от дерева и выполняет удаление
     *
     * @return mixed
     * @throws StaleObjectException
     * @throws \Throwable
     */
    public function actionRemove()
    {
        if (isset($_POST["selected_node"])) {
            if (isset($_POST["uuid"]))
                $uuid = $_POST["uuid"];
            else $uuid = 0;
            if (isset($_POST["type"]))
                $type = $_POST["type"];
            else $type = 0;

            if ($uuid && $type) {
                if ($type == 'street') {
                    $street = Street::find()->where(['uuid' => $uuid])->one();
                    if ($street) {
                        $house = House::find()->where(['streetUuid' => $street['uuid']])->one();
                        if (!$house) {
                            $street->delete();
                        }
                    }
                }
                if ($type == 'house') {
                    $house = House::find()->where(['uuid' => $uuid])->one();
                    if ($house) {
                        $object = Objects::find()->where(['houseUuid' => $house['uuid']])->one();
                        if (!$object) {
                            $house->delete();
                        }
                    }
                }
                if ($type == 'object') {
                    $object = Objects::find()->where(['uuid' => $uuid])->one();
                    if ($object) {
                        $object['deleted'] = true;
                        $object->save();
                    }

                }

                if ($type == 'contragent') {
                    $contragent = Contragent::find()->where(['uuid' => $uuid])->one();
                    if ($contragent) {
                        $contragent['deleted'] = true;
                        $contragent->save();
                    }

                }
            }
        }
        return 'Нельзя удалить этот объект';
    }

    /**
     * Creates a new Object model.
     * @return mixed
     * @throws InvalidConfigException
     */
    public
    function actionSave()
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
                            return $this->redirect(['/object/tree']);
                        }
                    }
                }
            }
            if ($type == 'house') {
                $new = false;
                if (isset($_POST['houseUuid']))
                    $model = House::find()->where(['uuid' => $_POST['houseUuid']])->one();
                else {
                    $model = new House();
                    $new = true;
                }
                if ($model->load(Yii::$app->request->post())) {
                    if ($model->save(false)) {
                        if ($new && $model['houseTypeUuid']==HouseType::HOUSE_TYPE_MKD) {
                            if (isset($_POST['flats']) && $_POST['flats'] > 0) {
                                for ($flat_num=0; $flat_num<$_POST['flats']; $flat_num++) {
                                    $objectUuid = self::createFlat($model['uuid'],$flat_num+1);
                                    if (isset($_POST['balcony']) && $_POST['balcony'] && $objectUuid) {
                                        self::createEquipment($objectUuid, "Балкон",
                                            EquipmentType::EQUIPMENT_TYPE_BALCONY);
                                    }
                                }
                            }
                            $objectHUuid=null;
                            if (isset($_POST['water_system']) && $_POST['water_system']) {
                                $objectHUuid = self::createObject($model['uuid'], 'Система ХВС', ObjectType::OBJECT_TYPE_SYSTEM_HVS);
                                if ($objectHUuid) {
                                    self::createEquipment($objectHUuid, "Водомерный узел и розлив",
                                        EquipmentType::EQUIPMENT_HVS_MAIN);
                                    self::createEquipment($objectHUuid, "Насосная станция",
                                        EquipmentType::EQUIPMENT_HVS_PUMP);
                                    self::createEquipment($objectHUuid, "Водосчетчик ХВС",
                                        EquipmentType::EQUIPMENT_HVS_COUNTER);
                                }
                            }
                            $objectGUuid=null;
                            if (isset($_POST['water_system']) && $_POST['water_system']) {
                                $objectGUuid = self::createObject($model['uuid'], 'Система ГВС', ObjectType::OBJECT_TYPE_SYSTEM_GVS);
                                if ($objectGUuid) {
                                    self::createEquipment($objectGUuid, "Водомерный узел и розлив",
                                        EquipmentType::EQUIPMENT_GVS_MAIN);
                                    self::createEquipment($objectGUuid, "Насосная станция",
                                        EquipmentType::EQUIPMENT_GVS_PUMP);
                                }
                            }

                            $objectHeatUuid = self::createObject($model['uuid'], 'Система теплоснабжения',
                                ObjectType::OBJECT_TYPE_SYSTEM_HEAT);
                            if ($objectHeatUuid) {
                                self::createEquipment($objectHeatUuid, "Тепловой пункт и розливы",
                                    EquipmentType::EQUIPMENT_HEAT_MAIN);
                                self::createEquipment($objectHeatUuid, "Батареи в общих помещениях",
                                    EquipmentType::EQUIPMENT_HEAT_RADIATOR);
                                self::createEquipment($objectHeatUuid, "Теплосчетчик и КиП",
                                    EquipmentType::EQUIPMENT_HEAT_COUNTER);
                                self::createEquipment($objectHeatUuid, "Циркулярный насос теплоснабжения",
                                    EquipmentType::EQUIPMENT_HEAT_PUMP);
                                self::createEquipment($objectHeatUuid, "Стояки теплоснабжения",
                                    EquipmentType::EQUIPMENT_HEAT_TOWER);
                            }

                            $objectRoofUuid = self::createObject($model['uuid'], 'Кровля',
                                ObjectType::OBJECT_TYPE_SYSTEM_ROOF);
                            if ($objectRoofUuid) {
                                self::createEquipment($objectRoofUuid, "Кровля",
                                    EquipmentType::EQUIPMENT_ROOF);
                                self::createEquipment($objectRoofUuid, "Вход на крышу",
                                    EquipmentType::EQUIPMENT_ROOF_ENTRANCE);
                                self::createEquipment($objectRoofUuid, "Помещение крыши",
                                    EquipmentType::EQUIPMENT_ROOF_ROOM);
                                self::createEquipment($objectRoofUuid, "Система водоотвода",
                                    EquipmentType::EQUIPMENT_ROOF_WATER_PIPE);
                            }

                            $objectRoofUuid = self::createObject($model['uuid'], 'Внешний фасад',
                                ObjectType::OBJECT_TYPE_SYSTEM_WALL);
                            if ($objectRoofUuid) {
                                self::createEquipment($objectRoofUuid, "Стены, конструкции, перекрытия",
                                    EquipmentType::EQUIPMENT_WALL);
                                self::createEquipment($objectRoofUuid, "Водостоки",
                                    EquipmentType::EQUIPMENT_WALL_WATER);
                            }

                            if (isset($_POST['yard']) && $_POST['yard']) {
                                $objectYardUuid = self::createObject($model['uuid'], 'Придомовая территория',
                                    ObjectType::OBJECT_TYPE_SYSTEM_YARD);
                                if ($objectYardUuid) {
                                    self::createEquipment($objectYardUuid, "Дворовая территория",
                                        EquipmentType::EQUIPMENT_YARD);
                                    self::createEquipment($objectYardUuid, "Дренажная система",
                                        EquipmentType::EQUIPMENT_YARD_DRENAGE);
                                    self::createEquipment($objectYardUuid, "Площадки для ТБО",
                                        EquipmentType::EQUIPMENT_YARD_TBO);
                                }
                            }

                            $objectSewerUuid = self::createObject($model['uuid'], 'Канализация',
                                ObjectType::OBJECT_TYPE_SYSTEM_SEWER);
                            if ($objectSewerUuid) {
                                self::createEquipment($objectSewerUuid, "Стояки канализация",
                                    EquipmentType::EQUIPMENT_SEWER_PIPE);
                                self::createEquipment($objectSewerUuid, "Основной узел",
                                    EquipmentType::EQUIPMENT_SEWER_MAIN);
                                self::createEquipment($objectSewerUuid, "Колодец канализации",
                                    EquipmentType::EQUIPMENT_SEWER_WELL);
                            }

                            $objectPowerUuid = self::createObject($model['uuid'], 'Электричество',
                                ObjectType::OBJECT_TYPE_SYSTEM_ELECTRO);
                            if ($objectPowerUuid) {
                                self::createEquipment($objectPowerUuid, "Счетчик электроэнергии",
                                    EquipmentType::EQUIPMENT_ELECTRICITY_COUNTER);
                                self::createEquipment($objectPowerUuid, "ВРУ",
                                    EquipmentType::EQUIPMENT_ELECTRICITY_VRU);
                                self::createEquipment($objectPowerUuid, "Освещение",
                                    EquipmentType::EQUIPMENT_ELECTRICITY_LIGHT);
                            }

                            $objectMediaUuid = self::createObject($model['uuid'], 'Остальные системы',
                                ObjectType::OBJECT_TYPE_SYSTEM_ELECTRO);
                            if ($objectMediaUuid) {
                                if (isset($_POST['internet']) && $_POST['internet']) {
                                    self::createEquipment($objectMediaUuid, "Интернет коммуникации",
                                        EquipmentType::EQUIPMENT_INTERNET);
                                }
                                if (isset($_POST['domophones']) && $_POST['domophones']) {
                                    self::createEquipment($objectMediaUuid, "Домофоны",
                                        EquipmentType::EQUIPMENT_DOMOPHONE);
                                }
                                if (isset($_POST['tv']) && $_POST['tv']) {
                                    self::createEquipment($objectMediaUuid, "Телевидение",
                                        EquipmentType::EQUIPMENT_TV);
                                }
                            }

                            $objectConditionerUuid = self::createObject($model['uuid'], 'Вентиляция и дымоудаление',
                                ObjectType::OBJECT_TYPE_SYSTEM_SMOKE);
                            if ($objectConditionerUuid) {
                                self::createEquipment($objectConditionerUuid, "Вентиляционные каналы",
                                    EquipmentType::EQUIPMENT_CONDITIONER);
                            }

                            if (isset($_POST['energy']) && $_POST['energy'] == 'Газ') {
                                $objectGasUuid = self::createObject($model['uuid'], 'Газоснабжение',
                                    ObjectType::OBJECT_TYPE_SYSTEM_GAS);
                                if ($objectGasUuid) {
                                    self::createEquipment($objectGasUuid, "Газовое оборудование",
                                        EquipmentType::EQUIPMENT_GAS);
                                }
                            }

                            $objectBasementUuid = self::createObject($model['uuid'], 'Подвал',
                                ObjectType::OBJECT_TYPE_SYSTEM_BASEMENT);
                            if ($objectBasementUuid) {
                                self::createEquipment($objectBasementUuid, "Окна",
                                    EquipmentType::EQUIPMENT_BASEMENT_WINDOWS);
                                self::createEquipment($objectBasementUuid, "Помещение",
                                    EquipmentType::EQUIPMENT_BASEMENT_ROOM);
                                self::createEquipment($objectBasementUuid, "Фундамент",
                                    EquipmentType::EQUIPMENT_BASEMENT);
                            }

                            if ((isset($_POST['stages']) && $_POST['stages'] > 0) &&
                                (isset($_POST['entrances']) && $_POST['entrances'] > 0)) {
                                for ($entrances_num=0; $entrances_num<$_POST['entrances']; $entrances_num++) {
                                    $objectEntranceUuid = self::createObject($model['uuid'], 'Подъезд №'.($entrances_num+1),
                                        ObjectType::OBJECT_TYPE_SYSTEM_ENTRANCE);
                                    if ($objectPowerUuid) {
                                        self::createEquipment($objectPowerUuid, "Осветительные приборы входной группы",
                                            EquipmentType::EQUIPMENT_ELECTRICITY_ENTRANCE_LIGHT);
                                        self::createEquipment($objectPowerUuid, "Стояки с проводкой",
                                            EquipmentType::EQUIPMENT_ELECTRICITY_ENTRANCE_PIPE);
                                    }

                                    if ($objectEntranceUuid) {
                                        self::createEquipment($objectEntranceUuid, "Окна",
                                            EquipmentType::EQUIPMENT_ENTRANCE_WINDOWS);
                                        self::createEquipment($objectEntranceUuid, "Дверь подъезда",
                                            EquipmentType::EQUIPMENT_ENTRANCE_DOOR);
                                        self::createEquipment($objectEntranceUuid, "Дверь тамбура",
                                            EquipmentType::EQUIPMENT_ENTRANCE_DOOR_TAMBUR);
                                        self::createEquipment($objectEntranceUuid, "Мусоропровод",
                                            EquipmentType::EQUIPMENT_ENTRANCE_TRASH_PIPE);
                                        self::createEquipment($objectEntranceUuid, "Лестничная клетка",
                                            EquipmentType::EQUIPMENT_ENTRANCE_STAIRS);
                                        self::createEquipment($objectEntranceUuid, "Входная группа",
                                            EquipmentType::EQUIPMENT_ENTRANCE_MAIN);
                                        if (isset($_POST['lift']) && $_POST['lift']) {
                                            self::createEquipment($objectEntranceUuid, "Лифт",
                                                EquipmentType::EQUIPMENT_LIFT);
                                        }
                                    }
                                    if ($objectHUuid) {
                                        self::createEquipment($objectHUuid, "Стояки ХВС",
                                            EquipmentType::EQUIPMENT_HVS_TOWER);
                                    }
                                    if ($objectGUuid) {
                                        self::createEquipment($objectGUuid, "Стояки ГВС",
                                            EquipmentType::EQUIPMENT_GVS_TOWER);
                                    }
                                    for ($stages_num=0; $stages_num<$_POST['stages']; $stages_num++) {
                                        if ($objectPowerUuid) {
                                            self::createEquipment($objectPowerUuid,
                                                "Щиток электрический (п.".($entrances_num+1)." - э.".($stages_num+1).")",
                                                EquipmentType::EQUIPMENT_ELECTRICITY_LEVEL_SHIELD);
                                        }
                                    }
                                }
                            }
                        }

                        if ($source)
                            return $this->redirect([$source]);
                        return $this->redirect(['/object/tree']);
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
                        return $this->redirect(['/object/tree']);
                    }
                }
            }
            if ($type == 'contragent') {
                if (isset($_POST['contragentUuid']))
                    $model = Contragent::find()->where(['uuid' => $_POST['contragentUuid']])->one();
                else
                    $model = new Contragent();
                if ($model->load(Yii::$app->request->post())) {
                    if ($model->save(false) && isset($_POST['objectUuid'])) {
                        $objectContragent = new ObjectContragent();
                        $objectContragent->contragentUuid = $model['uuid'];
                        $objectContragent->uuid = MainFunctions::GUID();
                        $objectContragent->oid = Users::getCurrentOid();
                        $objectContragent->objectUuid = $_POST['objectUuid'];
                        $objectContragent->save();
                        if ($source)
                            return $this->redirect([$source]);
                        return $this->redirect(['/object/tree']);
                    }
                }
            }
        }
/*        if ($source)
            return $this->redirect([$source]);
        return $this->redirect(['/object/tree']);*/
    }

    function createFlat($houseUuid, $flat_num) {
        $object = new Objects();
        $object->uuid = MainFunctions::GUID();
        $object->title = $flat_num;
        $object->oid = Users::getCurrentOid();
        $object->houseUuid = $houseUuid;
        $object->square = 33;
        $object->objectStatusUuid = ObjectStatus::OBJECT_STATUS_OK;
        $object->objectTypeUuid = ObjectType::OBJECT_TYPE_FLAT;
        $object->save();
        if ($object->errors==[])
            return $object->uuid;
        else
            return null;
    }

    function createObject($houseUuid, $name, $objectTypeUuid) {
        $object = new Objects();
        $object->uuid = MainFunctions::GUID();
        $object->title = $name;
        $object->oid = Users::getCurrentOid();
        $object->houseUuid = $houseUuid;
        $object->square = 0;
        $object->objectStatusUuid = ObjectStatus::OBJECT_STATUS_OK;
        $object->objectTypeUuid = $objectTypeUuid;
        $object->save();
        if ($object->errors==[])
            return $object->uuid;
        else
            return null;
    }

    function createEquipment($objectUuid, $name, $equipmentTypeUuid) {
        $equipment = new Equipment();
        $equipment->uuid = MainFunctions::GUID();
        $equipment->title = $name;
        $equipment->oid = Users::getCurrentOid();
        $equipment->objectUuid = $objectUuid;
        $equipment->equipmentStatusUuid = EquipmentStatus::WORK;
        $equipment->equipmentTypeUuid = $equipmentTypeUuid;
        $equipment->tag = $equipment->uuid;
        $equipment->period = 0;
        $equipment->replaceDate = date("Y-m-d H:i:s");
        $equipment->inputDate = date("Y-m-d H:i:s");
        $equipment->testDate = date("Y-m-d H:i:s");
        $equipment->deleted = 0;
        $equipment->serial = "-";
        $equipment->save();
        if ($equipment->errors==[])
            return $equipment->uuid;
        else {
            echo json_encode($equipment->errors);
            return null;
        }
    }
}
