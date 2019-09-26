<?php

namespace backend\controllers;

use backend\models\SignupForm;
use backend\models\UsersSearch;
use common\components\MainFunctions;
use common\components\ReferenceFunctions;
use common\models\Alarm;
use common\models\City;
use common\models\Contragent;
use common\models\ContragentType;
use common\models\Documentation;
use common\models\DocumentationType;
use common\models\Equipment;
use common\models\EquipmentRegister;
use common\models\EquipmentType;
use common\models\Gpstrack;
use common\models\House;
use common\models\Journal;
use common\models\LoginForm;
use common\models\Measure;
use common\models\Objects;
use common\models\ObjectType;
use common\models\Photo;
use common\models\Settings;
use common\models\Street;
use common\models\Task;
use common\models\TaskUser;
use common\models\User;
use common\models\UserContragent;
use common\models\Users;
use common\models\WorkStatus;
use Throwable;
use Yii;
use yii\base\InvalidConfigException;
use yii\db\Exception;
use yii\db\StaleObjectException;
use yii\filters\AccessControl;
use yii\filters\VerbFilter;
use yii\helpers\Html;
use yii\web\Controller;

/**
 * Site controller
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
                'class' => AccessControl::class,
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
                        'actions' => ['logout', 'index', 'dashboard', 'test', 'timeline', 'files', 'add', 'remove', 'config'],
                        'allow' => true,
                        'roles' => ['@'],
                    ],
                ],
            ],
            'verbs' => [
                'class' => VerbFilter::class,
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
     * @throws Exception
     * @throws InvalidConfigException
     */
    public function actionIndex()
    {
        /**
         * Работа с картой
         * [$online, $offline, $gpsOn, $gps description]
         *
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

        $users = Users::find()
            ->where('name!="sUser"')
            ->andWhere(['OR', ['type' => Users::USERS_WORKER], ['type' => Users::USERS_ARM_WORKER]])
            ->all();
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
            if (strtotime($current_user['changedAt']) >= $threshold) {
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
            $userData[$count]['contact'] = $current_user['contact'];

            $gps = Gpstrack::find()
                ->select('latitude, longitude, date')
                ->orderBy('date DESC')
                ->where(['userUuid' => $current_user['uuid']])
                ->limit(5000)
                ->all();
            if ($gps) {
                $lats[$count] = $gps;
            } else {
                $lats[$count] = [];
            }

            $count++;
        }

        if (!$gpsStatus) {
            $gps = Gpstrack::find()->orderBy('date DESC')->asArray()->one();
        }
        /**
         * Настройки - История активности
         */
        $cnt = 0;
        $photosGroup = 'var houses=L.layerGroup([';
        $photosList = '';
        $photoHouses = House::find()
            ->select('*')
            ->all();
        $default_coordinates = "[55.54,61.36]";
        $coordinates = $default_coordinates;

        foreach ($photoHouses as $photoHouse) {
            if ($photoHouse["latitude"] > 0) {
                $photosList .= 'var house'
                    . $photoHouse["_id"]
                    . '= L.marker([' . $photoHouse["latitude"]
                    . ',' . $photoHouse["longitude"]
                    . '], {icon: houseIcon}).bindPopup("<b>'
                    . $photoHouse["street"]->title . ', ' . $photoHouse["number"] . '</b><br/>").openPopup();' . PHP_EOL;
                if ($cnt > 0) {
                    $photosGroup .= ',';
                }
                $coordinates = "[".$photoHouse["latitude"].",".$photoHouse["longitude"]."]";
                if ($coordinates==$default_coordinates && $photoHouse["latitude"]>0) {
                    $coordinates = "[".$photoHouse["latitude"].",".$photoHouse["longitude"]."]";
                }
                $photosGroup .= 'house' . $photoHouse["_id"];
                $cnt++;
            }
        }
        $photosGroup .= ']);' . PHP_EOL;

        $cnt = 0;
        $usersGroup = 'var users=L.layerGroup([';
        $usersList = '';
        foreach ($userData as $user) {
            $usersList .= 'var user' . $user["_id"]
                . '= L.marker([' . $user["latitude"]
                . ',' . $user["longitude"]
                . '], {icon: userIcon}).bindPopup("<b>'
                . htmlspecialchars($user["name"]) . '</b><br/>'
                . $user["contact"] . '").openPopup();' . PHP_EOL;
            if ($cnt > 0) {
                $usersGroup .= ',';
            }
            $usersGroup .= 'user' . $user["_id"];
            $cnt++;
        }
        $usersGroup .= ']);' . PHP_EOL;

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
                'usersGroup' => $usersGroup,
                'usersList' => $usersList,
                'houses' => $photoHouses,
                'housesGroup' => $photosGroup,
                'housesList' => $photosList,
                'ways' => $ways,
                'coordinates' => $coordinates,
                'wayUsers' => $wayUsers,
                'lats' => $lats,
                'gps' => $gps,
                'gps2' => $gps2,
            ]
        );
    }

    /**
     * Dashboard
     *
     * @return string
     * @throws InvalidConfigException
     * @throws Exception
     */
    public function actionDashboard()
    {
        $searchModel = new UsersSearch();
        $dataProvider = $searchModel->search(Yii::$app->request->queryParams);
        $dataProvider->pagination->pageSize = 15;

        $accountUser = Yii::$app->user->identity;
        $currentUser = Users::find()
            ->where(['user_id' => $accountUser['id']])
            ->andWhere(['OR', ['type' => Users::USERS_WORKER], ['type' => Users::USERS_ARM_WORKER]])
            ->asArray()
            ->one();

        $cityCount = City::find()->count();
        $streetCount = Street::find()->count();
        $houseCount = House::find()->count();
        $objectsCount = Objects::find()->count();
        $flatCount = Objects::find()->where(['objectTypeUuid' => ObjectType::OBJECT_TYPE_FLAT])->count();
        $equipmentCount = Equipment::find()->count();
        $contragentCount = Contragent::find()->count();
        $equipmentTypeCount = EquipmentType::find()->count();
        //        $usersCount = Users::find()->count();

        $measures = Measure::find()
            ->orderBy('date desc')
            ->all();

        $equipments = Equipment::find()
            ->orderBy('_id DESC')
            ->limit(15)
            ->all();

        $userData = array();
        $users = Users::find()
            ->where('name != "sUser"')
            ->select('*')
            ->all();
        $userList[] = $users;
        $usersCount = count($users);
        $activeUsersCount = 0;

        $count = 0;
        $categories = "";
        $bar = "{ name: 'Выполнено в срок', color: 'green', ";
        $bar .= "data: [";
        foreach ($users as $current_user) {
            if ($current_user->user->status == User::STATUS_ACTIVE) {
                $activeUsersCount++;
            }

            if ($count > 0) {
                $categories .= ',';
                $bar .= ",";
            }
            $categories .= '"' . $current_user['name'] . '"';
            $taskGood=0;
            $taskUsers = TaskUser::find()
                ->where(['userUuid' => $current_user['uuid']])
                ->all();
            foreach ($taskUsers as $taskUser) {
                $taskGood += Task::find()
                    ->where(['uuid' => $taskUser['taskUuid']])
                    ->andWhere(['workStatusUuid' => WorkStatus::COMPLETE])
                    ->andWhere('endDate <= deadlineDate')
                    ->count();
            }
            $bar .= $taskGood;
            $count++;
        }
        $bar .= "]},";

        $bar .= "{ name: 'Выполнено', color: 'blue', ";
        $bar .= "data: [";
        $count = 0;
        foreach ($users as $current_user) {
            if ($count > 0)
                $bar .= ",";
            $taskComplete=0;
            $taskUsers = TaskUser::find()
                ->where(['userUuid' => $current_user['uuid']])
                ->all();
            foreach ($taskUsers as $taskUser) {
                $taskComplete += Task::find()
                    ->where(['uuid' => $taskUser['taskUuid']])
                    ->andWhere(['workStatusUuid' => WorkStatus::COMPLETE])
                    ->count();
            }
            $bar .= $taskComplete;
            $count++;
        }
        $bar .= "]},";

        $bar .= "{ name: 'Просрочено', color: 'red', ";
        $bar .= "data: [";
        $count = 0;
        foreach ($users as $current_user) {
            if ($count > 0)
                $bar .= ",";
            $taskBad=0;
            $taskUsers = TaskUser::find()
                ->where(['userUuid' => $current_user['uuid']])
                ->all();
            foreach ($taskUsers as $taskUser) {
                $taskBad += Task::find()
                    ->where(['uuid' => $taskUser['taskUuid']])
                    ->andWhere('deadlineDate > NOW()')
                    ->andWhere(['workStatusUuid' => WorkStatus::NEW])
                    ->count();
                $taskBad += Task::find()
                    ->where(['uuid' => $taskUser['taskUuid']])
                    ->andWhere('deadlineDate > NOW()')
                    ->andWhere(['workStatusUuid' => WorkStatus::IN_WORK])
                    ->count();
                $taskBad += Task::find()
                    ->where(['uuid' => $taskUser['taskUuid']])
                    ->andWhere('deadlineDate > endDate')
                    ->andWhere(['workStatusUuid' => WorkStatus::COMPLETE])
                    ->count();
            }
            $bar .= $taskBad;
            $count++;
        }
        $bar .= "]}";

        $userData = array();
        $lats = array();
        $online = [];
        $offline = [];
        $wayUsers = [];
        $gps = 0;
        $gps2 = 0;
        $gpsStatus = false;

        $users = Users::find()
            ->where('name!="sUser"')
            ->andWhere(['OR', ['type' => Users::USERS_WORKER], ['type' => Users::USERS_ARM_WORKER]])
            ->all();
        $userList[] = $users;

        $contragents = Contragent::find()
            ->where(['contragentTypeUuid' => [ContragentType::WORKER, ContragentType::EMPLOYEE, ContragentType::CONTRACTOR]])
            ->all();
        $workersCount = 0;
        foreach ($contragents as $contragent) {
            $userContragent = UserContragent::find()->where(['contragentUuid' => $contragent['uuid']])->one();
            if ($userContragent) {
                if ($userContragent['user']['type'] == Users::USERS_WORKER || $userContragent['user']['type'] == Users::USERS_ARM_WORKER) {
                    $workersCount++;
                }
            }
        }
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
            if (strtotime($current_user['changedAt']) >= $threshold) {
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
            $userData[$count]['contact'] = $current_user['contact'];

            $gps = Gpstrack::find()
                ->select('latitude, longitude, date')
                ->orderBy('date DESC')
                ->where(['userUuid' => $current_user['uuid']])
                ->limit(5000)
                ->all();
            if ($gps) {
                $lats[$count] = $gps;
            } else {
                $lats[$count] = [];
            }

            $count++;
        }

        if (!$gpsStatus) {
            $gps = Gpstrack::find()->orderBy('date DESC')->asArray()->one();
        }
        /**
         * Настройки - История активности
         */
        $cnt = 0;
        $photosGroup = 'var houses=L.layerGroup([';
        $photosList = '';
        $photoHouses = House::find()
            ->select('*')
            ->all();
        $default_coordinates = "[55.54,61.36]";
        $coordinates = $default_coordinates;

        foreach ($photoHouses as $photoHouse) {
            if ($photoHouse["latitude"] > 0) {
                $photosList .= 'var house'
                    . $photoHouse["_id"]
                    . '= L.marker([' . $photoHouse["latitude"]
                    . ',' . $photoHouse["longitude"]
                    . '], {icon: houseIcon}).bindPopup("<b>'
                    . $photoHouse["street"]->title . ', ' . $photoHouse["number"] . '</b><br/>").openPopup();';
                if ($cnt > 0) {
                    $photosGroup .= ',';
                }
                $coordinates = "[".$photoHouse["latitude"].",".$photoHouse["longitude"]."]";
                if ($coordinates==$default_coordinates && $photoHouse["latitude"]>0) {
                    $coordinates = "[".$photoHouse["latitude"].",".$photoHouse["longitude"]."]";
                }
                $photosGroup .= 'house' . $photoHouse["_id"];
                $cnt++;
            }
        }
        $photosGroup .= ']);' . PHP_EOL;

        $cnt = 0;
        $usersGroup = 'var users=L.layerGroup([';
        $usersList = '';
        foreach ($userData as $user) {
            $usersList .= 'var user' . $user["_id"]
                . '= L.marker([' . $user["latitude"]
                . ',' . $user["longitude"]
                . '], {icon: userIcon}).bindPopup("<b>'
                . $user["name"] . '</b><br/>'
                . $user["contact"] . '").openPopup();';
            if ($cnt > 0) {
                $usersGroup .= ',';
            }
            $usersGroup .= 'user' . $user["_id"];
            $cnt++;
        }
        $usersGroup .= ']);' . PHP_EOL;

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
            'dashboard',
            [
                'cityCount' => $cityCount,
                'streetCount' => $streetCount,
                'usersCount' => $usersCount,
                'objectsCount' => $objectsCount,
                'houseCount' => $houseCount,
                'flatCount' => $flatCount,
                'activeUsersCount' => $activeUsersCount,
                'measures' => $measures,
                'equipments' => $equipments,
                'contragents' => $contragents,
                'users' => $users,
                'categories' => $categories,
                'bar' => $bar,
                'usersGroup' => $usersGroup,
                'usersList' => $usersList,
                'equipmentCount' => $equipmentCount,
                'equipmentTypeCount' => $equipmentTypeCount,
                'contragentCount' => $contragentCount,
                'currentUser' => $currentUser,
                'searchModel' => $searchModel,
                'dataProvider' => $dataProvider,
                'houses' => $photoHouses,
                'housesGroup' => $photosGroup,
                'housesList' => $photosList,
                'ways' => $ways,
                'coordinates' => $coordinates,
                'wayUsers' => $wayUsers,
                'workersCount' => $workersCount,
                'lats' => $lats,
                'gps' => $gps,
                'gps2' => $gps2
            ]
        );
    }

    /**
     * Login action.
     *
     * @return string
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
     * Signup action.
     *
     * @return string
     * @throws Throwable
     */
    public function actionSignup()
    {
//        if (!Yii::$app->user->isGuest) {
            return $this->goHome();
//        }

        $model = new SignupForm();
        if ($model->load(Yii::$app->request->post()) && $model->signup()) {
            Yii::$app->user->login(User::findByUsername($model->username), true ? 3600 * 24 * 30 : 0);
            return $this->goBack();
        } else {
            $model->password = '';
            return $this->render('signup', [
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
        if (Yii::$app->getUser()->isGuest) {
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
     * Displays a timeline
     *
     * @return mixed
     * @throws Exception
     * @throws InvalidConfigException
     */
    public function actionTimeline()
    {
        ini_set('memory_limit', '-1');
        $events = [];
        $measures = Measure::find()
            ->orderBy('date DESC')
            ->limit(10)
            ->all();
        foreach ($measures as $measure) {
            $photo = Photo::find()
                ->where(['objectUuid' => $measure['equipmentUuid']])
                ->orderBy('createdAt DESC')
                ->one();

            $path = '/storage/equipment/' . $photo['uuid'] . '.jpg';
            if ($path == null)
                $path = 'images/no-image-icon-4.png';
            $text = '<a href="/storage/equipment/' . $photo['uuid'] . '.jpg"><img src="' . Html::encode($path) . '" class="margin" 
                        style="width:50px; margin: 2px; float:left" alt=""></a>';
            $text .= '<a class="btn btn-default btn-xs">' .
                $measure['equipment']['equipmentType']->title . ' [' .
                $measure['equipment']['object']['house']['street']->title . ', ' .
                $measure['equipment']['object']['house']->number . ', ' .
                $measure['equipment']['object']['title'] . ']</a><br/>
                <i class="fa fa-cogs"></i>&nbsp;Оборудование: ' . $measure['equipment']['equipmentType']->title . '<br/>
                <i class="fa fa-check-square"></i>&nbsp;Значение: ' . $measure['value'] . '';
            $events[] = ['date' => $measure['date'], 'event' => self::formEvent($measure['date'], 'measure',
                $measure['_id'],
                $measure['equipment']['equipmentType']->title, $text, $measure['user']->name)];
        }

        $alarms = Alarm::find()
            ->orderBy('date DESC')
            ->limit(20)
            ->all();
        foreach ($alarms as $alarm) {
            //$path = $alarm['user']->getImageUrl();
            //if ($path == null)
            $path = '/images/p1.jpg';
            $text = '<img src="' . Html::encode($path) . '" class="img-circle" style="width:50px; margin: 2; float:left" alt="">';
            $text .= '<i class="fa fa-cogs"></i>&nbsp;
                <a class="btn btn-default btn-xs">' . $alarm['alarmType']->title . '</a><br/>
                <i class="fa fa-user"></i>&nbsp;Пользователь: <span class="btn btn-primary btn-xs">'
                . $alarm['user']->name . '</span><br/>
                <i class="fa fa-clipboard"></i>&nbsp;Статус: <a class="btn btn-default btn-xs">'
                . $alarm['alarmStatus']->title . '</a>&nbsp;&gt;&nbsp;
                    <a class="btn btn-default btn-xs">[' . $alarm['longitude'] . '</a> | <a class="btn btn-default btn-xs">' . $alarm['longitude'] . ']</a>';
            $events[] = ['date' => $alarm['date'], 'event' => self::formEvent($alarm['date'],
                'alarm', 0, '', $text, $alarm['user']->name)];
        }

        $journals = Journal::find()
            ->orderBy('date DESC')
            ->limit(20)
            ->all();
        foreach ($journals as $journal) {
            $events[] = ['date' => $journal['date'], 'event' => self::formEvent($journal['date'], $journal['type'],
                0, $journal['title'], $journal['description'], $journal['user']['name'])];
        }

        $photos = Photo::find()
            ->limit(5)
            ->all();
        foreach ($photos as $photo) {
            $text = '<a class="btn btn-default btn-xs">Объект</a><br/>';
            $events[] = ['date' => $photo['createdAt'], 'event' => self::formEvent($photo['createdAt'], 'photo',
                $photo['_id'], 'Добавлено фото', $text, $photo['user']['name'])];
        }

        $equipment_registers = EquipmentRegister::find()
            ->orderBy('createdAt DESC')
            ->limit(20)
            ->all();
        foreach ($equipment_registers as $register) {
            $text = '<a class="btn btn-default btn-xs">' . $register['equipment']->title . '</a><br/>
                '.$register['description'].'<br/>
                <i class="fa fa-cogs"></i>&nbsp;Тип: ' . $register['registerType']['title'] . '<br/>';
            $events[] = ['date' => $register['date'], 'event' => self::formEvent($register['date'], 'register',
                $register['_id'], $register['equipment']['equipmentType']->title, $text, $register['user']['name'])];
        }

        $sort_events = MainFunctions::array_msort($events, ['date' => SORT_DESC]);
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
        if ($type == 'register')
            $event .= '<i class="fa fa-calendar bg-aqua"></i>';
        if ($type == "alarm")
            $event .= '<i class="fa fa-warning bg-red"></i>';
        if ($type == "documentation")
            $event .= '<i class="fa fa-book bg-blue"></i>';
        if ($type == "equipment")
            $event .= '<i class="fa fa-qrcode bg-aqua"></i>';
        if ($type == "object")
            $event .= '<i class="fa fa-home bg-green"></i>';
        if ($type == "request")
            $event .= '<i class="fa fa-send bg-orange"></i>';
        if ($type == "user-system")
            $event .= '<i class="fa fa-user bg-success"></i>';
        if ($type == "user")
            $event .= '<i class="fa fa-user bg-success"></i>';
        if ($type == 'measure')
            $event .= '<i class="fa fa-bar-chart bg-success"></i>';
        if ($type == 'photo')
            $event .= '<i class="fa fa-photo bg-aqua"></i>';
        if ($type == 'task')
            $event .= '<i class="fa fa-tasks bg-info"></i>';

        $event .= '<div class="timeline-item">';
        $event .= '<span class="time"><i class="fa fa-clock-o"></i> ' . date("M j, Y h:m", strtotime($date)) . '</span>';
        if ($type == 'measure')
            $event .= '<span class="timeline-header" style="vertical-align: middle">
                        <span class="btn btn-primary btn-xs">' . $user . '</span>&nbsp;' .
                Html::a('Снято показание &nbsp;',
                    ['/measure/view', 'id' => Html::encode($id)]) . $title . '</span>';

        if ($type == 'alarm')
            $event .= '&nbsp;<span class="btn btn-primary btn-xs">' . $user . '</span>&nbsp;
                    <span class="timeline-header" style="vertical-align: middle">' .
                Html::a('Зафиксировано событие &nbsp;',
                    ['/alarm/view', 'id' => Html::encode($id)]) . $title . '</span>';

        if ($type != 'alarm' && $type != 'measure') {
            $event .= '&nbsp;<span class="btn btn-primary btn-xs">' . $user . '</span>&nbsp;
                    <span class="timeline-header" style="vertical-align: middle">' . $title . '</span>';
        }

        $event .= '<div class="timeline-body">' . $text . '</div>';
        $event .= '</div></li>';
        return $event;
    }

    /**
     * Build tree of files
     *
     * @return mixed
     * @throws Exception
     * @throws InvalidConfigException
     */
    public function actionFiles()
    {
        $tree = array();
        $tree['children'][] = ['title' => 'Документация', 'key' => 1010,
            'expanded' => false, 'folder' => true];
        $documentationTypes = DocumentationType::find()->all();
        $documentationCount = 0;
        foreach ($documentationTypes as $documentationType) {
            $documentations = Documentation::find()->where(['documentationTypeUuid' => $documentationType['uuid']])->all();
            $tree['children'][0]['children'][] = [
                'title' => $documentationType['title'],
                'key' => $documentationType['_id'],
                'what' => 'documentation',
                'types' => 1,
                'expanded' => false, 'folder' => true];

            $sum_size = 0;
            foreach ($documentations as $documentation) {
                $fileName = $documentation->getDocFullPath();
                //$fileName = EquipmentController::getDocDir($documentation) . $documentation['path'];
                if (is_file($fileName)) {
                    $size = number_format(filesize($fileName) / 1024, 2) . 'Кб';
                    $real_size = filesize($fileName) / 1024;
                    $links = Html::a('<span class="glyphicon glyphicon-floppy-disk"></span>&nbsp',
                        [EquipmentController::getDocDir($documentation) . $documentation['path']], ['title' => $documentation['title']]
                    );
                    $ext = pathinfo($fileName, PATHINFO_EXTENSION);
                } else {
                    $size = "неизвестен";
                    $links = '';
                    $real_size = 0;
                    $ext = "-";
                }
                $title = 'общее';
                if ($documentation['equipment'])
                    $title = $documentation['equipment']['title'];
                if ($documentation['equipmentType'])
                    $title = 'Тип: ' . $documentation['equipmentType']['title'];
                if ($documentation['house'])
                    $title = $documentation['house']['street']['title'] . ', д.' . $documentation['house']['number'];

                $tree['children'][0]['children'][$documentationCount]['children'][] =
                    [
                        'title' => '[' . $title . '] ' . $documentation['title'],
                        'key' => $documentation['_id'] . "",
                        'date' => $documentation['createdAt'],
                        'size' => $size,
                        'links' => $links,
                        'ext' => $ext,
                        'expanded' => false,
                        'folder' => false];
                $sum_size += $real_size;
            }
            if ($sum_size > 0)
                $tree['children'][0]['children'][$documentationCount]['size'] = number_format($sum_size / 1024, 2) . 'Мб';
            else
                $tree['children'][0]['children'][$documentationCount]['size'] = 'нет файлов';
            $documentationCount++;
        }
        return $this->render('files', [
            'files' => $tree
        ]);
    }

    /**
     * функция отрабатывает сигналы от дерева и выполняет добавление нового аттрибута
     *
     * @return mixed
     */
    public function actionAdd()
    {
        if (isset($_POST["selected_node"])) {
            $folder = $_POST["folder"];
            if (isset($_POST["what"]))
                $what = $_POST["what"];
            else $what = 0;
            if (isset($_POST["types"]))
                $type = $_POST["types"];
            else $type = 0;
            if (isset($_POST["source"]))
                $source = $_POST["source"];
            else $source = 0;

            if ($folder == "true" && $type) {
                if ($what == "documentation") {
                    $documentation = new Documentation();
                    return $this->renderAjax('../documentation/_add_form', [
                        'documentation' => $documentation,
                        'equipmentType' => $type,
                        'equipmentUuid' => 0,
                        'equipmentTypeUuid' => 0,
                        'source' => $source
                    ]);
                }
                return false;
            }
        }
        return 'Выберите в дереве тип атрибута или документации';
    }

    /**
     * @return mixed
     * @throws StaleObjectException
     * @throws Throwable
     */
    public function actionRemove()
    {
        if (!isset($_POST["types"]) && isset($_POST["selected_node"])) {
            $model = Documentation::find()->where(['_id' => $_POST["selected_node"]])->one();
            if ($model) {
                    $model->delete();
            }
        }
        return self::actionFiles();
    }

    /**
     * @throws Exception
     */
    public function actionPicec()
    {
        $oid = Users::getCurrentOid();
        ReferenceFunctions::loadReferences($oid, Yii::$app->db);
        ReferenceFunctions::loadReferencesNext($oid, Yii::$app->db);
        ReferenceFunctions::loadReferencesAll($oid, Yii::$app->db);
        ReferenceFunctions::loadReferencesAll2($oid, Yii::$app->db);
        ReferenceFunctions::loadRequestTypes($oid, Yii::$app->db);
    }

    /**
     * @return string
     * @throws Exception
     * @throws InvalidConfigException
     */
    public function actionConfig()
    {
        if (isset($_POST["period"])) {
            Settings::storeSetting(Settings::SETTING_TASK_PAUSE_BEFORE_WARNING, $_POST["period"]);
        }
        if (isset($_POST["warning"])) {
            Settings::storeSetting(Settings::SETTING_SHOW_WARNINGS, "1");
        } else {
            Settings::storeSetting(Settings::SETTING_SHOW_WARNINGS, "0");
        }
        return $this->actionIndex();
    }

}
