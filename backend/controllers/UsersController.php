<?php

namespace backend\controllers;

use backend\models\UserArm;
use backend\models\UsersSearch;
use common\components\MainFunctions;
use common\models\Alarm;
use common\models\Contragent;
use common\models\ContragentType;
use common\models\Gpstrack;
use common\models\Journal;
use common\models\Measure;
use common\models\Message;
use common\models\Photo;
use common\models\User;
use common\models\TaskUser;
use common\models\UserContragent;
use common\models\UserHouse;
use common\models\Users;
use common\models\UserSystem;
use Yii;
use yii\db\StaleObjectException;
use yii\helpers\ArrayHelper;
use yii\helpers\Html;
use yii\web\HttpException;
use yii\web\NotFoundHttpException;
use yii\web\UploadedFile;
use Throwable;
use yii\base\InvalidConfigException;
use Exception;

/**
 * UsersController implements the CRUD actions for Users model.
 */
class UsersController extends ZhkhController
{
    /**
     * Lists all Users models.
     *
     * @return mixed
     * @throws InvalidConfigException
     * @throws \yii\db\Exception
     */
    public function actionIndex()
    {
        return self::actionTable();
/*        $searchModel = new UsersSearch();
        $dataProvider = $searchModel->search(Yii::$app->request->queryParams);
        $dataProvider->pagination->pageSize = 15;

        return $this->render(
            'index',
            [
                'searchModel' => $searchModel,
                'dataProvider' => $dataProvider,
            ]
        );*/
    }

    /**
     * Displays a single Users model.
     *
     * @param integer $id Id.
     *
     * @return mixed
     * @throws InvalidConfigException
     * @throws NotFoundHttpException
     * @throws \yii\db\Exception
     */
    public function actionView($id)
    {
        $model = $this->findModel($id);

        $user_photo = Photo::find()->where(['userUuid' => $model['uuid']])->count();
        $user_property['photo'] = $user_photo;
        $user_measure = Measure::find()->where(['userUuid' => $model['uuid']])->count();
        $user_property['measure'] = $user_measure;
        $user_alarm = Alarm::find()->where(['userUuid' => $model['uuid']])->count();
        $user_property['alarms'] = $user_alarm;
        $user_messages = Message::find()->where(['fromUserUuid' => $model['uuid']])->count();
        $user_property['messages'] = $user_messages;
        $user_attributes = Gpstrack::find()->where(['userUuid' => $model['uuid']])->count();
        $user_property['tracks'] = $user_attributes;
        $user_property['location'] = MainFunctions::getLocationByUser($model, true);

        $events = [];
        $measures = Measure::find()->where(['=', 'userUuid', $model['uuid']])->all();
        foreach ($measures as $measure) {
            $text = '<a class="btn btn-default btn-xs">' . $measure['equipment']['equipmentType']->title . '</a><br/>
                <i class="fa fa-cogs"></i>&nbsp;Значения: ' . $measure['value'] . '<br/>';
            $events[] = ['date' => $measure['date'], 'event' => self::formEvent($measure['date'], 'measure',
                $measure['_id'], $measure['equipment']['equipmentType']->title, $text)];
        }

        $journals = Journal::find()->where(['=', 'userUuid', $model['uuid']])->limit(5)->all();
        foreach ($journals as $journal) {
            $text = $journal['description'];
            $events[] = ['date' => $journal['date'], 'event' => self::formEvent($journal['date'], 'journal', 0,
                $journal['description'], $text)];
        }

        $sort_events = MainFunctions::array_msort($events, ['date' => SORT_DESC]);

        // вкладка со свойствами пользователя
        $userArm = new UserArm();
        $userArm->scenario = UserArm::SCENARIO_UPDATE;
        $userArm->load($model->user->attributes, '');
        $userArm->load($model->attributes, '');
        if ($model->type == Users::USERS_WORKER) {
            list($tagType, $pin) = explode(':', $userArm->pin);
            $userArm->tagType = $tagType;
            $userArm->pin = $pin;
        }

        $am = Yii::$app->getAuthManager();
        $roles = $am->getRoles();
        $roleList = ArrayHelper::map($roles, 'name', 'description');
        $assignments = $am->getAssignments($model->user_id);
        foreach ($assignments as $value) {
            $userArm->role = $value->roleName;
            break;
        }

        return $this->render(
            'view',
            [
                'model' => $model,
                'user_property' => $user_property,
                'events' => $sort_events,
                'userArm' => $userArm,
                'roleList' => $roleList,
            ]
        );
    }

