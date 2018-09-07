<?php
/**
 * PHP Version 7.0
 *
 * @category Category
 * @package  Api\controllers
 * @author   Максим Шумаков <ms.profile.d@gmail.com>
 * @license  http://www.yiiframework.com/license/ License name
 * @link     http://www.toirus.ru
 */

namespace api\old\controllers;

use Yii;
use yii\rest\ActiveController;
use yii\web\Response;
use yii\web\UnauthorizedHttpException;
use common\models\Equipment;

/**
 * PHP Version 7.0
 *
 * @category Category
 * @package  Api\controllers
 * @author   Максим Шумаков <ms.profile.d@gmail.com>
 * @license  http://www.yiiframework.com/license/ License name
 * @link     http://www.toirus.ru
 */
class EquipmentController extends ActiveController
{
    public $modelClass = 'app\models\Equipment';

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
     * @return Equipment[]
     */
    public function actionIndex()
    {
        Yii::$app->response->format = Response::FORMAT_JSON;

        // проверяем параметры запроса
        $req = \Yii::$app->request;
        $query = Equipment::find();

        $id = $req->getQueryParam('id');
        if ($id != null) {
            $query->andWhere(['_id' => $id]);
        }

        $uuid = $req->getQueryParam('uuid');
        if ($uuid != null) {
            $query->andWhere(['uuid' => $uuid]);
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

    /**
     * Установка ид метки для единицы оборудования.
     *
     * @return boolean В случае успеха возвращает true
     */
    public function actionSetTag()
    {
        Yii::$app->response->format = Response::FORMAT_JSON;

        // проверяем параметры запроса
        $req = \Yii::$app->request;
        $query = Equipment::find();

        // uuid оборудования
        $uuid = $req->getQueryParam('uuid');
        if ($uuid != null) {
            $query->andWhere(['uuid' => $uuid]);
        } else {
            return false;
        }

        // выбираем данные из базы
        $equipment = $query->one();
        if ($equipment == null) {
            return false;
        }

        // ID метки
        $tagId = $req->getQueryParam('tagId');
        if ($tagId == null) {
            return false;
        }

        // проверяем на наличие такой метки в базе
        $testEquipment = Equipment::find()->where(['tagId' => $tagId])->one();
        if ($testEquipment != null) {
            return false;
        }

        $equipment->tagId = $tagId;
        if (!$equipment->save()) {
            return false;
        } else {
            return true;
        }
    }
}
