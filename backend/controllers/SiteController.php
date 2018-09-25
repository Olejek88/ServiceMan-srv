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

use backend\models\UsersSearch;
use common\components\MainFunctions;
use common\models\Alarm;
use common\models\City;
use common\models\CriticalType;
use common\models\Defect;
use common\models\Equipment;
use common\models\EquipmentRegister;
use common\models\EquipmentType;
use common\models\ExternalEvent;
use common\models\Flat;
use common\models\Gpstrack;
use common\models\Journal;
use common\models\LoginForm;
use common\models\Measure;
use common\models\Operation;
use common\models\OperationStatus;
use common\models\Orders;
use common\models\OrderStatus;
use common\models\OrderVerdict;
use common\models\PhotoHouse;
use common\models\Resident;
use common\models\Stage;
use common\models\StageStatus;
use common\models\Street;
use common\models\Subject;
use common\models\Users;
use common\models\UsersAttribute;
use Yii;
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
                ->limit(30000)
                ->all();
            if ($gps) {
                $lats[$count] = $gps;
            } else {
                $lats[$count] = [];
            }

            $count++;
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
        $cnt = 0;
        $photosGroup = 'var photos=L.layerGroup([';
        $photosList = '';
        $photoHouses  = PhotoHouse::find()
            ->select('*')
            //->groupBy('houseUuid')
            //->asArray()
            ->all();

        foreach ($photoHouses as $photoHouse) {
            if ($photoHouse["latitude"] > 0) {
                $photosList .= 'var photo'
                    . $photoHouse["_id"]
                    . '= L.marker([' . $photoHouse["latitude"]
                    . ',' . $photoHouse["longitude"]
                    . '], {icon: houseIcon}).bindPopup("<b>'
                    . $photoHouse["house"]["street"]->title.', '.$photoHouse["house"]->number. '</b><br/>'
                    . $photoHouse["user"]->name . '['.$photoHouse['createdAt'].']").openPopup();';
                if ($cnt > 0) {
                    $photosGroup .= ',';
                }
                $photosGroup .= 'photo' . $photoHouse["_id"];
                $cnt++;
            }
        }
        $photosGroup .= ']);' . PHP_EOL;

        /*
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
        Yii::$app->view->params['user'] = $userJournal;*/

        /**
         * Журнал событий
         */

/*        // В случаи, если геоданные не были отправлены, ответ на запрос будет null
        $journal = Journal::find()
            ->select('userUuid, description, date')
            ->where('date  >= NOW() - INTERVAL 1 DAY')
            ->asArray()
            ->all();*/

        $userUuid = Users::find()
            ->select('uuid, name')
            ->asArray()
            ->all();

        // $userUuid   = array_map("unserialize", array_unique(array_map("serialize", $userUuid)));

/*        foreach ($userUuid as $i => $user) {
            foreach ($journal as $j => $jrnl) {
                if ($userUuid[$i]['uuid'] === $journal[$j]['userUuid']) {
                    $journal[$j]['userUuid'] = $userUuid[$i]['name'];
                }
            }
        }*/

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
            'index',
            [
                'users' => $userData,
                'usersGroup' => $usersGroup,
                'usersList' => $usersList,
                'photos' => $photoHouses,
                'photosGroup' => $photosGroup,
                'photosList' => $photosList,
                'ways' => $ways,
                'wayUsers' => $wayUsers,
                'lats' => $lats,
                'gps' => $gps,
                'gps2' => $gps2,
                //'accountUser' => $accountUser,
                //'activeUserLog' => $journalUserId
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
        $subjectCount = Subject::find()->count();
        $residentCount = Resident::find()->count();
        $equipmentTypeCount = EquipmentType::find()->count();
        $subjectsCount = Subject::find()->count() + Resident::find()->count();
        $usersCount = Users::find()->count();

        $measures = Measure::find()
            ->orderBy('date')
            ->all();

        $equipments = Equipment::find()
            ->orderBy('_id DESC')
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

            $userData[$count]['id'] = $current_user['_id'];
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
                'subjectsCount' => $subjectsCount,
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
                'subjectCount' => $subjectCount,
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
     * Displays a timeline
     *
     * @return mixed
     */
    public function actionTimeline()
    {
        $events = [];
        $measures = Measure::find()
            ->orderBy('date DESC')
            ->limit(20)
            ->all();
        foreach ($measures as $measure) {
            $status = '<a class="btn btn-success btn-xs">Значение</a>';
            //$path = $measure['equipment']->getPhotoUrl();
            //if ($path == null)
            $path = '/images/no-image-icon-4.png';
            $text = '<img src="' . Html::encode($path) . '" class="margin" style="width:50px; margin: 2; float:left" alt="">';
            $text .= '<a class="btn btn-default btn-xs">' . $measure['equipment']['equipmentType']->title . '</a><br/>
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
                    <a class="btn btn-default btn-xs">[' . $alarm['longitude'] .'</a> | <a class="btn btn-default btn-xs">'. $alarm['longitude'] . ']</a>';
            $events[] = ['date' => $alarm['date'], 'event' => self::formEvent($alarm['date'],
                'alarm', 0, '', $text, $alarm['user']->name)];
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
        if ($type == 'measure')
            $event .= '<i class="fa fa-wrench bg-red"></i>';
        if ($type == 'alarm')
            $event .= '<i class="fa fa-calendar bg-aqua"></i>';

        $event .= '<div class="timeline-item">';
        $event .= '<span class="time"><i class="fa fa-clock-o"></i> ' . date("M j, Y h:m", strtotime($date)) . '</span>';
        if ($type == 'measure')
            $event .= '<span class="timeline-header" style="vertical-align: middle">' .
                Html::a('Снято показание &nbsp;',
                    ['/measure/view', 'id' => Html::encode($id)]) . $title . '</span>';

        if ($type == 'alarm')
            $event .= '&nbsp;<span class="btn btn-primary btn-xs">'.$user.'</span>&nbsp;
                    <span class="timeline-header" style="vertical-align: middle">' .
                    Html::a('Зафиксировано событие &nbsp;',
                    ['/alarm/view', 'id' => Html::encode($id)]) . $title . '</span>';

        $event .= '<div class="timeline-body">' . $text . '</div>';
        $event .= '</div></li>';
        return $event;
    }
}
