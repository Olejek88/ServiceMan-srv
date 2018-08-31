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

use common\components\MainFunctions;
use common\components\MyHelpers;
use common\models\City;
use common\models\EquipmentRegister;
use common\models\EquipmentType;
use common\models\ExternalEvent;
use common\models\Flat;
use common\models\Measure;
use common\models\Message;
use common\models\ObjectType;
use common\models\Operation;
use common\models\Resident;
use common\models\Service;
use common\models\Stage;
use common\models\Street;
use common\models\Subject;
use common\models\UsersAttribute;
use Yii;
use yii\helpers\Html;
use yii\web\Controller;
use yii\filters\VerbFilter;
use yii\filters\AccessControl;
use yii\helpers\ArrayHelper;
use yii\web\HttpException;

use yii2fullcalendar\models\Event;
use common\models\LoginForm;
use common\models\User;
use common\models\Users;
use common\models\Journal;
use common\models\JournalUser;
use common\models\Defect;
use common\models\Equipment;
use common\models\EquipmentModel;
use common\models\EquipmentStatus;
use common\models\CriticalType;
use common\models\Task;
use common\models\Orders;
use common\models\Objects;
use common\models\Gpstrack;
use common\models\OrderStatus;
use common\models\TaskStatus;
use common\models\StageStatus;
use common\models\OperationStatus;
use common\models\OrderVerdict;
use backend\models\UsersSearch;

/**
 * Site controller
 *
 * @category Category
 * @package  Backend\controllers
 * @author   Максим Шумаков <ms.profile.d@gmail.com>
 * @license  http://www.yiiframework.com/license/ License name
 * @link     http://www.toirus.ru
 */