    /**
     * Creates a new Users model.
     * If creation is successful, the browser will be redirected to the 'view' page.
     *
     * @return mixed
     * @throws InvalidConfigException
     */
    /**
     * @return string
     * @throws Throwable
     */
    public function actionCreate()
    {
        parent::actionCreate();

        $model = new UserArm();
        $am = Yii::$app->getAuthManager();
        $existUser = User::find()->all();
        $login = 'user' . (count($existUser) + 1);
        $model->username = $login;
        $model->email = $login . '@' . time() . '.ru';
        if ($model->load(Yii::$app->request->post()) && $model->validate()) {
            $user = new User();
            $user->username = $model->username;
            $user->auth_key = Yii::$app->security->generateRandomString();
            $user->password_hash = Yii::$app->security->generatePasswordHash($model->password);
            $user->email = $model->email;
            if ($user->save()) {
                $users = new Users();
                $users->uuid = MainFunctions::GUID();
                $users->name = $model->name;
                $users->type = $model->type;
                $users->pin = $users->type == Users::USERS_WORKER ? $model->tagType . ':' . $model->pin : '-';
                $users->active = 1;
                $users->whoIs = $model->whoIs;
                $users->contact = $model->contact;
                $users->user_id = $user->id;
                $users->image = '';
                $users->oid = Users::getCurrentOid();
                if ($users->validate() && $users->save()) {
                    $newRole = $am->getRole($model->role);
                    $am->assign($newRole, $users->user_id);
                    MainFunctions::register('user', 'Добавлен пользователь ' . $model->name, $model->contact);
                    $contractor = new Contragent();
                    $contractor->uuid = MainFunctions::GUID();
                    $contractor->oid = Users::getCurrentOid();
                    $contractor->title = $users->name;
                    $contractor->address = '-';
                    $contractor->phone = $users->contact;
                    $contractor->inn = '-';
                    $contractor->account = '-';
                    $contractor->director = '-';
                    $contractor->email = $user->email;
                    $contractor->contragentTypeUuid = ContragentType::WORKER;
                    if (!$contractor->save()) {
                        // TODO: решить что делать! удалять всё или нет.
                        $errorString = '';
                        foreach ($contractor->errors as $error) {
                            $errorString .= $error;
                        }

                        MainFunctions::register('нет', 'Создание пользователя',
                            'Не удалось создать контрагента. Error: ' . $errorString);
                    } else {
                        $userContractor = new UserContragent();
                        $userContractor->uuid = MainFunctions::GUID();
                        $userContractor->oid = Users::getCurrentOid();
                        $userContractor->userUuid = $users->uuid;
                        $userContractor->contragentUuid = $contractor->uuid;
                        if (!$userContractor->save()) {
                            $errorString = '';
                            foreach ($contractor->errors as $error) {
                                $errorString .= $error;
                            }

                            MainFunctions::register('нет', 'Создание пользователя',
                                'Не удалось создать связь между пользователем и контрагентом. Error: ' . $errorString);
                        }
                    }

                    return $this->redirect(['/users/view', 'id' => $users->_id]);
                } else {
                    $user->delete();
                }
            }
        }

        $roles = $am->getRoles();
        $roleList = ArrayHelper::map($roles, 'name', 'description');
        return $this->render('create', [
            'userArm' => $model,
            'model' => new Users(),
            'roleList' => $roleList,
        ]);
    }

