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

namespace api\old\controllers;

use Yii;
use yii\rest\ActiveController;
use yii\web\NotAcceptableHttpException;
use yii\web\Response;
use yii\web\UnauthorizedHttpException;
use common\models\OperationFile;

/**
 * PHP Version 7.0
 *
 * @category Category
 * @package  Api\controllers
 * @author   Дмитрий Логачев <demonwork@yandex.ru>
 * @license  http://www.yiiframework.com/license/ License name
 * @link     http://www.toirus.ru
 */
class OperationFileController extends ActiveController
{
    public $modelClass = 'app\models\OperationFile';

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
     * @return OperationFile[]
     */
    public function actionIndex()
    {
        Yii::$app->response->format = Response::FORMAT_JSON;

        // проверяем параметры запроса
        $req = \Yii::$app->request;
        $query = OperationFile::find();

        $id = $req->getQueryParam('id');
        $uuid = $req->getQueryParam('uuid');
        if ($id != null && $uuid != null) {
            $query->andWhere(['_id' => $id]);
            $query->andWhere(['uuid' => $uuid]);
        }

        $operation = $req->getQueryParam('operation');
        if ($operation != null) {
            $query->andWhere(['operationUuid' => $operation]);
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
     * Метод для загрузки файлов сделаных во время выполнения операций.
     *
     * @return array
     * @throws NotAcceptableHttpException
     */
    public function actionUpload()
    {
        $success = true;
        $saved = array();

        $request = Yii::$app->request;
        if (!$request->isPost) {
            throw new NotAcceptableHttpException();
        }

        // список записей для загружаемых файлов
        $operationFiles = $request->getBodyParam('file');
        if ($_FILES == null && $operationFiles == null
            || (count($_FILES['file']['name']) != count($operationFiles))
        ) {
            throw new NotAcceptableHttpException();
        }

        foreach ($operationFiles as $file) {
            if (OperationFile::saveUploadFile($file)) {
                $saved[] = [
                    '_id' => $file['_id'],
                    'uuid' => $file['uuid']
                ];
            } else {
                $success = false;
            }
        }

        return ['success' => $success, 'data' => $saved];
    }
}