class SiteController extends Controller
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
            'access' => [
                'class' => AccessControl::className(),
                'rules' => [
                    [
                        'actions' => ['login', 'error'],
                        'allow' => true,
                    ],
                    [
                        'actions' => ['signup', 'error'],
                        'allow' => true,
                    ],
                    [
                        'actions' => ['logout', 'index', 'dashboard', 'test', 'timeline'],
                        'allow' => true,
                        'roles' => ['@'],
                    ],
                ],
            ],
            'verbs' => [
                'class' => VerbFilter::className(),
                'actions' => [
                    'logout' => ['post'],
                ],
            ],
        ];
    }

    /**
     * Actions
     *
     * @return array
     */
    public function actions()
    {
        $actions = parent::actions();
        unset($actions['error']);
        return $actions;
    }

    /**
     * Displays homepage.
     *
     * @return string
     */
    public function actionIndex()
    {
        /**
         * Работа с картой
         * [$online, $offline, $gpsOn, $gps description]
         *
         * @var $online - Список пользователей активных в течении 5 минут
         * @var $offline - Список пользователей не активных в течении 5 минут
         * @var $gpsOn - Список геоданных по онлайн пользователям
         * @var $gps - Список геоданных по оффлайн пользователям
         */
        $userData = array();
        $lats = array();
        $online = [];
        $offline = [];
        $wayUsers = [];
        $gps = 0;
        $gps2 = 0;
        $gpsStatus = false;


        $users = Users::find()->select('*')->all();
        $userList[] = $users;

        /**
         * [userList description]
         *
         * @var $userList - Список активных пользователей за сутки
         * @var $uuid - Uuid пользователя
         * @var $connectionDate - Дата последнего соединения
         */
        $today = time();
        $threshold = $today - 300000000;
        $count = 0;
        foreach ($users as $current_user) {
            if (strtotime($current_user['connectionDate']) >= $threshold) {
                $online[count($online)] = $current_user['uuid'];
            } else {
                $offline[count($offline)] = $current_user['uuid'];
            }

            $gps = Gpstrack::find()
                ->select('latitude, longitude, date')
                ->orderBy('date DESC')
                ->where(['userUuid' => $current_user['uuid']])
                ->one();
            if ($gps) {
                $userData[$count]['latitude'] = $gps['latitude'];
                $userData[$count]['longitude'] = $gps['longitude'];
            } else {
                $userData[$count]['latitude'] = 0;
                $userData[$count]['longitude'] = 0;
            }

            $userData[$count]['_id'] = $current_user['_id'];
            $userData[$count]['name'] = $current_user['name'];
            $userData[$count]['whois'] = $current_user['whoIs'];
            $userData[$count]['contact'] = $current_user['contact'];

            $gps = Gpstrack::find()
                ->select('latitude, longitude, date')
                ->orderBy('date DESC')
                ->where(['userUuid' => $current_user['uuid']])
                ->limit(30000)
                ->all();
            if ($gps) {
                $lats[$count] = $gps;
            } else {
                $lats[$count] = [];
            }

            $count++;
        }
        $allEquipment = Equipment::find()->all();
        if (count($online) >= 1) {
            /*$listOnline = count($online) - 1;
            $gpsOn = Gpstrack::find()
                ->select('userUuid, latitude, longitude, date')
                ->where(['userUuid' => $online[$listOnline]])
                ->asArray()
                ->limit(30000)
                ->all();*/
            $gpsStatus = true;
        }

        if (count($userList) >= 1) {
            // В случаи, если геоданные не были отправлены,
            // ответ на запрос будет null
            $gps = Gpstrack::find()
                ->select('userUuid, latitude, longitude, date')
                ->where('date  >= CURDATE()')
                ->orderBy('date DESC')
                ->asArray()
                ->limit(30000)
                ->all();
            $gpsStatus = true;
        }

        if (!$gpsStatus) {
            $gps = Gpstrack::find()->orderBy('date DESC')->asArray()->one();
            var_dump($gps);
        }
        /**
         * Настройки - История активности
         */

        $accountUser = Yii::$app->user->identity;

        $journalUserId = JournalUser::find()
            ->where(['userId' => $accountUser['id']])
            ->orderBy('_id DESC')
            ->asArray()
            ->all();

        // TODO: нужно заменть email на username т.к. у пользователей нет email
        $userJournal = User::find()
            ->select('id, email')
            ->where(['email' => $accountUser['email']])
            ->asArray()
            ->one();
        Yii::$app->view->params['user'] = $userJournal;


        //if (!empty(is_array($journalUserId))) {
        foreach ($journalUserId as $key => $value) {
            if ($journalUserId[$key]['userId'] === $userJournal['id']) {
                $journalUserId[$key]['userId'] = $userJournal['email'];
            }
        }
        //}

        /**
         * Наряды
         */

        $query = Orders::find()
            ->select(
                '_id,
                uuid,
                title,
                orderStatusUuid,
                orderVerdictUuid,
                createdAt,
                changedAt,
                closeDate'
            );

        $queryStatus = $query->where(
            'orderStatusUuid != :status',
            ['status' => '53238221-0EF7-4737-975E-FD49AFC92A05']
        )
            ->asArray()
            ->all();

        $queryActive = $query->where('closeDate >= CURDATE()')->all();

        $queryResult = array_merge($queryStatus, $queryActive);


        $orderStatus = OrderStatus::find()
            ->select('_id, uuid, title')
            ->asArray()
            ->all();

        $orderVerdict = OrderVerdict::find()
            ->select('uuid, title')
            ->asArray()
            ->all();

        $colorResult = ['#FDF8E7', '#DEF0F8', '#88BC8E', '#FF4A03', '#ccc'];

        foreach ($queryResult as $i => $result) {
            foreach ($orderStatus as $k => $status) {
                if ($queryResult[$i]['orderStatusUuid'] === $orderStatus[$k]['uuid']) {
                    $queryResult[$i]['orderStatusUuid'] = $orderStatus[$k]['title'];
                    $queryResult[$i]['color'] = $colorResult[$k];
                    $queryResult[$i]['flow'] = $k;
                }
            }

            foreach ($orderVerdict as $l => $verdict) {
                if ($queryResult[$i]['orderVerdictUuid'] === $orderVerdict[$l]['uuid']) {
                    $queryResult[$i]['orderVerdictUuid'] = $orderVerdict[$l]['title'];
                }
            }

            if ($queryResult[$i]['closeDate'] === '0000-00-00 00:00:00') {
                $queryResult[$i]['closeDate'] = '';
            }

            ArrayHelper::multisort($queryResult, ['flow'], [SORT_DESC]);
        }

        /**
         * Объекты
         */
        $objectSelect = Objects::find()
            ->select('_id, title, latitude, longitude, description')
            ->asArray()
            ->all();

        /**
         * Оборудование
         *
         * @var $criticalVery - Массив очень критичных оборудований
         */
        $criticalVery = [];
        $taskModel = [];
        $countTask = [];
        $equipmentList = [];
        //$equipmentIndex = [];

        $tasks = Task::find()
            ->select('uuid, comment');

        $equipmentSelect = Equipment::find()
            ->select(
                '_id,
                uuid,
                title,
                image,
                equipmentModelUuid,
                equipmentStatusUuid,
                criticalTypeUuid,
                createdAt,
                changedAt'
            );

        foreach ($queryResult as $i => $value) {
            $taskModel[] = $tasks->where(['orderUuid' => $queryResult[$i]['uuid']])
                ->asArray()
                ->all();

            $countTask[] = count($taskModel[$i]);
        }


        $criticalType = CriticalType::find()
            ->select('uuid, title')
            ->asArray()
            ->all();

        $equipmentModel = EquipmentModel::find()
            ->select('uuid, title')
            ->all();

        $equipmentStatus = EquipmentStatus::find()
            ->select('uuid, title')
            ->all();

        //$criticalUuid = $criticalType['0']['uuid'];

        foreach ($countTask as $index => $value) {

            if (!empty($taskModel[$index][$value - 1]['equipmentUuid'])) {
                $equipmentList[] = $equipmentSelect
                    ->where(
                        ['uuid' => $taskModel[$index][$value - 1]['equipmentUuid']]
                    )
                    ->asArray()
                    ->all();
                //$equipmentIndex[] = count($equipmentList[$index]);
            }
            /*
            foreach ($equipmentIndex as $key => $eqIndex) {
                if ($equipmentList[$key][$eqIndex - 1]['criticalTypeUuid'] === $criticalUuid) {
                    $criticalVery[] = $equipmentList[$key][$eqIndex - 1];
                }
            }*/
        }

        $criticalVery = array_map(
            "unserialize", array_unique(array_map("serialize", $criticalVery))
        );

        /**
         * Формирование ссылки для запроса на изображение
         *
         * @var [type]
         */
        foreach ($criticalVery as $key => $value) {
            $tmpPath = '/' . $criticalVery[$key]['equipmentModelUuid'] .
                '/' . $criticalVery[$key]['image'];
            $criticalVery[$key]['image'] = MyHelpers::getImgUrl($tmpPath);
        }

        foreach ($criticalVery as $i => $value) {
            foreach ($equipmentStatus as $l => $eqStatus) {
                if ($criticalVery[$i]['equipmentStatusUuid'] === $equipmentStatus[$l]['uuid']) {
                    $criticalVery[$i]['equipmentStatusUuid'] = $equipmentStatus[$l]['title'];
                }
            }

            foreach ($equipmentModel as $l => $eqModel) {
                if ($criticalVery[$i]['equipmentModelUuid'] === $equipmentModel[$l]['uuid']) {
                    $criticalVery[$i]['equipmentModelUuid'] = $equipmentModel[$l]['title'];
                }
            }

            foreach ($criticalType as $l => $crType) {
                if ($criticalVery[$i]['criticalTypeUuid'] === $criticalType[$l]['uuid']) {
                    $criticalVery[$i]['criticalTypeUuid'] = $criticalType[$l]['title'];
                }
            }
        }

        /**
         * Журнал событий
         */

        // В случаи, если геоданные не были отправлены, ответ на запрос будет null
        $journal = Journal::find()
            ->select('userUuid, description, date')
            ->where('date  >= NOW() - INTERVAL 1 DAY')
            ->asArray()
            ->all();

        $userUuid = Users::find()
            ->select('uuid, name')
            ->asArray()
            ->all();

        // $userUuid   = array_map("unserialize", array_unique(array_map("serialize", $userUuid)));

        foreach ($userUuid as $i => $user) {
            foreach ($journal as $j => $jrnl) {
                if ($userUuid[$i]['uuid'] === $journal[$j]['userUuid']) {
                    $journal[$j]['userUuid'] = $userUuid[$i]['name'];
                }
            }
        }

        $journal = array_map(
            "unserialize", array_unique(array_map("serialize", $journal))
        );
        $journal = array_reverse($journal);

        $cnt = 0;
        $objectsGroup = 'var objects=L.layerGroup([';
        $objectsList = '';
        foreach ($objectSelect as $object) {
            $objectsList .= 'var object' . $object["_id"]
                . '= L.marker([' . $object["latitude"]
                . ',' . $object["longitude"] . ']).bindPopup("<b>'
                . $object["title"] . '</b><br/>' . $object["description"]
                . '").openPopup();';
            if ($cnt > 0) {
                $objectsGroup .= ',';
            }

            $objectsGroup .= 'object' . $object["_id"];
            $cnt++;
        }

        $objectsGroup .= ']);' . PHP_EOL;

        $cnt = 0;
        $usersGroup = 'var users=L.layerGroup([';
        $usersList = '';
        foreach ($userData as $user) {
            $usersList .= 'var user' . $user["_id"]
                . '= L.marker([' . $user["latitude"]
                . ',' . $user["longitude"]
                . '], {icon: userIcon}).bindPopup("<b>'
                . $user["name"] . '</b><br/>'
                . $user["whois"] . ' ' . $user["contact"] . '").openPopup();';
            if ($cnt > 0) {
                $usersGroup .= ',';
            }

            $usersGroup .= 'user' . $user["_id"];
            $cnt++;
        }
        $usersGroup .= ']);' . PHP_EOL;

        $cnt = 0;
        $equipmentsGroup = 'var equipments=L.layerGroup([';
        $equipmentsList = '';
        foreach ($allEquipment as $equipment) {
            if ($equipment["latitude"] > 0) {
                $equipmentsList .= 'var equipment'
                    . $equipment["_id"]
                    . '= L.marker([' . $equipment["latitude"]
                    . ',' . $equipment["longitude"]
                    . '], {icon: equipmentIcon}).bindPopup("<b>'
                    . $equipment["title"] . '</b><br/>'
                    . $equipment["tagId"] . '").openPopup();';
                if ($cnt > 0) {
                    $equipmentsGroup .= ',';
                }

                $equipmentsGroup .= 'equipment' . $equipment["_id"];
                $cnt++;
            }
        }

        $equipmentsGroup .= ']);' . PHP_EOL;

        $ways = 'var lat;' . PHP_EOL;
        $cnt = 0;
        $ways .= 'var ways=L.layerGroup();' . PHP_EOL;
        foreach ($userData as $user) {
            $wayUsers[$cnt] = 'var wayUser' . $user['_id'] . '=L.layerGroup();' . PHP_EOL;
            //$way = 'lat = []' . PHP_EOL;
            if (count($lats[$cnt]) > 0) {
                $way = 'lat = [';
                foreach ($lats[$cnt] as $lat) {
                    $way .= '[' . $lat["latitude"] . ',' . $lat["longitude"] . '],';
                }
                $way .= '];' . PHP_EOL;
                $ways .= $way;
                $color = MainFunctions::random_color();
                $ways .= 'var way = L.polyline(lat, {color: "#'
                    . $color . '"});' . PHP_EOL;
                $wayUsers[$cnt] .= $way;
                $wayUsers[$cnt] .= 'var wayUser = L.polyline(lat, {color: "#'
                    . $color . '"});' . PHP_EOL;
                $ways .= 'ways.addLayer(way);' . PHP_EOL;
                $wayUsers[$cnt] .= 'wayUser' . $user['_id'] . '.addLayer(wayUser);'
                    . PHP_EOL;
            }
            $cnt++;
        }

        return $this->render(
            'index',
            [
                'users' => $userData,
                'objectsGroup' => $objectsGroup,
                'objectsList' => $objectsList,
                'usersGroup' => $usersGroup,
                'usersList' => $usersList,
                'equipmentsGroup' => $equipmentsGroup,
                'equipmentsList' => $equipmentsList,
                'ways' => $ways,
                'wayUsers' => $wayUsers,
                'lats' => $lats,
                'gps' => $gps,
                'gps2' => $gps2,
                'objects' => $objectSelect,
                'equipments' => $allEquipment,
                'orders' => $queryResult,
                'equipment' => $criticalVery,
                'journal' => $journal,
                'accountUser' => $accountUser,
                'activeUserLog' => $journalUserId
            ]
        );
    }

    /**
     * Dashboard
     *
     * @return string
     */
    public function actionDashboard()
    {
        $searchModel = new UsersSearch();
        $dataProvider = $searchModel->search(Yii::$app->request->queryParams);
        $dataProvider->pagination->pageSize = 15;

        $accountUser = Yii::$app->user->identity;
        $currentUser = Users::find()
            ->where(['user_id' => $accountUser['id']])
            ->asArray()
            ->one();

        $cityCount = City::find()->count();
        $streetCount = Street::find()->count();
        $flatCount = Flat::find()->count();
        $equipmentCount = Equipment::find()->count();
        $abonentCount = Subject::find()->count();
        $residentCount = Resident::find()->count();
        $equipmentTypeCount = EquipmentType::find()->count();
        $houseCount = Subject::find()->count();
        $usersCount = Users::find()->count();

        $measures = Measure::find()
            ->orderBy('date')
            ->all();

        $equipments = Equipment::find()
            ->orderBy('id DESC')
            ->all();

        $users = Users::find()
            ->all();

        /**
         * Работа с картой
         */
        $userData = array();
        $users = Users::find()->select('*')->all();
        $userList[] = $users;
        $usersCount = count($users);

        $count = 0;
        foreach ($users as $current_user) {
            $gps = Gpstrack::find()
                ->select('latitude, longitude, date')
                ->orderBy('date DESC')
                ->where(['userUuid' => $current_user['uuid']])
                ->one();
            if ($gps) {
                $userData[$count]['latitude'] = $gps['latitude'];
                $userData[$count]['longitude'] = $gps['longitude'];
            } else {
                $userData[$count]['latitude'] = 0;
                $userData[$count]['longitude'] = 0;
            }

            $userData[$count]['id'] = $current_user['id'];
            $userData[$count]['name'] = $current_user['name'];
            $userData[$count]['contact'] = $current_user['contact'];

            $count++;
        }

        $cnt = 0;
        $usersGroup = 'var users=L.layerGroup([';
        $usersList = '';
        foreach ($userData as $user) {
            $usersList .= 'var user' . $user["id"] . '= L.marker(['
                . $user["latitude"] . ',' . $user["longitude"]
                . '], {icon: userIcon}).bindPopup("<b>' . $user["name"]
                . '</b><br/> '  . $user["contact"] . '").openPopup();';
            if ($cnt > 0) {
                $usersGroup .= ',';
            }

            $usersGroup .= 'user' . $user["id"];
            $cnt++;
        }

        $usersGroup .= ']);' . PHP_EOL;


        return $this->render(
            'dashboard',
            [
                'cityCount' => $cityCount,
                'houseCount' => $houseCount,
                'streetCount' => $streetCount,
                'usersCount' => $usersCount,
                'flatCount' => $flatCount,
                'measures' => $measures,
                'equipments' => $equipments,
                'users' => $users,
                'usersGroup' => $usersGroup,
                'usersList' => $usersList,
                'equipmentCount' => $equipmentCount,
                'equipmentTypeCount' => $equipmentTypeCount,
                'abonentCount' => $abonentCount,
                'residentCount' => $residentCount,
                'currentUser' => $currentUser,
                'searchModel' => $searchModel,
                'dataProvider' => $dataProvider
            ]
        );
    }

    /**
     * Login action.
     *
     * @return string
     * @throws \yii\web\HttpException
     */
    public function actionLogin()
    {
        if (!Yii::$app->user->isGuest) {
            return $this->goHome();
        }

        $model = new LoginForm();
        if ($model->load(Yii::$app->request->post()) && $model->login()) {
            return $this->goBack();
        } else {
            $model->password = '';
            return $this->render('login', [
                'model' => $model,
            ]);
        }
    }

    /**
     * Action error
     *
     * @return string
     */
    public function actionError()
    {
        if (\Yii::$app->getUser()->isGuest) {
            Yii::$app->getResponse()->redirect("/")->send();
        } else {
            $exception = Yii::$app->errorHandler->exception;
            if ($exception !== null) {
                $statusCode = $exception->statusCode;
                $name = $exception->getName();
                $message = $exception->getMessage();
                return $this->render(
                    'error',
                    [
                        'exception' => $exception,
                        'name' => $name . " " . $statusCode,
                        'message' => $message
                    ]
                );
            }
        }

        return '';
    }

    /**
     * Logout action.
     *
     * @return string
     */
    public function actionLogout()
    {
        Yii::$app->user->logout();

        return $this->goHome();
    }

    /**
     */
    public function actionTest()
    {
        $order = new OrderService();
        $order->run();
        return $this->render(
            'test',
            [
            ]
        );
    }

    /**
     * Displays a timeline
     *
     * @return mixed
     */
    public function actionTimeline()
    {
        $events = [];
        $defects = Defect::find()
            ->orderBy('date DESC')
            ->limit(10)
            ->all();
        foreach ($defects as $defect) {
            if ($defect['process'] == 0) $status = '<a class="btn btn-success btn-xs">Исправлен</a>';
            else $status = '<a class="btn btn-danger btn-xs">Активен</a>';
            $path = $defect['equipment']->getImageUrl();
            if ($path == null)
                $path = '/storage/order-level/no-image-icon-4.png';
            $text = '<img src="' . Html::encode($path) . '" class="margin" style="width:50px; margin: 2; float:left" alt="">';
            $text .= '<a class="btn btn-default btn-xs">' . $defect['equipment']->title . '</a>
                ' . $defect['comment'] . '<br/>
                <i class="fa fa-cogs"></i>&nbsp;Задача: ' . $defect['task']['taskTemplate']->title . '<br/>
                <i class="fa fa-check-square"></i>&nbsp;Статус: ' . $status . '';
            $events[] = ['date' => $defect['date'], 'event' => self::formEvent($defect['date'], 'defect', $defect['_id'],
                $defect['defectType']->title, $text, $defect['user']->name)];
        }

        $journals = Journal::find()
            ->orderBy('date DESC')
            ->limit(10)
            ->all();
        foreach ($journals as $journal) {
            $text = '<i class="fa fa-calendar"></i>&nbsp;'.$journal['description'];
            $events[] = ['date' => $journal['date'], 'event' => self::formEvent($journal['date'], 'journal', 0,
                $journal['description'], $text, $journal['user']->name)];
        }

        $externalEvents = ExternalEvent::find()
            ->orderBy('date DESC')
            ->limit(5)
            ->all();
        foreach ($externalEvents as $event) {
            $text = '<i class="fa fa-desktop"></i>&nbsp; Система:
                <span class="btn btn-default btn-xs">' . $event['externalTag']['externalSystem']->title .
                ' ['.$event['externalTag']['externalSystem']->address.']</span><br/>
                <i class="fa fa-exclamation"></i>&nbsp; Тег: 
                <span class="btn btn-primary btn-xs">' . $event['externalTag']->tag .
                ' ['.$event['externalTag']->value.']</span><br/>
                <i class="fa fa-cogs"></i>&nbsp;Оборудование: 
                <span class="btn btn-default btn-xs">' . $event['externalTag']['equipment']->title . '</span><br/>
                <i class="fa fa-plug"></i>&nbsp;Действие: 
                <span class="btn btn-default btn-xs">' . $event['externalTag']['actionType']->title . '</span>';
            $events[] = ['date' => $event['date'], 'event' => self::formEvent($event['date'],
                'event', 0, '', $text, '')];
        }

        $equipmentRegisters = EquipmentRegister::find()
            ->orderBy('date DESC')
            ->limit(10)
            ->all();
        foreach ($equipmentRegisters as $equipmentRegister) {
            $path = $equipmentRegister['equipment']->getImageUrl();
            if ($path == null)
                $path = '/storage/order-level/no-image-icon-4.png';
            $text = '<img src="' . Html::encode($path) . '" class="img-circle" style="width:50px; margin: 2; float:left" alt="">';
            $text .= '<i class="fa fa-cogs"></i>&nbsp;
                <a class="btn btn-default btn-xs">' . $equipmentRegister['equipment']->title . '</a><br/>
                <i class="fa fa-user"></i>&nbsp;Пользователь: <span class="btn btn-primary btn-xs">'
                . $equipmentRegister['user']->name . '</span><br/>
                <i class="fa fa-clipboard"></i>&nbsp;Изменил параметр: <a class="btn btn-default btn-xs">'
                . $equipmentRegister['fromParameterUuid'] . '</a>&nbsp;&gt;&nbsp;
                    <a class="btn btn-default btn-xs">' . $equipmentRegister['toParameterUuid'] . '</a>';
            $events[] = ['date' => $equipmentRegister['date'], 'event' => self::formEvent($equipmentRegister['date'],
                'equipmentRegister', 0, '', $text, $equipmentRegister['user']->name)];
        }

        $usersAttributes = UsersAttribute::find()
            ->orderBy('date DESC')
            ->limit(10)
            ->all();
        foreach ($usersAttributes as $usersAttribute) {
            $text = '<i class="fa fa-user"></i>&nbsp;Пользователь: <a class="btn btn-primary btn-xs">' .
                $usersAttribute['user']->name . '</a><br/>';
            $text = '<a class="btn btn-default btn-xs">Для пользователя зарегистрировано событие</a><br/>
                &nbsp;' . $usersAttribute['attributeType']->name . ' <a class="btn btn-default btn-xs">'
                . $usersAttribute['value'] . '</a>';
            $events[] = ['date' => $usersAttribute['date'], 'event' => self::formEvent($usersAttribute['date'],
                'usersAttribute', 0, '', $text, $usersAttribute['user']->name)];
        }

        $orders = Orders::find()
            ->orderBy('startDate DESC')
            ->all();
        foreach ($orders as $order) {
            if ($order['openDate'] > 0) $openDate = date("j-d-Y h:m", strtotime($order['openDate']));
            else $openDate = 'не начинался';
            if ($order['closeDate'] > 0) $closeDate = date("j-d-Y h:m", strtotime($order['closeDate']));
            else $closeDate = 'не закончился';
            $path = $order['user']->getImageUrl();
            if (!$path) {
                $path='/images/unknown.png';
            }
            $text = '<img src="' . Html::encode($path) . '" class="margin img-circle" style="width:50px; margin: 2; float:left" alt="">';
            $text .= '<i class="fa fa-user"></i>&nbsp;Автор: <a class="btn btn-primary btn-xs">' .
                $order['author']->name . '</a>&nbsp;Исполнитель: <a class="btn btn-primary btn-xs">' .
                $order['user']->name . '</a><br/>
                <i class="fa fa-calendar"></i>&nbsp;Открыт: [' . $openDate . ']
                <i class="fa fa-calendar"></i>&nbsp;Закрыт: [' . $closeDate . ']<br/>';
            if ($order['reason'])
                $text .= 'Основание: <a class="btn btn-default btn-xs">' . $order['reason'] . '</a><br/>';
            else
                $text .= 'Основание: <a class="btn btn-default btn-xs">не указано</a><br/>';
            if ($order['comment']) $text .= $order['comment'] . '<br/>';
            switch ($order['orderStatus']) {
                case OrderStatus::COMPLETE:
                    $text .= '<a class="btn btn-success btn-xs">Закончен</a>&nbsp;';
                    break;
                case OrderStatus::CANCELED:
                    $text .= '<a class="btn btn-danger btn-xs">Отменен</a>&nbsp;';
                    break;
                case OrderStatus::UN_COMPLETE:
                    $text .= '<a class="btn btn-warning btn-xs">Не закончен</a>&nbsp;';
                    break;
                default:
                    $text .= '<a class="btn btn-warning btn-xs">Не определен</a>&nbsp;';
            }
            $events[] = ['date' => $order['startDate'], 'event' => self::formEvent($order['startDate'],
                'order', 0, $order['title'], $text, $order['user']->name)];
        }
        $sort_events = MainFunctions::array_msort($events, ['date'=>SORT_DESC]);
        $today = date("j-m-Y h:m");

        return $this->render(
            'timeline',
            [
                'events' => $sort_events,
                'today_date' => $today
            ]
        );
    }

    /**
     * Формируем код записи о событии
     * @param $date
     * @param $type
     * @param $id
     * @param $title
     * @param $text
     * @param $user
     *
     * @return string
     */
    public static function formEvent($date, $type, $id, $title, $text, $user)
    {
        $event = '<li>';
        if ($type == 'defect')
            $event .= '<i class="fa fa-wrench bg-red"></i>';
        if ($type == 'journal')
            $event .= '<i class="fa fa-calendar bg-aqua"></i>';
        if ($type == 'equipmentRegister')
            $event .= '<i class="fa fa-cogs bg-green"></i>';
        if ($type == 'usersAttribute')
            $event .= '<i class="fa fa-user bg-gray"></i>';
        if ($type == 'order')
            $event .= '<i class="fa fa-sitemap bg-yellow"></i>';
        if ($type == 'message')
            $event .= '<i class="fa fa-mail bg-blue"></i>';
        if ($type == 'event')
            $event .= '<i class="fa fa-link bg-maroon"></i>';

        $event .= '<div class="timeline-item">';
        $event .= '<span class="time"><i class="fa fa-clock-o"></i> ' . date("M j, Y h:m", strtotime($date)) . '</span>';
        if ($type == 'event')
            $event .= '<span class="timeline-header" style="vertical-align: middle">' .
                Html::a('Внешняя система сгенерировала событие &nbsp;',
                    ['/defect/view', 'id' => Html::encode($id)]) . $title . '</span>';

        if ($type == 'defect')
            $event .= '&nbsp;<span class="btn btn-primary btn-xs">'.$user.'</span>&nbsp;
                    <span class="timeline-header" style="vertical-align: middle">' .
                    Html::a('Пользователь зарегистрировал дефект &nbsp;',
                    ['/defect/view', 'id' => Html::encode($id)]) . $title . '</span>';

        if ($type == 'journal')
            $event .= '&nbsp;<span class="btn btn-primary btn-xs">'.$user.'</span>&nbsp;
                      <span class="timeline-header" style="vertical-align: middle">
                      <a href="#">Добавлено событие журнала</a></span>';

        if ($type == 'equipmentRegister')
            $event .= '&nbsp;<span class="btn btn-primary btn-xs">'.$user.'</span>&nbsp;
                    <span class="timeline-header" style="vertical-align: middle">' .
                    Html::a('Параметр оборудования изменен &nbsp;',
                    ['/equipment-register/view', 'id' => Html::encode($id)]) . $title . '</span>';

        if ($type == 'usersAttribute')
            $event .= '&nbsp;<span class="btn btn-primary btn-xs">'.$user.'</span>&nbsp; 
                    <span class="timeline-header" style="vertical-align: middle">' .
                    Html::a('Изменен аттрибут пользователя &nbsp;',
                    ['/equipment-register/view', 'id' => Html::encode($id)]) . $title . '</span>';
        if ($type == 'order')
            $event .= '<h3 class="timeline-header">' . Html::a('Сформирован наряд &nbsp;',
                    ['/orders/view', 'id' => Html::encode($id)]).'['. $title . ']</h3>';
        if ($type == 'message')
            $event .= '<h3 class="timeline-header">' . Html::a('Получено сообщение &nbsp;',
                    ['/messages/view', 'id' => Html::encode($id)]) . $title . '</h3>';

        $event .= '<div class="timeline-body">' . $text . '</div>';
        $event .= '</div></li>';
        return $event;
    }
}
