<?php
/**
 * PHP Version 7.0
 *
 * @category Category
 * @package  Common\models
 * @author   Максим Шумаков <ms.profile.d@gmail.com>
 * @license  http://www.yiiframework.com/license/ License name
 * @link     http://www.toirus.ru
 */

namespace common\models;

use Yii;
use yii\behaviors\TimestampBehavior;
use yii\db\ActiveRecord;
use yii\db\Expression;

/**
 * This is the model class for table "equipment".
 *
 * @category Category
 * @package  Common\models
 * @author   Максим Шумаков <ms.profile.d@gmail.com>
 * @license  http://www.yiiframework.com/license/ License name
 * @link     http://www.toirus.ru
 *
 * @property integer $_id
 * @property string $uuid
 * @property string $equipmentModelUuid
 * @property string $title
 * @property string $criticalTypeUuid
 * @property string $startDate
 * @property double $latitude
 * @property double $longitude
 * @property string $tagId
 * @property string $image
 * @property string $equipmentStatusUuid
 * @property string $inventoryNumber
 * @property string $locationUuid
 * @property string $createdAt
 * @property string $changedAt
 * @property string $parentEquipmentUuid
 * @property string $serialNumber
 */
class Equipment extends ActiveRecord
{
    private static $_IMAGE_ROOT = 'equipment';

    /**
     * Behaviors.
     *
     * @return array
     */
    public function behaviors()
    {
        return [
            [
                'class' => TimestampBehavior::className(),
                'createdAtAttribute' => 'createdAt',
                'updatedAtAttribute' => 'changedAt',
                'value' => new Expression('NOW()'),
            ],
        ];
    }

    /**
     * Table name.
     *
     * @return string
     */
    public static function tableName()
    {
        return 'equipment';
    }

    /**
     * Свойства объекта со связанными данными.
     *
     * @return array
     */
    public function fields()
    {
        return ['_id', 'uuid',
            'equipmentModelUuid',
            'equipmentModel' => function ($model) {
                return $model->equipmentModel;
            },
            'equipmentStatusUuid',
            'equipmentStatus' => function ($model) {
                return $model->equipmentStatus;
            },
            'title', 'inventoryNumber', 'serialNumber',
            'locationUuid',
            'location' => function ($model) {
                return $model->location;
            },
            'criticalTypeUuid',
            'criticalType' => function ($model) {
                return $model->criticalType;
            }, 'startDate', 'latitude', 'longitude',
            'tagId', 'image', 'createdAt', 'changedAt'
        ];
    }

    /**
     * Rules.
     *
     * @return array
     */
    public function rules()
    {
        return [
            [
                [
                    'uuid',
                    'equipmentModelUuid',
                    'title',
                    'criticalTypeUuid',
                    'tagId',
                    'equipmentStatusUuid',
                    'inventoryNumber',
                    'locationUuid'
                ],
                'required'
            ],
            [['startDate', 'createdAt', 'changedAt'], 'safe'],
            [['latitude', 'longitude'], 'number'],
            [['image'], 'file', 'skipOnEmpty' => true, 'extensions' => 'png, jpg'],
            [
                [
                    'uuid',
                    'equipmentModelUuid',
                    'criticalTypeUuid',
                    'tagId',
                    'equipmentStatusUuid',
                    'serialNumber',
                    'inventoryNumber'
                ],
                'string', 'max' => 50
            ],
            [['title', 'locationUuid'], 'string', 'max' => 100],
        ];
    }

