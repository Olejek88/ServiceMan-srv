<?php

namespace backend\controllers;

use backend\models\UsersSearch;
use common\components\MainFunctions;
use common\models\Alarm;
use common\models\Gpstrack;
use common\models\Journal;
use common\models\Measure;
use common\models\Message;
use common\models\Photo;
use common\models\TaskUser;
use common\models\UserHouse;
use common\models\Users;
use common\models\UserSystem;
use Yii;
use yii\db\StaleObjectException;
use yii\filters\VerbFilter;
use yii\helpers\Html;
use yii\web\Controller;
use yii\web\NotFoundHttpException;
use yii\web\UnauthorizedHttpException;
use yii\web\UploadedFile;

/**
 * UsersController implements the CRUD actions for Users model.
 */
class UsersController extends Controller
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
     * Lists all Users models.
     *
     * @return mixed
     */
    public function actionIndex()
    {
        $searchModel = new UsersSearch();
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
     * Displays a single Users model.
     *
     * @param integer $id Id.
     *
     * @return mixed
     * @throws NotFoundHttpException
     */
    public function actionView($id)
    {
        $model = $this->findModel($id);
        $oldPin = $model->pin;
        $oldImage = $model->image;
        // сохраняем старое значение image
        if ($model->load(Yii::$app->request->post())) {
            // хешируем пин
            if ($oldPin != $model->pin) {
                $model->pin = md5($model->pin);
            }

            // получаем изображение для последующего сохранения
            $file = UploadedFile::getInstance($model, 'image');
            if ($file && $file->tempName) {
                $fileName = self::_saveFile($model, $file);
                if ($fileName) {
                    $model->image = $fileName;
                } else {
                    $model->image = $oldImage;
                    // уведомить пользователя, админа о невозможности сохранить файл
                }
            } else {
                $model->image = $oldImage;
            }

            // FIXME: !!!! почему обновление записи происходит в методе view вместо update?!
            if ($model->save()) {
                MainFunctions::register('user','Обновлен профиль пользователя ' . $model->name,'');
                return $this->redirect(['view', 'id' => $model->id]);
            } else
                return $this->redirect(['view', 'id' => $model->id]);
        }

        $user = $this->findModel($id);
        if ($user) {
            $user_photo = Photo::find()->where(['userUuid' => $user['uuid']])->count();
            $user_property['photo'] = $user_photo;
            $user_measure = Measure::find()->where(['userUuid' => $user['uuid']])->count();
            $user_property['measure'] = $user_measure;
            $user_alarm = Alarm::find()->where(['userUuid' => $user['uuid']])->count();
            $user_property['alarms'] = $user_alarm;
            $user_messages = Message::find()->where(['fromUserUuid' => $user['uuid']])->count();
            $user_property['messages'] = $user_messages;
            $user_attributes = Gpstrack::find()->where(['userUuid' => $user['uuid']])->count();
            $user_property['tracks'] = $user_attributes;
            $user_property['location'] = MainFunctions::getLocationByUser($user, true);

            $events = [];
            $measures = Measure::find()
                ->where(['=', 'userUuid', $user['uuid']])
                ->all();
            foreach ($measures as $measure) {
                $text = '<a class="btn btn-default btn-xs">' . $measure['equipment']['equipmentType']->title . '</a><br/>
                <i class="fa fa-cogs"></i>&nbsp;Значения: ' . $measure['value'] . '<br/>';
                $events[] = ['date' => $measure['date'], 'event' => self::formEvent($measure['date'], 'measure',
                    $measure['_id'], $measure['equipment']['equipmentType']->title, $text)];
            }
            $journals = Journal::find()
                ->where(['=', 'userUuid', $user['uuid']])
                ->limit(5)
                ->all();
            foreach ($journals as $journal) {
                $text = $journal['description'];
                $events[] = ['date' => $journal['date'], 'event' => self::formEvent($journal['date'], 'journal', 0,
                    $journal['description'], $text)];
            }

            $sort_events = MainFunctions::array_msort($events, ['date' => SORT_DESC]);
            return $this->render(
                'view',
                [
                    'model' => $user,
                    'user_property' => $user_property,
                    'events' => $sort_events
                ]
            );
        }
    }

    /**
     * Creates a new Users model.
     * If creation is successful, the browser will be redirected to the 'view' page.
     *
     * @return mixed
     */
    public function actionCreate()
    {
        $model = new Users();

        if ($model->load(Yii::$app->request->post())) {
            // проверяем все поля, если что-то не так показываем форму с ошибками

            if (!$model->validate()) {
                return $this->render('create', ['model' => $model]);
            }

            // хешируем пин
            $model->pin = md5($model->pin);

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
            if ($model->load(Yii::$app->request->post()) && $model->save()) {
                MainFunctions::register('user','Добавлен пользователь ' . $model->name, $model->contact);
                return $this->redirect(['view', 'id' => $model->_id]);
            } else {
                return $this->render('create', ['model' => $model]);
            }
        }
        return $this->render('create', ['model' => $model]);
    }

    /**
     * @return mixed
     */
    public function actionDashboard()
    {
        $users = Users::find()->where('name!="sUser"')->orderBy('createdAt DESC')->all();
        $user_property[][] = '';
        $count = 0;
        foreach ($users as $user) {
            $user_systems = UserSystem::find()->where(['userUuid' => $user['uuid']])->all();
            $user_property[$count]['systems'] = "";
            foreach ($user_systems as $user_system) {
                $user_property[$count]['systems'] .=
                    '<span class="pull-right badge bg-blue">'.$user_system['equipmentSystem']['titleUser'].'</span>';
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
     */
    public function actionTable()
    {
        if (isset($_POST['editableAttribute'])) {
            $model = Users::find()
                ->where(['_id' => $_POST['editableKey']])
                ->one();
            if ($_POST['editableAttribute'] == 'type') {
                $model['type'] = intval($_POST['Users'][$_POST['editableIndex']]['type']);
                if ($model['active'] == true) $model['active'] = 1;
                else $model['active'] = 0;
                $model->save();
                return json_encode($model->errors);
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
     */
    public function actionUpdate($id)
    {
        $model = $this->findModel($id);
        // сохраняем старое значение image
        $oldImage = $model->image;

        if ($model->load(Yii::$app->request->post())) {
            // получаем изображение для последующего сохранения
            $file = UploadedFile::getInstance($model, 'image');
            if ($file && $file->tempName) {
                $fileName = self::_saveFile($model, $file);
                if ($fileName) {
                    $model->image = $fileName;
                } else {
                    $model->image = $oldImage;
                    // уведомить пользователя, админа о невозможности сохранить файл
                }
            } else {
                $model->image = $oldImage;
            }

            if ($model->save()) {
                MainFunctions::register('user','Обновлен профиль пользователя ' . $model->name,'');
                //return $this->redirect(['view', 'id' => $model->_id]);
            } else {
                return $this->render(
                    'update',
                    [
                        'model' => $model,
                    ]
                );
            }
        }
        return $this->render(
            'update',
            [
                'model' => $model,
            ]
        );
    }

    /**
     * Deletes an existing Users model.
     * If deletion is successful, the browser will be redirected to the 'index' page.
     *
     * @param integer $id Id.
     *
     * @return mixed
     * @throws NotFoundHttpException
     * @throws \Throwable
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
     * Возвращает объект Users по токену.
     *
     * @param string $token Токен.
     *
     * @return Users Оъект пользователя.
     */
    public static function getUserByToken($token)
    {
        if (TokenController::isTokenValid($token)) {
            $tokens = Token::find()->where(['accessToken' => $token])->all();
            if (count($tokens) == 1) {
                $users = Users::find()->where(['tagId' => $tokens[0]->tagId])->all();
                $user = count($users) == 1 ? $users[0] : null;
                return $user;
            } else {
                // TODO: нужно выбросить подходящее исключение!!!!
                return null;
            }
        } else {
            return null;
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
        $event .= '<h3 class="timeline-header"><a class="btn btn-default btn-xs">'.$user.'</a> '. $title . '</h3>';
        $event .= '<div class="timeline-body">' . $text . '</div>';
        $event .= '</div></li>';
        return $event;
    }

    /**
     *
     * @return mixed
     */
    public function actionAddSystem()
    {
        $model = new UserSystem();
        if (isset($_POST["uuid"])) {
            if ($model->load(Yii::$app->request->post())) {
                if (!$model->validate()) {
                    echo json_encode($model->errors);
                }
                $userSystem = UserSystem::find()->where(['userUuid' => $model['userUuid']])
                    ->andWhere(['equipmentSystemUuid' => $model['equipmentSystemUuid']])
                    ->one();
                echo json_encode($userSystem);
                if (!$userSystem) {
                    $model->save();
                }
                exit(0);
                //return $this->render('dashboard');
            }
        } else {
            if (isset($_GET["userUuid"]))
                $model->userUuid = $_GET["userUuid"];
            return $this->renderAjax('_add_system', ['model' => $model]);
        }
    }
}
