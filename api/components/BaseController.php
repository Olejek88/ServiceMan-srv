<?php

namespace api\components;

use common\models\Alarm;
use common\models\Defect;
use common\models\DefectType;
use common\models\Documentation;
use common\models\DocumentationType;
use common\models\Equipment;
use common\models\EquipmentSystem;
use common\models\House;
use common\models\MeasureType;
use common\models\Objects;
use common\models\Operation;
use common\models\OperationTemplate;
use common\models\Street;
use common\models\TaskTemplate;
use common\models\TaskType;
use common\models\TaskVerdict;
use common\models\UserHouse;
use common\models\UserSystem;
use common\models\WorkStatus;
use Yii;
use common\components\IPhoto;
use common\models\AlarmStatus;
use common\models\AlarmType;
use common\models\City;
use common\models\EquipmentStatus;
use common\models\EquipmentType;
use common\models\ObjectStatus;
use common\models\ObjectType;
use common\models\HouseStatus;
use common\models\HouseType;
use yii\db\ActiveRecord;
use yii\filters\auth\HttpBearerAuth;
use yii\rest\Controller;
use yii\web\BadRequestHttpException;
use yii\web\NotAcceptableHttpException;
use Exception;

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
        $req = Yii::$app->request;

        /** @var ActiveRecord $class */
        $class = $this->modelClass;
        $query = $class::find();

        // проверяем параметры запроса
        $uuid = $req->getQueryParam('uuid');
        if ($uuid != null) {
            $query->andWhere(['uuid' => $uuid]);
        }

        $changedAfter = $req->getQueryParam('changedAfter');
        if ($changedAfter != null) {
            $query->andWhere(['>=', 'changedAt', $changedAfter]);
        }
        $query->limit(1);
        // проверяем что хоть какие-то условия были заданы
        if ($query->where == null) {
            return [];
        }

        /*
         * Есть проблема рекурсии при выборке связанных объектов.
         * Это происходит когда выбирается связанный объект, который ссылается на объект который его содержит.
         * Это происходит через объявление полей модели в fields().
         * Для того чтобы избежать этого, нужно прямо указывать поля которые мы желаем выбрать в виде объектов.
         * для одного уровня вложенности
         * $query->with(['fieldName'])->asArray()->all()
         * для произвольного
         * $query->with(['fieldName' => function($query){
         *     $query->with(['someField'])->asArray();
         * }])->asArray()->all()
         */

        // отдаём "простые" справочники, т.е. которые не ссылаются на другие таблицы
        switch ($this->modelClass) {
            case Alarm::class :
            case AlarmStatus::class :
            case AlarmType::class :
            case City::class :
            case Defect::class :
            case DefectType::class :
            case Documentation::class :
            case DocumentationType::class :
            case Equipment::class :
            case EquipmentStatus::class :
            case EquipmentSystem::class :
            case EquipmentType::class :
            case House::class :
            case HouseStatus::class :
            case HouseType::class :
            case MeasureType::class :
            case ObjectStatus::class :
            case ObjectType::class :
            case Objects::class :
            case Operation::class :
            case OperationTemplate::class :
            case Street::class :
            case TaskTemplate::class :
            case TaskType::class :
            case TaskVerdict::class :
            case UserHouse::class :
            case UserSystem::class :
            case WorkStatus::class :
                // выбираем данные из базы
                $result = $query->asArray()->all();
                break;
            default :
                $result = [];
        }

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
        $dir = Yii::getAlias('@storage/') . $imageRoot;
        if (!is_dir($dir)) {
            if (!mkdir($dir, 0755, true)) {
                return false;
            }
        }

        return move_uploaded_file($_FILES[$fileElementName]['tmp_name'], $dir . '/' . $fileName);
    }

    protected function createBase()
    {
        $request = Yii::$app->request;

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
     * Создаём запись о файле, сохраняем файл.
     * Во входных данных должен быть один объект.
     *
     * @return array
     * @throws NotAcceptableHttpException
     */
    protected function createBasePhoto()
    {
        $request = Yii::$app->request;

        // запись для загружаемого файла
        $photos[] = $request->getBodyParam('photo');
        foreach ($photos as $key => $photo) {
            unset($photos[$key]['_id']);
        }

        $savedPhotos = self::createSimpleObjects($photos);

        // сохраняем файл
        foreach ($photos as $photo) {
            $ext = pathinfo($_FILES['file']['name'], PATHINFO_EXTENSION);
            /** @var IPhoto $class */
            $class = $this->modelClass;
            $isInterfacePresent = in_array(IPhoto::class, class_implements($class));
            if ($isInterfacePresent) {
                try {
                    if (!self::saveUploadFile($photo['uuid'] . '.' . $ext, $class::getImageRoot())) {
                        $savedPhotos = [
                            'success' => false,
                            'data' => []
                        ];
                    }
                } catch (Exception $exception) {
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
