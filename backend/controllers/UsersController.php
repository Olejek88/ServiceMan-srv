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
use common\models\Alarm;
use common\models\AlarmStatus;
use common\models\Defect;
use common\models\EquipmentRegister;
use common\models\Gpstrack;
use common\models\Journal;
use common\models\Measure;
use common\models\Message;
use common\models\Orders;
use common\models\OrderStatus;
use common\models\PhotoEquipment;
use common\models\PhotoFlat;
use common\models\PhotoHouse;
use common\models\Task;
use common\models\TaskStatus;
use common\models\User;
use common\models\UsersAttribute;
use Yii;
use yii\helpers\Html;
use yii\web\Controller;
use yii\web\NotFoundHttpException;
use yii\filters\VerbFilter;
use yii\web\UploadedFile;
use yii\web\UnauthorizedHttpException;
use api\controllers\TokenController;
use common\models\Users;
use common\models\Token;
use backend\models\UsersSearch;

/**
 * UsersController implements the CRUD actions for Users model.
 *
 * @category Category
 * @package  Backend\controllers
 * @author   Максим Шумаков <ms.profile.d@gmail.com>
 * @license  http://www.yiiframework.com/license/ License name
 * @link     http://www.toirus.ru
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
     */
    public function actionView($id)
    {
        $model = $this->findModel($id);
        $oldImage = $model->image;
        // сохраняем старое значение image
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

            // FIXME: !!!! почему обновление записи происходит в методе view вместо update?!
            if ($model->save()) {
                MainFunctions::register('Обновлен профиль пользователя ' . $model->name);
                return $this->redirect(['view', 'id' => $model->id]);
            } else
                return $this->redirect(['view', 'id' => $model->id]);
        }

        $user = $this->findModel($id);
        if ($user) {
            $user_photo = PhotoHouse::find()->where(['userUuid' => $user['uuid']])->count() +
                PhotoEquipment::find()->where(['userUuid' => $user['uuid']])->count() +
                PhotoFlat::find()->where(['userUuid' => $user['uuid']])->count();
            $user_property['photo'] = $user_photo;
            $user_measure = Measure::find()->where(['userUuid' => $user['uuid']])->count();
            $user_property['measure'] = $user_measure;
            $user_alarm = Alarm::find()->where(['userUuid' => $user['uuid']])->count();
            $user_property['alarms'] = $user_alarm;
            $user_attributes = Gpstrack::find()->where(['userUuid' => $user['uuid']])->count();
            $user_property['tracks'] = $user_attributes;
            $user_property['location'] = MainFunctions::getLocationByUser($user, true);

            $events=[];
            $measures = Measure::find()
                ->where(['=','userUuid', $user['uuid']])
                ->all();
            foreach ($measures as $measure) {
                $text = '<a class="btn btn-default btn-xs">'.$measure['equipment']->title.'</a><br/>
                <i class="fa fa-cogs"></i>&nbsp;Значения: ' . $measure['measure']->value . '<br/>';
                $events[]=['date' => $measure['date'],'event' => self::formEvent($measure['date'],'measure',
                    $measure['_id'], $measure['equipment']->title, $text)];
            }
            $journals = Journal::find()
                ->where(['=','userUuid', $user['uuid']])
                ->limit(5)
                ->all();
            foreach ($journals as $journal) {
                $text = $journal['description'];
                $events[]=['date' => $journal['date'],'event' => self::formEvent($journal['date'],'journal', 0,
                    $journal['description'], $text)];
            }

            $sort_events = MainFunctions::array_msort($events, ['date'=>SORT_DESC]);
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
                MainFunctions::register('Добавлен пользователь ' . $model->name);
                return $this->redirect(['view', 'id' => $model->id]);
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
        $users = Users::find()->orderBy('createdAt DESC')->all();
        $count=0;
        $user_property[][]='';
        foreach ($users as $user) {
            $user_orders = Orders::find()->where(['userUuid' => $user['uuid']])->count();
            $user_property[$count]['orders']=$user_orders;
            $user_defects = Defect::find()->where(['userUuid' => $user['uuid']])->count();
            $user_property[$count]['defects']=$user_defects;
            $user_messages = Message::find()->where(['toUserUuid' => $user['uuid']])->count();
            $user_property[$count]['messages']=$user_messages;
            $count++;
        }
        return $this->render('dashboard', [
            'users'  => $users,
            'user_property'  => $user_property
        ]);
    }

    /**
     * Updates an existing Users model.
     * If update is successful, the browser will be redirected to the 'view' page.
     *
     * @param integer $id Id.
     *
     * @return mixed
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
                MainFunctions::register('Обновлен профиль пользователя ' . $model->name);
                return $this->redirect(['view', 'id' => $model->id]);
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
     * @param Users        $model Пользователь
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

}