    /**
     * @return mixed
     * @throws InvalidConfigException
     * @throws \yii\db\Exception
     */
    public function actionDashboard()
    {
        $users = Users::find()
            ->where('name!="sUser"')
            ->orderBy('createdAt DESC')
            ->all();
        $user_property[][] = '';
        $count = 0;
        foreach ($users as $user) {
            $user_photo = Photo::find()->where(['userUuid' => $user['uuid']])->count();
            $user_property[$count]['photos'] = $user_photo;
//            $user_alarms = Alarm::find()->where(['userUuid' => $user['uuid']])->count();
//            $user_property[$count]['alarms'] = $user_alarms;
//            $user_messages = Message::find()->where(['userUuid' => $user['uuid']])->count();
//            $user_property[$count]['messages'] = $user_messages;

            $user_measure = Measure::find()->where(['userUuid' => $user['uuid']])->
            andWhere('date > NOW() - INTERVAL 7 DAY')->count();
            $user_property[$count]['measure'] = $user_measure;
            $user_systems = UserSystem::find()->where(['userUuid' => $user['uuid']])->all();
            $user_property[$count]['systems'] = "";
            foreach ($user_systems as $user_system) {
                $user_property[$count]['systems'] .=
                    '<span class="pull-right badge bg-blue">' . $user_system['equipmentSystem']['titleUser'] . '</span>';
            }
            $user_houses = UserHouse::find()->where(['userUuid' => $user['uuid']])->count();
            $user_property[$count]['alarms'] = $user_houses;

            $user_tracks = Gpstrack::find()->where(['userUuid' => $user['uuid']])->count();
            $user_property[$count]['tracks'] = $user_tracks;

            $user_tasks = TaskUser::find()->where(['userUuid' => $user['uuid']])->count();
            $user_property[$count]['tasks'] = $user_tasks;

            $count++;
        }
        return $this->render('dashboard', [
            'users' => $users,
            'user_property' => $user_property
        ]);
    }

    /**
     * Build tree of equipment by user
     * @return mixed
     * @throws InvalidConfigException
     * @throws \yii\db\Exception
     * @throws Exception
     */
    public function actionTable()
    {
        if (isset($_POST['editableAttribute'])) {
            $model = Users::find()
                ->where(['_id' => $_POST['editableKey']])
                ->one();
            if ($_POST['editableAttribute'] == 'type') {
                $am = Yii::$app->getAuthManager();
                $am->revokeAll($model['user_id']);
                $newRole = $am->getRole($model->role);
                $am->assign($newRole, $model['user_id']);
                return "huy";
            }
            if ($_POST['editableAttribute'] == 'active') {
                if ($_POST['Users'][$_POST['editableIndex']]['active'] == true)
                    $model['active'] = 1;
                else $model['active'] = 0;
                $model->save();
                return json_encode("hui2");
            }
        }
        $searchModel = new UsersSearch();
        $dataProvider = $searchModel->search(Yii::$app->request->queryParams);
        $dataProvider->pagination->pageSize = 15;
        return $this->render(
            'table',
            [
                'searchModel' => $searchModel,
                'dataProvider' => $dataProvider,
            ]
        );
    }

