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
use yii\web\Response;
use yii\web\UnauthorizedHttpException;
use common\models\EquipmentRegister;

/**
 * PHP Version 7.0
 *
 * @category Category
 * @package  Api\controllers
 * @author   Дмитрий Логачев <demonwork@yandex.ru>
 * @license  http://www.yiiframework.com/license/ License name
 * @link     http://www.toirus.ru
 */
class EquipmentRegisterController extends ActiveController
{
    public $modelClass = 'app\models\EquipmentRegister';

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
        return $actions;
    }

    /**
     * Index
     *
     * @return EquipmentRegister[]
     */
    public function actionIndex()
    {
        Yii::$app->response->format = Response::FORMAT_JSON;

        // проверяем параметры запроса
        $req = \Yii::$app->request;
        $query = EquipmentRegister::find();

        $id = $req->getQueryParam('id');
        if ($id != null) {
            $query->andWhere(['_id' => $id]);
        }

        $changedAfter = $req->getQueryParam('changedAfter');
        if ($changedAfter != null) {
            $query->andWhere(['>=', 'changedAt', $changedAfter]);
        }

        // проверяем что хоть какие-то условия были заданы
        if ($query->where == null) {
            return [];
        }

        // выбираем данные из базы
        $result = $query->all();
        return $result;
    }
}
