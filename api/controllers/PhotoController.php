<?php

namespace api\controllers;

use api\components\BaseController;
use common\models\Photo;
use Exception;
use Yii;
use yii\db\ActiveRecord;
use yii\web\NotAcceptableHttpException;
use yii\db\StaleObjectException;
use Throwable;

class PhotoController extends BaseController
{
    /** @var ActiveRecord $modelClass */
    public $modelClass = Photo::class;

    public function actionIndex()
    {
        $req = Yii::$app->request;

        /** @var ActiveRecord $class */
        $class = $this->modelClass;
        $query = $class::find();

        // проверяем параметры запроса
        $uuid = $req->getQueryParam('uuid');
        if ($uuid != null) {
            $query->andWhere(['uuid' => $uuid]);
        }

        $oUuid = $req->getQueryParam('oUuid');
        if ($oUuid != null) {
            $query->andWhere(['objectUuid' => $oUuid]);

        }

        $changedAfter = $req->getQueryParam('changedAfter');
        if ($changedAfter != null) {
            $query->andWhere(['>=', 'changedAt', $changedAfter]);
        }

        // проверяем что хоть какие-то условия были заданы
        if ($query->where == null) {
            return [];
        }

        $query->with(['user']);

        // выбираем данные из базы
        $result = $query->asArray()->all();

        return $result;
    }

    public function actionCreate()
    {

        return ['success' => false, 'data' => -1];
    }

    /**
     * Во входных данных должен быть один объект.
     *
     * @return array
     * @throws NotAcceptableHttpException
     * @throws Throwable
     * @throws StaleObjectException
     */
    public function actionUpdateAttribute()
    {
        $result = parent::actionUpdateAttribute();
        if ($result['success'] == true) {
            $req = Yii::$app->request;
            $model = Photo::find()->where(['uuid' => $req->getBodyParam('modelUuid')])->one();
            try {
                if (!self::saveUploadFile($_FILES['file']['name'], $model->getImagePath())) {
                    $model->delete();
                    $result['success'] = false;
                }
            } catch (Exception $exception) {
                $model->delete();
                $result['success'] = false;
            }
        }

        return $result;
    }
}