    /**
     * Updates an existing Users model.
     * If update is successful, the browser will be redirected to the 'view' page.
     *
     * @param integer $id Id.
     *
     * @return mixed
     * @throws NotFoundHttpException
     * @throws InvalidConfigException
     * @throws HttpException
     * @throws Exception
     */
    public function actionUpdate($id)
    {
        parent::actionUpdate($id);
        $am = Yii::$app->getAuthManager();

        $users = $this->findModel($id);

        $model = new UserArm();
        $model->load($users->user->attributes, '');
        $model->load($users->attributes, '');
        $model->scenario = UserArm::SCENARIO_UPDATE;
        if ($model->load(Yii::$app->request->post())) {
            $user = $users->user;
            $user->load($model->attributes, '');
            if (!empty($model->password)) {
                $user->setPassword($model->password);
            }

            if ($user->save()) {
                MainFunctions::register('user', 'Обновлен профиль пользователя ' . $user->username, '');
            }

            $users->load($model->attributes, '');
            if ($users->type == Users::USERS_WORKER) {
                $users->pin = $model->tagType . ':' . $model->pin;
            }

            if ($users->save()) {
                $am->revokeAll($users->user_id);
                $newRole = $am->getRole($model->role);
                $am->assign($newRole, $users->user_id);
                MainFunctions::register('users', 'Обновлен профиль пользователя ' . $users->name, '');
                return $this->redirect(['/users/view', 'id' => $users->_id]);
            }

            $assignments = $am->getAssignments($users->user_id);
            foreach ($assignments as $value) {
                $model->role = $value->roleName;
                break;
            }
        }

        if ($model->type == Users::USERS_WORKER) {
            list($tagType, $pin) = explode(':', $model->pin);
            $model->tagType = $tagType;
            $model->pin = $pin;
        }

        $roles = $am->getRoles();
        $roleList = ArrayHelper::map($roles, 'name', 'description');
        $assignments = $am->getAssignments($users->user_id);
        foreach ($assignments as $value) {
            $model->role = $value->roleName;
            break;
        }

        return $this->render('update', [
            'userArm' => $model,
            'model' => $users,
            'roleList' => $roleList,
        ]);
    }

    /**
     * Deletes an existing Users model.
     * If deletion is successful, the browser will be redirected to the 'index' page.
     *
     * @param integer $id Id.
     *
     * @return mixed
     * @throws NotFoundHttpException
     * @throws Throwable
     * @throws StaleObjectException
     */
    public function actionDelete($id)
    {
        parent::actionDelete($id);

        $this->findModel($id)->delete();

        return $this->redirect(['index']);
    }

    /**
     * Finds the Users model based on its primary key value.
     * If the model is not found, a 404 HTTP exception will be thrown.
     *
     * @param integer $id Id.
     *
     * @return Users the loaded model
     * @throws NotFoundHttpException if the model cannot be found
     */
    protected function findModel($id)
    {
        if (($model = Users::findOne($id)) !== null) {
            return $model;
        } else {
            throw new NotFoundHttpException('The requested page does not exist.');
        }
    }

