<?php

namespace api\components;

use common\components\IPhoto;
use yii\db\ActiveRecord;
use yii\filters\auth\HttpBearerAuth;
use yii\rest\Controller;
use yii\web\BadRequestHttpException;
use yii\web\NotAcceptableHttpException;

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
     * @param array $items
     * @return array
     */
    protected function createSimpleObjects($items)
    {
        $success = true;
        $saved = array();
        foreach ($items as $item) {
            $line = self::createSimpleObject($item);
            if ($line->save()) {
                $saved[] = [
                    '_id' => $line->getAttribute('_id'),
                    'uuid' => isset($item['uuid']) ? $item['uuid'] : '',
                ];
            } else {
                $success = false;
            }
        }

        return ['success' => $success, 'data' => $saved];
    }

    /**
     * Метод для сохранения в базу "простого" объекта.
     * Справочная информация на которую он ссылается уже есть в базе.
     *
     * @param array $item
     * @return ActiveRecord
     */
    protected function createSimpleObject($item)
    {
        /** @var ActiveRecord $class */
        /** @var ActiveRecord $line */
        $class = $this->modelClass;
        $line = $class::findOne(['uuid' => $item['uuid']]);
        if ($line == null) {
            $line = new $class;
        }

        $line->setAttributes($item, false);
        return $line;
    }

    /**
     * Сохраняет загруженый через форму файл.
     *
     * @param string $fileName
     * @param string $imageRoot
     * @param string $fileElementName
     * @return boolean
     */
    protected static function saveUploadFile($fileName, $imageRoot, $fileElementName = 'file')
    {
        $dir = \Yii::getAlias('@storage/') . $imageRoot;
        if (!is_dir($dir)) {
            if (!mkdir($dir, 0755, true)) {
                return false;
            }
        }

        return move_uploaded_file($_FILES[$fileElementName]['tmp_name'], $dir . '/' . $fileName);
    }

    protected function createBase()
    {
        $request = \Yii::$app->request;

        $rawData = $request->getRawBody();
        if ($rawData == null) {
            return [];
        }

        // список записей
        $items = json_decode($rawData, true);
        if (!is_array($items)) {
            return [];
        }

        foreach ($items as $key => $item) {
            unset($items[$key]['_id']);
        }

        // сохраняем записи
        $saved = self::createSimpleObjects($items);
        return $saved;
    }

    /**
     * Во входных данных будет один объект. Но для унификации он будет передан как один элемент массива.
     *
     * @return array
     * @throws NotAcceptableHttpException
     */
    protected function createBasePhoto()
    {
        $request = \Yii::$app->request;

        // запись для загружаемого файла
        $photos = $request->getBodyParam('photos');
        $savedPhotos = self::createSimpleObjects($photos);

        // сохраняем файл
        foreach ($photos as $photo) {
            $ext = pathinfo($_FILES['file']['name'], PATHINFO_EXTENSION);
            /** @var IPhoto $class */
            $class = $this->modelClass;
            $isInterfacePresent = in_array(IPhoto::class, class_implements($class));
            if ($isInterfacePresent) {
                if (!self::saveUploadFile($photo['uuid'] . '.' . $ext, $class::getImageRoot())) {
                    $savedPhotos = [
                        'success' => false,
                        'data' => []
                    ];
                }
            } else {
                throw new NotAcceptableHttpException();
            }
        }

        return $savedPhotos;
    }
}
