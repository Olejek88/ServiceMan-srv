<?php

namespace api\components;

use yii\db\ActiveRecord;
use yii\filters\auth\HttpBearerAuth;
use yii\rest\Controller;
use yii\web\BadRequestHttpException;

class BaseController extends Controller
{
    public $modelClass = ActiveRecord::class;

    /**
     * @inheritdoc
     */
    public function verbs()
    {
        $verbs = parent::verbs();
        $verbs['create'] = ['POST'];
        $verbs['index'] = ['GET'];
        return $verbs;
    }

    /**
     * @inheritdoc
     */
    public function behaviors()
    {
        $behaviors = parent::behaviors();
        $behaviors['authenticator']['class'] = HttpBearerAuth::class;
        return $behaviors;
    }

    public function actionIndex()
    {
        // проверяем параметры запроса
        $req = \Yii::$app->request;

        /** @var ActiveRecord $class */
        $class = $this->modelClass;
        $query = $class::find();

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
     * @return array|void
     * @throws BadRequestHttpException
     */
    public function actionCreate()
    {
        throw new BadRequestHttpException();
    }

    /**
     * Метод для сохранения в базу "простых" объектов.
     * Справочная информация на которую они ссылаются уже есть в базе.
     *
     * @return array
     */
    public function createSimpleObject()
    {
        $success = true;
        $saved = array();
        $request = \Yii::$app->getRequest();
        $rawData = $request->getRawBody();
        $items = json_decode($rawData, true);
        foreach ($items as $item) {
            /** @var ActiveRecord $class */
            $class = $this->modelClass;
            /** @var ActiveRecord $line */
            $line = new $class;
            $line->setAttributes($item, false);
            if ($line->save()) {
                $saved[] = [
                    '_id' => $item['_id'],
                    'uuid' => isset($item['uuid']) ? $item['uuid'] : '',
                ];
            } else {
                $success = false;
            }
        }

        return ['success' => $success, 'data' => $saved];
    }
}