    /**
     * Сохраняем файл согласно нашим правилам.
     *
     * @param Users $model Пользователь
     * @param UploadedFile $file Файл
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
     * Displays a equipment register
     *
     * @return mixed
     * @throws InvalidConfigException
     * @throws \yii\db\Exception
     */
    public function actionTimeline()
    {
        $events = [];

        $journals = Journal::find()->orderBy('date DESC')->all();
        foreach ($journals as $journal) {
            $events[] = ['date' => $journal['date'], 'event' => self::formEventUsers($journal['date'], $journal['type'],
                $journal['user']['name'], $journal['title'], $journal['description'])];
        }
        $photos = Photo::find()
            ->limit(5)
            ->all();
        foreach ($photos as $photo) {
            $text = '<a class="btn btn-default btn-xs">' . $photo['equipment']['title'] . '</a><br/>';
            $events[] = ['date' => $photo['createdAt'], 'event' => self::formEventUsers($photo['createdAt'], 'photo',
                $photo['user']['name'], 'Добавлено фото', $text)];
        }

        $measures = Measure::find()
            ->all();
        foreach ($measures as $measure) {
            $text = '<a class="btn btn-default btn-xs">' . $measure['equipment']->title . '</a><br/>
                <i class="fa fa-bar-chart"></i>&nbsp;Значения: ' . $measure['value'] . '<br/>';
            $events[] = ['date' => $measure['date'], 'event' => self::formEvent($measure['date'], 'measure',
                $measure['user']['name'], $measure['equipment']['equipmentType']->title, $text)];
        }

        return $this->render(
            'timeline',
            [
                'events' => $events
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
     *
     * @return string
     */
    public static function formEvent($date, $type, $id, $title, $text)
    {
        $event = '<li>';
        if ($type == 'measure')
            $event .= '<i class="fa fa-wrench bg-red"></i>';
        if ($type == 'journal')
            $event .= '<i class="fa fa-calendar bg-aqua"></i>';

        $event .= '<div class="timeline-item">';
        $event .= '<span class="time"><i class="fa fa-clock-o"></i> ' . date("M j, Y h:m", strtotime($date)) . '</span>';
        if ($type == 'measure')
            $event .= '<h3 class="timeline-header">' . Html::a('Оператор снял данные &nbsp;',
                    ['/measure/view', 'id' => Html::encode($id)]) . $title . '</h3>';
        if ($type == 'journal')
            $event .= '<h3 class="timeline-header"><a href="#">Добавлено событие журнала</a></h3>';

        $event .= '<div class="timeline-body">' . $text . '</div>';
        $event .= '</div></li>';
        return $event;
    }

    /**
     * Формируем код записи о событии
     * @param $date
     * @param $type
     * @param $user
     * @param $title
     * @param $text
     *
     * @return string
     */
    public static function formEventUsers($date, $type, $user, $title, $text)
    {
        // create/change
        // alarm
        // documentation
        // equipment
        // object
        // request
        // user_system
        // complete/create
        // task
        // measure
        // photo

        $event = '<li>';
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
            $event .= '<i class="fa fa-bar bg-success"></i>';
        if ($type == 'photo')
            $event .= '<i class="fa fa-photo bg-aqua"></i>';
        if ($type == 'task')
            $event .= '<i class="fa fa-tasks bg-info"></i>';

        $event .= '<div class="timeline-item">';
        $event .= '<span class="time"><i class="fa fa-clock-o"></i> ' . date("M j, Y h:m", strtotime($date)) . '</span>';
        $event .= '<h3 class="timeline-header"><a class="btn btn-default btn-xs">' . $user . '</a> ' . $title . '</h3>';
        $event .= '<div class="timeline-body">' . $text . '</div>';
        $event .= '</div></li>';
        return $event;
    }

    /**
     *
     * @return mixed
     * @throws InvalidConfigException
     * @throws StaleObjectException
     * @throws Throwable
     * @throws \yii\db\Exception
     */
    public function actionAddSystem()
    {
        $model = new UserSystem();
        if (isset($_POST["equipmentSystemUuid"]) && isset($_POST["userUuid"])) {
            $model->uuid = MainFunctions::GUID();
            $model->equipmentSystemUuid = $_POST["equipmentSystemUuid"];
            $model->userUuid = $_POST["userUuid"];
            $model->oid = Users::getCurrentOid();

            $userSystem = UserSystem::find()->where(['userUuid' => $_POST["userUuid"]])
                ->andWhere(['equipmentSystemUuid' => $_POST["equipmentSystemUuid"]])
                ->one();
            if (!$userSystem) {
                $model->save();
            }
        } else {
            if (isset($_GET["userUuid"])) {
                $model->userUuid = $_GET["userUuid"];
            }
            return $this->renderAjax('_add_system', ['model' => $model]);
        }

        if (isset($_POST["userUuid"])) {
            $userSystems = UserSystem::find()->where(['userUuid' => $_POST['userUuid']])->all();
            foreach ($userSystems as $userSystem) {
                $id = 'system-' . $userSystem['_id'];
                if (isset($_POST[$id]) && ($_POST[$id] == 1 || $_POST[$id] == "1")) {
                    self::checkAddUserSystem($userSystem['equipmentSystemUuid'], $_POST['userUuid'], false);
                }
            }
        }
        return null;
    }

    /**
     * @param $systemUuid
     * @param $userUuid
     * @param $add
     * @throws InvalidConfigException
     * @throws StaleObjectException
     * @throws Throwable
     * @throws \yii\db\Exception
     */
    public function checkAddUserSystem($systemUuid, $userUuid, $add)
    {
        $equipmentUserPresent = UserSystem::find()->where(['equipmentSystemUuid' => $systemUuid])
            ->andWhere(['userUuid' => $userUuid])
            ->one();
        if ($equipmentUserPresent && $add == false) {
            $equipmentUserPresent->delete();
        }
    }
}
