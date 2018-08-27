<?php
/**
 * PHP Version 7.0
 *
 * @category Category
 * @package  Api\controllers
 * @author   Дмитрий Логачев <demonwork@yandex.ru>
 * @license  http://www.yiiframework.com/license/ License name
 * @link     http://www.toirus.ru
 */

namespace api\controllers;

use Yii;
use yii\rest\ActiveController;
use yii\web\UnauthorizedHttpException;
use common\models\Gpstrack;
use yii\web\Response;
use common\models\Users;
use common\models\Journal;
use common\models\Token;

/**
 * PHP Version 7.0
 *
 * @category Category
 * @package  Api\controllers
 * @author   Дмитрий Логачев <demonwork@yandex.ru>
 * @license  http://www.yiiframework.com/license/ License name
 * @link     http://www.toirus.ru
 */
class GpstrackController extends ActiveController
{
    public $modelClass = 'common\models\Gpstrack';

    /**
     * Init
     *
     * @throws UnauthorizedHttpException
     * @return void
     */
    public function init()
    {
        Yii::$app->response->format = Response::FORMAT_JSON;

        $token = TokenController::getTokenString(Yii::$app->request);
        // проверяем авторизацию пользователя
        if (!TokenController::isTokenValid($token)) {
            throw new UnauthorizedHttpException();
        }
    }

    /**
     * Actions
     *
     * @return array
     */
    public function actions()
    {
        $actions = parent::actions();
        unset($actions['index']);
        unset($actions['create']);
        return $actions;
    }

    /**
     * Index
     *
     * @return Journal[]
     */
    public function actionIndex()
    {
        Yii::$app->response->format = Response::FORMAT_JSON;

        /**
         * [$online, $offline, $gpsOn, $gps description]
         *
         * @var array online  - Список пользователей активных в течении 5 минут
         * @var array offline - Список пользователей не активных в течении 5 минут
         * @var array gpsOn   - Список геоданных по онлайн пользователям
         * @var array gps     - Список геоданных по оффлайн пользователям
         */
        $online = [];
        $offline = [];
        $gpsOn = 0;
        $gps = 0;

        $userGet = Token::find()
            ->select('tagId, issued')
            ->where('issued >= CURDATE()')
            ->all();

        foreach ($userGet as $keys => $val) {
            $users[] = Users::find()
                ->select('uuid, connectionDate')
                ->where(['tagId' => $val['tagId']])
                ->one();

            $userList[] = $users[$keys];

            /**
             * [userList description]
             *
             * @var array userList        - Список активных пользователей за сутки
             * @var string uuid           - Uuid пользователя
             * @var string connectionDate - Дата последнего соединения
             */
            $today = time();
            $userUnix[] = strtotime($userList[$keys]->connectionDate);
            $threshold = $today - 300;

            if ($userUnix[$keys] >= $threshold) {
                if (isset($online) && is_array($online)) {
                    $online[] = $userList[$keys]->uuid;
                }
            } else {
                if (isset($offline) && is_array($offline)) {
                    $offline[] = $userList[$keys]->uuid;
                }
            }

            if (count($online) >= 1) {
                $listOnline = count($online) - 1;
                $gpsOn = Gpstrack::find()
                    ->select('latitude, longitude, date')
                    ->where(['userUuid' => $online[$listOnline]])
                    ->all();
            }

            if (count($userList) >= 1) {
                $list = count($userList) - 1;
                $gps = Gpstrack::find()
                    ->select('userUuid, latitude, longitude, date')
                    ->where('date >= CURDATE()')
                    ->asArray()
                    ->all();
            }
        }

        $result = $gps;
        return $result;
    }

    /**
     * Action create
     *
     * @return array
     */
    public function actionCreate()
    {
        Yii::$app->response->format = Response::FORMAT_JSON;

        $success = true;
        $saved = array();
        $rawData = Yii::$app->getRequest()->getRawBody();
        $items = json_decode($rawData, true);
        foreach ($items as $item) {
            $line = new Gpstrack();
            $line->setAttributes($item);
            $line->userUuid = $item['userUuid'];
            if ($line->save()) {
                $saved[] = [
                    '_id' => $item['_id'],
                    'uuid' => $item['userUuid'],
                ];
            } else {
                $success = false;
            }
        }

        return ['success' => $success, 'data' => $saved];
    }
}