    /**
     * Метки для свойств.
     *
     * @return array
     */
    public function attributeLabels()
    {
        return [
            '_id' => Yii::t('app', '№'),
            'uuid' => Yii::t('app', 'Uuid'),
            'equipmentModelUuid' => Yii::t('app', 'Модель оборудования'),
            'equipmentModel' => Yii::t('app', 'Модель'),
            'title' => Yii::t('app', 'Название'),
            'criticalTypeUuid' => Yii::t('app', 'Критичность'),
            'startDate' => Yii::t('app', 'Дата установки'),
            'latitude' => Yii::t('app', 'Широта'),
            'longitude' => Yii::t('app', 'Долгота'),
            'tagId' => Yii::t('app', 'Tag ID'),
            'image' => Yii::t('app', 'Фотография'),
            'equipmentStatusUuid' => Yii::t('app', 'Статус'),
            'inventoryNumber' => Yii::t('app', 'Инвентарный'),
            'locationUuid' => Yii::t('app', 'Локация'),
            'parentEquipmentUuid' => Yii::t(
                'app', 'Uuid родительского оборудования'
            ),
            'serialNumber' => Yii::t('app', 'Серийный номер'),
            'createdAt' => Yii::t('app', 'Создан'),
            'changedAt' => Yii::t('app', 'Изменен'),
        ];
    }

    /**
     * Проверка целостности модели?
     *
     * @return bool
     */
    public function upload()
    {
        if ($this->validate()) {
            return true;
        } else {
            return false;
        }
    }

    /**
     * Объект связанного поля.
     *
     * @return \yii\db\ActiveQuery
     */
    public function getCriticalType()
    {
        return $this->hasOne(
            CriticalType::className(), ['uuid' => 'criticalTypeUuid']
        );
    }

    /**
     * Объект связанного поля.
     *
     * @return \yii\db\ActiveQuery
     */
    public function getEquipmentStatus()
    {
        return $this->hasOne(
            EquipmentStatus::className(), ['uuid' => 'equipmentStatusUuid']
        );
    }

    /**
     * Объект связанного поля.
     *
     * @return \yii\db\ActiveQuery
     */
    public function getEquipmentModel()
    {
        return $this->hasOne(
            EquipmentModel::className(), ['uuid' => 'equipmentModelUuid']
        );
    }

    /**
     * Объект связанного поля.
     *
     * @return \yii\db\ActiveQuery
     */
    public function getLocation()
    {
        return $this->hasOne(Objects::className(), ['uuid' => 'locationUuid']);
    }

    /**
     * Объект связанного поля.
     *
     * @return \yii\db\ActiveQuery
     */
    /*
    public function getObjects()
    {
        return $this->hasOne(Objects::className(), ['uuid' => 'locationUuid']);
    }*/

    /**
     * URL изображения.
     *
     * @return string
     */
    public function getImageUrl()
    {
        if ($this->image == '') {
            return $this->equipmentModel->getImageUrl();
        }

        $dbName = \Yii::$app->session->get('user.dbname');
        $typeUuid = $this->equipmentModelUuid;
        $localPath = 'storage/' . $dbName . '/' . self::$_IMAGE_ROOT . '/'
            . $typeUuid . '/' . $this->image;
        if (file_exists(Yii::getAlias($localPath))) {
            $userName = \Yii::$app->user->identity->username;
            $dir = 'storage/' . $userName . '/' . self::$_IMAGE_ROOT . '/'
                . $typeUuid . '/' . $this->image;
            $url = Yii::$app->request->BaseUrl . '/' . $dir;
        } else {
            $url = $this->equipmentModel->getImageUrl();
        }

        return $url;
    }

    /**
     * Возвращает каталог в котором должен находится файл изображения,
     * относительно папки web.
     *
     * @return string
     */
    public function getImageDir()
    {
        $typeUuid = $this->equipmentModelUuid;
        $dbName = \Yii::$app->session->get('user.dbname');
        $dir = 'storage/' . $dbName . '/' . self::$_IMAGE_ROOT . '/'
            . $typeUuid . '/';
        return $dir;
    }

    /**
     * Возвращает каталог в котором должен находится файл изображения,
     * относительно папки web.
     *
     * @param string $typeUuid Uuid типа операции
     *
     * @return string
     */
    public function getImageDirType($typeUuid)
    {
        $dbName = \Yii::$app->session->get('user.dbname');
        $dir = 'storage/' . $dbName . '/' . self::$_IMAGE_ROOT . '/'
            . $typeUuid . '/';
        return $dir;
    }
}
