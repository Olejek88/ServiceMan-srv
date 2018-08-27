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

namespace api\controllers;

use Yii;
use yii\rest\ActiveController;
use yii\web\NotAcceptableHttpException;
use yii\web\Response;
use yii\web\UnauthorizedHttpException;
use common\models\Journal;

/**
 * Class JournalController
 *
 * @category Category
 * @package  Api\controllers
 * @author   Максим Шумаков <ms.profile.d@gmail.com>
 * @license  http://www.yiiframework.com/license/ License name
 * @link     http://www.toirus.ru
 */
class JournalController extends ActiveController
{
    public $modelClass = 'common\models\Journal';

    /**
     * Init
     *
     * @return void
     * @throws UnauthorizedHttpException
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
     * Displays homepage.
     *
     * @return string
     */
    public function actionIndex()
    {
        Yii::$app->response->format = Response::FORMAT_JSON;

        $result = '';
        return $result;
    }

    /**
     * Action Create
     *
     * @return array
     * @throws NotAcceptableHttpException
     */
    public function actionCreate()
    {
        Yii::$app->response->format = Response::FORMAT_JSON;

        $success = true;
        $saved = array();
        $rawData = Yii::$app->getRequest()->getRawBody();
        $items = json_decode($rawData, true);
        foreach ($items as $item) {
            $line = new Journal();
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
