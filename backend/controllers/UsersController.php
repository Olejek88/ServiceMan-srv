<?php

namespace backend\controllers;

use backend\models\UserArm;
use backend\models\UsersSearch;
use common\components\MainFunctions;
use common\components\Tag;
use common\models\Alarm;
use common\models\Contragent;
use common\models\ContragentType;
use common\models\Gpstrack;
use common\models\Journal;
use common\models\Measure;
use common\models\Message;
use common\models\Photo;
use common\models\TaskUser;
use common\models\User;
use common\models\UserContragent;
use common\models\UserHouse;
use common\models\Users;
use common\models\UserSystem;
use Exception;
use Throwable;
use Yii;
use yii\base\InvalidConfigException;
use yii\db\StaleObjectException;
use yii\helpers\ArrayHelper;
use yii\helpers\Html;
use yii\web\HttpException;
use yii\web\NotFoundHttpException;
use yii\web\UploadedFile;

/**
 * UsersController implements the CRUD actions for Users model.
 */
class UsersController extends ZhkhController
{
    protected $modelClass = Users::class;

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
        /*
        $searchModel = new UsersSearch();
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
        if ($model->user_id == Yii::$app->user->id ||
            Yii::$app->user->can(User::ROLE_ADMIN)) {
        } else {
            $this->redirect('index');
        }

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
        try {
            list($tagType, $pin) = explode(':', $userArm->pin);
            $userArm->tagType = $tagType;
            $userArm->pin = $pin;
        } catch (Exception $e) {
            $userArm->tagType = Tag::TAG_TYPE_PIN;
            $userArm->pin = '';
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
            $user->status = $model->status;
            if ($user->save()) {
                $users = new Users();
                $users->uuid = MainFunctions::GUID();
                $users->name = $model->name;
                $users->type = $model->type;
                if (in_array($users->type, [Users::USERS_WORKER, Users::USERS_ARM_WORKER])) {
                    $users->pin = $model->tagType . ':' . $model->pin;
                } else {
                    $users->pin = Tag::TAG_TYPE_PIN . ':';
                }

                $users->active = 1;
                $users->whoIs = $model->whoIs;
                $users->contact = $model->contact;
                $users->user_id = $user->id;
                $users->image = '';
                $users->oid = Users::getCurrentOid();
                if ($users->validate() && $users->save()) {
                    $newRole = $am->getRole($model->role);
                    $am->assign($newRole, $users->user_id);
                    MainFunctions::register('user', 'Добавлен пользователь ' . $model->name, $model->contact, $users->uuid);
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
                            'Не удалось создать контрагента. Error: ' . $errorString, "");
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
                                'Не удалось создать связь между пользователем и контрагентом. Error: ' . $errorString, "");
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
            $message = '';
            $role = Yii::$app->request->getBodyParam('role');
            if ($role != null) {
                if (in_array($role, ['admin', 'operator', 'dispatch', 'director'])) {
                    $am = Yii::$app->getAuthManager();
                    $am->revokeAll($model['user_id']);
                    $newRole = $am->getRole($role);
                    $output = self::formatRole($newRole->name);
                    $am->assign($newRole, $model['user_id']);
                } else {
                    $output = self::formatRole($role->name);
                    $message = 'Указано не верное значение.';
                }
            }

            $attribute = Yii::$app->request->getBodyParam('editableAttribute');
            if ($attribute == 'active') {
                if ($_POST['Users'][$_POST['editableIndex']]['active'] == User::STATUS_ACTIVE) {
                    $model['active'] = User::STATUS_ACTIVE;
                    $model->user->status = User::STATUS_ACTIVE;
                    $output = '<span class="glyphicon glyphicon-ok text-success"></span>';
                } else if ($_POST['Users'][$_POST['editableIndex']]['active'] == User::STATUS_DELETED) {
                    $model['active'] = User::STATUS_DELETED;
                    $model->user->status = User::STATUS_DELETED;
                    $output = '<span class="glyphicon glyphicon-remove text-danger"></span>';
                } else {
                    $message = 'Указано не верное значение.';
                }

                if (empty($message)) {
                    $model->save();
                    $model->user->save();
                }
            }

            if ($message != '') {
                return json_encode(['message' => $message]);
            } else {
                return json_encode(['output' => $output]);
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

    public static function formatRole($roleName)
    {
        $string = '';
        switch ($roleName) {
            case User::ROLE_ADMIN:
                $string = '<span class="label label-danger">Администратор</span>';
                break;
            case User::ROLE_OPERATOR:
                $string = '<span class="label label-success">Оператор</span>';
                break;
            case User::ROLE_DISPATCH:
                $string = '<span class="label label-info">Диспетчер</span>';
                break;
            case User::ROLE_DIRECTOR:
                $string = '<span class="label label-info">Директор</span>';
                break;
            default:
                $string = 'Неизвестная';
                break;
        }

        return $string;
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
        $users = $this->findModel($id);
        if ($users->user_id == Yii::$app->user->id || Yii::$app->user->can(User::ROLE_ADMIN)) {
        } else {
            Yii::$app->session->setFlash('error', '<h3>Не достаточно прав доступа.</h3>');
            $this->redirect('/');
        }

        $am = Yii::$app->getAuthManager();

        $roles = $am->getRoles();
        $roleList = ArrayHelper::map($roles, 'name', 'description');
        $assignments = $am->getAssignments($users->user_id);
        $model = new UserArm();
        $model->load($users->user->attributes, '');
        $model->load($users->attributes, '');
        $model->scenario = UserArm::SCENARIO_UPDATE;
        if ($model->load(Yii::$app->request->post())) {
            // загружаем данные из формы в моделе
            $user = $users->user;
            $user->load($model->attributes, '');
            if (!empty($model->password)) {
                $user->setPassword($model->password);
            }

            if ($user->save()) {
                MainFunctions::register('user', 'Обновлен профиль пользователя ' . $user->username,
                    '', $users->uuid);
            }

            $users->load($model->attributes, '');
            if (in_array($users->type, [Users::USERS_WORKER, Users::USERS_ARM_WORKER])) {
                $users->pin = $model->tagType . ':' . $model->pin;
            }

            $users->active = $user->status;

            if ($users->save()) {
                $am->revokeAll($users->user_id);
                $newRole = $am->getRole($model->role);
                $am->assign($newRole, $users->user_id);
                MainFunctions::register('users', 'Обновлен профиль пользователя ' . $users->name,
                    '', $users->uuid);
            } else {
                // прокинуть на форму с указанием ошибки
                $model->addError('type', $users->getFirstError('type') . ' (измените тип)');
                $model->addError('status', $users->getFirstError('type') . ' (измените статус)');
                return $this->render('update', [
                    'userArm' => $model,
                    'model' => $users,
                    'roleList' => $roleList,
                ]);
            }

            if ($user->save()) {
                MainFunctions::register('users', 'Обновлен профиль пользователя ' . $users->name,
                    '', $users->uuid);
                return $this->redirect(['/users/view', 'id' => $users->_id]);
            }

            $assignments = $am->getAssignments($users->user_id);
            foreach ($assignments as $value) {
                $model->role = $value->roleName;
                break;
            }
        }

        try {
            list($tagType, $pin) = explode(':', $model->pin);
            $model->tagType = $tagType;
            $model->pin = $pin;
        } catch (Exception $e) {
            $model->tagType = Tag::TAG_TYPE_PIN;
            $model->pin = '';
        }

        // текущая роль пользователя
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

        $journals = Journal::find()
            ->leftJoin('{{%users}}', '{{%users}}.oid = \'' . Users::getCurrentOid() . '\'')
            ->orderBy('date DESC')
            ->all();
        foreach ($journals as $journal) {
            $events[] = ['date' => $journal['date'], 'event' => self::formEventUsers($journal['date'], $journal['type'],
                $journal['user']['name'], $journal['title'], $journal['description'])];
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
