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
use common\models\Defect;
use common\models\EquipmentRegister;
use common\models\Gpstrack;
use common\models\Journal;
use common\models\Message;
use common\models\Orders;
use common\models\OrderStatus;
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
                return $this->redirect(['view', 'id' => $model->_id]);
            } else
                return $this->redirect(['view', 'id' => $model->_id]);
        }

        $user = $this->findModel($id);
        if ($user) {
            $user_orders = Orders::find()->where(['userUuid' => $user['uuid']])->count();
            $user_property['orders'] = $user_orders;
            $user_defects = Defect::find()->where(['userUuid' => $user['uuid']])->count();
            $user_property['defects'] = $user_defects;
            $user_messages = Message::find()->where(['toUserUuid' => $user['uuid']])->count();
            $user_property['messages'] = $user_messages;
            $user_attributes = UsersAttribute::find()->where(['userUuid' => $user['uuid']])->count();
            $user_property['attributes'] = $user_attributes;
            $user_attributes = Gpstrack::find()->where(['userUuid' => $user['uuid']])->count();
            $user_property['tracks'] = $user_attributes;
            $user_property['location'] = MainFunctions::getLocationByUser($user, true);

            $events=[];
            $defects = Defect::find()
                ->where(['=','userUuid', $user['uuid']])
                ->all();
            foreach ($defects as $defect) {
                if ($defect['process']==0) $status='<a class="btn btn-success btn-xs">Исправлен</a>';
                else $status='<a class="btn btn-danger btn-xs">Активен</a>';
                $text = '<a class="btn btn-default btn-xs">'.$defect['equipment']->title.'</a>
                ' . $defect['comment'] . '<br/>
                <i class="fa fa-cogs"></i>&nbsp;Задача: ' . $defect['task']['taskTemplate']->title . '<br/>
                <i class="fa fa-check-square"></i>&nbsp;Статус: ' . $status . '';
                $events[]=['date' => $defect['date'],'event' => self::formEvent($defect['date'],'defect',$defect['_id'],
                    $defect['defectType']->title, $text)];
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

            $equipmentRegisters = EquipmentRegister::find()
                ->where(['=','userUuid', $user['uuid']])
                ->limit(10)
                ->all();
            foreach ($equipmentRegisters as $equipmentRegister) {
                $path = $equipmentRegister['equipment']->getImageUrl();
                if ($path==null)
                    $path = '/storage/order-level/no-image-icon-4.png';
                $text = '<img src="'.Html::encode($path).'" style="margin:5px; width:50px; margin: 2; float:left" alt="">';
                $text .= '<i class="fa fa-cogs"></i>&nbsp;<a class="btn btn-default btn-xs">'.$equipmentRegister['equipment']->title.'</a><br/>
                <i class="fa fa-clipboard"></i>&nbsp;Изменил параметр: <a class="btn btn-default btn-xs">'
                    .$equipmentRegister['fromParameterUuid'].'</a>&nbsp;&gt;&nbsp;
                    <a class="btn btn-default btn-xs">'.$equipmentRegister['toParameterUuid'].'</a>';
                $events[]=['date' => $equipmentRegister['date'],'event' => self::formEvent($equipmentRegister['date'],
                    'equipmentRegister', 0, '', $text)];
            }

            $usersAttributes = UsersAttribute::find()
                ->where(['=','userUuid', $user['uuid']])
                ->all();
            foreach ($usersAttributes as $usersAttribute) {
                $text = '<a class="btn btn-default btn-xs">Для пользователя зарегистрировано событие</a><br/>
                &nbsp;'.$usersAttribute['attributeType']->name.' <a class="btn btn-default btn-xs">'
                    .$usersAttribute['value'].'</a>';
                $events[]=['date' => $usersAttribute['date'],'event' => self::formEvent($usersAttribute['date'],
                    'usersAttribute', 0, '', $text)];
            }

            $orders = Orders::find()
                ->where(['=','userUuid', $user['uuid']])
                ->all();
            foreach ($orders as $order) {
                if ($order['openDate']>0) $openDate = date("j-d-Y h:m", strtotime($order['openDate']));
                else $openDate = 'не начинался';
                if ($order['closeDate']>0) $closeDate = date("j-d-Y h:m", strtotime($order['closeDate']));
                else $closeDate = 'не закончился';
                $text = 'Автор: <a class="btn btn-primary btn-xs">'.$order['author']->name.'</a><br/>
                <i class="fa fa-calendar"></i>&nbsp;Открыт: '.$openDate. '
                <i class="fa fa-calendar"></i>&nbsp;Закрыт: '.$closeDate.'<br/>';
                $text .= 'Основание: <a class="btn btn-default btn-xs">'.$order['reason'].'</a><br/>';
                if ($order['comment']) $text .= $order['comment'].'<br/>';
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
                $events[]=['date' => $order['startDate'],'event' => self::formEvent($order['startDate'],
                    'order', 0, $order['title'], $text)];
            }

            $messages = Message::find()
                ->where(['=','toUserUuid', $user['uuid']])
                ->limit(5)
                ->all();
            foreach ($messages as $message) {
                $text = 'От: <a class="btn btn-default btn-xs">'.$message['fromUser']->name.'</a><br/>'.$message['text'];
                $events[]=['date' => $message['date'],'event' => self::formEvent($message['date'],
                    'message', 0, '', $text)];
            }

            $orders = Orders::find()->where(['userUuid' => $user['uuid']])->orderBy('startDate')->all();
            $orderCount=0;
            $tree=[];
            foreach ($orders as $order) {
                $tasks = Task::find()
                    ->where(['orderUuid' => $order['uuid']])
                    ->all();
                $tree[$orderCount]='';
                foreach ($tasks as $task) {
                    if ($task['startDate'] > 0) $startDate = date("M j, Y", strtotime($task['startDate']));
                    else $startDate = 'не начиналась';
                    if ($task['endDate'] > 0) $endDate = date("M j, Y", strtotime($task['endDate']));
                    else $endDate = 'не закончилась';
                    switch ($task['taskStatus']) {
                        case TaskStatus::COMPLETE:
                            $tree[$orderCount] .= '<span class="label label-success">Закончен</span>&nbsp;';
                            break;
                        case TaskStatus::CANCELED:
                            $tree[$orderCount] .= '<span class="label label-danger">Отменен</span>&nbsp;';
                            break;
                        case TaskStatus::UN_COMPLETE:
                            $tree[$orderCount] .= '<span class="label label-warning">Не закончен</span>&nbsp;';
                            break;
                        case TaskStatus::NEW_TASK:
                            $tree[$orderCount] .= '<span class="label label-info">Не закончен</span>&nbsp;';
                            break;
                        default:
                            $tree[$orderCount] .= '<span class="label label-info">Не определен</span>&nbsp;';
                    }
                    $tree[$orderCount] .= $task['taskTemplate']->title.'&nbsp;<i class="fa fa-calendar"></i>
                        &nbsp;['.$startDate.'&nbsp;-&nbsp;'.$endDate.']<br/>';
                }
                $orderCount++;
            }

            $sort_events = MainFunctions::array_msort($events, ['date'=>SORT_DESC]);
            return $this->render(
                'view',
                [
                    'model' => $user,
                    'user_property' => $user_property,
                    'orders' => $orders,
                    'events' => $sort_events,
                    'tree' => $tree
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
                return $this->redirect(['view', 'id' => $model->_id]);
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
            $event .= '<i class="fa fa-envelope bg-blue"></i>';

        $event .= '<div class="timeline-item">';
        $event .= '<span class="time"><i class="fa fa-clock-o"></i> ' . date("M j, Y h:m", strtotime($date)) . '</span>';
        if ($type == 'defect')
            $event .= '<h3 class="timeline-header">' . Html::a('Пользователь зарегистрировал дефект &nbsp;',
                    ['/defect/view', 'id' => Html::encode($id)]) . $title . '</h3>';
        if ($type == 'journal')
            $event .= '<h3 class="timeline-header"><a href="#">Добавлено событие журнала</a></h3>';
        if ($type == 'equipmentRegister')
            $event .= '<h3 class="timeline-header">' . Html::a('Параметр оборудования изменен &nbsp;',
                    ['/equipment-register/view', 'id' => Html::encode($id)]) . $title . '</h3>';
        if ($type == 'usersAttribute')
            $event .= '<h3 class="timeline-header">' . Html::a('Изменен аттрибут пользователя &nbsp;',
                    ['/equipment-register/view', 'id' => Html::encode($id)]) . $title . '</h3>';
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
